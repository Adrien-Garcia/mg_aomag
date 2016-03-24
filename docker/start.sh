#!/usr/bin/env bash

#
# Path of the db backup on the server
#
export DB_BACKUP_PATTERN=addonline_aomagento_magento_*.sql.gz
export DB_BACKUP_DIR=/opt/backup/mysql/mag5
export DB_BACKUP_SERVER=mag5.host.addonline.fr

. $(dirname "$0")/../../jetpulper/docker/start.sh