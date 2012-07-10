<?php
class ControleurConnexion
{
	// private $host="sql.olympe-network.com";
	// private $bdd="25848_musee";
	// private $login="25848_musee";
	// private $pass="grandia";
	
	private $host="mysql51-8.business";
	private $bdd="winetourbordo";
	private $login="winetourbordo";
	private $pass="ZQBT7vfu";
	public function consulter($select,$from,$join,$where,$like,$groupby,$having,$orderby,$limit)
	{
		require_once('Requete_Consultation.php');
		$data = new RequeteConsultation($this->host,$this->bdd,$this->login,$this->pass);
		$return_query = $data->query($select,$from,$join,$where,$like,$groupby,$having,$orderby,$limit);
		//$data->destruction();
		return $return_query;
	}
	public function modifier($table,$set,$where,$orderby,$limit)
	{
		require_once('Requete_Modification.php');
		$data = new RequeteModification($this->host,$this->bdd,$this->login,$this->pass);
		$return_query = $data->query($table,$set,$where,$orderby,$limit);
		//$data->destruction();
		return $return_query;
	}
	public function supprimer($from,$where,$orderby,$limit)
	{
		require_once('Requete_Suppression.php');
		$data = new RequeteSuppression($this->host,$this->bdd,$this->login,$this->pass);
		$return_query = $data->query($from,$where,$orderby,$limit);
		//$data->destruction();
		return $return_query;
	}
	public function inserer($insert,$tbl_name,$values)
	{
		require_once('Requete_Insertion.php');
		$data = new RequeteInsertion($this->host,$this->bdd,$this->login,$this->pass);
		$return_query = $data->query($insert,$tbl_name,$values);
		//$data->destruction();
		return $return_query;
	}
	public function destruction()
	{
		unset($this->host);
		unset($this->bdd);
		unset($this->login);
		unset($this->pass);
	}
}
?>
