<div id="contenu">
	<h1>Mes favoris</h1>

	<?php 
		$util = $_SESSION['iduser'];
		$a = new ControleurConnexion();

		$favoris = $a->consulter("*", "favori, musee", "", "util = $util AND musee = idmusee", "", "", "", "","");

		while($tab = mysql_fetch_array($favoris)){
			?>

			<p id="fav-<?php echo $tab['idmusee']; ?>"><?php echo $tab['nom']; ?> -- <a href="" onclick="deleteFavori(<?php echo $tab['idmusee']; ?>, <?php echo $tab['util']; ?>);return false;">Supprimer de vos favoris</a> </p>

			
			<?php
		}
	?>
</div>
