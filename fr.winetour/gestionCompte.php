<?php 
if(isset($_POST['wtb_inscription']) && !empty($_POST['wtb_inscription'])){
	$prob = false;
	if(isset($_POST['wtb_nomcompte']) && !empty($_POST['wtb_nomcompte']))
		$nomcompte = $_POST['wtb_nomcompte'];
	else
		$prob = true;

	if(isset($_POST['wtb_pdw']) && !empty($_POST['wtb_pdw']))
		$mdp = $_POST['wtb_pdw'];
	else
		$prob = true;

	if(isset($_POST['wtb_vmdp']) && !empty($_POST['wtb_vmdp']))
		$vmdp = $_POST['wtb_vmdp'];
	else
		$prob = true;

	if(isset($_POST['wtb_name']) && !empty($_POST['wtb_name']))
		$nom = $_POST['wtb_name'];
	else
		$prob = true;

	if(isset($_POST['wtb_prenom']) && !empty($_POST['wtb_prenom']))
		$prenom = $_POST['wtb_prenom'];
	else
		$prob = true;

	if(isset($_POST['wtb_mail']) && !empty($_POST['wtb_mail']))
		$mail = $_POST['wtb_mail'];
	else
		$prob = true;

	if(isset($_POST['wtb_phone']) && !empty($_POST['wtb_phone']))
		$phone = $_POST['wtb_phone'];
	else
		$prob = true;

	if($prob)
		echo "problème sur les varaibles";
	else if($mdp != $vmdp){
		echo "mot de passe ne correspondent pas !";
	}else{

		require_once('./classes/ControleurConnexionPers.php');

		$controleur = new ControleurConnexion();
		$controleur -> inserer("wp_customer","","");

		$select = $controleur->consulter("id_customer","wp_customer","","name=$name AND mail=$mail","","","","","");
		$id_customer = mysql_fetch_row($select);

		$controleur -> inserer("wtb_users","nomcompte, mdp, id_customer","$nomcompte, $nom, $mdp");
	}
}else{
	echo "Vous n'avez pas accès à cette partie !";
}

?>