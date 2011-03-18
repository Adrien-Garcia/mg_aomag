#!/bin/sh

################################################################################
# deploy.sh
#
# deploye aomagento sur le serveur de preprod
#
# Auteur : Samuel Forhan <samuel.forhan@addonline.fr>
#
# Historique :
#  17/02/2011 :
#      Création : Script applelé par aodeploy.sh le script de déploiement commun
#
################################################################################

#Définition des constantes et variables globales
APPLI_NAME="aomagento"
ENV="PREPROD"

apps_directory=/srv/www/vhosts.d/
destination=aomagento_preprod
# /!\ doit contenir _preprod_ : sert pour supprimer les anciennes versions.
pattern_zip=aomagento_preprod_*
NB_VERSIONS=5

function recup_fichier_create_ln
{

echo "$0 : recopie des fichiers et creation des liens logiques"
cp -Rf ${appli}/* ${destination}/

echo "$0 : changement des droits"
# pour le moment : deploie tout sur le serveur => sur tout.
chown -Rf www-data.www-data ${destination}
chmod -Rf g+w ${destination} 
# sauf les fichiers à nous et autres fichiers importants
chown -f root.root ${destination}/deploy.sh
chmod -Rf g-w ${destination}/deploy.sh  

}

function undeploy
{
 echo "${0} : ******************************************************************************* "
}

function deploy
{
 echo "${0} : ******************************************************************************* "
}
function switch
{
 echo "${0} : ******************************************************************************* "
}
function scriptdb
 {
 echo "${0} : ******************************************************************************* "
}
