<?php
//acces au controller parent pour l heritage
namespace App\Controllers;
use CodeIgniter\Controller;

//=========================================================================================
//définition d'une classe Controleur (meme nom que votre fichier Controleur.php) 
//héritée de Controller et permettant d'utiliser les raccoucis et fonctions de CodeIgniter
//  Attention vos Fichiers et Classes Controleur et Modele doit commencer par une Majuscule 
//  et suivre par des minuscules
//=========================================================================================

class Controleur extends BaseController {

//=====================================================================
//Fonction index correspondant au Controleur frontal (ou index.php) en MVC libre
//=====================================================================
public function index(){
			
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		//code exemple controleur frontal
		// Hors Forfait
		if (isset($_POST["km"])) {
					$this->enregistrer();
		}
		elseif (isset($_POST["date"])) {
					$this->enregistrer2();
		}
		elseif (isset($_POST["login"]) && isset($_POST["mdp"])) {
					$this->testlogin($_POST["login"], $_POST["mdp"]);
		}
				elseif (isset($_GET["action"]) && $_GET["action"] == "connexion") {
					$this->connexion();
				}
				elseif (isset($_GET["action"]) && $_GET["action"] == "notedefrais") {
					$this->notedefrais();
				}
				elseif (isset($_GET["action"]) && $_GET["action"] == "consulterfiche") {
					$this->consulterfiche();
				}
				elseif (isset($_GET["action"]) && $_GET["action"] == "consulterficheselection") {
					$this->consulterficheselection($_GET["mois"]);
				}
				elseif (isset($_GET["action"]) && $_GET["action"] == "renseignerfiche") {
					$this->renseignerfiche();
				}
				elseif (isset($_GET["action"]) && $_GET["action"] == "supprimerfiche") {
					$this->supprimerfiche();
				}
				elseif (isset($_GET["action"]) && $_GET["action"] == "supprimerhorsforfait") {
					$this->supprimerhorsforfait();
				}
				elseif (isset($_GET["action"]) && $_GET["action"] == "monprofil") {
					$this->monprofil();
				}
				elseif (isset($_GET["action"]) && $_GET["action"] == "ensavoirplus") {
					$this->ensavoirplus();
				}
				elseif (isset($_GET["action"]) && $_GET["action"] == "mentionlegale") {
					$this->mentionlegale();
				}
				else {
					$this->accueil();
				}
		
		//=========================
		//fin du controleur frontal
		//=========================
}


//======================================================
// Code du controleur simple (ex fichier Controleur.php)
//======================================================

//========================================================
// Action 1 : Affiche la liste de tous les billets du blog
// =======================================================

// Vérifie qu'un mois est au bon format AAAA-MM, sinonx on prend le mois qui est déjà en cours
private function moisValide($mois) {
	return preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string) $mois) ? $mois : date('Y-m');
}

public function enregistrer() {
	if (!isset($_SESSION['id'])) {
		$data['erreur'] = "Veuillez vous connecter.";
		echo view('vueConnexion', $data);
		return;
	}
	$Modele = new \App\Models\Modele();
	$id   = $_SESSION['id'];
	$mois = $this->moisValide($_POST["mois"] ?? '');
	$Modele->getRenseignerFiche7($id, $mois, "KM",  (int) $_POST["km"]);
	$Modele->getRenseignerFiche7($id, $mois, "NUI", (int) $_POST["nuit"]);
	$Modele->getRenseignerFiche7($id, $mois, "ETP", (int) $_POST["etape"]);
	$Modele->getRenseignerFiche7($id, $mois, "REP", (int) $_POST["repas"]);
	$this->renseignerfiche("Frais forfaitaires enregistrés avec succès !", $mois);
}

public function enregistrer2() {
	if (!isset($_SESSION['id'])) {
		$data['erreur'] = "Veuillez vous connecter.";
		echo view('vueConnexion', $data);
		return;
	}
	$Modele = new \App\Models\Modele();
	$mois = $this->moisValide($_POST["mois"] ?? '');
	$Modele->getRenseignerFiche9($_SESSION['id'], $mois, $_POST["libelle"], $_POST["date"], $_POST["montant"]);
	$this->renseignerfiche("Frais hors forfait ajouté avec succès !", $mois);
}

// ====================
// Supprime d'une fiche
// ====================
public function supprimerfiche() {
	if (!isset($_SESSION['id'])) {
		$data['erreur'] = "Veuillez vous connecter.";
		echo view('vueConnexion', $data);
		return;
	}
	$Modele = new \App\Models\Modele();
	$id   = $_SESSION['id'];
	$mois = $this->moisValide($_POST["mois"] ?? '');

	// On ne supprime que les fiches encore en saisie (état CR)
	$etat = $Modele->getRenseignerFiche6($id, $mois);
	if (!isset($etat[0]->idEtat) || $etat[0]->idEtat !== 'CR') {
		$this->renseignerfiche(null, $mois, "Cette fiche ne peut pas être supprimée (déjà clôturée ou validée).");
		return;
	}

	$Modele->supprimerFicheComplete($id, $mois);
	$this->renseignerfiche("Fiche " . $mois . " supprimée.", date('Y-m'));
}

// ====================================
// Suppression d'une ligne hors forfait
// ====================================
public function supprimerhorsforfait() {
	if (!isset($_SESSION['id'])) {
		$data['erreur'] = "Veuillez vous connecter.";
		echo view('vueConnexion', $data);
		return;
	}
	$Modele = new \App\Models\Modele();
	$id   = $_SESSION['id'];
	$mois = $this->moisValide($_POST["mois"] ?? '');

	$etat = $Modele->getRenseignerFiche6($id, $mois);
	if (!isset($etat[0]->idEtat) || $etat[0]->idEtat !== 'CR') {
		$this->renseignerfiche(null, $mois, "Cette fiche ne peut plus être modifiée.");
		return;
	}

	$Modele->supprimerLigneHorsForfait((int) ($_POST["id_hf"] ?? 0), $id, $mois);
	$this->renseignerfiche("Frais hors forfait supprimé.", $mois);
}

public function accueil() {
		echo view('vueAccueil');
}

public function connexion() {
		echo view('vueConnexion'); 
}

public function notedefrais() {
		echo view('vueNoteDeFrais'); 
}

// ====================
// Consulter les fiches
// ====================
public function consulterfiche() {
	$Modele = new \App\Models\Modele();
	if (!session_id()) {
				session_start(); 
	}
	$donnes = $Modele->getFicheFrais3($_SESSION['id'], date('Y-m'));
	// Frais Forfait
	$data['ligneForfait'] = $donnes;
	$data['moisLabel'] = date('Y-m');
	$data['etatLabel'] = 'CR'; 
	$donnees2 = $Modele->getFicheFrais4($_SESSION['id'], date('Y-m'));
	// Frais Hors Forfait
	$data['ligneHorsForfait'] = $donnees2;
	$data['fiches'] = $Modele->getFicheFrais5($_SESSION['id']);
	$data['totalForfait'] = array_sum(array_column($donnes, 'total'));
	$data['totalHorsForfait'] = array_sum(array_column($donnees2, 'montant'));
	echo view('vueConsulterFiche', $data); 
}

// ==================================
// Consulter la sélection d'une fiche
// ==================================
public function consulterficheselection($mois) {
	$Modele = new \App\Models\Modele();
	$donnes = $Modele->getFicheFrais3($_SESSION['id'], $mois);
	// Frais Forfait
	$data['ligneForfait'] = $donnes;
	$data['moisLabel'] = $mois;
	$data['etatLabel'] = 'CR'; 
	$donnees2 = $Modele->getFicheFrais4($_SESSION['id'], $mois);
	// Frais Hors Forfait
	$data['ligneHorsForfait'] = $donnees2;
	$donnees3 = $Modele->getFicheFrais5($_SESSION['id']);
	$data['fiches'] = $donnees3;

	$data['totalForfait'] = array_sum(array_column($Modele->getFicheFrais3($_SESSION['id'], $mois), 'total'));
	$data['totalHorsForfait'] = array_sum(array_column($Modele->getFicheFrais4($_SESSION['id'], $mois), 'montant'));
	echo view('vueConsulterFiche', $data);
}

// ====================
// Renseigner une fiche
// ====================
public function renseignerfiche($succes = null, $mois = null, $erreur = null) {
	if (!isset($_SESSION['id'])) {
		$data['erreur'] = "Veuillez vous connecter.";
		echo view('vueConnexion', $data);
		return;
	}

	$Modele = new \App\Models\Modele();
	$id   = $_SESSION['id'];
	$mois = $this->moisValide($mois ?? ($_GET['mois'] ?? date('Y-m')));

	// Si la fiche de ce mois n'existe pas encore, on la crée (comme à la connexion)
	$existe = $Modele->testFicheFrais($id, $mois);
	if (!isset($existe[0]->mois)) {
		$Modele->creerFicheFrais($id, $mois, date('Y-m-d'));
		foreach (["KM", "NUI", "REP", "ETP"] as $frais) {
			$Modele->insereLigneFraisForfait($id, $mois, $frais);
		}
	}

	// Quantités de la fiche : ['KM' => 120, 'NUI' => 0, ...]
	$quantites = [];
	foreach ($Modele->getFicheFrais3($id, $mois) as $ligne) {
		$quantites[$ligne->idFraisForfait] = $ligne->quantite;
	}
	
	$data['quantites']        = $quantites;
	$data['ligneHorsForfait'] = $Modele->getFicheFrais4($id, $mois);
	$data['fiches']           = $Modele->getFicheFrais5($id);
	$data['moisLabel']        = $mois;
	if ($succes !== null) {
		$data['succes'] = $succes;
	}
	if ($erreur !== null) {
		$data['erreur'] = $erreur;
	}
	echo view('vueRenseignerFiche', $data);
}

// ==========
// Mon profil
// ==========
public function monprofil() {
    if (!isset($_SESSION['id'])) {
        $data['erreur'] = "Veuillez vous connecter.";
        echo view('vueConnexion', $data);
        return;
    }
    $Modele = new \App\Models\Modele();
    $resultat = $Modele->getMonProfilVisiteur($_SESSION['id']);
    $data['user'] = (array) $resultat[0];
    echo view('vueMonProfil', $data);
}

public function ensavoirplus() {
		echo view('vueEnSavoirPlus'); 
}

public function mentionlegale() {
		echo view('vueMentionLegale'); 
}

// ============================================
// Action 2 : Affiche les détails sur un billet
// ============================================
public function testlogin($login, $mdp) {
		//================
		//acces au modele
		//================
		$Modele = new \App\Models\Modele();
		
		//===============================
		//Appel d'une fonction du Modele
		//===============================	
		$donnees = $Modele->getLoginVisiteur($login, $mdp);
		if (isset($donnees[0]->id)) {
			// Connexion réussie, rediriger vers la page d'accueil
			if (!session_id()) {
				session_start(); 
			}
			$_SESSION['id'] = $donnees[0]->id; 
			$_SESSION['user_nom'] = $donnees[0]->nom;
			$_SESSION['user_prenom'] = $donnees[0]->prenom;
			$donnes2 = $Modele->testFicheFrais($_SESSION['id'], date('Y-m'));
			if (!isset($donnes2[0]->mois)) {
				$Modele->creerFicheFrais($_SESSION['id'], date('Y-m'), date('Y-m-d'));
				$Modele->insereLigneFraisForfait($_SESSION['id'], date('Y-m'), "KM");
				$Modele->insereLigneFraisForfait($_SESSION['id'], date('Y-m'), "NUI");
				$Modele->insereLigneFraisForfait($_SESSION['id'], date('Y-m'), "REP");
				$Modele->insereLigneFraisForfait($_SESSION['id'], date('Y-m'), "ETP");
			}
			$data['login'] = $login; 
			echo view('vueAccueil', $data);
		} else {
			// Connexion échouée, afficher un message d'erreur
			$data['erreur'] = "Identifiant ou mot de passe incorrect.";
			echo view('vueConnexion', $data);
		}
		
		
		//=================================================================================
		//!!! Création d'un jeu de données $data sécurisé pouvant etre passé à la vue
		//!!! on créé une variable qui récupère le résultat de la requete : $getBillets();
		//=================================================================================
		$data['resultat']=$donnees;
  		
		//==========================================
		//on charge la vue correspondante
		//et on envoie le jeu de données $data à la vue
		//la vue aura acces a une variable $resultat
		//==========================================
  		// echo view('vueBillet',$data);
  
}

// ==================
// Affiche une erreur
// ==================
public function erreur($msgErreur) {
	$data['erreur'] = $msgErreur;
	echo view('vueErreur.php', $data);
}

//==========================
//Fin du code du controleur simple
//===========================

//fin de la classe
}
?>