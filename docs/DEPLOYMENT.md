# 🚀 Guia de Deploy - Onfly Microservice

## 📋 Índice

- [Pré-requisitos](#pré-requisitos)
- [Configuração do Ambiente](#configuração-do-ambiente)
- [Deploy Local](#deploy-local)
- [Deploy com Docker](#deploy-com-docker)
- [Deploy em Produção](#deploy-em-produção)
- [Monitoramento](#monitoramento)
- [Backup & Recovery](#backup--recovery)
- [Troubleshooting](#troubleshooting)

---

## 📋 Pré-requisitos

### Ambiente de Desenvolvimento
- **PHP**: 8.2+
- **Composer**: 2.0+
- **SQLite**: 3.0+
- **Node.js**: 16+ (opcional, para assets)
- **Docker**: 20.10+ (opcional)

### Extensões PHP Necessárias
```bash
php -m | grep -E "(pdo_sqlite|mbstring|xml|ctype|json|tokenizer|bcmath|fileinfo)"
```

### Verificação de Sistema
```bash
# Verificar versão do PHP
php --version

# Verificar extensões
php -m

# Verificar Composer
composer --version

# Verificar SQLite
sqlite3 --version
```

---

## ⚙️ Configuração do Ambiente

### 1. **Clone do Repositório**
```bash
git clone https://github.com/your-org/onfly-microservice.git
cd onfly-microservice
```

### 2. **Instalação de Dependências**
```bash
# Instalar dependências PHP
composer install

# Instalar dependências Node.js (opcional)
npm install
```

### 3. **Configuração do Environment**
```bash
# Copiar arquivo de configuração
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate

# Configurar banco SQLite
touch database/database.sqlite
```

### 4. **Arquivo .env**
```env
# Configuração básica
APP_NAME="Onfly Microservice"
APP_ENV=local
APP_KEY=base64:generated_key_here
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Authentication
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1

# Cache (opcional)
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=debug
```

---

## 🏠 Deploy Local

### 1. **Configuração Inicial**
```bash
# Migrar banco de dados
php artisan migrate

# Executar seeders (opcional)
php artisan db:seed

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### 2. **Iniciar Servidor**
```bash
# Servidor de desenvolvimento
php artisan serve

# Ou especificar host/porta
php artisan serve --host=0.0.0.0 --port=8000
```

### 3. **Testar Instalação**
```bash
# Executar testes
vendor/bin/phpunit

# Verificar health check
curl http://localhost:8000/health
```

### 4. **Comandos Úteis**
```bash
# Verificar rotas
php artisan route:list

# Verificar configuração
php artisan config:show

# Limpar todos os caches
php artisan optimize:clear

# Recriar banco (desenvolvimento)
php artisan migrate:fresh --seed
```

---

## 🐳 Deploy com Docker

### 1. **Estrutura Docker**
```
docker/
├── nginx/
│   └── default.conf
├── php/
│   └── local.ini
├── Dockerfile
└── docker-compose.yml
```

### 2. **Build da Imagem**
```bash
# Build da imagem
docker build -t onfly-microservice .

# Ou usando docker-compose
docker-compose build
```

### 3. **Executar Container**
```bash
# Executar com docker-compose
docker-compose up -d

# Verificar status
docker-compose ps

# Ver logs
docker-compose logs -f app
```

### 4. **Comandos Docker**
```bash
# Executar comando no container
docker-compose exec app php artisan migrate

# Acessar container
docker-compose exec app bash

# Parar containers
docker-compose down

# Rebuild completo
docker-compose down && docker-compose build --no-cache && docker-compose up -d
```

### 5. **Docker Compose para Desenvolvimento**
```yaml
version: '3.8'

services:
  app:
    build: .
    ports:
      - "8000:8000"
    volumes:
      - .:/var/www/html
      - ./database:/var/www/html/database
    environment:
      - APP_ENV=local
      - DB_CONNECTION=sqlite
      - DB_DATABASE=/var/www/html/database/database.sqlite
    command: php artisan serve --host=0.0.0.0 --port=8000
    
  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
    volumes:
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
      - .:/var/www/html
    depends_on:
      - app
```

---

## 🌐 Deploy em Produção

### 1. **Preparação do Ambiente**
```bash
# Configurar servidor (Ubuntu/Debian)
sudo apt update
sudo apt install -y nginx php8.2-fpm php8.2-sqlite3 php8.2-mbstring php8.2-xml php8.2-curl
sudo systemctl enable nginx php8.2-fpm
```

### 2. **Configuração do Nginx**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/html/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 3. **Script de Deploy**
```bash
#!/bin/bash
# deploy.sh

set -e

echo "🚀 Iniciando deploy..."

# Backup do banco atual
cp database/database.sqlite database/database.backup.$(date +%Y%m%d_%H%M%S).sqlite

# Atualizar código
git pull origin main

# Instalar dependências
composer install --no-dev --optimize-autoloader

# Migrar banco
php artisan migrate --force

# Limpar e otimizar cache
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ajustar permissões
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Reiniciar serviços
sudo systemctl reload nginx
sudo systemctl reload php8.2-fpm

echo "✅ Deploy concluído!"
```

### 4. **Arquivo .env de Produção**
```env
APP_NAME="Onfly Microservice"
APP_ENV=production
APP_KEY=base64:your_production_key_here
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

LOG_CHANNEL=stack
LOG_LEVEL=warning

SANCTUM_STATEFUL_DOMAINS=yourdomain.com
```

### 5. **SSL/HTTPS com Let's Encrypt**
```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx

# Gerar certificado
sudo certbot --nginx -d yourdomain.com

# Renovação automática
sudo crontab -e
# Adicionar linha: 0 12 * * * /usr/bin/certbot renew --quiet
```

---

## 📊 Monitoramento

### 1. **Health Check Endpoint**
```bash
# Verificar saúde da aplicação
curl https://yourdomain.com/health

# Resposta esperada:
{
  "status": "healthy",
  "checks": {
    "database": {"status": "ok"},
    "cache": {"status": "ok"},
    "storage": {"status": "ok"}
  }
}
```

### 2. **Logs da Aplicação**
```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Logs com filtro
grep "ERROR" storage/logs/laravel.log

# Logs do Nginx
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log
```

### 3. **Monitoramento de Performance**
```bash
# Verificar uso de memória
free -h

# Verificar espaço em disco
df -h

# Verificar processos PHP
ps aux | grep php

# Verificar conexões
netstat -tulpn | grep :80
```

### 4. **Script de Monitoramento**
```bash
#!/bin/bash
# monitor.sh

# Verificar se a aplicação está respondendo
if curl -f -s http://localhost:8000/health > /dev/null; then
    echo "✅ Aplicação OK"
else
    echo "❌ Aplicação com problemas"
    # Reiniciar serviços
    sudo systemctl restart php8.2-fpm
    sudo systemctl restart nginx
fi

# Verificar espaço em disco
DISK_USAGE=$(df / | tail -1 | awk '{print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 80 ]; then
    echo "⚠️ Espaço em disco baixo: ${DISK_USAGE}%"
fi

# Verificar logs de erro
ERROR_COUNT=$(grep "ERROR" storage/logs/laravel.log | wc -l)
if [ $ERROR_COUNT -gt 0 ]; then
    echo "⚠️ Encontrados ${ERROR_COUNT} erros nos logs"
fi
```

---

## 💾 Backup & Recovery

### 1. **Backup do Banco de Dados**
```bash
#!/bin/bash
# backup.sh

BACKUP_DIR="/var/backups/onfly"
DATE=$(date +%Y%m%d_%H%M%S)
DB_PATH="/var/www/html/database/database.sqlite"

mkdir -p $BACKUP_DIR

# Backup do banco
cp $DB_PATH $BACKUP_DIR/database_$DATE.sqlite

# Backup completo da aplicação
tar -czf $BACKUP_DIR/app_backup_$DATE.tar.gz \
    /var/www/html \
    --exclude=node_modules \
    --exclude=vendor

# Manter apenas últimos 7 dias
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup criado: $BACKUP_DIR/database_$DATE.sqlite"
```

### 2. **Restauração**
```bash
#!/bin/bash
# restore.sh

if [ $# -ne 1 ]; then
    echo "Uso: $0 <arquivo_backup>"
    exit 1
fi

BACKUP_FILE=$1
DB_PATH="/var/www/html/database/database.sqlite"

# Backup do banco atual
cp $DB_PATH $DB_PATH.before_restore

# Restaurar banco
cp $BACKUP_FILE $DB_PATH

# Verificar integridade
sqlite3 $DB_PATH "PRAGMA integrity_check;"

echo "Restauração concluída"
```

### 3. **Cron Job para Backup Automático**
```bash
# Editar crontab
crontab -e

# Adicionar linha para backup diário às 2:00
0 2 * * * /path/to/backup.sh >> /var/log/backup.log 2>&1
```

---

## 🔧 Troubleshooting

### 1. **Problemas Comuns**

#### **Erro: "Database file does not exist"**
```bash
# Verificar se arquivo existe
ls -la database/database.sqlite

# Criar arquivo se não existir
touch database/database.sqlite

# Executar migrações
php artisan migrate
```

#### **Erro: "Permission denied"**
```bash
# Ajustar permissões
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### **Erro: "Route not found"**
```bash
# Limpar cache de rotas
php artisan route:clear

# Verificar rotas
php artisan route:list
```

### 2. **Debug da Aplicação**
```bash
# Habilitar debug
php artisan down
# Editar .env: APP_DEBUG=true
php artisan up

# Ver logs detalhados
tail -f storage/logs/laravel.log

# Verificar configuração
php artisan config:show
```

### 3. **Performance Issues**
```bash
# Verificar queries lentas
php artisan telescope:install  # Para desenvolvimento

# Otimizar autoloader
composer dump-autoload --optimize

# Cache de configuração
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. **Recuperação de Emergência**
```bash
# Reset completo (desenvolvimento)
php artisan migrate:fresh --seed

# Reconstruir cache
php artisan optimize:clear
php artisan optimize

# Verificar integridade do banco
sqlite3 database/database.sqlite "PRAGMA integrity_check;"
```

---

## 🚀 Comandos Úteis

### Desenvolvimento
```bash
# Servidor local
php artisan serve

# Executar testes
vendor/bin/phpunit

# Verificar código
vendor/bin/pint

# Gerar documentação
php artisan route:list --json > api-routes.json
```

### Produção
```bash
# Deploy
./deploy.sh

# Backup
./backup.sh

# Monitoramento
./monitor.sh

# Logs
tail -f storage/logs/laravel.log
```

### Docker
```bash
# Iniciar ambiente
docker-compose up -d

# Parar ambiente
docker-compose down

# Rebuild
docker-compose build --no-cache

# Executar comando
docker-compose exec app php artisan migrate
```

---

## 📞 Suporte

### Contatos
- **Desenvolvedor**: Leonardo Ribeiro
- **Email**: leonardo@onfly.com.br
- **Slack**: #onfly-dev

### Recursos
- **Documentação**: `/docs` folder
- **API**: `/api/documentation`
- **Health Check**: `/health`
- **Logs**: `storage/logs/laravel.log`

---

**Desenvolvido com ❤️ por Leonardo Ribeiro para Onfly Teams**
