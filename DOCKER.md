# 🐳 Docker Setup - Sistema de Gestão de Igrejas

## Quick Start

```bash
# 1. Clonar e entrar na pasta
git clone <repo>
cd check-docs-engenharia

# 2. Iniciar containers
docker-compose up -d

# 3. Instalar dependências
docker-compose exec app composer install
docker-compose exec app npm install

# 4. Configurar aplicação
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate:fresh --seed

# 5. Acessar
# Portal:  http://localhost:7842/
# Admin:   http://localhost:7842/igrejas (login com admin@igrejas.local / Admin@123456)
# Vite:    http://localhost:5173/ (já configurado)
```

## O que roda em Docker

### `app` - PHP + Apache
- **Dockerfile**: `docker/php/Dockerfile`
- **Porta**: 7842 (configurável via `APP_PORT`)
- **Volume**: `.` → `/var/www/html`
- **Dependências**: mysql (espera health check)

### `node` - Vite + NPM
- **Imagem**: `node:24-alpine`
- **Porta**: 5173 (configurável via `VITE_PORT`)
- **Comando**: `npm run dev`
- **Volume**: `.` → `/var/www/html` + `node_modules:/var/www/html/node_modules`

### `mysql` - Database
- **Imagem**: `mysql:8.4`
- **Porta**: 3306 (forward: `FORWARD_DB_PORT`)
- **Database**: laravel (conforme `.env`)
- **User**: sail / password

## Variáveis de Ambiente (compose.yaml)

```yaml
APP_PORT=7842              # Porta do Apache
VITE_PORT=5173             # Porta do Vite
FORWARD_DB_PORT=3306       # Port forwarding MySQL
WWWUSER=1000              # UID do www-data (Linux)
WWWGROUP=1000             # GID do www-data (Linux)

DB_DATABASE=laravel        # Nome do DB
DB_USERNAME=sail           # User MySQL
DB_PASSWORD=password       # Senha MySQL
DB_ROOT_PASSWORD=password  # Root MySQL
```

## Comandos Úteis

### Container Management

```bash
# Listar containers em execução
docker-compose ps

# Ver logs em tempo real
docker-compose logs -f app    # PHP/Apache
docker-compose logs -f mysql  # Database
docker-compose logs -f node   # Vite

# Parar/iniciar containers
docker-compose stop
docker-compose start
docker-compose restart app

# Remover volumes (CUIDADO: deleta dados!)
docker-compose down -v
```

### Executar Comandos

```bash
# Artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan tinker
docker-compose exec app php artisan queue:listen

# Composer
docker-compose exec app composer install
docker-compose exec app composer require package-name

# NPM
docker-compose exec node npm install
docker-compose exec node npm run build

# Bash shell
docker-compose exec app bash
docker-compose exec node sh
```

### Database

```bash
# Acessar MySQL
docker-compose exec mysql mysql -u sail -p laravel

# Backup
docker-compose exec mysql mysqldump -u sail -p laravel > backup.sql

# Restore
docker-compose exec -T mysql mysql -u sail -p laravel < backup.sql
```

## Troubleshooting

### "Cannot assign requested address" (Porta já em uso)

```bash
# Mudar porta no .env ou compose.yaml
APP_PORT=8842  # usar porta diferente
docker-compose down
docker-compose up -d
```

### MySQL não inicia

```bash
docker-compose logs mysql
docker-compose down -v
docker-compose up -d mysql --wait
```

### "Connection refused" ao acessar app

Aguarde MySQL estar saudável:

```bash
docker-compose exec mysql mysqladmin ping -u root -p
# Deve retorir "mysqld is alive"
```

### Permissões de arquivo

Se houver problemas com `storage/`:

```bash
docker-compose exec app chown -R www-data:www-data storage/
docker-compose exec app chmod -R 755 storage/
```

### Node modules erro

```bash
docker-compose down
rm -rf node_modules
docker-compose up -d node
# Aguardar npm install automático
```

## Desenvolvimento Local

### Hot Reload (Vite)

O Vite já está configurado para hot module replacement. Editar CSS/JS automaticamente recarrega.

```bash
# Em recursos/css/app.css ou resources/js/app.js
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

### Debugging

#### Usar Tinker

```bash
docker-compose exec app php artisan tinker
> $user = User::first();
> $user->name;
```

#### Ver SQL Queries

Em `config/logging.php`, adicionar:

```php
'channels' => [
    'single' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
],
```

Depois:

```bash
docker-compose logs -f app | grep -i "query"
```

#### Usar Pail

```bash
docker-compose exec app php artisan pail
```

## Performance

### Otimizar para Produção

```bash
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### Build Asset Otimizado

```bash
docker-compose exec node npm run build
```

Isso minifica CSS/JS em `public/build/`.

## Monitorar Recursos

```bash
# CPU, memória, I/O dos containers
docker stats

# Ou detalhado
docker system df
```

## Health Checks

Os containers têm health checks configurados no `compose.yaml`:

```bash
docker-compose exec mysql mysql -u root -p -e "SELECT 1"
# Ou via inspect
docker inspect check-docs-engenharia-mysql-1
```

## Troubleshooting Avançado

### Build customizado

Se precisar modificar Dockerfile:

```bash
docker-compose up -d --build app
```

### Usar outra imagem MySQL

No `compose.yaml`:

```yaml
mysql:
  image: postgres:16  # Trocar para PostgreSQL, por exemplo
```

Depois atualizar `.env`:

```
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
```

## Dicas

1. **Sempre usar `docker-compose exec`** ao invés de `docker exec` (melhor integração)
2. **Versione o `.env`** com valores padrão; `.env.local` para customizações
3. **Use `--wait`** ao aguardar serviços: `docker-compose up -d --wait`
4. **Logs em time real**: `docker-compose logs -f --tail=50 app`
5. **Reconstruir container**: `docker-compose up -d --build --no-cache app`

## Referências

- Docker Docs: https://docs.docker.com/
- Docker Compose: https://docs.docker.com/compose/
- Laravel & Docker: https://laravel.com/docs/container

---

**Última atualização**: 2026-04-01
