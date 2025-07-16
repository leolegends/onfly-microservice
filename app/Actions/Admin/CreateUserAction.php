<?php

namespace App\Actions\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    /**
     * Create a new user
     */
    public function execute(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $data['is_active'] ?? true;
        
        return User::create($data);
    }
}
