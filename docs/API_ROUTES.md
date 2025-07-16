# API Routes Documentation

## Estrutura de Rotas

A API está organizada em diferentes arquivos para melhor manutenção:

### 📁 Arquivos de Rotas

- **`routes/api.php`** - Arquivo principal que importa as outras rotas
- **`routes/auth.php`** - Rotas de autenticação
- **`routes/user.php`** - Rotas para usuários autenticados
- **`routes/admin.php`** - Rotas administrativas

---

## 🔐 Autenticação (`/api/auth`)

### Rotas Públicas
- `POST /api/auth/login` - Login do usuário
- `POST /api/auth/register` - Registro de novo usuário
- `POST /api/auth/forgot-password` - Solicitar reset de senha
- `POST /api/auth/reset-password` - Resetar senha
- `POST /api/auth/verify-email` - Verificar email
- `POST /api/auth/resend-verification` - Reenviar email de verificação

### Rotas Protegidas
- `POST /api/auth/logout` - Logout do usuário
- `GET /api/auth/me` - Dados do usuário autenticado
- `POST /api/auth/refresh` - Renovar token

---

## 👤 Usuário (`/api`)

### Perfil do Usuário
- `GET /api/profile` - Dados do perfil
- `PUT /api/profile` - Atualizar perfil
- `POST /api/profile/change-password` - Alterar senha

### Pedidos de Viagem do Usuário
- `GET /api/travel-requests` - Listar meus pedidos
- `GET /api/travel-requests/{id}` - Detalhes do pedido
- `POST /api/travel-requests` - Criar novo pedido
- `PUT /api/travel-requests/{id}` - Atualizar pedido
- `PATCH /api/travel-requests/{id}/cancel` - Cancelar pedido
- `GET /api/travel-requests/{id}/history` - Histórico do pedido

### Rotas de Manager
- `GET /api/manager/travel-requests/pending` - Pedidos pendentes
- `PATCH /api/manager/travel-requests/{id}/approve` - Aprovar pedido
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
