FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        xml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Install PHP dependencies
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# Copy application
COPY . .

# Install Node.js 22
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && node --version \
    && npm --version

# Install frontend dependencies and build assets
RUN rm -f public/hot \
    && npm ci \
    && npm run build \
    && mkdir -p /opt/ngdrs/public-build \
    && cp -a public/build/. /opt/ngdrs/public-build/ \
    && rm -f public/hot \
    && rm -rf node_modules

COPY docker/entrypoint.sh /usr/local/bin/ngdrs-entrypoint
RUN chmod +x /usr/local/bin/ngdrs-entrypoint

# Laravel permissions
RUN chown -R www-data:www-data \
    /var/www/storage \
    /var/www/bootstrap/cache

# Run Laravel package discovery after application files are present
RUN composer dump-autoload --optimize

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/ngdrs-entrypoint"]
CMD ["php-fpm"]
