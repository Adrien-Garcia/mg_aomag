#!/usr/bin/env bash
set -e

echo "Start init-db.sh";

echo "Restore database";
dump_files=( $(ls -t /docker-entrypoint-initdb.d/${DB_BACKUP_PATH_PATTERN}) )

echo "Restore ${dump_files[0]}";
zcat ${dump_files[0]} | mysql -proot -u root $MYSQL_DATABASE

echo "Update database to local context ${SERVER_NAME}";
mysql  -proot -u root $MYSQL_DATABASE <<- EOM
UPDATE core_config_data SET value=replace(value, 'aomagento.addonline.biz', '${SERVER_NAME}');
EOM

echo "End init-db.sh";
