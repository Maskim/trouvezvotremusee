<?php
	if(isset($_GET['Nom']) AND !empty($_GET['Nom'])){
		require_once("./core/classes/ControleurConnexionPers.php");
		$rech_type = $_GET['Nom'];
		switch ($rech_type) {
		// Recherche par région
			case 'region':
				$a = new ControleurConnexion;
				$sql = $a->consulter("*","region","","","","","","nomregion","");
				?>
					<div id="contenu">
						<h1>Choisir une région</h1>
						<form action="./musees.html" method="POST" >
							<p>
								Liste des régions :
								<select name="id">
									<?php while($reg = mysql_fetch_array($sql)){ ?>
										<option value="<?php echo $reg['idregion']; ?>"><?php echo $reg['nomregion']; ?></option>
									<?php } ?>
								</select>
							</p>
							<p>
								<input type="hidden" name="type" value="region" />
								<input type="submit" value="Sélectionnez" />
							</p>
						</form>
					</div>
				<?php
			break;
		// recherche par département
			case 'departement':
				$a = new ControleurConnexion;
				$sql = $a->consulter("*","departement","","","","","","nomdep","");
				?>
					<div id="contenu">
						<h1>Choisir un département</h1>
						<form action="./musees.html" method="POST" >
							<p>
								Liste des départements :
								<select name="id">
									<?php while($reg = mysql_fetch_array($sql)){ ?>
										<option value="<?php echo $reg['iddep']; ?>"><?php echo $reg['nomdep']; ?></option>
									<?php } ?>
								</select>
							</p>
							<p>
								<input type="hidden" name="type" value="dep" />
								<input type="submit" value="Sélectionnez" />
							</p>
						</form>
					</div>
				<?php
			break;
		// recherche par ville
			case 'ville':
				$a = new ControleurConnexion;
				$sql = $a->consulter("*","ville","","","","","","nomville","");
				?>
					<div id="contenu">
						<h1>Choisir une ville</h1>
						<form action="./musees.html" method="POST" >
							<p>
								Liste des villes :
								<select name="id">
									<?php while($reg = mysql_fetch_array($sql)){ ?>
										<option value="<?php echo $reg['idville']; ?>"><?php echo $reg['nomville']; ?></option>
									<?php } ?>
								</select>
							</p>
							<p>
								<input type="hidden" name="type" value="ville" />
								<input type="submit" value="Sélectionnez" />
							</p>
						</form>
					</div>
				<?php
			break;
		}
	}else{
		echo "erreur";
	}
?>