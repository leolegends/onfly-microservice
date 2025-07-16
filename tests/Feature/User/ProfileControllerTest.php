<?php

namespace Tests\Feature\User;

use App\Models\User;
use Tests\TestCase;
use Tests\Traits\TestHelpers;

class ProfileControllerTest extends TestCase
{
    use TestHelpers;

    /** @test */
    public function user_can_view_their_profile()
    {
        $user = $this->actingAsUser();

        $response = $this->getJson('/api/user/profile');

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'success',
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
    }

    /** @test */
    public function user_can_update_their_profile()
    {
        $user = $this->actingAsUser();

        $updateData = [
            'name' => 'Updated Name',
            'department' => 'Updated Department',
            'position' => 'Updated Position',
            'phone' => '(11) 99999-9999',
        ];

        $response = $this->putJson('/api/user/profile', $updateData);

        $this->assertSuccessResponse($response, 'Perfil atualizado com sucesso.');
        $response->assertJsonPath('data.name', 'Updated Name');
        $response->assertJsonPath('data.department', 'Updated Department');
        $response->assertJsonPath('data.position', 'Updated Position');
        $response->assertJsonPath('data.phone', '(11) 99999-9999');
    }

    /** @test */
    public function user_can_update_their_email()
    {
        $user = $this->actingAsUser();

        $response = $this->putJson('/api/user/profile', [
            'email' => 'newemail@example.com',
        ]);

        $this->assertSuccessResponse($response, 'Perfil atualizado com sucesso.');
        $response->assertJsonPath('data.email', 'newemail@example.com');
    }

    /** @test */
    public function user_cannot_update_email_to_existing_one()
    {
        $user1 = $this->actingAsUser();
        $user2 = $this->createUser(['email' => 'existing@example.com']);

        $response = $this->putJson('/api/user/profile', [
            'email' => 'existing@example.com',
        ]);

        $this->assertValidationErrors($response, ['email']);
    }

    /** @test */
    public function user_can_change_password()
    {
        $user = $this->createUser([
            'password' => bcrypt('oldpassword'),
        ]);
        $this->actingAsUser($user);

        $response = $this->putJson('/api/user/profile/change-password', [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $this->assertSuccessResponse($response, 'Senha alterada com sucesso.');
    }

    /** @test */
    public function user_cannot_change_password_with_wrong_current_password()
    {
        $user = $this->createUser([
            'password' => bcrypt('oldpassword'),
        ]);
        $this->actingAsUser($user);

        $response = $this->putJson('/api/user/profile/change-password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $this->assertValidationErrors($response, ['current_password']);
    }

    /** @test */
    public function password_change_requires_confirmation()
    {
        $user = $this->createUser([
            'password' => bcrypt('oldpassword'),
        ]);
        $this->actingAsUser($user);

        $response = $this->putJson('/api/user/profile/change-password', [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ]);

        $this->assertValidationErrors($response, ['password']);
    }

    /** @test */
    public function profile_update_validation_works()
    {
        $user = $this->actingAsUser();

        $response = $this->putJson('/api/user/profile', [
            'name' => str_repeat('a', 256), // Too long
            'email' => 'invalid-email',
            'phone' => str_repeat('1', 25), // Too long
        ]);

        $this->assertValidationErrors($response, ['name', 'email', 'phone']);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_profile_routes()
    {
        $response = $this->getJson('/api/user/profile');
        $response->assertStatus(401);

        $response = $this->putJson('/api/user/profile', []);
        $response->assertStatus(401);

        $response = $this->putJson('/api/user/profile/change-password', []);
        $response->assertStatus(401);
    }
}
