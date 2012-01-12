<?php
class RequeteInsertion
{
	private $connexion;
	private $host,$bdd,$login,$pass;
	public function RequeteInsertion($host,$bdd,$login,$pass)
	{
		$this->host = $host;
		$this->bdd = $bdd;
		$this->login = $login;
		$this->pass = $pass;
	}
	public function query($insert,$tbl_name,$values)
	{
		$this->connexion = mysql_connect($this->host,$this->login,$this->pass) or die(mysql_error());
		mysql_select_db($this->bdd, $this->connexion) or die("<span class=\"error\">Les parametres d'insertion sont incorrect.</span>");
		$req = "INSERT INTO ".$insert." (".$tbl_name.") VALUES(".$values.")";
		// echo "<br />","18 Requete_Insertion","<pre>",print_r($req),"</pre>";
		$sql = mysql_query($req, $this->connexion) or die(mysql_error());
	}
	public function destruction()
	{
		mysql_close($this->connexion);
	}
}
?>
