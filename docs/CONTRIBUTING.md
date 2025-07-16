# 🤝 Guia de Contribuição - Onfly Microservice

## 📋 Índice

- [Como Contribuir](#como-contribuir)
- [Configuração do Ambiente](#configuração-do-ambiente)
- [Padrões de Código](#padrões-de-código)
- [Testes](#testes)
- [Git Workflow](#git-workflow)
- [Pull Requests](#pull-requests)
- [Revisão de Código](#revisão-de-código)
- [Documentação](#documentação)

---

## 🚀 Como Contribuir

Agradecemos seu interesse em contribuir para o **Onfly Microservice**! Este guia irá ajudá-lo a configurar seu ambiente de desenvolvimento e seguir nossas práticas de contribuição.

### Tipos de Contribuição

- 🐛 **Bug fixes**: Correções de bugs
- ✨ **Features**: Novas funcionalidades
- 📚 **Documentation**: Melhorias na documentação
- 🧪 **Tests**: Adição ou melhoria de testes
- 🔧 **Refactoring**: Melhoria do código existente
- 🎨 **UI/UX**: Melhorias na interface

---

## ⚙️ Configuração do Ambiente

### 1. **Fork e Clone**
```bash
# Fork o repositório no GitHub
# Depois clone seu fork
git clone https://github.com/SEU_USERNAME/onfly-microservice.git
cd onfly-microservice

# Adicionar repositório upstream
git remote add upstream https://github.com/onfly/onfly-microservice.git
```

### 2. **Instalação**
```bash
# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env
php artisan key:generate
touch database/database.sqlite

# Migrar banco
php artisan migrate

# Executar seeders
php artisan db:seed
```

### 3. **Verificação**
```bash
# Executar testes
vendor/bin/phpunit

# Verificar code style
vendor/bin/pint --test

# Iniciar servidor
php artisan serve
```

---

## 📝 Padrões de Código

### 1. **PSR-12 Coding Standard**

Seguimos o padrão PSR-12 para formatação de código PHP:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExampleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = [
            'message' => 'Hello World',
            'timestamp' => now()->toISOString(),
        ];

        return response()->json($data);
    }
}
```

### 2. **Naming Conventions**

#### Classes
```php
// ✅ Correto
class TravelRequestController
class UserService
class TravelRequestFactory

// ❌ Incorreto
class travelRequestController
class userservice
class Travel_Request_Factory
```

#### Métodos
```php
// ✅ Correto
public function createTravelRequest()
public function getUserProfile()
public function isAdmin()

// ❌ Incorreto
public function CreateTravelRequest()
public function get_user_profile()
public function is_admin()
```

#### Variáveis
```php
// ✅ Correto
$travelRequest = new TravelRequest();
$userProfile = $user->profile;
$isActive = $user->is_active;

// ❌ Incorreto
$TravelRequest = new TravelRequest();
$user_profile = $user->profile;
$is_active = $user->is_active;
```

### 3. **Documentação de Código**

#### DocBlocks
```php
/**
 * Cria uma nova solicitação de viagem
 *
 * @param array $data Dados da solicitação
 * @param User $user Usuário que está criando a solicitação
 * @return TravelRequest
 * @throws ValidationException
 */
public function createTravelRequest(array $data, User $user): TravelRequest
{
    // Implementação
}
```

#### Comentários
```php
// ✅ Correto - Explicar o "porquê"
// Precisamos verificar se o usuário tem permissão antes de aprovar
if (!$user->canApprove($travelRequest)) {
    throw new UnauthorizedException();
}

// ❌ Incorreto - Explicar o "o quê"
// Verificar se usuário pode aprovar
if (!$user->canApprove($travelRequest)) {
    throw new UnauthorizedException();
}
```

### 4. **Estrutura de Arquivos**

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── API/
│   │   │   ├── Admin/
│   │   │   │   ├── TravelRequestController.php
│   │   │   │   └── UserController.php
│   │   │   └── User/
│   │   │       └── TravelRequestController.php
│   │   └── Controller.php
│   ├── Middleware/
│   │   └── AdminMiddleware.php
│   └── Resources/
│       └── TravelRequestResource.php
├── Models/
│   ├── User.php
│   └── TravelRequest.php
└── Services/
    └── TravelRequestService.php
```

---

## 🧪 Testes

### 1. **Estrutura de Testes**

```
tests/
├── Feature/
│   ├── Admin/
│   │   ├── TravelRequestControllerTest.php
│   │   └── UserControllerTest.php
│   └── User/
│       └── TravelRequestControllerTest.php
├── Unit/
│   ├── UserTest.php
│   └── TravelRequestTest.php
├── TestCase.php
└── Traits/
    └── TestHelpers.php
```

### 2. **Padrões de Teste**

#### Naming
```php
// ✅ Correto
/** @test */
public function user_can_create_travel_request()

/** @test */
public function admin_can_approve_travel_request()

/** @test */
public function it_throws_exception_when_user_not_found()

// ❌ Incorreto
/** @test */
public function testUserCanCreateTravelRequest()

/** @test */
public function test_admin_approve()
```

#### Estrutura AAA (Arrange, Act, Assert)
```php
/** @test */
public function user_can_create_travel_request()
{
    // Arrange
    $user = $this->actingAsUser();
    $payload = [
        'requestor_name' => 'Leonardo',
        'destination' => 'Salvador - BA',
        'departure_date' => '2025-07-16',
        'return_date' => '2025-07-20',
        'purpose' => 'Venda de Software',
        'justification' => 'Viagem a trabalho',
    ];

    // Act
    $response = $this->postJson('/api/user/travel-requests', $payload);

    // Assert
    $response->assertStatus(201);
    $response->assertJsonStructure([
        'id',
        'requestor_name',
        'destination',
        'status',
        'created_at',
    ]);
    $this->assertDatabaseHas('travel_requests', [
        'requestor_name' => 'Leonardo',
        'destination' => 'Salvador - BA',
    ]);
}
```

### 3. **Factories**

```php
class TravelRequestFactory extends Factory
{
    protected $model = TravelRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'requestor_name' => $this->faker->name,
            'destination' => $this->faker->city . ' - ' . $this->faker->stateAbbr,
            'departure_date' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
            'return_date' => $this->faker->dateTimeBetween('+2 days', '+35 days'),
            'purpose' => $this->faker->sentence,
            'justification' => $this->faker->paragraph,
            'status' => 'requested',
        ];
    }

    public function approved(): self
    {
        return $this->state(fn() => ['status' => 'approved']);
    }
}
```

### 4. **Executar Testes**

```bash
# Todos os testes
vendor/bin/phpunit

# Testes específicos
vendor/bin/phpunit tests/Feature/Admin/TravelRequestControllerTest.php

# Com coverage
vendor/bin/phpunit --coverage-html coverage

# Filtrar por método
vendor/bin/phpunit --filter test_user_can_create_travel_request
```

---

## 🔄 Git Workflow

### 1. **Branching Strategy**

```
main
├── develop
│   ├── feature/travel-request-approval
│   ├── feature/user-dashboard
│   └── bugfix/fix-date-validation
└── hotfix/critical-auth-fix
```

### 2. **Convenções de Branch**

```bash
# Features
feature/travel-request-approval
feature/user-dashboard
feature/email-notifications

# Bug fixes
bugfix/fix-date-validation
bugfix/resolve-auth-issue

# Hotfixes
hotfix/critical-security-fix
hotfix/database-connection-issue

# Refactoring
refactor/improve-controller-structure
refactor/optimize-database-queries
```

### 3. **Commits Convencionais**

```bash
# Formato
type(scope): description

# Exemplos
feat(auth): add user authentication
fix(travel): resolve date validation bug
docs(readme): update installation instructions
test(user): add unit tests for user model
refactor(controller): improve code structure
style(formatting): fix code formatting
```

### 4. **Workflow de Desenvolvimento**

```bash
# 1. Atualizar branch principal
git checkout develop
git pull upstream develop

# 2. Criar nova branch
git checkout -b feature/nova-funcionalidade

# 3. Fazer alterações e commits
git add .
git commit -m "feat(travel): add travel request approval"

# 4. Push para seu fork
git push origin feature/nova-funcionalidade

# 5. Criar Pull Request no GitHub
```

---

## 🔍 Pull Requests

### 1. **Template de PR**

```markdown
## 📝 Descrição

Breve descrição das alterações implementadas.

## 🔧 Tipo de Mudança

- [ ] 🐛 Bug fix
- [ ] ✨ Nova feature
- [ ] 📚 Documentação
- [ ] 🧪 Testes
- [ ] 🔧 Refactoring

## 🧪 Testes

- [ ] Testes unitários passando
- [ ] Testes de integração passando
- [ ] Novos testes adicionados (se necessário)

## 📋 Checklist

- [ ] Código segue padrões PSR-12
- [ ] Documentação atualizada
- [ ] Testes adicionados/atualizados
- [ ] Não há conflitos de merge
- [ ] PR está linkado a issue relacionada

## 🔗 Issues Relacionadas

Closes #123
```

### 2. **Critérios de Aceitação**

- ✅ **Testes**: Todos os testes devem passar
- ✅ **Code Style**: Código deve seguir PSR-12
- ✅ **Documentação**: Documentação deve estar atualizada
- ✅ **Revisão**: Aprovação de pelo menos 1 revisor
- ✅ **Conflitos**: Não deve haver conflitos de merge

### 3. **Exemplo de PR**

```markdown
## 📝 Descrição

Implementa funcionalidade de aprovação de solicitações de viagem para administradores.

### Alterações:
- Adiciona endpoint POST `/api/admin/travel-requests/{id}/approve`
- Implementa validação de permissões de admin
- Adiciona testes unitários e de integração
- Atualiza documentação da API

## 🔧 Tipo de Mudança

- [x] ✨ Nova feature

## 🧪 Testes

- [x] Testes unitários passando (`AdminTravelRequestControllerTest`)
- [x] Testes de integração passando
- [x] Novos testes adicionados para aprovação

## 📋 Checklist

- [x] Código segue padrões PSR-12
- [x] Documentação atualizada (API_ROUTES.md)
- [x] Testes adicionados/atualizados
- [x] Não há conflitos de merge
- [x] PR está linkado a issue relacionada

## 🔗 Issues Relacionadas

Closes #45
```

---

## 👀 Revisão de Código

### 1. **Checklist do Revisor**

#### Funcionalidade
- [ ] A funcionalidade está implementada corretamente?
- [ ] O código resolve o problema proposto?
- [ ] Há casos edge não cobertos?

#### Código
- [ ] Código está limpo e legível?
- [ ] Variáveis e funções têm nomes descritivos?
- [ ] Não há código duplicado?
- [ ] Padrões de projeto são seguidos?

#### Testes
- [ ] Testes cobrem cenários principais?
- [ ] Testes estão passando?
- [ ] Novos testes foram adicionados quando necessário?

#### Segurança
- [ ] Validação de entrada está implementada?
- [ ] Não há vulnerabilidades de segurança?
- [ ] Autenticação/autorização está correta?

#### Performance
- [ ] Código é eficiente?
- [ ] Não há queries N+1?
- [ ] Recursos são liberados adequadamente?

### 2. **Exemplo de Feedback**

```markdown
## 💬 Feedback Geral

Excelente implementação! A funcionalidade está bem estruturada e os testes cobrem os cenários principais.

## 📝 Sugestões

### `TravelRequestController.php:42`
```php
// Sugestão: Extrair validação para FormRequest
public function approve(ApproveTravelRequestRequest $request, TravelRequest $travelRequest)
{
    // Implementação
}
```

### `TravelRequestTest.php:25`
```php
// Sugestão: Usar factory state para approved requests
$approvedRequest = TravelRequest::factory()->approved()->create();
```

## ✅ Aprovação

Aprovado após implementar sugestões mencionadas.
```

---

## 📚 Documentação

### 1. **Documentação de Código**

#### Controllers
```php
/**
 * Controller para gerenciar solicitações de viagem
 *
 * @package App\Http\Controllers\API\Admin
 * @author Leonardo Ribeiro <leonardo@onfly.com.br>
 */
class TravelRequestController extends Controller
{
    /**
     * Aprovar uma solicitação de viagem
     *
     * @param ApproveTravelRequestRequest $request
     * @param TravelRequest $travelRequest
     * @return JsonResponse
     */
    public function approve(ApproveTravelRequestRequest $request, TravelRequest $travelRequest): JsonResponse
    {
        // Implementação
    }
}
```

#### Models
```php
/**
 * Model para solicitações de viagem
 *
 * @property int $id
 * @property string $requestor_name
 * @property string $destination
 * @property string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @package App\Models
 */
class TravelRequest extends Model
{
    // Implementação
}
```

### 2. **README Updates**

Sempre atualizar o README quando:
- Adicionar nova funcionalidade
- Modificar instalação/configuração
- Adicionar novas dependências
- Alterar comandos ou scripts

### 3. **API Documentation**

Manter atualizada a documentação da API em:
- `docs/API_ROUTES.md`
- `docs/API_PAYLOADS.md`
- Postman collection (se houver)

---

## 🎯 Boas Práticas

### 1. **Commits Pequenos e Frequentes**

```bash
# ✅ Correto
git commit -m "feat(auth): add login endpoint"
git commit -m "feat(auth): add logout endpoint"
git commit -m "test(auth): add authentication tests"

# ❌ Incorreto
git commit -m "feat(auth): add complete authentication system"
```

### 2. **Testes Antes de Submeter**

```bash
# Executar antes de cada commit
vendor/bin/phpunit
vendor/bin/pint --test

# Verificar se aplicação funciona
php artisan serve
```

### 3. **Rebase vs Merge**

```bash
# Usar rebase para manter histórico limpo
git rebase develop

# Usar merge para pull requests
git merge --no-ff feature/nova-funcionalidade
```

### 4. **Comunicação Clara**

- Descrever claramente o que foi implementado
- Explicar decisões técnicas complexas
- Mencionar breaking changes
- Incluir screenshots quando relevante

---

## 🚨 Reportando Bugs

### 1. **Template de Bug Report**

```markdown
## 🐛 Descrição do Bug

Descrição clara e concisa do bug.

## 🔄 Passos para Reproduzir

1. Vá para '...'
2. Clique em '...'
3. Role para baixo até '...'
4. Veja o erro

## 🎯 Comportamento Esperado

Descrição do que deveria acontecer.

## 📸 Screenshots

Se aplicável, adicione screenshots.

## 🖥️ Ambiente

- OS: [e.g. macOS, Linux, Windows]
- PHP Version: [e.g. 8.2]
- Laravel Version: [e.g. 11.x]
- Browser: [e.g. Chrome, Firefox]

## 📋 Contexto Adicional

Qualquer informação adicional sobre o problema.
```

### 2. **Prioridade de Bugs**

- 🔴 **Critical**: Sistema não funciona
- 🟡 **High**: Funcionalidade principal quebrada
- 🟢 **Medium**: Funcionalidade secundária afetada
- 🔵 **Low**: Problema cosmético

---

## 🆘 Obtendo Ajuda

### Canais de Comunicação

- **Issues**: Para bugs e feature requests
- **Discussions**: Para perguntas e discussões
- **Email**: leonardo@onfly.com.br
- **Slack**: #onfly-dev

### Recursos Úteis

- [Laravel Documentation](https://laravel.com/docs)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)

---

## 🎉 Reconhecimento

Agradecemos a todos os contribuidores que ajudam a tornar este projeto melhor!

### Como Contribuir para este Guia

Este guia também é um projeto em constante evolução. Se você tem sugestões para melhorá-lo, por favor:

1. Abra uma issue
2. Faça um pull request
3. Participe das discussions

---

**Obrigado por contribuir! 🚀**

**Desenvolvido com ❤️ por Leonardo Ribeiro para Onfly Teams**
