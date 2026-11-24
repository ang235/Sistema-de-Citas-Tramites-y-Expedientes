#!/bin/bash
# ==============================================================================
# Script de Despliegue Automático para S_E_T_C en AWS EC2 (Ubuntu 24.04 / 22.04)
# Optimizado para instancias t2.micro / t3.micro (Capa Gratuita de AWS)
# ==============================================================================

set -e

echo "=========================================================="
echo "  Iniciando aprovisionamiento del servidor SETC en AWS EC2"
echo "=========================================================="

# 1. Configuración de Memoria SWAP de 2GB (Crucial para t2.micro de 1GB RAM)
if [ ! -f /swapfile ]; then
    echo ">> Configurando 2GB de memoria SWAP para prevenir saturación de RAM..."
    sudo fallocate -l 2G /swapfile || sudo dd if=/dev/zero of=/swapfile bs=1M count=2048
    sudo chmod 600 /swapfile
    sudo mkswap /swapfile
    sudo swapon /swapfile
    echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
    echo ">> SWAP configurada exitosamente."
else
    echo ">> SWAP ya existente. Omitiendo paso."
fi

# 2. Actualización de paquetes del sistema
echo ">> Actualizando repositorios del sistema..."
sudo apt-get update -y
sudo apt-get install -y ca-certificates curl gnupg lsb-release ufw git

# 3. Instalación de Docker y Docker Compose Plugin (Repositorio oficial)
if ! command -v docker &> /dev/null; then
    echo ">> Instalando Docker Engine oficial..."
    sudo install -m 0755 -d /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
    sudo chmod a+r /etc/apt/keyrings/docker.gpg

    echo \
      "deb [arch="$(dpkg --print-architecture)" signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
      "$(. /etc/os-release && echo "$VERSION_CODENAME")" stable" | \
      sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

    sudo apt-get update -y
    sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

    # Permitir al usuario ubuntu ejecutar docker sin sudo
    sudo usermod -aG docker $USER
    echo ">> Docker instalado correctamente."
else
    echo ">> Docker ya instalado. Omitiendo paso."
fi

# 4. Habilitar puertos en el firewall (UFW)
echo ">> Configurando cortafuegos del servidor..."
sudo ufw allow 22/tcp || true
sudo ufw allow 80/tcp || true
sudo ufw allow 443/tcp || true
sudo ufw allow 8080/tcp || true
echo "y" | sudo ufw enable || true

# 5. Detección de IP Pública de la instancia EC2
PUBLIC_IP=$(curl -s http://checkip.amazonaws.com || curl -s https://api.ipify.org || echo "localhost")
echo ">> IP Pública detectada: $PUBLIC_IP"

# 6. Preparación de variables de entorno para Producción
if [ ! -f .env.docker ]; then
    echo ">> Creando .env.docker para producción..."
    cat <<EOF > .env.docker
APP_NAME=SETC
APP_ENV=production
APP_DEBUG=false
APP_PORT=80
APP_UID=1000
APP_GID=1000
APP_KEY=\$(openssl rand -base64 32)
APP_URL=http://$PUBLIC_IP
APP_TIMEZONE=America/Mexico_City
APP_LOCALE=es
LOG_LEVEL=warning

MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=\${MAIL_HOST:-smtp.gmail.com}
MAIL_PORT=\${MAIL_PORT:-587}
MAIL_USERNAME=\${MAIL_USERNAME:-}
MAIL_PASSWORD=\${MAIL_PASSWORD:-}
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="\${MAIL_FROM_ADDRESS:-noreply@setc.gob.mx}"
MAIL_FROM_NAME="SETC Portal"

GEMINI_API_KEY=\${GEMINI_API_KEY:-}
EOF
else
    # Asegurar que el puerto en producción sea el puerto 80 (HTTP estándar)
    sed -i 's/APP_PORT=.*/APP_PORT=80/' .env.docker
    sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env.docker
    sed -i "s|APP_URL=.*|APP_URL=http://$PUBLIC_IP|" .env.docker
fi

# 7. Construcción y levantamiento de los contenedores
echo ">> Construyendo y levantando contenedores SETC..."
sudo docker compose down --remove-orphans || true
sudo docker compose build app
sudo docker compose up -d

# 8. Esperar a que el contenedor esté listo y ejecutar migraciones / seeders
echo ">> Esperando a que el servicio inicialice la base de datos..."
sleep 15

echo ">> Ejecutando migraciones y seeders de base de datos..."
sudo docker compose exec -T app php artisan migrate --force
sudo docker compose exec -T app php artisan db:seed --force
sudo docker compose exec -T app php artisan config:cache
sudo docker compose exec -T app php artisan route:cache
sudo docker compose exec -T app php artisan view:cache
sudo docker compose exec -T app php artisan storage:link || true

echo "=========================================================="
echo "  ¡DESPLIEGUE COMPLETADO CON ÉXITO EN AWS EC2!"
echo "=========================================================="
echo "Accede a la plataforma desde tu navegador en:"
echo "👉 http://$PUBLIC_IP"
echo ""
echo "Credenciales disponibles:"
echo "- Ciudadano:       sus@sus.com / password"
echo "- Revisor Técnico: jesus@tramites.gob.mx / password"
echo "- Supervisor:      jeff@tramites.gob.mx / password"
echo "- Administrador:   admin@tramites.gob.mx / password"
echo "=========================================================="
