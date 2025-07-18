<?php

namespace App\Observers;

use App\Models\TravelRequest;
use App\Notifications\TravelRequestStatusChanged;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TravelRequestObserver
{
    /**
     * Handle the TravelRequest "created" event.
     */
    public function created(TravelRequest $travelRequest): void
    {
        try {
            // Create initial status history entry
            $userId = $travelRequest->user_id ?? Auth::id() ?? 1;
            
            $travelRequest->statusHistory()->create([
                'new_status' => $travelRequest->status,
                'user_id' => $userId,
                'notes' => 'Solicitação de viagem criada',
            ]);
            
            Log::info('Travel request created', [
                'id' => $travelRequest->id,
                'user_id' => $travelRequest->user_id,
                'destination' => $travelRequest->destination,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating travel request status history', [
                'error' => $e->getMessage(),
                'travel_request_id' => $travelRequest->id,
            ]);
        }
    }

    /**
     * Handle the TravelRequest "updating" event.
     */
    public function updating(TravelRequest $travelRequest): void
    {
        try {
            // Check if status is changing
            if ($travelRequest->isDirty('status')) {
                $originalStatus = $travelRequest->getOriginal('status');
                $newStatus = $travelRequest->status;
                $userId = Auth::id() ?? $travelRequest->user_id ?? 1;
                
                // Create status history entry before updating
                $travelRequest->statusHistory()->create([
                    'previous_status' => $originalStatus,
                    'new_status' => $newStatus,
                    'user_id' => $userId,
                    'notes' => $travelRequest->rejection_reason ?? null,
                ]);
                
                Log::info('Travel request status history created', [
                    'id' => $travelRequest->id,
                    'from' => $originalStatus,
                    'to' => $newStatus,
                    'user_id' => $userId,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating travel request status history', [
                'error' => $e->getMessage(),
                'travel_request_id' => $travelRequest->id,
            ]);
        }
    }

    /**
     * Handle the TravelRequest "updated" event.
     */
    public function updated(TravelRequest $travelRequest): void
    {
        // Check if status was changed
        if ($travelRequest->wasChanged('status')) {
            $this->handleStatusChange($travelRequest);
        }
    }

    /**
     * Handle status change notifications
     */
    private function handleStatusChange(TravelRequest $travelRequest): void
    {
        $originalStatus = $travelRequest->getOriginal('status');
        $newStatus = $travelRequest->status;

        // Log the status change
        Log::info('Travel request status changed', [
            'id' => $travelRequest->id,
            'from' => $originalStatus,
            'to' => $newStatus,
            'user_id' => $travelRequest->user_id,
        ]);

        // Send notification for status changes that affect the user
        if (in_array($newStatus, [TravelRequest::STATUS_APPROVED, TravelRequest::STATUS_CANCELLED, TravelRequest::STATUS_REJECTED])) {
            try {
                $travelRequest->user->notify(new TravelRequestStatusChanged($travelRequest, $originalStatus, $newStatus));
            } catch (\Exception $e) {
                Log::error('Failed to send travel request notification', [
                    'travel_request_id' => $travelRequest->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Handle the TravelRequest "deleted" event.
     */
    public function deleted(TravelRequest $travelRequest): void
    {
        Log::info('Travel request deleted', [
            'id' => $travelRequest->id,
            'user_id' => $travelRequest->user_id,
        ]);
    }

    /**
     * Handle the TravelRequest "restored" event.
     */
    public function restored(TravelRequest $travelRequest): void
    {
        Log::info('Travel request restored', [
            'id' => $travelRequest->id,
            'user_id' => $travelRequest->user_id,
        ]);
    }

    /**
     * Handle the TravelRequest "force deleted" event.
     */
    public function forceDeleted(TravelRequest $travelRequest): void
    {
        Log::info('Travel request force deleted', [
            'id' => $travelRequest->id,
            'user_id' => $travelRequest->user_id,
        ]);
    }
}
