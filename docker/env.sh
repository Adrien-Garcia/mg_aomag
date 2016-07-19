#!/usr/bin/env bash

#
# environnement variables for running the project on docker
#
# db backup name pattern
export DB_BACKUP_PATTERN="theme_mag_20*.sql.gz"
# Path of the db backup on the server
export DB_BACKUP_DIR=/vol/nfs_backup_sql/mag-db3-new/mysql/mag-db3-new
# db backup server
export DB_BACKUP_SERVER=aotools.host.addonline.fr
# local server name
export SERVER_NAME=mg-aomagento.$JETPULP_USERNAME.jetpulp.dev
# original server name
export ORIGINAL_SERVER_NAME=theme-mg.jetpulp.fr
# database name
export MYSQL_DATABASE=addonline_aomagento_magento