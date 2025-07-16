<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelRequest extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'approver_id',
        'requestor_name',
        'destination',
        'departure_date',
        'return_date',
        'start_date',
        'end_date',
        'status',
        'purpose',
        'budget',
        'justification',
        'rejection_reason',
        'approved_at',
        'cancelled_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'departure_date' => 'date',
        'return_date' => 'date',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'budget' => 'decimal:2',
    ];

    /**
     * The attributes that have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => self::STATUS_REQUESTED,
    ];

    /**
     * Status constants
     */
    const STATUS_REQUESTED = 'requested';
    const STATUS_APPROVED = 'approved';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get all possible statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_REQUESTED,
            self::STATUS_APPROVED,
            self::STATUS_CANCELLED,
            self::STATUS_REJECTED,
        ];
    }

    /**
     * Get the user who made the request
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved/rejected the request
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Get the status history of this travel request
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(TravelRequestStatusHistory::class);
    }

    /**
     * Check if request can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_REQUESTED, self::STATUS_APPROVED]);
    }

    /**
     * Check if request can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_REQUESTED;
    }

    /**
     * Check if request is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_REQUESTED;
    }

    /**
     * Check if request is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if request is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if request is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if request is requested
     */
    public function isRequested(): bool
    {
        return $this->status === self::STATUS_REQUESTED;
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by destination
     */
    public function scopeByDestination($query, $destination)
    {
        return $query->where('destination', 'like', '%' . $destination . '%');
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by request date range
     */
    public function scopeByRequestDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get duration in days
     */
    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Check if travel is in the past
     */
    public function isInPast(): bool
    {
        return $this->start_date->isPast();
    }

    /**
     * Check if travel is in the future
     */
    public function isInFuture(): bool
    {
        return $this->start_date->isFuture();
    }

    /**
     * Check if travel is current
     */
    public function isCurrent(): bool
    {
        return $this->start_date->isPast() && $this->end_date->isFuture();
    }

    /**
     * Get formatted budget
     */
    public function getFormattedBudget(): string
    {
        return 'R$ ' . number_format($this->budget, 2, ',', '.');
    }

    /**
     * Get formatted start date
     */
    public function getFormattedStartDate(): string
    {
        return $this->start_date->format('d/m/Y');
    }

    /**
     * Get formatted end date
     */
    public function getFormattedEndDate(): string
    {
        return $this->end_date->format('d/m/Y');
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_REQUESTED => 'Solicitado',
            self::STATUS_APPROVED => 'Aprovado',
            self::STATUS_REJECTED => 'Rejeitado',
            self::STATUS_CANCELLED => 'Cancelado',
            default => 'Desconhecido',
        };
    }
}
