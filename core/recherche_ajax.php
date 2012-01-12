<?php
/*
   ===========================================================
   ---- MODULE DE RECHERCHE RAPIDE 						  ----
   ---- © Twan 				http://www.twan-diz.fr		  ----
   ---- © Maskim				     					  ----
   ---- 2010 / support@trouvezvotremusee.com			  ----
   =========================================================== 
*/

	if(isset($_POST['musee']) AND !empty($_POST['musee'])) {

		// Connexion à la BDD
		// define('DB_NAME', '25848_musee');
		// define('DB_USER', '25848_musee');
		// define('DB_PASSWORD', 'grandia');
		// define('DB_HOST', 'sql.olympe-network.com');
		
		define('DB_NAME', 'trouvez-votre-musee');
		define('DB_USER', 'root');
		define('DB_PASSWORD', '');
		define('DB_HOST', 'localhost');
		 
		 
		// Vérif de la BDD
		$link   =   mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
					mysql_select_db(DB_NAME, $link);
		 
		// Recherche des résultats dans la base de données
		// OR lastname LIKE \'' . $_GET['musee'] . '%\'
		$result =   mysql_query( 'SELECT nom
								  FROM musee
								  WHERE nom LIKE \'%' . htmlspecialchars($_POST['musee']) . '%\'
								  LIMIT 0,5' 
								);


		// Affichage d'un message "Pas de résultats"
		if(mysql_num_rows( $result ) == 0) {
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
			echo '<div class="article-resultat">';
			while($post = mysql_fetch_object($result)){
				$replace = array(" ", "-", "'");
			?>
				<p>
					<a href="musees-<?php echo strtolower(htmlspecialchars(str_replace($replace, "", $post->nom))); ?>.html"><?php echo '<strong>'. htmlspecialchars($_POST['musee']) .'</strong>'.substr($post->nom, strlen(htmlspecialchars($_POST['musee'])), strlen($post->nom)); ?></a>
				</p>        
			<?php
			}
			echo '</div>';
		}
	}
	else {
		header("Location: ../accueil.html");
	}
?>