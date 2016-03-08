#!/usr/bin/env bash

# For mac users : export docker machine env variables :
if hash docker-machine 2>/dev/null; then
    eval "$(docker-machine env default)"
fi
dockerComposeFilePath=$(dirname "$0")

#
# Path of the db backup on the server
#
export DB_BACKUP_PATH_PATTERN=addonline_aomagento_magento_*.sql.gz
export DB_BACKUP_DIR=/opt/backup/mysql/mag5
export DB_BACKUP_SERVER=mag5.host.addonline.fr

# Check if db backup file has already been downloaded
localDbBackupFilename=`ls -tr ${dockerComposeFilePath}/db/00_${DB_BACKUP_PATH_PATTERN} | tail -1`
if [ $localDbBackupFilename ]
then
    echo "${localDbBackupFilename} already exists, no need to download"
else

    # Get the last backup file on remote server
    dbBackupFilename=`ssh ${DB_BACKUP_SERVER} "ls -tr ${DB_BACKUP_DIR}/${DB_BACKUP_PATH_PATTERN} | tail -1"`

    if [ $dbBackupFilename ]
    then
        # Download the last backup file on remote server
        echo "Download database backup file ${dbBackupFilename} from ${DB_BACKUP_SERVER}  ..."
        scp ${DB_BACKUP_SERVER}:$dbBackupFilename ${dockerComposeFilePath}/db/00_`basename $dbBackupFilename`
    else
       echo "No ${DB_BACKUP_PATH_PATTERN} file on ${DB_BACKUP_SERVER} !"
       exit 1;
    fi
fi
echo "Start docker-compose"
cd ${dockerComposeFilePath}
docker-compose up -d
#stop and remove docker containers when stop script
trap "docker-compose stop;docker-compose rm -f" SIGINT SIGTERM
docker-compose logs


