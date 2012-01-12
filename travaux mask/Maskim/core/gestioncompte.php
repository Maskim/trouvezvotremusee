<?php
	session_start();
	require_once("./classes/ControleurConnexionPers.php");

	$type=$_POST['type'];
	switch ($type)
	{
		case "inscription" :
			$antibot=$_POST['antibot'];
			if($antibot="Je souhaite m'inscrire."){
				$utili=$_POST['nomutilisateur'];
				$mdp=$_POST['mdp'];
				$vmdp=$_POST['validmdp'];
				$nom=$_POST['nom'];
				$prenom=$_POST['prenom'];
				$mail=$_POST['adressemail'];
				
				if($mdp == $vmdp){
					$a = new ControleurConnexion;
					$sql=$a->inserer("utilisateur","utilisateur, mdp, nom, prenom, mail, niveau","'$utili', '$mdp', '$nom', '$prenom', '$mail', '1'");
					header ("Location: ../index.php");
					$_SESSION["connexion"] = true;
					$_SESSION['util'] = $utili;
					$_SESSION['nom'] = $nom;
					$_SESSION['prenom'] = $prenom;
					$_SESSION['niveau']= 1 ;
				}else{header ("Location: ../index.php");}
				
			}else{
				header ("Location: ../index.php");
			}
		break;
		
		case "connexion" :
			$util = $_POST['util'];
			$mdp = $_POST['mdp'];
			
			
			$a = new ControleurConnexion;
			$sql = $a -> consulter("*","utilisateur","","","","","","");
			while($tab_sql = mysql_fetch_array($sql)){
				if($util == $tab_sql['utilisateur'] && $mdp == $tab_sql['mdp']){
					$_SESSION['connexion'] = true;
					$_SESSION['util'] = $tab_sql['utilisateur'];
					$_SESSION['nom'] = $tab_sql['nom'];
					$_SESSION['prenom'] = $tab_sql['prenom'];
					$_SESSION['niveau']= $tab_sql['niveau'];
				}
			}
			header('Location: ../index.php');
		break;
		
		case "modifier" : 
			$idutil = $_POST['idutil'];
			$utilisateur = $_POST['utilisateur'];
			$nom = $_POST['nom'];
			$prenom = $_POST['prenom'];
			$mail = $_POST['mail'];
			$niveau = $_POST['niveau'];?>
			<fieldset>
				<legend>Modifier l'utilisateur <?php echo $utilisateur ?></legend>
				<form method="POST" action="./gestioncompte.php">
					Le nom d'utilisateur : <input type="text" name="utilisateur" value="<?php echo $utilisateur ; ?>" /> <br />
					Le nom : <input type="text" name="nom" value="<?php echo $nom ; ?>" /> <br />
					Le prenom : <input type="text" name="prenom" value="<?php echo $prenom ; ?>" /><br />
					L'adresse mail :<input type="text" name="mail" value="<?php echo $mail ; ?>" /><br />
					Le niveau de l'utilisateur : 
					<select name="niveau">
						<option <?php if($niveau == 1){echo "selected";} ?> value="1">utilisateur</option>
						<option <?php if($niveau == 2){echo "selected";} ?> value="2">administrateur</option>
						<option <?php if($niveau == 3){echo "selected";} ?> value="3">Directeur de musée</option>
					</select>
					<input type="hidden" name="idutil" value="<?php echo $idutil ; ?>" />
					<input type="hidden" name="type" value="modif-action" />
					<input type="submit" name="mod" name="modifier" />
				</form>
			</fieldset>
			Vous vous êtes trompé ? Le retour c'est<a href="./affichutil.php"> là </a>
		<?php
		break;
		
		case "modif-action" :
			$idutil = $_POST['idutil'];
			$utilisateur = $_POST['utilisateur'];
			$nom = $_POST['nom'];
			$prenom = $_POST['prenom'];
			$mail = $_POST['mail'];
			$niveau = $_POST['niveau'];
			
			$a = new ControleurConnexion;
			$sql = $a->modifier("utilisateur","utilisateur='$utilisateur', nom='$nom', prenom='$prenom', mail='$mail', niveau='$niveau'","idutil='$idutil'","","");
			header ("Location: ./affichutil.php");
		break;
	}
?>