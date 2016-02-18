#!/usr/bin/env bash
#
# Upgrade (or install) magento modules using modman
#
# the parameter of the function are
#  - name of the module
#  - URL of git repo
#  - boolean : to commit or not
#
function upgrademodule {

    echo "################"
    echo ""
    echo "Upgrade(Install) Module $1"
    echo ""
    echo "################"

    cd server/.modman/
    if [ ! -d $1 ]
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
    modman deploy --force --copy $1
    if [ $3 ]
    then
        git add *
        git commit -m "Upgrade module $1 $2"
    fi
    cd .. #retour à la racine

}

#
# Remove magento modules using modman
#
# the parameter of the function are
#  - name of the module
#  - URL of git repo
#  - boolean : to commit or not
#
function removemodule {

    echo "################"
    echo ""
    echo "Remove Module $1"
    echo ""
    echo "################"
    cd server/.modman/
    if [ ! -d $1 ]
    then
        echo "create $1 by git clone"
        git clone $2 $1
    fi
    cd .. #retour dans server
    modman undeploy --force --copy $1
    if [ $3 ]
    then
        git add *
        git commit -m "Remove module $1 $2"
    fi
    cd .. #retour à la racine

}

# Installed modules
upgrademodule widgento-login https://github.com/netzkollektiv/widgento-login.git true
upgrademodule mg_mod_enhancedgrid git@git.jetpulp.hosting:php/mg_mod_enhancedgrid.git true
upgrademodule Aoe_Scheduler https://github.com/AOEpeople/Aoe_Scheduler true
upgrademodule Aoe_QuoteCleaner https://github.com/AOEpeople/Aoe_QuoteCleaner true
upgrademodule Aoe_CacheCleaner https://github.com/AOEpeople/Aoe_CacheCleaner.git true


# à tester !
#upgrademodule Aoe_ClassPathCache https://github.com/AOEpeople/Aoe_ClassPathCache.git
#https://github.com/AOEpeople/Cm_Cache_Backend_Redis.git
#https://github.com/AOEpeople/Cm_RedisSession

# Mgt_toolbar ?
# Antidot
# Ecom Dev

#upgrademodule mg_mod_GLS git@git.jetpulp.hosting:php/mg_mod_GLS.git false
#upgrademodule mg_mod_SoColissimo git@git.jetpulp.hosting:php/mg_mod_SoColissimo.git false
#upgrademodule Aoe_TemplateHints https://github.com/AOEpeople/Aoe_TemplateHints.git false

# Remove modules (need forked modman)
#removemodule mg_mod_expeditorinet git@git.jetpulp.hosting:php/mg_mod_expeditorinet.git true
#removemodule mg_mod_GLS git@git.jetpulp.hosting:php/mg_mod_GLS.git true
#removemodule Aoe_TemplateHints https://github.com/AOEpeople/Aoe_TemplateHints.git true
