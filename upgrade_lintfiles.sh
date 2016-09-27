#!/usr/bin/env bash
#
# Upgrade .eslintrc, .sass-lint.yml, etc... files, the sources are in http://git.jetpulp.hosting/dev/jetpulper
#
# After upgrading theses files, run `docker-gulp lint` in docker directory to check and correct errors
#
#
green=`tput setaf 2`
reset=`tput sgr0`
assets_folder='server/skin/frontend/COMPUTEC/default/'

#Authenticate in Gitlab
if [[ -z "${GITLAB_PRIVATE_TOKEN}" ]] ;
then
    echo "${green}Please Create a Personnal Acces Token in your Gitlab Settings (http://git.jetpulp.hosting/profile/personal_access_tokens) then copy/paste it here : ${reset}"
    read GITLAB_PRIVATE_TOKEN
    echo "" >> ~/.docker_jetpulp
    echo "export GITLAB_PRIVATE_TOKEN=${GITLAB_PRIVATE_TOKEN}" >> ~/.docker_jetpulp
    source ~/.docker_jetpulp
fi

file="${assets_folder}scss/.sass-lint.yml"
echo "Upgrade ${file}"
curl -o $file -s --header "PRIVATE-TOKEN: ${GITLAB_PRIVATE_TOKEN}" http://git.jetpulp.hosting/dev/jetpulper/raw/master/scss/.sass-lint.yml

file="${assets_folder}js/.eslintrc"
echo "Upgrade ${file}"
curl -o $file -s --header "PRIVATE-TOKEN: ${GITLAB_PRIVATE_TOKEN}" http://git.jetpulp.hosting/dev/jetpulper/raw/master/js/.eslintrc

file="${assets_folder}js/_eslint.js"
echo "Upgrade ${file}"
curl -o $file -s --header "PRIVATE-TOKEN: ${GITLAB_PRIVATE_TOKEN}" http://git.jetpulp.hosting/dev/jetpulper/raw/master/js/_eslint.js

file="${assets_folder}js/_eslint_projet.js"
if [[ ! -e "${file}" ]] ;
then
    echo "Upgrade ${file}"
    curl -o $file -s --header "PRIVATE-TOKEN: ${GITLAB_PRIVATE_TOKEN}" http://git.jetpulp.hosting/dev/jetpulper/raw/master/js/_eslint_projet.js
fi
