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
    wget \
    iproute2 \
    netcat-openbsd \
    cron \
    nano

# Instalação das extensões PHP necessárias
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets

# Instalação do Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Copiar o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar os arquivos da aplicação
COPY . /var/www

# Configuração de permissões
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap \
    && chown -R www-data:www-data /var/www/storage/logs \
    && chmod 777 -R /var/www

RUN mkdir -p /var/log && touch /var/log/xdebug.log && chmod 777 /var/log/xdebug.log

# Configuração de memória e Xdebug
RUN echo "memory_limit = 2G" >> /usr/local/etc/php/conf.d/20-memory-limit.ini

RUN echo "xdebug.mode=debug\n\
    xdebug.start_with_request=yes\n\
    xdebug.discover_client_host=1\n\
    xdebug.client_port=9000\n\
    xdebug.log=/var/log/xdebug.log\n\
    xdebug.max_nesting_level=256" > /usr/local/etc/php/conf.d/20-xdebug.ini

# Copiar o arquivo de configuração cron para o contêiner
COPY cronjob /etc/cron.d/schedule

# Configurar permissões adequadas para o cronjob
RUN chmod 0644 /etc/cron.d/schedule

# Aplicar as permissões do cronjob para ser reconhecido pelo cron
RUN crontab /etc/cron.d/schedule

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 9000
