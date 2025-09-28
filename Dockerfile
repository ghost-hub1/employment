# Base: PHP with Apache
FROM php:8.2-apache

# Install system dependencies + Python
RUN apt-get update && apt-get install -y \
    python3 python3-pip \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html/

# Install Python dependencies (if requirements.txt exists)
RUN if [ -f requirements.txt ]; then pip3 install --no-cache-dir -r requirements.txt; fi

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose Apache port
EXPOSE 80

# Default command runs Apache in foreground
CMD ["apache2-foreground"]
