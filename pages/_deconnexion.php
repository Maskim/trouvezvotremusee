<?php session_start(); ?>
<?php
	if($_SESSION['connexion']){
		session_destroy();
		header("Location: ./accueil.html");
	}else{
		header("Location: ./accueil.html");
	}
?>