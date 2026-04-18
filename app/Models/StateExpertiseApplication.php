<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StateExpertiseApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'laboratory_id',
        'application_ids',
        // Muddatlar
        'submitted_at',
        'institute_deadline',
        'ministry_deadline',
        'total_deadline',
        // To'lov
        'payment_amount',
        'payment_receipt_number',
        'payment_confirmed',
        // Institut ko'rib chiqish
        'institute_status',
        'institute_expert_id',
        'institute_comment',
        'institute_return_reason',
        'institute_return_legal_ref',
        'institute_return_deadline',
        'institute_reviewed_at',
        // Vazirlik ko'rib chiqish
        'ministry_status',
        'ministry_expert_id',
        'ministry_comment',
        'ministry_return_reason',
        'ministry_return_legal_ref',
        'ministry_return_deadline',
        'ministry_reviewed_at',
        // Xulosa
        'conclusion_number',
        'conclusion_series',
        'conclusion_blank_no',
        'qr_code_path',
        // Qayta topshirish
        'resubmission_count',
        'is_auto_approved',
        'auto_approved_at',
    ];

    protected $casts = [
        'application_ids' => 'array',
        'submitted_at' => 'datetime',
        'institute_deadline' => 'date',
        'ministry_deadline' => 'date',
        'total_deadline' => 'date',
        'institute_reviewed_at' => 'datetime',
        'ministry_reviewed_at' => 'datetime',
        'payment_confirmed' => 'boolean',
        'is_auto_approved' => 'boolean',
        'auto_approved_at' => 'datetime',
        'institute_return_deadline' => 'date',
        'ministry_return_deadline' => 'date',
        'payment_amount' => 'decimal:2',
    ];

    /* ── Boot: yangi ariza yaratilganda muddatlarni avtomatik o'rnatish ── */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (! $model->submitted_at) {
                $model->submitted_at = now();
            }
            // Tartibnoma 26-band: Institut — 15 kalendar kun
            if (! $model->institute_deadline) {
                $model->institute_deadline = now()->addDays(15)->toDateString();
            }
            // Tartibnoma 29-band: Jami 25 kun
            if (! $model->total_deadline) {
                $model->total_deadline = now()->addDays(25)->toDateString();
            }
        });

        // Institut ma'quullagandan keyin Vazirlik muddatini o'rnatish
        static::updated(function (self $model) {
            if ($model->wasChanged('institute_status') && $model->institute_status === 'approved') {
                $model->updateQuietly([
                    'ministry_deadline' => now()->addDays(10)->toDateString(),
                ]);
            }
        });
    }

    /* ── Munosabatlar ── */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }

    public function instituteExpert()
    {
        return $this->belongsTo(User::class, 'institute_expert_id');
    }

    public function ministryExpert()
    {
        return $this->belongsTo(User::class, 'ministry_expert_id');
    }

    /* ── Muddat helperlari ── */

    /**
     * Tartibnoma §33: 25 kun o'tib ketganmi?
     */
    public function isTotalDeadlineExpired(): bool
    {
        return $this->total_deadline && Carbon::today()->gt($this->total_deadline);
    }

    /**
     * Institut muddati o'tib ketganmi? (15 kun)
     */
    public function isInstituteDeadlineExpired(): bool
    {
        return $this->institute_deadline
            && $this->institute_status === 'pending'
            && Carbon::today()->gt($this->institute_deadline);
    }

    /**
     * Vazirlik muddati o'tib ketganmi? (10 kun)
     */
    public function isMinistryDeadlineExpired(): bool
    {
        return $this->ministry_deadline
            && $this->ministry_status === 'pending'
            && Carbon::today()->gt($this->ministry_deadline);
    }

    /**
     * Institut uchun qolgan kunlar
     */
    public function instituteDaysRemaining(): int
    {
        if (! $this->institute_deadline) {
            return 0;
        }

        return max(0, Carbon::today()->diffInDays($this->institute_deadline, false));
    }

    /**
     * Jami qolgan kunlar (25 kundan)
     */
    public function totalDaysRemaining(): int
    {
        if (! $this->total_deadline) {
            return 0;
        }

        return max(0, Carbon::today()->diffInDays($this->total_deadline, false));
    }

    /**
     * Tartibnoma §33: Avtomatik tasdiqlash (muddat o'tsa)
     */
    public function autoApproveIfOverdue(): bool
    {
        if ($this->isTotalDeadlineExpired() && ! $this->is_auto_approved && $this->ministry_status === 'pending') {
            $this->update([
                'is_auto_approved' => true,
                'auto_approved_at' => now(),
                'ministry_status' => 'approved',
                'conclusion_number' => $this->generateConclusionNumber(),
                'conclusion_series' => 'DX',
                'conclusion_blank_no' => $this->generateBlankNumber(),
            ]);

            return true;
        }

        return false;
    }

    /**
     * Xulosa raqami: DX-2024-000001 formatida
     * Tartibnoma 38-40-band
     */
    public static function generateConclusionNumber(): string
    {
        $year = now()->year;
        $lastNo = self::whereYear('created_at', $year)->max('id') ?? 0;

        return 'DX-'.$year.'-'.str_pad($lastNo + 1, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Blank raqami (qat'iy hisobot hujjati)
     */
    public static function generateBlankNumber(): string
    {
        $year = now()->year;
        $seq = self::whereYear('created_at', $year)->count() + 1;

        return 'DX/'.substr($year, -2).'/'.str_pad($seq, 6, '0', STR_PAD_LEFT);
    }

    /* ── Status yorliqlari ── */
    public function statusLabel(): string
    {
        if ($this->is_auto_approved) {
            return '⚡ Muddati o\'tganligi sababli avtomatik tasdiqlandi';
        }

        return match (true) {
            $this->ministry_status === 'approved' => '✅ Davlat xulosasi berildi',
            $this->ministry_status === 'returned' => '↩️ Vazirlik qaytardi',
            $this->institute_status === 'approved' => '🔄 Vazirlik ko\'rib chiqmoqda',
            $this->institute_status === 'returned' => '↩️ Institut qaytardi',
            default => '🕐 Institut ko\'rib chiqmoqda',
        };
    }
}
