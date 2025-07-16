# 📚 Documentação - Onfly Microservice

## 🎯 Visão Geral

Este diretório contém toda a documentação do **Onfly Microservice**, um sistema de gerenciamento de viagens corporativas desenvolvido com Laravel 12.x.

---

## 📁 Estrutura da Documentação

### 📖 Documentos Principais

| Arquivo | Descrição | Público-Alvo |
|---------|-----------|--------------|
| [`README.md`](../README.md) | Documentação principal do projeto | Desenvolvedores, usuários finais |
| [`ARCHITECTURE.md`](./ARCHITECTURE.md) | Arquitetura detalhada do sistema | Arquitetos, desenvolvedores sênior |
| [`DEPLOYMENT.md`](./DEPLOYMENT.md) | Guia completo de deploy | DevOps, administradores de sistema |
| [`CONTRIBUTING.md`](./CONTRIBUTING.md) | Guia de contribuição | Desenvolvedores, contribuidores |
| [`FAQ.md`](./FAQ.md) | Perguntas frequentes | Todos os usuários |

### 🚀 Documentação da API

| Arquivo | Descrição | Público-Alvo |
|---------|-----------|--------------|
| [`API_ROUTES.md`](./API_ROUTES.md) | Documentação completa das rotas da API | Desenvolvedores frontend, integradores |
| [`API_PAYLOADS.md`](./API_PAYLOADS.md) | Exemplos de payloads e respostas | Desenvolvedores, QA |

---

## 🚀 Começando

### Para Desenvolvedores
1. Leia o [README.md](../README.md) para configuração inicial
2. Consulte [CONTRIBUTING.md](./CONTRIBUTING.md) para padrões de desenvolvimento
3. Veja [API_ROUTES.md](./API_ROUTES.md) para integração com a API

### Para DevOps/Administradores
1. Consulte [DEPLOYMENT.md](./DEPLOYMENT.md) para deploy em produção
2. Veja [ARCHITECTURE.md](./ARCHITECTURE.md) para entender a arquitetura

### Para Arquitetos/Tech Leads
1. Estude [ARCHITECTURE.md](./ARCHITECTURE.md) para design patterns
2. Revise [CONTRIBUTING.md](./CONTRIBUTING.md) para padrões de código

---

## 🎯 Documentação por Funcionalidade

### 🔐 Autenticação
- **Arquivo**: [API_ROUTES.md](./API_ROUTES.md#autenticação)
- **Descrição**: Sistema de autenticação com Laravel Sanctum
- **Endpoints**: Login, logout, refresh token

### 👥 Gerenciamento de Usuários
- **Arquivo**: [API_ROUTES.md](./API_ROUTES.md#gerenciamento-de-usuários)
- **Descrição**: CRUD completo de usuários e perfis
- **Endpoints**: Usuários, perfis, permissões

### ✈️ Solicitações de Viagem
- **Arquivo**: [API_ROUTES.md](./API_ROUTES.md#solicitações-de-viagem)
- **Descrição**: Workflow completo de solicitações de viagem
- **Endpoints**: Criar, listar, aprovar, rejeitar, cancelar

### 📊 Dashboard e Relatórios
- **Arquivo**: [API_ROUTES.md](./API_ROUTES.md#dashboard-e-relatórios)
- **Descrição**: Estatísticas e métricas do sistema
- **Endpoints**: Dashboard, relatórios, métricas

---

## 🏗️ Arquitetura do Sistema

### 📊 Visão Geral Arquitetural

```
┌─────────────────────────────────────────────────────────────┐
│                    Frontend/Cliente                         │
│                  (React/Vue/Angular)                        │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                   API Gateway/Proxy                        │
│                    (Nginx/Apache)                          │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                 Onfly Microservice                          │
│                   (Laravel 12.x)                           │
│                                                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │ Controllers │  │ Middleware  │  │ Resources   │          │
│  │ (API)       │  │ (Auth)      │  │ (JSON)      │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
│                                                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │  Models     │  │  Services   │  │  Observers  │          │
│  │ (Eloquent)  │  │ (Business)  │  │ (Events)    │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                   Database Layer                            │
│                    (SQLite/MySQL)                          │
└─────────────────────────────────────────────────────────────┘
```

### 🔧 Tecnologias Utilizadas

- **Backend**: Laravel 12.x (PHP 8.2+)
- **Database**: SQLite (desenvolvimento), MySQL (produção)
- **Authentication**: Laravel Sanctum
- **Testing**: PHPUnit (82 testes)
- **Documentation**: Markdown
- **Containerization**: Docker

---

## 🧪 Testes

### 📊 Estatísticas de Testes

- **Total de Testes**: 82
- **Testes Unitários**: 31 (37.8%)
- **Testes de Feature**: 51 (62.2%)
- **Assertions**: 402
- **Coverage**: Completa para funcionalidades principais

### 🗂️ Estrutura de Testes

```
tests/
├── Feature/                    # Testes de integração (51 testes)
│   ├── Admin/                  # Testes admin (30 testes)
│   │   ├── DashboardControllerTest.php
│   │   ├── TravelRequestControllerTest.php
│   │   └── UserControllerTest.php
│   ├── User/                   # Testes usuário (11 testes)
│   │   ├── ProfileControllerTest.php
│   │   └── TravelRequestControllerTest.php
│   └── AuthControllerTest.php  # Testes autenticação (10 testes)
├── Unit/                       # Testes unitários (31 testes)
│   └── Models/                 # Testes de models (30 testes)
│       ├── TravelRequestStatusHistoryTest.php
│       ├── TravelRequestTest.php
│       └── UserTest.php
└── TestCase.php                # Base dos testes
```

### 🚀 Executar Testes

```bash
# Todos os testes
vendor/bin/phpunit

# Apenas testes unitários
vendor/bin/phpunit tests/Unit

# Apenas testes de feature
vendor/bin/phpunit tests/Feature

# Com coverage
vendor/bin/phpunit --coverage-html coverage
```

---

## 🔄 Fluxo de Desenvolvimento

### 1. **Setup Inicial**
```bash
git clone https://github.com/your-org/onfly-microservice.git
cd onfly-microservice
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

### 2. **Desenvolvimento**
```bash
# Executar testes
vendor/bin/phpunit

# Verificar code style
vendor/bin/pint

# Iniciar servidor
php artisan serve
```

### 3. **Contribuição**
```bash
# Criar branch
git checkout -b feature/nova-funcionalidade

# Fazer alterações
git add .
git commit -m "feat: add nova funcionalidade"

# Enviar PR
git push origin feature/nova-funcionalidade
```

---

## 📞 Suporte e Contato

### 👨‍💻 Desenvolvedor Principal
- **Nome**: Leonardo Ribeiro
- **Email**: leonardo@onfly.com.br
- **GitHub**: @leonardo-ribeiro

### 🔗 Links Úteis
- **Repositório**: https://github.com/your-org/onfly-microservice
- **Issues**: https://github.com/your-org/onfly-microservice/issues
- **Wiki**: https://github.com/your-org/onfly-microservice/wiki

### 📚 Recursos Externos
- [Laravel Documentation](https://laravel.com/docs)
- [PHP Documentation](https://www.php.net/docs.php)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)

---

## 📝 Versionamento

### Versão Atual: v1.0.0

### Changelog
- **v1.0.0**: Release inicial com funcionalidades completas
  - Sistema de autenticação
  - Gerenciamento de usuários
  - Solicitações de viagem
  - Dashboard administrativo
  - 82 testes implementados

---

## 📄 Licença

Este projeto é proprietário da **Onfly Teams** e está protegido por direitos autorais.

---

**Desenvolvido com ❤️ por Leonardo Ribeiro para Onfly Teams**

*Última atualização: Dezembro 2024*
