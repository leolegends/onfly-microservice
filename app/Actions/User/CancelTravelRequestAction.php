<?php

namespace App\Actions\User;

use App\Models\TravelRequest;
use App\Services\TravelRequestService;
use Illuminate\Support\Facades\Auth;

class CancelTravelRequestAction
{
    public function __construct(
        private TravelRequestService $travelRequestService
    ) {}

    public function execute(TravelRequest $travelRequest): TravelRequest
    {
        // Verificar se o usuário pode cancelar esta solicitação
        if ($travelRequest->user_id !== Auth::id()) {
            throw new \Exception('Você não tem permissão para cancelar esta solicitação.');
        }

        // Verificar se a solicitação pode ser cancelada
        if (!$this->canBeCancelled($travelRequest)) {
            throw new \Exception('Esta solicitação não pode ser cancelada.');
        }

        $travelRequest->update([
            'status' => TravelRequest::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);

        // O histórico é criado automaticamente pelo Observer

        return $travelRequest;
    }

    private function canBeCancelled(TravelRequest $travelRequest): bool
    {
        // Pode cancelar se estiver em status "requested"
        return in_array($travelRequest->status, [
            TravelRequest::STATUS_REQUESTED,
        ]);
    }
}
