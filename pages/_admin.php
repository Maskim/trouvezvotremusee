	<?php
		if(isset($_SESSION['connexion']) AND $_SESSION['niveau'] == 5 AND $_SESSION['connexion']){ 

			$a = new ControleurConnexion();
			$sql = $a->consulter("idmusee, nom", "musee", "", "", "", "", "", "nom", "");

	?>
		<div id="contenu">
			<h1>Administration</h1>
			
			<br/>
			
			<form method="POST" action="affichage.html" >
				<fieldset>
					<legend>Accès à l'affichage des données </legend>
						<input name="affich" type="radio" value="region" />Région <br />
						<input name="affich" type="radio" value="departement" />Département <br />
						<input name="affich" type="radio" value="ville" />Ville <br />
						<input name="affich" type="radio" value="musee" />Musée <br />
						<br />
						<input type="submit" name="affiche" value="afficher" />
				</fieldset>
			</form>
			
			<br />

			<form method="POST" action="./core/php/ajoutimage.php"  ENCTYPE="multipart/form-data">
				<fieldset>
					<legend>Ajout d'image pour un musée</legend>
					<label for="musee">Trouvez un musée</label>
					<input type="text" id="musee" name="musee" onchange="findMusee(this);" /><br />
					<span>Si vous ne retourvez pas le musée souhaité, cherchez le dans la liste suivante :</span><br />
					<select>
						<?php
							while($musee = mysql_fetch_array($sql)){
								?>
								<option value="<?php echo $musee["idmusee"]; ?>"><?php echo $musee["nom"]; ?></option>
								<?php
							}
						?>
					</select><br />

					<input type='hidden' name='MAX_FILE_SIZE' value='2097152'>
					<label for="image">Selectionner une image : </label><input id="image" name="image" type="file" /><br />
					<span class="info">Taille maximum autorisée 10Mo</span><br />

					<label>Type de l'image</label>
					<select name="typeImage">
						<option value="mini">Miniature</option>
						<option value="normal">Présentation</option>
					</select>
					<br />

					<input type="submit" name="Envoyer" value="Envoyer" />
				</fieldset>
			</form>

			<br/>
			
			<form method="POST" action="ajout.html">
				<fieldset>
					<legend>Accès ajout de données</legend>
						<input name="ajout" type="radio" value="region" />Région <br />
						<input name="ajout" type="radio" value="departement" />Département <br />
						<input name="ajout" type="radio" value="ville" />Ville <br />
						<input name="ajout" type="radio" value="musee" />Musée <br />
						<br />
						<input type="submit" name="choix_modif" value="ajouter" />
				</fieldset>
			</form>
			
			<br/>
			
			<form method="POST" action="modification.html">
				<fieldset>
					<legend>Accès au modificateur de données</legend>
						<input name="modif" type="radio" value="region" />Région <br />
						<input name="modif" type="radio" value="departement" />Département <br />
						<input name="modif" type="radio" value="ville" />Ville <br />
						<input name="modif" type="radio" value="musee" />Musée <br />
						<br />
						<input type="submit" name="choix_modif" value="modifier" />
				</fieldset>
			</form>
			
			<br/>
			
			<form>
				<fieldset>
					<legend>Accès liste des utilisateurs</legend>
						<a href="./utilisateurs.html">Liste des utilisateurs</a>
				</fieldset>
			</form>
			
		</div>
	<?php 
		}
		elseif(isset($_SESSION['connexion']) AND $_SESSION['niveau'] == 1 AND $_SESSION['connexion']){
	?>
			<p>Vous n'etes pas autorisé à allé sur cette zone de Trouvez Votre Musée !</P>
			<script type="text/javascript">
				document.location.href = "connexion.html";
			</script>
	<?php
		}else{
	?>
		<script type="text/javascript">
			document.location.href = "connexion.html";
		</script>
	<?php
		}
	?>
