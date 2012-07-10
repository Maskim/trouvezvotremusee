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
		$replace = array(" ", "-", "'", "(", ")");
		$accent = array('é', 'è', 'à', 'â', 'ç', 'ô');
		$no_accent = array('e', 'e', 'a', 'a', 'c', 'o');

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
?>