<?php

class Chaine
{
	//Attributs
	private $texte; 
	private $dateFr; 
	private $ldate;
	private $jour;
	private $mois;
	private $annee;
	private $convDate;
	private $convHeure;
	
	//Operations
		function __construct($as_chaine,$as_convDate,$as_convHeure){
		  	$this->texte = $as_chaine;
		 	$this->convDate = $as_convDate;
		 	$this->convHeure = $as_convHeure;
		 	//echo "<br />construct_Chaine  ",$as_convDate," resultat=",$this->convDate;
		 	//echo "<br />construct_Chaine  ",$as_convHeure," resultat=",$this->convHeure;
		 	//echo "<br />construct_Chaine = ",$as_chaine," + ",$as_convDate," + "," resultat=",$as_convHeure,"<br />";
		 }
		function convHeure(){
			$ldate=strlen($this->convHeure);
			if($ldate!=8){
			$heure = substr($this->convHeure,'0','2');
			$minute = substr($this->convHeure,'2','2');
			//echo "<br/>heur 10car=",$heure," ",$minute;
			$this->varh=$heure.":".$minute.":"."00";	
		 	//$this->varh=implode('G:i',$this->convHeure);
		  	return ($this->varh);}else{return ($this->convHeure);}
		}
		
		function suppEspace(){ //enleve les espace et met en majuscule
		  	$this->var1=preg_replace('/ /','', $this->texte);
		 	$this->var1=strtoupper($this->var1);
		  	return 	($this->var1);
		 }
		function dateFr() {
		 	$ldate=strlen($this->convDate);
		 	//echo "<br/>longueur de la date =",$ldate;
		 	switch ($ldate) {
			case 10://date saisie=le 3eme car est diff de /
				if (substr($this->convDate,'2','1')!="/" ){
				$jour = substr($this->convDate,'0','2');
				$mois = substr($this->convDate,'2','2');
				$annee = substr($this->convDate,'6','4');
				//echo "<br/>date 10car1=",$jour," / ",$mois," / ",$annee;
				$this->var2=$jour."/".$mois."/".$annee;	
				}
				if (substr($this->convDate,'4','1')=="-" ){
				$jour = substr($this->convDate,'8','2');
				$mois = substr($this->convDate,'5','2');
				$annee = substr($this->convDate,'0','4');
				//echo "<br/>date 10car2=",$jour," - ",$mois," - ",$annee;
				$this->var2=$jour."/".$mois."/".$annee;	
				}
				else{$this->var2=$this->convDate;}
				break;
			case 9 ://date saisie=1/07/2008
				if (substr($this->convDate,'1','1')=="/" or substr($this->date,'1','1')==" "){
				$jour = substr($this->convDate,'0','1');
				$mois = substr($this->convDate,'2','2');
				$annee = substr($this->convDate,'5','4');
				//echo "<br/>date 9car=",$jour," ",$mois," ",$annee;
				$this->var2="0".$jour."/".$mois."/".$annee;
				}
				break;
			case 8://date saisie=10072008
				if (substr($this->convDate,'2','1')=='/'){
				$jour = substr($this->convDate,'0','2');
				$mois = substr($this->convDate,'3','2');
				$annee = substr($this->convDate,'6','2');
				if ($annee>"30"){$an="19";}else{$an="20";}
				//echo "<br/>date 7car !=/ =",$jour," ",$mois," ",$annee;
				$this->var2=$jour."/".$mois."/".$an.$annee;	
				}else{//date saisie=10/7/08
				$jour = substr($this->convDate,'0','2');
				$mois = substr($this->convDate,'2','2');
				$annee = substr($this->convDate,'4','4');
				//echo "<br/>date 7car=",$jour," ",$mois," ",$annee;
				$this->var2=$jour."/".$mois."/".$annee;
				}

				break;
			case 7://date saisie=1072008
				if (substr($this->convDate,'1','1')!='/'){
				$jour = substr($this->convDate,'0','1');
				$mois = substr($this->convDate,'1','2');
				$annee = substr($this->convDate,'3','4');
				//echo "<br/>date 7car !=/ =",$jour," ",$mois," ",$annee;
				$this->var2="0".$jour."/".$mois."/".$annee;	
				}else{//date saisie=10/7/08
				$jour = substr($this->convDate,'0','1');
				$mois = substr($this->convDate,'2','2');
				$annee = substr($this->convDate,'5','2');
				if ($annee>"30"){$an="19";}else{$an="20";}
				//echo "<br/>date 7car=",$jour," ",$mois," ",$annee;
				$this->var2="0".$jour."/".$mois."/".$an.$annee;
				}
				break;	
			case 5://date saisie=10708
				$jour = substr($this->convDate,'0','1');
				$mois = substr($this->convDate,'1','2');
				$annee = substr($this->convDate,'3','2');
				if ($annee>"30"){$an="19";}else{$an="20";}
				//echo "<br/>date 5car=",$jour," ",$mois," ",$annee;
				$this->var2="0".$jour."/".$mois."/".$an.$annee;
				break;
			case 6://date saisie=1/7/08
				if (substr($this->convDate,'1','1')=='/'){
				$jour = substr($this->convDate,'0','1');
				$mois = substr($this->convDate,'2','1');
				$annee = substr($this->convDate,'4','2');
				if ($annee>"30"){$an="19";}else{$an="20";}
				//echo "<br/>date 6car=",$jour," ",$mois," ",$annee;
				$this->var2="0".$jour."/"."0".$mois."/".$an.$annee;
				}else{//date saisie=100108
				$jour = substr($this->convDate,'0','2');
				$mois = substr($this->convDate,'2','2');
				$annee = substr($this->convDate,'4','2');
				if ($annee>"30"){$an="19";}else{$an="20";}
				//echo "<br/>date 6car=",$jour," ",$mois," ",$annee;
				$this->var2=$jour."/".$mois."/".$an.$annee;
				}
				break;
			case 4://date saisie=1708
				$jour = substr($this->convDate,'0','1');
				$mois = substr($this->convDate,'1','1');
				$annee = substr($this->convDate,'2','2');
				if ($annee>"30"){$an="19";}else{$an="20";}
				//echo "<br/>date 4car=",$jour," ",$mois," ",$annee;
				$this->var2="0".$jour."/"."0".$mois."/".$an.$annee;
				break;		
			default:
			$this->var2=$this->convDate;
			break;
			}
		 	//echo "<br />methode date FR= ",$ldate," jours=",$jour," mois=",$mois," an=",$annee," date=",$this->var2,"<br/>";
			//echo "return date fr=  ",$this->var2,"<br />";
			return ($this->var2);
	
		 }
	function convDateFrUs(){
		 	//echo "<br />convDate=",$this->convDate;
		 	$this->var3=implode('-',array_reverse(explode('/',$this->convDate)));
		 	//echo "<br />convDateFrUs=  ",$this->var3,"<br />";
		  	return ($this->var3);
		 }
	function convDateUsFr(){
		 	$this->var3=implode('/',array_reverse(explode('-',$this->convDate)));
		 	//echo "<br />convDateUsFr=  ",$this->var3,"<br />";
		 	return ($this->var3);
		 }
	function convMatriculeBdFr(){
		if(strlen($this->texte) == 10) $this->var4=substr($this->texte,0,2)." ".substr($this->texte,2,3)." ".substr($this->texte,5,5);
		else $this->var4 = $this->texte;
		return($this->var4);
		}
}
?>
