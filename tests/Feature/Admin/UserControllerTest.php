<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Tests\TestCase;
use Tests\Traits\TestHelpers;

class UserControllerTest extends TestCase
{
    use TestHelpers;

    /** @test */
    public function admin_can_list_users()
    {
        $admin = $this->actingAsAdmin();
        $user1 = $this->createUser(['name' => 'John Doe']);
        $user2 = $this->createUser(['name' => 'Jane Smith']);

        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'department',
                    'position',
                    'phone',
                    'is_active',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    /** @test */
    public function admin_can_create_new_user()
    {
        $admin = $this->actingAsAdmin();

        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'role' => 'employee',
            'department' => 'IT',
            'position' => 'Developer',
            'phone' => '(11) 99999-9999',
        ];

        $response = $this->postJson('/api/admin/users', $userData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'role',
                'department',
                'position',
                'phone',
                'is_active',
                'created_at',
                'updated_at',
            ],
        ]);

        $response->assertJsonPath('data.name', 'New User');
        $response->assertJsonPath('data.email', 'newuser@example.com');
        $response->assertJsonPath('data.role', 'employee');
        $response->assertJsonPath('data.is_active', true);
    }

    /** @test */
    public function admin_can_view_specific_user()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser(['name' => 'John Doe']);

        $response = $this->getJson("/api/admin/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'role',
                'department',
                'position',
                'phone',
                'is_active',
                'created_at',
                'updated_at',
            ],
        ]);

        $response->assertJsonPath('data.id', $user->id);
        $response->assertJsonPath('data.name', 'John Doe');
    }

    /** @test */
    public function admin_can_update_user()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'manager',
            'department' => 'HR',
            'is_active' => false,
        ];

        $response = $this->putJson("/api/admin/users/{$user->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Updated Name');
        $response->assertJsonPath('data.email', 'updated@example.com');
        $response->assertJsonPath('data.role', 'manager');
        $response->assertJsonPath('data.department', 'HR');
        $response->assertJsonPath('data.is_active', false);
    }

    /** @test */
    public function admin_can_deactivate_user()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser();

        $response = $this->deleteJson("/api/admin/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Usuário desativado com sucesso',
        ]);

        $response->assertJsonPath('user.is_active', false);
    }

    /** @test */
    public function admin_can_filter_users_by_role()
    {
        $admin = $this->actingAsAdmin();
        $employee = $this->createUser(['role' => 'employee']);
        $manager = $this->createManager();

        $response = $this->getJson('/api/admin/users?role=employee');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        foreach ($data as $user) {
            $this->assertEquals('employee', $user['role']);
        }
    }

    /** @test */
    public function admin_can_filter_users_by_department()
    {
        $admin = $this->actingAsAdmin();
        $user1 = $this->createUser(['department' => 'IT']);
        $user2 = $this->createUser(['department' => 'HR']);

        $response = $this->getJson('/api/admin/users?department=IT');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        foreach ($data as $user) {
            $this->assertEquals('IT', $user['department']);
        }
    }

    /** @test */
    public function admin_can_search_users()
    {
        $admin = $this->actingAsAdmin();
        $user = $this->createUser(['name' => 'John Doe']);

        $response = $this->getJson('/api/admin/users?search=John');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertCount(1, $data);
        $this->assertEquals('John Doe', $data[0]['name']);
    }

    /** @test */
    public function user_creation_requires_valid_data()
    {
        $admin = $this->actingAsAdmin();

        $response = $this->postJson('/api/admin/users', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123', // Too short
            'role' => 'invalid-role',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
    }

    /** @test */
    public function cannot_create_user_with_duplicate_email()
    {
        $admin = $this->actingAsAdmin();
        $existingUser = $this->createUser(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/admin/users', [
            'name' => 'New User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'role' => 'employee',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function non_admin_user_cannot_access_user_management()
    {
        $user = $this->actingAsUser();

        $response = $this->getJson('/api/admin/users');
        $response->assertStatus(403);

        $response = $this->postJson('/api/admin/users', []);
        $response->assertStatus(403);

        $response = $this->getJson('/api/admin/users/1');
        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_user_management()
    {
        $response = $this->getJson('/api/admin/users');
        $response->assertStatus(401);

        $response = $this->postJson('/api/admin/users', []);
        $response->assertStatus(401);
    }
}
