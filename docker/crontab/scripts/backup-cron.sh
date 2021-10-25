#!/bin/sh

# /bin/sh /appdata/scripts/backup-cron.sh
tagnow=$(date '+%Y%m%d-%H%M%S')

mysqldump --host=php-marketing-db --user=$MYSQL_USER --password=$MYSQL_ROOT_PASSWORD db_marketing > /appdata/io/out/db_backup_db_marketing_$tagnow.sql