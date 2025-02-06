FROM php:8.2-fpm

# Update package list and install required system dependencies for the PHP environment.
RUN apt-get update && apt-get install -y wget git unzip lsof firefox-esr libicu-dev libzip-dev libonig-dev libxml2-dev

# Install necessary PHP extensions for the project (ZIP handling, PDO for MySQL).
RUN docker-php-ext-install intl pdo_mysql zip opcache

RUN apt-get clean

# Configure OPCache in development mode.
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Install Xdebug for debugging and enable the extension in the PHP configuration.
RUN pecl install xdebug && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/xdebug.ini

# Download and install GeckoDriver for E2E tests.
RUN wget -q -O /tmp/geckodriver.tar.gz https://github.com/mozilla/geckodriver/releases/download/v0.33.0/geckodriver-v0.33.0-linux64.tar.gz \
    && tar -xzf /tmp/geckodriver.tar.gz -C /usr/local/bin \
    && chmod +x /usr/local/bin/geckodriver

# Download and install Composer for managing PHP dependencies.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN chown -R www-data:www-data var/cache var/log

EXPOSE 9000

CMD ["php-fpm"]
