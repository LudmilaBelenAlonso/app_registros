#!/bin/bash
# Script para iniciar servicios en el contenedor app_registros
# Ejecutar con: docker exec -u root app_registros /var/www/html/start_services.sh

echo "=== Iniciando servicios ==="

# Iniciar MySQL
service mysql start
sleep 5

# Verificar si la BD existe, si no crearla
mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS economia_hogar;" 2>/dev/null
mysql -u root -proot -e "GRANT ALL PRIVILEGES ON economia_hogar.* TO 'devroot'@'%' IDENTIFIED BY '123'; FLUSH PRIVILEGES;" 2>/dev/null

# Iniciar Apache
service apache2 start

echo "=== Servicios iniciados ==="
service mysql status
service apache2 status
