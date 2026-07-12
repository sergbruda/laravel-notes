#!/bin/bash
L=$(ls -t /opt/webstack/backups/*.sql | head -n 1)
if [ -z "$L" ]; then echo "Нет бэкапов!"; exit 1; fi
echo "Восстанавливаем из $L..."
docker exec -i web_db mysql -u site_user -pSiteUserPassword456! site_db < "$L"
echo "Готово!"
