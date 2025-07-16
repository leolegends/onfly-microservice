<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TravelRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users with different roles
        $admin = \App\Models\User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $manager = \App\Models\User::factory()->manager()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
        ]);

        $employee1 = \App\Models\User::factory()->employee()->create([
            'name' => 'Employee One',
            'email' => 'employee1@example.com',
        ]);

        $employee2 = \App\Models\User::factory()->employee()->create([
            'name' => 'Employee Two',
            'email' => 'employee2@example.com',
        ]);

        // Create travel requests in different states
        \App\Models\TravelRequest::factory()
            ->count(5)
            ->requested()
            ->for($employee1)
            ->create();

        \App\Models\TravelRequest::factory()
            ->count(3)
            ->approved()
            ->for($employee1)
            ->create([
                'approver_id' => $manager->id,
            ]);

        \App\Models\TravelRequest::factory()
            ->count(2)
            ->cancelled()
            ->for($employee2)
            ->create();

        \App\Models\TravelRequest::factory()
            ->count(1)
            ->rejected()
            ->for($employee2)
            ->create([
                'approver_id' => $manager->id,
            ]);

        // Create some additional random travel requests
        \App\Models\TravelRequest::factory()
            ->count(10)
            ->create();
    }
}
