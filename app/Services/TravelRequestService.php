<?php

namespace App\Services;

use App\Models\TravelRequest;
use App\Models\TravelRequestStatusHistory;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TravelRequestService
{
    /**
     * Create a new travel request
     */
    public function createTravelRequest(array $data): TravelRequest
    {
        return DB::transaction(function () use ($data) {
            $travelRequest = TravelRequest::create($data);
            
            // O histórico é criado automaticamente pelo Observer
            
            return $travelRequest;
        });
    }

    /**
     * Update travel request status
     */
    public function updateStatus(TravelRequest $travelRequest, string $newStatus, User $user, ?string $reason = null): bool
    {
        if (!$this->canUpdateStatus($travelRequest, $newStatus, $user)) {
            throw new Exception('User cannot update this travel request status');
        }

        return DB::transaction(function () use ($travelRequest, $newStatus, $user, $reason) {
            $previousStatus = $travelRequest->status;
            
            // Update the travel request
            $updateData = ['status' => $newStatus];
            
            if ($newStatus === TravelRequest::STATUS_APPROVED) {
                $updateData['approved_at'] = now();
                $updateData['approver_id'] = $user->id;
            } elseif ($newStatus === TravelRequest::STATUS_CANCELLED) {
                $updateData['cancelled_at'] = now();
            } elseif ($newStatus === TravelRequest::STATUS_REJECTED) {
                $updateData['rejection_reason'] = $reason;
                $updateData['approver_id'] = $user->id;
            }
            
            $travelRequest->update($updateData);
            
            // Create status history entry
            $this->createStatusHistory($travelRequest, $previousStatus, $newStatus, $reason, $user);
            
            return true;
        });
    }

    /**
     * Check if a travel request can be cancelled after approval
     */
    public function canCancelAfterApproval(TravelRequest $travelRequest): bool
    {
        if (!$travelRequest->isApproved()) {
            return false;
        }

        // Business rule: Can only cancel if departure is at least 24 hours away
        return $travelRequest->departure_date->gt(now()->addDay());
    }

    /**
     * Cancel an approved travel request
     */
    public function cancelApprovedRequest(TravelRequest $travelRequest, User $user, string $reason): bool
    {
        if (!$this->canCancelAfterApproval($travelRequest)) {
            throw new Exception('Cannot cancel this approved travel request');
        }

        return $this->updateStatus($travelRequest, TravelRequest::STATUS_CANCELLED, $user, $reason);
    }

    /**
     * Get travel requests with filters
     */
    public function getTravelRequests(array $filters = [])
    {
        $query = TravelRequest::with(['user', 'approver']);

        // Filter by status
        if (isset($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        // Filter by user
        if (isset($filters['user_id'])) {
            $query->byUser($filters['user_id']);
        }

        // Filter by destination
        if (isset($filters['destination'])) {
            $query->byDestination($filters['destination']);
        }

        // Filter by travel date range
        if (isset($filters['departure_date_from']) && isset($filters['departure_date_to'])) {
            $query->byDateRange($filters['departure_date_from'], $filters['departure_date_to']);
        }

        // Filter by request date range
        if (isset($filters['request_date_from']) && isset($filters['request_date_to'])) {
            $query->byRequestDateRange($filters['request_date_from'], $filters['request_date_to']);
        }

        // Ordering
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        // Pagination
        $perPage = $filters['per_page'] ?? 15;
        
        return $query->paginate($perPage);
    }

    /**
     * Check if user can update the status of a travel request
     */
    private function canUpdateStatus(TravelRequest $travelRequest, string $newStatus, User $user): bool
    {
        // User who made the request cannot approve/reject their own request
        if ($travelRequest->user_id === $user->id && in_array($newStatus, [TravelRequest::STATUS_APPROVED, TravelRequest::STATUS_REJECTED])) {
            return false;
        }

        // Only managers/admins can approve/reject requests
        if (in_array($newStatus, [TravelRequest::STATUS_APPROVED, TravelRequest::STATUS_REJECTED]) && !$user->canApproveRequests()) {
            return false;
        }

        // Check if the request is in a state that allows the transition
        switch ($newStatus) {
            case TravelRequest::STATUS_APPROVED:
                return $travelRequest->canBeApproved();
            case TravelRequest::STATUS_CANCELLED:
                return $travelRequest->canBeCancelled();
            case TravelRequest::STATUS_REJECTED:
                return $travelRequest->canBeApproved();
            default:
                return false;
        }
    }

    /**
     * Create status history entry
     */
    private function createStatusHistory(TravelRequest $travelRequest, ?string $previousStatus, string $newStatus, ?string $reason = null, ?User $user = null): void
    {
        TravelRequestStatusHistory::create([
            'travel_request_id' => $travelRequest->id,
            'user_id' => $user ? $user->id : $travelRequest->user_id,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'reason' => $reason,
        ]);
    }

    /**
     * Get travel request statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_requests' => TravelRequest::count(),
            'pending_requests' => TravelRequest::byStatus(TravelRequest::STATUS_REQUESTED)->count(),
            'approved_requests' => TravelRequest::byStatus(TravelRequest::STATUS_APPROVED)->count(),
            'cancelled_requests' => TravelRequest::byStatus(TravelRequest::STATUS_CANCELLED)->count(),
            'rejected_requests' => TravelRequest::byStatus(TravelRequest::STATUS_REJECTED)->count(),
        ];
    }
}
