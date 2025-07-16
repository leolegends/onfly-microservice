<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\TravelRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

trait TestHelpers
{
    use RefreshDatabase;

    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'employee',
            'is_active' => true,
        ], $attributes));
    }

    protected function createAdmin(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'admin',
            'is_active' => true,
        ], $attributes));
    }

    protected function createManager(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'manager',
            'is_active' => true,
        ], $attributes));
    }

    protected function createTravelRequest(array $attributes = []): TravelRequest
    {
        $user = $attributes['user_id'] ?? null;
        if ($user && !is_object($user)) {
            // Se user_id é um ID, não um objeto User, deixe como está
            return TravelRequest::factory()->create(array_merge([
                'status' => TravelRequest::STATUS_REQUESTED,
            ], $attributes));
        }

        // Se não há user_id ou é um objeto User
        if (!$user) {
            $user = $this->createUser();
            $attributes['user_id'] = $user->id;
        }

        return TravelRequest::factory()->create(array_merge([
            'status' => TravelRequest::STATUS_REQUESTED,
        ], $attributes));
    }

    protected function actingAsUser(User $user = null): User
    {
        if (!$user) {
            $user = $this->createUser();
        }

        Sanctum::actingAs($user);
        return $user;
    }

    protected function actingAsAdmin(User $admin = null): User
    {
        if (!$admin) {
            $admin = $this->createAdmin();
        }

        Sanctum::actingAs($admin);
        return $admin;
    }

    protected function actingAsManager(User $manager = null): User
    {
        if (!$manager) {
            $manager = $this->createManager();
        }

        Sanctum::actingAs($manager);
        return $manager;
    }

    protected function authenticateUser(User $user): string
    {
        return $user->createToken('test-token')->plainTextToken;
    }

    protected function assertSuccessResponse($response, $message = null)
    {
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        if ($message) {
            $response->assertJson(['message' => $message]);
        }
    }

    protected function assertErrorResponse($response, int $status, string $message = null)
    {
        $response->assertStatus($status);
        $response->assertJson(['success' => false]);
        
        if ($message) {
            $response->assertJson(['message' => $message]);
        }
    }

    protected function assertValidationErrors($response, array $fields)
    {
        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        
        foreach ($fields as $field) {
            $response->assertJsonValidationErrors($field);
        }
    }
}
