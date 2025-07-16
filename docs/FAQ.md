# ❓ FAQ - Onfly Microservice

## 📋 Perguntas Frequentes

### 🚀 **Instalação e Configuração**

#### **Q: Como instalo o projeto?**
**A:** Siga os passos no [README.md](./README.md):
```bash
git clone https://github.com/your-org/onfly-microservice.git
cd onfly-microservice
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

#### **Q: Quais são os requisitos do sistema?**
**A:** 
- PHP 8.2 ou superior
- Composer 2.0+
- SQLite 3.0+ (desenvolvimento)
- MySQL 8.0+ (produção)
- 512MB RAM mínimo

#### **Q: Como configurar o banco de dados?**
**A:** Para desenvolvimento, use SQLite:
```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Para produção, use MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=onfly_db
DB_USERNAME=root
DB_PASSWORD=password
```

#### **Q: O projeto funciona com Docker?**
**A:** Sim! Use:
```bash
docker-compose up -d
```

---

### 🔐 **Autenticação**

#### **Q: Como funciona a autenticação?**
**A:** Usamos Laravel Sanctum para autenticação baseada em tokens:
```bash
POST /api/auth/login
{
  "email": "user@example.com",
  "password": "password"
}
```

#### **Q: Como obter um token de API?**
**A:** Após login, você recebe um token:
```json
{
  "access_token": "1|xyz123...",
  "token_type": "Bearer",
  "user": {...}
}
```

#### **Q: O token expira?**
**A:** Sim, por padrão expira em 24 horas. Configure em `config/sanctum.php`:
```php
'expiration' => 60 * 24, // 24 horas
```

#### **Q: Como usar o token nas requisições?**
**A:** Adicione no header:
```bash
Authorization: Bearer 1|xyz123...
```

---

### 👥 **Usuários e Permissões**

#### **Q: Quais são os tipos de usuários?**
**A:** Existem 3 roles:
- **employee**: Usuário comum
- **manager**: Gerente (pode aprovar algumas solicitações)
- **admin**: Administrador (acesso total)

#### **Q: Como criar um usuário admin?**
**A:** Via seeder ou manualmente:
```bash
php artisan db:seed --class=AdminUserSeeder
```

#### **Q: Como alterar a role de um usuário?**
**A:** Via API admin:
```bash
PUT /api/admin/users/{id}
{
  "role": "admin"
}
```

#### **Q: Funcionários podem ver solicitações de outros?**
**A:** Não, apenas administradores podem ver todas as solicitações.

---

### ✈️ **Solicitações de Viagem**

#### **Q: Como criar uma solicitação de viagem?**
**A:** Via API:
```bash
POST /api/user/travel-requests
{
  "requestor_name": "João Silva",
  "destination": "São Paulo - SP",
  "departure_date": "2025-01-15",
  "return_date": "2025-01-20",
  "purpose": "Reunião de negócios",
  "justification": "Apresentação para cliente"
}
```

#### **Q: Quais são os status possíveis?**
**A:** 
- **requested**: Solicitada (inicial)
- **approved**: Aprovada
- **rejected**: Rejeitada
- **cancelled**: Cancelada

#### **Q: Posso editar uma solicitação aprovada?**
**A:** Não, apenas solicitações com status "requested" podem ser editadas.

#### **Q: Como aprovar uma solicitação?**
**A:** Apenas admins podem aprovar:
```bash
POST /api/admin/travel-requests/{id}/approve
{
  "notes": "Aprovado conforme política"
}
```

#### **Q: Posso cancelar uma solicitação?**
**A:** Sim, você pode cancelar suas próprias solicitações:
```bash
POST /api/user/travel-requests/{id}/cancel
{
  "reason": "Motivo do cancelamento"
}
```

---

### 🧪 **Testes**

#### **Q: Como executar os testes?**
**A:** 
```bash
# Todos os testes
vendor/bin/phpunit

# Apenas unitários
vendor/bin/phpunit tests/Unit

# Apenas feature
vendor/bin/phpunit tests/Feature
```

#### **Q: Quantos testes existem?**
**A:** 82 testes no total:
- 31 testes unitários
- 51 testes de feature

#### **Q: Como executar testes específicos?**
**A:** 
```bash
# Teste específico
vendor/bin/phpunit tests/Feature/Admin/TravelRequestControllerTest.php

# Método específico
vendor/bin/phpunit --filter test_user_can_create_travel_request
```

#### **Q: Como gerar relatório de coverage?**
**A:** 
```bash
vendor/bin/phpunit --coverage-html coverage
```

---

### 📊 **API e Endpoints**

#### **Q: Onde encontro a documentação da API?**
**A:** Em [docs/API_ROUTES.md](./docs/API_ROUTES.md)

#### **Q: Qual é a URL base da API?**
**A:** 
- Desenvolvimento: `http://localhost:8000/api`
- Produção: `https://yourdomain.com/api`

#### **Q: Como funciona a paginação?**
**A:** Automática com parâmetros `page` e `per_page`:
```bash
GET /api/admin/travel-requests?page=2&per_page=10
```

#### **Q: Como filtrar resultados?**
**A:** Via query parameters:
```bash
GET /api/admin/travel-requests?status=approved&user_id=1
```

#### **Q: Existe rate limiting?**
**A:** Sim, 60 requisições por minuto por IP.

---

### 🚀 **Deploy e Produção**

#### **Q: Como fazer deploy?**
**A:** Consulte [docs/DEPLOYMENT.md](./docs/DEPLOYMENT.md)

#### **Q: Posso usar SQLite em produção?**
**A:** Não recomendado. Use MySQL/PostgreSQL para produção.

#### **Q: Como configurar HTTPS?**
**A:** Use Let's Encrypt com Certbot:
```bash
sudo certbot --nginx -d yourdomain.com
```

#### **Q: Como fazer backup?**
**A:** Script de backup automático:
```bash
# Ver em docs/DEPLOYMENT.md
./backup.sh
```

#### **Q: Como monitorar a aplicação?**
**A:** Health check endpoint:
```bash
GET /health
```

---

### 🔧 **Problemas Comuns**

#### **Q: Erro "Database file does not exist"**
**A:** Crie o arquivo:
```bash
touch database/database.sqlite
php artisan migrate
```

#### **Q: Erro "Permission denied" nos logs**
**A:** Ajuste permissões:
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### **Q: Testes ficam "travados"**
**A:** Verifique a configuração de teste:
```bash
# Recriar banco de teste
php artisan migrate:fresh --env=testing
```

#### **Q: Erro "Route not found"**
**A:** Limpe cache:
```bash
php artisan route:clear
php artisan cache:clear
```

#### **Q: Token inválido**
**A:** Verifique:
1. Se o token não expirou
2. Se está no formato correto
3. Se o usuário ainda está ativo

---

### 📚 **Desenvolvimento**

#### **Q: Como contribuir?**
**A:** Leia [docs/CONTRIBUTING.md](./docs/CONTRIBUTING.md)

#### **Q: Quais são os padrões de código?**
**A:** PSR-12 + Laravel conventions:
```bash
vendor/bin/pint --test
```

#### **Q: Como criar um novo endpoint?**
**A:** 
1. Criar controller
2. Adicionar rota
3. Escrever testes
4. Atualizar documentação

#### **Q: Como adicionar um novo teste?**
**A:** 
```bash
# Teste de feature
php artisan make:test NewFeatureTest

# Teste unitário
php artisan make:test NewUnitTest --unit
```

#### **Q: Como debugar a aplicação?**
**A:** 
```bash
# Habilitar debug
APP_DEBUG=true

# Ver logs
tail -f storage/logs/laravel.log

# Usar dump/dd
dd($variable);
```

---

### 🔄 **Migrações e Dados**

#### **Q: Como executar migrações?**
**A:** 
```bash
# Executar pendentes
php artisan migrate

# Rollback
php artisan migrate:rollback

# Fresh (limpa tudo)
php artisan migrate:fresh --seed
```

#### **Q: Como criar dados de teste?**
**A:** 
```bash
# Executar seeders
php artisan db:seed

# Seeder específico
php artisan db:seed --class=UserSeeder
```

#### **Q: Como resetar o banco?**
**A:** 
```bash
# Desenvolvimento
php artisan migrate:fresh --seed

# Produção (cuidado!)
php artisan migrate:reset
php artisan migrate
```

---

### 📱 **Integração Frontend**

#### **Q: Como integrar com React/Vue/Angular?**
**A:** Use a API REST documentada em [docs/API_ROUTES.md](./docs/API_ROUTES.md)

#### **Q: Como tratar erros de autenticação?**
**A:** Intercepte respostas 401:
```javascript
// Axios example
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      // Redirecionar para login
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);
```

#### **Q: Como implementar refresh token?**
**A:** Monitore expiração e renove automaticamente:
```javascript
// Implementar lógica de refresh
if (tokenIsExpired()) {
  await refreshToken();
}
```

---

### 📞 **Suporte**

#### **Q: Como reportar bugs?**
**A:** 
1. Abra issue no GitHub
2. Inclua logs relevantes
3. Passos para reproduzir
4. Ambiente utilizado

#### **Q: Como solicitar funcionalidades?**
**A:** 
1. Abra issue com label "enhancement"
2. Descreva a funcionalidade
3. Justifique a necessidade
4. Sugira implementação

#### **Q: Onde obter ajuda?**
**A:** 
- **Issues**: Para bugs e features
- **Email**: leonardo@onfly.com.br
- **Documentação**: Pasta `docs/`

---

### 📊 **Performance**

#### **Q: Como otimizar performance?**
**A:** 
```bash
# Cache de configuração
php artisan config:cache

# Cache de rotas
php artisan route:cache

# Cache de views
php artisan view:cache

# Otimizar autoloader
composer dump-autoload --optimize
```

#### **Q: Como monitorar queries lentas?**
**A:** Habilite query logging:
```php
// Em AppServiceProvider
DB::listen(function ($query) {
    if ($query->time > 1000) {
        Log::warning('Slow query', [
            'sql' => $query->sql,
            'time' => $query->time,
        ]);
    }
});
```

---

### 🔒 **Segurança**

#### **Q: Como proteger contra ataques?**
**A:** 
- Rate limiting habilitado
- Validação de entrada
- Sanitização de dados
- CORS configurado
- HTTPS em produção

#### **Q: Como configurar CORS?**
**A:** No arquivo `config/cors.php`:
```php
'paths' => ['api/*'],
'allowed_methods' => ['*'],
'allowed_origins' => ['http://localhost:3000'],
```

#### **Q: Como habilitar logs de segurança?**
**A:** Configure em `config/logging.php` e monitore `storage/logs/`

---

## 📞 Não Encontrou Sua Pergunta?

Entre em contato:
- **Email**: leonardo@onfly.com.br
- **Issues**: https://github.com/your-org/onfly-microservice/issues
- **Documentação**: Pasta `docs/`

---

**Desenvolvido com ❤️ por Leonardo Ribeiro para Onfly Teams**

*Última atualização: 20 de dezembro de 2024*
