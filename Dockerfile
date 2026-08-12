FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-install pdo pdo_sqlite mbstring zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node.js (for building Tailwind/Vite assets)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

WORKDIR /app

# Copy application code
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies and build assets
RUN npm install && npm run build

# Make sure the sqlite database file exists
RUN mkdir -p database && touch database/database.sqlite

# Set permissions
RUN chmod -R 775 storage bootstrap/cache database

EXPOSE 10000

CMD php artisan migrate:fresh --seed --force && php artisan serve --host 0.0.0.0 --port $PORT