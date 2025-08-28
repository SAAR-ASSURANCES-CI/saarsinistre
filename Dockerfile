# Image de base
FROM php:8.2-apache

# Variables d'environnement
ENV DEBIAN_FRONTEND=noninteractive
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installation des dépendances système et extensions PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zip \
    unzip \
    nodejs \
    npm \
    supervisor \
    cron \
    # Outils pour Redis et WebSockets
    redis-tools \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configuration d'OPcache pour de meilleures performances
RUN echo 'opcache.memory_consumption=128' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.interned_strings_buffer=8' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.max_accelerated_files=4000' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.revalidate_freq=60' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.fast_shutdown=1' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.enable=1' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.enable_cli=1' >> /usr/local/etc/php/conf.d/opcache.ini

# Configuration PHP pour uploads et performances
RUN echo 'upload_max_filesize = 10M' >> /usr/local/etc/php/conf.d/upload.ini \
    && echo 'post_max_size = 10M' >> /usr/local/etc/php/conf.d/upload.ini \
    && echo 'memory_limit = 256M' >> /usr/local/etc/php/conf.d/memory.ini \
    && echo 'max_execution_time = 300' >> /usr/local/etc/php/conf.d/execution.ini

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Activation des modules Apache nécessaires
RUN a2enmod rewrite headers expires deflate

# Définition du répertoire de travail
WORKDIR /var/www/html

# Copie de la configuration Apache optimisée
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copie et installation des dépendances PHP (pour optimiser le cache Docker)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --no-autoloader

# Copie et installation des dépendances Node.js
COPY package.json package-lock.json ./
RUN npm ci --only=production

# Copie du code source (en excluant les fichiers via .dockerignore)
COPY . .

# Finalisation de l'installation Composer
RUN composer dump-autoload --optimize --classmap-authoritative

# Nettoyage du lien symbolique existant et création des répertoires
RUN rm -rf public/storage \
    && mkdir -p storage/app/public \
    && mkdir -p storage/logs \
    && mkdir -p storage/framework/{cache,sessions,views} \
    && mkdir -p bootstrap/cache

# Construction des assets front-end
RUN npm run build

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage \
    && chmod -R 755 bootstrap/cache \
    && chmod +x artisan

# Configuration de l'environnement
RUN if [ ! -f .env ]; then cp .env.docker.example .env; fi

# Génération de la clé d'application
RUN php artisan key:generate --force

# Création du lien symbolique pour le storage
RUN php artisan storage:link

# Configuration de Supervisor pour les processus en arrière-plan
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copie du script de démarrage
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Exposition des ports
EXPOSE 80 8080

# Commande de démarrage
CMD ["/usr/local/bin/start.sh"]