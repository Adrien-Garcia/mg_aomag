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
upgrademodule widgento-login https://github.com/jetpulp/widgento-login.git true
upgrademodule mg_mod_enhancedgrid git@git.jetpulp.hosting:php/mg_mod_enhancedgrid.git true
upgrademodule Aoe_Scheduler https://github.com/AOEpeople/Aoe_Scheduler true
upgrademodule Aoe_QuoteCleaner https://github.com/AOEpeople/Aoe_QuoteCleaner true
upgrademodule Aoe_CacheCleaner https://github.com/AOEpeople/Aoe_CacheCleaner.git true
upgrademodule customer-activation https://github.com/Vinai/customer-activation.git true
upgrademodule Magento-ChangeAttributeSet https://github.com/Flagbit/Magento-ChangeAttributeSet.git true
upgrademodule mg_mod_massrelater git@git.jetpulp.hosting:php/mg_mod_massrelater.git true
upgrademodule mg_mod_ResponsiveSlider git@git.jetpulp.hosting:php/mg_mod_ResponsiveSlider.git true
upgrademodule Aoe_ClassPathCache https://github.com/AOEpeople/Aoe_ClassPathCache.git true
upgrademodule EcomDev_LayoutCompiler https://github.com/EcomDev/EcomDev_LayoutCompiler true


# Non Installed modules :
# uncomment if needed by the client project
#upgrademodule mg_mod_advancedslideshow git@git.jetpulp.hosting:php/mg_mod_advancedslideshow.git
#upgrademodule mg_mod_expeditorinet git@git.jetpulp.hosting:php/mg_mod_expeditorinet.git true
#upgrademodule Atos-Magento https://github.com/quadra-informatique/Atos-Magento.git true
#upgrademodule Paybox-Magento https://github.com/quadra-informatique/Paybox-Magento.git true => TODO : fork to add modman file
#upgrademodule mg_mod_GLS git@git.jetpulp.hosting:php/mg_mod_GLS.git false
#upgrademodule mg_mod_SoColissimo git@git.jetpulp.hosting:php/mg_mod_SoColissimo.git false
#upgrademodule Aoe_TemplateHints https://github.com/AOEpeople/Aoe_TemplateHints.git false
#upgrademodule MagentoConnector https://github.com/AntidotForge/MagentoConnector.git false
#upgrademodule EcomDev_PHPUnit  git://github.com/EcomDev/EcomDev_PHPUnit.git false


# Reste à ajouter (sortir du dépôt aomagento, créer leur propre dépôt)
# Jetpulp_Checkout
# Addonline_Varnish
# Addonline_Brand
# Addonline_CategoryNavigation
# Addonline Newsletter dolist (à supprimer)
# Addonline Review_boost
# Addonline_Seo
# Addonline_UrlrewriteCleaner
# OneStepCheckout à supprimer


# Optimistations :

#TODO : php 7.0

#TODO : add apt sur le docker
#TODO : add REDIS sur le docker-compose
#https://github.com/AOEpeople/Cm_Cache_Backend_Redis.git
#https://github.com/AOEpeople/Cm_RedisSession


# Remove modules (need forked modman : https://github.com/jetpulp/modman.git)
#removemodule mg_mod_advancedslideshow git@git.jetpulp.hosting:php/mg_mod_advancedslideshow.git true
#removemodule mg_mod_expeditorinet git@git.jetpulp.hosting:php/mg_mod_expeditorinet.git true
#removemodule mg_mod_GLS git@git.jetpulp.hosting:php/mg_mod_GLS.git true
#removemodule Aoe_TemplateHints https://github.com/AOEpeople/Aoe_TemplateHints.git true

#Vider le cache magento
rm -Rf server/var/cache/mage-*
rm -Rf server/var/cache/cm-*