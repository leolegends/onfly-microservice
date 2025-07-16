<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelRequestResource extends JsonResource
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
            'requestor_name' => $this->requestor_name,
            'destination' => $this->destination,
            'departure_date' => $this->departure_date,
            'return_date' => $this->return_date,
            'status' => $this->status,
            'purpose' => $this->purpose,
            'estimated_cost' => $this->estimated_cost,
            'justification' => $this->justification,
            'rejection_reason' => $this->rejection_reason,
            'approved_at' => $this->approved_at,
            'cancelled_at' => $this->cancelled_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'approver' => new UserResource($this->whenLoaded('approver')),
            'status_history' => TravelRequestStatusHistoryResource::collection($this->whenLoaded('statusHistory')),
        ];
    }
}
