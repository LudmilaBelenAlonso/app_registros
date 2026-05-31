#!/bin/bash
# Script de backup automático para la base de datos economia_hogar
# Se ejecuta dentro del contenedor Docker app_registros

# Configuración
BACKUP_DIR="/mnt/360C17970C17516B/backups/mysql"
CONTAINER_NAME="app_registros"
DB_USER="devroot"
DB_PASS="123"
DB_NAME="economia_hogar"
DATE=$(date +"%Y-%m-%d_%H-%M")
BACKUP_FILE="${BACKUP_DIR}/backup_${DB_NAME}_${DATE}.sql"

# Crear carpeta de backups si no existe
mkdir -p "$BACKUP_DIR"

# Ejecutar mysqldump dentro del contenedor y guardar el archivo en el host
docker exec "$CONTAINER_NAME" mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE"

# Verificar si el backup fue exitoso
if [ $? -eq 0 ]; then
    echo "[$(date)] ✅ Backup exitoso: $BACKUP_FILE"
    
    # Comprimir el backup para ahorrar espacio
    gzip "$BACKUP_FILE"
    echo "[$(date)] 📦 Comprimido: ${BACKUP_FILE}.gz"
else
    echo "[$(date)] ❌ Error al crear el backup"
fi

# Eliminar backups de más de 30 días para no llenar el disco
find "$BACKUP_DIR" -name "backup_*.sql.gz" -mtime +30 -delete
echo "[$(date)] 🧹 Limpieza: backups de más de 30 días eliminados"
