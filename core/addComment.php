<?php
	session_start();

	if(isset($_SESSION['niveau']) && !empty($_SESSION['niveau']) ) {
		require_once('./classes/ControleurCOnnexionPers.php');
		require_once('./fonctions.php');

		if(isset($_POST)){
			$comment = htmlspecialchars($_POST['commentaire']);
			$id_musee = $_POST['id_musee'];
			$id_user = $_SESSION['iduser'];
			$date = date("Y/m/d H:i");

			$a = new ControleurConnexion();

			$a -> inserer("commentaire", "com, idmusee, iduser, dateCom", "'$comment', '$id_musee', '$id_user', '$date'");

			$sql = $a->consulter("nom", "musee", "", "idmusee = '$id_musee'", "", "", "", "", "");
			$musee = mysql_fetch_row($sql);

			$musee = prepareString($musee[0]);

			$lien = prepareString(utf8_encode($musee));
			$lien = "../musees-" .$lien. ".html";

			header("Location: $lien");

		}else{

		}
	}else{
		echo 'not connected';
		//header("Location: ../accueil.html");
	}
?>