# 🚀 Setup - Sistema de Gestão de Igrejas

## Pré-requisitos

- Docker & Docker Compose
- Node.js (opcional, para desenvolvimento frontend)
- PHP 8.3+ (opcional, se rodar localmente)

## Instalação Rápida

### 1. Clonar e Configurar

```bash
git clone <repo>
cd check-docs-engenharia
cp .env.example .env
```

### 2. Iniciar Docker

```bash
docker-compose up -d
```

Isso inicia:
- **Apache + PHP** (porta 7842)
- **MySQL** (porta 3306)
- **Node/Vite** (porta 5173)

### 3. Instalar Dependências

```bash
docker-compose exec app composer install
docker-compose exec app npm install
```

### 4. Gerar Chave de Encriptação

```bash
docker-compose exec app php artisan key:generate
```

### 5. Executar Migrations + Seeds

```bash
docker-compose exec app php artisan migrate:fresh --seed
```

### 6. Acessar

- **Portal Público**: http://localhost:7842/
- **Painel Admin**: http://localhost:7842/igrejas (login)
  - Email: `admin@igrejas.local`
  - Senha: `Admin@123456`

---

## Rodar sem Docker (Desenvolvimento Local)

### 1. Instalar Dependências

```bash
composer install
npm install
```

### 2. Configurar .env

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Banco de Dados

Configurar `DB_*` no `.env` para seu banco (MySQL/PostgreSQL).

```bash
php artisan migrate:fresh --seed
```

### 4. Rodar Servidor

```bash
php artisan serve    # http://localhost:8000
npm run dev          # Vite em http://localhost:5173
```

---

## Comandos Úteis

### Artisan

```bash
# Migrations
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh --seed

# Tinker (console interativo)
php artisan tinker

# Gerar novo comando/model/controller
php artisan make:model Igreja -m
php artisan make:controller IgrejaController --resource

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Qualidade de Código

```bash
# PHPStan (análise estática)
php vendor/bin/phpstan analyse app/ --level=9

# PHPUnit (testes)
php vendor/bin/phpunit

# Pint (formatação)
php vendor/bin/pint app/
```

### Logs

```bash
# Ver logs em tempo real
php artisan pail

# Ou no Docker
docker-compose logs -f app
```

---

## Estrutura do Projeto

```
├── app/
│   ├── Models/              # Igreja, Foto, User, Permission, AuditLog
│   ├── Controllers/         # IgrejaController, FotoController, PortalPublicoController
│   ├── Policies/            # IgrejaPolicy, FotoPolicy
│   ├── Observers/           # IgrejaObserver, FotoObserver (auditoria)
│   └── Providers/           # AppServiceProvider
├── database/
│   ├── migrations/          # Tabelas
│   ├── factories/           # Dados fake (testing)
│   └── seeders/             # Dados iniciais
├── resources/
│   ├── views/
│   │   ├── portal/          # Home pública, detalhes públicos
│   │   ├── igrejas/         # CRUD admin
│   │   └── fotos/           # Upload
│   └── css/js               # Assets
├── routes/
│   ├── web.php              # Rotas públicas + autenticadas
│   └── auth.php             # Login/registro
├── tests/                   # PHPUnit/Pest
├── storage/                 # Logs, uploads
└── docker/                  # Configuração Docker
```

---

## Desenvolvimento

### Criar um Model com Migration

```bash
php artisan make:model Tarefa -m
```

### Criar um Controller Resource

```bash
php artisan make:controller TarefaController --resource --model=Tarefa
```

### Criar uma Policy

```bash
php artisan make:policy TarefaPolicy --model=Tarefa
```

### Criar um Observer

```bash
php artisan make:observer TarefaObserver --model=Tarefa
```

---

## Troubleshooting

### "No application encryption key has been specified"

```bash
php artisan key:generate
```

### "SQLSTATE[HY000] [2002] Connection refused"

Verifique se MySQL está rodando:

```bash
docker-compose ps
docker-compose up -d mysql
```

### "Target class [Controller] does not exist"

Verifique o namespace no arquivo de rotas.

### Permissões de arquivo

Se houver problemas de permissão em `storage/`:

```bash
sudo chown -R $USER:$USER storage/
chmod -R 755 storage/
```

---

## Variáveis de Ambiente

Principais do `.env`:

```
APP_NAME              # Nome da aplicação
APP_DEBUG             # Debug mode (true=desenvolvimento, false=produção)
APP_URL               # URL da aplicação

DB_CONNECTION         # mysql, sqlite, pgsql
DB_HOST/PORT/DATABASE # Credenciais do banco
DB_USERNAME/PASSWORD

SESSION_DRIVER        # database, cookie
FILESYSTEM_DISK       # public, private

MAIL_MAILER           # log, smtp, mailgun...
ADMIN_EMAIL           # Email padrão do admin
ADMIN_PASSWORD        # Senha padrão do admin
```

---

## Deploy (Produção)

### 1. Preparar

```bash
APP_ENV=production
APP_DEBUG=false
composer install --optimize-autoloader --no-dev
npm run build
```

### 2. Migrar

```bash
php artisan migrate --force
```

### 3. Cache

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Storage Link

```bash
php artisan storage:link
```

---

## Suporte

- Documentação: [FASE_1_MVP.md](FASE_1_MVP.md)
- Issues: Abra uma issue no repositório
- Laravel Docs: https://laravel.com/docs/

---

**Última atualização**: 2026-04-01
