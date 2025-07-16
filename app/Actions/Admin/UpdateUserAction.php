<?php

namespace App\Actions\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateUserAction
{
    /**
     * Update an existing user
     */
    public function execute(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $user->update($data);
        
        return $user->refresh();
    }
}
