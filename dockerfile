# Use official PHP with Apache
FROM php:8.3-apache

# Install MySQL extension (required for mysqli/PDO)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Optional but if you also need gd, intl, etc. add them here
# RUN docker-php-ext-install gd intl zip

# Copy your code into Apache document root
COPY . /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite   # only if you use .htaccess

# Expose port 80 (Render will map it to 443 automatically)
EXPOSE 80
