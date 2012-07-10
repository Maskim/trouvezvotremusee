<?php
	require_once("./classes/ControleurConnexionPers.php");

	if(isset($_POST['id_musee']) && isset($_POST['id_user'])){
		$id_musee = $_POST['id_musee'];
		$id_user = $_POST['id_user'];

		$a = new ControleurConnexion();
		$a->supprimer('favori',"musee = '$id_musee' AND util = '$id_user'",'','');
	}
?>