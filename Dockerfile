FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy files
COPY . /var/www/html
WORKDIR /var/www/html

# Run build script
RUN chmod +x render-build.sh
RUN ./render-build.sh

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage