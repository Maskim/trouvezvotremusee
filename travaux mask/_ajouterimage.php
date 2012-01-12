<?php 
	$nom_musee = $_SESSION['nom_musee'];
	$chemin = "./images/";
?>

<div id="contenu">
	<h1> Ajouter une image au musée <?php echo $nom_musee ; ?></h1>
	<div id="le_musee">
		<div id="infos">
			<p>
				téléchargement de l'image :
			</p>
			<form method="post" enctype="multipart/form-data" action="../core/ajoutimg_action.php">
				<input type="file" name="img-musees" />
				<input type="submit" value="ajouter le fichier" />
				<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
			</form>
			
			<?php move_uploaded_file($_FILES['fichier']['tmp_name'], $chemin.$fichier); ?>
			
		</div>
	</div>

</div>