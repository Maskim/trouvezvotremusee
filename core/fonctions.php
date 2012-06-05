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

	function prepareString($string){
		$replace = array(" ", "-", "'");
		$accent = array('é', 'è', 'à', 'ç');
		$no_accent = array('e', 'e', 'a', 'c');

		$string = str_replace($replace, "", $string);
		$string = str_replace($accent, $no_accent, $string);
		$string = strtolower($string);

		return $string;
	}
?>