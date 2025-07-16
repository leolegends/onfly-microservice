<?php

namespace App\Actions\Admin;

use App\Models\TravelRequest;
use App\Models\User;
use App\Services\TravelRequestService;

class UpdateTravelRequestStatusAction
{
    public function __construct(
        private TravelRequestService $travelRequestService
    ) {}

    /**
     * Update travel request status
     */
    public function execute(TravelRequest $travelRequest, string $status, User $user, ?string $reason = null): bool
    {
        return $this->travelRequestService->updateStatus($travelRequest, $status, $user, $reason);
    }
}
