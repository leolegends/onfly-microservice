<?php

namespace App\Actions\User;

use App\Models\TravelRequest;
use App\Services\TravelRequestService;
use Illuminate\Support\Facades\Auth;

class UpdateTravelRequestAction
{
    public function __construct(
        private TravelRequestService $travelRequestService
    ) {}

    public function execute(TravelRequest $travelRequest, array $data): TravelRequest
    {
        // Verificar se o usuário pode editar esta solicitação
        if ($travelRequest->user_id !== Auth::id()) {
            throw new \Exception('Você não tem permissão para editar esta solicitação.');
        }

        // A verificação de status é feita no Form Request
        $travelRequest->update($data);

        return $travelRequest;
    }
}
