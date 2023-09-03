###### DOCUMENTATION DU PROJET PLANNING_BY_DAY

## Initialiser le projet (ou reset) sur un environnement Windows
- **Installez** composer : https://getcomposer.org/download/
- **Installez** wamp/ xamp **(PHP 8.1)**: https://www.wampserver.com/#wampserver-64-bits-php-5-6-25-php-7
- **Installez** git : https://github.com/git-for-windows/git/releases/download/v2.42.0.windows.2/Git-2.42.0.2-64-bit.exe
- **Configurez** la variable d'environnement PATH avec le chemin de votre bin PHP
- **Rédemarrez votre machine**
- **Ouvrer votre IDE favori**, configurer **GIT** afin de créer nouveau projet avec un **git clone** à cette **url** : https://github.com/theofaf/planningByDay.git
- assurez vous d'avoir le fichier .env dans votre fichiers sources (demandez-le à l'équipe technique en charge du projet si nécessaire)
- **Ouvrer un terminal git bash** pour **lancer** la commande : **sh initProject.sh**
- Et voilà, votre environnement de travail est **prêt** !

# contraintes pour créer la fixtures session :
une session c'est un module, un prof, une salle et une classe.
il faut vérifier :
- que la salle n'est pas utilisé par une autre session 
- que la classe n'a pas de chevauchement de cours
- que le prof n'a pas de chevauchement de cours
- que le nb d'elève d'une classe ne dépasse pas la capacité de la salle
- que le module enseigné fait partie des compétences actives du prof
- que le prof, la classe, la salle et le module appartiennent au même établissement
- que le module fait bien parti du cursus de la classe
- que la durée totale des sessions d'un module d'une classe ne dépasse pas la durée totale du module
  (exemple : si un module A fait 3h, une classe ne peut pas avoir 4h de ce module A)
- peut être d'autres truc mais t'as capté l'idée le sang

# faire modif/ajout
- il manque des info concernant les modules (distanciel, présentiel, campus en ligne)
- commencer l'authentification
- commencer les controllers rest
