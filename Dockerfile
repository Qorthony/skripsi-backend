# Gunakan image node untuk build assets
# FROM node:22-alpine AS frontend

# WORKDIR /app

# COPY package*.json vite.config.js ./
# COPY resources/ resources/
# COPY public/ public/
# RUN npm install
# RUN npm run build

FROM dunglas/frankenphp:php8.3-bookworm

# Enable PHP production settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Set environment variables
ENV DEBIAN_FRONTEND=noninteractive
# ENV NODE_ENV=production

RUN install-php-extensions @composer \
    gd \
    pcntl \
    opcache \
    pdo \
    pdo_mysql

# Install dependencies nodejs
RUN apt-get update && apt-get install -y \
    curl \
    build-essential \
    netcat-openbsd \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Verifikasi instalasi
RUN node -v && npm -v
 
WORKDIR /app
 
COPY . .

# remove hot file in public
RUN rm -f public/hot

# Create necessary directories before setting permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

# Set proper permissions
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

COPY start-prod.sh /usr/local/bin/start-prod.sh
RUN chmod +x /usr/local/bin/start-prod.sh

# Copy hasil build Vite ke direktori Laravel public
# COPY --from=frontend /app/public/build /var/www/html/public/build
 
RUN composer install \
  --ignore-platform-reqs \
  --optimize-autoloader \
  --prefer-dist \
  --no-interaction \
  --no-progress \
  --no-scripts

RUN npm ci --audit false
RUN npm run build

# Default command
CMD ["/usr/local/bin/start-prod.sh"]