<?php
	require_once("./classes/ControleurConnexionPers.php");
	switch ($_POST['ajout_musee'])
	{		
		case "choixregion":
			$id_region=$_POST['idregion'];
			
			
			header ("Location: ../index.php");
		break;
			
		case "choixdep":
			header ("Location: ../index.php");
		break;
		
		case "envoyer" :
			$nom_musee=$_POST['nom_musee'];
			$adresse=$_POST['adresse'];
			$tel=$_POST['tel'];
			$mail=$_POST['email'];
			$description=$_POST['description'];
			$photo=$_POST['photo'];
			$site_internet=$_POST['siteinternet'];
			
			
			if(isset($nom_musee ) && isset($adresse)){
				$ajout_musee = new ControleurConnexion; 
				$action=$ajout_musee->inserer("musee", "nom, adresse, tel, mail, description, photo, siteinternet", "'$nom_musee', '$adresse', '$tel', '$mail', '$description', '$photo', '$site_internet'");
				header ("Location: ../index.php");
			}else{
				echo "raté...";
			}
		break;
	}
?>