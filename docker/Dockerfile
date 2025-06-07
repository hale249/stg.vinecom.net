# Dockerfile
FROM php:8.3-fpm

# Cài đặt các extension PHP cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libgmp-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd gmp

# Cài Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Thiết lập thư mục làm việc
WORKDIR /var/www

# Copy mã nguồn vào container
COPY . .

# Tạo thư mục nếu chưa tồn tại và thiết lập quyền
RUN mkdir -p /var/www/storage \
    && chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Expose cổng
EXPOSE 9000

CMD ["php-fpm"]
