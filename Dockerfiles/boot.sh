#!/bin/sh
set -e

cd /var/www/html || exit 0

if [ -n "$DB_HOST" ] && [ -n "$DB_PORT" ] && [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ] && command -v mysql >/dev/null 2>&1; then
    echo "Aguardando o banco em ${DB_HOST}:${DB_PORT}..."

    attempt=1
    while ! MYSQL_PWD="${DB_PASSWORD:-}" mysql \
        -h"$DB_HOST" \
        -P"$DB_PORT" \
        -u"$DB_USERNAME" \
        -e "SELECT 1" \
        "$DB_DATABASE" >/dev/null 2>&1; do
        if [ "$attempt" -ge 30 ]; then
            echo "Banco não ficou pronto a tempo."
            exit 1
        fi

        echo "Banco ainda indisponível, tentativa ${attempt}/30..."
        attempt=$((attempt + 1))
        sleep 2
    done
else
    echo "Variáveis do banco ausentes ou cliente MySQL indisponível, pulando espera."
fi

if command -v composer >/dev/null 2>&1; then
    if [ ! -d vendor ] || [ ! -f vendor/composer/installed.json ] || [ composer.lock -nt vendor/composer/installed.json ]; then
        echo "Executando composer install..."
        composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader
    else
        echo "Dependências já instaladas, pulando composer install."
    fi
else
    echo "Composer não encontrado, pulando composer install."
fi

if [ -f artisan ] && command -v php >/dev/null 2>&1; then
    echo "Arquivo artisan encontrado, executando migrations..."
    php artisan migrate --force || echo "Falha ao executar migrate, seguindo inicialização."
else
    echo "Artisan não encontrado, pulando migrations."
fi

if [ -x /usr/local/bin/docker-php-entrypoint ]; then
    exec docker-php-entrypoint "$@"
else
    exec "$@"
fi
