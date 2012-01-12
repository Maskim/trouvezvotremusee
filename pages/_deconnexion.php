<?php
	session_start();
	if($_SESSION['connexion']){
		session_destroy();
		header("Location: ./accueil.html");
	}
?>