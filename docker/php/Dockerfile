FROM php:8.2-fpm

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копируем файлы зависимостей внутрь образа
COPY ./app/composer.json ./

# Устанавливаем зависимости без запуска скриптов и автозагрузки (пока нет всего кода)
RUN composer install --no-interaction --no-scripts --no-autoloader

# Копируем остальной код (если он есть на этапе сборки)
COPY ./app .

# Генерируем финальный автозагрузчик
RUN composer dump-autoload --optimize