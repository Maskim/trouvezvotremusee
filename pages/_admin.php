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
					<legend>Acc�s � l'affichage des donn�es </legend>
						<input name="affich" type="radio" value="region" />R�gion <br />
						<input name="affich" type="radio" value="departement" />D�partement <br />
						<input name="affich" type="radio" value="ville" />Ville <br />
						<input name="affich" type="radio" value="musee" />Mus�e <br />
						<br />
						<input type="submit" name="affiche" value="afficher" />
				</fieldset>
			</form>
			
			<br />

			<form method="POST" action="./core/php/ajoutimage.php"  ENCTYPE="multipart/form-data">
				<fieldset>
					<legend>Ajout d'image pour un mus�e</legend>
					<label for="musee">Trouvez un mus�e</label>
					<input type="text" id="musee" name="musee" onchange="findMusee(this);" /><br />
					<span>Si vous ne retourvez pas le mus�e souhait�, cherchez le dans la liste suivante :</span><br />
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
					<span class="info">Taille maximum autoris�e 10Mo</span><br />

					<label>Type de l'image</label>
					<select name="typeImage">
						<option value="mini">Miniature</option>
						<option value="normal">Pr�sentation</option>
					</select>
					<br />

					<input type="submit" name="Envoyer" value="Envoyer" />
				</fieldset>
			</form>

			<br/>
			
			<form method="POST" action="ajout.html">
				<fieldset>
					<legend>Acc�s ajout de donn�es</legend>
						<input name="ajout" type="radio" value="region" />R�gion <br />
						<input name="ajout" type="radio" value="departement" />D�partement <br />
						<input name="ajout" type="radio" value="ville" />Ville <br />
						<input name="ajout" type="radio" value="musee" />Mus�e <br />
						<br />
						<input type="submit" name="choix_modif" value="ajouter" />
				</fieldset>
			</form>
			
			<br/>
			
			<form method="POST" action="modification.html">
				<fieldset>
					<legend>Acc�s au modificateur de donn�es</legend>
						<input name="modif" type="radio" value="region" />R�gion <br />
						<input name="modif" type="radio" value="departement" />D�partement <br />
						<input name="modif" type="radio" value="ville" />Ville <br />
						<input name="modif" type="radio" value="musee" />Mus�e <br />
						<br />
						<input type="submit" name="choix_modif" value="modifier" />
				</fieldset>
			</form>
			
			<br/>
			
			<form>
				<fieldset>
					<legend>Acc�s liste des utilisateurs</legend>
						<a href="./utilisateurs.html">Liste des utilisateurs</a>
				</fieldset>
			</form>
			
		</div>
	<?php 
		}
		elseif(isset($_SESSION['connexion']) AND $_SESSION['niveau'] == 1 AND $_SESSION['connexion']){
	?>
			<p>Vous n'etes pas autoris� � all� sur cette zone de Trouvez Votre Mus�e !</P>
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
