@echo off
setlocal
cd /d "%~dp0"

echo Verificando requerimientos...

:: 1. Verificar si PHP está instalado y en el PATH
php -v >nul 2>nul
if %errorlevel% neq 0 (
    echo Error: PHP no esta instalado o no esta agregado al PATH del sistema.
    echo Por favor, instala PHP y asegurate de que este disponible en la linea de comandos.
    echo.
    pause
    exit /b 1
)

:: 2. Verificar si Composer está instalado
composer --version >nul 2>nul
if %errorlevel% neq 0 (
    echo Error: Composer no esta instalado o no esta en el PATH.
    echo Por favor, instala Composer desde getcomposer.org.
    echo.
    pause
    exit /b 1
)

:: 3. Verificar si existe la carpeta 'vendor' (dependencias de Composer)
if not exist vendor (
    echo La carpeta 'vendor' no se encuentra. Instalando dependencias con Composer...
    composer install --no-interaction --prefer-dist --optimize-autoloader
    if %errorlevel% neq 0 (
        echo Error: Fallo la instalacion de las dependencias con Composer.
        echo.
        pause
        exit /b 1
    )
)

:: 4. Verificar si el archivo .env existe, si no, copiarlo de .env.example
if not exist .env (
    echo El archivo .env no existe. Copiando desde .env.example...
    copy .env.example .env >nul
    echo Generando la clave de la aplicacion (APP_KEY)...
    php artisan key:generate
    if %errorlevel% neq 0 (
        echo Error: Fallo la generacion de la clave de la aplicacion (php artisan key:generate).
        echo.
        pause
        exit /b 1
    )
)

echo.
echo Todos los requerimientos estan correctos.
echo Iniciando el servidor de desarrollo de Laravel...

:: Inicia el servidor de Laravel en una nueva ventana de terminal.
start "Laravel Dev Server" php artisan serve

:: Espera 5 segundos para dar tiempo a que el servidor se inicie correctamente.
timeout /t 5 /nobreak > nul

:: Abre la URL de la aplicación en el navegador predeterminado.
start http://127.0.0.1:8000

endlocal
echo.
pause