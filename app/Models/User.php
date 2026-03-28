<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'pinfl',
        'tin',
        'person_type',
        'is_verified',
        'organization_id',
        'laboratory_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    // Role helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEmployer(): bool
    {
        return $this->role === 'employer';
    }

    public function isCommission(): bool
    {
        return $this->role === 'commission';
    }

    public function isExpert(): bool
    {
        return $this->role === 'expert';
    }

    public function isInstituteExpert(): bool
    {
        return $this->role === 'institute_expert';
    }

    public function isLaboratory(): bool
    {
        return $this->role === 'laboratory';
    }

    // Ish beruvchi sifatida berilgan arizalar
    public function applications()
    {
        return $this->hasMany(AttestationApplication::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }
}
