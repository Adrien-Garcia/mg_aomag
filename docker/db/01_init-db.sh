#!/usr/bin/env bash
set -e

echo "Start init-db.sh";

echo "Update database to local context ${SERVER_NAME}";
mysql  -proot -u root $MYSQL_DATABASE <<- EOM
UPDATE core_config_data SET value=replace(value, '${ORIGINAL_SERVER_NAME}', '${SERVER_NAME}');
UPDATE core_config_data SET value=replace(value, 'http', 'https') where path like 'web/%/base_url';
EOM

echo "End init-db.sh";
