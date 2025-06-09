Aquí tienes el `README.md` con el comando `php artisan db:seed` agregado a la sección de "Comandos Comunes de Laravel Artisan".

-----

# Proyecto Laravel con Docker Compose

Este repositorio contiene un proyecto Laravel configurado para ser ejecutado y desarrollado utilizando **Docker Compose**. La estructura permite levantar un entorno de desarrollo completo (servidor web Apache, PHP, base de datos MariaDB) de manera aislada y reproducible.

-----

## 🧰 Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

  * **Docker Desktop** (incluye Docker Engine y Docker Compose)
      * [Descargar Docker Desktop](https://www.docker.com/products/docker-desktop/)

-----

## 🚀 Cómo Levantar el Proyecto desde Cero

Sigue estos pasos para inicializar y poner en marcha tu proyecto Laravel dentro de Docker.

### 1\. Estructura del Proyecto

Asegúrate de que tu directorio raíz del proyecto tenga la siguiente estructura. La carpeta `src/` estará vacía al principio.

```
.
├── compose.yml
├── Dockerfile
├── docker-entrypoint.sh
├── apache-site.conf
├── php-config/
│   └── php.ini
└── src/
```

Si la carpeta `src/` no existe, créala:

```bash
mkdir src
```

### 2\. Levantar los Servicios de Docker Compose

Este comando construirá las imágenes (si es necesario), creará los contenedores y los iniciará en segundo plano.

```bash
docker compose up -d
```

### 3\. Instalar Laravel dentro del Contenedor PHP

Una vez que los servicios estén en ejecución, necesitas instalar Laravel en el volumen compartido de `src/` dentro del contenedor `php`.

Primero, accede al contenedor de PHP:

```bash
docker compose exec -it php bash
```

Una vez dentro del contenedor (tu terminal cambiará de prompt), navega al directorio del proyecto e instala Laravel:

```bash
cd /var/www/html
composer create-project laravel/laravel .
```

> ⚠️ **Nota de Permisos:**
> Si encuentras errores de permisos durante la instalación de Composer, puedes ajustar el propietario de los archivos dentro del contenedor con el siguiente comando **dentro del contenedor**:
>
> ```bash
> chown -R www-data:www-data .
> ```
>
> Luego, intenta `composer create-project` de nuevo si es necesario.

### 4\. Configurar la Conexión a la Base de Datos y Sesiones

Edita el archivo de configuración de entorno de Laravel, `.env`. Este archivo se encuentra en `./src/.env` (en tu máquina host) o en `/var/www/html/.env` (dentro del contenedor).

**Asegúrate de que las credenciales de la base de datos en tu `.env` coincidan exactamente con las definidas en tu `compose.yml` para el servicio `db`:**

**En tu `compose.yml`, tienes:**

```yaml
db:
  image: mariadb:10.6
  # ...
  environment:
    MARIADB_ROOT_PASSWORD: root
    MYSQL_DATABASE: mydatabase
    MYSQL_USER: user
    MYSQL_PASSWORD: userpassword
  # ...
```

**Por lo tanto, tu `./src/.env` debe contener:**

```dotenv
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=mydatabase
DB_USERNAME=user
DB_PASSWORD=userpassword

# --- Configuración de Sesiones, Colas y Caché para evitar tablas adicionales ---
# Para almacenar sesiones en archivos en lugar de la base de datos
SESSION_DRIVER=file

# Para ejecutar trabajos de cola inmediatamente (sincrónicamente) en lugar de una tabla 'jobs'
QUEUE_CONNECTION=sync

# Para almacenar la caché en archivos en lugar de una tabla 'cache'
CACHE_STORE=file
```

### 5\. Ejecutar las Migraciones de la Base de Datos

Con la configuración de la base de datos lista, ejecuta las migraciones de Laravel para crear las tablas en tu base de datos MariaDB. Asegúrate de estar **dentro del contenedor `php`**.

```bash
php artisan migrate
```

### 6\. Crear un Modelo y Migración para "Autos"

Puedes generar automáticamente un modelo y su migración asociada para una entidad "Auto":

Aún dentro del contenedor `php`:

```bash
php artisan make:model Auto -m
```

Luego, edita el archivo de migración recién generado. Lo encontrarás en `database/migrations/xxxx_create_autos_table.php` (donde `xxxx` es una marca de tiempo). Modifica la función `up()` para que tenga esta estructura:

```php
// database/migrations/xxxx_create_autos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('autos', function (Blueprint $table) {
            $table->id();
            $table->string('marca');
            $table->string('modelo');
            $table->integer('anio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autos');
    }
};
```

Después de guardar los cambios en la migración, vuelve a ejecutar las migraciones para aplicar los cambios a la base de datos:

```bash
php artisan migrate
```

### 7\. Crear una Ruta Básica para Ver los Autos

Edita el archivo de rutas `routes/web.php` para que muestre todos los autos al acceder a la ruta principal.

```php
// routes/web.php

use App\Models\Auto;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auto::all();
});
```

Ahora, puedes abrir tu navegador y visitar:

[http://localhost:8080](https://www.google.com/search?q=http://localhost:8080)

Inicialmente, verás un arreglo JSON vacío (`[]`) ya que no hay autos en la base de datos.

### 🧪 Bonus: Insertar un Auto para Probar

Para insertar un auto y verificar que todo funciona, puedes usar Laravel Tinker. Accede a Tinker desde el contenedor `php`:

```bash
php artisan tinker
```

Dentro de Tinker, ejecuta el siguiente comando:

```php
\App\Models\Auto::create(['marca' => 'Ford', 'modelo' => 'Focus', 'anio' => 2018]);
```

Deberías ver una salida que confirma la creación del auto. Ahora, al refrescar [http://localhost:8080](https://www.google.com/search?q=http://localhost:8080) en tu navegador, deberías ver el auto que acabas de insertar en formato JSON.

-----

## 🛠️ Comandos Útiles de Docker Compose y Laravel

Aquí hay algunos comandos comunes para gestionar tu entorno Docker y tu aplicación Laravel, ejecutados **desde la raíz de tu proyecto en tu máquina local** (a menos que se especifique lo contrario).

  * **Levantar servicios en segundo plano y reconstruir imágenes (útil después de cambios en `Dockerfile` o `docker-entrypoint.sh`):**
    ```bash
    docker compose up -d --build
    ```
  * **Levantar servicios en segundo plano (sin reconstruir imágenes):**
    ```bash
    docker compose up -d
    ```
  * **Detener y eliminar contenedores, redes y volúmenes (⚠️ ¡cuidado, esto borra los datos de la DB si no tienes volúmenes persistentes):**
    ```bash
    docker compose down -v # -v para eliminar los volúmenes, incluyendo los datos de la DB
    ```
  * **Detener servicios:**
    ```bash
    docker compose stop
    ```
  * **Reiniciar servicios:**
    ```bash
    docker compose restart
    ```
  * **Ver el estado de los servicios:**
    ```bash
    docker compose ps
    ```
  * **Ver los logs de un servicio (ej. `php`):**
    ```bash
    docker compose logs php
    ```
  * **Acceder a la terminal de un contenedor (ej. `php`):**
    ```bash
    docker compose exec php bash
    ```
    (o `sh` si `bash` no está disponible)

### Comandos Comunes de Laravel Artisan (dentro del contenedor PHP)

Una vez que estés **dentro del contenedor `php`** (usando `docker compose exec php bash`), puedes ejecutar estos comandos directamente:

  * **Ejecutar todas las migraciones pendientes:**
    ```bash
    php artisan migrate
    ```
  * **Restablecer todas las migraciones y volver a ejecutarlas (opcionalmente con seeders). Ideal para desarrollo:**
    ```bash
    php artisan migrate:fresh --seed
    ```
  * **Deshacer la última tanda de migraciones:**
    ```bash
    php artisan migrate:rollback
    ```
  * **Cargar los datos de prueba definidos en tus seeders:**
    ```bash
    php artisan db:seed
    ```
  * **Limpiar toda la caché de Laravel (configuración, rutas, vistas, aplicación):**
    ```bash
    php artisan optimize:clear
    ```
  * **Generar una nueva clave de aplicación (crucial para nuevos proyectos o si se pierde):**
    ```bash
    php artisan key:generate
    ```
  * **Crear un enlace simbólico para el almacenamiento público (si tu aplicación usa `storage/app/public`):**
    ```bash
    php artisan storage:link
    ```

### Comandos Comunes de Composer (dentro del contenedor PHP)

Una vez que estés **dentro del contenedor `php`**, puedes ejecutar estos comandos directamente:

  * **Actualizar el autoloader de Composer (útil después de añadir clases o modelos):**
    ```bash
    composer dump-autoload
    ```
  * **Instalar dependencias de Composer:**
    ```bash
    composer install
    ```
  * **Actualizar dependencias de Composer:**
    ```bash
    composer update
    ```



---

## 🖥️ Acceso a phpMyAdmin

Una vez que tus servicios Docker estén en ejecución (y hayas agregado el servicio `phpmyadmin` a tu `compose.yml`), puedes acceder a phpMyAdmin para gestionar tu base de datos MariaDB:

* **URL:** [http://localhost:8081](http://localhost:8081)
* **Usuario:** `root`
* **Contraseña:** `root`

---