		<?php
			// La recherche est transmise par la variable $_POST['musee']
			// Il faut la tester d'abord pour savoir si elle est vide ou non -> OK
			// Si elle est vide, erreur -> OK
			// Sinon la recherche est effectuée et on affiche la liste des résultats -> OK
			if(!isset($_POST['musee']) || empty($_POST['musee'])) {
		?>
		<div id="contenu">
			<h1>Erreur dans votre recherche</h1>
			
			<p>
				Il y a eu un <strong>problème lors de votre recherche</strong>.
			</p>
			
			<p>
				Soit celle-ci est <strong>vide</strong>, soit ce que vous recherchez <strong>n'est pas indexé dans notre infrastructure</strong>.<br/>
				<br/><br/><br/><br/>
				<a href="accueil.html" title="Retourner à la page d'Accueil" class="erreur">Retourner à la page d'Accueil</a>
			</p>
			
		</div>
		<?php
			}
			else {
		?>
		
		<div id="contenu">
			<h1>Votre recherche <?php if(isset($_POST['musee'])) { echo ': "'.htmlspecialchars($_POST['musee']); } ?>"</h1>
			
			<?php 
				require_once("./core/classes/ControleurConnexionPers.php");
				
				var_dump($_POST['musee']);

				$valeurRecherche = prepareString($_POST['musee']);
				$musee = true;
				$ville = false;
				$dep = false;
				$reg = false;
				
				$recherche = new ControleurConnexion;
				$nom_du_musee_bdd = $recherche->consulter("nom","musee","","nom","'%$valeurRecherche%'","","","","");
				$tab_verif = mysql_num_rows($nom_du_musee_bdd);
				
				//Est-ce une ville ??
				if($tab_verif == 0){
					$musee = false;
					$ville = true;
					$nom_ville_bdd = $recherche->consulter("nomville","ville","","nomville","'%$valeurRecherche%'","","","","");
					$tab_verif = mysql_num_rows($nom_ville_bdd);
				}
				
				//Est-ce un departement ??
				if($tab_verif == 0){
					$ville = false;
					$dep = true;
					$nom_dep_bdd = $recherche->consulter("nomdep","departement","","nomdep","'%$valeurRecherche%'","","","","");
					$tab_verif = mysql_num_rows($nom_dep_bdd);
				}
				
				//Est-ce une region ??
				if($tab_verif == 0){
					$dep = false;
					$reg = true;
					$valeurRecherche = findRegion($valeurRecherche);
					$nom_reg_bdd = $recherche->consulter("nomregion","region","","nomregion","'%$valeurRecherche%'","","","","");
					$tab_verif = mysql_num_rows($nom_reg_bdd);
				}
				
				if($tab_verif == 1) {
					if($musee){
						$tab_musee = mysql_fetch_array($nom_du_musee_bdd);
					}elseif($ville){
						$tab_musee = mysql_fetch_array($nom_ville_bdd);
					}elseif($dep){
						$tab_musee = mysql_fetch_array($nom_dep_bdd);
					}elseif($reg){  
						$tab_musee = mysql_fetch_array($nom_reg_bdd);
					}
					$tab_musee = str_replace(" ", "", $tab_musee);
					$tab_musee = str_replace("-", "", $tab_musee);
					$tab_musee = str_replace("'", "", $tab_musee);
			?>
				<script type="text/javascript">
					<?php
						if($musee){
					?>
						document.location.href = "musees-<?php echo strtolower(htmlspecialchars($tab_musee['nom'])); ?>.html";
					<?php
						}elseif($ville){
					?>
						document.location.href = "ville-<?php echo strtolower(htmlspecialchars($tab_musee['nomville'])); ?>.html";
					<?php
						}elseif($dep){
					?>
						document.location.href = "departement-<?php echo strtolower(htmlspecialchars($tab_musee['nomdep'])); ?>.html";
					<?php
						}elseif($reg){
					?>
						document.location.href = "region-<?php echo strtolower(htmlspecialchars($tab_musee['nomregion'])); ?>.html";
					<?php
						}
					?>
					
				</script>
			<?php
				}else if($tab_verif == 0){
					?>
						<p>
							Il y a eu un <strong>problème lors de votre recherche</strong>.
						</p>
						
						<p>
							Soit celle-ci est <strong>vide</strong>, soit ce que vous recherchez <strong>n'est pas indexé dans notre infrastructure</strong>.<br/>
							<br/><br/><br/><br/>
							<a href="accueil.html" title="Retourner à la page d'Accueil" class="erreur">Retourner à la page d'Accueil</a>
						</p>
					<?php
				}
				else { ?>
				<h2>Notre suggestion pour la recherche :</h2>
				<?php	
				while($tab_musee = mysql_fetch_array($nom_du_musee_bdd)){ ?>
				<p>
					<?php 
						$lien = strtolower(htmlspecialchars($tab_musee['nom'])); 
						$lien = str_replace(" ", "",$lien);
					?>
					<a href="./musees-<?php echo $lien; ?>.html"><?php echo $tab_musee['nom']; ?></a>
				</p>
				<?php } ?>
		</div>
		
		<?php
				}
			}
		?>
