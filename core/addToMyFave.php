<?php
	session_start();

	if(isset($_SESSION['connexion']) && $_SESSION['connexion']){ 
		require_once("./classes/ControleurConnexionPers.php");
		require_once("./fonctions.php");

		$a = new ControleurConnexion();

		$idUser = $_SESSION['iduser'];
		$idmusee = $_GET['id'];

		$sql = $a->consulter("COUNT(*)", "favori", "", "musee = '$idmusee' AND util = '$idUser'", "", "", "", "", "");
		$isFav = mysql_fetch_row($sql);

		print_r($isFav);

		if($isFav[0] == 0)
			$a->inserer("favori", "musee, util", "'$idmusee', '$idUser'");

		$sql = $a->consulter("nom", "musee", "", "idmusee = '$idmusee'", "", "", "", "", "");
		$musee = mysql_fetch_row($sql);

		$musee = prepareString($musee[0]);

		$lien = prepareString(utf8_encode($musee));
		$lien = "../musees-" .$lien. ".html";

		header("Location: $lien");

	}else{
		
	}
?>