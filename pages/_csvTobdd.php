<?php
require_once("./core/classes/ControleurConnexionPers.php");

$row = 1;
$i = 0;
if (($handle = fopen("./musees.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        //echo "<p> $num champs à la ligne $row: <br /></p>\n";
        $row++;
        for ($c=0; $c < $num; $c++) {
            //echo $data[$c] . "<br />\n";
    		$array[$i][$c] = ($data[$c]);
        }
        $i++;
    }
    fclose($handle);
    //print_r($array);
}

$a = new ControleurConnexion();
$sql_region = $a->consulter("*", "region", "", "", "", "", "", "", "");

$all_nomregion;
$all_idregion;

while($region = mysql_fetch_array($sql_region)){
    $temp = str_replace(" ", "-", $region['nomregion']);
    $temp = str_replace("é", "e", $temp);
    $all_nomregion[] = strtolower($temp);
    $all_idregion[] = $region['idregion'];
}

//print_r($all_nomregion);
//print_r($all_idregion);

for($l = 1; $l < count($array); $l++){
    $region[] = $array[$l][0];
    $departement[] = $array[$l][1];
    $nomDuMusee[] = $array[$l][4];
    $adresse[] = $array[$l][5];
    $cp[] = $array[$l][6];
    $ville[] = $array[$l][7];
    $site[] = $array[$l][8];
    $fermetureannuel[] = $array[$l][9];
    $ouverture[] = $array[$l][10];
    $nocturne[] = $array[$l][11];
    $ferme[] = $array[$l][2];
}

for($i = 0; $i < count($region); $i++){
    $ok = false;
    for($j=0; $j < count($all_nomregion); $j++){
        $region[$i] = str_replace(" ", "-", $region[$i]);
        if(strtolower($region[$i]) == $all_nomregion[$j]){
            $region[$i] = $all_idregion[$j];
            $ok = true;
        }
    }
    if(!$ok){
        $add = strtolower($region[$i]);
        $add = str_replace("'", "\'", $add);
        $a->inserer("region","nomregion", "'".$add."'");
        $sql_new_region = $a->consulter("*", "region", "", "nomregion = '".$add."'", "", "", "", "", "");
        $newregion = mysql_fetch_row($sql_new_region);

        $all_nomregion[] = $newregion[1];
        $all_idregion[] = $newregion[0];

        $region[$i] = $newregion[0];
    }
}


$a = new ControleurConnexion();
$sql_dep = $a->consulter("*", "departement", "", "", "", "", "", "", "");

$all_nomdep;
$all_iddep;

while($dep = mysql_fetch_array($sql_dep)){
    $temp = str_replace(" ", "-", $dep['nomdep']);
    $temp = str_replace("é", "e", $temp);
    $all_nomdep[] = strtolower($temp);
    $all_iddep[] = $dep['iddep'];
}

//print_r($all_nomdep);
//print_r($all_iddep);

for($i = 0; $i < count($departement); $i++){
    $ok = false;
    for($j=0; $j < count($all_nomdep); $j++){
        $departement[$i] = str_replace(" ", "-", $departement[$i]);
        if(strtolower($departement[$i]) == $all_nomdep[$j]){
            $departement[$i] = $all_iddep[$j];
            $ok = true;
        }
    }
    if(!$ok){
        $add = strtolower($departement[$i]);
        $add = str_replace("'", "\'", $add);
        $a->inserer("departement","nomdep, idregion", "'".$add."', '".$region[$i]."'");
        $sql_new_region = $a->consulter("*", "departement", "", "nomdep = '".$add."'", "", "", "", "", "");
        $newregion = mysql_fetch_row($sql_new_region);

        $all_nomdep[] = $newregion[1];
        $all_iddep[] = $newregion[0];

        $departement[$i] = $newregion[0];
    }
}

$a = new ControleurConnexion();
$sql_ville = $a->consulter("*", "ville", "", "", "", "", "", "", "");

$all_nomville;
$all_idville;

while($ville_tab = mysql_fetch_array($sql_ville)){
    $temp = str_replace(" ", "-", $ville_tab['nomville']);
    $temp = str_replace("é", "e", $temp);
    $all_nomville[] = strtolower($temp);
    $all_idville[] = $ville_tab['idville'];
}

for($i = 0; $i < count($ville); $i++){
    $ok = false;
    for($j=0; $j < count($all_nomville); $j++){
        $ville[$i] = str_replace(" ", "-", $ville[$i]);
        if(strtolower($ville[$i]) == $all_nomville[$j]){
            $ville[$i] = $all_idville[$j];
            $ok = true;
        }
    }
    if(!$ok){
        $add = strtolower($ville[$i]);
        $add = str_replace("'", "\'", $add);
        $a->inserer("ville","nomville, CP, iddep", "'".$add."', '".$cp[$i]."', '".$departement[$i]."'");
        $sql_new_ville = $a->consulter("*", "ville", "", "nomville = '".$add."'", "", "", "", "", "");
        $newregion = mysql_fetch_row($sql_new_ville);

        $all_nomville[] = $newregion[1];
        $all_idville[] = $newregion[0];

        $ville[$i] = $newregion[0];
    }
}

for ($i=0; $i < count($nomDuMusee) ; $i++) { 
    $museeAdd = strtolower($nomDuMusee[$i]);
    $museeAdd = str_replace("'", "\'", $museeAdd);

    $museeAdresse = strtolower($adresse[$i]);
    $museeAdresse = str_replace("'", "\'", $museeAdresse);

    $siteinternetadd = strtolower($site[$i]);
    $siteinternetadd = str_replace("'", "\'", $siteinternetadd);

    $overtureadd = strtolower($ouverture[$i]);
    $overtureadd = str_replace("'", "\'", $overtureadd);

    $nocturneadd = strtolower($nocturne[$i]);
    $nocturneadd = str_replace("'", "\'", $nocturneadd);

    $fermAnAdd = strtolower($fermetureannuel[$i]);
    $fermAnAdd = str_replace("'", "\'", $fermAnAdd);

    $fermAdd = strtolower($ferme[$i]);
    $fermAdd = str_replace("'", "\'", $fermAdd);

    $a->inserer("musee","nom, adresse, siteinternet, ouverture, nocturne, fermetureAnnuelle, ferme, idville", "'".$museeAdd."', '".$museeAdresse."', 
                                    '".$siteinternetadd."', '".$overtureadd."', '".$nocturneadd."', '".$fermAnAdd."', '".$fermAdd."', '".$ville[$i]."'");
}

print_r($nomDuMusee);
?>