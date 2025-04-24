Déploiement du projet – tutoriel

Avant de commencer, télécharger le zip du projet, puis retirer le dossier vendor et le fichier .git.
Ensuite, exécuter la commande "composer install --optimize-autoloader" pour générer un nouveau dossier vendor en mode production.
Envoyer tous les fichiers sur le serveur.
Dans le fichier .env changer app_env= dev par app_env=prod.
Dans la racine ajouter un fichier .htaccess pour rediriger les commande vers « /public/ »
Le projet est maintenant déployé.
