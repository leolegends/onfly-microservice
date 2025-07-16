<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UpdateProfileAction
{
    public function execute(array $data): User
    {
        /** @var User $user */
        $user = Auth::user();
        
        $user->update($data);
        
        return $user;
    }
}
