#!/usr/bin/env bash

#
# Path of the db backup on the server
#
export DB_BACKUP_PATTERN=theme_mag_20*.sql.gz
export DB_BACKUP_DIR=/vol/nfs_backup_sql/mag-db3-new/mysql/mag-db3-new
export DB_BACKUP_SERVER=aotools.host.addonline.fr

start=$(dirname "$0")/../../jetpulper/docker/start.sh
if [ -f $start ]
then
    . $start
else
    echo "You need to clone git@git.jetpulp.hosting:dev/jetpulper.git in the same workspace"
fi