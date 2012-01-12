<?php
	session_start();
	if(isset($_SESSION['connexion']) AND isset($_SESSION['niveau']) AND $_SESSION['niveau']== 2 AND $_SESSION['connexion']){ 
		require_once("./core/classes/ControleurConnexionPers.php");
		require_once ("./core/Modif.php");
		
		$a = new ControleurConnexion;
		$sql_nbregion = $a->consulter("COUNT(\"idregion\")","region","","","","","","","");
		$nbregion = mysql_fetch_row($sql_nbregion);
		
		$b = new ControleurConnexion;
		$region = $b->consulter("*","region","","","","","","nomregion","");
		
		while($tab = mysql_fetch_array($region)){
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

	<?php
		$affich = htmlspecialchars($_POST["affich"]);
		if($affich != "musee"){ 
	?>
	
	<div id="contenu">
		<h1>
			<?php 
				if($affich == 'departement') { echo 'Les départements'; }
				if($affich == 'region') { echo 'Les régions'; }
				if($affich == 'ville') { echo 'Les villes'; }
			?>
		</h1>
			<a href="./administration.html">Retour à l'accueil</a> <br /><br />
	<?php 	
			}
			
		switch($affich) {
			case "region" :
				$reg = new ControleurConnexion;
				$sql_reg = $reg -> consulter("*","region","","","","","","nomregion",""); ?>
				<fieldset>
					<legend>Liste des régions</legend>
					<ul>
						<?php while($tabreg = mysql_fetch_array($sql_reg)){ ?>
							<li> <?php echo $tabreg['nomregion']; ?> </li>
						<?php } ?>
					</ul>
				</fieldset>
	<?php		break;
			
			case "departement" :
				$reg = new ControleurConnexion;
				$sql_reg = $reg -> consulter("*","region","","","","","","nomregion",""); ?>
				<fieldset>
					<legend>Choix de la région</legend>
					<form method="POST" action="">
						<select name="idregion">
							<?php while($tabreg = mysql_fetch_array($sql_reg)){ ?>
								<option value="<?php echo $tabreg['idregion']; ?>"> <?php echo $tabreg['nomregion']; ?> </option>
							<?php } ?>
						</select>
						<input type="hidden" name="affich" value="departement" />
						<input type="submit" name="choixreg" value="choisir" />
					</form>
				</fieldset>
				
				<br/><br/>
				
				<?php
					if(isset($_POST['idregion'])){
						$idregion = $_POST['idregion'];
						$dep = new ControleurConnexion;
						$sql_dep = $dep -> consulter("*","departement","","idregion='$idregion'","","","","nomdep","");
						
						$region = new ControleurConnexion;
						$sql_region = $region -> consulter("nomregion","region","","idregion='$idregion'","","","","","");
						$nomregion = mysql_fetch_row($sql_region);
						?>
						<fieldset>
						<legend> Liste des départements pour la region :  <?php echo $nomregion[0]; ?></legend>
							<ul>
								<?php while($tabdep = mysql_fetch_array($sql_dep)){ ?>
									<li> <?php echo $tabdep['nomdep']; ?> </li>
								<?php } ?>
							</ul>
						</fieldset>
					<?php }else{
						$dep = new ControleurConnexion;
						$sql_dep = $dep -> consulter("nomdep","departement","","","","","","","");
						?>
						<fieldset>
						<legend> Liste des départements</legend>
							<ul>
								<?php while($tabdep = mysql_fetch_array($sql_dep)){ ?>
									<li> <?php echo $tabdep['nomdep']; ?> </li>
								<?php } ?>
							</ul>
						</fieldset>
					<?php }
			break;
			
			case "ville" :?>
				<fieldset>
					<legend> Choix de la région et du département : </legend>
					<form method="POST" action="" >
							<fieldset>
								choisir une région puis un département :
								<select name="region" id="departement0" onchange="ajout(this)">
									<option value="defaut"> choisir une region </option>
									<?php
										for($i=0; $i < $nbregion[0]; $i++){
											echo "<option value=\"".$tab_region[$i]."\">".$tab_region[$i]."</option>";
										}
									?>
								</select>
							</fieldset>
							<input type="hidden" name="affich" value="ville" />
							<p><input type="submit" name="choixville" value="Choisir" /></p>
					</form>
				</fieldset>
				<?php	
					if(isset($_POST['choix1'])){
						$nomdep = $_POST['choix1'];
						$iddep = new ControleurConnexion;
						$sql_iddep = $iddep -> consulter("iddep","departement","","nomdep = '$nomdep'","","","","","");
						$iddep = mysql_fetch_row($sql_iddep);
						
						$ville = new ControleurConnexion;
						$sql_ville = $ville -> consulter("*","ville","","iddep = '$iddep[0]'","","","","nomville",""); ?>
						<fieldset>
							<legend> Liste des villes pour le département : <?php echo $nomdep; ?> </legend>
							<ul>
								<?php while($tabville = mysql_fetch_array($sql_ville)){ ?>
									<li> <?php echo $tabville['CP']." ".$tabville['nomville']; ?> </li>
								<?php } ?>
							</ul>
						</fieldset>
					<?php }else{
						$ville = new ControleurConnexion;
						$sql_ville = $ville -> consulter("*","ville","","","","","","CP",""); ?>
						<fieldset>
							<legend> Liste des villes </legend>
							<ul>
								<?php while($tabville = mysql_fetch_array($sql_ville)){ ?>
									<li> <?php echo $tabville['CP']." ".$tabville['nomville']; ?> </li>
								<?php } ?>
							</ul>
						</fieldset>
					<?php }
			break;
			
			case "musee" : ?>
				<script language="javascript" type="text/javascript">window.location.replace("affichagemusee.html");</script>
			<?php
			break;
		}
?>
	</div>
<?php
		}
	else {
?>
	<script language="javascript" type="text/javascript">window.location.replace("connexion.html");</script>
<?php
	}
?>