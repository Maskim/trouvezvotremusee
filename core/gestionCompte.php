<?php session_start(); ?>
<?php
	require_once("./classes/ControleurConnexionPers.php");

	if(isset($_POST['type']) AND !empty($_POST['type'])) {
	
		$type = htmlspecialchars($_POST['type']);
		switch ($type)
		{
			case "inscription" :
				$antibot = $_POST['antibot'];
				if($antibot == "Je souhaite m'inscrire."){
					$utili = htmlspecialchars($_POST['util']);
					$mdp = htmlspecialchars($_POST['mdp']);
					$vmdp = htmlspecialchars($_POST['vmdp']);
					$nom = htmlspecialchars($_POST['nom']);
					$prenom = htmlspecialchars($_POST['prenom']);
					$mail = htmlspecialchars($_POST['mail']);
					
					$a = new ControleurConnexion();
					$sql = $a -> consulter("COUNT(*)","utilisateur","","utilisateur = '$utili'","","","","","");
					$is_exist = mysql_fetch_row($sql);

					$sql_mail = $a -> consulter("COUNT(*)", "utilisateur", "", "mail = '$mail'", "", "", "", "", "");
					$is_mail_exist = mysql_fetch_row($sql_mail);


					if($is_exist[0]  != 0){
						$_SESSION['error'] = "- Le nom d'utilisateur est déjà utilisé !";
						if($is_mail_exist[0] != 0){
							$_SESSION['error'] .= "</p><p>- Le mail est déjà utilisé !";
						}
						if($mdp != $vmdp){
							$_SESSION['error'] .= "</p><p>- Vos mots de passe ne correspondent pas !";
						}
						header("Location: ../inscription.html");
					}else if($is_mail_exist[0] != 0){
						$_SESSION['error'] = "- Le mail est déjà utilisé !";
						if($mdp != $vmdp){
							$_SESSION['error'] .= "</p><p>- Vos mots de passe ne correspondent pas !";
						}
						header("Location: ../inscription.html");
					}else{

						if($mdp == $vmdp){
							$mdp = md5($mdp);
							$a->inserer("utilisateur","utilisateur, mdp, nom, prenom, mail, niveau","'$utili', '$mdp', '$nom', '$prenom', '$mail', '1'");
							$_SESSION["connexion"] = true;
							$_SESSION['util'] = $utili;
							$_SESSION['nom'] = $nom;
							$_SESSION['prenom'] = $prenom;
							$_SESSION['niveau']= 1 ;
							
							$sql_iduser = $a->consulter("idutil", "utilisateur", "", "utilisateur = '$utili'", "", "", "", "", "");
							$iduser = mysql_fetch_row($sql_iduser);

							$_SESSION['iduser'] = $iduser[0];

							header("Location: ../accueil.html");
						}else{ 
							$_SESSION['error'] = "- Vos mots de passe ne correspondent pas !";
							header("Location: ../inscription.html");
						}
					}
					
				}else{

					header("Location: ../inscription.html");
				}
			break;
			
			case "connexion" :
				$util = htmlspecialchars($_POST['login']);
				$mdp = htmlspecialchars($_POST['mdp']);
				$mdp = md5($mdp);
				
				$a = new ControleurConnexion;
				$sql = $a -> consulter("*","utilisateur","","","","","","", "");
				while($tab_sql = mysql_fetch_array($sql)){
					if($util == $tab_sql['utilisateur'] AND $mdp == $tab_sql['mdp']){
						$_SESSION['connexion'] = true;
						$_SESSION['util'] = $tab_sql['utilisateur'];
						$_SESSION['nom'] = $tab_sql['nom'];
						$_SESSION['prenom'] = $tab_sql['prenom'];
						$_SESSION['niveau']= $tab_sql['niveau'];
						$_SESSION['iduser'] = $tab_sql['idutil'];
					}
				}

				header("Location: ../accueil.html");
			break;
			
			case "modifier" : 
				$idutil = htmlspecialchars($_POST['idutil']);
				$utilisateur = htmlspecialchars($_POST['utilisateur']);
				$nom = htmlspecialchars($_POST['nom']);
				$prenom = htmlspecialchars($_POST['prenom']);
				$mail = htmlspecialchars($_POST['mail']);
				$niveau = htmlspecialchars($_POST['niveau']);?>
				<fieldset>
					<legend>Modifier l'utilisateur <?php echo $utilisateur ?></legend>
					<form method="POST" action="./gestionCompte.php">
						<p>
							Le nom d'utilisateur : <input type="text" name="utilisateur" value="<?php echo $utilisateur ; ?>" /> <br /><br />
							Le nom : <input type="text" name="nom" value="<?php echo $nom ; ?>" /> <br /><br />
							Le prenom : <input type="text" name="prenom" value="<?php echo $prenom ; ?>" /><br /><br />
							L'adresse mail :<input type="text" name="mail" value="<?php echo $mail ; ?>" /><br /><br />
							Le niveau de l'utilisateur : 
							<select name="niveau">
								<option <?php if($niveau == 1){echo "selected";} ?> value="1">utilisateur</option>
								<option <?php if($niveau == 2){echo "selected";} ?> value="2">administrateur</option>
								<option <?php if($niveau == 3){echo "selected";} ?> value="3">Directeur de musÃ©e</option>
							</select>
						</p>
						
						<p>
							<input type="hidden" name="idutil" value="<?php echo $idutil ; ?>" />
							<input type="hidden" name="type" value="modif-action" />
							<input type="submit" name="mod" name="modifier" />
						</p>
					</form>
				</fieldset>
				Vous vous Ãªtes trompÃ© ? Le retour c'est<a href="../utilisateurs.html"> lÃ  </a>
			<?php
			break;
			
			case "modif-action" :
				$idutil = htmlspecialchars($_POST['idutil']);
				$utilisateur = htmlspecialchars($_POST['utilisateur']);
				$nom = htmlspecialchars($_POST['nom']);
				$prenom = htmlspecialchars($_POST['prenom']);
				$mail = htmlspecialchars($_POST['mail']);
				$niveau = htmlspecialchars($_POST['niveau']);
				
				$a = new ControleurConnexion;
				$sql = $a->modifier("utilisateur","utilisateur='$utilisateur', nom='$nom', prenom='$prenom', mail='$mail', niveau='$niveau'","idutil='$idutil'","","");
				?>
				<script language="javascript" type="text/javascript">window.location.replace("../utilisateurs.html");</script>
				<?php
			break;
			
			case "supprimer" :
				$idutil = htmlspecialchars($_POST['idutil']);
				$utilisateur = htmlspecialchars($_POST['utilisateur']);
				$nom = htmlspecialchars($_POST['nom']);
				$prenom = htmlspecialchars($_POST['prenom']);
				$mail = htmlspecialchars($_POST['mail']);
				$niveau = htmlspecialchars($_POST['niveau']);
				
				$a = new ControleurConnexion;
				$sql = $a->supprimer("utilisateur","utilisateur='$utilisateur'", "","");
				?>
				<script language="javascript" type="text/javascript">window.location.replace("../utilisateurs.html");</script>
				<?php
			break;
		}
		
	// Fin du Si pour savoir si le type existe!
	}
	else {
	?>
		<script language="javascript" type="text/javascript">window.location.replace("../accueil.html");</script>
	<?php
	}
?>