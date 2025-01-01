FROM dunglas/frankenphp:php8.2-bookworm
 
ENV SERVER_NAME="http://skripsi.qorthony.my.id"

# Enable PHP production settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Set environment variables
ENV DEBIAN_FRONTEND=noninteractive

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
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Verifikasi instalasi
RUN node -v && npm -v
 
WORKDIR /app
 
COPY . .
 
RUN composer install \
  --ignore-platform-reqs \
  --optimize-autoloader \
  --prefer-dist \
  --no-interaction \
  --no-progress \
  --no-scripts

RUN npm install && npm run build