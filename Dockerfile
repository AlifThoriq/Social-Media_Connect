# Gunakan PHP 8.3 dengan Apache
FROM php:8.3-apache

# Install ekstensi sistem yang dibutuhkan untuk Laravel & Postgres (Neon)
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    libpq-dev \
    curl

# Install Node.js & npm (Untuk compile Vite/Tailwind)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

# Aktifkan mod_rewrite Apache (Wajib untuk routing Laravel)
RUN a2enmod rewrite

# Install ekstensi PHP (PDO Postgres & Zip)
RUN docker-php-ext-install pdo pdo_pgsql zip

# Ubah arah root web langsung ke folder /public milik Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Salin seluruh kode aplikasi Anda ke dalam server
COPY . /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --optimize-autoloader --no-dev

# Build frontend (Tailwind/Alpine)
RUN npm install && npm run build

# Berikan izin akses folder agar Laravel bisa menyimpan cache & session
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache