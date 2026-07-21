#!/bin/bash

# Настройки
CONTAINER_NAME="web_db"
DB_NAME="my_database"
BACKUP_DIR="/opt/webstack/backups"
DATE=$(date +%Y-%m-%d_%H-%M-%S)
BACKUP_FILE="$BACKUP_DIR/backup_$DATE.sql.gz"

echo "[$DATE] Начинаем бекап базы $DB_NAME..."

# МАГИЯ: Берем пароль root прямо из переменных контейнера
DB_ROOT_PASS=$(docker exec $CONTAINER_NAME printenv MYSQL_ROOT_PASSWORD)

# Делаем дамп, подставив украденный пароль
docker exec $CONTAINER_NAME sh -c "exec mysqldump -u root -p\"$DB_ROOT_PASS\" $DB_NAME" 2>/dev/null | gzip > $BACKUP_FILE

# Проверяем, создался ли файл
if [ -f "$BACKUP_FILE" ] && [ $(stat -c%s "$BACKUP_FILE") -gt 1024 ]; then
    echo "[$DATE] Успех! Бекап сохранен: $BACKUP_FILE"
else
    echo "[$DATE] ОШИБКА! Бекап пустой или не создался."
    rm -f $BACKUP_FILE
fi

# Удаляем бекапы старше 7 дней
find $BACKUP_DIR -name "backup_*.sql.gz" -type f -mtime +7 -delete
echo "[$DATE] Проверка старых бекапов завершена."
