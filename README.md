Aomagento
-------------

Magento version 1.9.2.4

Dépôt "modèle" Magento Community Edition, qui sert de base pour les nouveaux projets magento.

URLs
-------------

* PREPROD : http://theme-mg.preprod.jetpulp.hosting/ 
* PROD : http://theme-mg.jetpulp.fr/

----------

Pré-Requis
-------------

- Apache 2.4 
- MariaDB 10
- PHP 7.0
- Gulp

> **Note:**

----------

<i class="fa fa-cog"></i>Installation - Mise en route
-------------

```
cd docker
bash start.sh
```

<i class="fa fa-picture"></i>Thèmes
-------------


Thème COMPUTEC
-------------

Thème non-responsive : description à compléter

Gulp

Thème BRANDER
-------------

Thème non-responsive : description à compléter

Gulp


Thème ANGLECIA
-------------

Thème non-responsive : description à compléter

Grunt

Thème JETRWD
-------------

Thème responsive de magento, avec les sélecteurs pour nos tests sélénium

----------

----------

<i class="fa fa-exchange"></i>ERP
-------------

Pas d'interface ERP



<i class="fa fa-exchange"></i>Flux shopping
-------------

Pas de flux shopping

----------

<i class="fa fa-cog"></i>Modules tiers installées 
-------------


----------

<i class="fa fa-cog"></i>Modules développés installées
-------------

> **Note:**
> Décrire ici  les modules développés installées et leur roles

<i class="fa fa-server"></i>Hébergement
-------------

http://wikisi.addonline.local/index.php/Mutu154.host.addonline.fr

<i class="fa fa-help"></i>Informations spécifiques complémentaires
--------------

Une partie des pages est en privée, d'ou la page de connection. Celle ci contient également un formulaire de demande 
d'accès (Gravity Forms), le client est en charge d'envoyer le môt de passe au personne le contactant via celui-ci.

* Sylvain PRAS sylvain.pras@jetpulp.fr
* Laurent FERREIRA laurent.ferreira@jetpulp.fr

Gitlab-CI
----------
L'intégration continue dans Gitlab CI a été mis en place.

Il est possible de tester les tâches de build ou test avant de pusher :

`gitlab-ci-multi-runner exec docker phpcs`

`gitlab-ci-multi-runner exec docker gulp`

Il faut au préalable avoir installé gitlab-ci-multi-runner  : https://gitlab.com/gitlab-org/gitlab-ci-multi-runner
