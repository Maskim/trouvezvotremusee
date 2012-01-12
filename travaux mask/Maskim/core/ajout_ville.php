<?php
	require_once("./classes/ControleurConnexionPers.php");
	if(isset($_POST) && $_POST['ajout_ville']="envoyer"){
		$ville=$_POST['ville'];
		$CP=$_POST['CP'];
		$iddep=$_POST['iddep'];
		
		if(isset($ville) && isset($CP)){
			$a = new ControleurConnexion;
			$b = $a->inserer("ville","nomville, CP, iddep","'$ville', '$CP', '$iddep'");
			header ("Location: ../index.php");
		}
	}
?>