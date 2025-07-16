# 🚀 API Routes Documentation

## 📋 Índice

- [Estrutura de Rotas](#estrutura-de-rotas)
- [Autenticação](#autenticação)
- [Usuário](#usuário)
- [Admin](#admin)
- [Middleware](#middleware)
- [Códigos de Status](#códigos-de-status)
- [Exemplos de Uso](#exemplos-de-uso)
- [Tratamento de Erros](#tratamento-de-erros)

---

## 🏗️ Estrutura de Rotas

A API está organizada em diferentes arquivos para melhor manutenção:

### 📁 Arquivos de Rotas

- **`routes/api.php`** - Arquivo principal que importa as outras rotas
- **`routes/auth.php`** - Rotas de autenticação
- **`routes/user.php`** - Rotas para usuários autenticados
- **`routes/admin.php`** - Rotas administrativas

### 🔗 Base URL

```
Base URL: http://localhost:8000/api
```

---

## 🔐 Autenticação (`/api/auth`)

### 🔓 Rotas Públicas

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

**Resposta (200):**
```json
{
  "success": true,
  "access_token": "1|xxx",
  "token_type": "Bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "role": "admin",
    "department": "IT",
    "is_active": true
  }
}
```

#### Registro
```http
POST /api/auth/register
Content-Type: application/json

{
  "name": "João Silva",
  "email": "joao@empresa.com",
  "password": "password123",
  "password_confirmation": "password123",
  "department": "IT"
}
```

#### Recuperar Senha
```http
POST /api/auth/forgot-password
Content-Type: application/json

{
  "email": "usuario@empresa.com"
}
```

#### Resetar Senha
```http
POST /api/auth/reset-password
Content-Type: application/json

{
  "token": "reset_token_here",
  "email": "usuario@empresa.com",
  "password": "nova_senha",
  "password_confirmation": "nova_senha"
}
```

### 🔒 Rotas Protegidas

#### Logout
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

#### Dados do Usuário
```http
GET /api/auth/me
Authorization: Bearer {token}
```

---

## 👤 Usuário (`/api/user`)

### 📱 Perfil do Usuário

#### Obter Perfil
```http
GET /api/user/profile
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "João Silva",
    "email": "joao@empresa.com",
    "role": "employee",
    "department": "IT",
    "position": "Developer",
    "phone": "(11) 99999-9999",
    "is_active": true,
    "created_at": "2025-01-01T00:00:00Z"
  }
}
```

#### Atualizar Perfil
```http
PUT /api/user/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "João Silva Santos",
  "phone": "(11) 98888-8888",
  "position": "Senior Developer"
}
```

#### Alterar Senha
```http
POST /api/user/profile/change-password
Authorization: Bearer {token}
Content-Type: application/json

{
  "current_password": "senha_atual",
  "new_password": "nova_senha",
  "new_password_confirmation": "nova_senha"
}
```

### ✈️ Solicitações de Viagem

#### Listar Minhas Solicitações
```http
GET /api/user/travel-requests
Authorization: Bearer {token}

# Parâmetros opcionais:
# ?status=requested
# ?page=1
# ?per_page=10
```

#### Criar Solicitação
```http
POST /api/user/travel-requests
Authorization: Bearer {token}
Content-Type: application/json

{
  "requestor_name": "Leonardo",
  "departure_date": "16-07-2025",
  "return_date": "20-07-2025",
  "justification": "Viagem a trabalho",
  "destination": "Salvador - BA",
  "purpose": "Venda de Software e Consultoria"
}
```

**Resposta (201):**
```json
{
  "success": true,
  "message": "Solicitação de viagem criada com sucesso.",
  "data": {
    "id": 1,
    "requestor_name": "Leonardo",
    "destination": "Salvador - BA",
    "departure_date": "2025-07-16",
    "return_date": "2025-07-20",
    "status": "requested",
    "purpose": "Venda de Software e Consultoria",
    "justification": "Viagem a trabalho",
    "created_at": "2025-01-01T00:00:00Z"
  }
}
```

#### Visualizar Solicitação
```http
GET /api/user/travel-requests/{id}
Authorization: Bearer {token}
```

#### Atualizar Solicitação
```http
PUT /api/user/travel-requests/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "destination": "Rio de Janeiro - RJ",
  "purpose": "Treinamento atualizado"
}
```

#### Cancelar Solicitação
```http
PATCH /api/user/travel-requests/{id}/cancel
Authorization: Bearer {token}
```

---

## 🔧 Admin (`/api/admin`)

### 📊 Dashboard

#### Estatísticas Gerais
```http
GET /api/admin/dashboard
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
  "success": true,
  "dashboard": {
    "users": {
      "total": 150,
      "active": 140,
      "inactive": 10,
      "by_role": {
        "employee": 120,
        "manager": 25,
        "admin": 5
      }
    },
    "travel_requests": {
      "total": 500,
      "pending": 25,
      "approved": 400,
      "cancelled": 50,
      "rejected": 25,
      "by_month": {
        "1": 45,
        "2": 38,
        "3": 52
      }
    },
    "recent_activities": {
      "recent_requests": [...],
      "recent_users": [...]
    }
  }
}
```

#### Health Check
```http
GET /api/admin/dashboard/health
Authorization: Bearer {token}
```

### 👥 Gerenciamento de Usuários

#### Listar Usuários
```http
GET /api/admin/users
Authorization: Bearer {token}

# Parâmetros opcionais:
# ?role=employee
# ?department=IT
# ?is_active=true
# ?search=joao
# ?page=1
# ?per_page=15
```

#### Criar Usuário
```http
POST /api/admin/users
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Maria Santos",
  "email": "maria@empresa.com",
  "password": "password123",
  "role": "employee",
  "department": "Sales",
  "position": "Sales Representative",
  "phone": "(11) 99999-9999",
  "is_active": true
}
```

#### Visualizar Usuário
```http
GET /api/admin/users/{id}
Authorization: Bearer {token}
```

#### Atualizar Usuário
```http
PUT /api/admin/users/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Maria Santos Silva",
  "role": "manager",
  "department": "Sales"
}
```

#### Desativar Usuário
```http
PATCH /api/admin/users/{id}/deactivate
Authorization: Bearer {token}
```

### 🛫 Gerenciamento de Solicitações de Viagem

#### Listar Todas as Solicitações
```http
GET /api/admin/travel-requests
Authorization: Bearer {token}

# Parâmetros opcionais:
# ?status=requested
# ?user_id=1
# ?destination=São Paulo
# ?start_date=2025-01-01
# ?end_date=2025-12-31
# ?search=leonardo
# ?page=1
# ?per_page=20
```

#### Visualizar Solicitação
```http
GET /api/admin/travel-requests/{id}
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 5,
    "requestor_name": "Leonardo",
    "destination": "Salvador - BA",
    "departure_date": "2025-07-16",
    "return_date": "2025-07-20",
    "status": "requested",
    "purpose": "Venda de Software e Consultoria",
    "justification": "Viagem a trabalho",
    "created_at": "2025-01-01T00:00:00Z",
    "user": {
      "id": 5,
      "name": "Leonardo",
      "email": "leonardo@empresa.com",
      "department": "Sales"
    },
    "status_history": [
      {
        "id": 1,
        "previous_status": null,
        "new_status": "requested",
        "notes": "Solicitação de viagem criada",
        "created_at": "2025-01-01T00:00:00Z",
        "user": {
          "name": "Leonardo"
        }
      }
    ]
  }
}
```

#### Aprovar Solicitação
```http
PATCH /api/admin/travel-requests/{id}/approve
Authorization: Bearer {token}
Content-Type: application/json

{
  "comment": "Aprovado pela administração"
}
```

#### Rejeitar Solicitação
```http
PATCH /api/admin/travel-requests/{id}/reject
Authorization: Bearer {token}
Content-Type: application/json

{
  "comment": "Orçamento insuficiente para este período"
}
```

#### Cancelar Solicitação
```http
PATCH /api/admin/travel-requests/{id}/cancel
Authorization: Bearer {token}
Content-Type: application/json

{
  "comment": "Cancelado devido a mudança de planos"
}
```

#### Estatísticas de Solicitações
```http
GET /api/admin/travel-requests/statistics
Authorization: Bearer {token}
```

#### Exportar Solicitações
```http
GET /api/admin/travel-requests/export
Authorization: Bearer {token}

# Parâmetros opcionais:
# ?format=csv
# ?start_date=2025-01-01
# ?end_date=2025-12-31
# ?status=approved
```

### ⚙️ Configurações do Sistema

#### Obter Configurações
```http
GET /api/admin/settings
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
  "success": true,
  "settings": {
    "app_name": "Onfly Travel Manager",
    "timezone": "America/Sao_Paulo",
    "locale": "pt_BR",
    "environment": "production"
  }
}
```

---

## 🛡️ Middleware de Autenticação

### `auth:sanctum`
- Verifica se o usuário está autenticado via token
- Aplicado a todas as rotas protegidas
- Retorna erro 401 se não autenticado

### `admin`
- Verifica se o usuário tem role 'admin'
- Verifica se o usuário está ativo
- Aplicado às rotas administrativas
- Retorna erro 403 se não autorizado

### `manager`
- Verifica se o usuário tem role 'manager' ou 'admin'
- Verifica se o usuário está ativo
- Aplicado às rotas de aprovação
- Retorna erro 403 se não autorizado

---

## 📱 Códigos de Status HTTP

### Sucesso (2xx)
- **200 OK**: Requisição bem-sucedida
- **201 Created**: Recurso criado com sucesso
- **204 No Content**: Operação bem-sucedida sem conteúdo de retorno

### Erro do Cliente (4xx)
- **400 Bad Request**: Dados inválidos
- **401 Unauthorized**: Não autenticado
- **403 Forbidden**: Não autorizado
- **404 Not Found**: Recurso não encontrado
- **422 Unprocessable Entity**: Erro de validação

### Erro do Servidor (5xx)
- **500 Internal Server Error**: Erro interno do servidor

---

## 🔧 Exemplos de Uso

### Fluxo Completo de Solicitação

```javascript
// 1. Login
const loginResponse = await fetch('/api/auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: 'leonardo@empresa.com',
    password: 'password123'
  })
});

const { access_token } = await loginResponse.json();

// 2. Criar solicitação
const createResponse = await fetch('/api/user/travel-requests', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${access_token}`
  },
  body: JSON.stringify({
    requestor_name: "Leonardo",
    departure_date: "16-07-2025",
    return_date: "20-07-2025",
    justification: "Viagem a trabalho",
    destination: "Salvador - BA",
    purpose: "Venda de Software e Consultoria"
  })
});

const { data: travelRequest } = await createResponse.json();

// 3. Admin aprova (com token de admin)
const approveResponse = await fetch(`/api/admin/travel-requests/${travelRequest.id}/approve`, {
  method: 'PATCH',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${adminToken}`
  },
  body: JSON.stringify({
    comment: "Aprovado pela administração"
  })
});
```

### Filtros e Busca

```javascript
// Buscar solicitações com filtros
const searchResponse = await fetch('/api/admin/travel-requests?' + new URLSearchParams({
  status: 'requested',
  start_date: '2025-01-01',
  end_date: '2025-12-31',
  search: 'São Paulo',
  page: 1,
  per_page: 20
}), {
  headers: { 'Authorization': `Bearer ${token}` }
});
```

---

## ⚠️ Tratamento de Erros

### Estrutura de Erro Padrão

```json
{
  "success": false,
  "message": "Mensagem de erro amigável",
  "errors": {
    "campo": [
      "Erro específico do campo"
    ]
  },
  "code": "ERROR_CODE"
}
```

### Exemplos de Erros

#### Erro de Validação (422)
```json
{
  "success": false,
  "message": "Os dados fornecidos são inválidos.",
  "errors": {
    "email": ["O campo email é obrigatório."],
    "password": ["O campo password deve ter pelo menos 8 caracteres."]
  }
}
```

#### Erro de Autorização (403)
```json
{
  "success": false,
  "message": "Você não tem permissão para acessar este recurso.",
  "code": "INSUFFICIENT_PERMISSIONS"
}
```

#### Erro de Autenticação (401)
```json
{
  "success": false,
  "message": "Token de autenticação inválido ou expirado.",
  "code": "INVALID_TOKEN"
}
```

---

## 🎯 Vantagens desta Organização

1. **Separação de Responsabilidades**: Cada arquivo tem uma função específica
2. **Manutenibilidade**: Fácil de encontrar e modificar rotas
3. **Escalabilidade**: Pode adicionar novos grupos de rotas facilmente
4. **Segurança**: Middleware organizados por contexto
5. **Documentação**: Cada arquivo pode ter sua própria documentação
6. **Testes**: Mais fácil de testar grupos específicos de rotas
7. **Versionamento**: Facilita criação de versões da API

---

## 🚀 Próximos Passos

### Features Planejadas

- **Notificações**: Sistema de notificações em tempo real
- **Relatórios**: Geração de relatórios avançados
- **Integração**: APIs externas para reservas
- **Mobile**: Aplicativo mobile
- **Webhooks**: Integração com sistemas externos

### Melhorias Técnicas

- **Rate Limiting**: Implementação de rate limiting
- **Caching**: Cache de consultas frequentes
- **Logging**: Sistema de logs avançado
- **Monitoring**: Métricas e alertas
- **Documentation**: Swagger/OpenAPI integration

---

**Desenvolvido com ❤️ por Leonardo Ribeiro para Onfly Teams**
- `PATCH /api/manager/travel-requests/{id}/reject` - Rejeitar pedido

### Notificações
- `GET /api/notifications` - Listar notificações
- `PATCH /api/notifications/{id}/read` - Marcar como lida
- `DELETE /api/notifications/{id}` - Deletar notificação

---

## 🔧 Admin (`/api/admin`)

### Gerenciamento de Usuários
- `GET /api/admin/users` - Listar usuários
- `GET /api/admin/users/{id}` - Detalhes do usuário
- `POST /api/admin/users` - Criar usuário
- `PUT /api/admin/users/{id}` - Atualizar usuário
- `DELETE /api/admin/users/{id}` - Deletar usuário

### Gerenciamento de Pedidos de Viagem
- `GET /api/admin/travel-requests` - Listar todos os pedidos
- `GET /api/admin/travel-requests/{id}` - Detalhes do pedido
- `PATCH /api/admin/travel-requests/{id}/approve` - Aprovar pedido
- `PATCH /api/admin/travel-requests/{id}/reject` - Rejeitar pedido
- `PATCH /api/admin/travel-requests/{id}/cancel` - Cancelar pedido
- `GET /api/admin/travel-requests/statistics` - Estatísticas

### Gerenciamento de Departamentos
- `GET /api/admin/departments` - Listar departamentos
- `POST /api/admin/departments` - Criar departamento
- `PUT /api/admin/departments/{id}` - Atualizar departamento
- `DELETE /api/admin/departments/{id}` - Deletar departamento

### Configurações do Sistema
- `GET /api/admin/settings` - Configurações
- `PUT /api/admin/settings` - Atualizar configurações

### Relatórios
- `GET /api/admin/reports/travel-requests` - Relatório de pedidos
- `GET /api/admin/reports/users` - Relatório de usuários
- `GET /api/admin/reports/departments` - Relatório de departamentos

---

## 🔒 Middleware de Autenticação

### `auth:sanctum`
- Verifica se o usuário está autenticado
- Aplicado a todas as rotas protegidas

### `admin`
- Verifica se o usuário tem role 'admin'
- Verifica se o usuário está ativo
- Aplicado às rotas administrativas

### `manager`
- Verifica se o usuário tem role 'manager' ou 'admin'
- Verifica se o usuário está ativo
- Aplicado às rotas de aprovação

---

## 🚀 Rotas de Sistema

### Health Check
- `GET /api/test` - Teste básico da API
- `GET /api/health` - Status detalhado do sistema

---

## 📝 Exemplo de Uso

### Importar rotas no api.php:
```php
// Load authentication routes
require __DIR__ . '/auth.php';

// Load user routes
require __DIR__ . '/user.php';

// Load admin routes
require __DIR__ . '/admin.php';
```

### Aplicar middleware nas rotas:
```php
// Em admin.php
Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum', 'admin']], function () {
    // Rotas administrativas
});

// Em user.php
Route::group(['middleware' => ['auth:sanctum']], function () {
    // Rotas de usuário
});
```

---

## 🎯 Vantagens desta Organização

1. **Separação de Responsabilidades**: Cada arquivo tem uma função específica
2. **Manutenibilidade**: Fácil de encontrar e modificar rotas
3. **Escalabilidade**: Pode adicionar novos grupos de rotas facilmente
4. **Segurança**: Middleware organizados por contexto
5. **Documentação**: Cada arquivo pode ter sua própria documentação
6. **Testes**: Mais fácil de testar grupos específicos de rotas
