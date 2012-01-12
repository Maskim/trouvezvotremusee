<?php
	session_start();
	require_once ('/classes/ControleurConnexionPers.php');
	//zone modifier une région
	$type = $_POST['type'];
	switch($type)
	{
		case "modifier_region" :
			$nomregion = $_POST['nomregion'];
			$idregion = $_POST['idregion'];
			$action = $_POST["action"];
			if($action == "modifier"){
				echo "choisissez la nouvelle region qui remplacera : ".$nomregion;
				echo 
				"<form method=\"POST\" action=\"modifaction.php\">
					<input type=\"text\" name=\"nouvregion\" value=\"$nomregion\"/>
					<input type=\"hidden\" name=\"ancienregion\" value=\"".$nomregion."\" />
					<input type=\"hidden\" name=\"type\" value=\"envoyer_region\" />
					<input type=\"submit\" name=\"modif_region\" value=\"envoyer\" />
				</form>";
			}elseif($action == "supprimer"){
			
			}
			echo "<br />Vous vous etes trompé ??? Pas d'inquiétude, le retout c'est <a href=\"../modification.php\">par içi </a>!!! ";
		break;
		
		case "envoyer_region" :
			$ancienregion = $_POST['ancienregion'];
			$nouvregion = $_POST['nouvregion'];
			$a = new ControleurConnexion;
			$nouvregion = $a->modifier("region","nomregion='$nouvregion'","nomregion='$ancienregion'","",""); 
			header ("Location: ../index.php");
		break;

		// zone modifier un departement
		case "modifier_dep" :
			if($_POST['choix1']){
				$nomregion=$_POST['region'];
				$nomdep=$_POST['choix1'];
				if($nomregion != "" && $nomdep != "choisir departement"){
					echo "choisissez le département qui remplacera : ".$nomdep;
					echo 
					"<form method=\"POST\" action=\"modifaction.php\">
						<input type=\"text\" name=\"nouvdep\" value=\"".$nomdep."\" />
						<select name=\"idregion\">";
							$b = new ControleurConnexion;
							$listeregion = $b->consulter("*","region","","","","","","");
							while($tablisteregion=mysql_fetch_array($listeregion)){
								echo "<option value=\"".$tablisteregion['idregion']."\">".$tablisteregion['nomregion']."</option>";
							}
					echo	
						"</select>
						<input type=\"hidden\" name=\"nomdep\" value=\"".$nomdep."\" />
						<input type=\"hidden\" name=\"type\" value=\"envoyer_dep\" />
						<input type=\"submit\" name=\"modif_region\" value=\"envoyer\" />
					</form>";
					echo "<br />Vous vous etes trompé ??? Pas d'inquiétude, le retout c'est <a href=\"../index.php\">par içi </a>!!! ";
				}else{
					echo "Attention, il n'y a pas de Département pour cette région pensez à le créer, ou alors, vous n'avez pas selectionné de Département <br />
					<a href=\"../index.php\">retour</a>
					";
				}
			}else{
				echo "Vous vous etes trompé !!! Vous n'avez peut-être pas renseigné les champs ... Recommencez ! <br />
				Pas d'inquiétude, le retout c'est <a href=\"../index.php\">par içi </a>!!! ";
			}
		break;
		
		case "envoyer_dep" :
			$idregion = $_POST['idregion'];
			$anciendep = $_POST['nomdep'];
			$nouvdep = $_POST['nouvdep'];
			$c = new ControleurConnexion;
			$nouvdepmod = $c->modifier("departement","nomdep='$nouvdep', idregion=\"$idregion\"","nomdep='$anciendep'","",""); 
			header ("Location: ../index.php");
		break;
		
		case "modifier_ville" :
			if($_POST['modif_ville'] == "modifier"){
				$ancienneVille=$_POST['ville'];
				$sql_ville = new ControleurConnexion;
				$rech_ville = $sql_ville -> consulter("CP","ville","","nomville='$ancienneVille'","","","","");
				$cp = mysql_fetch_row($rech_ville);
				
				?>
				<fieldset>
					<legend><?php echo $ancienneVille;?></legend>
					<form method="POST" action="modifaction.php">
						Modifier la ville :
						<input type="hidden" name="ancienneville" value="<?php echo $ancienneVille; ?>" />
						<input type="text" name="nouvville" value="<?php echo $ancienneVille; ?>" /> <br />
						<p>Verifier le code postale : <input type="text" name="cp" value="<?php echo $cp[0]; ?>" /> </p>
						<input type="hidden" name="type" value="modifier_ville_action" />
						<input type="submit" name="modifierville" value="modifier" />
					</form>
				</fieldset>
				<?php echo "<br />Vous vous etes trompé ??? Pas d'inquiétude, le retout c'est <a href=\"../index.php\">par içi </a>!!! "; ?>
			<?php }elseif($_POST['modif_ville'] == "modifier-departement"){
				$region=$_POST['region'];
				$dep=$_POST['dep'];
				$ville=$_POST['ville'];
				
				$sql = new ControleurConnexion;
				$sql_iddep = $sql->consulter("iddep","departement","","nomdep='$dep'","","","","");
				$iddep=mysql_fetch_row($sql_iddep);
				
				$sql2 = new ControleurConnexion;
				$sql_idreg = $sql2->consulter("idregion","region","","nomregion='$region'","","","","");
				$idreg=mysql_fetch_row($sql_idreg);
				
				$sql3 = new ControleurConnexion;
				$sql_listdep = $sql3->consulter("*","departement","","idregion='$idreg[0]'","","","nomdep","");
				?>
				
				<fieldset>
					<legend><strong>Choix du nouveau département pour : <?php echo $ville;?></strong></legend>
					<form method="POST" action="./modifaction.php" >
						<p>Liste des départements de la région <?php echo $region;?> :
						<select name="nouvdep">
							<?php while($listdep = mysql_fetch_array($sql_listdep)){ ?>
								<option value="<?php echo $listdep['iddep']; ?>"><?php echo $listdep['nomdep'];?></option>
							<?php }?>
						</select></p>
						<input type="hidden" name="type" value="modifier_dep_ville" />
						<input type="hidden" name="ville" value="<?php echo $ville; ?>" />
						<input type="submit" name="modifier-dep-ville" value="envoyer" />
						<?php echo "<br />Vous vous etes trompé ??? Pas d'inquiétude, le retout c'est <a href=\"../index.php\">par içi </a>!!! "; ?>
			<?php }elseif($_POST['modif_ville'] == "modifier-region"){
				$region=$_POST['region'];
				$dep=$_POST['dep'];
				$ville=$_POST['ville'];
				
				$sql3 = new ControleurConnexion;
				$sql_listreg = $sql3->consulter("*","region","","","","","nomregion","");
				?>
				
				<fieldset>
					<legend><strong>Choix de la nouvelle région pour : <?php echo $ville;?></strong></legend>
					<form method="POST" action="./modifaction.php" >
						<p>Liste des régions : 
						<select name="nouvreg">
							<option>Choisir une région</option>
							<?php while($listreg = mysql_fetch_array($sql_listreg)){ ?>
								<option value="<?php echo $listreg['idregion']; ?>"><?php echo $listreg['nomregion'];?></option>
							<?php }?>
						</select></p>
						<input type="hidden" name="type" value="modifier_reg_ville" />
						<input type="hidden" name="ville" value="<?php echo $ville; ?>" />
						<input type="submit" name="modifier-reg-ville" value="envoyer" />
				</fieldset>
				<?php echo "<br />Vous vous etes trompé ??? Pas d'inquiétude, le retout c'est <a href=\"../index.php\">par içi </a>!!! "; ?>
			<?php }
		break;
		
		case "modifier_ville_action": 
			$ancienneVille = $_POST['ancienneville'];
			$nouvville = $_POST['nouvville'];
			$cp = $_POST['cp'];
			$c = new ControleurConnexion;
			$nouvvilleaction = $c->modifier("ville","nomville='$nouvville', CP='$cp'","nomville='$ancienneVille'","",""); 
			header ("Location: ../index.php");
		break;
		
		case "modifier_dep_ville":
			$iddep = $_POST['nouvdep'];
			$ville = $_POST['ville'];
			$sql= new ControleurConnexion;
			$sql_mod=$sql->modifier("ville","iddep='$iddep'","nomville='$ville'","","");
			header ("Location: ../index.php");
		break;
		
		case "modifier_reg_ville":
			$idregion=$_POST['nouvreg'];
			$ville=$_POST['ville'];
			
			$sql = new ControleurConnexion;
			$sql_listdep = $sql->consulter("*","departement","","idregion='$idregion'","","","","");
			?>
			<fieldset>
					<legend><strong>Choix du département pour : <?php echo $ville;?></strong></legend>
					<form method="POST" action="./modifaction.php" >
						<p>Liste des département :
						<select name="nouvdep">
							<option>Choisir un département</option>
							<?php while($listdep = mysql_fetch_array($sql_listdep)){ ?>
								<option value="<?php echo $listdep['iddep']; ?>"><?php echo $listdep['nomdep'];?></option>
							<?php }?>
						</select></p>
						<input type="hidden" name="type" value="modifier_reg_dep_ville" />
						<input type="hidden" name="ville" value="<?php echo $ville; ?>" />
						<input type="submit" name="modifier-reg-dep-ville" value="envoyer" />
			</fieldset>
			<?php echo "<br />Vous vous etes trompé ??? Pas d'inquiétude, le retout c'est <a href=\"../index.php\">par içi </a>!!! "; 
		break;
		
		case "modifier_reg_dep_ville":
			$iddep = $_POST['nouvdep'];
			$ville = $_POST['ville'];
			$sql= new ControleurConnexion;
			$sql_mod=$sql->modifier("ville","iddep='$iddep'","nomville='$ville'","","");
			header ("Location: ../index.php");
		break;
	}
?>