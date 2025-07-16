<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@onfly.com',
            'role' => 'admin',
            'department' => 'IT',
            'is_active' => true,
        ]);

        // Create manager user
        \App\Models\User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@onfly.com',
            'role' => 'manager',
            'department' => 'HR',
            'is_active' => true,
        ]);

        // Create employee user
        \App\Models\User::factory()->create([
            'name' => 'Employee User',
            'email' => 'employee@onfly.com',
            'role' => 'employee',
            'department' => 'Sales',
            'is_active' => true,
        ]);

        // Create additional random users
        \App\Models\User::factory(10)->create();
    }
}
