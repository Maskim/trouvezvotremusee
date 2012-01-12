<?php
	require_once("./classes/ControleurConnexionPers.php");
	if(isset($_POST) && $_POST['ajout_departement']="envoyer"){
		$nomdepartement=$_POST['nomdepartement'];
		$idregion=$_POST['idregion'];
		
		if(isset($nomdepartement)){
			$a = new ControleurConnexion;
			$b = $a->inserer("departement","nomdep, idregion","'$nomdepartement', '$idregion'");
			header ("Location: ../index.php");
		}
	}
?>