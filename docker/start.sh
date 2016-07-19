#!/usr/bin/env bash

# source variables
. $(dirname "$0")/env.sh

#init data containers
docker run --name mg_aomagento-dbdata -v /var/lib/mysql tianon/true /true &> /dev/null

start=$(dirname "$0")/../../jetpulper/docker/start.sh
if [ -f $start ]
then
    . $start
else
    echo "You need to clone git@git.jetpulp.hosting:dev/jetpulper.git in the same workspace"
fi