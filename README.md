# 🚀 Onfly Microservice - Travel Manager Module

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)
![Tests](https://img.shields.io/badge/Tests-82%20Tests-green?style=for-the-badge)
![SQLite](https://img.shields.io/badge/SQLite-Test%20Database-003B57?style=for-the-badge&logo=sqlite)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker)

## 📋 Índice

- [🎯 Sobre o Projeto](#-sobre-o-projeto)
- [🏗️ Arquitetura](#️-arquitetura)
- [🚀 Recursos](#-recursos)
- [🛠️ Tecnologias](#️-tecnologias)
- [🔧 Instalação](#-instalação)
- [📖 Uso](#-uso)
- [📚 Documentação](#-documentação)
- [📚 API Documentation](#-api-documentation)
- [🧪 Testes](#-testes)
- [📁 Estrutura do Projeto](#-estrutura-do-projeto)
- [🔒 Segurança](#-segurança)
- [🌐 Deployment](#-deployment)
- [📈 Monitoramento](#-monitoramento)
- [🤝 Contribuição](#-contribuição)
- [📞 Contato](#-contato)
- [📄 Licença](#-licença)

## 🎯 Sobre o Projeto

O **Onfly Microservice** é um sistema completo de gerenciamento de viagens corporativas desenvolvido em Laravel 12. O sistema permite que funcionários solicitem viagens, gerentes aprovem ou rejeitem solicitações, e administradores tenham controle total sobre o sistema.

### Principais Funcionalidades

- ✅ **Gestão de Usuários**: Criação, edição e gerenciamento de usuários com diferentes níveis de acesso
- ✅ **Solicitações de Viagem**: Sistema completo para criar, aprovar, rejeitar e cancelar viagens
- ✅ **Dashboard Administrativo**: Estatísticas e relatórios em tempo real
- ✅ **Histórico de Status**: Rastreamento completo de mudanças de status
- ✅ **Autenticação Segura**: Sistema de autenticação com Laravel Sanctum
- ✅ **API RESTful**: API completa com documentação
- ✅ **Testes Abrangentes**: 82 testes unitários e de integração

## 🏗️ Arquitetura

### Padrão Arquitetural

O projeto segue os princípios da **Arquitetura Limpa** e **DDD (Domain-Driven Design)**:

```
┌─────────────────────────────────────────────────────────────┐
│                    Presentation Layer                        │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │   Controllers   │  │   Middleware    │  │   Resources     │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│                    Application Layer                        │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │   Services      │  │   Observers     │  │   Notifications │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│                    Domain Layer                             │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │     Models      │  │   Factories     │  │   Repositories  │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│                  Infrastructure Layer                       │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │    Database     │  │      Cache      │  │    External     │ │
│  │    (Mysql)      │  │     (Redis)     │  │     APIs        │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### Principais Componentes

#### 1. **Models & Entities**
- **User**: Gestão de usuários com roles (employee, manager, admin)
- **TravelRequest**: Entidade principal para solicitações de viagem
- **TravelRequestStatusHistory**: Histórico de mudanças de status

#### 2. **Controllers**
- **API/Admin**: Controladores para funcionalidades administrativas
- **API/User**: Controladores para funcionalidades de usuário
- **API/Auth**: Controladores de autenticação

#### 3. **Services & Business Logic**
- **TravelRequestObserver**: Observador para mudanças de status
- **Middleware**: Autenticação e autorização
- **Notifications**: Sistema de notificações

#### 4. **Database Design**

```sql
-- Principais tabelas
users (id, name, email, role, department, is_active)
travel_requests (id, user_id, destination, departure_date, return_date, status)
travel_request_status_history (id, travel_request_id, user_id, previous_status, new_status)
```

## 🚀 Recursos

### 👥 Gestão de Usuários

- **Roles**: Employee, Manager, Admin
- **Departamentos**: IT, Sales, Marketing, Finance, HR, Operations
- **Status**: Ativo/Inativo
- **Autenticação**: Laravel Sanctum com tokens seguros

### ✈️ Gestão de Viagens

- **Status**: Requested, Approved, Rejected, Cancelled
- **Campos**: Destino, datas, propósito, justificativa, custo estimado
- **Aprovação**: Fluxo de aprovação por managers/admins
- **Histórico**: Rastreamento completo de mudanças

### 📊 Dashboard & Relatórios

- **Estatísticas**: Usuários ativos, solicitações por status, estatísticas mensais
- **Filtros**: Por status, data, usuário, departamento
- **Exportação**: CSV, Excel (futuro)
- **Health Check**: Monitoramento de sistema

## 🛠️ Tecnologias

### Backend
- **Laravel 12.x**: Framework PHP robusto
- **PHP 8.2+**: Linguagem de programação
- **SQLite**: Banco de dados para desenvolvimento/testes
- **MySQL**: Banco de dados para produção
- **Laravel Sanctum**: Autenticação API
- **Observer Pattern**: Para eventos de domínio

### Testes
- **PHPUnit 11.5+**: Framework de testes
- **Feature Tests**: Testes de integração
- **Unit Tests**: Testes unitários
- **Test Helpers**: Utilitários de teste customizados

### DevOps
- **Docker**: Containerização
- **Docker Compose**: Orquestração de containers
- **Redis**: Cache e sessões
- **Nginx**: Servidor web

## 🔧 Instalação

### Pré-requisitos

- PHP 8.2+
- Composer
- Docker & Docker Compose (opcional)
- SQLite (para desenvolvimento)
- MySQL (para produção)

### Método 1: Instalação Local

```bash
# Clonar o repositório
git clone https://github.com/leolegends/onfly-microservice.git
cd onfly-microservice

# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Configurar banco de dados
touch database/database.sqlite

# Executar migrações
php artisan migrate

# Iniciar servidor
php artisan serve
```

### Método 2: Docker

```bash
# Clonar o repositório
git clone https://github.com/leolegends/onfly-microservice.git
cd onfly-microservice

# Construir e iniciar containers
docker-compose up -d

# Executar migrações
docker-compose exec app php artisan migrate

# Acessar em http://localhost:8000
```

## 📖 Uso

### Autenticação

```bash
# Login
POST /api/auth/login
{
  "email": "admin@example.com",
  "password": "password"
}

# Resposta
{
  "access_token": "1|xxx",
  "token_type": "Bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@example.com",
    "role": "admin"
  }
}
```

### Criar Solicitação de Viagem

```bash
# Criar solicitação
POST /api/user/travel-requests
Authorization: Bearer {token}
{
  "requestor_name": "Leonardo",
  "departure_date": "16-07-2025",
  "return_date": "20-07-2025",
  "justification": "Viagem a trabalho",
  "destination": "Salvador - BA",
  "purpose": "Venda de Software e Consultoria"
}
```

### Aprovar Solicitação (Admin)

```bash
# Aprovar solicitação
PATCH /api/admin/travel-requests/{id}/approve
Authorization: Bearer {token}
{
  "comment": "Aprovado pela administração"
}
```

## 📚 Documentação

### 📖 Documentação Principal
- **[📋 INDEX](./docs/INDEX.md)** - Índice completo da documentação
- **[🏗️ ARCHITECTURE](./docs/ARCHITECTURE.md)** - Arquitetura detalhada do sistema
- **[🚀 DEPLOYMENT](./docs/DEPLOYMENT.md)** - Guia completo de deploy
- **[🤝 CONTRIBUTING](./docs/CONTRIBUTING.md)** - Guia de contribuição
- **[❓ FAQ](./docs/FAQ.md)** - Perguntas frequentes

### 🚀 Documentação da API
- **[📋 API Routes](./docs/API_ROUTES.md)** - Documentação completa das rotas
- **[📦 API Payloads](./docs/API_PAYLOADS.md)** - Exemplos de payloads e respostas

### 📊 Changelog
- **[📋 CHANGELOG](./CHANGELOG.md)** - Histórico de versões e alterações

---

## 📚 API Documentation

### Estrutura de Endpoints

```
📁 /api/auth          # Autenticação
📁 /api/user          # Funcionalidades de usuário
📁 /api/admin         # Funcionalidades administrativas
📁 /api/manager       # Funcionalidades de manager
```

### Principais Endpoints

#### 🔐 Autenticação
- `POST /api/auth/login` - Login
- `POST /api/auth/register` - Registro
- `POST /api/auth/logout` - Logout
- `GET /api/auth/me` - Dados do usuário

#### 👤 Usuário
- `GET /api/user/profile` - Perfil do usuário
- `PUT /api/user/profile` - Atualizar perfil
- `GET /api/user/travel-requests` - Listar solicitações
- `POST /api/user/travel-requests` - Criar solicitação
- `PATCH /api/user/travel-requests/{id}/cancel` - Cancelar solicitação

#### 🔧 Admin
- `GET /api/admin/dashboard` - Dashboard
- `GET /api/admin/users` - Listar usuários
- `POST /api/admin/users` - Criar usuário
- `GET /api/admin/travel-requests` - Listar todas as solicitações
- `PATCH /api/admin/travel-requests/{id}/approve` - Aprovar
- `PATCH /api/admin/travel-requests/{id}/reject` - Rejeitar

Para documentação completa, consulte: [`docs/API_ROUTES.md`](docs/API_ROUTES.md)

## 🧪 Testes

### Cobertura de Testes

O projeto possui **82 testes** distribuídos em:

- **Feature Tests (39 testes)**: Testes de integração
  - Admin Controllers (25 testes)
  - User Controllers (14 testes)
  
- **Unit Tests (43 testes)**: Testes unitários
  - Models (35 testes)
  - Services (8 testes)

### Executar Testes

```bash
# Todos os testes
php artisan test
```

### Estrutura de Testes

```
tests/
├── Feature/
│   ├── Admin/
│   │   ├── DashboardControllerTest.php    # 6 testes
│   │   ├── TravelRequestControllerTest.php # 15 testes
│   │   └── UserControllerTest.php         # 12 testes
│   └── User/
│       ├── ProfileControllerTest.php      # 6 testes
│       └── TravelRequestControllerTest.php # 8 testes
├── Unit/
│   └── Models/
│       ├── TravelRequestTest.php          # 15 testes
│       ├── TravelRequestStatusHistoryTest.php # 12 testes
│       └── UserTest.php                   # 16 testes
└── Traits/
    └── TestHelpers.php                    # Utilitários de teste
```

### Configuração de Testes

- **Database**: SQLite em memória (`:memory:`)
- **Environment**: `.env.testing`
- **Helpers**: Traits customizados para criação de dados
- **Factories**: Factories para geração de dados de teste

## 📁 Estrutura do Projeto

```
onfly-microservice/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── API/
│   │   │       ├── Admin/         # Controllers administrativos
│   │   │       ├── Auth/          # Controllers de autenticação
│   │   │       └── User/          # Controllers de usuário
│   │   ├── Middleware/            # Middlewares customizados
│   │   └── Resources/             # API Resources
│   ├── Models/                    # Eloquent Models
│   ├── Observers/                 # Event Observers
│   ├── Notifications/             # Notifications
│   └── Providers/                 # Service Providers
├── database/
│   ├── factories/                 # Model Factories
│   ├── migrations/                # Database Migrations
│   └── seeders/                   # Database Seeders
├── docs/                          # Documentação
├── routes/
│   ├── api.php                    # Rotas principais
│   ├── admin.php                  # Rotas administrativas
│   ├── auth.php                   # Rotas de autenticação
│   └── user.php                   # Rotas de usuário
├── tests/
│   ├── Feature/                   # Testes de integração
│   ├── Unit/                      # Testes unitários
│   └── Traits/                    # Test Helpers
└── docker/                        # Configurações Docker
```

## 🔒 Segurança

### Autenticação & Autorização

- **Laravel Sanctum**: Tokens seguros para API
- **Middleware**: Verificação de roles e permissões
- **CORS**: Configuração adequada para produção
- **Rate Limiting**: Proteção contra ataques

### Validação

- **Form Requests**: Validação de dados de entrada
- **Database Constraints**: Integridade referencial
- **Sanitização**: Limpeza de dados de entrada

### Auditoria

- **Observer Pattern**: Log de mudanças importantes
- **Status History**: Rastreamento completo de alterações
- **User Activity**: Log de ações do usuário

## 🌐 Deployment

### Produção

```bash
# Otimizações
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrations
php artisan migrate --force

# Supervisor para queues
php artisan queue:work
```

### Docker Production

```dockerfile
# Dockerfile otimizado para produção
FROM php:8.2-fpm
# ... configurações de produção
```

## 📈 Monitoramento

### Health Checks

- `GET /api/health` - Status geral do sistema
- `GET /api/admin/dashboard/health` - Status detalhado

### Métricas

- **Database**: Conexões e queries
- **Memory**: Uso de memória
- **Storage**: Espaço em disco
- **Cache**: Status do Redis

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

### Padrões de Código

- **PSR-4**: Autoloading padrão
- **PSR-12**: Estilo de código
- **Laravel Conventions**: Padrões do framework
- **Tests**: Cobertura mínima de 80%

## 📞 Contato

**Leonardo Ribeiro**
- Email: lviniciusribeiro@yahoo.com.br
- LinkedIn: [Leonardo Ribeiro](https://linkedin.com/in/leonardo-ribeiro)
- GitHub: [@leolegends](https://github.com/leolegends)

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

**Desenvolvido com ❤️ por Leonardo Ribeiro para Onfly Teams**

---

## 🆘 Suporte

Para suporte, documentação adicional ou questões específicas, entre em contato através do email: `lviniciusribeiro@yahoo.com.br`

### FAQ

**Q: Como resetar o banco de dados?**
```bash
php artisan migrate:fresh --seed
```

**Q: Como executar apenas testes unitários?**
```bash
php artisan test
```

**Q: Como gerar documentação da API?**
```bash
php artisan scribe:generate
```

**Q: Como limpar cache?**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```
