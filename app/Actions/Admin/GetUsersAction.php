<?php

namespace App\Actions\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class GetUsersAction
{
    /**
     * Get users with optional filters
     */
    public function execute(array $filters = []): Collection
    {
        $query = User::query();

        // Filter by role
        if (isset($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        // Filter by department
        if (isset($filters['department'])) {
            $query->where('department', 'like', '%' . $filters['department'] . '%');
        }

        // Filter by active status
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Search by name or email
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Load relationships
        $query->withCount(['travelRequests', 'approvedTravelRequests']);

        // Order by
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        return $query->get();
    }
}
