<?php
/*
Page:           db.php
Created:        Aug 2006
Last Mod:       Mar 18 2007
This page handles the database update if the user
does NOT have Javascript enabled.	
--------------------------------------------------------- 
ryan masuga, masugadesign.com
ryan@masugadesign.com 
Licensed under a Creative Commons Attribution 3.0 License.
http://creativecommons.org/licenses/by/3.0/
See readme.txt for full credit details.
--------------------------------------------------------- */
header("Cache-Control: no-cache");
header("Pragma: nocache");
require('_config-rating.php'); // get the db connection info

//getting the values
$vote_sent = preg_replace("/[^0-9]/","",$_REQUEST['j']);
$id_sent = preg_replace("/[^0-9a-zA-Z]/","",$_REQUEST['q']);
$ip_num = preg_replace("/[^0-9\.]/","",$_REQUEST['t']);
$units = preg_replace("/[^0-9]/","",$_REQUEST['c']);
$ip = $_SERVER['REMOTE_ADDR'];
$referer  = $_SERVER['HTTP_REFERER'];

if ($vote_sent > $units) die("Sorry, vote appears to be invalid."); // kill the script because normal users will never see this.

//connecting to the database to get some information
$query = mysql_query("SELECT nb_votant, note, ip FROM musee, ipvotant WHERE musee.idmusee='$id_sent' AND musee.idmusee = ipvotant.id")or die(" Error: ".mysql_error());
$numbers = mysql_fetch_assoc($query);
$checkIP = unserialize($numbers['ip']);
$count = $numbers['nb_votant']; //how many votes total
$current_rating = $numbers['note']; //total number of rating added together and stored
$sum = $vote_sent+$current_rating; // add together the current vote value and the total vote value
$tense = ($count==1) ? "vote" : "votes"; //plural form votes/vote

// checking to see if the first vote has been tallied
// or increment the current number of votes
($sum==0 ? $added=0 : $added=$count+1);

// if it is an array i.e. already has entries the push in another value
((is_array($checkIP)) ? array_push($checkIP,$ip_num) : $checkIP=array($ip_num));
$insertip=serialize($checkIP);

//IP check when voting
$voted=mysql_num_rows(mysql_query("SELECT ip FROM ipvotant WHERE ip LIKE '%".$ip."%' AND id='".$id_sent."' "));
if(!$voted) {     //if the user hasn't yet voted, then vote normally...


if (($vote_sent >= 1 && $vote_sent <= $units) && ($ip == $ip_num)) { // keep votes within range
	$update = "UPDATE ipvotant, musee SET nb_votant='".$added."', note='".$sum."', ip='".$insertip."' WHERE musee.idmusee='$id_sent' AND musee.idmusee = ipvotant.id";
	$result = mysql_query($update);		
} 
header("Location: $referer"); // go back to the page we came from 
exit;
} //end for the "if(!$voted)"
?>