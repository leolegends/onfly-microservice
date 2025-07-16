<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'department' => $this->department,
            'is_active' => $this->is_active,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'travel_requests_count' => $this->whenCounted('travelRequests'),
            'statistics' => $this->when($this->relationLoaded('travelRequests'), function () {
                return [
                    'total_requests' => $this->travelRequests->count(),
                    'approved_requests' => $this->travelRequests->where('status', 'approved')->count(),
                    'pending_requests' => $this->travelRequests->where('status', 'requested')->count(),
                    'cancelled_requests' => $this->travelRequests->where('status', 'cancelled')->count(),
                ];
            }),
        ];
    }
}
