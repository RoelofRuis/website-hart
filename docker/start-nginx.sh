#!/usr/bin/env bash
set -euo pipefail

# Determine port: prioritize $PORT (used by DigitalOcean App Platform), else default to 8000
PORT_TO_USE="${PORT:-8000}"

# Ensure Yii writable directories exist and are owned by www-data (PHP-FPM user)
mkdir -p /var/www/web/assets /var/www/runtime
chown -R www-data:www-data /var/www/web/assets /var/www/runtime || true
chmod -R u+rwX,g+rwX /var/www/web/assets /var/www/runtime || true

# Patch Nginx config to listen on the chosen port
sed -ri "s#listen\s+[^;]+;#listen ${PORT_TO_USE};#" /etc/nginx/conf.d/default.conf

echo "Starting Nginx on port ${PORT_TO_USE}..."
exec /usr/sbin/nginx -g 'daemon off;'
