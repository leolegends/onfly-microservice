<?php

namespace App\Http\Controllers\API\User;

use App\Actions\User\CancelTravelRequestAction;
use App\Actions\User\CreateTravelRequestAction;
use App\Actions\User\ListTravelRequestsAction;
use App\Actions\User\UpdateTravelRequestAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateTravelRequestRequest;
use App\Http\Requests\User\UpdateTravelRequestRequest;
use App\Http\Resources\User\TravelRequestResource;
use App\Models\TravelRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TravelRequestController extends Controller
{
    /**
     * Lista Solicitações de Viagem do Usuário
     */
    public function index(Request $request, ListTravelRequestsAction $action): JsonResponse
    {

        try {
            $travelRequests = $action->execute($request);

            return response()->json([
                'success' => true,
                'data' => TravelRequestResource::collection($travelRequests),
                'meta' => [
                    'current_page' => $travelRequests->currentPage(),
                    'last_page' => $travelRequests->lastPage(),
                    'per_page' => $travelRequests->perPage(),
                    'total' => $travelRequests->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar solicitações: ' . $e->getMessage(),
            ], 500);
        }

    }

    /**
     * Cria Nova Solicitação de Viagem
     */
    public function store(CreateTravelRequestRequest $request, CreateTravelRequestAction $action): JsonResponse
    {
        try {

            $travelRequest = $action->execute($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Solicitação de viagem criada com sucesso.',
                'data' => new TravelRequestResource($travelRequest),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar solicitação: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Busca Detalhes de uma Solicitação de Viagem
     */
    public function show(TravelRequest $travelRequest): JsonResponse
    {
        // Verificar se o usuário pode ver esta solicitação
        if ($travelRequest->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para ver esta solicitação.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new TravelRequestResource($travelRequest->load(['user', 'statusHistory.user'])),
        ]);
    }

    /**
     * Atualiza uma Solicitação de Viagem
     */
    public function update(UpdateTravelRequestRequest $request, TravelRequest $travelRequest, UpdateTravelRequestAction $action): JsonResponse
    {
        try {
            $updatedTravelRequest = $action->execute($travelRequest, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Solicitação de viagem atualizada com sucesso.',
                'data' => new TravelRequestResource($updatedTravelRequest),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Cancela uma Solicitação de Viagem
     */
    public function cancel(TravelRequest $travelRequest, CancelTravelRequestAction $action): JsonResponse
    {

        try {
            
            $cancelledTravelRequest = $action->execute($travelRequest);

            return response()->json([
                'success' => true,
                'message' => 'Solicitação de viagem cancelada com sucesso.',
                'data' => new TravelRequestResource($cancelledTravelRequest),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
