@echo off
chcp 65001 >nul
title SETC — Acceso en Red Local (IP Dinamica)
cls

echo ======================================================================
echo    SETC — SISTEMA DE EXPEDIENTES DE TRÁMITES Y CITAS
echo    Detección Dinámica de IP de Red
echo ======================================================================
echo.
echo Detectando la dirección IP de tu red actual (Wi-Fi / Ethernet)...

:: 1. Detectar la IP IPv4 activa con ruta por defecto (omitiendo WSL/Hyper-V/VirtualBox)
for /f "usebackq delims=" %%A in (`powershell -NoProfile -Command "$r = Get-NetRoute -DestinationPrefix '0.0.0.0/0' -ErrorAction SilentlyContinue | Sort-Object RouteMetric | Select-Object -First 1; if ($r) { (Get-NetIPAddress -InterfaceIndex $r.InterfaceIndex -AddressFamily IPv4 -ErrorAction SilentlyContinue | Select-Object -First 1).IPAddress } else { (Get-NetIPAddress -AddressFamily IPv4 | Where-Object { $_.InterfaceAlias -notlike '*Loopback*' -and $_.InterfaceAlias -notlike '*WSL*' -and $_.InterfaceAlias -notlike '*Virtual*' -and $_.IPAddress -notlike '169.254*' } | Select-Object -First 1).IPAddress }"`) do (
    set "LAN_IP=%%A"
)

if "%LAN_IP%"=="" (
    echo [!] No se pudo detectar una IP de red activa. Usando localhost.
    set "LAN_IP=127.0.0.1"
)

set "APP_PORT=8080"
set "ACCESS_URL=http://%LAN_IP%:%APP_PORT%"

:: 2. Guardar IP en storage\app\host_ip.txt
if not exist "storage\app" mkdir "storage\app"
echo %LAN_IP%> "storage\app\host_ip.txt"

:: 3. Actualizar APP_URL en .env y .env.docker
powershell -NoProfile -Command "$ip='%LAN_IP%'; foreach($f in @('.env', '.env.docker')) { if (Test-Path $f) { (Get-Content $f) -replace '^APP_URL=.*$', ('APP_URL=http://' + $ip + ':8080') | Set-Content $f } }"

:: 4. Sincronizar con el contenedor Docker si está activo
docker ps --format "{{.Names}}" 2>nul | findstr /i "setc_app" >nul
if %errorlevel% equ 0 (
    echo [✓] Sincronizando configuración con el contenedor Docker...
    docker cp "storage\app\host_ip.txt" "setc_app:/var/www/setc/storage/app/host_ip.txt" >nul 2>&1
    docker exec setc_app php artisan optimize:clear >nul 2>&1
) else (
    echo [i] El contenedor Docker no está en ejecución. Iniciando servicios...
    docker compose up -d
    timeout /t 3 >nul
    docker cp "storage\app\host_ip.txt" "setc_app:/var/www/setc/storage/app/host_ip.txt" >nul 2>&1
    docker exec setc_app php artisan optimize:clear >nul 2>&1
)

echo.
echo ======================================================================
echo  ✓ Red actual detectada: %LAN_IP%
echo  ✓ URL Oficial de Acceso: %ACCESS_URL%
echo ======================================================================
echo.
echo  📱 PARA ACCEDER DESDE TU CELULAR O CUALQUIER OTRA COMPUTADORA:
echo     1. Asegúrate de que el dispositivo esté conectado a la MISMA RED WI-FI.
echo     2. Abre el navegador (Chrome, Safari, etc.) e ingresa:
echo        👉  %ACCESS_URL%
echo.
echo  📄 Los códigos QR generados en pases de citas y resoluciones
echo     usarán esta dirección IP para que cualquier teléfono pueda escanearlos.
echo ======================================================================
echo.
echo Abriendo en tu navegador predeterminado...
start %ACCESS_URL%

timeout /t 5 >nul

