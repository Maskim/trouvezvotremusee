<?php
/*
   ===========================================================
   ---- MODULE DE RECHERCHE RAPIDE 						  ----
   ---- ? Twan 				http://www.twan-diz.fr		  ----
   ---- ? Maskim				     					  ----
   ---- 2010 / support@trouvezvotremusee.com			  ----
   =========================================================== 
*/

   require_once('./classes/ControleurConnexionPers.php');

	if(isset($_POST['musee']) AND !empty($_POST['musee'])) {

		$a = new ControleurConnexion();
		 
		// Recherche des r?sultats dans la base de donn?es
		// OR lastname LIKE \'' . $_GET['musee'] . '%\'
		$result = $a -> consulter("nom", "musee", "", 'nom LIKE \'%' . htmlspecialchars($_POST['musee']) . '%\'', "", "", "", "", "0, 5");

		$result_ville = $a -> consulter("nomville", "ville", "", 'nomville LIKE \'%' .htmlspecialchars($_POST['musee']) . '%\'',"", "", "", "", "0,5");

		$result_dep = $a -> consulter("nomdep", "departement", "", 'nomdep LIKE \'%' .htmlspecialchars($_POST['musee']) . '%\'',"", "", "", "", "0,5");

		$result_reg = $a -> consulter("nomregion", "region", "", 'nomregion LIKE \'%' .htmlspecialchars($_POST['musee']) . '%\'',"", "", "", "", "0,5");

		// Affichage d'un message "Pas de r?sultats"
		if(mysql_num_rows( $result ) == 0 && mysql_num_rows( $result_ville ) && mysql_num_rows( $result_dep ) 
			&& mysql_num_rows( $result_reg )) {
		?>
			<div class="article-resultat">
				<p>
					<a href="accueil.html">Pas de r&eacute;sultats pour cette recherche</a>
				</p>
			</div>
		<?php
		}
		else
		{
			require_once("./fonctions.php");
			echo '<div class="article-resultat">';
			$first = true;

			while($post = mysql_fetch_object($result)){
				if($first)
					echo '<p class="enteteRecherche">Musée</p>';
				$replace = array(" ", "-", "'");
				$affiche = findCurrentSearchInString($_POST['musee'], utf8_encode($post->nom));
			?>
				<p>
					<a href="musees-<?php echo strtolower(htmlspecialchars(prepareString(utf8_encode($post->nom)))); ?>.html">
						<?php echo $affiche; ?>
					</a>
				</p>        
			<?php
				$first = false;
			}

			$first = true;
			while($post = mysql_fetch_object($result_ville)){
				if($first)
					echo '<p class="enteteRecherche">Ville</p>';
				$replace = array(" ", "-", "'");
				$affiche = findCurrentSearchInString($_POST['musee'], utf8_encode($post->nomville));
			?>
				<p>
					<a href="ville-<?php echo strtolower(htmlspecialchars(prepareString(utf8_encode($post->nomville)))); ?>.html">
						<?php echo $affiche; ?>
					</a>
				</p>        
			<?php
				$first = false;
			}

			$first = true;
			while($post = mysql_fetch_object($result_dep)){
				if($first)
					echo '<p class="enteteRecherche">Département</p>';
				$replace = array(" ", "-", "'");
				$affiche = findCurrentSearchInString($_POST['musee'], utf8_encode($post->nomdep));
			?>
				<p>
					<a href="departement-<?php echo strtolower(htmlspecialchars(prepareString(utf8_encode($post->nomdep)))); ?>.html">
						<?php echo $affiche; ?>
					</a>
				</p>        
			<?php
				$first = false;
			}

			$first = true;
			while($post = mysql_fetch_object($result_reg)){
				if($first)
					echo '<p class="enteteRecherche">Région</p>';

				$affiche = findCurrentSearchInString($_POST['musee'], utf8_encode($post->nomregion));
			?>
				<p>
					<a href="region-<?php echo strtolower(htmlspecialchars(prepareString(utf8_encode($post->nomregion)))); ?>.html">
						<?php echo $affiche; ?>
					</a>
				</p>        
			<?php
				$first = false;
			}
			echo '</div>';
		}
	}
	else {
		header("Location: ../accueil.html");
	}
?>