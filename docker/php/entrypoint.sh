#!/bin/sh
set -eu

APP_PORT="${APP_PORT:-8000}"

resolve_runtime_id() {
  flag="$1"
  configured_id="${2:-}"

  if [ -n "${configured_id}" ]; then
    printf '%s' "${configured_id}"
    return
  fi

  detected_id="$(stat -c "${flag}" /var/www/html 2>/dev/null || printf '33')"

  if [ "${detected_id}" = "0" ]; then
    printf '33'
    return
  fi

  printf '%s' "${detected_id}"
}

runtime_uid() {
  resolve_runtime_id '%u' "${APP_UID:-}"
}

runtime_gid() {
  resolve_runtime_id '%g' "${APP_GID:-}"
}

render_apache_config() {
  sed "s/__APACHE_PORT__/${APP_PORT}/g" \
    /usr/local/share/apache2/ports.conf.template \
    > /etc/apache2/ports.conf

  sed "s/__APACHE_PORT__/${APP_PORT}/g" \
    /usr/local/share/apache2/000-default.conf.template \
    > /etc/apache2/sites-available/000-default.conf
}

configure_apache_runtime_user() {
  app_uid="$(runtime_uid)"
  app_gid="$(runtime_gid)"

  current_uid="$(id -u www-data)"
  current_gid="$(id -g www-data)"

  if [ "${current_gid}" != "${app_gid}" ]; then
    groupmod -o -g "${app_gid}" www-data
  fi

  if [ "${current_uid}" != "${app_uid}" ]; then
    usermod -o -u "${app_uid}" -g "${app_gid}" www-data
  fi
}

ensure_laravel_permissions() {
  app_uid="$(runtime_uid)"
  app_gid="$(runtime_gid)"

  mkdir -p \
    /var/www/html/bootstrap/cache \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/testing \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs
  touch /var/www/html/storage/logs/laravel.log

  chmod 755 /var/www/html /var/www/html/public

  if chown -R "${app_uid}:${app_gid}" /var/www/html/bootstrap/cache /var/www/html/storage 2>/dev/null; then
    find /var/www/html/bootstrap/cache /var/www/html/storage -type d -exec chmod 2775 {} +
    find /var/www/html/bootstrap/cache /var/www/html/storage -type f -exec chmod 664 {} +
    return
  fi

  chmod -R a+rwX /var/www/html/bootstrap/cache /var/www/html/storage
}

install_php_dependencies() {
  if [ ! -f /var/www/html/vendor/autoload.php ] \
    || [ /var/www/html/composer.json -nt /var/www/html/vendor/autoload.php ] \
    || [ /var/www/html/composer.lock -nt /var/www/html/vendor/autoload.php ]; then
    composer install --working-dir=/var/www/html --no-interaction --prefer-dist
  fi
}

bootstrap_laravel() {
  if [ ! -f /var/www/html/.env ] && [ -f /var/www/html/.env.example ]; then
    cp /var/www/html/.env.example /var/www/html/.env
  fi

  if ! grep -q '^APP_KEY=base64:' /var/www/html/.env 2>/dev/null; then
    php /var/www/html/artisan key:generate --force
  fi
}

render_apache_config
configure_apache_runtime_user
ensure_laravel_permissions
install_php_dependencies
bootstrap_laravel

exec docker-php-entrypoint "$@"
