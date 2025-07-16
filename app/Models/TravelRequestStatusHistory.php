<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelRequestStatusHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'travel_request_status_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'travel_request_id',
        'status',
        'comment',
        'changed_by',
    ];

    /**
     * Get the travel request that owns this status history
     */
    public function travelRequest(): BelongsTo
    {
        return $this->belongsTo(TravelRequest::class);
    }

    /**
     * Get the user who made the status change
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Get formatted date
     */
    public function getFormattedDate(): string
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            TravelRequest::STATUS_REQUESTED => 'Solicitado',
            TravelRequest::STATUS_APPROVED => 'Aprovado',
            TravelRequest::STATUS_REJECTED => 'Rejeitado',
            TravelRequest::STATUS_CANCELLED => 'Cancelado',
            default => 'Desconhecido',
        };
    }
}
