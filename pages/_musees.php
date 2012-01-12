	<?php 
		require_once("./core/classes/ControleurConnexionPers.php");
		require_once("./core/ajax/rating_bar/_drawrating.php");
		$replace = array(" ", "-", "'");
		
		if(isset($_GET['Nom']) AND !empty($_GET['Nom'])) {

			$ok = false;
			
			$nom_musee = htmlspecialchars($_GET['Nom']);
			// On selectionne tous les musées
			$a = new ControleurConnexion;
			$sql = $a->consulter("*","musee","", "", "","", "", "", "");
			
			while($rech_mus = mysql_fetch_array($sql)){
				$mus = $rech_mus['nom'];
				// On regarde si le musée selectionné existe
				$ms = new ControleurConnexion;
				$sql_ms = $ms->consulter("*","musee","", "nom=\"$mus\"", "","", "", "", "");
				$verif = mysql_fetch_array($sql_ms);
				// On enleve les espaces
				$nom_musee_rech = str_replace($replace, "", $verif['nom']);
				if(strtolower($nom_musee_rech) == $nom_musee){
					$ok = true;
					$sel = new ControleurConnexion;
					$sql_sel = $sel->consulter("*","musee","","nom = '".$verif['nom']."'","","","","","");
					$tab_sql = mysql_fetch_array($sql_sel);

				}
			}
			
			if($ok) {
	?>
		<div class="conteneur_musee">
		<div class="en_tete">
			<h2 class="caterogie_musee">Les musées</h2>
			<h3 class="titre_musee"><?php echo $tab_sql['nom']; ?></h3>
			<a href="musees.html"><img src="./core/img/icon_musee.png" alt="Les musées" title="Les musées" width="32" height="32" /></a>
		</div>
		
		<div class="contenu_musee">
			<div class="cadre">
				<p class="titre">
					Informations :
				</p>
				
				<ul>
					<?php 
						if(!empty($tab_sql['tel'])) { 
							echo '<li class="telephone">+33(0) '.$tab_sql['tel'].'</li>';
						}
						
						if(!empty($tab_sql['mail'])) {
							echo '<li class="email">'.$tab_sql['mail'].'</li>';
						}
						
						if(!empty($tab_sql['siteinternet'])) {
							if(strstr($tab_sql['siteinternet'],'http://')) {
								echo '<li class="site_internet"><a href="'.$tab_sql['siteinternet'].'">Site web du musée</a></li>';
							}
							else {
								echo '<li class="site_internet"><a href="http://'.$tab_sql['siteinternet'].'">'.$tab_sql['siteinternet'].'</a></li>';
							}
						}
						
						if(!empty($tab_sql['adresse'])) {
							echo '<li class="adresse">'.$tab_sql['adresse'].'</li>';
						}
					?>
				</ul>
				<?php
					if(empty($tab_sql['adresse']) AND empty($tab_sql['siteinternet']) AND empty($tab_sql['mail']) AND empty($tab_sql['tel'])){
						echo '<p>Aucune information n\'est disponible pour le moment.</p>';
					}
				?>
			</div>
			
			<div class="cadre">
				<p class="titre">
					Horaires :
				</p>
				
				<p>
					<?php 
						if(!empty($tab_sql['horaire'])) {
							echo $tab_sql['horaire']; 
						}
						else {
							echo 'Aucun horaire n\'est disponible pour le moment.';
						}
					?>
				</p>
			</div>
			
			<div class="cadre">
				<p class="titre">
					Note :
				</p>
				
				<p class="nb_vot">
					Le musée à une note de 
					<?php
						if(!empty($tab_sql['note']))
						{
							echo $tab_sql['note'];
						}else{
							echo "0";
						}
					?>
					/10
				</p>
				<p class="nb_vot">
					<?php echo rating_bar($tab_sql['idmusee'],''); ?>
				</p>
				<p class="nb_vot">
					Pour 
					<?php
						if(!empty($tab_sql['nb_votant']))
						{
							echo $tab_sql['nb_votant'];
							if($tab_sql['nb_votant'] > 1)
								echo "votants";
							else
								echo "votant";
						}else{
							echo "aucun votant";
						}
					?>
				</p>
			</div>
			
			<div class="cadre_style_2">
				<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
					<a class="addthis_button_preferred_1"></a>
					<a class="addthis_button_preferred_2"></a>
					<a class="addthis_button_preferred_3"></a>
					<a class="addthis_button_preferred_4"></a>
					<a class="addthis_button_compact"></a>
				</div>
			</div>

			<div id="slider">
				<ul>				
					<?php
						$b = new ControleurConnexion;
						$sql_img = $b->consulter("*","images","","idmusee = '".$tab_sql['idmusee']."' ","","","","","");
						while($tab_img = mysql_fetch_array($sql_img)){
							if(!empty($tab_img['images'])){
						?>
								<li><a href="#"><img src="images/<?php echo $tab_img['images'];?>" alt="<?php echo $tab_img['alt'];?>" /></a></li>
					<?php
							}
						}
					?>					
				</ul>
			</div>

			<div class="description">
				<div class="titre">
					Description : 
				</div>
				<p><?php echo $tab_sql['description']; ?></p>
			</div>
			
			<div class="clear"></div>
		</div>
	</div>
	<?php
			}
			else {
	?>
		<div id="contenu">
			<h1>Erreur</h1>
			
			<p>
				Il y a eu un <strong>problème avec le musée que vous demandez</strong>.
			</p>
			
			<p>
				Soit celui-ci <strong>n'existe pas</strong>, soit il n'est tout simplement <strong>pas indexé dans notre infrastructure</strong>.<br/>
				<br/><br/><br/><br/>
				<a href="accueil.html" title="Retourner à la page d'Accueil" class="erreur">Retourner à la page d'Accueil</a>
			</p>
			
		</div>
	<?php
			}
		}
		// Sinon, on affiche la liste complète de tous les musées !
		else {
			if(isset($_POST['type']) AND !empty($_POST['type'])){
				$type = $_POST['type'];
				$id = $_POST['id'];
				switch ($type){
					case 'region':
						$select = "musee.nom";
						$from = "region, departement, ville, musee";
						$where = "region.idregion = departement.idregion
							AND departement.iddep = ville.iddep
							AND ville.idville = musee.idville
							AND region.idregion = $id
							AND musee.nom";
					break;
					case 'dep':
						$select = "musee.nom";
						$from = "departement, ville, musee";
						$where = "departement.iddep = ville.iddep
							AND ville.idville = musee.idville
							AND departement.iddep = $id
							AND musee.nom";
					break;
					case 'ville':
						$select = "musee.nom";
						$from = "ville, musee";
						$where = "ville.idville = musee.idville
							AND ville.idville = $id
							AND musee.nom";
					break;
					case '':
						$select = "nom";
						$from = "musee";
						$type = "";
						$where ="nom";
					break;
				}
			}else{
				$select = "nom";
				$from = "musee";
				$type = "";
				$where ="nom";
			}
	?>
		<div id="contenu">
			<h1>Les musées</h1>
		
			<img src="./core/img/musee.png" alt="Tous les musées par ordre alphabétique." title="Tous les musées par ordre alphabétique." class="img_musees_liens" />
			<p class="nombre_de_musees">
				<?php 
					$e = new ControleurConnexion;
					$sql=$e->consulter("nom","musee","", "", "", "", "", "", "");
					$nb_musee = mysql_num_rows($sql);
					if($nb_musee == 0) {
						echo "Il n'y a <strong>aucun musée référencé</strong> dans notre infrastruture.";
					}
					if($nb_musee == 1) {
						echo "Il n'y a <strong>qu'un seul musée référencé</strong> dans notre infrastruture.";
					}
					if($nb_musee > 1) {
						echo "<strong>".$nb_musee." musées sont référencés</strong> dans notre infrastruture.";
					}
				?>
			</p>
			
			<div class="classement_musees_liens">
				<p>
					<?php
						foreach(range('A','Z') as $lettres) {
							echo '<a href="#'.$lettres.'" class="c">'.$lettres.'</a> ';
						}
					?>
				</p>
			</div>
			
			<br/>
			
			<div class="liste_musees">
				<?php
					foreach(range('A','Z') as $lettre) {
				?>
						<h3 id="<?php echo $lettre; ?>"><?php echo $lettre; ?></h3>
						<?php
							$b = new ControleurConnexion;
							$sql=$b->consulter("$select","$from","","$where", "'$lettre%' ", "", "", "", "");
							while($tab = mysql_fetch_array($sql)){
							$nom_mus = str_replace($replace , "", $tab['nom']);
						?>
						<ul>
							<li><a href="musees-<?php echo strtolower($nom_mus); ?>.html"><?php echo $tab['nom']; ?></a></li>
						</ul>
					
				<?php 	
							} 
					}
				?>			
		</div>
	<?php
		}
	?>
