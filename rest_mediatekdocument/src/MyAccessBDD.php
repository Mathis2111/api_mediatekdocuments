<?php
include_once("AccessBDD.php");

/**
 * Classe de construction des requêtes SQL
 * hérite de AccessBDD qui contient les requêtes de base
 * Pour ajouter une requête :
 * - créer la fonction qui crée une requête (prendre modèle sur les fonctions 
 *   existantes qui ne commencent pas par 'traitement')
 * - ajouter un 'case' dans un des switch des fonctions redéfinies 
 * - appeler la nouvelle fonction dans ce 'case'
 */
class MyAccessBDD extends AccessBDD {
	    
    /**
     * constructeur qui appelle celui de la classe mère
     */
    public function __construct(){
        try{
            parent::__construct();
        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * demande de recherche
     * @param string $table
     * @param array|null $champs nom et valeur de chaque champ
     * @return array|null tuples du résultat de la requête ou null si erreur
     * @override
     */	
    protected function traitementSelect(string $table, ?array $champs) : ?array{
        
        switch($table){  
            case "livre" :
                return $this->selectAllLivres();
            case "dvd" :
                return $this->selectAllDvd();
            case "revue" :
                return $this->selectAllRevues();
            case "exemplaire" :
                return $this->selectExemplairesRevue($champs);
            case "service" :
                return $this->getServiceByUserName($champs);
            case "commandedocument" :
                return $this->selectAllCommandesDocument($champs);
            case "suivi" : 
                return $this->getAllSuivis();
            case "commande" :
                return $this->selectAllCommande($champs);
            case "abonnement" :
                return $this->selectAllAbonnementsRevues($champs);
            case "abonnementsecheance" :
                return $this->selectAllAbonnementsEcheance();
            case "genre" :
            case "public" :
            case "rayon" :
            case "etat" :
                // select portant sur une table contenant juste id et libelle
                return $this->selectTableSimple($table);
            case "" :
                // return $this->uneFonction(parametres);
            default:
                // cas général
                return $this->selectTuplesOneTable($table, $champs);
        }
    }

    /**
     * demande d'ajout (insert)
     * @param string $table
     * @param array|null $champs nom et valeur de chaque champ
     * @return int|null nombre de tuples ajoutés ou null si erreur
     * @override
     */	
    protected function traitementInsert(string $table, ?array $champs) : ?int{
        switch($table){
            case "ajout_livre" :
                return $this->AjouterLivre($champs);
            case "ajout_commande" :
                return $this->AjouterCommande($champs);
            case"ajout_commandeDocument" : 
                return $this->AjouterCommandeDocument($champs);
            case "ajout_abonnement" : 
                return $this->AjouterAbonnementRevue($champs);
            case "ajout_dvd" :
                return $this->AjouterDvd($champs);
            case "ajout_revue" :
                return $this->AjouterRevue($champs);
                // return $this->uneFonction(parametres);
            default:                    
                // cas général
                return $this->insertOneTupleOneTable($table, $champs);	
        }
    }
    
    /**
     * demande de modification (update)
     * @param string $table
     * @param string|null $id
     * @param array|null $champs nom et valeur de chaque champ
     * @return int|null nombre de tuples modifiés ou null si erreur
     * @override
     */	
    protected function traitementUpdate(string $table, ?string $id, ?array $champs) : ?int{
        switch($table){
            case "" :
                // return $this->uneFonction(parametres);
            case "modif_livre" :
                return $this->ModifierLivre($champs);
            case "modif_dvd" :
                return $this->ModifierDvd($champs);
            case "modif_etatCommande" :
                return $this->ModifierEtatCommande($champs);
            case "modif_revue" :
                return $this->ModifierRevue($champs);
            default:                    
                // cas général
                return $this->updateOneTupleOneTable($table, $id, $champs);
        }	
    }  
    
    /**
     * demande de suppression (delete)
     * @param string $table
     * @param array|null $champs nom et valeur de chaque champ
     * @return int|null nombre de tuples supprimés ou null si erreur
     * @override
     */	
    protected function traitementDelete(string $table, ?array $champs) : ?int{
        switch($table){
            case "sup_commande" :
                return $this->SupprimerCommandeDocument($champs);
            case "sup_abonnement" : 
                return $this->SupprimerAbonnementRevue($champs);
            case "sup_livre" : 
                return $this->SupprimerLivre($champs);
            case "sup_dvd" :
                return $this->SupprimerDvd($champs);
            case "sup_revue" :
                return $this->SupprimerRevue($champs);
                // return $this->uneFonction(parametres);
            default:                    
                // cas général
                return $this->deleteTuplesOneTable($table, $champs);	
        }
    }	    
        
    /**
     * récupère les tuples d'une seule table
     * @param string $table
     * @param array|null $champs
     * @return array|null 
     */
    private function selectTuplesOneTable(string $table, ?array $champs) : ?array{
        if(empty($champs)){
            // tous les tuples d'une table
            $requete = "select * from $table;";
            return $this->conn->queryBDD($requete);  
        }else{
            // tuples spécifiques d'une table
            $requete = "select * from $table where ";
            foreach ($champs as $key => $value){
                $requete .= "$key=:$key and ";
            }
            // (enlève le dernier and)
            $requete = substr($requete, 0, strlen($requete)-5);	          
            return $this->conn->queryBDD($requete, $champs);
        }
    }	

    /**
     * demande d'ajout (insert) d'un tuple dans une table
     * @param string $table
     * @param array|null $champs
     * @return int|null nombre de tuples ajoutés (0 ou 1) ou null si erreur
     */	
    private function insertOneTupleOneTable(string $table, ?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        // construction de la requête
        $requete = "insert into $table (";
        foreach ($champs as $key => $value){
            $requete .= "$key,";
        }
        // (enlève la dernière virgule)
        $requete = substr($requete, 0, strlen($requete)-1);
        $requete .= ") values (";
        foreach ($champs as $key => $value){
            $requete .= ":$key,";
        }
        // (enlève la dernière virgule)
        $requete = substr($requete, 0, strlen($requete)-1);
        $requete .= ");";
        return $this->conn->updateBDD($requete, $champs);
    }

    /**
     * demande de modification (update) d'un tuple dans une table
     * @param string $table
     * @param string\null $id
     * @param array|null $champs 
     * @return int|null nombre de tuples modifiés (0 ou 1) ou null si erreur
     */	
    private function updateOneTupleOneTable(string $table, ?string $id, ?array $champs) : ?int {
        if(empty($champs)){
            return null;
        }
        if(is_null($id)){
            return null;
        }
        // construction de la requête
        $requete = "update $table set ";
        foreach ($champs as $key => $value){
            $requete .= "$key=:$key,";
        }
        // (enlève la dernière virgule)
        $requete = substr($requete, 0, strlen($requete)-1);				
        $champs["id"] = $id;
        $requete .= " where id=:id;";		
        return $this->conn->updateBDD($requete, $champs);	        
    }
    
    /**
     * demande de suppression (delete) d'un ou plusieurs tuples dans une table
     * @param string $table
     * @param array|null $champs
     * @return int|null nombre de tuples supprimés ou null si erreur
     */
    private function deleteTuplesOneTable(string $table, ?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        // construction de la requête
        $requete = "delete from $table where ";
        foreach ($champs as $key => $value){
            $requete .= "$key=:$key and ";
        }
        // (enlève le dernier and)
        $requete = substr($requete, 0, strlen($requete)-5);   
        return $this->conn->updateBDD($requete, $champs);	        
    }
 
    /**
     * récupère toutes les lignes d'une table simple (qui contient juste id et libelle)
     * @param string $table
     * @return array|null
     */
    private function selectTableSimple(string $table) : ?array{
        $requete = "select * from $table order by libelle;";		
        return $this->conn->queryBDD($requete);	    
    }
    
    /**
     * récupère toutes les lignes de la table Livre et les tables associées
     * @return array|null
     */
    private function selectAllLivres() : ?array{
        $requete = "Select l.id, l.ISBN, l.auteur, d.titre, d.image, l.collection, ";
        $requete .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $requete .= "from livre l join document d on l.id=d.id ";
        $requete .= "join genre g on g.id=d.idGenre ";
        $requete .= "join public p on p.id=d.idPublic ";
        $requete .= "join rayon r on r.id=d.idRayon ";
        $requete .= "order by titre ";		
        return $this->conn->queryBDD($requete);
    }	

    /**
     * récupère toutes les lignes de la table DVD et les tables associées
     * @return array|null
     */
    private function selectAllDvd() : ?array{
        $requete = "Select l.id, l.duree, l.realisateur, d.titre, d.image, l.synopsis, ";
        $requete .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $requete .= "from dvd l join document d on l.id=d.id ";
        $requete .= "join genre g on g.id=d.idGenre ";
        $requete .= "join public p on p.id=d.idPublic ";
        $requete .= "join rayon r on r.id=d.idRayon ";
        $requete .= "order by titre ";	
        return $this->conn->queryBDD($requete);
    }	

    /**
     * récupère toutes les lignes de la table Revue et les tables associées
     * @return array|null
     */
    private function selectAllRevues() : ?array{
        $requete = "Select l.id, l.periodicite, d.titre, d.image, l.delaiMiseADispo, ";
        $requete .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $requete .= "from revue l join document d on l.id=d.id ";
        $requete .= "join genre g on g.id=d.idGenre ";
        $requete .= "join public p on p.id=d.idPublic ";
        $requete .= "join rayon r on r.id=d.idRayon ";
        $requete .= "order by titre ";
        return $this->conn->queryBDD($requete);
    }	

    /**
     * récupère tous les exemplaires d'une revue
     * @param array|null $champs 
     * @return array|null
     */
    private function selectExemplairesRevue(?array $champs) : ?array{
        if(empty($champs)){
            return null;
        }
        if(!array_key_exists('id', $champs)){
            return null;
        }
        $champNecessaire['id'] = $champs['id'];
        $requete = "Select e.id, e.numero, e.dateAchat, e.photo, e.idEtat ";
        $requete .= "from exemplaire e join document d on e.id=d.id ";
        $requete .= "where e.id = :id ";
        $requete .= "order by e.dateAchat DESC";
        return $this->conn->queryBDD($requete, $champNecessaire);
    }		    
    
    private function AjouterLivre(?array $champs): int {
        
        if (empty($champs)) {
            return 0;
        }

        try {
            $this->conn->updateBDD("START TRANSACTION");

            $champsDocument['id'] = $champs['Id'];
            $champsDocument['titre'] = $champs['Titre'];
            $champsDocument['image'] = $champs['Image'];
            $champsDocument['idGenre'] = $champs['IdGenre'];
            $champsDocument['idPublic'] = $champs['IdPublic'];
            $champsDocument['idRayon'] = $champs['IdRayon'];
            $result1 = $this->insertOneTupleOneTable("document", $champsDocument);
            
            $champsLivres_dvd['id'] = $champs['Id'];
            $result2 = $this->insertOneTupleOneTable("livres_dvd", $champsLivres_dvd);

            $champsLivre['id'] = $champs['Id'];
            $champsLivre['auteur'] = $champs['Auteur'];
            $champsLivre['isbn'] = $champs['Isbn'];
            $champsLivre['collection'] = $champs['Collection'];
            $result3 = $this->insertOneTupleOneTable("livre", $champsLivre);

            $this->conn->updateBDD("COMMIT");

            return $result1 + $result2 + $result3;
        } catch (Exception $e) {

            $this->conn->updateBDD("ROLLBACK");
            return 0;
        }
    }
    
    /**
    * Récupère le service d'un utilisateur en fonction de son nom
    * @param ?array $champs
    * @return array|null
    */
    private function getServiceByUserName(?array $champs) : ?array {

        if (!is_array($champs) || empty($champs)) {
            throw new InvalidArgumentException("Erreur 1 : aucun nom d'utilisateur fourni.");
        }
        if(!array_key_exists('utilisateur', $champs)){
            throw new InvalidArgumentException("Erreur 2 : aucun nom d'utilisateur fourni.");
        }
        $nomUtilisateur = $champs['utilisateur'];
        $requete = "SELECT service.service 
                   FROM utilisateur 
                   JOIN service ON utilisateur.service_id = service.id 
                   WHERE utilisateur.nom = :nom";
        
        $resultat = $this->conn->queryBDD($requete, ["nom" => $nomUtilisateur]);
        
        if ($resultat === false) {
            throw new RuntimeException("Erreur lors de la récupération du service.");
        }

        return $resultat;
    }
    
    private function ModifierLivre(?array $champs): int {
        

        if (empty($champs)) {
            return 0;
        }

        try {
            $this->conn->updateBDD("START TRANSACTION");

            $champsId['id'] = $champs['Id'];
            $champId = implode("", $champsId);
            $champsDocument['titre'] = $champs['Titre'];
            $champsDocument['image'] = $champs['Image'];
            $champsDocument['idGenre'] = $champs['IdGenre'];
            $champsDocument['idPublic'] = $champs['IdPublic'];
            $champsDocument['idRayon'] = $champs['IdRayon'];
            $result1 = $this->updateOneTupleOneTable("document", $champId, $champsDocument);

            $champsLivre['auteur'] = $champs['Auteur'];
            $champsLivre['isbn'] = $champs['Isbn'];
            $champsLivre['collection'] = $champs['Collection'];
            $result3 = $this->updateOneTupleOneTable("livre", $champId, $champsLivre);

            $this->conn->updateBDD("COMMIT");

            return $result1 + $result3;
        } catch (Exception $e) {

            $this->conn->updateBDD("ROLLBACK");
            return 0;
        }
    }
    
    private function SupprimerLivre(?array $champs): int {

        if (empty($champs)) {
            return 0;
        }

        try {
            $this->conn->updateBDD("START TRANSACTION");
            
            $champsLivre['id'] = $champs['Id'];
            $champsLivre['ISBN'] = $champs['Isbn'];
            $champsLivre['auteur'] = $champs['Auteur'];
            $champsLivre['collection'] = $champs['Collection'];
            $result1 = $this->deleteTuplesOneTable("livre", $champsLivre);
            
            $champsLivresDvd['id'] = $champs['Id'];
            $result2 = $this->deleteTuplesOneTable("livres_dvd", $champsLivresDvd);
            
            $champsDocument['id'] = $champs['Id'];
            $champsDocument['titre'] = $champs['Titre'];
            $champsDocument['image'] = $champs['Image'];
            $champsDocument['idRayon'] = $champs['IdRayon'];
            $champsDocument['idPublic'] = $champs['IdPublic'];
            $champsDocument['idGenre'] = $champs['IdGenre'];
            $result3 = $this->deleteTuplesOneTable("document", $champsDocument);
            
            $this->conn->updateBDD("COMMIT");

            return $result1 + $result2 + $result3;
        } catch (Exception $e) {

            $this->conn->updateBDD("ROLLBACK");
            return 0;
        }
    }
    
    private function AjouterDvd(?array $champs): int {
        
        if (empty($champs)) {
            return 0;
        }

        try {
            $this->conn->updateBDD("START TRANSACTION");

            $champsDocument['id'] = $champs['Id'];
            $champsDocument['titre'] = $champs['Titre'];
            $champsDocument['image'] = $champs['Image'];
            $champsDocument['idGenre'] = $champs['IdGenre'];
            $champsDocument['idPublic'] = $champs['IdPublic'];
            $champsDocument['idRayon'] = $champs['IdRayon'];
            $result1 = $this->insertOneTupleOneTable("document", $champsDocument);
            
            $champsLivres_dvd['id'] = $champs['Id'];
            $result2 = $this->insertOneTupleOneTable("livres_dvd", $champsLivres_dvd);

            $champsDvd['id'] = $champs['Id'];
            $champsDvd['synopsis'] = $champs['Synopsis'];
            $champsDvd['realisateur'] = $champs['Realisateur'];
            $champsDvd['duree'] = $champs['Duree'];
            $result3 = $this->insertOneTupleOneTable("dvd", $champsDvd);

            $this->conn->updateBDD("COMMIT");

            return $result1 + $result2 + $result3;
        } catch (Exception $e) {

            $this->conn->updateBDD("ROLLBACK");
            return 0;
        }
    }
    
    private function ModifierDvd(?array $champs): int {
        

        if (empty($champs)) {
            return 0;
        }

        try {
            $this->conn->updateBDD("START TRANSACTION");

            $champsId['id'] = $champs['Id'];
            $champId = implode("", $champsId);
            $champsDocument['titre'] = $champs['Titre'];
            $champsDocument['image'] = $champs['Image'];
            $champsDocument['idGenre'] = $champs['IdGenre'];
            $champsDocument['idPublic'] = $champs['IdPublic'];
            $champsDocument['idRayon'] = $champs['IdRayon'];
            $result1 = $this->updateOneTupleOneTable("document", $champId, $champsDocument);
            
            $champsDvd['synopsis'] = $champs['Synopsis'];
            $champsDvd['realisateur'] = $champs['Realisateur'];
            $champsDvd['duree'] = $champs['Duree'];
            $result3 = $this->updateOneTupleOneTable("dvd", $champId, $champsDvd);

            $this->conn->updateBDD("COMMIT");

            return $result1 + $result3;
        } catch (Exception $e) {

            $this->conn->updateBDD("ROLLBACK");
            return 0;
        }
    }
    
    private function SupprimerDvd(?array $champs): int {

        if (empty($champs)) {
            return 0;
        }

        try {
            $this->conn->updateBDD("START TRANSACTION");
            
            $champsDocument['id'] = $champs['Id'];
            $champsDocument['titre'] = $champs['Titre'];
            $champsDocument['image'] = $champs['Image'];
            $champsDocument['idGenre'] = $champs['IdGenre'];
            $champsDocument['idPublic'] = $champs['IdPublic'];
            $champsDocument['idRayon'] = $champs['IdRayon'];
            $result1 = $this->deleteTuplesOneTable("document", $champsDocument);
            
            $champsLivres_dvd['id'] = $champs['Id'];
            $result2 = $this->deleteTuplesOneTable("livres_dvd", $champsLivres_dvd);

            $champsDvd['id'] = $champs['Id'];
            $champsDvd['synopsis'] = $champs['Synopsis'];
            $champsDvd['realisateur'] = $champs['Realisateur'];
            $champsDvd['duree'] = $champs['Duree'];
            $result3 = $this->deleteTuplesOneTable("dvd", $champsDvd);
            
            $this->conn->updateBDD("COMMIT");

            return $result1 + $result2 + $result3;
        } catch (Exception $e) {

            $this->conn->updateBDD("ROLLBACK");
            return 0;
        }
    }
    
    private function AjouterRevue(?array $champs): int {
        
        if (empty($champs)) {
            return 0;
        }

        try {
            $this->conn->updateBDD("START TRANSACTION");

            $champsDocument['id'] = $champs['Id'];
            $champsDocument['titre'] = $champs['Titre'];
            $champsDocument['image'] = $champs['Image'];
            $champsDocument['idGenre'] = $champs['IdGenre'];
            $champsDocument['idPublic'] = $champs['IdPublic'];
            $champsDocument['idRayon'] = $champs['IdRayon'];
            $result1 = $this->insertOneTupleOneTable("document", $champsDocument);

            $champsRevue['id'] = $champs['Id'];
            $champsRevue['periodicite'] = $champs['Periodicite'];
            $champsRevue['delaiMiseADispo'] = $champs['DelaiMiseADispo'];
            $result2 = $this->insertOneTupleOneTable("revue", $champsRevue);

            $this->conn->updateBDD("COMMIT");

            return $result1 + $result2;
        } catch (Exception $e) {

            $this->conn->updateBDD("ROLLBACK");
            return 0;
        }
    }
    
    private function ModifierRevue(?array $champs): int {
        

        if (empty($champs)) {
            return 0;
        }

        try {
            $this->conn->updateBDD("START TRANSACTION");

            $champsId['id'] = $champs['Id'];
            $champId = implode("", $champsId);
            $champsDocument['titre'] = $champs['Titre'];
            $champsDocument['image'] = $champs['Image'];
            $champsDocument['idGenre'] = $champs['IdGenre'];
            $champsDocument['idPublic'] = $champs['IdPublic'];
            $champsDocument['idRayon'] = $champs['IdRayon'];
            $result1 = $this->updateOneTupleOneTable("document", $champId, $champsDocument);
            
            $champsRevue['periodicite'] = $champs['Periodicite'];
            $champsRevue['delaMiseADispo'] = $champs['DelaiMiseADispo'];
            $result2 = $this->updateOneTupleOneTable("revue", $champId, $champsRevue);

            $this->conn->updateBDD("COMMIT");

            return $result1 + $result2;
        } catch (Exception $e) {

            $this->conn->updateBDD("ROLLBACK");
            return 0;
        }
    }
    
    private function SupprimerRevue(?array $champs): int {

        if (empty($champs)) {
            return 0;
        }

        try {
            $this->conn->updateBDD("START TRANSACTION");
            
            $champsDocument['id'] = $champs['Id'];
            $champsDocument['titre'] = $champs['Titre'];
            $champsDocument['image'] = $champs['Image'];
            $champsDocument['idGenre'] = $champs['IdGenre'];
            $champsDocument['idPublic'] = $champs['IdPublic'];
            $champsDocument['idRayon'] = $champs['IdRayon'];
            $result1 = $this->deleteTuplesOneTable("document", $champsDocument);
            
            $champsRevue['id'] = $champs['Id'];
            $champsRevue['periodicite'] = $champs['Periodicite'];
            $champsRevue['delaiMiseADispo'] = $champs['DelaiMiseADispo'];
            $result2 = $this->deleteTuplesOneTable("revue", $champsRevue);
            
            $this->conn->updateBDD("COMMIT");

            return $result1 + $result2;
        } catch (Exception $e) {

            $this->conn->updateBDD("ROLLBACK");
            return 0;
        }
    }
    
    /**
     * récupération de toutes les commandes d'un document
     * @param string $champs id du document concerné
     * @return lignes de la requete
     */
    public function selectAllCommandesDocument(?array $champs) : ?array {
        $sql = "SELECT cd.*, c.dateCommande, c.montant, s.libelle 
            FROM commandedocument cd
            JOIN commande c ON cd.id = c.id
            JOIN suivi s ON cd.idSuivi = s.id
            WHERE cd.idLivreDvd = :idLivreDvd";
            $champNecessaire['idLivreDvd'] = $champs['Id'];

            return $this->conn->queryBDD($sql, $champNecessaire);
    }
    
    private function getAllSuivis() : ?array{
        $requete = "SELECT s.* FROM suivi s";		
        return $this->conn->queryBDD($requete);
    }
    
    private function selectAllCommande(?array $champs) : ?array{
        $champNecessaire['id'] = $champs['Id'];
        $requete = "SELECT * FROM commande WHERE id = :id";		
        return $this->conn->queryBDD($requete, $champNecessaire);
    }
    
    private function AjouterCommande(?array $champs): int { 

        if (empty($champs)) {
            return 0;
        }

        try {
            $this->conn->updateBDD("START TRANSACTION");
            $champsCommande['id'] = $champs['Id'];
            $champsCommande['dateCommande'] = $champs['DateCommande'];
            $champsCommande['montant'] = $champs['Montant'];
            $result1 = $this->insertOneTupleOneTable("commande", $champsCommande);

            $this->conn->updateBDD("COMMIT");

            return $result1;
        } catch (Exception $e) {

            $this->conn->updateBDD("ROLLBACK");
            return 0;
        }
    }
    
    private function AjouterCommandeDocument(?array $champs): int {

        if (empty($champs)) {
            return 0;
        }

        try {
            $this->conn->updateBDD("START TRANSACTION");
            $champsCommandeDocument['id'] = $champs['Id'];
            $champsCommandeDocument['nbExemplaire'] = $champs['NbExemplaire'];
            $champsCommandeDocument['idLivreDvd'] = $champs['IdLivreDvd'];
            $champsCommandeDocument['idSuivi'] = $champs['IdSuivi'];
            $result1 = $this->insertOneTupleOneTable("commandedocument", $champsCommandeDocument);

            $this->conn->updateBDD("COMMIT");

            return $result1;
        } catch (Exception $e) {

            $this->conn->updateBDD("ROLLBACK");
            return 0;
        }
    }
    
    private function ModifierEtatCommande(?array $champs): int {

        if (empty($champs)) {
            return 0;
        }

        try {
            $this->conn->updateBDD("START TRANSACTION");
            $champsId['id'] = $champs['Id'];
            $champId = implode("", $champsId);
            $champsEtatCommande['idSuivi'] = $champs['IdSuivi'];
            
            $result1 = $this->updateOneTupleOneTable("commandedocument", $champId, $champsEtatCommande);


            $this->conn->updateBDD("COMMIT");

            return $result1;
        } catch (Exception $e) {

            $this->conn->updateBDD("ROLLBACK");
            return 0;
        }
    }
    
    private function SupprimerCommandeDocument(?array $champs): int {

        if (empty($champs)) {
            return 0;
        }

        try {
            $this->conn->updateBDD("START TRANSACTION");
            
            $champsCommandeDocument['id'] = $champs['Id'];
            $champsCommandeDocument['nbExemplaire'] = $champs['NbExemplaire'];
            $champsCommandeDocument['idLivreDvd'] = $champs['IdLivreDvd'];
            $champsCommandeDocument['idSuivi'] = $champs['IdSuivi'];
            $result1 = $this->deleteTuplesOneTable("commandedocument", $champsCommandeDocument);
            
            $champsCommande['dateCommande'] = $champs['DateCommande'];
            $champsCommande['id'] = $champs['Id'];
            $champsCommande['montant'] = $champs['Montant'];
            $result2 = $this->deleteTuplesOneTable("commande", $champsCommande);

            $this->conn->updateBDD("COMMIT");

            return $result1 + $result2;
        } catch (Exception $e) {

            $this->conn->updateBDD("ROLLBACK");
            return 0;
        }
    }
    
    public function selectAllAbonnementsRevues($champs){
        $champsId['id'] = $champs['Id'];
        $req = "select c.id, c.dateCommande, c.montant, a.dateFinAbonnement, a.idRevue  ";
        $req .= "from commande c join abonnement a ON c.id=a.id ";
        $req .= "where a.idRevue= :id  ";
        $req .= "order by c.dateCommande DESC  ";
        return $this->conn->queryBDD($req, $champsId);
    }
    
    public function selectAllAbonnementsEcheance(){
        $req ="select a.dateFinAbonnement, a.idRevue, d.titre ";
        $req .="from abonnement a ";
        $req .="join revue r on a.idRevue = r.id ";
        $req .="join document d on r.id = d.id ";
        $req .="where datediff(current_date(), a.dateFinAbonnement) < 30 ";
        $req .="order by a.dateFinAbonnement ASC; ";
        return $this->conn->queryBDD($req);
    }
    
    private function AjouterAbonnementRevue(?array $champs): int {
        if (empty($champs)) {
            return 0;
        }

        try {
            $this->conn->updateBDD("START TRANSACTION");
            $champsAbonnement['id'] = $champs['Id'];
            $champsAbonnement['dateFinAbonnement'] = $champs['DateFinAbonnement'];
            $champsAbonnement['idRevue'] = $champs['IdRevue'];
            $result1 = $this->insertOneTupleOneTable("abonnement", $champsAbonnement);

            $this->conn->updateBDD("COMMIT");

            return $result1;
        } catch (Exception $e) {

            $this->conn->updateBDD("ROLLBACK");
            return 0;
        }
    }
    
    private function SupprimerAbonnementRevue(?array $champs): int {

        if (empty($champs)) {
            return 0;
        }

        try {
            $this->conn->updateBDD("START TRANSACTION");
            
            $champsAbonnement['id'] = $champs['Id'];
            $champsAbonnement['dateFinAbonnement'] = $champs['DateFinAbonnement'];
            $champsAbonnement['idRevue'] = $champs['IdRevue'];
            $result1 = $this->deleteTuplesOneTable("abonnement", $champsAbonnement);
            
            $champsCommande['dateCommande'] = $champs['DateCommande'];
            $champsCommande['id'] = $champs['Id'];
            $champsCommande['montant'] = $champs['Montant'];
            $result2 = $this->deleteTuplesOneTable("commande", $champsCommande);
            
            $this->conn->updateBDD("COMMIT");

            return $result1 + $result2;
        } catch (Exception $e) {

            $this->conn->updateBDD("ROLLBACK");
            return 0;
        }
    }
}
