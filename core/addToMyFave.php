<?php
	session_start();

	require_once("./classes/ControleurConnexionPers.php");
	require_once("./fonctions.php");

	if(isset($_SESSION['connexion']) && $_SESSION['connexion']){ 

		$a = new ControleurConnexion();

		$idUser = $_SESSION['iduser'];
		$idmusee = $_GET['id'];

		$sql = $a->consulter("COUNT(*)", "favori", "", "musee = '$idmusee' AND util = '$idUser'", "", "", "", "", "");
		$isFav = mysql_fetch_row($sql);

		print_r($isFav);

		if($isFav[0] == 0)
			$a->inserer("favori", "musee, util", "'$idmusee', '$idUser'");

		$sql = $a->consulter("nom, nomville", "musee, ville", "", "idmusee = '$idmusee' AND musee.idville = ville.idville", "", "", "", "", "");
		$musee = mysql_fetch_row($sql);

		$lienmusee = prepareString(utf8_encode($musee[0]));
		$ville = prepareString(utf8_encode($musee[1]));

		$lien = $lienmusee . '-' . $ville;
		$lien = "../musees-" .$lien. ".html";

		header("Location: $lien");

	}else{
		$a = new ControleurConnexion();

		$idmusee = $_GET['id'];

		$lien = "./addToMyFave.php?id=$idmusee";

		$_SESSION['redirect_to'] = $lien;
		$_SESSION['type_redirect'] = 'addToMyFave';
		$_SESSION['id_musee'] = $idmusee;

		header("Location: ../connexion.html");
	}
?>