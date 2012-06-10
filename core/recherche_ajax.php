<?php
/*
   ===========================================================
   ---- MODULE DE RECHERCHE RAPIDE 						  ----
   ---- ? Twan 				http://www.twan-diz.fr		  ----
   ---- ? Maskim				     					  ----
   ---- 2010 / support@trouvezvotremusee.com			  ----
   =========================================================== 
*/

	if(isset($_POST['musee']) AND !empty($_POST['musee'])) {

		// Connexion ? la BDD
		// define('DB_NAME', '25848_musee');
		// define('DB_USER', '25848_musee');
		// define('DB_PASSWORD', 'grandia');
		// define('DB_HOST', 'sql.olympe-network.com');
		
		define('DB_NAME', 'trouvez-votre-musee');
		define('DB_USER', 'root');
		define('DB_PASSWORD', '');
		define('DB_HOST', 'localhost');
		
		// V?rif de la BDD
		$link   =   mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
					mysql_select_db(DB_NAME, $link);
		 
		// Recherche des r?sultats dans la base de donn?es
		// OR lastname LIKE \'' . $_GET['musee'] . '%\'
		$result =   mysql_query( 'SELECT nom
								  FROM musee
								  WHERE nom LIKE \'%' . htmlspecialchars($_POST['musee']) . '%\'
								  LIMIT 0,5' 
								);

		$result_ville = mysql_query('	SELECT nomville
										FROM ville
										WHERE nomville LIKE \'%' .htmlspecialchars($_POST['musee']) . '%\'
								  		LIMIT 0,5' 
								);

		$result_dep = mysql_query('	SELECT nomdep
										FROM departement
										WHERE nomdep LIKE \'%' .htmlspecialchars($_POST['musee']) . '%\'
								  		LIMIT 0,5' 
								);

		$result_reg = mysql_query('	SELECT nomregion
										FROM region
										WHERE nomregion LIKE \'%' .htmlspecialchars($_POST['musee']) . '%\'
								  		LIMIT 0,5' 
								);

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