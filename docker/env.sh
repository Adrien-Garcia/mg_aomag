#!/usr/bin/env bash

#
# Set environnement variables for running the project on docker
#
function init {

    export COMPOSE_PROJECT_NAME=mgaomagento
    # db backup name pattern
    export DB_BACKUP_PATTERN="theme_mag_20*.sql.gz"
    # Path of the db backup on the server
    export DB_BACKUP_DIR=/vol/nfs_backup_sql/mag-db3-new/mysql/mag-db3-new
    # db backup server
    export DB_BACKUP_SERVER=aotools.host.addonline.fr
    # timeout for DB restoring waiting (default 3m0s)
    #export DB_RESTORE_TIMEOUT=3m0s
    # local server name
    export SERVER_NAME=mg-aomagento.$JETPULP_USERNAME.jetpulp.dev
    # original server name
    export ORIGINAL_SERVER_NAME=theme-mg.jetpulp.fr
    # every vitural_host separated by , (used for nginx proxy)
    export VIRTUAL_HOST=$SERVER_NAME
    # database name
    export MYSQL_DATABASE=addonline_aomagento_magento

}

#
# OVERRIDE functions for specific cases :
# init-data-containers
# delete-data-containers
# build-assets