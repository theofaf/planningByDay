# planningByDay
Un projet ENI à la con, pour des cons, fait par les boss

# contraintes pour créer la fixtures session :
une session c'est un module, un prof, une salle et une classe.
il faut vérifier :
- que la salle n'est pas utilisé par une autre session 
- que le nb d'elève d'une classe ne dépasse pas la capacité de la salle
- que le module enseigné fait partie des compétences actives du prof
- que le prof, la classe, la salle et le module appartiennent au même établissement
- que la classe n'a pas de chevauchement de cours
- que le module fait bien parti du cursus de la classe
- peut être d'autres truc mais t'as capté l'idée le sang

# contraintes pour créer la fixtures message :
vérifier que émetteur/receveur font parti du même établissement

# faire modif/ajout
- ajouter une date dans message pour améliorer la trace
- faire fixtures pour session, message et tickets
- mettre date_derniere_action dans Utilisateur, pour savoir si le token doit expiré <br> (si pas d'activité depuis 10 min => expiration)
- dans moduleFormationUtilisateur, ajouter date_dernier_cours et is_reabilité (default true), si + de 6 mois, le prof doit faire une réabilitation pour pourvoir enseigné le module