<?php

namespace App\Http\Controllers\API\Admin;

use App\Actions\Admin\GetTravelRequestsAction;
use App\Actions\Admin\GetTravelRequestStatisticsAction;
use App\Actions\Admin\UpdateTravelRequestStatusAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TravelRequestFilterRequest;
use App\Http\Requests\Admin\UpdateTravelRequestStatusRequest;
use App\Http\Resources\Admin\TravelRequestResource;
use App\Models\TravelRequest;
use Illuminate\Http\Request;

class TravelRequestController extends Controller
{
    public function __construct(
        private GetTravelRequestsAction $getTravelRequestsAction,
        private UpdateTravelRequestStatusAction $updateStatusAction,
        private GetTravelRequestStatisticsAction $getStatisticsAction
    ) {}

    /**
     * Lista Todas as Solicitações de Viagem
     */
    public function index(TravelRequestFilterRequest $request)
    {
        $filters = $request->validated();
        $travelRequests = $this->getTravelRequestsAction->execute($filters);

        return TravelRequestResource::collection($travelRequests);
    }

    /**
     * Busca Detalhes de uma Solicitação de Viagem
     */
    public function show(TravelRequest $travelRequest)
    {
        $travelRequest->load(['user', 'approver', 'statusHistory.user']);
        
        return new TravelRequestResource($travelRequest);
    }

    /**
     * Aprova uma Solicitação de Viagem
     */
    public function approve(TravelRequest $travelRequest, Request $request)
    {
        $this->updateStatusAction->execute(
            $travelRequest,
            TravelRequest::STATUS_APPROVED,
            $request->user(),
            $request->input('notes')
        );

        return response()->json([
            'message' => 'Pedido de viagem aprovado com sucesso',
            'travel_request' => new TravelRequestResource($travelRequest->refresh())
        ]);
    }

    /**
     * Rejeita uma Solicitação de Viagem
     */
    public function reject(UpdateTravelRequestStatusRequest $request, TravelRequest $travelRequest)
    {
        $this->updateStatusAction->execute(
            $travelRequest,
            TravelRequest::STATUS_REJECTED,
            $request->user(),
            $request->input('reason')
        );

        return response()->json([
            'message' => 'Pedido de viagem rejeitado com sucesso',
            'travel_request' => new TravelRequestResource($travelRequest->refresh())
        ]);
    }

    /**
     * Cancela uma Solicitação de Viagem
     */
    public function cancel(UpdateTravelRequestStatusRequest $request, TravelRequest $travelRequest)
    {
        $this->updateStatusAction->execute(
            $travelRequest,
            TravelRequest::STATUS_CANCELLED,
            $request->user(),
            $request->input('reason')
        );

        return response()->json([
            'message' => 'Pedido de viagem cancelado com sucesso',
            'travel_request' => new TravelRequestResource($travelRequest->refresh())
        ]);
    }

    /**
     * Obtém Estatísticas de Solicitações de Viagem
     */
    public function statistics()
    {
        $statistics = $this->getStatisticsAction->execute();

        return response()->json([
            'statistics' => $statistics
        ]);
    }
}
