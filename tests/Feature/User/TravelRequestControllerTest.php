<?php

namespace Tests\Feature\User;

use App\Models\TravelRequest;
use Tests\TestCase;
use Tests\Traits\TestHelpers;

class TravelRequestControllerTest extends TestCase
{
    use TestHelpers;

    /** @test */
    public function user_can_list_their_travel_requests()
    {
        $user = $this->actingAsUser();
        $this->createTravelRequest(['user_id' => $user->id]);
        $this->createTravelRequest(['user_id' => $user->id]);

        $response = $this->getJson('/api/user/travel-requests');

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'requestor_name',
                    'destination',
                    'departure_date',
                    'return_date',
                    'status',
                    'purpose',
                    'estimated_cost',
                    'justification',
                    'created_at',
                    'updated_at',
                ],
            ],
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
            ],
        ]);
    }

    /** @test */
    public function user_can_create_travel_request()
    {
        $user = $this->actingAsUser();

        $travelData = [
            'requestor_name' => 'John Doe',
            'destination' => 'São Paulo',
            'departure_date' => '16-08-2025',
            'return_date' => '20-08-2025',
            'purpose' => 'Business meeting',
            'estimated_cost' => 1500.00,
            'justification' => 'Important client meeting',
        ];

        $response = $this->postJson('/api/user/travel-requests', $travelData);

        $response->assertStatus(201);
        $this->assertSuccessResponse($response, 'Solicitação de viagem criada com sucesso.');
        $response->assertJsonPath('data.requestor_name', 'John Doe');
        $response->assertJsonPath('data.destination', 'São Paulo');
        $response->assertJsonPath('data.status', TravelRequest::STATUS_REQUESTED);
    }

    /** @test */
    public function user_can_view_their_travel_request()
    {
        $user = $this->actingAsUser();
        $travelRequest = $this->createTravelRequest(['user_id' => $user->id]);

        $response = $this->getJson("/api/user/travel-requests/{$travelRequest->id}");

        $this->assertSuccessResponse($response);
        $response->assertJsonPath('data.id', $travelRequest->id);
        $response->assertJsonPath('data.destination', $travelRequest->destination);
    }

    /** @test */
    public function user_cannot_view_other_users_travel_request()
    {
        $user = $this->actingAsUser();
        $otherUser = $this->createUser();
        $travelRequest = $this->createTravelRequest(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/user/travel-requests/{$travelRequest->id}");

        $this->assertErrorResponse($response, 403, 'Você não tem permissão para ver esta solicitação.');
    }

    /** @test */
    public function user_can_update_their_pending_travel_request()
    {
        $user = $this->actingAsUser();
        $travelRequest = $this->createTravelRequest(['user_id' => $user->id, 'status' => TravelRequest::STATUS_REQUESTED]);

        $updateData = [
            'destination' => 'Rio de Janeiro',
            'purpose' => 'Updated purpose',
        ];

        $response = $this->putJson("/api/user/travel-requests/{$travelRequest->id}", $updateData);

        $this->assertSuccessResponse($response, 'Solicitação de viagem atualizada com sucesso.');
        $response->assertJsonPath('data.destination', 'Rio de Janeiro');
        $response->assertJsonPath('data.purpose', 'Updated purpose');
    }

    /** @test */
    public function user_cannot_update_non_pending_travel_request()
    {
        $user = $this->actingAsUser();
        $travelRequest = $this->createTravelRequest(["user_id" => $user->id, 'status' => TravelRequest::STATUS_APPROVED]);

        $response = $this->putJson("/api/user/travel-requests/{$travelRequest->id}", [
            'destination' => 'Rio de Janeiro',
        ]);

        $this->assertErrorResponse($response, 403);
    }

    /** @test */
    public function user_can_cancel_their_travel_request()
    {
        $user = $this->actingAsUser();
        $travelRequest = $this->createTravelRequest(["user_id" => $user->id, 'status' => TravelRequest::STATUS_REQUESTED]);

        $response = $this->patchJson("/api/user/travel-requests/{$travelRequest->id}/cancel");

        $this->assertSuccessResponse($response, 'Solicitação de viagem cancelada com sucesso.');
        $response->assertJsonPath('data.status', TravelRequest::STATUS_CANCELLED);
    }

    /** @test */
    public function user_cannot_cancel_other_users_travel_request()
    {
        $user = $this->actingAsUser();
        $otherUser = $this->createUser();
        $travelRequest = $this->createTravelRequest(['user_id' => $otherUser->id]);

        $response = $this->patchJson("/api/user/travel-requests/{$travelRequest->id}/cancel");

        $this->assertErrorResponse($response, 400, 'Você não tem permissão para cancelar esta solicitação.');
    }

    /** @test */
    public function travel_request_creation_requires_valid_data()
    {
        $user = $this->actingAsUser();

        $response = $this->postJson('/api/user/travel-requests', [
            'requestor_name' => '',
            'destination' => '',
            'departure_date' => 'invalid-date',
            'return_date' => '15-08-2025', // Before departure
            'purpose' => '',
            'justification' => '',
        ]);

        $this->assertValidationErrors($response, [
            'requestor_name',
            'destination',
            'departure_date',
            'return_date',
            'purpose',
            'justification',
        ]);
    }

    /** @test */
    public function return_date_must_be_after_departure_date()
    {
        $user = $this->actingAsUser();

        $response = $this->postJson('/api/user/travel-requests', [
            'requestor_name' => 'John Doe',
            'destination' => 'São Paulo',
            'departure_date' => '20-08-2025',
            'return_date' => '18-08-2025',
            'purpose' => 'Business meeting',
            'justification' => 'Important client meeting',
        ]);

        $this->assertValidationErrors($response, ['return_date']);
    }

    /** @test */
    public function user_can_filter_travel_requests_by_status()
    {
        $user = $this->actingAsUser();
        $this->createTravelRequest(["user_id" => $user->id, 'status' => TravelRequest::STATUS_REQUESTED]);
        $this->createTravelRequest(["user_id" => $user->id, 'status' => TravelRequest::STATUS_APPROVED]);

        $response = $this->getJson('/api/user/travel-requests?status=' . TravelRequest::STATUS_REQUESTED);

        $this->assertSuccessResponse($response);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.status', TravelRequest::STATUS_REQUESTED);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_travel_request_routes()
    {
        $response = $this->getJson('/api/user/travel-requests');
        $response->assertStatus(401);

        $response = $this->postJson('/api/user/travel-requests', []);
        $response->assertStatus(401);

        $response = $this->getJson('/api/user/travel-requests/1');
        $response->assertStatus(401);
    }
}
