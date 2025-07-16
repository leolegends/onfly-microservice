<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminAPIController extends Controller
{
    /**
     * Display admin API information
     */
    public function index()
    {
        return response()->json([
            'message' => 'Admin API v1.0',
            'description' => 'Onfly Travel Management - Admin Panel API',
            'version' => '1.0.0',
            'endpoints' => [
                'dashboard' => '/api/admin/dashboard',
                'users' => '/api/admin/users',
                'travel-requests' => '/api/admin/travel-requests',
                'settings' => '/api/admin/settings',
                'reports' => '/api/admin/reports',
                'health' => '/api/admin/health',
            ],
            'documentation' => url('/docs/admin'),
            'maintainer' => 'Onfly Teams - Leonardo Ribeiro',
            'contact' => 'lviniciusribeiro@yahoo.com.br'
        ]);
    }

    /**
     * Get current admin user info
     */
    public function me(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'department' => $user->department,
                'permissions' => $this->getUserPermissions($user),
                'last_login' => $user->updated_at,
            ]
        ]);
    }

    /**
     * Get user permissions based on role
     */
    private function getUserPermissions($user): array
    {
        $permissions = [];
        
        if ($user->role === 'admin') {
            $permissions = [
                'users.view',
                'users.create',
                'users.update',
                'users.delete',
                'travel-requests.view',
                'travel-requests.approve',
                'travel-requests.reject',
                'travel-requests.cancel',
                'settings.view',
                'settings.update',
                'reports.view',
                'dashboard.view',
            ];
        } elseif ($user->role === 'manager') {
            $permissions = [
                'travel-requests.view',
                'travel-requests.approve',
                'travel-requests.reject',
                'reports.view',
                'dashboard.view',
            ];
        }
        
        return $permissions;
    }
}
