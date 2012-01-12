<?php
	function listeDep($idregion){
		$a = new ControleurConnexion;
		$dep = $a->consulter("*","departement","","idregion = ".$idregion."","","","","nomdep","");
		return $dep;
	}
	
	function listeVille($iddep){
		$a = new ControleurConnexion;
		$ville = $a -> consulter("*","ville","","iddep='$iddep'","","","","nomville","");
		return $ville;
	}
?>