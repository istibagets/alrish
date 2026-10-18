FROM php:8.3-apache

# Install PDO MySQL driver
RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

# Enable .htaccess overrides in Apache
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy application source code
COPY . /var/www/html/

# Secure file permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html

EXPOSE 80
