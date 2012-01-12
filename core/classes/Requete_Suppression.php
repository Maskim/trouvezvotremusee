<?php
class RequeteSuppression
{
	private $connexion;
	private $host,$bdd,$login,$pass;
	public function RequeteSuppression($as_host,$as_bdd,$as_login,$as_pass)
	{
		$this->host = $as_host;
		$this->bdd = $as_bdd;
		$this->login = $as_login;
		$this->pass = $as_pass;
	}
	public function query($from,$where,$orderby,$limit)
	{
		$this->connexion = mysql_connect($this->host,$this->login,$this->pass) or die(mysql_error());
		mysql_select_db($this->bdd, $this->connexion) or die(mysql_error());
		$req = "DELETE FROM ".$from;
		if(!empty($where))$req .= " WHERE ".$where;
		if(!empty($orderby)) $req .= " ORDER BY ".$orderby;
		if(!empty($limit)) $req .= " LIMIT ".$limit;
		//echo "<br />","32 Requete_Suppresion","<pre>",print_r($req),"</pre>";
		$sql = mysql_query($req, $this->connexion) or die(mysql_error());
	}
	public function destruction()
	{
		mysql_close($this->connexion);
	}
}
?>
