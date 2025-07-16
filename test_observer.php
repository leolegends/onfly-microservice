<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\TravelRequest;
use Illuminate\Support\Facades\DB;

// Configurar o ambiente
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Definir a conexão do banco
config(['database.default' => 'sqlite_testing']);
config(['database.connections.sqlite_testing' => [
    'driver' => 'sqlite',
    'database' => ':memory:',
    'prefix' => '',
]]);

// Executar as migrations
Artisan::call('migrate:fresh', ['--database' => 'sqlite_testing']);

try {
    // Criar um usuário
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'role' => 'employee',
    ]);

    echo "Usuario criado com ID: " . $user->id . "\n";

    // Criar uma solicitação de viagem
    $travelRequest = TravelRequest::create([
        'user_id' => $user->id,
        'requestor_name' => 'Test User',
        'destination' => 'São Paulo',
        'departure_date' => now()->addDays(7),
        'return_date' => now()->addDays(10),
        'purpose' => 'Business',
        'justification' => 'Important meeting',
        'status' => 'requested',
    ]);

    echo "Solicitação de viagem criada com ID: " . $travelRequest->id . "\n";

    // Verificar se o histórico foi criado
    $history = $travelRequest->statusHistory()->first();
    
    if ($history) {
        echo "Histórico criado com sucesso! User ID: " . $history->user_id . "\n";
    } else {
        echo "ERRO: Histórico não foi criado!\n";
    }

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
