#!/usr/bin/env bash
set -euo pipefail

# Determine port: prioritize $PORT (used by DigitalOcean App Platform), else default to 8000
PORT_TO_USE="${PORT:-8000}"

# Patch Nginx config to listen on the chosen port
sed -ri "s#listen\s+[^;]+;#listen ${PORT_TO_USE};#" /etc/nginx/conf.d/default.conf

echo "Starting Nginx on port ${PORT_TO_USE}..."
exec /usr/sbin/nginx -g 'daemon off;'
