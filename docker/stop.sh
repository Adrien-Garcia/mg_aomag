#!/usr/bin/env bash

# For mac users : export docker machine env variables :
if hash docker-machine 2>/dev/null; then
    eval "$(docker-machine env default)"
fi
dockerComposeFilePath=$(dirname "$0")
cd ${dockerComposeFilePath}
docker-compose stop
docker-compose rm -f
