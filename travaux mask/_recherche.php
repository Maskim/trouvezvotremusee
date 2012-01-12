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
			<h1>Votre recherche <?php if(isset($_POST['musee'])) { echo ': '.htmlspecialchars($_POST['musee']); } ?></h1>
			
			<?php 
				require_once("./core/classes/ControleurConnexionPers.php");
				
				$rplce = str_replace(" ","", $_POST['musee']);
				$rplce = str_replace("le","", $rplace);
				echo $rplce;
				$nom_du_musee_souhaite = htmlspecialchars($rplce);
				
				$recherche = new ControleurConnexion;
				$nom_du_musee_bdd = $recherche->consulter("nom","musee","","nom='$nom_du_musee_souhaite'","","","","","");
				$tab_verif = mysql_num_rows($nom_du_musee_bdd);
				
				if($tab_verif == 1) {
					$tab_musee = mysql_fetch_array($nom_du_musee_bdd);					
			?>
				<script type="text/javascript">
					document.location.href = "musees-<?php echo strtolower(htmlspecialchars($tab_musee['nom'])); ?>.html";
				</script>
			<?php
				}
				else {
			?>
			
			<h2>Notre suggestion :</h2>
			<p>
				<?php echo htmlspecialchars($_POST['musee']); ?>
			</p>
			
		</div>
		
		<?php	
				}
			}
		?>
