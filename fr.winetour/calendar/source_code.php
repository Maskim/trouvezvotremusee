<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<title>Google Maps JavaScript API v3 Example: Optimized Directions</title>

<!-- ------------------------------------------ -->
<!-- ----------- Debut fonction --------------- -->
<!-- ------------------------------------------ -->

<?php

// ******** Fonction annexe ************

function renvoieBonneDate ($dateInitiale) {
    //explode pour mettre la date du fin en format numerique: 12/05/2006  -> 12052006
    $dfin = explode("/", $dateInitiale);
    // concaténation pour inverser l'ordre: 12052006 -> 20060512
    return $dfin[2].$dfin[1].$dfin[0];
}

/*
// ************* Test valeur d'entree *************

if (isset($_GET['id']))
	$idDuBooking 	= $_GET['id'];

// ************* Import classes *************

$path = $_SERVER['DOCUMENT_ROOT'];
// $path = $_SERVER['DOCUMENT_ROOT'] . 'wordpress/';

// Classes de wordpress
include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';


// ************* Recuperation valeurs dans la BDD *************

global $wpdb;

$sql 				= 	"SELECT dt.booking_date, dt.approved, bk.booking_type, bk.form, bk.cost, bk.nombreDeVisiteur, tp.title

						FROM (".$wpdb->prefix ."booking as bk

						INNER JOIN ".$wpdb->prefix ."bookingdates as dt

						ON    bk.booking_id = dt.booking_id) INNER JOIN ".$wpdb->prefix ."bookingtypes tp ON bk.booking_type = tp.booking_type_id

						WHERE  bk.booking_id = " . $idDuBooking ." ORDER BY dt.booking_date ASC ";					
	// echo '<br/>';
	// echo $sql;						
	// echo '<br/>';
$result 				= $wpdb->get_results( $wpdb->prepare($sql) );

// Nombre de reservations
$numRow = 0;
// Tableau comprenant latitude, longitude, date, et identifiant date
$tabRetour = new Array ();
// Nombre de dates differentes
$numDate = 0;
// Valeur temporaire permettant de garder la date de la derniere reservation parcourue
$dateActuelle = 0;
// Comprend chaque date de reservation differente
$datesReservation = new Array ();


foreach ($result as $res) { 
	// Pour chaque reservation
	// On récupere la reservation (propriété)
	// La date
	// Voir pour récuperer directement les coordonnées de la propriété
	
	if ($dateActuelle != renvoieBonneDate($res->booking_date)) {
		$datesReservation [$numDate] = renvoieBonneDate($res->booking_date);
		$numDate++;
		$dateActuelle = renvoieBonneDate($res->booking_date);
	}
	
	$tabRetour [$numRow] [0] = $res->long;
	$tabRetour [$numRow] [1] = $res->lat;
	$tabRetour [$numRow] [2] = $numDate;
	$tabRetour [$numRow] [3] = $res->date;
	
	$numRow++;
}*/
	$datesReservation = Array ();
	$tabRetour = Array ();
	$datesReservation [0] = '2012-03-22';
	$datesReservation [1] = '2012-03-24';
	$datesReservation [2] = '2012-03-27';
	$tabRetour [0] [0] = '44.837789';
	$tabRetour [0] [1] = '-0.57918';
	$tabRetour [0] [2] = '1';
	$tabRetour [0] [3] = '2012-03-22';
	$tabRetour [1] [0] = '44.533185';
	$tabRetour [1] [1] = '-0.33283';
	$tabRetour [1] [2] = '1';
	$tabRetour [1] [3] = '2012-03-22';
	$tabRetour [2] [0] = '45.3192216';
	$tabRetour [2] [1] = '-0.791979';
	$tabRetour [2] [2] = '2';
	$tabRetour [2] [3] = '2012-03-24';
	$tabRetour [3] [0] = '45.0405066';
	$tabRetour [3] [1] = '-0.5553528';
	$tabRetour [3] [2] = '2';
	$tabRetour [3] [3] = '2012-03-24';
	$tabRetour [4] [0] = '44.894387';
	$tabRetour [4] [1] = '-0.155729';
	$tabRetour [4] [2] = '2';
	$tabRetour [4] [3] = '2012-03-24';
	$tabRetour [5] [0] = '44.7640655';
	$tabRetour [5] [1] = '-0.6606029';
	$tabRetour [5] [2] = '3';
	$tabRetour [5] [3] = '2012-03-27';
	$tabRetour [6] [0] = '45.01843';
	$tabRetour [6] [1] = '-0.623513';
	$tabRetour [6] [2] = '3';
	$tabRetour [6] [3] = '2012-03-27';
?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
  var directionDisplay;
  var directionsService = new google.maps.DirectionsService();
  var map;
  var origin 			= null;
  var destination 		= null;
  var waypoints 		= [];
  var directionsVisible = false;
  var tabRetour 		= new Array ();
	
  function initialize() {
    directionsDisplay 	= new google.maps.DirectionsRenderer();
    var myOptions 		= {
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	
	<?php
	// Déclaration du tableau JavaScript
	$tableau_JS="tabRetour";

	foreach($tabRetour as $keyItems => $valueItems)
	{
		printf("%s[%s]= new Array ();\n",$tableau_JS, $keyItems);
		foreach($valueItems as $keyItem => $valueItem)
		{	
			printf("%s[%s][%s] =%s;\n",$tableau_JS, $keyItems, $keyItem,
				is_string($valueItem) ?  "\"".addslashes($valueItem)."\"" : $valueItem);
		}
	}
	?>
	calcRoute();
  }

  function calcRoute() {
	reset();
	
	/*<?php
	
	echo 'origin = new google.maps.LatLng(44.837789, -0.57918);';
	echo 'destination = new google.maps.LatLng(45.01843, -0.623513);';
	
	//echo 'waypoints.push({ location: new google.maps.LatLng(44.837789, -0.57918), stopover: true });';
	echo 'waypoints.push({ location: new google.maps.LatLng(44.533185, -0.33283), stopover: true });';
	echo 'waypoints.push({ location: new google.maps.LatLng(45.0405066, -0.5553528), stopover: true });';
	echo 'waypoints.push({ location: new google.maps.LatLng(44.894387, -0.155729), stopover: true });';
	echo 'waypoints.push({ location: new google.maps.LatLng(45.3192216, -0.791979), stopover: true });';
	echo 'waypoints.push({ location: new google.maps.LatLng(44.7640655, -0.6606029), stopover: true });';
	//echo 'waypoints.push({ location: new google.maps.LatLng(45.01843, -0.623513), stopover: true });';
	?>*/
	
	// origin = new google.maps.LatLng(44.837789, -0.57918);
	// destination = new google.maps.LatLng(45.3192216, -0.791979);
	// waypoints.push({ location: new google.maps.LatLng(44.533185, -0.33283), stopover: true });
	
	/*addMarker(44.837789, -0.57918);
	addMarker(44.533185, -0.33283);
	addMarker(45.3192216, -0.791979);*/	

	var idDate = getRadioValue();
	var nbReservations = getNombreReservations (idDate);
	//alert ("nbreservation :" + nbReservations);
	//alert("radio value : " + idDate);
	if (nbReservations == 0) {
		// On ne fait rien
	}
	else if (nbReservations == 1) {
		// afficher un seul point
	}
	else if (nbReservations > 1) {
		// afficher les itineraires
		
		// Cas d'affichage de l'ensemble des reservations
		if (idDate == '0') {
			origin 		= new google.maps.LatLng(tabRetour [0] [0], tabRetour [0] [1]);
			destination	= new google.maps.LatLng(tabRetour [tabRetour.length - 1] [0], tabRetour [tabRetour.length - 1] [1]);

			if (nbReservations > 2) {
				for (var i = 1; i < (tabRetour.length - 1); i++) {
					waypoints.push({ location: new google.maps.LatLng(tabRetour [i] [0], tabRetour [i] [1]), stopover: true });
				}
			}
		}
		// Cas d'affichage avec filtre jour
		else {
			var nbResPasse = 0;
			for (var j = 0; j < tabRetour.length; j++) {
				if (tabRetour [j] [2] == idDate) {
					nbResPasse++;
					if (nbResPasse == 1) {
						origin 		= new google.maps.LatLng(tabRetour [j] [0], tabRetour [j] [1]);
					}
					else if (nbResPasse == nbReservations) {
						destination = new google.maps.LatLng(tabRetour [j] [0], tabRetour [j] [1]);
					}
					else {
						waypoints.push({ location: new google.maps.LatLng(tabRetour [j] [0], tabRetour [j] [1]), stopover: true });
					}
				}
			}
		}
		
		var request = {
			origin: origin,
			destination: destination,
			waypoints: waypoints,
			travelMode: google.maps.DirectionsTravelMode.DRIVING,
			avoidHighways: document.getElementById('highways').checked,
			avoidTolls: document.getElementById('tolls').checked
		};
		
		directionsService.route(request, function(response, status) {
		  if (status == google.maps.DirectionsStatus.OK) {
			directionsDisplay.setDirections(response);
		  }
		});
		
		//clearMarkers();
		directionsVisible = true;
	}
  }
  
  function getRadioValue() {
	 for (var i = 0; i < document.formulaire.date.length; i++)
     {
		if (document.formulaire.date[i].checked)
			return document.formulaire.date[i].value;
     }
	 return '0';
  }
  
  function getNombreReservations (idJour) {
	var nbReservations = 0;
	if (idJour != '0') 
	{
		for (var i = 0; i < tabRetour.length; i++) 
		{
			if (tabRetour [i][2] == idJour)
				nbReservations++;
		}
		return nbReservations;
	}
	else
		return tabRetour.length;
  }
  
  function reset() {
    directionsVisible = false;
	origin 		= null;
    destination = null;
    waypoints 	= [];
	directionsDisplay.setMap(null);
    directionsDisplay.setPanel(null);
    directionsDisplay = new google.maps.DirectionsRenderer();
    directionsDisplay.setMap(map);
    directionsDisplay.setPanel(document.getElementById("directionsPanel"));
  }
</script>
</head>
<body onload="initialize()" style="font-family: sans-serif;">
  <table style="width: 600px">
    <tr>
      <td><input type="checkbox" id="highways" checked />Eviter l'autoroute</td>
	  <td>
		<form name="formulaire">
		<input type="radio" name="date" id="date" value="0" checked /> Toutes les reservations<br />
		<?php 
		foreach ($datesReservation as $keyItems => $valueItems) { 
		$val = $keyItems + 1; 
		printf("<input type=\"radio\" name=\"date\" id=\"date\" value=\"%s\" /> %s <br />", $val, $valueItems); 
		} 
		?>
		</form>
	  </td>
    </tr>
    <tr>
      <td><input type="checkbox" id="tolls" checked />Eviter peages</td>
      <td><input type="button" value="Voir itineraire !" onclick="calcRoute()" /></td>
      <td></td>
    </tr>
  </table>
  <div style="position:relative; border: 1px; width: 1020px; height: 600px;">
    <div id="map_canvas" style="border: 1px solid black; position:absolute; width:600px; height:600px"></div>
    <div id="directionsPanel" style="position:absolute; left: 610px; width:410px; height:600px; overflow: auto"></div>
  </div>
  
<!-- ------------------------------------------ -->
<!-- ------------ Fin fonction ---------------- -->
<!-- ------------------------------------------ -->
  
</body>
</html>