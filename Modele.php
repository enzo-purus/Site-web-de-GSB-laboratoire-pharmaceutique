<?php
//acces au Modele parent pour l heritage
namespace App\Models;
use CodeIgniter\Model;

//=========================================================================================
//définition d'une classe Modele (meme nom que votre fichier Modele.php) 
//héritée de Modele et permettant d'utiliser les raccoucis et fonctions de CodeIgniter
//  Attention vos Fichiers et Classes Controleur et Modele doit commencer par une Majuscule 
//  et suivre par des minuscules
//=========================================================================================
class Modele extends Model {






//==========================
// Code du modele
//==========================

//=========================================================================
// Fonction 1
// récupère les données BDD dans une fonction getBillets
// Renvoie la liste de tous les billets, triés par identifiant décroissant
//=========================================================================
public function getLoginVisiteur($login, $mdp) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT * FROM Visiteur WHERE login = ? AND mdp = ?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$login, $mdp]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}

//=====================================================================
// Supprime une fiche complète : frais hors forfait, frais forfaitaires, puis la fiche
//=====================================================================
public function supprimerFicheComplete($id, $mois) {
	$db = db_connect();
	$db->transStart();
	$db->query('DELETE FROM LigneFraisHorsForfait WHERE idVisiteur = ? AND mois = ?', [$id, $mois]);
	$db->query('DELETE FROM LigneFraisForfait WHERE idVisiteur = ? AND mois = ?', [$id, $mois]);
	$db->query('DELETE FROM FicheFrais WHERE idVisiteur = ? AND mois = ?', [$id, $mois]);
	$db->transComplete();
	return $db->transStatus();
}

//=====================================================================
// Supprime un seul frais hors forfait (uniquement s'il appartient au visiteur)
//=====================================================================
public function supprimerLigneHorsForfait($idLigne, $id, $mois) {
	$db = db_connect();
	return $db->query('DELETE FROM LigneFraisHorsForfait WHERE id = ? AND idVisiteur = ? AND mois = ?', [$idLigne, $id, $mois]);
}

public function testFicheFrais($id, $mois) {
    //==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT * FROM FicheFrais WHERE idVisiteur = ? AND mois = ?';
	
//=============================
// execution de la requete sql
//=============================	
     $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
}


public function creerFicheFrais($id, $mois, $date) {
    //==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
$sql = 'INSERT INTO FicheFrais (idVisiteur, mois, nbJustificatifs, montantValide, dateModif, idEtat) VALUES (?, ?, 0, 0, ?, "CR") ON DUPLICATE KEY UPDATE idVisiteur=?, mois=?, dateModif=date("Y-m-d"), idEtat="CR"'; 
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois, $date, $id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
}


public function insereLigneFraisForfait($id, $mois, $frais) {
    //==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'INSERT INTO LigneFraisForfait (idVisiteur, mois, idFraisForfait, quantite) VALUES (?, ?, ?, 0)'; 

//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois, $frais]);

//=============================
// récupération des données de la requete sql
//=============================

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
}


public function getTotalHorsForfait($id, $mois) {
    //==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT SUM(montant) AS total FROM LigneFraisHorsForfait WHERE idVisiteur = ? AND mois = ? GROUP BY mois'; 

//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
    $resultat = $resultat->getResult();
//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
}


public function getTotalFraisForfait($id, $mois) {
    //==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT SUM(quantite) AS total FROM LigneFraisForfait WHERE idVisiteur = ? AND mois = ? GROUP BY mois'; 

//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================

    $resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
}

public function getFicheFrais($id, $anneeMin) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT f.mois, f.idEtat, e.libelle AS etat FROM FicheFrais f JOIN Etat e ON f.idEtat = e.id WHERE f.idVisiteur = ? AND f.mois >= ? ORDER BY f.mois DESC';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $anneeMin]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getFicheFrais2($id, $mois) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT f.*, e.libelle AS etat FROM FicheFrais f JOIN Etat e ON f.idEtat = e.id WHERE f.idVisiteur = ? AND f.mois = ?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getFicheFrais3($id, $mois) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT lff.quantite, lff.idFraisForfait, ff.libelle, ff.montant, (lff.quantite * ff.montant) AS total FROM LigneFraisForfait lff JOIN FraisForfait ff ON lff.idFraisForfait = ff.id WHERE lff.idVisiteur = ? AND lff.mois = ?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getFicheFrais4($id, $mois) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT * FROM LigneFraisHorsForfait WHERE idVisiteur = ? AND mois = ? ORDER BY date ASC';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getFicheFrais5($id) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT mois FROM FicheFrais WHERE idVisiteur = ? ORDER BY mois ASC';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getVisiteurAdresse($id) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'UPDATE visiteur SET adresse=:adresse, cp=:cp, ville=:ville WHERE id=?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getMdpVisiteur($id) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT mdp FROM visiteur WHERE id = ?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getMdpVisiteur2($id) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'UPDATE visiteur SET mdp=:mdp WHERE id=?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getMonProfilVisiteur($id) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    // $sql = 'SELECT * FROM visiteur WHERE id = :id';
    $sql = 'SELECT id, nom, prenom, login, adresse, cp, ville, dateEmbauche FROM Visiteur WHERE id = ?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getRenseignerFiche($id) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT f.mois, f.idEtat, e.libelle AS etat FROM FicheFrais f JOIN Etat e ON f.idEtat = e.id WHERE f.idVisiteur = ? ORDER BY f.mois DESC';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getRenseignerFiche2() { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT * FROM FraisForfait';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getRenseignerFiche3($id, $mois) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT COUNT(*) FROM FicheFrais WHERE idVisiteur=? AND mois=?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getRenseignerFiche4($id, $mois) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = "INSERT INTO FicheFrais (idVisiteur, mois, nbJustificatifs, montantValide, dateModif, idEtat) VALUES (?, ?, 0, 0, CURDATE(), 'CR')";
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getRenseignerFiche5($id, $mois, $idf) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'INSERT INTO LigneFraisForfait (idVisiteur, mois, idFraisForfait, quantite) VALUES (?, ?, ?, 0)';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois, $idf]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}

public function getRenseignerFiche6($id, $mois) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT idEtat FROM FicheFrais WHERE idVisiteur=? AND mois=?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getRenseignerFiche7($id, $mois, $idf, $q) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'UPDATE LigneFraisForfait SET quantite=? WHERE idVisiteur=? AND mois=? AND idFraisForfait=?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$q, $id, $mois, $idf]);

//=============================
// récupération des données de la requete sql
//=============================

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getRenseignerFiche8($id, $mois) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'DELETE FROM LigneFraisHorsForfait WHERE idVisiteur=? AND mois=?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getRenseignerFiche9($id, $mois, $lib, $date, $montant) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'INSERT INTO LigneFraisHorsForfait (idVisiteur, mois, libelle, date, montant) VALUES (?, ?, ?, ?, ?)';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois, $lib, $date, $montant]);

//=============================
// récupération des données de la requete sql
//=============================

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getRenseignerFiche10($id, $mois) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'UPDATE FicheFrais SET dateModif=CURDATE() WHERE idVisiteur=? AND mois=?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getRenseignerFiche11($id, $mois) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT f.*, e.libelle AS etat FROM FicheFrais f JOIN Etat e ON f.idEtat = e.id WHERE f.idVisiteur=? AND f.mois=?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getRenseignerFiche12($id, $mois) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT lff.*, ff.libelle, ff.montant FROM LigneFraisForfait lff JOIN FraisForfait ff ON lff.idFraisForfait = ff.id WHERE lff.idVisiteur=? AND lff.mois=?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getRenseignerFiche13($id, $mois) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT * FROM LigneFraisHorsForfait WHERE idVisiteur=:id AND mois=:mois ORDER BY date';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getSupprimerFiche($id, $mois) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT idEtat FROM FicheFrais WHERE idVisiteur=? AND mois=?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getSupprimerFiche1($id, $mois) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'DELETE FROM LigneFraisForfait WHERE idVisiteur=? AND mois=?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getSupprimerFiche2($id, $mois) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'DELETE FROM LigneFraisHorsForfait WHERE idVisiteur=? AND mois=?';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


public function getSupprimerFiche3($id, $mois) { 

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
	$db = db_connect();

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'DELETE FROM FicheFrais WHERE idVisiteur=:id AND mois=:mois';
	
//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql, [$id, $mois]);

//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================	
    return $resultat;
   
}


//=========================================================================
// Fonction 2 
// récupère les données BDD dans une fonction getDetails
// Renvoie le détail d'un billet précédemment sélectionné par son id
//=========================================================================
public function getDetails($id) {
	
//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
    $db = db_connect();	
	
//=====================================
// rédaction de la requete sql préparée
//=====================================
	$sql = 'SELECT * from T_BILLET WHERE BIL_ID=?';
	
//=====================================================
// execution de la requete sql en passant un parametre id
//=====================================================	
    $resultat = $db->query($sql, [$id]);
	
//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================		
    return $resultat;
  
}

//==========================
// Fin Code du modele
//===========================


//fin de la classe
}


?>