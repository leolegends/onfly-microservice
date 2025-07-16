<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\TravelRequest;
use App\Models\TravelRequestStatusHistory;
use Tests\TestCase;
use Tests\Traits\TestHelpers;

class UserTest extends TestCase
{
    use TestHelpers;

    /** @test */
    public function user_has_travel_requests_relationship()
    {
        $user = $this->createUser();
        $travelRequest = $this->createTravelRequest(['user_id' => $user->id]);

        $this->assertInstanceOf(TravelRequest::class, $user->travelRequests->first());
        $this->assertEquals($travelRequest->id, $user->travelRequests->first()->id);
    }

    /** @test */
    public function user_can_check_if_is_admin()
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();
        $manager = $this->createManager();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($manager->isAdmin());
    }

    /** @test */
    public function user_can_check_if_is_manager()
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();
        $manager = $this->createManager();

        $this->assertFalse($admin->isManager());
        $this->assertFalse($user->isManager());
        $this->assertTrue($manager->isManager());
    }

    /** @test */
    public function user_can_check_if_is_employee()
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();
        $manager = $this->createManager();

        $this->assertFalse($admin->isEmployee());
        $this->assertTrue($user->isEmployee());
        $this->assertFalse($manager->isEmployee());
    }

    /** @test */
    public function user_name_is_fillable()
    {
        $user = new User();
        $user->fill(['name' => 'John Doe']);

        $this->assertEquals('John Doe', $user->name);
    }

    /** @test */
    public function user_email_is_fillable()
    {
        $user = new User();
        $user->fill(['email' => 'john@example.com']);

        $this->assertEquals('john@example.com', $user->email);
    }

    /** @test */
    public function user_password_is_hidden()
    {
        $user = $this->createUser(['password' => 'secret']);

        $array = $user->toArray();
        $this->assertArrayNotHasKey('password', $array);
    }

    /** @test */
    public function user_remember_token_is_hidden()
    {
        $user = $this->createUser();

        $array = $user->toArray();
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    /** @test */
    public function user_can_be_created_with_all_attributes()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret',
            'role' => 'manager',
            'department' => 'IT',
            'is_active' => true,
        ];

        $user = User::factory()->create($userData);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('manager', $user->role);
        $this->assertEquals('IT', $user->department);
        $this->assertTrue($user->is_active);
    }
}
