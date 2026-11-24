# Guía de Despliegue en AWS EC2 (Capa Gratuita / Free Tier)

Esta guía te permite desplegar el **Sistema de Expedientes de Trámites y Citas (SETC)** en una instancia virtual de Amazon Web Services (AWS) con costo **$0 USD** aprovechando la capa gratuita.

---

## Paso 1: Crear la Instancia en AWS Console

1. Inicia sesión en tu cuenta de AWS: [https://console.aws.amazon.com/ec2](https://console.aws.amazon.com/ec2).
2. Haz clic en el botón naranja **"Lanzar una instancia" (Launch instance)**.
3. Configura los siguientes parámetros:
   * **Nombre**: `SETC-Servidor-Nube`
   * **Imágenes de aplicaciones y SO (AMI)**: Selecciona **Ubuntu** $\rightarrow$ `Ubuntu Server 24.04 LTS (HVM), SSD Volume Type` (Verifica que tenga la etiqueta verde *Apto para la capa gratuita*).
   * **Tipo de instancia**: `t2.micro` (o `t3.micro` según tu región, 1 vCPU, 1 GB de memoria, *Apto para la capa gratuita*).
   * **Par de claves (inicio de sesión)**: 
     * Si no tienes uno, haz clic en *Crear nuevo par de claves*.
     * Nombre: `setc-key`. Tipo: `RSA`. Formato: `.pem` (o `.ppk` si usas PuTTY). Descárgalo y guárdalo.
   * **Configuración de red (Firewall / Security Group)**:
     * Marca las siguientes casillas:
       * ☑ **Permitir el tráfico de SSH desde**: *En cualquier lugar (0.0.0.0/0)* o *Mi IP*.
       * ☑ **Permitir el tráfico de HTTP desde Internet** (Puerto 80).
       * ☑ **Permitir el tráfico de HTTPS desde Internet** (Puerto 443).
   * **Configurar almacenamiento**: 
     * Cambia de 8 GiB a **20 GiB** o **30 GiB** `gp3` (AWS incluye hasta 30 GB de almacenamiento gratuito).
4. Haz clic en el botón **"Lanzar instancia"**. En 1 minuto tu servidor estará en estado *En ejecución*.

---

## Paso 2: Conectarte a tu Instancia

Tienes dos opciones muy sencillas:

### Opción A: Desde el navegador (Sin programas, la más fácil)
1. En la lista de instancias de AWS EC2, selecciona tu instancia y haz clic en **"Conectar" (Connect)**.
2. En la pestaña **"Conexión de la instancia EC2" (EC2 Instance Connect)**, haz clic en **"Conectar"**.
3. Se abrirá una terminal negra directamente en tu navegador web como usuario `ubuntu@ip-...`.

### Opción B: Desde tu terminal local (PowerShell o Git Bash)
```powershell
ssh -i "C:\ruta\a\tu\setc-key.pem" ubuntu@<IP-PUBLICA-DE-TU-EC2>
```

---

## Paso 3: Clonar el Proyecto y Ejecutar el Despliegue Automático

Una vez dentro de la terminal de tu servidor en AWS, ejecuta los siguientes comandos:

```bash
# 1. Clonar el repositorio del proyecto (o subir los archivos vía SCP / Git)
git clone <URL-DE-TU-REPOSITORIO-GIT> setc
cd setc

# 2. Dar permisos de ejecución al script de aprovisionamiento
chmod +x deploy/setup-ec2.sh

# 3. Ejecutar el script automatizado
./deploy/setup-ec2.sh
```

### ¿Qué hace el script automáticamente?
1. Configura **2 GB de memoria SWAP** en el disco para que la compilación de Docker y Composer no saturen la memoria RAM de 1 GB de la instancia `t2.micro`.
2. Instala **Docker** y **Docker Compose Plugin** oficiales.
3. Configura el cortafuegos `ufw` abriendo los puertos 80 (HTTP), 443 (HTTPS) y 22 (SSH).
4. Genera el archivo de entorno de producción `.env.docker` configurando la IP pública y mapeando el puerto al **puerto 80 estándar**.
5. Construye la imagen multi-etapa de Laravel 13 / PHP 8.4 y levanta los contenedores en segundo plano.
6. Ejecuta las migraciones, seeders (catálogos de trámites, roles y usuarios de prueba), optimiza la caché de rutas/configuración y enlaza el almacenamiento de archivos.

---

## Paso 4: ¡Acceder a tu Aplicación!

Abre cualquier navegador web e ingresa directamente la IP pública de tu instancia:
```
http://<TU-IP-PUBLICA-DE-AWS>
```

### Usuarios de prueba precargados:
* **Ciudadano**: `sus@sus.com` | Contraseña: `password`
* **Revisor Técnico**: `jesus@tramites.gob.mx` | Contraseña: `password`
* **Supervisor**: `jeff@tramites.gob.mx` | Contraseña: `password`
* **Administrador**: `admin@tramites.gob.mx` | Contraseña: `password`

