<?php

namespace App\Actions\Admin;

use App\Models\TravelRequest;
use App\Services\TravelRequestService;
use Illuminate\Pagination\LengthAwarePaginator;

class GetTravelRequestsAction
{
    public function __construct(
        private TravelRequestService $travelRequestService
    ) {}

    /**
     * Get travel requests with filters and pagination
     */
    public function execute(array $filters = []): LengthAwarePaginator
    {
        return $this->travelRequestService->getTravelRequests($filters);
    }
}
