<?php
class Requete_Modification
{
	private $connexion;
	
	public function Requete_Modification($host,$bdd,$login,$pass)
	{
		$this->host = $host;
		$this->bdd = $bdd;
		$this->login = $login;
		$this->pass = $pass;
	}
	public function query($table,$set,$where,$orderby,$limit)
	{
		$this->connexion = mysql_connect($this->host,$this->login,$this->pass) or die(mysql_error());
		mysql_select_db($this->bdd, $this->connexion) or die(mysql_error());
		$req = "UPDATE ".$table." SET ".$set;
		if(!empty($where)) $req .= " WHERE ".$where;
		if(!empty($orderby)) $req .= " ORDER BY ".$orderby;
		if(!empty($limit)) $req .= " LIMIT ".$limit;
//echo "<br />","24 Requete_Modification","<pre>",print_r($req),"</pre>";
		$sql = mysql_query($req, $this->connexion) or die(mysql_error());
	}
	public function destruction()
	{mysql_close($this->connexion);}
}
?>
