# 🏗️ Arquitetura do Sistema

## 📋 Índice

- [Visão Geral](#visão-geral)
- [Padrões Arquiteturais](#padrões-arquiteturais)
- [Estrutura de Camadas](#estrutura-de-camadas)
- [Models & Entities](#models--entities)
- [Database Design](#database-design)
- [Services & Business Logic](#services--business-logic)
- [API Layer](#api-layer)
- [Security](#security)
- [Testing Strategy](#testing-strategy)
- [Deployment](#deployment)

---

## 🎯 Visão Geral

O **Onfly Microservice** é construído seguindo princípios de **Clean Architecture** e **Domain-Driven Design (DDD)**, garantindo:

- **Separação de Responsabilidades**
- **Testabilidade**
- **Manutenibilidade**
- **Escalabilidade**
- **Flexibilidade**

### Tecnologias Core

- **Laravel 12.x**: Framework PHP moderno
- **PHP 8.2+**: Linguagem com tipos modernos
- **SQLite**: Banco de dados para desenvolvimento
- **Laravel Sanctum**: Autenticação API
- **PHPUnit**: Framework de testes

---

## 🏛️ Padrões Arquiteturais

### 1. **Clean Architecture**

```
┌─────────────────────────────────────────────────────────────┐
│                 🌐 Presentation Layer                        │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │ Controllers │  │ Middleware  │  │ Resources   │          │
│  │ (HTTP)      │  │ (Auth)      │  │ (JSON)      │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│                 🔧 Application Layer                        │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │  Services   │  │  Observers  │  │Notifications│          │
│  │ (Business)  │  │ (Events)    │  │ (Messages)  │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│                 🏢 Domain Layer                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │   Models    │  │ Factories   │  │ Policies    │          │
│  │ (Entities)  │  │ (Builders)  │  │ (Rules)     │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│                 🗄️ Infrastructure Layer                     │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │  Database   │  │    Cache    │  │  External   │          │
│  │  (MYSQL )   │  │   (Redis)   │  │    APIs     │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
└─────────────────────────────────────────────────────────────┘
```

### 2. **Domain-Driven Design (DDD)**

#### Domínios Identificados

- **🔐 Authentication**: Autenticação e autorização
- **👥 User Management**: Gestão de usuários
- **✈️ Travel Management**: Gestão de viagens
- **📊 Analytics**: Relatórios e estatísticas

#### Bounded Contexts

```
┌─────────────────────────────────────────────────────────────┐
│                    Authentication Context                   │
│  - User authentication                                      │
│  - Token management                                         │
│  - Role-based access control                                │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                  Travel Request Context                     │
│  - Travel request lifecycle                                 │
│  - Approval workflow                                        │
│  - Status tracking                                          │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                   User Management Context                   │
│  - User profiles                                            │
│  - Department management                                    │
│  - Permission management                                    │
└─────────────────────────────────────────────────────────────┘
```

### 3. **Observer Pattern**

```php
// Exemplo de uso do Observer
class TravelRequestObserver
{
    public function created(TravelRequest $travelRequest): void
    {
        // Criar histórico inicial
        $travelRequest->statusHistory()->create([
            'new_status' => $travelRequest->status,
            'user_id' => $travelRequest->user_id,
            'notes' => 'Solicitação de viagem criada',
        ]);
    }
    
    public function updating(TravelRequest $travelRequest): void
    {
        // Rastrear mudanças de status
        if ($travelRequest->isDirty('status')) {
            // Criar entrada no histórico
        }
    }
}
```

---

## 🏗️ Estrutura de Camadas

### 1. **Presentation Layer**

#### Controllers
```php
namespace App\Http\Controllers\API\Admin;

class TravelRequestController extends Controller
{
    public function index(Request $request)
    {
        // Validação e filtros
        // Chamada para service layer
        // Transformação de dados
        // Resposta JSON
    }
}
```

#### Middleware
```php
class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        
        return $next($request);
    }
}
```

#### Resources (JSON Transformers)
```php
class TravelRequestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'requestor_name' => $this->requestor_name,
            'destination' => $this->destination,
            'status' => $this->status,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
```

### 2. **Application Layer**

#### Services
```php
class TravelRequestService
{
    public function createTravelRequest(array $data, User $user): TravelRequest
    {
        // Validação de negócio
        // Criação da entidade
        // Disparo de eventos
        // Retorno da entidade
    }
}
```

#### Observers
```php
class TravelRequestObserver
{
    public function created(TravelRequest $travelRequest): void
    {
        // Lógica de evento
    }
}
```

### 3. **Domain Layer**

#### Models (Entities)
```php
class TravelRequest extends Model
{
    // Atributos
    // Relacionamentos
    // Métodos de domínio
    // Scopes
    // Accessors/Mutators
}
```

#### Factories
```php
class TravelRequestFactory extends Factory
{
    public function definition(): array
    {
        // Definição de dados fake
    }
}
```

### 4. **Infrastructure Layer**

#### Database
- **Migrations**: Estrutura do banco
- **Seeds**: Dados iniciais
- **Repositories**: Abstrações de acesso a dados

---

## 🗄️ Database Design

### 📊 Diagrama ER

```sql
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│      users      │    │ travel_requests │    │ travel_request_ │
│                 │    │                 │    │ status_history  │
│ • id (PK)       │    │ • id (PK)       │    │                 │
│ • name          │◄───┤ • user_id (FK)  │    │ • id (PK)       │
│ • email         │    │ • requestor_name│    │ • travel_req_id │
│ • role          │    │ • destination   │────┤   (FK)          │
│ • department    │    │ • departure_date│    │ • user_id (FK)  │
│ • is_active     │    │ • return_date   │    │ • prev_status   │
│ • created_at    │    │ • status        │    │ • new_status    │
│ • updated_at    │    │ • purpose       │    │ • notes         │
└─────────────────┘    │ • justification │    │ • created_at    │
                       │ • created_at    │    └─────────────────┘
                       │ • updated_at    │
                       └─────────────────┘
```

### 🔑 Principais Tabelas

#### 1. **users**
```sql
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP,
    password VARCHAR(255) NOT NULL,
    role ENUM('employee', 'manager', 'admin') DEFAULT 'employee',
    department VARCHAR(100),
    position VARCHAR(100),
    phone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 2. **travel_requests**
```sql
CREATE TABLE travel_requests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    approver_id INTEGER,
    requestor_name VARCHAR(255) NOT NULL,
    destination VARCHAR(255) NOT NULL,
    departure_date DATE NOT NULL,
    return_date DATE NOT NULL,
    start_date DATE,
    end_date DATE,
    status ENUM('requested', 'approved', 'rejected', 'cancelled') DEFAULT 'requested',
    purpose TEXT NOT NULL,
    justification TEXT NOT NULL,
    budget DECIMAL(10,2),
    rejection_reason TEXT,
    approved_at TIMESTAMP,
    cancelled_at TIMESTAMP,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (approver_id) REFERENCES users(id) ON DELETE SET NULL
);
```

#### 3. **travel_request_status_history**
```sql
CREATE TABLE travel_request_status_history (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    travel_request_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    previous_status VARCHAR(50),
    new_status VARCHAR(50) NOT NULL,
    reason TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (travel_request_id) REFERENCES travel_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 📈 Indexação

```sql
-- Índices para performance
CREATE INDEX idx_users_role_active ON users(role, is_active);
CREATE INDEX idx_users_department ON users(department);
CREATE INDEX idx_travel_requests_status ON travel_requests(status);
CREATE INDEX idx_travel_requests_user_id ON travel_requests(user_id);
CREATE INDEX idx_travel_requests_dates ON travel_requests(departure_date, return_date);
CREATE INDEX idx_status_history_travel_request ON travel_request_status_history(travel_request_id, created_at);
```

---

## 🛡️ Security

### 1. **Autenticação**

#### Laravel Sanctum
```php
// Configuração no config/sanctum.php
'expiration' => 60 * 24, // 24 horas
'middleware' => [
    'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
];
```

#### Token Generation
```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $user = Auth::user();
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user,
    ]);
}
```

### 2. **Autorização**

#### Role-Based Access Control (RBAC)
```php
class User extends Model
{
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    public function isManager(): bool
    {
        return in_array($this->role, ['manager', 'admin']);
    }
    
    public function canManageTravelRequest(TravelRequest $request): bool
    {
        return $this->isAdmin() || $this->id === $request->user_id;
    }
}
```

#### Middleware de Autorização
```php
class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Privilégios de administrador necessários.'
            ], 403);
        }

        return $next($request);
    }
}
```

### 3. **Validação**

#### Form Requests
```php
class CreateTravelRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'requestor_name' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date|after:today',
            'return_date' => 'required|date|after:departure_date',
            'purpose' => 'required|string|max:500',
            'justification' => 'required|string|max:1000',
        ];
    }
}
```

### 4. **Sanitização**

#### Input Sanitization
```php
class TravelRequestController extends Controller
{
    public function store(CreateTravelRequestRequest $request)
    {
        $validated = $request->validated();
        
        // Sanitização automática pelo Laravel
        $validated['requestor_name'] = strip_tags($validated['requestor_name']);
        $validated['destination'] = strip_tags($validated['destination']);
        
        // Criação segura
        $travelRequest = TravelRequest::create($validated);
        
        return response()->json($travelRequest, 201);
    }
}
```

---

## 🧪 Testing Strategy

### 1. **Pirâmide de Testes**

```
         ┌─────────────┐
         │   E2E (5%)  │  ← Testes de ponta a ponta
         └─────────────┘
       ┌─────────────────┐
       │ Integration(25%)│  ← Testes de integração
       └─────────────────┘
     ┌─────────────────────┐
     │   Unit Tests (70%)  │  ← Testes unitários
     └─────────────────────┘
```

### 2. **Estrutura de Testes**

#### Unit Tests (43 testes)
```php
class UserTest extends TestCase
{
    /** @test */
    public function user_can_check_if_is_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'employee']);
        
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isAdmin());
    }
}
```

#### Feature Tests (39 testes)
```php
class TravelRequestControllerTest extends TestCase
{
    use TestHelpers;
    
    /** @test */
    public function user_can_create_travel_request()
    {
        $user = $this->actingAsUser();
        
        $payload = [
            'requestor_name' => 'Leonardo',
            'departure_date' => '16-07-2025',
            'return_date' => '20-07-2025',
            'justification' => 'Viagem a trabalho',
            'destination' => 'Salvador - BA',
            'purpose' => 'Venda de Software e Consultoria'
        ];
        
        $response = $this->postJson('/api/user/travel-requests', $payload);
        
        $response->assertStatus(201);
        $this->assertDatabaseHas('travel_requests', [
            'requestor_name' => 'Leonardo',
            'destination' => 'Salvador - BA',
        ]);
    }
}
```

### 3. **Test Helpers**

```php
trait TestHelpers
{
    protected function actingAsUser(User $user = null): User
    {
        if (!$user) {
            $user = User::factory()->create(['role' => 'employee']);
        }
        
        Sanctum::actingAs($user);
        return $user;
    }
    
    protected function actingAsAdmin(User $admin = null): User
    {
        if (!$admin) {
            $admin = User::factory()->create(['role' => 'admin']);
        }
        
        Sanctum::actingAs($admin);
        return $admin;
    }
    
    protected function createTravelRequest(array $attributes = []): TravelRequest
    {
        return TravelRequest::factory()->create($attributes);
    }
}
```

### 4. **Configuração de Testes**

#### phpunit.xml
```xml
<phpunit>
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite_testing"/>
        <env name="DB_DATABASE" value=":memory:"/>
    </php>
</phpunit>
```

---

## 🚀 Deployment

### 1. **Docker Configuration**

#### Dockerfile
```dockerfile
FROM php:8.2-fpm

# Instalar dependências
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Instalar extensões PHP
RUN docker-php-ext-install pdo_sqlite mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos
COPY . .

# Instalar dependências
RUN composer install --no-dev --optimize-autoloader

# Permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
```

#### docker-compose.yml
```yaml
version: '3.8'

services:
  app:
    build: .
    ports:
      - "8000:8000"
    environment:
      - APP_ENV=production
      - DB_CONNECTION=sqlite
      - DB_DATABASE=/var/www/html/database/database.sqlite
    volumes:
      - ./database:/var/www/html/database
      - ./storage:/var/www/html/storage
    depends_on:
      - redis

  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
    volumes:
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app
```

### 2. **Environment Configuration**

#### .env.production
```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:generated_key_here

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PORT=6379

SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,your-domain.com
```

### 3. **CI/CD Pipeline**

#### GitHub Actions
```yaml
name: CI/CD Pipeline

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        extensions: mbstring, xml, ctype, iconv, intl, pdo_sqlite
        
    - name: Install dependencies
      run: composer install --no-dev --optimize-autoloader
      
    - name: Run tests
      run: vendor/bin/phpunit
      
    - name: Build Docker image
      run: docker build -t onfly-microservice .
      
    - name: Deploy to production
      if: github.ref == 'refs/heads/main'
      run: |
        # Deploy commands here
```

---

## 📊 Performance & Monitoring

### 1. **Caching Strategy**

```php
// Cache de queries frequentes
public function getDashboardStats()
{
    return Cache::remember('dashboard_stats', 300, function () {
        return [
            'users' => User::count(),
            'travel_requests' => TravelRequest::count(),
            'pending_requests' => TravelRequest::pending()->count(),
        ];
    });
}
```

### 2. **Database Optimization**

```php
// Eager loading para evitar N+1
public function index()
{
    $travelRequests = TravelRequest::with(['user', 'approver', 'statusHistory'])
        ->paginate(20);
        
    return TravelRequestResource::collection($travelRequests);
}
```

### 3. **Health Checks**

```php
public function health()
{
    $checks = [
        'database' => $this->checkDatabase(),
        'redis' => $this->checkRedis(),
        'storage' => $this->checkStorage(),
    ];
    
    $status = collect($checks)->every(fn($check) => $check['status'] === 'ok')
        ? 'healthy'
        : 'unhealthy';
        
    return response()->json(['status' => $status, 'checks' => $checks]);
}
```

---

**Desenvolvido com ❤️ por Leonardo Ribeiro para Onfly Teams**
