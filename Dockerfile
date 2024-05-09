# Use an official PHP image with Apache as the base image
FROM php:8.1.25-apache

# Set the working directory in the container
WORKDIR /var/www/html

# Copy the composer.lock and composer.json files into the container
COPY composer.lock composer.json /var/www/html/

# Install PHP extensions and other dependencies
RUN apt-get update && \
    apt-get install -y \
        libpq-dev \
        libzip-dev \
        unzip \
        git \
        && \
    docker-php-ext-install \
        pdo \
        pdo_pgsql \
        zip \
        && \
    pecl install xdebug && docker-php-ext-enable xdebug && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Symfony CLI


# Copy the entire Symfony project into the container
COPY . /var/www/html/

# Expose port 80 to the outside world
EXPOSE 80
CMD ["apache2-foreground"]

# Start the Apache web server
