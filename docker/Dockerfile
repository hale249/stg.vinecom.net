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
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd gmp zip

# Cài đặt Composer trực tiếp thay vì dùng composer image
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

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
