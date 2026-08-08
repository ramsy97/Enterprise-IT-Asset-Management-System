#!/usr/bin/env bash
set -euo pipefail

# Render injects PORT (defaults to 10000 locally)
PORT="${PORT:-10000}"

# Point Apache at the Laravel public folder on Render's PORT.
# Free web services cannot bind to reserved ports; $PORT is always provided.
cat > /etc/apache2/ports.conf <<EOF
Listen ${PORT}
EOF

cat > /etc/apache2/sites-available/000-default.conf <<EOF
<VirtualHost *:${PORT}>
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Generate APP_KEY on first boot when Render has not provided one.
php artisan key:generate --no-interaction 2>/dev/null || true

# Apply migrations (idempotent).
php artisan migrate --force

# Seed roles/permissions/admin/demo data only on a fresh database.
if [ "$(php artisan tinker --execute="echo App\\Models\\User::count();")" = "0" ]; then
    php artisan db:seed --force
fi

# Clear stale caches between deploys.
php artisan optimize:clear

exec apache2-foreground
