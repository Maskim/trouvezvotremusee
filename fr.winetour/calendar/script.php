<?php

// - file header -
header('Content-type: text/calendar');
header('Content-Disposition: attachment; filename="WineTourBooking.ics"');

// - content header -
?>
BEGIN:VCALENDAR
VERSION:2.0
<?php

// ************* Recuperation valeurs passées en GET *************

// Récupere l'id passé en GET
// Par la suite rewrite de l'url

if (isset($_GET['id']))
	$idDuBooking 	= $_GET['id'];
// 1 pour client, 2 pour proprietaire
if (isset($_GET['dest']))
	$destinataire 	= $_GET['dest'];


// ************* Import classes *************

$path = $_SERVER['DOCUMENT_ROOT'];
// $path = $_SERVER['DOCUMENT_ROOT'] . 'wordpress/';

// classes du plugin
//include_once $path . '/wp-content/plugins/booking.multiuser.3.0/lib/wpdev-booking-class.php';
//include_once $path . '/wp-content/plugins/booking.multiuser.3.0/lib/wpdev-booking-functions.php';
//include_once $path . '/wp-content/plugins/booking.multiuser.3.0/lib/wpdev-booking-ajax.php';

// Classes de wordpress
include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';


// ************* Recuperation valeurs dans la BDD *************

global $wpdb;

if (isset($idDuBooking)) 
	$idDuBooking 	= ' WHERE  bk.booking_id = ' . $idDuBooking . ' ';
else 
	$idDuBooking 	= ' ';

$sql 				= 	"SELECT dt.booking_date, dt.approved, bk.booking_type, bk.form, bk.cost, bk.nombreDeVisiteur, tp.title

						FROM (".$wpdb->prefix ."booking as bk

						INNER JOIN ".$wpdb->prefix ."bookingdates as dt

						ON    bk.booking_id = dt.booking_id) LEFT OUTER JOIN ".$wpdb->prefix ."bookingtypes tp ON bk.booking_type = tp.booking_type_id

						". $idDuBooking ."   ORDER BY dt.booking_date ASC ";					

$result 				= $wpdb->get_results( $wpdb->prepare($sql) );

// ************* Traitement des valeurs *************

$return 				= array( 'dates'=>array());

// wp_booking.booking_date
foreach ($result as $res) { 
	//echo '*******'.substr($res->booking_date, 11, 2).'*******';
	$return['dates'][] = $res->booking_date;
	if (substr($res->booking_date, 11, 2) != '00')
	{
		$return['dates'] [0] = $res->booking_date;
	}
}

//wp_bookingdates.approved
$return['approved'] 	= $res->approved;

// wp_booking.booking_type
$return['type'] 		= $res->booking_type;

// wp_booking.form
$return['form'] 		= $res->form;

// wp_booking.cost
$return['cost']			= $res->cost;

// wp_booking.nombreDeVisiteur
$return['nbVisiteur']	= $res->nombreDeVisiteur;

$return['title']	= htmlentities ($res->title, ENT_QUOTES, "UTF-8");
	
// Extrait les valeurs du formulaire
$return['parsed_form'] = get_form_content($return['form'], $return['type']);

// Recuperation de l'URL de la propriete

$sql 				= 	"SELECT p.post_name

						FROM ".$wpdb->prefix ."posts as p

						WHERE p.post_type = 'post' AND p.post_title ='". $res->title ."'";					

$result 				= $wpdb->get_results( $wpdb->prepare($sql) );

// wp_posts.post_name
foreach ($result as $res) {
	$return['post_name']	= $res->post_name;
}

$dateStart 	= $return['dates'][0];
$hourStart 	= str_replace(':', '', substr($dateStart, 11, 5));

	
?>
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
BEGIN:VEVENT
SUMMARY:<?php echo html_entity_decode('Visite de la propri&eacute;t&eacute; ' .$return['title'] . ' - WineTourBooking', ENT_QUOTES, "UTF-8"); ?>

DTSTART:<?php echo substr($dateStart, 0, 4)	.substr($dateStart, 5, 2)	.substr($dateStart, 8, 2)	.'T'.$hourStart.'00'; ?>

LOCATION:<?php echo html_entity_decode($return['title'], ENT_QUOTES, "UTF-8"); ?>

DESCRIPTION:<?php 
// Client
if ($destinataire == 1) {
	echo html_entity_decode('visite de la propri&eacute;t&eacute; ' .$return['title'], ENT_QUOTES, "UTF-8") . '\n';
	echo 'voir le descriptif : http://bordeaux.winetourbooking.com/' . $return['post_name'] . '\n';
	echo html_entity_decode('Acc&egrave;s &agrave; votre feuille de route : http://bordeaux.winetourbooking.com/votre-reservation/', ENT_QUOTES, "UTF-8");
}
// Proprietaire
else if ($destinataire == 2) {
	echo 'Detail de la reservation : '. '\n';
	echo '- Nom : ' .$return['parsed_form']['secondname']. '\n';
	echo '- Prenom : ' .$return['parsed_form']['name']. '\n';
	echo '- Nombre de personnes : ' .$return['nbVisiteur']. '\n';
	echo '- Enfant(s) present(s) : ';
	if ($return['parsed_form']['children'] == '') echo 'Non';  
	else echo $return['parsed_form']['children'];
	echo'\n';
	echo '- Cout de la prestation : ' .$return['cost'] .' euros'. html_entity_decode(' r&eacute;gl&eacute; en ligne', ENT_QUOTES, "UTF-8") .'\n\n';
	// echo 'Informations complémentaires :\n';
/*	if 
amateur
ludique
decouverte
connais
adore
curieux
commentaire*/
}

?>

END:VEVENT
END:VCALENDAR