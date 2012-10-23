#!/bin/sh

################################################################################
# deploy.sh
#
# deploye aomagento sur le serveur de prod
#
# Auteur : Sylvain Pras <sylvain.pras@addonline.fr>
#
# Historique :
#  17/02/2011 :
#      Création : Script applelé par aodeploy.sh le script de déploiement commun
#  04/10/2012 :
#      Evolution : on déploie une liste de répertoires qui sont remplacés au lieu 
#      d'être simplement écrasé comme auparavent (les fichiers obsolères sont 
#      donc maintenant supprimés du serveur 
#
################################################################################

#Définition des constantes et variables globales
APPLI_NAME="aomagento"
ENV="PROD"

apps_directory=/srv/www/vhosts.d/
destination=aomagento
# /!\ doit contenir _prod_ : sert pour supprimer les anciennes versions.
pattern_zip=aomagento_prod_*
NB_VERSIONS=3
APPLI_OWNER_USER=www-data

function recup_fichier_create_ln
{

echo "$0 : recopie des fichiers et creation des liens logiques"
# on récupère les fichiers atos de la version en place sur le serveur
cp -Rf ${destination}/lib/atos ${appli}/lib/ 
# pour chaque répertoire défini dans le fichier deploy.dirs, on supprime l'ancien et on le remplace par le nouveau
dos2unix ${appli}/deploy.dirs
for rep in $(cut -f 1 ${appli}/deploy.dirs);
do
  echo "$0 : déploiement répertoire ${rep}"
  # sauvegarde (archive) du répertoire actuel (vraiement au cas où)
  tar -zcf ${appli}/${rep}.tgz ${destination}/${rep}
	# suppression du répertoire actuel
	rm -r ${destination}/${rep}
  # recopie du nouveau répertoire avec les droits du user qui lance l'application
	sudo -u $APPLI_OWNER_USER cp -Rf  ${appli}/${rep} ${destination}/${rep} 
done
# on remplace les fichiers php qui sont à la racine
rm ${destination}/*.php
for file in ${appli}/*.php;
do
	sudo -u $APPLI_OWNER_USER cp -Rf  ${file} ${destination}/ 
done

}
function change_droits
{
  echo "$0 : changement des droits"
  # les fichiers ont été créé avec le bon user, pas besoin de changer le propriétaire
  chmod -R g+w ${destination} 

  set -o nounset  
}
function undeploy
{
 echo "${0} : on commence le déploiement : on passe magento en mode maintenance "
 touch ${destination}/maintenance.flag
}
function deploy
{
 echo "${0} : on fini le déploiement : on passe magento en mode production "
 rm ${destination}/maintenance.flag
}
function switch
{
 echo "${0} : ******************************************************************************* "
}
function scriptdb
 {
 echo "${0} : ******************************************************************************* "
}
