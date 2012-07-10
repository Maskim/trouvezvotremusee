<!--
-------------------------------------------------------------
 Topic	 : Exemple PHP traitement de l'autor&eacute;ponse de paiement
 Version : P615

 		Dans cet exemple, les donn&eacute;es de la transaction	sont
		d&eacute;crypt&eacute;es et sauvegard&eacute;es dans un fichier log.

-------------------------------------------------------------
-->

<?php

	// R&eacute;cup&eacute;ration de la variable crypt&eacute;e DATA
	$message="message=$HTTP_POST_VARS[DATA]";

	// Initialisation du chemin du fichier pathfile (&agrave; modifier)
	    //   ex :
	    //    -> Windows : $pathfile="pathfile=c:/repertoire/pathfile"
	    //    -> Unix    : $pathfile="pathfile=/home/repertoire/pathfile"
	    
	$pathfile="pathfile=/homez.478/winetour/cgi-bin/pathfile";

	//Initialisation du chemin de l'executable response (&agrave; modifier)
	//ex :
	//-> Windows : $path_bin = "c:/repertoire/bin/response"
	//-> Unix    : $path_bin = "/home/repertoire/bin/response"
	//

	$path_bin = "/homez.478/winetour/cgi-bin/bin/static/response";

	// Appel du binaire response
  	$message = escapeshellcmd($message);
  	$result=exec("$path_bin $pathfile $message");

	//	Sortie de la fonction : !code!error!v1!v2!v3!...!v29
	//		- code=0	: la fonction retourne les donn&eacute;es de la transaction dans les variables v1, v2, ...
	//				: Ces variables sont d&eacute;crites dans le GUIDE DU PROGRAMMEUR
	//		- code=-1 	: La fonction retourne un message d'erreur dans la variable error


	//	on separe les differents champs et on les met dans une variable tableau

	$tableau = explode ("!", $result);

	$code = $tableau[1];
	$error = $tableau[2];
	$merchant_id = $tableau[3];
	$merchant_country = $tableau[4];
	$amount = $tableau[5];
	$transaction_id = $tableau[6];
	$payment_means = $tableau[7];
	$transmission_date= $tableau[8];
	$payment_time = $tableau[9];
	$payment_date = $tableau[10];
	$response_code = $tableau[11];
	$payment_certificate = $tableau[12];
	$authorisation_id = $tableau[13];
	$currency_code = $tableau[14];
	$card_number = $tableau[15];
	$cvv_flag = $tableau[16];
	$cvv_response_code = $tableau[17];
	$bank_response_code = $tableau[18];
	$complementary_code = $tableau[19];
	$complementary_info= $tableau[20];
	$return_context = $tableau[21];
	$caddie = $tableau[22];
	$receipt_complement = $tableau[23];
	$merchant_language = $tableau[24];
	$language = $tableau[25];
	$customer_id = $tableau[26];
	$order_id = $tableau[27];
	$customer_email = $tableau[28];
	$customer_ip_address = $tableau[29];
	$capture_day = $tableau[30];
	$capture_mode = $tableau[31];
	$data = $tableau[32];
	$order_validity = $tableau[33];
	$transaction_condition = $tableau[34];
	$statement_reference = $tableau[35];
	$card_validity = $tableau[36];
	$score_value = $tableau[37];
	$score_color = $tableau[38];
	$score_info = $tableau[39];
	$score_threshold = $tableau[40];
	$score_profile = $tableau[41];


	// Initialisation du chemin du fichier de log (&agrave; modifier)
    //   ex :
    //    -> Windows : $logfile="c:\\repertoire\\log\\logfile.txt";
    //    -> Unix    : $logfile="/home/repertoire/log/logfile.txt";
    //

	$logfile="/homez.478/winetour/cgi-bin/param/logfile.txt";

	// Ouverture du fichier de log en append

	$fp=fopen($logfile, "a");

	//  analyse du code retour

  if (( $code == "" ) && ( $error == "" ) )
 	{
  	fwrite($fp, "#======= Le : " . date("d/m/Y H:i:s") . " ========#\n");
	fwrite($fp, "Erreur appel response\n");
	fwrite($fp, "Executable response non trouv&eacute; : $path_bin \n");
	fwrite($fp, "-------------------------------------------\n");
 	}

	//	Erreur, sauvegarde le message d'erreur

	else if ( $code != 0 ){
        fwrite($fp, "#======= Le : " . date("d/m/Y H:i:s") . " ========#\n");
		fwrite($fp, "Erreur appel API de paiement.\n");
		fwrite($fp, "Message erreur :  $error \n");
		fwrite($fp, "-------------------------------------------\n");
 	}
	else {

	// OK, Sauvegarde des champs de la r&eacute;ponse
		// Si paiement accept&eacute;
		if($bank_response_code == "00"){

			//Caddie
			//Ici nous retrouvons tout notre caddie que nous remmettons dans un tableau
			$arrayCaddie = unserialize(base64_decode($caddie));

			require_once("./classes/ControleurConnexionPers.php");
			require_once("./function.php");

			$NumCommande = $arrayCaddie[1];

			$controleur = new ControleurConnexion();
			$controleur -> modifier("wp_booking","is_new = 0, pay_status = 'Atos:Payed'","NumCommande = '$NumCommande'","","");

			$select = $controleur -> consulter("booking_id","wp_booking","","NumCommande = '$NumCommande'","","","","", "");
			$booking_id = mysql_fetch_row($select);

			$controleur -> modifier("wp_bookingdates","approved = 1","booking_id = '".$booking_id[0]."'","","");

			$select_date = $controleur -> consulter("booking_date","wp_booking, wp_bookingdates","","NumCommande = '$NumCommande' AND wp_booking.booking_id = wp_bookingdates.booking_id","","","","", "");
			$date_resa = mysql_fetch_row($select_date);

			$select_nbPers = $controleur -> consulter("nombreDeVisiteur","wp_booking","","NumCommande = '$NumCommande'","","","","", "");
			$nombre_personne = mysql_fetch_row($select_nbPers);

			$select_form = $controleur -> consulter("form","wp_booking","","NumCommande = '$NumCommande'","","","","", "");
			$form = mysql_fetch_row($select_form); 
			$form = $form[0];

			$select_type = $controleur -> consulter("booking_type","wp_booking","","NumCommande = '$NumCommande'","","","","", "");
			$booking_type = mysql_fetch_row($select_type);

			$where = "booking_type_id = ".$booking_type[0]." AND booking_type = booking_type_id AND wp_bookingtypes.users = ID";
			$select_mailPropriete = $controleur -> consulter("user_email", "wp_users, wp_bookingtypes, wp_booking", "", $where, "", "", "", "", "");

			//Mail pour la propri&eacute;t&eacute;
			$propriete_mail = mysql_fetch_row($select_mailPropriete);

			$attente = findAttenteForm($form, strlen($booking_type[0]));
			$connait = findConnaitForm($form, strlen($booking_type[0]));

			$isAnglais = isAnglais($form, strlen($booking_type[0]));

			$attente = utf8_encode($attente);
			$connait = utf8_encode($connait);

			//Date (ymd) / Heure (His) de paiement en fran&ccedil;ais
			$DatePay = substr($payment_date, 6, 2) . "/" . substr($payment_date, 4, 2) . "/". substr($payment_date, 0, 4) ;

			$HeurePay = substr($payment_time, 0, 2) . "h " . substr($payment_time, 2, 2) . ":". substr($payment_time, 4, 2) ;

			//Le re&ccedil;u de la transaction que nous allons envoyer pour confirmation
			$sujet = "Confirmation de votre paiement en ligne sur WineTourBooking";

			$Msg = '<html><head><title>Confirmation de votre paiement en ligne sur WineTourBooking</title></head><body>'; 
			$Msg.= '<table width="600" border="0" cellspacing="0" cellpadding="10">
  						<tr>
    						<td bgcolor="#660033"><img src=" http://bordeaux.winetourbooking.com/wp-content/uploads/2012/03/logov2.png " /></td>
	  					</tr>';
	  		$Msg.= "<tr>";
			$Msg.= "<td><p>### CECI EST UN MESSAGE AUTOMATIQUE . MERCI DE NE PAS Y REPONDRE ###</p>";
			$Msg.= "<p>Bonjour,<br />";
			$Msg.= "Veuillez trouver ci-dessous le re&ccedil;u de votre paiement en ligne sur bordeaux.winetourbooking.com </p>";
			$Msg.= "<p>Prenez soin d'imprimer ce message ou de l'ouvrir avec votre smartphone.</p>
				<p>Cette facture vous sera demand&eacute;e lors de votre visite.
					Ces documents vous seront &eacute;galement <em>indispensables en cas de r&eacute;clamation.</em></p>";
			$Msg.= "<p>Cette facture vous sera demand&eacute;e lors de votre visite.</p>";

			$Msg.= "<h2>DETAIL DE VOTRE COMMANDE</h2>";
			$Msg.= "<p>Vous avez r&eacute;serv&eacute; une visite de ".$arrayCaddie[9]." personne(s) pour la propri&eacute;t&eacute; ".htmlspecialchars($arrayCaddie[10])." pour le ".$date_resa[0]."<br />";
			$Msg.= "------------------------------------------------------------<br />";
			$Msg.= "<p><strong>Num&eacute;ro de Commande :" . $arrayCaddie[1] . " </strong></p>";

			$Msg.= "</ul>";
			$Msg.= "<li>Date de la transaction : $DatePay &agrave; $HeurePay </li>";
			$Msg.= "<li>Adresse web du commer&ccedil;ant : bordeaux.winetourbooking.com </li>";
			$Msg.= "<li>Identifiant du commer&ccedil;ant : $merchant_id </li>";
			$Msg.= "<li>R&eacute;f&eacute;rence de la transaction : $transaction_id </li>";
			$Msg.= "<li>Montant de la transaction : " . substr($amount,0,-2) . "," . substr($amount ,-2)
			. " euros </p>";
			$Msg.= "<li>Nombre de Visiteur : ".$nombre_personne[0].".</li>";
			$Msg.= "<li>Autorisation : $authorisation_id </li>";
			$Msg.= "<li>Certificat de la transaction : $payment_certificate </li>";
			$Msg .="</ul";

			$Msg.="<ul>";
			$Msg.= "<li>Nom                            : " . $arrayCaddie[2] . " </li>";
			$Msg.= "<li>Pr&eacute;nom                  : " . $arrayCaddie[3] . " </li>";
			$Msg.= "<li>Adresse                        : " . $arrayCaddie[4] . " </li>";
			$Msg.= "<li>Ville                          : " . $arrayCaddie[5] . " </li>";
			$Msg.= "<li>Code postal                    : " . $arrayCaddie[7] . " </li>";
			$Msg.= "<li>Pays                           : " . $arrayCaddie[6] . " </li>";
			$Msg.= "<li>Num&eacute;ro de t&eacute;l&eacute;phone            : " . $arrayCaddie[8] . " </li>";

			if($attente != ''){
				$Msg .= '<li>Ses attentes 				: ' . $attente . '. </li>';
			}

			if($connait != ''){
				$Msg .= '<li>Connaissance de la propri&eacute;t&eacute; : ' . $connait . '.</li>';
			}

			if($isAnglais){
				$Msg .= '<li>Visite en anglais</li>';
			}

			$Msg.= "</ul>";

			$Msg .= "<p><a href=\"http://bordeaux.winetourbooking.com/calendar/generer-1-".$booking_id[0].".ics\">Ajouter cette visite &agrave; votre agenda</a></p>";

			$Msg.= "<p><a href=\"http://bordeaux.winetourbooking.com\">http://bordeaux.winetourbooking.com</a></p>";

			$Msg.= "<p>Merci de votre confiance </p></td>";

			$Msg.= '</tr><tr>
					    <td bgcolor="#660033"><font color="#FFF">Merci, l\'&eacute;quipe de WineTourBooking. Profitez de votre escapade !
					  <a href="http://bordeaux.winetourbooking.com/"><font color="#FFF">WineTourBooking</font></a></font></td>
					  </tr>
					</table></body></html>';

					// To send HTML mail, the Content-type header must be set
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

					// Additional headers
					$headers .= 'From: contact@winetourbooking.com' . "\r\n";

			//Envoi du message au client
			mail($customer_email, $sujet, $Msg, $headers);
			mail('cperonmagnan@winetourbooking.fr', $sujet, $Msg, $headers);


			//mail propriété
			$Msg = '<html><head><title>Confirmation de votre paiement en ligne sur WineTourBooking</title></head><body>'; 
			$Msg.= '<table width="600" border="0" cellspacing="0" cellpadding="10">
  						<tr>
    						<td bgcolor="#660033"><img src=" http://bordeaux.winetourbooking.com/wp-content/uploads/2012/03/logov2.png " /></td>
	  					</tr>';
	  		$Msg.= "<tr>";
			$Msg.= "<td><p>### CECI EST UN MESSAGE AUTOMATIQUE . MERCI DE NE PAS Y REPONDRE ###</p>";
			$Msg.= "<p>Bonjour,<br />";
			$Msg.= "Veuillez trouver ci-dessous le détail de la réservation en ligne sur bordeaux.winetourbooking.com  </p>";

			$Msg.= "<h2>DETAIL DE VOTRE COMMANDE</h2>";
			$Msg.= "<p>Une visite de ".$arrayCaddie[9]." personne(s) pour la propri&eacute;t&eacute; ".htmlspecialchars($arrayCaddie[10])." pour le ".$date_resa[0]."<br />";
			$Msg.= "------------------------------------------------------------<br />";
			$Msg.= "<p><strong>Num&eacute;ro de Commande :" . $arrayCaddie[1] . " </strong></p>";

			$Msg.= "</ul>";
			$Msg.= "<li>Date de la transaction : $DatePay &agrave; $HeurePay </li>";
			$Msg.= "<li>Adresse web du commer&ccedil;ant : bordeaux.winetourbooking.com </li>";
			$Msg.= "<li>Identifiant du commer&ccedil;ant : $merchant_id </li>";
			$Msg.= "<li>R&eacute;f&eacute;rence de la transaction : $transaction_id </li>";
			$Msg.= "<li>Montant de la transaction : " . substr($amount,0,-2) . "," . substr($amount ,-2)
			. " euros </p>";
			$Msg.= "<li>Nombre de Visiteur : ".$nombre_personne[0].".</li>";
			$Msg.= "<li>Autorisation : $authorisation_id </li>";
			$Msg.= "<li>Certificat de la transaction : $payment_certificate </li>";
			$Msg .="</ul";

			$Msg.="<ul>";
			$Msg.= "<li>Nom                            : " . $arrayCaddie[2] . " </li>";
			$Msg.= "<li>Prenom                  		: " . $arrayCaddie[3] . " </li>";
			$Msg.= "<li>Adresse mail 					: ". $customer_email ."</li>";
			$Msg.= "<li>Adresse                        : " . $arrayCaddie[4] . " </li>";
			$Msg.= "<li>Ville                          : " . $arrayCaddie[5] . " </li>";
			$Msg.= "<li>Code postal                    : " . $arrayCaddie[7] . " </li>";
			$Msg.= "<li>Pays                           : " . $arrayCaddie[6] . " </li>";
			$Msg.= "<li>Num&eacute;ro de t&eacute;l&eacute;phone            : " . $arrayCaddie[8] . " </li>";

			if($attente != ''){
				$Msg .= '<li>Ses attentes 				: ' . $attente . '. </li>';
			}

			if($connait != ''){
				$Msg .= '<li>Connaissance de la propri&eacute;t&eacute; : ' . $connait . '.</li>';
			}

			if($isAnglais){
				$Msg .= '<li>Visite en anglais</li>';
			}

			$Msg.= "</ul>";

			$Msg .= "<p><a href=\"http://bordeaux.winetourbooking.com/calendar/generer-2-".$booking_id[0].".ics\" >Ajouter cette visite &agrave; votre agenda</a></p>";

			$Msg.= "<p><a href=\"http://bordeaux.winetourbooking.com\">http://bordeaux.winetourbooking.com</a></p>";

			$Msg.= "<p>Merci de votre confiance </p></td>";

			$Msg.= '</tr><tr>
					    <td bgcolor="#660033"><font color="#FFF">Merci, l\'&eacute;quipe de WineTourBooking. Profitez de votre escapade !
					  <a href="http://bordeaux.winetourbooking.com/"><font color="#FFF">WineTourBooking</font></a></font></td>
					  </tr>
					</table></body></html>';

			//On en profite pour s'envoyer &eacute;galement le re&ccedil;u
			mail('cperonmagnan@winetourbooking.fr' , $sujet, $Msg, $headers);
			mail('edepins@winetourbooking.fr' , $sujet, $Msg, $headers);
			mail('mhersand@epsi.fr', $sujet, $Msg, $headers);
			mail($propriete_mail[0], $sujet, $Msg, $headers);
			
			
		}else{
			$arrayCaddie = unserialize(base64_decode($caddie));

			require_once("./classes/ControleurConnexionPers.php");

			$NumCommande = $arrayCaddie[1];

			$controleur = new ControleurConnexion();
			$controleur -> supprimer("wp_booking","NumCommande = '$NumCommande'","","");
		}

		fwrite( $fp, "merchant_id : $merchant_id\n");
		fwrite( $fp, "merchant_country : $merchant_country\n");
		fwrite( $fp, "amount : $amount\n");
		fwrite( $fp, "transaction_id : $transaction_id\n");
		fwrite( $fp, "transmission_date: $transmission_date\n");
		fwrite( $fp, "payment_means: $payment_means\n");
		fwrite( $fp, "payment_time : $payment_time\n");
		fwrite( $fp, "payment_date : $payment_date\n");
		fwrite( $fp, "response_code : $response_code\n");
		fwrite( $fp, "payment_certificate : $payment_certificate\n");
		fwrite( $fp, "authorisation_id : $authorisation_id\n");
		fwrite( $fp, "currency_code : $currency_code\n");
		fwrite( $fp, "card_number : $card_number\n");
		fwrite( $fp, "cvv_flag: $cvv_flag\n");
		fwrite( $fp, "cvv_response_code: $cvv_response_code\n");
		fwrite( $fp, "bank_response_code: $bank_response_code\n");
		fwrite( $fp, "complementary_code: $complementary_code\n");
		fwrite( $fp, "complementary_info: $complementary_info\n");
		fwrite( $fp, "return_context: $return_context\n");

		//ici on d&eacute;piote le caddie
		fwrite( $fp, "caddie : \n");
		fwrite( $fp, "----------- \n");

		for($i = 0 ; $i < count($arrayCaddie); $i++){
			fwrite( $fp, $arrayCaddie[$i] . "\n");
		}
		fwrite( $fp, "-------------------------------- \n");

		fwrite( $fp, "caddie : $caddie\n");
		fwrite( $fp, "receipt_complement: $receipt_complement\n");
		fwrite( $fp, "merchant_language: $merchant_language\n");
		fwrite( $fp, "language: $language\n");
		fwrite( $fp, "customer_id: $customer_id\n");
		fwrite( $fp, "order_id: $order_id\n");
		fwrite( $fp, "customer_email: $customer_email\n");
		fwrite( $fp, "customer_ip_address: $customer_ip_address\n");
		fwrite( $fp, "capture_day: $capture_day\n");
		fwrite( $fp, "capture_mode: $capture_mode\n");
		fwrite( $fp, "data: $data\n");
		fwrite( $fp, "order_validity: $order_validity\n");
		fwrite( $fp, "transaction_condition: $transaction_condition\n");
		fwrite( $fp, "statement_reference: $statement_reference\n");
		fwrite( $fp, "card_validity: $card_validity\n");
		fwrite( $fp, "card_validity: $score_value\n");
		fwrite( $fp, "card_validity: $score_color\n");
		fwrite( $fp, "card_validity: $score_info\n");
		fwrite( $fp, "card_validity: $score_threshold\n");
		fwrite( $fp, "card_validity: $score_profile\n");
		fwrite( $fp, "-------------------------------------------\n");
	}

	fclose ($fp);

?>
