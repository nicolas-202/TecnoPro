# TecnoPro soluciones

Este proyecto corresponde al proyecto final de desarrollo de software 1

## Requisitos Previos

- **PHP**: >= 8.2
- **Composer**: Última versión
- **Node.js**: >= 16.x
- **MySQL**: >= 5.7
- **Git**: Para clonar el repositorio
- Un servidor local (e.g., XAMPP, WAMP, Laravel Valet) o Docker

## Instalación

Sigue estos pasos para configurar y ejecutar el proyecto localmente:

1. **Clonar el repositorio**:
   ```bash
   git clone https://github.com/nicolas-202/TecnoPro.git
   cd TecnoPro


Instalar dependencias de PHP:
   ```bash
  composer install 
   ```

Instalar dependencias de Node.js:
```bash
  npm install 
   ```

Configurar el archivo .env:

Copia .env.example a .env:
```bash
  cp .env.example .env
   ```



Edita .env con las credenciales de tu base de datos:DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña




Generar la clave de la aplicación:
```bash
  php artisan key:generate
   ```

#NOTA 
Tienes que asegurarte de tener tu gestor de bd en funcionamiento y haber configurado bien la informacion en el archivo .env

Ejecutar migraciones y seeders:
```bash
  php artisan migrate --seed
   ```



Compilar assets (CSS/JS):
```bash
  npm run build
   ```

Iniciar el servidor de desarrollo:
```bash
  php artisan serve
   ```
Accede al proyecto en http://localhost:8000.

Uso

Regístrate en /register o inicia sesión en /login.
Para acceder a funciones administrativas (e.g., /configuracion), el usuario debe estar asociado a un empleado.
Si ejecutaste bien toda la parte de configuración del proyecto puedes loguearte con el email admin@tecnopro.com y contraseña admin123 
para entrar como un empleado.
