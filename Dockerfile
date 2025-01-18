FROM php:8.4-fpm

WORKDIR /var/www

# Instalação das dependências necessárias, incluindo o netcat-openbsd
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    iputils-ping \
    wget -y \
    iproute2 \
    netcat-openbsd  # Adiciona o netcat-openbsd

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www

RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap \
    && chown -R www-data:www-data /var/www/storage/logs \
    && chmod 777 -R /var/www

RUN mkdir -p /var/log && touch /var/log/xdebug.log && chmod 777 /var/log/xdebug.log

RUN echo "memory_limit = 2G" >> /usr/local/etc/php/conf.d/20-memory-limit.ini

RUN echo "xdebug.mode=debug\n\
    xdebug.start_with_request=yes\n\
    xdebug.discover_client_host=1\n\
    xdebug.client_port=9000\n\
    xdebug.log=/var/log/xdebug.log\n\
    xdebug.max_nesting_level=256" > /usr/local/etc/php/conf.d/20-xdebug.ini

ENTRYPOINT ["sh", "-c", "while ! nc -z seller_tray_elasticsearch 9200; do sleep 1; done; echo 'Elasticsearch is ready!'; exec php-fpm"]

EXPOSE 9000
