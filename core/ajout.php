<?php
	session_start();

	require_once("./classes/ControleurConnexionPers.php");
	
	if(isset($_POST['type'])) {
		$type = htmlspecialchars($_POST['type']);
		switch($type) {
			
			//Zone de traitement d'un ajout d'un dÃ©partement
			case "ajout_departement" :
				if(isset($_POST['ajout_departement']) AND $_POST['ajout_departement'] == "envoyer"){
					$nomdepartement = htmlspecialchars($_POST['nomdepartement']);
					$idregion = htmlspecialchars($_POST['idregion']);
		
					if(isset($nomdepartement)){
						$a = new ControleurConnexion;
						$b = $a->inserer("departement","nomdep, idregion","'$nomdepartement', '$idregion'");
						header ("Location: ../administration.html");
					}
				}
				else {
					?>
					<script language="javascript" type="text/javascript">window.location.replace("../administration.html");</script>
				<?php
				}
						
			break;
			
			
			//Zone de traitement d'un ajout d'une region
			case "ajout_region" :
				if($_POST['ajout_region'] == "envoyer"){
					$nomregion = htmlspecialchars($_POST['nomregion']);
		
					if(isset($nomregion)){
						$a = new ControleurConnexion;
						$b = $a->inserer("region","nomregion","'$nomregion'");
						?>
							<script language="javascript" type="text/javascript">window.location.replace("../administration.html");</script>
						<?php
					}
				}
			
			
			break;
			
			
			//Zone de traitement d'un ajout d'une ville
			case "ajout_ville" :
				if($_POST['ajout_ville'] == "ajouter"){
					$ville = htmlspecialchars($_POST['nomville']);
					$CP = htmlspecialchars($_POST['cp']);
					$nomdep = htmlspecialchars($_POST['choix1']);
					$nomregion = htmlspecialchars($_POST['region']);
					
					if($nomdep != "choisir departement" || $nomregion != "defaut"){
						$a = new ControleurConnexion;
						$b = $a->consulter("iddep","departement","","nomdep='$nomdep'","","","","");
						$iddep = mysql_fetch_row($b);
						
						if(!empty($ville) && !empty($CP)){
							$c = new ControleurConnexion;
							$d = $a->inserer("ville","nomville, CP, iddep","'$ville', '$CP', '$iddep[0]'");
							?>
								<script language="javascript" type="text/javascript">window.location.replace("../administration.html");</script>
							<?php
						}
						else {
							echo "N'oubliez pas de renseigner les champs villes et/ou code postale. <br/> <a href=\".//administration.html\">Retour</a>";
						}
					} 
					else {
						echo "N'oubliez pas de sélectionnez une région ou un département, si vous n'avez pas de département dans la liste, il faut l'ajouter via 
						la zone d'ajout de département. <br/> <a href=\".//administration.html\">Retour</a>";
					}
				}
			
			
			break;
			
			
			case "musee_dep" : ?>
					<!-- zone ajouter musÃ©e -->
					<?php
						if(isset($_POST['choix1'])) {
							$dep = htmlspecialchars($_POST['choix1']);
							
							$a = new ControleurConnexion;
							$b = $a->consulter("iddep","departement","","nomdep='$dep'","","","","","");
							$iddep = mysql_fetch_row($b);
							
							$c = new ControleurConnexion;
							$d = $c->consulter("*","ville","","iddep='$iddep[0]'","","","","","");
						?>
							<form method="POST" action="" >
								<fieldset>
									<legend>Ajouter un musée</legend> 
									
									Nom du musée : <input type="text" name="nom_musee" /><br />
									Adresse du musée : <input type="text" name="adresse" /><br />
									Numéro de téléphone : <input type="text" size="10" name="tel" /><br />
									Adresse mail : <input type="text" name="email" /><br />
									Site internet : <input type="text" name="siteinternet" /><br />
									Description : <textarea name="description" cols="50" rows="10"/></textarea><br />
									
									<p>
										La ville :
										<select name="idville" >
										<?php
											while($listVille = mysql_fetch_array($d)){
										?>
											<option value="<?php echo $listVille['idville']; ?>"><?php echo $listVille['nomville']; ?></option>
										<?php }	?>
										</select>
									</p>
									
									<p>
										<input type="hidden" name="type" value="ajout_musee" />
										<input type="submit" name="ajout_musee" value="envoyer" />
									</p>
								</fieldset>
							</form>
						Vous vous êtes trompés ? le retour c'est par <a href="../administration.html">là </a>
						<br /><br />
				<?php 	
						} 
						else {
							?>
								<script language="javascript" type="text/javascript">window.location.replace("../administration.html");</script>
							<?php
						}
						break;
			
			//Zone de traitement d'un ajout d'un musée
			case "ajout_musee" :
				if($_POST['ajout_musee'] == "envoyer"){
					$nom_musee = htmlspecialchars($_POST['nom_musee']);
					$adresse = htmlspecialchars($_POST['adresse']);
					$tel = htmlspecialchars($_POST['tel']);
					$mail = htmlspecialchars($_POST['email']);
					$description = htmlspecialchars($_POST['description']);
					$description = str_replace("\"", "'", $description);
					$description = str_replace("'", "\'", $description);
					$site_internet = htmlspecialchars($_POST['siteinternet']);
					$idville = htmlspecialchars($_POST['idville']);
			
			
					if(isset($nom_musee) && isset($adresse)){
						$ajout_musee = new ControleurConnexion; 
						$action = $ajout_musee->inserer("musee", "nom, adresse, tel, mail, description, siteinternet, idville", "'$nom_musee', '$adresse', '$tel', '$mail', '$description', '$site_internet', '$idville'");
						?>
							<script language="javascript" type="text/javascript">window.location.replace("../administration.html");</script>
						<?php
					}
					else {
						echo 'Echec. <br/> <a href="../administration.html">Retour</a>';
					}
				}
			break;
		}
	}
	else {
		?>
			<script language="javascript" type="text/javascript">window.location.replace("../administration.html");</script>
		<?php
	}
?>