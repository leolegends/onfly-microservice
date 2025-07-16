<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Busca Estatísticas do Dashboard Administrativo
     */
    public function index()
    {
        $statistics = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'inactive' => User::where('is_active', false)->count(),
                'by_role' => User::selectRaw('role, COUNT(*) as count')
                    ->groupBy('role')
                    ->get()
                    ->pluck('count', 'role')
            ],
            'travel_requests' => [
                'total' => TravelRequest::count(),
                'pending' => TravelRequest::where('status', 'requested')->count(),
                'approved' => TravelRequest::where('status', 'approved')->count(),
                'cancelled' => TravelRequest::where('status', 'cancelled')->count(),
                'rejected' => TravelRequest::where('status', 'rejected')->count(),
                'by_month' => TravelRequest::selectRaw('CAST(strftime("%m", created_at) AS INTEGER) as month, COUNT(*) as count')
                    ->whereRaw('strftime("%Y", created_at) = ?', [now()->year])
                    ->groupBy('month')
                    ->get()
                    ->pluck('count', 'month')
            ],
            'recent_activities' => [
                'recent_requests' => TravelRequest::with('user')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
                'recent_users' => User::orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
            ]
        ];

        return response()->json([
            'dashboard' => $statistics
        ]);
    }

    /**
     * Verifica Status de Saúde do Sistema
     */
    public function health()
    {
        $health = [
            'status' => 'healthy',
            'database' => $this->checkDatabase(),
            'storage' => $this->checkStorage(),
            'memory' => $this->getMemoryUsage(),
            'timestamp' => now()->toISOString()
        ];

        return response()->json($health);
    }

    /**
     * Verifica Conexão com o Banco de Dados
     */
    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return [
                'status' => 'connected',
                'message' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verifica Espaço de Armazenamento
     */
    private function checkStorage(): array
    {
        $storagePath = storage_path();
        $freeSpace = disk_free_space($storagePath);
        $totalSpace = disk_total_space($storagePath);
        
        return [
            'status' => 'ok',
            'free_space' => $this->formatBytes($freeSpace),
            'total_space' => $this->formatBytes($totalSpace),
            'usage_percentage' => round((($totalSpace - $freeSpace) / $totalSpace) * 100, 2)
        ];
    }

    /**
     * Obtém Informações de Uso de Memória
     */
    private function getMemoryUsage(): array
    {
        return [
            'current' => $this->formatBytes(memory_get_usage(true)),
            'peak' => $this->formatBytes(memory_get_peak_usage(true))
        ];
    }

    /**
     * Formata Bytes para Formato Legível
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
