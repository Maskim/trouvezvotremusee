<?php
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//------------------------------------------------- Modification by Winetourbooking ------------------------------------//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function getHoraireBDD($bk_type){
        global $wpdb;
        $mysql = "
            SELECT booking_date, form 
            FROM wp_booking, wp_bookingdates, wp_bookingtypes
            WHERE wp_booking.booking_id = wp_bookingdates.booking_id
            AND wp_booking.booking_type = wp_bookingtypes.booking_type_id
            AND booking_type_id = \"$bk_type\"
            ORDER BY booking_date";
        return $wpdb->get_results($mysql);
    }

    function getNomPropriete($booking_type){
            global $wpdb;
            $mysql = "
                SELECT title
                FROM wp_bookingtypes
                WHERE wp_bookingtypes.booking_type_id = '$booking_type'";
            $return = $wpdb->get_results($mysql);
            $return = $return[0]->title;

            return $return;
        }

    function getHoraireAnglais($bk_type){
        $list = getHoraireBDD($bk_type);

        $bk_type_size = strlen($bk_type);
        if($list)
            $last_date = "";
            $horaires = "";
            foreach($list as $elementlist){
                $form = $elementlist->form;
                $date = $elementlist->booking_date;

                $visiteAnglais = substr($form, stripos($form, "anglais") +  10 + $bk_type_size, 3);

                if($visiteAnglais == "oui")
                {
                    if(!empty($last_date) && $last_date != substr($date, 0, 10)){
                        $horaires .= "--";
                        $horaires .= substr($date, 0, 10);
                    }else if(empty($last_date)){
                        $horaires .= "--";
                        $horaires .= substr($date, 0, 10);
                    }
                    
                        $horaires .= "/";
                        $horaires .= substr($form, stripos($form, "starttime") +  10 + $bk_type_size, 5);
                    $last_date = substr($date, 0, 10);
                }
        }
        return $horaires;
    }

    function getHoraire($bk_type){
        $list = getHoraireBDD($bk_type);

        $bk_type_size = strlen($bk_type);
        if($list)
            $last_date = "";
            $horaires = "";
            foreach($list as $elementlist){
                $form = $elementlist->form;
                $date = $elementlist->booking_date;

                if(!empty($last_date) && $last_date != substr($date, 0, 10)){
                    $horaires .= "--";
                    $horaires .= substr($date, 0, 10);
                }else if(empty($last_date)){
                    $horaires .= "--";
                    $horaires .= substr($date, 0, 10);
                }
                $horaires .= "/";
                $horaires .= substr($form, stripos($form, "starttime") + 10 + $bk_type_size, 5);

                $last_date = substr($date, 0, 10);
            }
        return $horaires;
    }

    function get_Nb_Visitor($bk_type){
        $html = '<script type="text/javascript">';
        $html .= 'var Visiteur = new Array();';
        global $wpdb;
        $mysql = "
            SELECT booking_date, SUM(nombreDeVisiteur) AS NbVisitor 
            FROM wp_booking, wp_bookingdates
            WHERE wp_booking.booking_id = wp_bookingdates.booking_id
            AND booking_type = \"$bk_type\"
            GROUP BY booking_date
            ORDER BY booking_date";

        $result = $wpdb->get_results($mysql);

        foreach ($result as $key) {
            $date = $key->booking_date;
            $date = substr($date, 0, 16);
            $NbVisitor = $key->NbVisitor;

            $html .= 'Visiteur["'.$date.'"] = '.$NbVisitor.';';
        }

        $html .= '</script>';
        return $html;
    }

    function get_Nb_Visitor_Max($bk_type){
        $html = '<script type="text/javascript">';
        global $wpdb;
        $mysql = "  SELECT maxvisitor 
                    FROM wp_bookingtypes
                    WHERE booking_type_id = \"$bk_type\"";

        $result = $wpdb->get_results($mysql);
        foreach ($result as $key) {
             $html .= "var MaxVisitor = " .$key->maxvisitor. ";";
        }

        $html .= "</script>";
        return $html;
    }

    function wtb_get_customer($name, $prenom, $email){
        global $wpdb;

        $requete = "SELECT * FROM ".$wpdb->prefix."wpdev_crm_customers WHERE name = '$prenom' AND second_name = '$name' AND email = '$email'";
        $return = $wpdb->get_results($wpdb->prepare($requete));

        return $return;
    }

    function get_reservation_for_propriete($id){
        require_once("/homez.478/winetour/bordeaux.winetourbooking.com/classes/ControleurConnexionPers.php");

        $controleur = new ControleurConnexion();

        $select = "wp_bookingdates.booking_date AS date, 
                wp_bookingtypes.cost AS prixVisite, 
                wp_booking.cost AS prixTotal,
                wp_bookingtypes.title,
                name, second_name, phone, adress, city,
                wp_wpdev_crm_customers.email";

        $where = "users = $id 
                AND booking_type_id = booking_type 
                AND wp_bookingdates.booking_id = wp_booking.booking_id 
                AND booking_type_id = internal_filters1 
                AND wp_booking.booking_id = internal_id
                AND wp_wpdev_crm_orders.customer_id = wp_wpdev_crm_customers.customer_id";

        return $controleur -> consulter($select,"wp_booking, wp_bookingdates, wp_bookingtypes, wp_wpdev_crm_orders, wp_wpdev_crm_customers","",$where,"","","","booking_type_id, booking_date","");
    }
    
    function get_id_post_propriete ($titre_propriete) {
        require_once("/homez.478/winetour/bordeaux.winetourbooking.com/classes/ControleurConnexionPers.php");

        $controleur = new ControleurConnexion();
        
        $select     = "wp_posts.ID AS id";
        
        $from       = "wp_posts";
        
        $where      = "wp_posts.post_type = 'post' AND wp_posts.post_title = '$titre_propriete'";
        
        return $controleur -> consulter($select,$from,"",$where,"","","","","");
    }

    function sendMailProprietaire($booking_type, $bookingId, $merchant_id, $transaction_id, $payment_certificate, $authorisation_id,
                                    $DatePay, $HeurePay, $numeroCommande, $date, $nbpers, $isAnglais, $connait, $attente,
                                    $customerEmail, $nom, $prenom, $addr, $ville, $cdePost, $pays, $phone){
        global $wpdb;

        $where = "booking_type_id = '".$booking_type."' AND booking_type = booking_type_id AND wp_bookingtypes.users = ID";
        $requete = "SELECT user_email, title FROM ".$wpdb->prefix."users, ".$wpdb->prefix."bookingtypes, ".$wpdb->prefix."booking
                    WHERE $where";

        $user_email = $wpdb->get_results($wpdb->prepare($requete));
        $user_email = $user_email[0]->user_email;
        $propriete = $user_email[0]->title;

        if($merchant_id == "") $merchant_id = "053779919900014";
        if($transaction_id == "") $transaction_id = date ("mdHis");
        if($DatePay == "") $DatePay = date("d/m");
        if($HeurePay == "") $HeurePay = date("H:i");
        if($payment_certificate == "") $payment_certificate = "00000-".date("mHdis");
        if($authorisation_id == "") $authorisation_id = date('sidHm');

        $mois = replaceMonthNumberToString(substr($date, 3, 2));
        $date = substr($date, 0, 2) . " " . $mois . " " . substr($date, 6, 4);

        $sujet = "Une r&eacute;servation pour votre propri&eacute;t&eacute; depuis Winetourbooking";


        //mail propri&eacute;t&eacute;
            $Msg = '<html><head><title>Confirmation de votre paiement en ligne sur WineTourBooking</title></head><body>'; 
            $Msg.= '<table width="600" border="0" cellspacing="0" cellpadding="10">
                        <tr>
                            <td bgcolor="#660033"><img src=" http://' . $_SERVER['HTTP_HOST'] . '/wp-content/uploads/2012/03/logov2.png " /></td>
                        </tr>';
            $Msg.= "<tr>";
            $Msg.= "<td><p>### CECI EST UN MESSAGE AUTOMATIQUE . MERCI DE NE PAS Y REPONDRE ###</p>";
            $Msg.= "<p>Bonjour,<br />";
            $Msg.= "Veuillez trouver ci-dessous le d&eacute;tail de la r&eacute;servation en ligne sur " . $_SERVER['HTTP_HOST'] . "  </p>";

            $Msg.= "<h2>DETAIL DE LA COMMANDE</h2>";
            $Msg.= "<p>Une visite de ".$nbpers." personne(s) pour la propri&eacute;t&eacute; ".$propriete." pour le ".$date."<br />";
            $Msg.= "------------------------------------------------------------<br />";
            $Msg.= "<p><strong>Num&eacute;ro de Commande</strong> : " . $numeroCommande . " </p>";

            $Msg.= "</ul>";
            $Msg.= "<li><strong>Date de la transaction</strong>                     : $DatePay &agrave; $HeurePay </li>";
            $Msg.= "<li><strong>Adresse web du commer&ccedil;ant</strong>           : " . $_SERVER['HTTP_HOST'] . " </li>";
            $Msg.= "<li><strong>Identifiant du commer&ccedil;ant</strong>           : $merchant_id </li>";
            $Msg.= "<li><strong>R&eacute;f&eacute;rence de la transaction</strong>  : $transaction_id </li>";
            $Msg.= "<li><strong>Nombre de Visiteur</strong>                         : ".$nbpers.".</li>";
            $Msg.= "<li><strong>Autorisation</strong>                               : $authorisation_id </li>";
            $Msg.= "<li><strong>Certificat de la transaction</strong>               : $payment_certificate </li>";
            $Msg.= "<li><strong>Nom</strong>                                        : " . $nom . " </li>";
            $Msg.= "<li><strong>Prenom </strong>                                    : " . $prenom . " </li>";
            $Msg.= "<li><strong>Adresse mail</strong>                               : " . $customerEmail ."</li>";
            $Msg.= "<li><strong>Adresse </strong>                                   : " . $addr . " </li>";
            $Msg.= "<li><strong>Ville </strong>                                     : " . $ville . " </li>";
            $Msg.= "<li><strong>Code postal</strong>                                : " . $cdePost . " </li>";
            $Msg.= "<li><strong>Pays</strong>                                       : " . $pays . " </li>";
            $Msg.= "<li><strong>Num&eacute;ro de t&eacute;l&eacute;phone</strong>   : " . $phone . " </li>";

            if($attente[0] != ''){
                $Msg .= '<li>Ses attentes               : ' . $attente[0] . '. </li>';
            }

            if($connait[0] != ''){
                $Msg .= '<li>Connaissance de la propri&eacute;t&eacute; : ' . $connait[0] . '.</li>';
            }

            if($isAnglais){
                $Msg .= '<li>La visite sera anglais</li>';
            }

            $Msg.= "</ul>";

            $Msg .= "<p><a href=\"http://" . $_SERVER['HTTP_HOST'] . "/calendar/generer-2-".$bookingId.".ics\" >Ajouter cette visite &agrave; votre agenda</a></p>";

            $Msg.= "<p><a href=\"http://" . $_SERVER['HTTP_HOST'] . "\">http://" . $_SERVER['HTTP_HOST'] . "</a></p>";

            $Msg.= "<p>Merci de votre confiance </p></td>";

            $Msg.= '</tr><tr>
                        <td bgcolor="#660033"><font color="#FFF">Merci, l\'&eacute;quipe de WineTourBooking. Profitez de votre escapade !
                      <a href="http://' . $_SERVER['HTTP_HOST'] . '/"><font color="#FFF">WineTourBooking</font></a></font></td>
                      </tr>
                    </table></body></html>';


            // To send HTML mail, the Content-type header must be set
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

            // Additional headers
            $headers .= 'From: contact@winetourbooking.com' . "\r\n";

        mail('cperonmagnan@winetourbooking.fr' , $sujet, $Msg, $headers);
        //mail('edepins@winetourbooking.fr' , $sujet, $Msg, $headers);
        mail("maxime.hersand@gmail.com", $sujet, $Msg, $headers);
        mail($user_email, $sujet, $Msg, $headers);
    }
	
	function arrayToObject($d) {
		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return (object) array_map(__FUNCTION__, $d);
		}
		else {
			// Return object
			return $d;
		}
	}
	
	function estDansLaListe ($element, $liste_element) {
		$reponse = false;
		$element = arrayToObject($element);
		$liste_element = arrayToObject($liste_element);
		if (!empty($liste_element)) {
			foreach ($liste_element as $e) {
				if ($e == $element) {
					$reponse = true;
				}
			}
		}
		return $reponse;
	}
	
	function get_liste_placecategories_dans ($liste_posts) {
		
		// Recupere la liste des categories dont au moins une propriete est affichee
		$liste_cats = Array ();
		$liste_id_placecategories = arrayToObject(get_liste_id_placecategories());
		if (! empty($liste_posts)) {
			foreach ($liste_posts as $key=>$value) {
				if (!empty($value->place_cats)) {
					foreach ($value->place_cats as $cat) {
						if (empty ($liste_cats) && estDansLaListe ($cat, $liste_id_placecategories))
								$liste_cats[] = $cat;
						else if (!estDansLaListe ($cat, $liste_cats) && estDansLaListe ($cat, $liste_id_placecategories))
							$liste_cats[] = $cat;
					}
				}
			}
		}
		return $liste_cats;
	}
	
	function get_liste_id_placecategories () { 
        global $wpdb;

		$select = $wpdb->prefix."terms.name AS tag, ".$wpdb->prefix."terms.slug AS slug, ".$wpdb->prefix."term_taxonomy.term_id AS ID";
		
        $from 	= $wpdb->prefix."term_taxonomy, ".$wpdb->prefix."terms";

        $where 	= $wpdb->prefix."terms.term_id = ".$wpdb->prefix."term_taxonomy.term_id AND ".$wpdb->prefix."term_taxonomy.taxonomy = 'placecategory'";

        $requete = "SELECT $select FROM $from WHERE $where";

        $return = $wpdb->get_results($wpdb->prepare($requete));
		
        return $return;   
	}
	
	function get_quicksand_categories_hmtl($liste_posts) {
		
		$liste_cats = arrayToObject(get_liste_placecategories_dans ($liste_posts));
		
		// Preparation du code html a afficher
		$html_return .= '<div id="filter">' . __('Filter by', 'templatic') . ' :</div>';
		$html_return .= '<ul id="filterOptions">
		<li class="active"><a href="#" class="all">' . __('All', 'templatic') . '</a></li>';
		foreach ($liste_cats as $key=>$value) {
			$html_return .= '<li><a href="#" class="' . $value->slug . '">' . $value->tag . '</a></li>';
		}
		$html_return .= '</ul>';
		
		return $html_return;
	}
	
	function get_quicksand_slug_categories_of_post ($id_post) {
		
		$html_return = '';
		$categories = arrayToObject(get_the_terms ($id_post, 'placecategory'));

		if (! empty($categories))
			foreach ($categories as $cat) {
				$html_return .= $cat->slug . ' ';
			}
		return $html_return;
	}

//MAXIME : fonction qui met dans un tableau pour chaque chaque jour et chaque horaire le nombre de personn en fonction du booking type.
    function get_capacite_per_hour($booking_type){
        $bookings = wtb_get_booking_from_type($booking_type);

        $return = array();

        foreach ($bookings as $value) {
            $date = $value -> booking_date;
            $nb_pers = $value -> nombreDeVisiteur;

            $jour = substr($date, 0, 10);
            $heure = substr($date, 11, 5);


            if(!isset($return[$jour])) $return[$jour] = array();
            if(!empty($return[$jour][$heure]) || $return[$jour][$heure] != 0)
                $return[$jour][$heure] .= intval($nb_pers);
            else
                $return[$jour][$heure] = intval($nb_pers);
        }

        return $return;
    }

    function wtb_get_booking_from_type($booking_type){
        global $wpdb;

        $from = $wpdb->prefix."booking, ". $wpdb->prefix ."bookingdates, ". $wpdb->prefix ."bookingtypes";

        $where = "wp_booking.booking_id = wp_bookingdates.booking_id
                    AND wp_booking.booking_type = wp_bookingtypes.booking_type_id
                    AND booking_type_id = \"$booking_type\"";

        $requete = "SELECT booking_date, SUM(nombreDeVisiteur) AS nombreDeVisiteur FROM $from WHERE $where GROUP BY wp_bookingdates.booking_date  ORDER BY wp_bookingdates.booking_date ";

        $return = $wpdb->get_results($wpdb->prepare($requete));

        return $return;
    }
	
	function wtb_get_formulaire_reservation () {

		$res = "<form method=\"POST\" action=\"../feuillederoute/\">";
		$res .= '
			<table>
				<tr>

					<td><label for="wtb_email_client">E-mail</label></td>
					<td><input type="input" name="wtb_email_client" /></td>

				</tr>

				<tr>
					<td><label for="wtb_name_client">Nom de famille</label></td>
					<td><input type="input" name="wtb_name_client" /></td>
				</tr>

				<tr>
					<td><label for="wtb_prenom_client">Pr&eacute;nom</label></td>
					<td><input type="input" name="wtb_prenom_client" /></td>
				</tr>

				<tr>
					<td><input type="hidden" name="wtb_info" value="ok" />
					<input type="submit" value="Validez" /></td>
				</tr>
			</table>
		';
		$res .= "</form>";
		
		return $res;
	}
function mailAttenteConfirmation($booking_id){
        global $wpdb;

        $prefix = $wpdb->prefix;

        $requete = "SELECT booking_type, NumCommande, nombreDeVisiteur, title, wp_booking.cost AS cost, booking_date, form, user_email 
                    FROM " .$prefix. "booking, " .$prefix. "bookingdates, " .$prefix. "bookingtypes, " .$prefix. "users 
                    WHERE " .$prefix. "booking.booking_id = $booking_id
                    AND " .$prefix. "booking.booking_id = " .$prefix. "bookingdates.booking_id
                    AND booking_type = booking_type_id
                    AND users = ID";

        $result = $wpdb -> get_results($wpdb->prepare($requete));

        $prix = $result[0] -> cost;
        $numeroCommande = $result[0] -> NumCommande;
        $nbpers = $result[0] -> nombreDeVisiteur;
        $date = $result[0] -> booking_date;
        $propriete = $result[0] -> title;
        $mailPropriete = $result[0] -> user_email;
        $booking_type = $result[0] -> booking_type;

        $transaction_id;

        $form = $result[0] -> form;
        $nom = find_in_form($form, "secondname", $booking_type);
        $prenom = find_in_form($form, "name", $booking_type);
        $customerEmail = find_in_form($form, "email", $booking_type);
        $addr = find_in_form($form, "address", $booking_type);
        $cdePost = find_in_form($form, "postcode", $booking_type);
        $ville = find_in_form($form, "city", $booking_type);
        $phone = find_in_form($form, "phone", $booking_type);
        $pays = find_in_form($form, "country", $booking_type);

        $isAnglais = find_in_form($form, "anglais", $booking_type);
        if(!empty($isAnglais) && $isAnglais != "")
            $isAnglais = true;
        else
            $isAnglais = false;


        $heure = substr($date, 11, 5);
        $date = substr($date, 0, 10);
        $date = updateFormatDateForPayment($date);

        $prix = str_replace(".", ",", $prix);

        $sujet = "Votre demande de reservation en ligne sur WineTourBooking a bien ete prise en compte";

        //mail propri&eacute;t&eacute;
        $Msg = '<html><head><title>Votre r&eacute;servation en ligne sur WineTourBooking a bien &eacute;t&eacute; prise en compte</title></head><body>'; 
        $Msg.= '<table width="600" border="0" cellspacing="0" cellpadding="10">
                    <tr>
                        <td bgcolor="#660033"><img src=" http://' . $_SERVER['HTTP_HOST'] . '/wp-content/uploads/2012/03/logov2.png " /></td>
                    </tr>';
        $Msg.= "<tr>";
        $Msg.= "<p>### CECI EST UN MESSAGE AUTOMATIQUE . MERCI DE NE PAS Y REPONDRE ###</p>";
        $Msg.= "<p>Bonjour,<br />";
        $Msg.= "Veuillez trouver ci-dessous le d&eacute;tail de la r&eacute;servation en ligne sur <a href=\"http://". $_SERVER['HTTP_HOST'] ."\">" . $_SERVER['HTTP_HOST'] . "</a>  </p>";
        $Msg.= "<p>Votre r&eacute;servation a <strong>bien &eacute;t&eacute; prise en compte</strong>, une demande est envoy&eacute;e à la propri&eacute;t&eacute; pour confirmation.</p>";
        $Msg.= "<p></p><p><strong>Cette derni&egrave;re vous fera parvenir dans les plus brefs d&eacute;lais la validation de votre r&eacute;servation.</strong></p>";

        $Msg.= "<h2>RECAPITULATIF DE VOTRE COMMANDE</h2>";
        $Msg.= "<p>Une visite de ".$nbpers." personne(s) pour la propri&eacute;t&eacute; ".$propriete." pour le ".$date."<br />";
        $Msg.= "------------------------------------------------------------<br />";

        $Msg.= "</ul>";
        $Msg.= "<li>Adresse web du commer&ccedil;ant : " . $_SERVER['HTTP_HOST'] . " </li>";
        $Msg.= "<li>La visite se passera le $date à $heure</li>";
        $Msg.= "<li>Nombre de Visiteur : ".$nbpers.".</li>";
        $Msg.= "<li>Prix de la future transaction : $prix €.</li>";
        $Msg .="</ul";

        $Msg.="<ul>";
        $Msg.= "<li>Nom                             : " . $nom . " </li>";
        $Msg.= "<li>Prenom                          : " . $prenom . " </li>";
        $Msg.= "<li>Adresse mail                    : " . $customerEmail ."</li>";
        $Msg.= "<li>Adresse                         : " . $addr . " </li>";
        $Msg.= "<li>Ville                           : " . $ville . " </li>";
        $Msg.= "<li>Code postal                     : " . $cdePost . " </li>";
        $Msg.= "<li>Pays                            : " . $pays . " </li>";
        $Msg.= "<li>Num&eacute;ro de t&eacute;l&eacute;phone            : " . $phone . " </li>";

        if($isAnglais){
            $Msg .= '<li>La visite sera anglais</li>';
        }

        $Msg.= "</ul>";

        $Msg.= "<p><a href=\"http://" . $_SERVER['HTTP_HOST'] . "\">http://" . $_SERVER['HTTP_HOST'] . "</a></p>";

        $Msg.= "<p>Nous vous remercions de votre confiance.</p>";

        $Msg.= '</tr><tr>
                    <td bgcolor="#660033"><font color="#FFF">Toute l\'&eacute;quipe de WineTourBooking.
                  <a href="http://' . $_SERVER['HTTP_HOST'] . '/"><font color="#FFF">WineTourBooking</font></a></font></td>
                  </tr>
                </table></body></html>';

        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

        // Additional headers
        $headers .= 'From: contact@winetourbooking.com' . "\r\n";

        mail("maxime.hersand@gmail.com", $sujet, $Msg, $headers);
        mail("cperonmagnan@winetourbooking.fr", $sujet, $Msg, $headers);
        mail($customerEmail, $sujet, $Msg, $headers);

    }

    function find_in_form($form, $identifiant, $booking_type){
        $pos = strpos($form, $identifiant . $booking_type) + strlen($identifiant) + strlen($booking_type) + 1;

        $return = "";

        while(substr($form, $pos, 1) != "~"){
            $return .= substr($form, $pos, 1);

            $pos++;
        }

        if($return == "[]" || $return == "]^")
            $return = "";

        return $return;
    }

    function mailDemandeProprietePourConfirmation($booking_id){
        global $wpdb;

        $prefix = $wpdb->prefix;

        $requete = "SELECT booking_type, NumCommande, nombreDeVisiteur, title, wp_booking.cost AS cost, booking_date, form, user_email 
                    FROM " .$prefix. "booking, " .$prefix. "bookingdates, " .$prefix. "bookingtypes, " .$prefix. "users 
                    WHERE " .$prefix. "booking.booking_id = $booking_id
                    AND " .$prefix. "booking.booking_id = " .$prefix. "bookingdates.booking_id
                    AND booking_type = booking_type_id
                    AND users = ID";

        $result = $wpdb -> get_results($wpdb->prepare($requete));

        $prix = $result[0] -> cost;
        $numeroCommande = $result[0] -> NumCommande;
        $nbpers = $result[0] -> nombreDeVisiteur;
        $date = $result[0] -> booking_date;
        $propriete = $result[0] -> title;
        $mailPropriete = $result[0] -> user_email;
        $booking_type = $result[0] -> booking_type;

        $transaction_id;

        $form = $result[0] -> form;
        $nom = find_in_form($form, "secondname", $booking_type);
        $prenom = find_in_form($form, "name", $booking_type);
        $customerEmail = find_in_form($form, "email", $booking_type);
        $addr = find_in_form($form, "address", $booking_type);
        $cdePost = find_in_form($form, "postcode", $booking_type);
        $ville = find_in_form($form, "city", $booking_type);
        $phone = find_in_form($form, "phone", $booking_type);
        $pays = find_in_form($form, "country", $booking_type);

        $isAnglais = find_in_form($form, "anglais", $booking_type);
        if(!empty($isAnglais) && $isAnglais != "")
            $isAnglais = true;
        else
            $isAnglais = false;


        $heure = substr($date, 11, 5);
        $date = substr($date, 0, 10);
        $date = updateFormatDateForPayment($date);

        $prix = str_replace(".", ",", $prix);

        $sujet = "Vous avez une nouvelle demande de reservation en ligne";

        //mail propri&eacute;t&eacute;
        $Msg = '<html><head><title>Votre r&eacute;servation en ligne sur WineTourBooking a bien &eacute;t&eacute; prise en compte</title></head><body>'; 
        $Msg.= '<table width="600" border="0" cellspacing="0" cellpadding="10">
                    <tr>
                        <td bgcolor="#660033"><img src=" http://' . $_SERVER['HTTP_HOST'] . '/wp-content/uploads/2012/03/logov2.png " /></td>
                    </tr>';
        $Msg.= "<tr>";
        $Msg.= "<p>### CECI EST UN MESSAGE AUTOMATIQUE . MERCI DE NE PAS Y REPONDRE ###</p>";
        $Msg.= "<p>Bonjour,<br />";
        $Msg.= "Veuillez trouver ci-dessous le d&eacute;tail de la r&eacute;servation en ligne sur " . $_SERVER['HTTP_HOST'] . "  </p>";

        $Msg.= "<p>Vous avez reçu une demande de r&eacute;servation, cette derni&egrave;re est d&eacute;taill&eacute;e un peu plus loin dans le mail. ";
        $Msg.= "L'identifiant de la r&eacute;servation est le suivant : <strong>" .$booking_id. "</strong>.<br />";
        $Msg.= "Vous pouvez acc&egrave;der à votre interface via ce lien : <a href=\"http://" . $_SERVER['HTTP_HOST'] . "/wp-admin\">Votre interface</a>.<br />";
        $Msg.= "Si vous acceptez cette visite, envoyez la demande de paiement au client. ";
        $Msg.= "Dans le cas contraire, sp&eacute;cifiez en la raison via l'interface.</p>";

        $Msg.= "<br />";

        $Msg.= "<h2>RECAPITULATIF DE LA DEMANDE</h2>";
        $Msg.= "<p>Une visite de ".$nbpers." personne(s) pour la propri&eacute;t&eacute; ".$propriete." pour le ".$date."<br />";
        $Msg.= "------------------------------------------------------------<br />";

        $Msg.= "</ul>";
        $Msg.= "<li>Adresse web du commer&ccedil;ant : " . $_SERVER['HTTP_HOST'] . " </li>";
        $Msg.= "<li>La visite se passera le $date à $heure</li>";
        $Msg.= "<li>Nombre de Visiteur : ".$nbpers.".</li>";
        $Msg.= "<li>Prix de la future transaction : $prix €.</li>";
        $Msg .="</ul";

        $Msg.="<ul>";
        $Msg.= "<li>Nom                             : " . $nom . " </li>";
        $Msg.= "<li>Prenom                          : " . $prenom . " </li>";
        $Msg.= "<li>Adresse mail                    : " . $customerEmail ."</li>";
        $Msg.= "<li>Adresse                         : " . $addr . " </li>";
        $Msg.= "<li>Ville                           : " . $ville . " </li>";
        $Msg.= "<li>Code postal                     : " . $cdePost . " </li>";
        $Msg.= "<li>Pays                            : " . $pays . " </li>";
        $Msg.= "<li>Num&eacute;ro de t&eacute;l&eacute;phone            : " . $phone . " </li>";

        if($isAnglais){
            $Msg .= '<li>La visite sera anglais</li>';
        }

        $Msg.= "</ul>";

        $Msg.= "<p><a href=\"http://" . $_SERVER['HTTP_HOST'] . "\">http://" . $_SERVER['HTTP_HOST'] . "</a></p>";

        $Msg.= "<p>Merci de votre confiance </p>";

        $Msg.= '</tr><tr>
                    <td bgcolor="#660033"><font color="#FFF">Merci, l\'&eacute;quipe de WineTourBooking.
                  <a href="http://' . $_SERVER['HTTP_HOST'] . '/"><font color="#FFF">WineTourBooking</font></a></font></td>
                  </tr>
                </table></body></html>';

        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

        // Additional headers
        $headers .= 'From: contact@winetourbooking.com' . "\r\n";

        mail("maxime.hersand@gmail.com", $sujet, $Msg, $headers);
        mail("cperonmagnan@winetourbooking.fr", $sujet, $Msg, $headers);
        mail($mailPropriete, $sujet, $Msg, $headers);
    }

    function updateFormatDateForPayment($date){
        

        $jour = substr($date, 8, 2);
        $mois = replaceMonthNumberToString(substr($date, 5, 2));
        $an = substr($date, 0,4);

        $return = $jour . " " . $mois . " " . $an;

        return $return;
    }

    function replaceMonthNumberToString($month){
        $mois[] = __("january", "wpdev-booking");
        $mois[] = __("february", "wpdev-booking");
        $mois[] = __("march", "wpdev-booking");
        $mois[] = __("april", "wpdev-booking");
        $mois[] = __("may", "wpdev-booking");
        $mois[] = __("june", "wpdev-booking");
        $mois[] = __("july", "wpdev-booking");
        $mois[] = __("august", "wpdev-booking");
        $mois[] = __("september", "wpdev-booking");
        $mois[] = __("october", "wpdev-booking");
        $mois[] = __("november", "wpdev-booking");
        $mois[] = __("december", "wpdev-booking");

        $numMois[] = "01";
        $numMois[] = "02";
        $numMois[] = "03";
        $numMois[] = "04";
        $numMois[] = "05";
        $numMois[] = "06";
        $numMois[] = "07";
        $numMois[] = "08";
        $numMois[] = "09";
        $numMois[] = "10";
        $numMois[] = "11";
        $numMois[] = "12";

        $month = str_replace($numMois, $mois, $month);

        return $month;
    }

    function prepareDateForBdd($date){
        $date = substr($date, 6, 4) . substr($date, 3, 2) . substr($date, 0, 2);

        return $date;
    }
?>