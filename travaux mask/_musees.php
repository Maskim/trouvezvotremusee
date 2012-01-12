	<?php 
		require_once("./core/classes/ControleurConnexionPers.php");
		
		if(isset($_GET['Nom']) AND !empty($_GET['Nom'])) {

			$nom_musee = htmlspecialchars($_GET['Nom']);
			$a = new ControleurConnexion;
			$sql=$a->consulter("*","musee","", "nom='$nom_musee'", "","", "", "", "");
			
			$tab_sql = mysql_fetch_array($sql);
			if(strtolower($tab_sql['nom']) == $nom_musee) {
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
							echo '<li class="telephone">+33'.$tab_sql['tel'].'</li>';
						}
						
						if(!empty($tab_sql['mail'])) {
							echo '<li class="email">'.$tab_sql['mail'].'</li>';
						}
						
						if(!empty($tab_sql['siteinternet'])) {
							if(strstr($tab_sql['siteinternet'],'http://')) {
								echo '<li class="site_internet"><a href="'.$tab_sql['siteinternet'].'">'.$tab_sql['siteinternet'].'</a></li>';
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
					<li><a href="#"><img src="images/toystory.jpg" alt="Css Template Preview" /></a></li>
					<li><a href="#"><img src="images/walle.jpg" alt="Css Template Preview" /></a></li>	
				</ul>
			</div>

			<div class="description">
				<?php echo $tab_sql['description']; ?>
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
						echo "Il n'y a <strong>aucun musées référencé</strong> dans notre infrastruture.";
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
							$sql=$b->consulter("nom","musee","", "nom", "'$lettre%'", "", "", "", "");
							while($tab = mysql_fetch_array($sql)){
						?>
						<ul>
							<li><a href="musees-<?php echo strtolower($tab['nom']); ?>.html"><?php echo $tab['nom']; ?></a></li>
						</ul>
					
				<?php 	
							} 
					}
				?>			
		</div>
	<?php
		}
	?>
