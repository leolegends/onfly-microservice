<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\NotificationResource;
use App\Models\TravelRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Lista Notificações do Usuário
     */
    public function index(): JsonResponse
    {
        // try {
            $userId = Auth::id();
            
            $notifications = TravelRequest::with(['statusHistory' => function($query) use ($userId) {
                    $query->where('user_id', '!=', $userId)
                        ->latest()
                        ->take(1);
                }, 'statusHistory.user'])
                ->where('user_id', $userId)
                ->whereIn('status', [
                    TravelRequest::STATUS_APPROVED,
                    TravelRequest::STATUS_REJECTED,
                    TravelRequest::STATUS_CANCELLED
                ])
                ->whereHas('statusHistory', function($query) use ($userId) {
                    $query->where('user_id', '!=', $userId);
                })
                ->orderBy('updated_at', 'desc')
                ->take(50)
                ->get()
                ->map(function($travelRequest) {
                    $statusHistory = $travelRequest->statusHistory->first();
                    
                    return [
                        'id' => $travelRequest->id,
                        'type' => 'travel_request_status_change',
                        'title' => $this->getNotificationTitle($travelRequest->status),
                        'message' => $this->getNotificationMessage($travelRequest),
                        'data' => [
                            'travel_request_id' => $travelRequest->id,
                            'status' => $travelRequest->status,
                            'changed_by' => $statusHistory->user->name ?? 'Sistema',
                            'changed_at' => $statusHistory->created_at,
                        ],
                        'read' => false,
                        'created_at' => $statusHistory->created_at,
                    ];
            });

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'meta' => [
                    'total' => $notifications->count(),
                    'unread' => $notifications->count(),
                ],
            ]);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Erro ao buscar notificações: ' . $e->getMessage(),
        //     ], 500);
        // }
    }

    /**
     * Obtém Estatísticas de Notificações
     */
    public function stats(): JsonResponse
    {
        try {
            $userId = Auth::id();
            
            $pendingCount = TravelRequest::where('user_id', $userId)
                ->where('status', TravelRequest::STATUS_REQUESTED)
                ->count();
                
            $approvedCount = TravelRequest::where('user_id', $userId)
                ->where('status', TravelRequest::STATUS_APPROVED)
                ->count();
                
            $rejectedCount = TravelRequest::where('user_id', $userId)
                ->where('status', TravelRequest::STATUS_REJECTED)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'pending_requests' => $pendingCount,
                    'approved_requests' => $approvedCount,
                    'rejected_requests' => $rejectedCount,
                    'total_requests' => $pendingCount + $approvedCount + $rejectedCount,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar estatísticas: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getNotificationTitle(string $status): string
    {
        return match ($status) {
            TravelRequest::STATUS_APPROVED => 'Solicitação Aprovada',
            TravelRequest::STATUS_REJECTED => 'Solicitação Rejeitada',
            TravelRequest::STATUS_CANCELLED => 'Solicitação Cancelada',
            default => 'Atualização de Solicitação',
        };
    }

    private function getNotificationMessage(TravelRequest $travelRequest): string
    {
        $destination = $travelRequest->destination;
        
        return match ($travelRequest->status) {
            TravelRequest::STATUS_APPROVED => "Sua solicitação de viagem para {$destination} foi aprovada.",
            TravelRequest::STATUS_REJECTED => "Sua solicitação de viagem para {$destination} foi rejeitada.",
            TravelRequest::STATUS_CANCELLED => "Sua solicitação de viagem para {$destination} foi cancelada.",
            default => "Sua solicitação de viagem para {$destination} foi atualizada.",
        };
    }
}
