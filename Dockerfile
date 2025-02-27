FROM php:8.2-apache


# Install system dependencies and PHP extensions for Kafka
RUN apt-get update && apt-get install -y \
    librdkafka-dev \
    git \
    zip \
    unzip \
    libzip-dev \
    && pecl install rdkafka \
    && docker-php-ext-enable rdkafka \
    && docker-php-ext-install pdo_mysql zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


COPY . /var/www/html/
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
EXPOSE 80