<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordAction
{
    public function execute(array $data): User
    {
        /** @var User $user */
        $user = Auth::user();
        
        $user->update([
            'password' => Hash::make($data['password'])
        ]);
        
        return $user;
    }
}
