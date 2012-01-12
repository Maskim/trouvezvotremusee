<?php
	session_start();
	if(isset($_SESSION['connexion']) AND isset($_SESSION['niveau']) AND $_SESSION['niveau']== 2){ 
		require_once("./core/classes/ControleurConnexionPers.php");
		require_once ("./core/Modif.php");
	 
		$f = new ControleurConnexion;
		$region_dep = $f->consulter("*","region","","","","","","nomregion","");
		
		$a = new ControleurConnexion;
		$sql_nbregion = $a->consulter("COUNT('idregion')","region","","","","","","","");
		$nbregion = mysql_fetch_row($sql_nbregion);
		
		$b = new ControleurConnexion;
		$region = $b->consulter("*","region","","","","","","","");
		while($tab=mysql_fetch_array($region)){
			$tab_region[] = $tab['nomregion'];
			$tab_idregion[] = $tab['idregion'];
		}
		
		//liste de département
		$javas_dep = "";
		for($i=0; $i < $nbregion[0]; $i++){
			$javas_dep .= " \"$tab_region[$i]\" : [";
			$temp = listeDep($tab_idregion[$i]);
			$javas_dep .= "\"choisir departement\"";
			while($tab_temp = mysql_fetch_array($temp)){
				$javas_dep .= ", \"".$tab_temp['nomdep']."\"";
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
		<h1>Ajout de données</h1>
			<a href="./administration.html">Retour à l'accueil</a> <br /><br />
	<?php
		$ajout=$_POST['ajout'];
		switch($ajout)
		{
			case "musee":?>
				<form method="POST" action="./core/ajout.php">
					
					<p>Choisissez une région puis un département :</p>
					<fieldset>
						<select name="region" id="departement0" onchange="ajout(this)">
							<option value="defaut">Choisissez une région </option>
							<?php
								for($i=0; $i < $nbregion[0]; $i++){
									echo "<option value=\"".$tab_region[$i]."\">".$tab_region[$i]."</option>";
								}
							?>
						</select>
					</fieldset>
					
					<p>
						<input type="hidden" name="type" value="musee_dep" />
						<input type="submit" name="choix_reg_dep" value="selectionner" />
					</p>
				</form>
			<?php break;
			
			case "region" : ?>
			<!-- zone ajouter region -->
				<form method="POST" action="./core/ajout.php" >
					<fieldset>
						<legend>Ajouter une région </legend>
						
						<p>
							Nom de la région : <input type="text" name="nomregion" /> <br />
						</p>
						
						<p>
							<input type="hidden" name="type" value="ajout_region" />
							<input type="submit" name="ajout_region" value="envoyer" />
						</p>
					</fieldset>
				</form>
				<br /><br />
			<?php break; ?>
			
			
			<?php case "departement" : ?>
				<!-- zone ajouter departement -->
				<form method="POST" action="./core/ajout.php" >
					
					<?php
						$instlistereg = new ControleurConnexion;
						$listeregion = $instlistereg->consulter("*","region","","","","","","nomregion","");
					?>
					<fieldset>
						<legend><strong>Ajouter un département</strong></legend>
					
						<p>Choisissez la région du département : 
							<select name="idregion" >
							<?php
								while($tab_region=mysql_fetch_array($listeregion)){
									echo "<option value=\"".$tab_region['idregion']."\" >".$tab_region['nomregion']."</option>";
								}
							?>
							</select>
						</p>
						
						<p>
							Nom du département : <input type="text" name="nomdepartement" /> 
						</p>
						
						<p>
							<input type="hidden" name="type" value="ajout_departement" />
							<input type="submit" name="ajout_departement" value="envoyer" />
						</p>
					</fieldset>
				</form>
				<br /> <br />
			<?php break; ?>
			
			
			<?php case "ville" : ?>
				<!-- zone ajouter une ville -->
				<form method="POST" action="./core/ajout.php" >
					<fieldset>
						<legend><strong>Ajouter une ville</strong></legend>
						
						<p>
							Choisissez une région puis un département :
						</p>
						
						<fieldset>
							<select name="region" id="departement0" onchange="ajout(this)">
								<option value="defaut">Choisissez une région</option>
								<?php
									for($i=0; $i < $nbregion[0]; $i++){
										echo "<option value=\"".$tab_region[$i]."\">".$tab_region[$i]."</option>";
									}
								?>
							</select>
						</fieldset>
						
						<p>
							Entrez le nom de la ville à ajouter : <input type="text" name="nomville" /> 
						</p>
						
						<p>
							Entrez son code postale : <input type="text" name="cp" /> 
						</p>
						
						<p>
							<input type="hidden" name="type" value="ajout_ville" />
							<input type="submit" name="ajout_ville" value="ajouter" />
						</p>
					</fieldset>
				</form>
	<?php 
			break;
			} 
	?>
		<p>
			Vous vous êtes trompés ?<br/>Le retour c'est <a href="./administration.html">par ici.</a>
		</p>
		
	</div>
<?php 
	}	
	else {
?>
	<script type="text/javascript">
		document.location.href = "connexion.html";
	</script>
<?php
	}
?>