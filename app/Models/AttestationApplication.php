<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttestationApplication extends Model
{
    use HasFactory;

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_HR_APPROVED = 'hr_approved';

    public const STATUS_HR_REJECTED = 'hr_rejected';

    public const STATUS_FINALIZED = 'finalized';

    public const ALLOWED_TRANSITIONS = [
        self::STATUS_SUBMITTED => [self::STATUS_HR_APPROVED, self::STATUS_HR_REJECTED],
        self::STATUS_HR_APPROVED => [self::STATUS_FINALIZED],
        self::STATUS_HR_REJECTED => [],
        self::STATUS_FINALIZED => [],
    ];

    protected $fillable = [
        'user_id',
        'organization_id',
        'campaign_id',
        'position',
        'comment',
        'workplace_name',
        'department',
        'employee_count',
        'workplace_description',
        'hazard_factors',
        'equipment_list',
        'protective_equipment',
        'workplace_photo_path',
        'cv_path',
        'diploma_path',
        'documents_path',
        'status',
        'hr_reviewed_by',
        'hr_reviewed_at',
        'hr_comment',
        'final_score',
        'final_decision',
        'workplace_class',
        'finalized_by',
        'finalized_at',
    ];

    protected $casts = [
        'hr_reviewed_at' => 'datetime',
        'finalized_at' => 'datetime',
        'final_score' => 'decimal:2',
        'hazard_factors' => 'array',
    ];

    /* ── Ish o'rni klassini o'zbekcha ko'rinishda qaytarish ── */
    public function getWorkplaceClassLabel(): string
    {
        return match ($this->workplace_class) {
            'optimal' => 'Optimal (1-klass)',
            'ruxsat_etilgan' => 'Ruxsat etilgan (2-klass)',
            'zararli_xavfli' => 'Zararli / Xavfli (3-klass)',
            default => 'Aniqlanmagan',
        };
    }

    /* ── Munosabatlar ── */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function campaign()
    {
        return $this->belongsTo(AttestationCampaign::class, 'campaign_id');
    }

    /**
     * Bu ish o'rniga tegishli 18-omilli o'lchov protokoli
     */
    public function protocol()
    {
        return $this->hasOne(AttestationProtocol::class, 'application_id');
    }

    public function evaluations()
    {
        return $this->hasMany(AttestationEvaluation::class, 'application_id');
    }

    public function hrReviewer()
    {
        return $this->belongsTo(User::class, 'hr_reviewed_by');
    }

    public function finalizer()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function recalculateFinalScore(): void
    {
        $avg = $this->evaluations()->avg('score');
        $this->final_score = $avg !== null ? round((float) $avg, 2) : null;
        $this->save();
    }

    /* ── Status helpers ── */
    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isFinalized(): bool
    {
        return $this->status === 'finalized';
    }

    public function hasProtocol(): bool
    {
        return $this->protocol !== null;
    }

    public function canTransitionTo(string $nextStatus): bool
    {
        return in_array($nextStatus, self::ALLOWED_TRANSITIONS[$this->status] ?? [], true);
    }

    public function transitionTo(string $nextStatus, array $attributes = []): bool
    {
        if (! $this->canTransitionTo($nextStatus)) {
            return false;
        }

        $payload = array_merge($attributes, ['status' => $nextStatus]);

        return $this->update($payload);
    }
}
