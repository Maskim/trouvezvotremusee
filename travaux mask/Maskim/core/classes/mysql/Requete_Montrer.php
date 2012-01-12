<?php
class Requete_Montrer
{
	private $connexion;
	
	public function Requete_Montrer($host,$bdd,$login,$pass)
	{
		$this->host = $host;
		$this->bdd = $bdd;
		$this->login = $login;
		$this->pass = $pass;
	}
	public function query($table)
	{
		$this->connexion = mysql_connect($this->host,$this->login,$this->pass) or die(mysql_error());
		mysql_select_db($this->bdd, $this->connexion) or die("<span class=\"error\">Les paramètres de consultation sont incorrect.</span>");
		$req = "SHOW FULL FIELDS FROM ".$table;
		$sql = mysql_query($req, $this->connexion) or die(mysql_error());
		//$sql = mysql_query($req, $this->connexion) or die(mysql_error());
		return $sql;
	}
	public function destruction()
	{mysql_close($this->connexion);}
}
?>
