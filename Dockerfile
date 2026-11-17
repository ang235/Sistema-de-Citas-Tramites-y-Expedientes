# ============================================================
#  Etapa 1: Dependencias de Node (assets frontend)
# ============================================================
FROM node:22-alpine AS node_builder

WORKDIR /app

# Copiar solo los archivos necesarios para instalar dependencias
COPY package.json package-lock.json* ./
# Usa ci si existe lockfile, install si no (primera vez)
RUN if [ -f package-lock.json ]; then npm ci --frozen-lockfile; else npm install; fi

# Copiar fuentes y compilar assets
COPY vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

RUN npm run build


# ============================================================
#  Etapa 2: Dependencias PHP (Composer)
# ============================================================
FROM composer:2.8 AS composer_builder

WORKDIR /app

# Copiar manifesto primero para aprovechar caché de capas
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# Copiar el resto del código fuente
COPY . .

# Ejecutar scripts post-install manualmente (sin root)
RUN composer dump-autoload --optimize --no-dev


# ============================================================
#  Etapa 3: Imagen final de producción
# ============================================================
FROM php:8.4-fpm-alpine AS app

# ---------- dependencias del sistema ----------
RUN apk add --no-cache \
        nginx \
        supervisor \
        sqlite \
        sqlite-dev \
        curl \
        libpng \
        libpng-dev \
        libjpeg-turbo \
        libjpeg-turbo-dev \
        libwebp \
        libwebp-dev \
        freetype \
        freetype-dev \
        icu-libs \
        icu-dev \
        oniguruma-dev \
        zip \
        unzip \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        pdo \
        pdo_sqlite \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl \
    && apk del \
        libpng-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
        freetype-dev \
        icu-dev \
        oniguruma-dev \
        sqlite-dev \
    && rm -rf /var/cache/apk/*

# ---------- usuario sin privilegios ----------
# UID/GID 1000 — puede sobreescribirse en build con --build-arg
ARG APP_UID=1000
ARG APP_GID=1000

RUN addgroup -g "${APP_GID}" -S setc \
    && adduser -u "${APP_UID}" -S -G setc -h /home/setc -s /bin/sh setc

# ---------- directorio de la aplicación ----------
WORKDIR /var/www/setc

# Copiar código compilado desde etapas anteriores
COPY --from=composer_builder --chown=setc:setc /app .
COPY --from=node_builder     --chown=setc:setc /app/public/build ./public/build

# Respaldar migraciones de la aplicación para sincronizarlas con volúmenes montados
RUN cp -r /var/www/setc/database/migrations /var/www/setc/database_defaults_migrations

# ---------- permisos de storage y bootstrap/cache ----------
RUN mkdir -p storage/framework/{sessions,views,cache} \
             storage/logs \
             bootstrap/cache \
    && chown -R setc:setc storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ---------- configuración PHP ----------
COPY docker/php/php.ini /usr/local/etc/php/conf.d/setc.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# ---------- configuración PHP-FPM (sin root) ----------
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# ---------- configuración Nginx ----------
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# ---------- configuración Supervisor ----------
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Nginx: carpetas de logs/pids accesibles por el usuario setc
RUN mkdir -p /var/log/nginx /var/lib/nginx/tmp /run/nginx \
    && chown -R setc:setc /var/log/nginx /var/lib/nginx /run/nginx \
    && chown -R setc:setc /var/log/supervisor 2>/dev/null || true \
    && touch /run/supervisord.pid && chown setc:setc /run/supervisord.pid

# Eliminar usuario www-data de php-fpm default pool
RUN sed -i 's/user = www-data/user = setc/g; s/group = www-data/group = setc/g' \
        /usr/local/etc/php-fpm.d/www.conf 2>/dev/null || true

EXPOSE 8080

USER setc

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# El COPY anterior no preserva permisos, así que necesitamos root momentáneamente
# para chmod. Lo hacemos como root antes de cambiar al usuario final.
USER root
RUN chmod +x /usr/local/bin/entrypoint.sh
USER setc

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

