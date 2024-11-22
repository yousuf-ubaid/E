FROM php:8.3-apache

RUN apt-get update && \
    apt-get install -y vim unzip curl openssl nano

# Installing Dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    openssl \
    nano \
    vim \
    wget \
    build-essential \
    zlib1g-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    libxml2-dev \
    libonig-dev \
    libgmp-dev \
    libldap2-dev \
    libpng-dev \
    && docker-php-ext-install mysqli \
    gmp \
    ldap \
    curl \
    intl \
    mbstring \
    gd \
    bcmath \
    xml \
    zip

RUN curl -sS https://getcomposer.org/download/2.8.1/composer.phar -o composer.phar && \
    chmod +x composer.phar && \
    mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html

COPY composer.* .

RUN composer install

COPY . .

RUN chown -R www-data:www-data /var/www/html

# Copy conf file
COPY ./docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/php.ini $PHP_INI_DIR/php.ini


# Enable apache modules
RUN a2enmod rewrite

#copying
COPY ./docker/bootstrap.sh /usr/local/bin/bootstrap.sh
RUN chmod +x /usr/local/bin/bootstrap.sh
RUN sed -i '$ i\/usr/local/bin/bootstrap.sh' /usr/local/bin/docker-php-entrypoint

EXPOSE 80