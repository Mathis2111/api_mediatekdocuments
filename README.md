<h1>Présentation de MyAccessBDD</h1>
<br>
Ce projet est une extension de l'API d'origine, disponible ici : https://github.com/CNED-SLAM/rest_mediatekdocuments Le README de ce dépôt contient la présentation complète de l'API de base, incluant sa structure et son mode de fonctionnement.<br>
Les ajouts effectués dans cette version concernent uniquement le fichier MyAccessBDD.php, qui introduit de nouvelles fonctions pour répondre aux besoins spécifiques de l'application MediaTekDocuments.<br>

<h2>Créer et remplir la base de données :</h2>

Récupérer le script mediatek86.sql.<br>
Créer la base de données mediatek86 dans phpMyAdmin.<br>
Exécuter le script SQL pour la remplir.<br>

<h2>Ajout de nouvelles méthodes</h2>

<h3>Fonctionnalités ajoutées :</h3>

Le fichier MyAccessBDD.php enrichit AccessBDD en ajoutant des fonctionnalités spécifiques pour la gestion des données dans l'application MediaTekDocuments.<br>

<h3>Nouvelles fonctions SQL ajoutées :</h3>

selectAllCommandesDocument : Méthode qui permet de sélectionner les commandes d'un livre / dvd et les renvoies dans un tableau. <br>
selectAllAbonnementsRevues : Méthode qui permet de selectionner les abonnements d'une revue et les renvoies dans un tableau. <br>
selectAllAbonnementsEcheance : Méthode qui sélectionne les abonnements d'une revue qui se termine dans les 30 jours et les renvoies dans un tableau. <br>
getServiceByUserName : Méthode qui récupère le service d'un utilisateur en fonction de son Prénom et Nom. <br>
getAllSuivis : Méthode qui sélectionne les etats et leurs identifiants de suivis et les renvoies sous forme de tableau. <br>
AjouterLivre : Méthode qui permet l'ajout d'un livre. <br>
AjouterCommande : Méthode qui permet d'insérer une commande de livre / dvd dans la base de donnée. <br>
AjouterCommandeDocument : Méthode qui permet d'insérer une commande d'abonnements dans la base de donnée. <br>
AjouterAbonnementRevue : Méthode qui permet d'ajouter un abonnement pour une revue. <br>
ModifierEtatCommande : Méthode qui permet de modifier une commande de livre / dvd dans la base de donnée. <br>
ModifierLivre : Méthode qui permet de modifier les informations d'un livre. <br>
SupprimerCommandeDocument : Méthode qui permet de supprimer une commande de livre / dvd dans la base de donnée. <br>
SupprimerAbonnementRevue : Méthode qui permet de supprimer un abonnement pour une revue. <br>

<h2>Conclusion</h2>

Ces améliorations permettent d'optimiser la gestion des données et d'assurer une meilleure interaction avec l'application MediaTekDocuments.<br>
