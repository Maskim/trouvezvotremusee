<?php
	function findRegion($search_region){

		$a = new ControleurConnexion();
		$sql = $a -> consulter("*", "region", "", "", "", "", "", "", "");

		while($region = mysql_fetch_array($sql)){
			$nom_region = prepareString($region['nomregion']);
			if($nom_region == $search_region){
				return $region['nomregion'];
			}
		}

		return "";
	}

	function findDepartement($search_dep){

		$a = new ControleurConnexion();
		$sql = $a -> consulter("*", "departement", "", "", "", "", "", "", "");

		while($dep = mysql_fetch_array($sql)){
			$nom_dep = prepareString($dep['nomdep']);
			if($nom_dep == $search_dep){
				return $dep['nomdep'];
			}
		}

		return "";
	}

	function prepareString($string){
		$replace = array(" ", "-", "'", "(", ")", '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'cedex');
		$accent = array('é', 'è', '&eacute;', '&egrave;', 'Ã¨', 'à', 'â', 'ç', 'ô', 'î');
		$no_accent = array('e', 'e', 'e', 'e', 'e','a', 'a', 'c', 'o', 'i');

		$string = str_replace($replace, "", $string);
		$string = str_replace($accent, $no_accent, $string);
		$string = strtolower($string);

		return $string;
	}

	function findCurrentSearchInString($search, $string)
	{
		$stringTemp = strtolower($string);
		$searchTemp = strtolower($search);
		$pos = strpos($stringTemp, $searchTemp);

		$return = substr($string, 0, $pos) . "<strong>" . substr($string, $pos, strlen($search) ) . "</strong>" .
					substr($string, $pos + strlen($search), strlen($string));

		return $return;
	}

	function isAlreadyExist($table, $champ, $test, $id){
		

		$a = new ControleurConnexion();
		$result = $a->consulter("COUNT(*)", "$table", "", "$champ = '$test' AND idutil != $id", "", "", "", "", "");

		$nb_result = mysql_fetch_row($result);

		if($nb_result[0] == 0){
			return false;
		}else{
			return true;
		}

	}

	function isGoodMdp($id, $mdp){
		$a = new ControleurConnexion();
		$result = $a->consulter("mdp", "utilisateur", "", "idutil = '".$id."'", "", "", "", "", "");

		$result = mysql_fetch_row($result);

		if($result[0] == $mdp){
			return true;
		}else
			return false;
	}

	function userIsLogin(){
		if(isset($_SESSION['iduser']))
			return true;
		else
			return false;
	}

	function findMuseeForLetter($lettre){
		$lettre = strtolower($lettre);
		$a = new ControleurConnexion();
		$sql = $a->consulter("nomville, CP, nom", "musee, ville", "", "ville.idville = musee.idville", "", "", "" , "nom", "");

		$liste = array();
		$i = 0;

		while ($musee = mysql_fetch_array($sql)){
			$museeExist = false;
			$recherche = prepareString(utf8_encode(substr($musee['nom'], 0, 5)));

			if(substr($musee['nom'], 0, 1) == $lettre && $recherche != 'musee'){
				$liste[$i][0] = $musee['nom'];
				$museeExist = true;
			}

			if($recherche == "musee"){
				$find = utf8_encode($musee['nom']);

				$find = str_replace('musée', '', $find);
				$find = str_replace('museum', '', $find);
				$find = str_replace('muséum', '', $find);
				$find = str_replace('de', '', $find);
				$find = str_replace('des', '', $find);
				$find = str_replace('du', '', $find);
				$find = str_replace("d'", '', $find);
				$find = str_replace("l'", '', $find);
				$find = str_replace('la', '', $find);
				$find = str_replace('le', '', $find);
				$find = str_replace(' ', '', $find);

				if(substr($find, 0, 1) == $lettre){
					$liste[$i][0] = $musee['nom'];
					$museeExist = true;
				}
			}

			if($museeExist)
				$liste[$i][1] = $musee['CP'] . ' ' . $musee['nomville'];

			$i++;
		}

		return $liste;
	}

	function findCityForIdMusee($id){
		$a = new ControleurConnexion();
		$sql = $a->consulter("nomville", "ville, musee", "", "ville.idville = musee.idville AND idmusee = $id", "", "", "", "", "");

		$return = mysql_fetch_row($sql);

		return $return[0];
	}

	function adapterNomMusee($musee){
		$find = utf8_encode($musee);

		$find = str_replace('musée', '', $find);
		$find = str_replace('museum', '', $find);
		$find = str_replace('muséum', '', $find);
		$find = str_replace('des ', '', $find);
		$find = str_replace('de ', '', $find);
		$find = str_replace('du ', '', $find);
		$find = str_replace("d'", '', $find);
		$find = str_replace(" l'", '', $find);
		$find = str_replace('la ', '', $find);
		$find = str_replace('le ', '', $find);
		$find = str_replace(' ', '', $find);
		$find = str_replace('-', '', $find);

		return $find;
	}

	function trierMusee($musee){
		foreach ($musee as $value) {
			$temp[] = adapterNomMusee($value[0]);
		}

		for($i = 0; $i < count($temp) - 1; $i++){
			for($j = $i + 1; $j < count($temp); $j++){
				if($temp[$i] > $temp [$j]){
					$inWait = $temp[$i];
					$temp[$i] = $temp[$j];
					$temp[$j] = $inWait;

					$inWait = $musee[$i];
					$musee[$i] = $musee[$j];
					$musee[$j] = $inWait;
				}
			}
		}

		return $musee;
	}
?>