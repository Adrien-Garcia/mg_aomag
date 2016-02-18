#!/usr/bin/env bash
#
# Upgrade magento modules using modman
#
# the parameter of the function are
#  - name of the module
#  - URL of git repo
#  - boolean : to commit or not
#
function upgrademodule {

    cd server/.modman/
    if [ ! -f $1 ]
    then
        echo "create $1 by git clone"
        git clone $2 $1
    else
        echo "$1 exist, pull it"
        cd $1
        git pull
        cd .. #retour dans .modman
    fi
    cd .. #retour dans server
    modman deploy --force --copy mg_mod_expeditorinet
    if [ $3 ]
    then
        git add *
        git commit -m "Upgrade module $1 $2"
    fi
    cd .. #retour à la racine

}

# Test install mg_mod_expeditorinet
#upgrademodule 'mg_mod_expeditorinet' git@git.jetpulp.hosting:php/mg_mod_expeditorinet.git false

# Mgt_toolbar
# Antidot
# Ecom Dev
# SoColissimo
# GLS

upgrademodule 'widgento-login' https://github.com/netzkollektiv/widgento-login.git
