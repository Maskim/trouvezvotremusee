<?php
class Requete_Consultation
{
	private $connexion;
	
	public function Requete_Consultation($host,$bdd,$login,$pass)
	{
		$this->host = $host;
		$this->bdd = $bdd;
		$this->login = $login;
		$this->pass = $pass;
	}
	public function query($select,$from,$join,$where,$groupby,$having,$orderby,$limit)
	{
		$this->connexion = mysql_connect($this->host,$this->login,$this->pass) or die(mysql_error());
		mysql_select_db($this->bdd, $this->connexion) or die("<span class=\"error\">Les parametres de consultation sont incorrect.</span>");
		$req = "SELECT ".$select." FROM ".$from;
		if(!empty($join)) $req .= " ".$join;
		if(!empty($where)) $req .= " WHERE ".$where;
		if(!empty($groupby)) $req .= " GROUP BY ".$groupby;
		if(!empty($having)) $req .= " HAVING ".$having;
		if(!empty($orderby)) $req .= " ORDER BY ".$orderby;
		if(!empty($limit)) $req .= " LIMIT ".$limit;
//echo "<br />","24 Requete_Consultation","<pre>",print_r($req),"</pre>";
		$sql = mysql_query($req, $this->connexion) or die("<span class=\"error\">Les parametres de consultation sont incorrect.</span>");
		//$sql = mysql_query($req, $this->connexion) or die(mysql_error());
		return $sql;
	}
	public function destruction()
	{mysql_close($this->connexion);}
}
?>
