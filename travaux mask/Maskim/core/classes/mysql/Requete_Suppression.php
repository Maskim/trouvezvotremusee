<?php
class Requete_Suppression
{
	private $connexion;
	
	public function Requete_Suppression($host,$bdd,$login,$pass)
	{
		$this->host = $host;
		$this->bdd = $bdd;
		$this->login = $login;
		$this->pass = $pass;
	}
	public function query($from,$where,$orderby,$limit)
	{
		$this->connexion = mysql_connect($this->host,$this->login,$this->pass) or die(mysql_error());
		mysql_select_db($this->bdd, $this->connexion) or die(mysql_error());
		$req = "DELETE FROM ".$from;
		if(!empty($where))$req .= " WHERE ".$where;
		if(!empty($orderby)) $req .= " ORDER BY ".$orderby;
		if(!empty($limit)) $req .= " LIMIT ".$limit;
		$sql = mysql_query($req, $this->connexion) or die(mysql_error());
	}
	public function destruction()
	{mysql_close($this->connexion);}
}
?>
