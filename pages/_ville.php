<?php
	require_once("./core/classes/ControleurConnexionPers.php");
	$replace = array(" ", "-", "'");

	if(isset($_GET['Nom']) AND !empty($_GET['Nom'])) {
		$ok = false;
			
		$nom_ville = htmlspecialchars($_GET['Nom']);
		// On selectionne tous les mus�es
		$a = new ControleurConnexion;
		$nb_ville = $a->consulter("COUNT(*)","ville","", "nomville = '$nom_ville'", "","", "", "", "");

		$nb_ville = mysql_fetch_row($nb_ville);
		if($nb_ville[0] == 1){ 
			$liste_musee = $a->consulter("nom", "musee, ville", "", "nomville = '$nom_ville' AND musee.idville = ville.idville", "", "", "", "nom", "");
			?>

			<div id="contenu">
				<h1>Les mus�es de <?php echo $nom_ville; ?></h1>
			
				<img src="./core/img/musee.png" alt="Tous les mus�es par ordre alphab�tique." title="Tous les mus�es par ordre alphab�tique." class="img_musees_liens" />
				

				<div class="classement_musees_liens">
					<p>
						<?php
							foreach(range('A','Z') as $lettre) {
								echo '<a href="#'.$lettre.'" class="c">'.$lettre.'</a> ';
							}
						?>
						<span>Recherche avanc�e</span>
					</p>
				</div>

				<div class="liste_musees">
					<?php
					$i = 0;
					while($musee = mysql_fetch_array($liste_musee)){
						$lien = str_replace($replace, "", $musee['nom']);
						$lien = strtolower($lien);
						$lien = "musees-$lien.html";

						$les_musees[$i][0] = $musee['nom'];
						$les_musees[$i][1] = $lien;
						$i++;
					}
					?>

						<?php 
						$i=0;
						foreach (range('A', 'Z') as $lettre) {
							?> 
							<h3 id="<?php echo $lettre; ?>"><?php echo $lettre; ?></h3>
							<?php
							if($i == count($les_musees)) $first_lettre = 0;
							else $first_lettre = substr($les_musees[$i][0], 0, 1);
							while(strtolower($first_lettre) == strtolower($lettre)){
								?>
								<ul>
									<li><a href="<?php echo $les_musees[$i][1]; ?>" ><?php echo $les_musees[$i][0]; ?></a></li>
								</ul>
								<?php
								$i++;
								if($i == count($les_musees)) $first_lettre = 0;
								else $first_lettre = substr($les_musees[$i][0], 0, 1);
							}
						} ?>
					</div>					
			</div>

			<?php
		}else{ ?>
			<p>FAIL</p>
		<?php }
	}else{
		//On affiche toutes les villes
	}
?>