<?php
/*
   ===========================================================
   ---- MODULE DE CONTACT		 						  ----
   ---- © Twan 				http://www.twan-diz.fr		  ----
   ---- © Maskim				     					  ----
   ---- 2010 / support@trouvezvotremusee.com			  ----
   =========================================================== 
*/

	if(isset($_POST['nom']) AND isset($_POST['mail']) AND isset($_POST['objet']) AND isset($_POST['message'])) {
		if(!empty($_POST['mail']) AND $_POST['mail']!="Votre email" AND !empty($_POST['objet']) AND $_POST['objet']!="Objet du Message" AND !empty($_POST['message']) AND $_POST['message']!="Votre message...") {
			$objet = stripslashes($_POST['objet']);
			$message = stripslashes($_POST['message']);
			$exp = $_POST['mail'];
			$envoi = mail("support@trouvezvotremusee.com", $objet, $message,"From: $exp\r\n"."Reply-To: $exp\r\n");
			if($envoi) { 
				echo "<li>Le mail a bien été envoyé.</li>";
			}
			else {
				echo "<li>L'envoi a échoué, merci de renouveller l'opération !</li>";
			}
		}
	}
?>
