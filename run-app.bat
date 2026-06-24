@echo off
echo "Iniciando el servidor de desarrollo de Laravel..."

:: Inicia el servidor de Laravel en una nueva ventana de terminal.
start "Laravel Dev Server" php artisan serve

:: Espera 5 segundos para dar tiempo a que el servidor se inicie correctamente.
timeout /t 5 /nobreak > nul

:: Abre la URL de la aplicación en el navegador predeterminado.
start http://127.0.0.1:8000