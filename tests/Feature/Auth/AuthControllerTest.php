<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;
use Tests\Traits\TestHelpers;

class AuthControllerTest extends TestCase
{
    use TestHelpers;

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'user' => ['id', 'name', 'email', 'role'],
            'token',
            'token_type',
            'expires_in',
            'expires_at',
        ]);

        $this->assertEquals('Bearer', $response->json('token_type'));
        $this->assertEquals(7200, $response->json('expires_in'));
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'The provided credentials are incorrect.',
        ]);
    }

    /** @test */
    public function inactive_user_cannot_login()
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'is_active' => false,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'User account is inactive.',
        ]);
    }

    /** @test */
    public function login_requires_email_and_password()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Validation failed',
            'errors' => [
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.'],
            ],
        ]);
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = $this->actingAsUser();
        $token = $this->authenticateUser($user);

        $response = $this->postJson('/api/logout', [], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Logged out successfully',
        ]);
    }

    /** @test */
    public function authenticated_user_can_get_profile()
    {
        $user = $this->actingAsUser();

        $response = $this->getJson('/api/me');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id', 'name', 'email', 'role', 'department', 'is_active',
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }
}
