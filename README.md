<ins>**Documentation**</ins>

•	[Guide Utilisateur](https://github.com/MelisandeOnana/CongeFacile/blob/master/docs/CAHIER%20%20DE%20%20RECETTES%20-%20CONGEFACILE.pdf) 
•	[Cahier de recette](https://github.com/MelisandeOnana/CongeFacile/blob/master/docs/CAHIER%20%20DE%20%20RECETTES%20-%20CONGEFACILE.pdf](https://github.com/MelisandeOnana/CongeFacile/blob/master/docs/GUIDE%20UTILISATEUR%20-%20CongeFacile.pdf))

<ins>**Guide de démarrage du projet**</ins>

1)	Récupérer les fichiers du projet en téléchargeant le zip du projet.
2)	Ouvrir le projet sur un logiciel d’édition de code « Visual studio code » par exemple.
3)	Créer une base de données dans MySQL, puis modifier le lien vers celle-ci dans le dossier .env
4)	Dans un terminal, exécuter la commande « composer install » pour générer le dossier vendor essentiel au lancement du projet afin de charger les différentes dépendances.
5)	Dans la même terminale, exécuter la commande « php bin/console doctrine:schema:update --dump-sql --force » pour charger la structure de la base de données.
6)	Dans la même terminale, exécuter la commande « php bin/console doctrine:fixtures:load » pour charger les données.
7)	Démarrer le projet en exécutant la commande « symfony server:start ».

<ins>**Guide d’hébergement du projet**</ins>

1)	Récupérer les fichiers du projet en téléchargeant le zip.
2)	Ouvrir le projet sur un éditeur de code « Visual studio code » par exemple.
3)	Créer une base de données sur MySQL, puis modifier le lien vers celle-ci dans le fichier .env .
4)	Dans ce fichier, changer le l’environment dev par prod sur la ligne « app_env=prod ».
5)	Dans un terminal, exécuter la comande « composer install --optimize-autoloader » pour générer le dossier vendor contenant les dépendances du projet, important pour son fonctionnement et son lancement.
6)	Dans la même terminale, exécuter la commande « php bin/console doctrine:schema:update --dump-sql --force » pour charger la structure de la base de données.
7)	Dans la même terminale, exécuter la commande « php bin/console doctrine:fixtures:load » pour charger les données (si besoin).
8)	Dans le même terminal, exécuter la commande « php bin/console cache:clear » pour vider le cache prod et éviter les problèmes au chargement du site.
9)	Envoyer l’ensemble des fichiers vers le serveur d’hébergement.
10)	Dans le dossier Public ajouter un fichier htaccess pour rediriger toutes les requêtes vers index.php grace au script suiviant :
    
RewriteEngine On 
RewriteCond %{REQUEST_FILENAME} !-f 
RewriteCond %{REQUEST_FILENAME} !-d 
//Rediriger toutes les requêtes vers index.php
 RewriteRule ^ index.php [QSA,L]
 
12)	 Ouvrir le site via l’url de cette manière « nomdedomaine/public/ »

