FROM php:8.4-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    composer \
    php-dom \
    php-ctype \
    php-xml \
    php-mbstring \
    php-tokenizer

# Set working directory
WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy application code
COPY . .

# Default command
CMD ["php", "app.php", "input.txt"]
