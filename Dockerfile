FROM dunglas/frankenphp:php8.2-alpine

RUN apk add --no-cache \
    zip \
    libzip-dev \
    freetype \
    libjpeg-turbo \
    libpng \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    nodejs \
    npm

RUN install-php-extensions \
    pcntl \
	pdo_mysql \
	gd \
	intl \
    imap \
    bcmath \
    # redis \
    curl \
	ctype \
	dom \
    exif \
	fileinfo \
	filter \
    hash \
    iconv \
    json \
    mbstring \
    mysqli \
    mysqlnd \
	tokenizer \
	openssl \
    pcre \
	pdo \
	session \
    xml \
    libxml \
    zlib \
	zip

	
COPY . /app

WORKDIR /app

RUN npm install

COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 8000 5173

# Default command
CMD ["/usr/local/bin/start.sh"]

# ENTRYPOINT ["php", "artisan", "octane:frankenphp"]