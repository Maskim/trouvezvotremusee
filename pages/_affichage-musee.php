<?php
	if(isset($_SESSION['connexion']) AND isset($_SESSION['niveau']) AND $_SESSION['niveau']== 5){ 
		require_once("./core/classes/ControleurConnexionPers.php");
		require_once ("./core/Modif.php");
	
	
		$a = new ControleurConnexion;
		$sql_nbregion = $a->consulter("COUNT(\"idregion\")","region","","","","","","","");
		$nbregion = mysql_fetch_row($sql_nbregion);
		
		$b = new ControleurConnexion;
		$region = $b->consulter("*","region","","","","","","","");
		while($tab = mysql_fetch_array($region)){
			$tab_region[] = $tab['nomregion'];
			$tab_idregion[] = $tab['idregion'];
		}
		
		$c = new ControleurConnexion;
		$departement = $c -> consulter("*","departement","","","","","","nomdep","");
		while($tab2 = mysql_fetch_array($departement)){
			$tab_dep[] = $tab2['nomdep'];
			$tab_iddep[] = $tab2['iddep'];
		}
		
		$d = new ControleurConnexion;
		$sql_nbdep = $d->consulter("COUNT(\"iddep\")","departement","","","","","","","");
		$nbdep = mysql_fetch_row($sql_nbdep);
		
		//liste de département
		$javas_dep= "";
		for($i=0; $i < $nbregion[0]; $i++){
			$javas_dep .= " \"$tab_region[$i]\" : [";
			$temp = listeDep($tab_idregion[$i]);
			$javas_dep .= "\"Choisir departement\"";
			while($tab_temp = mysql_fetch_array($temp)){
				$javas_dep .= ", \"".$tab_temp['nomdep']."\"";
			}
			$javas_dep .= "],\n";
		}
		for($j=0; $j < $nbdep[0]; $j++){
			$javas_dep .= " \"$tab_dep[$j]\" : [";
			$temp2 = listeVille($tab_iddep[$j]);
			$javas_dep .= "\"Choisir ville\"";
			while($tab_temp2 = mysql_fetch_array($temp2)){
				$javas_dep .= ", \"".$tab_temp2['nomville']."\"";
			}
			$javas_dep .= "],\n";
		}
		$javas_dep .= " \"une blague\" : [\"ahah\", \"hihi\"]";
		
		
		//liste des villes
		$javas_ville;
?>

<script type="text/javascript">
<!--
list_choix = {
<?php
	echo $javas_dep;
?>
}

function ajout(selection){
	nb_select = selection.parentNode.getElementsByTagName("select").length;
	if ( selection == selection.parentNode.getElementsByTagName("select")[nb_select-1] ) {
		element_select = selection;
		selection = selection.options[selection.selectedIndex].value;
		if ( list_choix[selection] ) {
			new_liste = document.createElement("select");
			truc = element_select.parentNode.appendChild(new_liste);
			if ( document.all) {
				new_liste.outerHTML = "<select id=\"choix"+nb_select+"\" name=\"choix"+nb_select+"\" onchange=\"ajout(this)\"></select>";
			}
			else {
				new_liste.setAttribute("id", "choix"+nb_select);
				new_liste.setAttribute("name", "choix"+nb_select);
				new_liste.setAttribute("onchange", "ajout(this)");
			}
			for (var i=0; i<list_choix[selection].length; i++) {
				new_option = document.createElement("option");
				document.getElementById("choix"+nb_select).appendChild(new_option);
				new_option.value = list_choix[selection][i];
				new_option.text = list_choix[selection][i];
			}
		}
	}
	else {
		selection.parentNode.removeChild(selection.nextSibling);
		ajout(selection)
	}
}
//-->
</script>
	
	<div id="contenu">
		<h1>Les musées</h1>
			<a href="./administration.html">Retour à l'accueil</a> <br /><br />
			
	<fieldset>
		<legend> Choix de la région, du département et de la ville : </legend>
		<form method="POST" action="" >
				<fieldset>
					Choisir une région puis un département et enfin une ville :
					<select name="region" id="departement0" onchange="ajout(this)">
						<option value="defaut"> Choisir une région </option>
						<?php
							for($i=0; $i < $nbregion[0]; $i++){
								echo "<option value=\"".$tab_region[$i]."\">".$tab_region[$i]."</option>";
							}
						?>
					</select>
				</fieldset>
				<p><input type="submit" name="choixville" value="Choisir" /></p>
		</form>
	</fieldset> <br />
	<?php	
		if(isset($_POST['choix2'])){
			$nomville = $_POST['choix2'];
			$idville = new ControleurConnexion;
			$sql_idville = $idville -> consulter("idville","ville","","nomville = '$nomville'","","","","","");
			$idville = mysql_fetch_row($sql_idville);
			
			$musee = new ControleurConnexion;
			$sql_musee = $musee -> consulter("*","musee","","idville = '$idville[0]'","","","","nom",""); ?>
			<fieldset>
				<legend> Liste des musées de la ville : <?php echo $nomville; ?> </legend>
				<ul>
					<?php while($tabmusee = mysql_fetch_array($sql_musee)){ ?>
						<li> <?php echo $tabmusee['nom']; ?> </li>
					<?php } ?>
				</ul>
			</fieldset>
		<?php } else{
			$musee = new ControleurConnexion;
			$sql_musee = $musee -> consulter("*","musee","","","","","","nom",""); ?>
			<fieldset>
				<legend> Liste des musées </legend>
				<ul>
					<?php while($tabmusee = mysql_fetch_array($sql_musee)){ ?>
						<li> <?php echo $tabmusee['nom']; ?> </li>
					<?php } ?>
				</ul>
			</fieldset>
<?php 
			} 
	}
	else {
?>
	<script language="javascript" type="text/javascript">window.location.replace("connexion.html");</script>
<?php
	}
?>