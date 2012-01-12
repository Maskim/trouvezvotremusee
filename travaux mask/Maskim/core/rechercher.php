<?php
	require_once ("./classes/ControleurConnexionPers.php");
	
	$recherche=$_POST['recherche'];
	
	$a = new ControleurConnexion;
	$b = $a-> consulter("*","musee","","nom LIKE '%$recherche%'","","","","");
	
	while($list_musee=mysql_fetch_array($b)){
		echo "<li>".$list_musee['nom']."</li>";
	}
?>