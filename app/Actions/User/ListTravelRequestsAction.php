<?php

namespace App\Actions\User;

use App\Models\TravelRequest;
use App\Services\TravelRequestService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ListTravelRequestsAction
{
    public function __construct(
        private TravelRequestService $travelRequestService
    ) {}

    public function execute(Request $request): LengthAwarePaginator
    {
        $filters = [
            'status' => $request->get('status'),
            'departure_date_from' => $request->get('departure_date_from'),
            'departure_date_to' => $request->get('departure_date_to'),
            'search' => $request->get('search'),
        ];

        $query = TravelRequest::with(['user', 'statusHistory'])
            ->where('user_id', Auth::id());

        // Apply filters
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['departure_date_from']) {
            $query->whereDate('departure_date', '>=', $filters['departure_date_from']);
        }

        if ($filters['departure_date_to']) {
            $query->whereDate('departure_date', '<=', $filters['departure_date_to']);
        }

        if ($filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('destination', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('purpose', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('requestor_name', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));
    }
}
