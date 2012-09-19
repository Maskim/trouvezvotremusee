<?php
	require_once("./core/classes/ControleurConnexionPers.php");
	$replace = array(" ", "-", "'");

	if(isset($_GET['Nom']) AND !empty($_GET['Nom'])) {
		$ok = false;
			
		$nom_reg = htmlspecialchars($_GET['Nom']);
		$nom_reg = findRegion($nom_reg);

		$a = new ControleurConnexion;
		$nb_reg = $a->consulter("COUNT(*)","region","", "nomregion LIKE '%$nom_reg%'", "","", "", "", "");

		$nb_reg = mysql_fetch_row($nb_reg);
		if($nb_reg[0] == 1){ 
			$liste_musee = $a->consulter("nom, CP, nomville", "musee, region, departement, ville", "", "nomregion = '$nom_reg' AND musee.idville = ville.idville AND ville.iddep = departement.iddep AND departement.idregion = region.idregion", "", "", "", "nom", "");
			?>

			<div id="contenu">
				<h1>Les mus�es de <?php echo $nom_reg; ?></h1>
			
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
						$lien = prepareString(utf8_encode($musee['nom']));
						$lien .= '-' . prepareString(utf8_encode($musee['nomville']));
						$lien = "musees-$lien.html";

						$les_musees[$i][0] = $musee['nom'];
						$les_musees[$i][1] = $lien;
						$les_musees[$i][2] = $musee['CP'] .  ' ' . $musee['nomville'];
						$i++;
					}

					$les_musees = trierMusee($les_musees);

						$i=0;
						foreach (range('A', 'Z') as $lettre) {
							?> 
							<h3 id="<?php echo $lettre; ?>"><?php echo $lettre; ?></h3>
							<?php
							$adaptNomMusee = adapterNomMusee($les_musees[$i][0]);

							if($i == count($les_musees)) $first_lettre = 0;
							else $first_lettre = substr($adaptNomMusee, 0, 1);

							while(strtolower($first_lettre) == strtolower($lettre)){
								?>
								<ul>
									<li><a href="<?php echo $les_musees[$i][1]; ?>" ><strong><?php echo $les_musees[$i][0]; ?></strong></a> - <?php echo $les_musees[$i][2] ?></li>
								</ul>

								<?php
								if($i < count($les_musees) - 1)
									$i++;

								if($i == (count($les_musees) - 1 ) ) $first_lettre = 0;
								else{ 
									$adaptNomMusee = adapterNomMusee($les_musees[$i][0]);
									$first_lettre = substr($adaptNomMusee, 0, 1);
								}

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