<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Role constants
     */
    const ROLE_EMPLOYEE = 'employee';
    const ROLE_MANAGER = 'manager';
    const ROLE_ADMIN = 'admin';

    /**
     * Get all possible roles
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_EMPLOYEE,
            self::ROLE_MANAGER,
            self::ROLE_ADMIN,
        ];
    }

    /**
     * Get travel requests made by this user
     */
    public function travelRequests(): HasMany
    {
        return $this->hasMany(TravelRequest::class);
    }

    /**
     * Get travel requests approved by this user
     */
    public function approvedTravelRequests(): HasMany
    {
        return $this->hasMany(TravelRequest::class, 'approver_id');
    }

    /**
     * Get status history actions made by this user
     */
    public function statusHistoryActions(): HasMany
    {
        return $this->hasMany(TravelRequestStatusHistory::class);
    }

    /**
     * Check if user can approve travel requests
     */
    public function canApproveRequests(): bool
    {
        return in_array($this->role, [self::ROLE_MANAGER, self::ROLE_ADMIN]);
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isEmployee(): bool
    {
        return $this->role === self::ROLE_EMPLOYEE;
    }

}
