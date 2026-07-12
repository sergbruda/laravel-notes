#!/bin/bash
D=$(date +%Y-%m-%d)
docker exec web_db mysqldump -u site_user -pSiteUserPassword456! site_db > /opt/webstack/backups/db_$D.sql 2>/dev/null
find /opt/webstack/backups -name '*.sql' -mtime +7 -delete
