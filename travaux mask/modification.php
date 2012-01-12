<?php require_once("header.php"); ?>

<!-- zone php -->
<?php
	require_once ("./core/classes/ControleurConnexionPers.php");
	require_once ("./core/Modif.php");
	
	
	$a = new ControleurConnexion;
	$sql_nbregion = $a->consulter("COUNT(\"idregion\")","region","","","","","","");
	$nbregion = mysql_fetch_row($sql_nbregion);
	
	$b = new ControleurConnexion;
	$region = $b->consulter("*","region","","","","","","");
	while($tab=mysql_fetch_array($region)){
		$tab_region[] = $tab['nomregion'];
		$tab_idregion[] = $tab['idregion'];
	}
	
	//liste de département
	$javas_dep= "";
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


<body>
	<a href="index.php"> retour index </a> <br />
	<?php
		$modif=$_POST["modif"];
		switch($modif)
		{
	
			case "musee" : ?>			
				zone d'affichage des différents musées :
				<form action="" method="POST">
				<fieldset>
					<select name="region" id="departement0" onchange="ajout(this)">
						<option value="defaut"> choisir une region </option>
						<?php
							for($i=0; $i < $nbregion[0]; $i++){
								echo "<option value=\"".$tab_region[$i]."\">".$tab_region[$i]."</option>";
							}
						?>
					</select>
				</fieldset>
					<p><input type="submit" value="envoyer" /></p>

				</form>
				<?php echo "<br />Vous vous etes trompé ??? Pas d'inquiétude, le retout c'est <a href=\"../index.php\">par içi </a>!!! "; ?>
			<?php break ?>
			
			<!-- zone modification region -->
			<?php case "region" :?>
				<fieldset>
					<legend> modifier une région </legend>
					<?php
						//affichage des régions
						$instreg = new ControleurConnexion;
						$varreg = $instreg -> consulter("*","region","","","","","",""); ?>
						<form method="POST" action="./core/modifaction.php">
							<ul>
								<?php while($tabregion = mysql_fetch_array($varreg)){?>	
										<form method="POST" action="./core/modifaction.php" >
											<li> <?php echo $tabregion['nomregion']; ?> </li>
											<input type="submit" name="action" value="modifier" />
											<input type="submit" name="action" value="supprimer" />
											<input type="hidden" name="nomregion" value="<?php echo $tabregion['nomregion']; ?>" />
											<input type="hidden" name="idregion" value="<?php echo $tabregion['idregion']; ?>" />
											<input type="hidden" name="type" value="modifier_region" />
										</form>
								<?php }	?>
							</ul>
				</fieldset>
				<?php echo "<br />Vous vous etes trompé ??? Pas d'inquiétude, le retout c'est <a href=\"../index.php\">par içi </a>!!! "; ?>
			<?php break; ?>
			
			
			<?php case "departement" : ?>
				<fieldset>
					<legend> modifier un département </legend>
					<form action="./core/modifaction.php" method="POST">
						<fieldset>
									<select name="region" id="departement0" onchange="ajout(this)">
										<option value="defaut"> choisir une region </option>
										<?php
											for($i=0; $i < $nbregion[0]; $i++){
												echo "<option value=\"".$tab_region[$i]."\">".$tab_region[$i]."</option>";
											}
										?>
									</select>
						</fieldset>
						<input type="hidden" name="type" value="modifier_dep" />
						<p><input type="submit" name="modif_dep" value="modifer" /></p>
					</form>
				</fieldset>
				<?php echo "<br />Vous vous etes trompé ??? Pas d'inquiétude, le retout c'est <a href=\"../index.php\">par içi </a>!!! "; ?>
			<?php break; ?>
			
			<?php case "ville" : ?>
				<fieldset>
					<legend> modifier une ville </legend>
					<form method="POST" action="./modification.php" >
						<fieldset>
							choisir une région et un département : <br />
							<select name="region" id="departement0" onchange="ajout(this)">
								<option value="defaut"> choisir une region </option>
								<?php
									for($i=0; $i < $nbregion[0]; $i++){
										echo "<option value=\"".$tab_region[$i]."\">".$tab_region[$i]."</option>";
									}
								?>
							</select>
						</fieldset>
						<input type="hidden" name="modif" value="choix_reg_dep" />
						<p><input type="submit" name="ville" value="envoyer" /></p>
					</form>
				</fieldset>
				<?php echo "<br />Vous vous etes trompé ??? Pas d'inquiétude, le retout c'est <a href=\"../index.php\">par içi </a>!!! "; ?>
			<?php break; ?>
			
			<?php case 'choix_reg_dep' : ?>
				<?php $region=$_POST['region'];
				$nomdep=$_POST['choix1'];
				
				$rech_id_dep = new ControleurConnexion;
				$sql_rech_id_dep = $rech_id_dep-> consulter("iddep","departement","","nomdep='$nomdep'","","","","");
				$iddep=mysql_fetch_row($sql_rech_id_dep);
				
				$sql_ville = new ControleurConnexion;
				$rech_ville = $sql_ville -> consulter("*","ville","","iddep='$iddep[0]'","","","",""); ?>
				
				<fieldset>
					<legend><?php echo $region." et ".$nomdep ;?></legend>
					<?php while($tab_ville=mysql_fetch_array($rech_ville)){ ?>
						<form method="POST" action="./core/modifaction.php" >
							<fieldset>
								<legend><?php echo $tab_ville['nomville']; ?></legend>
								<!--Choisir la ville à modifier : 
								<select name="ville">
										<option value="<?php //echo $tab_ville['nomville']; ?>"><?php //echo $tab_ville['nomville'];?></option>
									
								</select> <br />-->
								<p>le code postale de la ville : <?php echo $tab_ville['CP']; ?> </p>
								<input type="hidden" name="type" value="modifier_ville" />
								<input type="hidden" name="ville" value="<?php echo $tab_ville['nomville']; ?>"/>
								<input type="hidden" name="cp" value="<?php echo $tab_ville['CP']; ?>"/>
								<input type="hidden" name="region" value="<?php echo $region; ?>"/>
								<input type="hidden" name="dep" value="<?php echo $nomdep; ?>"/>
								<input type="submit" name="modif_ville" value="modifier" />
								<input type="submit" name="modif_ville" value="modifier-departement" />
								<input type="submit" name="modif_ville" value="modifier-region" />
							</fieldset>
						</form>
					<?php } ?>
				</fieldset>
				<?php echo "<br />Vous vous etes trompé ??? Pas d'inquiétude, le retout c'est <a href=\"../index.php\">par içi </a>!!! "; ?>
			<?php break; ?>
		<?php }	?>
		
</body>
</html>