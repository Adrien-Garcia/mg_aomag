#!/usr/bin/env bash
#
# Upgrade .eslintrc, .sass-lint.yml, etc... files, the sources are in http://git.jetpulp.hosting/dev/jetpulper
#
# After upgrading theses files, run `docker-gulp lint` in docker directory to check and correct errors
#
#
green=`tput setaf 2`
reset=`tput sgr0`

#Authenticate in Gitlab
if [[ -z "${GITLAB_PRIVATE_TOKEN}" ]] ;
then
    echo "${green}Please Create a Personnal Acces Token in your Gitlab Settings (http://git.jetpulp.hosting/profile/personal_access_tokens) then copy/paste it here : ${reset}"
    read GITLAB_PRIVATE_TOKEN
    echo "" >> ~/.docker_jetpulp
    echo "export GITLAB_PRIVATE_TOKEN=${GITLAB_PRIVATE_TOKEN}" >> ~/.docker_jetpulp
fi

file='server/skin/frontend/COMPUTEC/default/scss/.sass-lint.yml'
echo "Upgrade $file"
curl -o $file -s --header "PRIVATE-TOKEN: ${GITLAB_PRIVATE_TOKEN}" http://git.jetpulp.hosting/dev/jetpulper/raw/master/scss/.sass-lint.yml

file='server/skin/frontend/COMPUTEC/default/js/.eslintrc'
echo "Upgrade $file"
curl -o $file -s --header "PRIVATE-TOKEN: ${GITLAB_PRIVATE_TOKEN}" http://git.jetpulp.hosting/dev/jetpulper/raw/master/js/.eslintrc

file='server/skin/frontend/COMPUTEC/default/js/_eslint.js'
echo "Upgrade $file"
curl -o $file -s --header "PRIVATE-TOKEN: ${GITLAB_PRIVATE_TOKEN}" http://git.jetpulp.hosting/dev/jetpulper/raw/master/js/_eslint.js

file='server/skin/frontend/COMPUTEC/default/js/_eslint_user.js'
echo "Upgrade $file"
curl -o $file -s --header "PRIVATE-TOKEN: ${GITLAB_PRIVATE_TOKEN}" http://git.jetpulp.hosting/dev/jetpulper/raw/master/js/_eslint_user.js