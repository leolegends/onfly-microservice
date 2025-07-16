<?php

namespace App\Actions\User;

use App\Models\TravelRequest;
use App\Services\TravelRequestService;
use Illuminate\Support\Facades\Auth;

class CreateTravelRequestAction
{
    public function __construct(
        private TravelRequestService $travelRequestService
    ) {}

    public function execute(array $data): TravelRequest
    {
        $data['user_id'] = Auth::id();
        $data['status'] = TravelRequest::STATUS_REQUESTED;

        return $this->travelRequestService->createTravelRequest($data);
    }
}
