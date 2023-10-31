#!/bin/bash
echo "Configuration du projet"
# Ce script automatise l'installation et la configuration d'un projet Symfony :
# - Génère les dépendances composer pour le projet.
# - Génère l'autoloader Composer et vide le cache Symfony.
# - Supprime la base de données existante (si elle existe) et crée une nouvelle base de données.
# - Effectue les migrations pour synchroniser les entités avec la base de données.
# - Charge les fixtures pour peupler la base de données avec des données d'exemple.
# - Vide à nouveau le cache Symfony.
echo -e "\n\e[94mInitialisation de PlanningByDay...\e[0m"
echo ""
# Vérifier si Composer et WAMP/XAMP sont installés
#read -p "Avez-vous installé Composer et WAMP sur votre environnement ? (y/n): " installationComplete
#if [ "$installationComplete" != "y" ]; then
#    echo -e "\nInstallez Composer à cette adresse : https://getcomposer.org/download/\e[0m"
#    echo -e "Installer WAMP à cette adresse : https://sourceforge.net/projects/wampserver/files/WampServer%203/WampServer%203.0.0/wampserver3.3.0_x64.exe/download\e[0m"
#    echo -e "\nConfigurer votre variable d'environnement PATH avec le path du bin PHP"
#    echo "Ensuite, redemarrer la machine, et relancez cette commande"
#    echo -e "\e[42m\e[97mCommande terminée avec succès.\e[0m"
#    exit 1
#fi
# Installer les dépendances Composer
echo -e "\n\e[94mInstallation des dépendances Composer...\e[0m"
composer install
# Générer l'autoloader Composer
echo -e "\n\e[94mGénération de l'autoloader Composer...\e[0m"
composer dump-autoload
# Vider le cache
echo -e "\n\e[94mVidage du cache Symfony...\e[0m"
php bin/console c:c
# Supprimer la base de données si elle existe
echo -e "\n\e[94mSuppression de la base de données existante (si elle existe)...\e[0m"
php bin/console doctrine:database:drop --if-exists --force
echo ""
# Créer la base de données
echo -e "\n\e[94mCréation de la base de données...\e[0m"
php bin/console doctrine:database:create
# Exécution des migrations
echo -e "\n\e[94mExécution des migrations...\e[0m"
php bin/console doctrine:migrations:migrate --no-interaction
# Valider la synchronisation entre les entités et les tables
echo -e "\n\e[94mMaj et validation de la synchronisation entre les entités et les tables...\e[0m"
php bin/console d:s:u
php bin/console d:s:v
# Charger les fixtures
echo -e "\n\e[94mChargement des fixtures...\e[0m"
php bin/console d:f:l --append
# Vider à nouveau le cache Symfony
echo -e "\n\e[94mVidage du cache Symfony...\e[0m"
php bin/console c:c
# Afficher le temps écoulé
duration=$SECONDS
heures=$((duration/3600))
minutes=$((duration%3600/60))
secondes=$((duration%60))
echo "Temps écoulé: ${heures}h ${minutes}m ${secondes}s"
# Afficher un bandeau vert avec un message en blanc
echo -e "\e[42m\e[97mCommande terminée avec succès.\e[0m"
echo "Démarrage du projet"
symfony server:start
