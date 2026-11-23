#!/bin/sh
# ============================================================
#  Entrypoint — S_E_T_C
#  Se ejecuta como usuario setc (sin root)
# ============================================================
set -e

echo "[entrypoint] Iniciando S_E_T_C Laravel App..."
echo "[entrypoint] Usuario: $(id)"
echo "[entrypoint] Hora:    $(date)"

# ------------------------------------------------------------------
# 1. Crear estructura de directorios necesaria
#    (el volumen puede estar vacío en el primer arranque)
# ------------------------------------------------------------------
echo "[entrypoint] Preparando directorios de storage..."
mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache/data \
    storage/logs \
    bootstrap/cache

chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# ------------------------------------------------------------------
# 2. Verificar que existe la base de datos SQLite y sincronizar migraciones
# ------------------------------------------------------------------
DB_PATH="${DB_DATABASE:-/var/www/setc/database/database.sqlite}"

if [ ! -f "$DB_PATH" ]; then
    echo "[entrypoint] Creando base de datos SQLite en $DB_PATH ..."
    touch "$DB_PATH"
fi

if [ -d "/var/www/setc/database_defaults_migrations" ]; then
    echo "[entrypoint] Sincronizando migraciones de la aplicación..."
    mkdir -p /var/www/setc/database/migrations
    cp -r /var/www/setc/database_defaults_migrations/* /var/www/setc/database/migrations/ 2>/dev/null || true
fi

# ------------------------------------------------------------------
# 3. Limpiar caché de arranque (errores no fatales con || true)
# ------------------------------------------------------------------
echo "[entrypoint] Limpiando cachés..."
php artisan optimize:clear --no-interaction 2>&1 || true

# ------------------------------------------------------------------
# 4. Ejecutar migraciones
# ------------------------------------------------------------------
echo "[entrypoint] Ejecutando migraciones..."
php artisan migrate --force --no-interaction 2>&1

# ------------------------------------------------------------------
# 5. Optimizar para producción (errores no fatales con || true)
# ------------------------------------------------------------------
echo "[entrypoint] Optimizando..."
php artisan config:cache  --no-interaction 2>&1 || true
php artisan route:cache   --no-interaction 2>&1 || true
php artisan view:cache    --no-interaction 2>&1 || true
php artisan event:cache   --no-interaction 2>&1 || true

echo "[entrypoint] Listo. Iniciando Supervisor (Nginx + PHP-FPM)..."

# ------------------------------------------------------------------
# 6. Arrancar Supervisor como proceso principal
# ------------------------------------------------------------------
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
