<?php

namespace App\Actions\Admin;

use App\Services\TravelRequestService;

class GetTravelRequestStatisticsAction
{
    public function __construct(
        private TravelRequestService $travelRequestService
    ) {}

    /**
     * Get travel request statistics
     */
    public function execute(): array
    {
        return $this->travelRequestService->getStatistics();
    }
}
