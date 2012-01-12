<?php
	session_start();

	require_once("./classes/ControleurConnexionPers.php");
	
	$type = $_POST['type'];
	switch($type)
	{
		
		//zone de traitement d'un ajout d'un département
		case "ajout_departement" :
			if($_POST['ajout_departement']=="envoyer"){
				$nomdepartement=$_POST['nomdepartement'];
				$idregion=$_POST['idregion'];
	
				if(isset($nomdepartement)){
					$a = new ControleurConnexion;
					$b = $a->inserer("departement","nomdep, idregion","'$nomdepartement', '$idregion'");
					header ("Location: ../index.php");
				}
			}
		
		
		break;
		
		
		//zone de traitement d'un ajout d'une region
		case "ajout_region" :
			if($_POST['ajout_region']=="envoyer"){
				$nomregion=$_POST['nomregion'];
	
				if(isset($nomregion)){
				$a = new ControleurConnexion;
				$b = $a->inserer("region","nomregion","'$nomregion'");
				header ("Location: ../index.php");
				}
			}
		
		
		break;
		
		
		//zone de traitement d'un ajout d'une ville
		case "ajout_ville" :
			if($_POST['ajout_ville']=="ajouter"){
				$ville=$_POST['nomville'];
				$CP=$_POST['cp'];
				$nomdep=$_POST['choix1'];
				$nomregion=$_POST['region'];
				if($nomdep!="choisir departement" || $nomregion!="defaut"){
					$a = new ControleurConnexion;
					$b = $a->consulter("iddep","departement","","nomdep='$nomdep'","","","","");
					$iddep = mysql_fetch_row($b);
					
					if($ville!="" && $CP!=""){
						$c = new ControleurConnexion;
						$d = $a->inserer("ville","nomville, CP, iddep","'$ville', '$CP', '$iddep[0]'");
						header ("Location: ../index.php");
					}else{
						echo "N'oubliez pas de renseigner les champs villes et/ou code postale. Le <a href=\".//index.php\">retour";
					}
				}else{
					echo "N'oubliez pas de selectionnez une région ou un département, si vous n'avez pas de département dans la liste, il faut l'ajouter via 
					la zone d'ajout de département. <br /> Le <a href=\"../index.php\">retour</a>";
				}
			}
		
		
		break;
		
		
		case "musee_dep" : ?>
				<!-- zone ajouter musée -->
				<?php
					$dep=$_POST['choix1'];
					
					$a = new ControleurConnexion;
					$b = $a->consulter("iddep","departement","","nomdep='$dep'","","","","");
					$iddep=mysql_fetch_row($b);
					
					$c = new ControleurConnexion;
					$d = $c->consulter("*","ville","","iddep='$iddep[0]'","","","","");
				?>
				<form method="POST" action="./ajout.php" >
					<fieldset>
						<legend>zone ajouter un musée </legend> 
						Nom du musée : <input type="text" name="nom_musee" /><br />
						Adresse du musé: <input type="text" name="adresse" /><br />
						Numéro de téléphone : <input type="text" size="10" name="tel" /><br />
						Adresse mail : <input type="text" name="email" /><br />
						Site internet : <input type="text" name="siteinternet" /><br />
						Description : <textarea name="description" /></textarea><br />
						Photo : <input type="text" name="photo" /> <br />
						
						<p>La ville :
						<select name="idville" >
						<?php
							while($listVille=mysql_fetch_array($d)){?>
								<option value="<?php echo $listVille['idville']; ?>"><?php echo $listVille['nomville']; ?></option>
							<?php }
						?>
						</select>
						<input type="hidden" name="type" value="ajout_musee" />
						<input type="submit" name="ajout_musee" value="envoyer" />
					</fieldset>
				</form>
				Vous vous êtes trompés ? le retour c'est par <a href="../index.php">là</a>
				<br /><br />
			<?php break;
		
		//zone de traitement d'un ajout d'un musée
		case "ajout_musee" :
			if($_POST['ajout_musee']=="envoyer"){
				$nom_musee=$_POST['nom_musee'];
				$adresse=$_POST['adresse'];
				$tel=$_POST['tel'];
				$mail=$_POST['email'];
				$description=$_POST['description'];
				$photo=$_POST['photo'];
				$site_internet=$_POST['siteinternet'];
				$idville=$_POST['idville'];
		
		
				if(isset($nom_musee ) && isset($adresse)){
					$ajout_musee = new ControleurConnexion; 
					$action=$ajout_musee->inserer("musee", "nom, adresse, tel, mail, description, photo, siteinternet, idville", "'$nom_musee', '$adresse', '$tel', '$mail', '$description', '$photo', '$site_internet', '$idville'");
					header ("Location: ../index.php");
				}else{
					echo "raté...";
				}

			}
		
		break;
	}
?>