<?php

namespace Tests\Feature\Admin;

use App\Models\TravelRequest;
use Tests\TestCase;
use Tests\Traits\TestHelpers;

class TravelRequestControllerTest extends TestCase
{
    use TestHelpers;

    /** @test */
    public function admin_can_list_all_travel_requests()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();
        $request1 = $this->createTravelRequest(['user_id' => $user->id]);
        $request2 = $this->createTravelRequest(['user_id' => $user->id]);

        $response = $this->getJson('/api/admin/travel-requests');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'destination',
                    'start_date',
                    'end_date',
                    'purpose',
                    'status',
                    'budget',
                    'created_at',
                    'updated_at',
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function admin_can_view_specific_travel_request()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();
        $request = $this->createTravelRequest(['user_id' => $user->id]);

        $response = $this->getJson("/api/admin/travel-requests/{$request->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'destination',
                'start_date',
                'end_date',
                'purpose',
                'status',
                'budget',
                'created_at',
                'updated_at',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'status_history' => [
                    '*' => [
                        'id',
                        'status',
                        'comment',
                        'created_at',
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function admin_can_approve_travel_request()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();
        $request = $this->createTravelRequest([
            'user_id' => $user->id,
            'status' => 'requested',
        ]);

        $response = $this->patchJson("/api/admin/travel-requests/{$request->id}/approve", [
            'comment' => 'Aprovado pela administração',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.status', 'approved');
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
            'comment' => 'Rejeitado por falta de orçamento',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.status', 'rejected');
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
            'comment' => 'Cancelado devido a mudança de planos',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.status', 'cancelled');
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
            $this->assertEquals($user1->id, $request['user_id']);
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
    public function admin_can_export_travel_requests()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();
        $request = $this->createTravelRequest(['user_id' => $user->id]);

        $response = $this->getJson('/api/admin/travel-requests/export?format=csv');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="travel_requests.csv"');
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

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Apenas pedidos com status "requested" podem ser aprovados',
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
            'comment' => 'Tentativa de rejeição novamente',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Apenas pedidos com status "requested" podem ser rejeitados',
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
            'comment' => 'Tentativa de cancelamento novamente',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Apenas pedidos com status diferente de "requested" podem ser cancelados',
        ]);
    }

    /** @test */
    public function non_admin_user_cannot_access_admin_travel_requests()
    {
        $user = $this->actingAsUser();

        $response = $this->getJson('/api/admin/travel-requests');
        $response->assertStatus(403);

        $response = $this->patchJson('/api/admin/travel-requests/1/approve', []);
        $response->assertStatus(403);
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
