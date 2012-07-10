<?php
class RequeteConsultation
{
	private $connexion;
	private $host,$bdd,$login,$pass;
	public function RequeteConsultation($as_host,$as_bdd,$as_login,$as_pass)
	{
		$this->host = $as_host;
		$this->bdd = $as_bdd;
		$this->login = $as_login;
		$this->pass = $as_pass;
	}
	public function query($select,$from,$join,$where,$like,$groupby,$having,$orderby,$limit)
	{
		$this->connexion = mysql_connect($this->host,$this->login,$this->pass) or die(mysql_error());
		mysql_select_db($this->bdd, $this->connexion) or die("<span class=\"error\">Les parametres de consultation sont incorrects.</span>");
		$req = "SELECT ".$select." FROM ".$from;
		if(!empty($join)) 		$req .= " ".$join;
		if(!empty($where)) 		$req .= " WHERE ".$where;
		if(!empty($like)) 		$req .= " LIKE ".$like;
		if(!empty($groupby)) 	$req .= " GROUP BY ".$groupby;
		if(!empty($having)) 	$req .= " HAVING ".$having;
		if(!empty($orderby)) 	$req .= " ORDER BY ".$orderby;
		if(!empty($limit)) 		$req .= " LIMIT ".$limit;
		//echo "<br />","24 Requete_Consultation","<pre>",print_r($req),"</pre>";
		$sql = mysql_query($req, $this->connexion) or die("<span class=\"error\">Les parametres de consultation sont incorrects.</span>
			<br />24 Requete_Consultation <pre>".print_r($req)."</pre><br />".mysql_error());
		//$sql = mysql_query($req, $this->connexion) or die(mysql_error());
		return $sql;
	}
	public function destruction()
	{
		mysql_close($this->connexion);
	}
}
?>
