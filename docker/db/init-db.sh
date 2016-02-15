#!/usr/bin/env bash
set -e

echo "Start init-db.sh";

echo "Restore database";
dump_files=( $(ls -t /docker-entrypoint-initdb.d/addonline_aomagento_magento_*) )

echo "Restore ${dump_files[0]}";
zcat ${dump_files[0]} | mysql -proot -u root $MYSQL_DATABASE

echo "Update database to local context ${SERVER_NAME}";
mysql  -proot -u root $MYSQL_DATABASE <<- EOM
UPDATE core_config_data SET value='http://${SERVER_NAME}/' WHERE value = 'http://aomagento.spras.jetpulp.dev/';
EOM

echo "End init-db.sh";
