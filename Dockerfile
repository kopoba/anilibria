FROM php:7.3.31-fpm-alpine3.14

USER root
WORKDIR /var/www/html

# Use the default development configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Set custom config
# COPY .docker/modules/backend/configs/php $PHP_INI_DIR/conf.d/

# Install required dependencies
RUN  \
    apk --update add \
        mc \
        git \
        nginx \
        libzip-dev \
        libxml2-dev \
        supervisor \
        mysql-client && \
    docker-php-ext-install -j$(nproc) \
        zip \
        xml \
        bcmath \
        pdo_mysql


RUN \
    apk add $PHPIZE_DEPS && \
    pecl install -o -f redis && \
    rm -rf /tmp/pear && \
    docker-php-ext-enable redis

# Copy source code
COPY . .

# Copy supervisor config
COPY .docker/configs/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# Copy nginx configurations
RUN mkdir -p /run/nginx
COPY .docker/configs/nginx/nginx.conf /etc/nginx/nginx.conf
COPY .docker/configs/nginx/locker.conf /etc/nginx/locker.conf
COPY .docker/configs/nginx/snippets /etc/nginx/snippets
COPY .docker/configs/nginx/conf.d /etc/nginx/conf.d

# Copy php-fpm configuration
#COPY .docker/modules/backend/configs/php-fpm/zz-custom-docker.conf /usr/local/etc/php-fpm.d/

# Copy dependencies to outer folder
#RUN mkdir -p /dependencies && cp -r vendor /dependencies

# Start supervisor
ENTRYPOINT ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
#ENTRYPOINT ["php", "-S", "0.0.0.0:8000"]