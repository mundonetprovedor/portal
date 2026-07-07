#!/bin/bash
set -e

echo "=== Portal MUNDONET - Setup ==="

# Instalar Composer se nao existir
if [ ! -f "/usr/local/bin/composer" ]; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

# Instalar dependencias se vendor nao existir
if [ ! -d "vendor" ]; then
    echo " Instalando dependencias..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Gerar chave se necessario
if grep -q "GENERATE_KEY_ON_FIRST_RUN" .env 2>/dev/null; then
    echo " Gerando APP_KEY..."
    php artisan key:generate --force
fi

# Permissoes
chmod -R 777 storage bootstrap/cache public/uploads 2>/dev/null || true

# Aguardar banco
echo " Aguardando banco de dados..."
for i in $(seq 1 30); do
    if php -r "new PDO('mysql:host=${DB_HOST:-db};port=${DB_PORT:-3306}', '${DB_USERNAME:-mundonet}', '${DB_PASSWORD:-secret_password}');" 2>/dev/null; then
        echo " Banco conectado!"
        break
    fi
    sleep 2
done

# Migrations
php artisan migrate --force --no-interaction 2>/dev/null || true

# Seed settings se vazio
php artisan db:seed --class=SettingsSeeder --force --no-interaction 2>/dev/null || true

# Cache
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

echo "=== Setup Completo ==="

exec "$@"
