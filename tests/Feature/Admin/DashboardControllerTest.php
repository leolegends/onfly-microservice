<?php

namespace Tests\Feature\Admin;

use App\Models\TravelRequest;
use Tests\TestCase;
use Tests\Traits\TestHelpers;

class DashboardControllerTest extends TestCase
{
    use TestHelpers;

    /** @test */
    public function admin_can_view_dashboard_statistics()
    {
        $admin = $this->actingAsAdmin();
        
        // Create test data
        $user1 = $this->createUser();
        $user2 = $this->createManager();
        $this->createTravelRequest(['user_id' => $user1->id, 'status' => TravelRequest::STATUS_REQUESTED]);
        $this->createTravelRequest(['user_id' => $user2->id, 'status' => TravelRequest::STATUS_APPROVED]);

        $response = $this->getJson('/api/admin/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'dashboard' => [
                'users' => [
                    'total',
                    'active',
                    'inactive',
                    'by_role',
                ],
                'travel_requests' => [
                    'total',
                    'pending',
                    'approved',
                    'cancelled',
                    'rejected',
                    'by_month',
                ],
                'recent_activities' => [
                    'recent_requests',
                    'recent_users',
                ],
            ],
        ]);
    }
    
    /** @test */
    public function admin_can_view_system_health()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->getJson('/api/admin/dashboard/health');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'database' => [
                'status',
                'message',
            ],
            'storage' => [
                'status',
                'free_space',
                'total_space',
                'usage_percentage',
            ],
            'memory' => [
                'current',
                'peak',
            ],
            'timestamp',
        ]);
    }

    /** @test */
    public function dashboard_shows_correct_user_statistics()
    {
        $admin = $this->actingAsAdmin();
        
        // Create test users
        $this->createUser(['role' => 'employee']);
        $this->createManager();
        $this->createUser(['is_active' => false]);

        $response = $this->getJson('/api/admin/dashboard');

        $response->assertStatus(200);
        $dashboard = $response->json('dashboard');
        
        // Total users including admin
        $this->assertGreaterThanOrEqual(4, $dashboard['users']['total']);
        $this->assertGreaterThanOrEqual(3, $dashboard['users']['active']);
        $this->assertGreaterThanOrEqual(1, $dashboard['users']['inactive']);
    }

    /** @test */
    public function dashboard_shows_correct_travel_request_statistics()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();
        
        // Create travel requests with different statuses
        $this->createTravelRequest(['user_id' => $user->id, 'status' => TravelRequest::STATUS_REQUESTED]);
        $this->createTravelRequest(['user_id' => $user->id, 'status' => TravelRequest::STATUS_APPROVED]);
        $this->createTravelRequest(['user_id' => $user->id, 'status' => TravelRequest::STATUS_CANCELLED]);

        $response = $this->getJson('/api/admin/dashboard');

        $response->assertStatus(200);
        $dashboard = $response->json('dashboard');
        
        $this->assertEquals(3, $dashboard['travel_requests']['total']);
        $this->assertEquals(1, $dashboard['travel_requests']['pending']);
        $this->assertEquals(1, $dashboard['travel_requests']['approved']);
        $this->assertEquals(1, $dashboard['travel_requests']['cancelled']);
    }

    /** @test */
    public function non_admin_user_cannot_access_dashboard()
    {
        $user = $this->actingAsUser();

        $response = $this->getJson('/api/admin/dashboard');

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->getJson('/api/admin/dashboard');

        $response->assertStatus(401);
    }

    /** @test */
    public function health_check_returns_healthy_status()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->getJson('/api/admin/dashboard/health');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'healthy');
        $response->assertJsonPath('database.status', 'connected');
        $response->assertJsonPath('storage.status', 'ok');
    }

    
}
