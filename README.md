###### DOCUMENTATION DU PROJET PLANNING_BY_DAY

## Initialiser le projet (ou reset) sur un environnement Windows

### Installer symfony-cli:
Le composant symfony-cli s'installe via **cmd windows** à l'aide de scoop : **scoop install symfony-cli**

La CLI Symfony est un outil de développement pour vous aider à créer, exécuter et gérer vos applications Symfony 
directement depuis votre terminal. Pour plus d'informations : https://symfony.com/download

Si vous ne possèdez pas scoop, pas de panique et suivez ces étapes.
- **Ouvrer un terminal powershell** et saisisez ces commandes, une à une :
- **Set-ExecutionPolicy RemoteSigned -Scope CurrentUser** (répondez **Oui** afin d'exécuter la modification de stratégie du système)
- **irm get.scoop.sh | iex**

### Installer composer, wamp, git et configurez votre environnement :
- **Installez** composer : https://getcomposer.org/download/
- **Installez** wamp/ xamp **(PHP 8.1)**: https://www.wampserver.com/#wampserver-64-bits-php-5-6-25-php-7
- **Installez** git : https://github.com/git-for-windows/git/releases/download/v2.42.0.windows.2/Git-2.42.0.2-64-bit.exe
- **Configurez** la variable d'environnement PATH avec le chemin de votre bin PHP
- **Rédemarrez votre machine**
- **Ouvrer votre IDE favori**, configurer **GIT** afin de créer nouveau projet avec un **git clone** à cette **url** : https://github.com/theofaf/planningByDay.git
- **Assurez** vous d'avoir le fichier **.env** dans votre fichiers sources (demandez-le à l'équipe technique en charge du projet si nécessaire)
- **Ouvrer un terminal git bash** dans votre **IDE** pour **lancer** la commande : **sh initProject.sh**
- Ensuite, nous allons **installer le certifat TLS** pour le serveur symfony : **symfony.exe server:ca:install**
- Enfin, pour **lancer votre serveur en local**, c'est tous simple : **symfony server:start**  (pour l'arrêt : symfony server:stop)
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

# faire modif/ajout
- il manque des info concernant les modules (distanciel, présentiel, campus en ligne)
