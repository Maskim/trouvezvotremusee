<?php
class Requete_Drop
{
	private $connexion;
	
	public function Requete_Drop($host,$bdd,$login,$pass)
	{
		$this->host = $host;
		$this->bdd = $bdd;
		$this->login = $login;
		$this->pass = $pass;
	}
	public function query($schema)
	{
		$this->connexion = mysql_connect($this->host,$this->login,$this->pass) or die(mysql_error());
		$req = "DROP DATABASE IF EXISTS ".$this->bdd;
		$sql = mysql_query($req, $this->connexion) or die(mysql_error());
	}
	public function destruction()
	{mysql_close($this->connexion);}
}
?>
