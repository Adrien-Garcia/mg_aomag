#!/bin/sh

################################################################################
# deploy.sh
#
# deploye aomagento sur le serveur de prod
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
ENV="PROD"

apps_directory=/srv/www/vhosts.d/
destination=aomagento
# /!\ doit contenir _prod_ : sert pour supprimer les anciennes versions.
pattern_zip=aomagento_prod_*
NB_VERSIONS=5

function recup_fichier_create_ln
{

echo "$0 : recopie des fichiers et creation des liens logiques"
cp -Rf ${appli}/* ${destination}/

echo "$0 : changement des droits"
# pour le moment : deploie tout sur le serveur => sur tout.
chown -R www-data.www-data ${destination}
chmod -R g+w ${destination} 
# sauf les fichiers à nous et autres fichiers importants
chown root.root ${destination}/deploy.sh
chmod -R g-w ${destination}/deploy.sh  

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
