#!/bin/bash
set -e

echo "=== Portal MUNDONET - Iniciando ==="

# Instalar dependencias PHP (apenas se vendor nao existir)
if [ ! -d "vendor" ]; then
    echo " Instalando dependencias Composer..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Gerar chave se nao existir
if ! grep -q "base64:" .env 2>/dev/null || grep -q "GENERATE_KEY_ON_FIRST_RUN" .env 2>/dev/null; then
    echo " Gerando APP_KEY..."
    php artisan key:generate --force
fi

# Criar diretorios de storage
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p bootstrap/cache
mkdir -p public/uploads/logo

# Permissoes
chmod -R 777 storage
chmod -R 777 bootstrap/cache
chmod -R 777 public/uploads

# Aguardar banco de dados
echo " Aguardando banco de dados..."
until php -r "new PDO('mysql:host=db;port=3306', '${DB_USERNAME:-mundonet}', '${DB_PASSWORD:-secret_password}');" 2>/dev/null; do
    sleep 2
done
echo " Banco de dados conectado!"

# Rodar migrations
echo " Rodando migrations..."
php artisan migrate --force --no-interaction

# Rodar seeders (apenas se tabela settings estiver vazia)
SETTINGS_COUNT=$(php -t /dev/null -r "
try {
    \$pdo = new PDO('mysql:host=db;port=3306;dbname=${DB_DATABASE:-portal_mundonet}', '${DB_USERNAME:-mundonet}', '${DB_PASSWORD:-secret_password}');
    echo (int) \$pdo->query('SELECT COUNT(*) FROM settings')->fetchColumn();
} catch (Exception \$e) {
    echo '0';
}
" 2>/dev/null)

if [ "$SETTINGS_COUNT" = "0" ]; then
    echo " Rodando seeders..."
    php artisan db:seed --force --no-interaction
fi

# Limpar cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear

echo "=== Portal MUNDONET Pronto! ==="

# Manter container rodando
tail -f /dev/null
