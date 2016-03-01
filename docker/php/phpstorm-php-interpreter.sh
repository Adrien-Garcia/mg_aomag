#!/usr/bin/env bash

# eval docker-chine env to "attach the shel to docker"
eval "$(docker-machine env default)"

# run docker container :
# ----------------------
# -i --interactive -- Keep STDIN open even if not attached
# NOT use -t
# --rm -- Automatically remove the container when it exits (incompatible with -d)
# -v Volumes : $PWD : current directory
#              /private/var/folders/ (used by PHPStorm) : this volume has to be mounted in the docker-machine
# --net=bridge --link=mg_aomagento-db:db  -- for accessg the db , unit test magento needs it
# OR --net=host
# -w  -- Working directory inside the container
docker run -i --rm -v "${PWD}":"${PWD}" -v /private/var/folders/:/private/var/folders/ --net=bridge --link=mg_aomagento-db:db \
    -w  /Users/spras/Devl/workspace_php/aomagento/server jetpulp/phpunit php "$@"
#docker run -i --rm -v "${PWD}":"${PWD}" -v /private/var/folders/:/private/var/folders/ --net=host  \
#    -w "${PWD}" jetpulp/phpunit php "$@"

