@echo off
setlocal

echo ==================================================
echo ==      SCRIPT DE MIGRACION DE BASE DE DATOS    ==
echo ==================================================
echo.

echo Verificando requerimientos...

:: 1. Verificar si PHP está instalado y en el PATH
php -v >nul 2>nul
if %errorlevel% neq 0 (
    echo Error: PHP no esta instalado o no esta agregado al PATH del sistema.
    echo Por favor, instala PHP y asegurate de que este disponible en la linea de comandos.
    pause
    exit /b 1
)
echo  - PHP........................... OK

:: 2. Verificar si Composer está instalado
composer --version >nul 2>nul
if %errorlevel% neq 0 (
    echo Error: Composer no esta instalado o no esta en el PATH.
    echo Por favor, instala Composer desde getcomposer.org.
    pause
    exit /b 1
)
echo  - Composer...................... OK

:: 3. Verificar si existe la carpeta 'vendor' (dependencias de Composer)
if not exist vendor (
    echo La carpeta 'vendor' no se encuentra. Instalando dependencias con Composer...
    composer install --no-interaction --prefer-dist --optimize-autoloader
    if %errorlevel% neq 0 (
        echo Error: Fallo la instalacion de las dependencias con Composer.
        pause
        exit /b 1
    )
)
echo  - Dependencias (vendor)......... OK

:: 4. Verificar si el archivo .env existe, si no, copiarlo de .env.example
if not exist .env (
    echo El archivo .env no existe. Copiando desde .env.example...
    copy .env.example .env >nul
    echo Generando la clave de la aplicacion (APP_KEY)...
    php artisan key:generate
)
echo  - Archivo de entorno (.env)..... OK
echo.

echo Requerimientos verificados correctamente.
echo.
echo --------------------------------------------------
echo Elige una opcion para la migracion:
echo --------------------------------------------------
echo.
echo  [1] Ejecutar migraciones pendientes (php artisan migrate)
echo      Aplica las nuevas migraciones sin borrar datos existentes.
echo.
echo  [2] Recrear la base de datos (php artisan migrate:fresh --seed)
echo      AVISO: Se borraran TODOS los datos y se ejecutaran los seeders.
echo.
echo  [3] Cancelar
echo.

choice /C 123 /N /M "Introduce tu opcion (1, 2 o 3):"

if errorlevel 3 (
    echo Operacion cancelada.
    goto:eof
)

if errorlevel 2 (
    echo.
    echo Has elegido RECREAR la base de datos.
    echo Ejecutando 'php artisan migrate:fresh --seed'...
    echo.
    php artisan migrate:fresh --seed
    goto:end
)

if errorlevel 1 (
    echo.
    echo Has elegido ejecutar migraciones pendientes.
    echo Ejecutando 'php artisan migrate'...
    echo.
    php artisan migrate
    goto:end
)

:end
echo.
echo ==================================================
echo ==         PROCESO DE MIGRACION FINALIZADO        ==
echo ==================================================
echo.
pause
endlocal