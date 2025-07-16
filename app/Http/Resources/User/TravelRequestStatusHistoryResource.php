<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelRequestStatusHistoryResource extends JsonResource
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
            'previous_status' => $this->previous_status,
            'previous_status_label' => $this->getStatusLabel($this->previous_status),
            'new_status' => $this->new_status,
            'new_status_label' => $this->getStatusLabel($this->new_status),
            'reason' => $this->reason,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'changed_by' => $this->when($this->relationLoaded('user'), function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'role' => $this->user->role,
                ];
            }),
        ];
    }

    /**
     * Get status label in Portuguese
     */
    private function getStatusLabel(?string $status): ?string
    {
        if (!$status) return null;

        return match ($status) {
            'requested' => 'Solicitado',
            'approved' => 'Aprovado',
            'cancelled' => 'Cancelado',
            'rejected' => 'Rejeitado',
            default => 'Desconhecido'
        };
    }
}
