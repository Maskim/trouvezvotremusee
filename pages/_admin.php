	<?php
		if(isset($_SESSION['connexion']) AND $_SESSION['niveau'] == 5 AND $_SESSION['connexion']){ 
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
