FROM php:8.2-fpm

# Install system dependencies, Nginx, Supervisor and PHP extensions
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    libpq-dev \
    git \
    unzip \
  && docker-php-ext-install pdo_pgsql \
  && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

# Copy only composer files first to leverage Docker layer cache
COPY composer.json composer.lock ./

# Install PHP dependencies (production-friendly by default)
ARG COMPOSER_FLAGS="--no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader"
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install $COMPOSER_FLAGS || (echo "Composer install failed" && cat /var/www/composer.json && exit 1)

# Now copy the rest of the application source
COPY . /var/www

# Nginx configuration
# Copy existing project Nginx config and adapt it for single-container (fastcgi to 127.0.0.1)
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
RUN sed -i 's#fastcgi_pass\s\+app:9000;#fastcgi_pass 127.0.0.1:9000;#' /etc/nginx/conf.d/default.conf \
    && sed -i 's#root /var/www/web;#root /var/www/web;#' /etc/nginx/conf.d/default.conf \
    && mkdir -p /var/log/nginx

# Supervisor configuration to run both php-fpm and nginx
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Make sure runtime directories exist (for Nginx and Yii runtime if needed)
RUN mkdir -p /run/php /var/run/nginx /var/cache/nginx /var/www/runtime \
    && chown -R www-data:www-data /var/www

# Ensure php-fpm listens on TCP 127.0.0.1:9000 for Nginx upstream
RUN sed -i 's#^listen = .*#listen = 127.0.0.1:9000#' /usr/local/etc/php-fpm.d/www.conf

# Startup helper script to set Nginx listen port from $PORT (App Platform) or default 8000
COPY docker/scripts/start-nginx.sh /usr/local/bin/start-nginx.sh
RUN chmod +x /usr/local/bin/start-nginx.sh

EXPOSE 8080

# Start supervisord (which starts php-fpm and nginx)
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
