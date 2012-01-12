<?php
	require_once("./classes/ControleurConnexionPers.php");
	if(isset($_POST) && $_POST['ajout_region']="envoyer"){
		$nomregion=$_POST['nomregion'];
		
		if(isset($nomregion)){
			$a = new ControleurConnexion;
			$b = $a->inserer("region","nomregion","'$nomregion'");
			header ("Location: ../index.php");
		}
	}
?>
