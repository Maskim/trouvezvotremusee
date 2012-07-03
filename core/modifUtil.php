<?php
	session_start();
	require_once("./classes/ControleurConnexionPers.php");
	require_once("./fonctions.php");

	if(isset($_SESSION['niveau']) && !empty($_SESSION['niveau']) ) {
		if(isset($_POST['type']) && $_POST['type'] == 'modifutil'){
			$idutil = htmlspecialchars($_POST['idutil']);
			$utilisateur = htmlspecialchars($_POST['login']);
			$nom = htmlspecialchars($_POST['nom']);
			$prenom = htmlspecialchars($_POST['prenom']);
			$mail = htmlspecialchars($_POST['mail']);

			if( isAlreadyExist("utilisateur", "utilisateur", $utilisateur, $idutil)){
				$error = 'Le login selectionné existe déjà, veuillez en renseignez un nouveau !';
				setcookie('error', $error, time() + 3600, "/");

			}else if(isAlreadyExist("utilisateur", "mail", $mail, $idutil) ){
				$error = 'Le mail selectionné est déjà utilisé, veuillez en renseignez un nouveau !';
				setcookie('error', $error, time() + 3600, "/");

			}else{
				$a = new ControleurConnexion;
				$sql = $a->modifier("utilisateur","utilisateur='$utilisateur', nom='$nom', prenom='$prenom', mail='$mail'","idutil='$idutil'","","");

			}
		}else if(isset($_POST['type']) && $_POST['type'] == 'modifmdp'){
			$idutil = htmlspecialchars($_POST['idutil']);
			$mdp = md5( htmlspecialchars($_POST['mdp']) );
			$nmdp = htmlspecialchars($_POST['nmdp']);
			$vmdp = htmlspecialchars($_POST['vmdp']);

			if( isGoodMdp($idutil, $mdp) ){
				if($nmdp == $vmdp){
					$nmdp = md5($nmdp);
					$a = new ControleurConnexion();
					$sql = $a->modifier("utilisateur", "mdp = '$nmdp'", "idutil = '$idutil'", "", "");
				}else{
					$error = 'Les nouveaux mots de passe ne correspondent pas !';
					setcookie('error', $error, time() + 3600, "/");
				}
			}else{
				$error = 'Votre ancien mot de passe ne correspond pas !';
				setcookie('error', $error, time() + 3600, "/");
			}
		}

		header("Location: ../moncompte.html");
	}
?>