FROM php:8.4-cli

WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Cache Composer dependencies
COPY composer.json composer.lock ./

RUN composer install --no-scripts --no-autoloader --no-interaction

# Copy application source code
COPY . .

# Generate autoloader
RUN composer dump-autoload --optimize --no-interaction

# Make entrypoint script executable
RUN chmod +x /app/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/app/docker-entrypoint.sh"]

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]