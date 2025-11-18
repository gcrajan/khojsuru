FROM php:8.3-apache

# This line installs the PostgreSQL driver (the fix!)
RUN docker-php-ext-install pdo pdo_pgsql

# Copy your code
COPY . /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html && a2enmod rewrite

EXPOSE 80
