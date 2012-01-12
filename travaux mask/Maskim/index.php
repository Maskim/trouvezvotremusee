<?php include("./header.php"); ?>

<body>

<?php if(isset($_SESSION['connexion']) && $_SESSION['connexion']){ ?>
	<p><a href="./core/deconnexion.php">deconnexion</a></p>
<?php } else{?>
<p><a href="./core/inscription.php">inscription</a> - <a href="./core/connexion.php">connexion</a></p>
<?php } ?>
<p><a href="rechercher.php"> rechercher un musée </a></p>
	
	<form method="POST" action="affichage.php" >
		<fieldset>
			<legend> Accès à l'affichage des données </legend>
				<input name="affich" type="radio" value="region" />région <br />
				<input name="affich" type="radio" value="departement" />département <br />
				<input name="affich" type="radio" value="ville" />ville <br />
				<input name="affich" type="radio" value="musee" />musée <br />
				<br />
				<input type="submit" name="affiche" value="afficher" />
		</fieldset>
	</form>
		
	<?php if(isset($_SESSION['connexion']) && $_SESSION['niveau']==2 && $_SESSION['connexion']){ ?>
		<form method="POST" action="ajout.php">
			<fieldset>
				<legend> accès ajout de donnée </legend>
					<input name="ajout" type="radio" value="region" />région <br />
					<input name="ajout" type="radio" value="departement" />département <br />
					<input name="ajout" type="radio" value="ville" />ville <br />
					<input name="ajout" type="radio" value="musee" />musée <br />
					<br />
					<input type="submit" name="choix_modif" value="ajouter" />
			</fieldset>
		</form>
		
			
			
		<form method="POST" action="modification.php">
			<fieldset>
				<legend> accès modificateur </legend>
					<input name="modif" type="radio" value="region" />région <br />
					<input name="modif" type="radio" value="departement" />département <br />
					<input name="modif" type="radio" value="ville" />ville <br />
					<input name="modif" type="radio" value="musee" />musée <br />
					<br />
					<input type="submit" name="choix_modif" value="modifier" />
			</fieldset>
		</form>
		
		<form>
			<fieldset>
				<legend> accès liste des utilisateurs </legend>
					<a href="./core/affichutil.php">liste des utilisateurs</a>
			</fieldset>
		</form>
	<?php } ?>
</body>
</html>