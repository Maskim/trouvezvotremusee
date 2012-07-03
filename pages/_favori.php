<div id="contenu">
	<h1>Mes favoris</h1>

	<?php 
		$util = $_SESSION['iduser'];
		$a = new ControleurConnexion();

		$favoris = $a->consulter("*", "favori, musee", "", "util = $util AND musee = idmusee", "", "", "", "","");

		while($tab = mysql_fetch_array($favoris)){
			?>

			<p><?php echo $tab['nom']; ?> -- <a href="">Supprimer de vos favoris</a> </p>

			
			<?php
		}
	?>
</div>
