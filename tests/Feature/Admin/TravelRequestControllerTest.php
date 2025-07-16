<?php

namespace Tests\Feature\Admin;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\TravelRequest;
use Tests\Traits\TestHelpers;

class TravelRequestControllerTest extends TestCase
{
    use TestHelpers;

    /** @test */
    public function admin_can_list_all_travel_requests()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();

        $payload = [
            'user_id' => $user->id,
            'destination' => 'São Paulo',
            'departure_date' => Carbon::now()->addDays(1)->format('d-m-Y'),
            'return_date' => Carbon::now()->addDays(5)->format('d-m-Y'),
            'purpose' => 'Reunião com cliente',
            'budget' => 1500.00,
        ];

        $request1 = $this->createTravelRequest($payload);

        $request2 = $this->createTravelRequest($payload);

        $response = $this->getJson('/api/admin/travel-requests');

        $response->assertStatus(200);

        $response->assertJsonStructure([
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
                'rejection_reason',
                'approved_at',
                'cancelled_at',
                'created_at',
                'updated_at',
                'user',
                'approver',
            ],
            ],
        ]);

    }

    /** @test */
    public function admin_can_view_specific_travel_request()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();

        $payload = [
            'user_id' => $user->id,
            'destination' => 'São Paulo',
            'departure_date' => Carbon::now()->addDays(1)->format('d-m-Y'),
            'return_date' => Carbon::now()->addDays(5)->format('d-m-Y'),
            'purpose' => 'Reunião com cliente',
            'budget' => 1500.00,
        ];

        $request = $this->createTravelRequest($payload);

        $response = $this->getJson("/api/admin/travel-requests/{$request->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'requestor_name',
                'destination',
                'departure_date',
                'return_date',
                'status',
                'purpose',
                'estimated_cost',
                'justification',
                'rejection_reason',
                'approved_at',
                'cancelled_at',
                'created_at',
                'updated_at',
                'user',
                'approver',
            ],
        ]);
    }

    /** @test */
    public function admin_can_approve_travel_request()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();

        $payload = [
            'user_id' => $user->id,
            'destination' => 'São Paulo',
            'departure_date' => Carbon::now()->addDays(1)->format('d-m-Y'),
            'return_date' => Carbon::now()->addDays(5)->format('d-m-Y'),
            'purpose' => 'Reunião com cliente',
            'budget' => 1500.00,
        ];

        $request = $this->createTravelRequest($payload);        

        $response = $this->patchJson("/api/admin/travel-requests/{$request->id}/approve");

        $response->assertStatus(200);
        $response->assertJsonPath('travel_request.status', 'approved');
        $response->assertJson([
            'message' => 'Pedido de viagem aprovado com sucesso',
        ]);
    }

    /** @test */
    public function admin_can_reject_travel_request()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();
        $request = $this->createTravelRequest([
            'user_id' => $user->id,
            'status' => 'requested',
        ]);

        $response = $this->patchJson("/api/admin/travel-requests/{$request->id}/reject", [
            'reason' => 'Rejeitado por falta de orçamento',
            'status' => 'rejected',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('travel_request.status', 'rejected');
        $response->assertJson([
            'message' => 'Pedido de viagem rejeitado com sucesso',
        ]);
    }

    /** @test */
    public function admin_can_cancel_travel_request()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();
        $request = $this->createTravelRequest([
            'user_id' => $user->id,
            'status' => 'approved',
        ]);

        $response = $this->patchJson("/api/admin/travel-requests/{$request->id}/cancel", [
            'reason' => 'Cancelado devido a mudança de planos',
            'status' => 'cancelled',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('travel_request.status', 'cancelled');
        $response->assertJson([
            'message' => 'Pedido de viagem cancelado com sucesso',
        ]);
    }

    /** @test */
    public function admin_can_filter_travel_requests_by_status()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();
        $requested = $this->createTravelRequest([
            'user_id' => $user->id,
            'status' => 'requested',
        ]);
        $approved = $this->createTravelRequest([
            'user_id' => $user->id,
            'status' => 'approved',
        ]);

        $response = $this->getJson('/api/admin/travel-requests?status=requested');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        foreach ($data as $request) {
            $this->assertEquals('requested', $request['status']);
        }
    }

    /** @test */
    public function admin_can_filter_travel_requests_by_date_range()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();
        $request = $this->createTravelRequest([
            'user_id' => $user->id,
            'start_date' => '2024-02-01',
            'end_date' => '2024-02-05',
        ]);

        $response = $this->getJson('/api/admin/travel-requests?start_date=2024-01-01&end_date=2024-02-28');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertCount(1, $data);
        $this->assertEquals($request->id, $data[0]['id']);
    }

    /** @test */
    public function admin_can_filter_travel_requests_by_user()
    {
        $admin = $this->actingAsAdmin();
        $user1 = $this->createUser(['name' => 'User 1']);
        $user2 = $this->createUser(['name' => 'User 2']);
        $request1 = $this->createTravelRequest(['user_id' => $user1->id]);
        $request2 = $this->createTravelRequest(['user_id' => $user2->id]);

        $response = $this->getJson("/api/admin/travel-requests?user_id={$user1->id}");

        $response->assertStatus(200);
        $data = $response->json('data');
        
        foreach ($data as $request) {
            $this->assertEquals($user1->id, $request['user']['id']);
        }
    }

    /** @test */
    public function admin_can_search_travel_requests()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();
        $request = $this->createTravelRequest([
            'user_id' => $user->id,
            'destination' => 'São Paulo',
            'purpose' => 'Reunião com cliente',
        ]);

        $response = $this->getJson('/api/admin/travel-requests?search=São Paulo');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertCount(1, $data);
        $this->assertEquals($request->id, $data[0]['id']);
    }


    /** @test */
    public function cannot_approve_already_approved_request()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();
        $request = $this->createTravelRequest([
            'user_id' => $user->id,
            'status' => 'approved',
        ]);

        $response = $this->patchJson("/api/admin/travel-requests/{$request->id}/approve", [
            'comment' => 'Tentativa de aprovação novamente',
        ]);

        $response->assertJson([
            'message' => 'User cannot update this travel request status',
        ]);
    }

    /** @test */
    public function cannot_reject_already_rejected_request()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();
        $request = $this->createTravelRequest([
            'user_id' => $user->id,
            'status' => 'rejected',
        ]);

        $response = $this->patchJson("/api/admin/travel-requests/{$request->id}/reject", [
            'reason' => 'Tentativa de rejeição novamente',
            'status' => 'rejected',
        ]);

        $response->assertJson([
            'message' => 'User cannot update this travel request status',
        ]);
    }

    /** @test */
    public function cannot_cancel_already_cancelled_request()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();
        $request = $this->createTravelRequest([
            'user_id' => $user->id,
            'status' => 'cancelled',
        ]);

        $response = $this->patchJson("/api/admin/travel-requests/{$request->id}/cancel", [
            'reason' => 'Tentativa de cancelamento novamente',
            'status' => 'cancelled',
        ]);

        $response->assertJson([
            'message' => 'User cannot update this travel request status',
        ]);
    }

    /** @test */
    public function non_admin_user_cannot_access_admin_travel_requests()
    {
        $user = $this->actingAsUser();

        $response = $this->getJson('/api/admin/travel-requests');
        $response->assertJson([
            'message' => 'Access denied. Admin privileges required.',
        ]);

    }

    /** @test */
    public function unauthenticated_user_cannot_access_admin_travel_requests()
    {
        $response = $this->getJson('/api/admin/travel-requests');
        $response->assertStatus(401);

        $response = $this->patchJson('/api/admin/travel-requests/1/approve', []);
        $response->assertStatus(401);
    }
}
