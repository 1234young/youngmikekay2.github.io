FROM php:8.2-apache

# Install required extensions and libraries
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libssl-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli openssl

# Enable Apache modules (important for routing and .htaccess)
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy project files to server directory
COPY . /var/www/html/

# Fix permissions so Apache can read/write where needed
RUN chmod -R 755 /var/www/html

# Expose HTTP port
EXPOSE 80
