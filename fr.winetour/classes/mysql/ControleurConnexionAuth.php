<?php
require_once('config.php');
class ControleurConnexionAuth
{
	private $host,$bdd,$login,$pass;
	public function ControleurConnexionAuth()
	{
		$this->host = serveur_mysql;
		$this->bdd = 'sif';
		$this->login = login_mysql;
		$this->pass = passwd_mysql;
	}
		
	public function consulter($select,$from,$join,$where,$groupby,$having,$orderby,$limit)
	{
		require_once('Requete_Consultation.php');
		$data = new Requete_Consultation($this->host,$this->bdd,$this->login,$this->pass);
		$return_query = $data->query($select,$from,$join,$where,$groupby,$having,$orderby,$limit);
		$data->destruction();
		return $return_query;
	}
	public function modifier($table,$set,$where,$orderby,$limit)
	{
		require_once('Requete_Modification.php');
		$data = new Requete_Modification($this->host,$this->bdd,$this->login,$this->pass);
		$return_query = $data->query($table,$set,$where,$orderby,$limit);
		$data->destruction();
		return $return_query;
	}
	
	public function supprimer($from,$where,$orderby,$limit)
	{
		require_once('Requete_Suppression.php');
		$data = new Requete_Suppression($this->host,$this->bdd,$this->login,$this->pass);
		$return_query = $data->query($from,$where,$orderby,$limit);
		$data->destruction();
		return $return_query;
	}
	
	public function inserer($insert,$tbl_name,$values)
	{
		require_once('Requete_Insertion.php');
		$data = new Requete_Insertion($this->host,$this->bdd,$this->login,$this->pass);
		$return_query = $data->query($insert,$tbl_name,$values);
		$data->destruction();
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
