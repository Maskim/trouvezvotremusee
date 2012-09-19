﻿<?php
	if(isset($_SESSION['niveau']) && !empty($_SESSION['niveau']) ) {

		$a = new ControleurConnexion();
		$id = $_SESSION['iduser'];
		$result = $a-> consulter("*","utilisateur","","idutil = $id","","","","","");
		$utilisateur = mysql_fetch_row($result);
	?>
		<div id="contenu">
			<div id="info">
				<h1>Vos Informations</h1>
					<?php
						if(isset($_COOKIE['error'])){
							$error = utf8_decode($_COOKIE['error']);
							echo '<div class="error">' . $error . '</div>';
							setcookie("error", "", time() - 3600, "/");
						}

					?>
					<div id="modifmdp">
						<form method="POST" action="./core/modifUtil.php">
							<p>Mot de passe actuel : <input type="password" name="mdp" /></p>
							<p>Nouveau mot de passe : <input type="password" name="nmdp" /></p>
							<p>Validez le mot de passe : <input type="password" name="vmdp" /></p>
							<p><input type="submit" value="Modifier"/></p>
							<input type="hidden" name="type" value="modifmdp" />
							<input type="hidden" name="idutil" value="<?php echo $utilisateur[0] ?>" />
						</form>
					</div>
					<form action="./core/modifUtil.php" method="POST" id="formModifUser">
						<p>Login : <span id="login"><?php echo $utilisateur[1]; ?></span></p>
						<p>Email : <span id="mail"><?php echo $utilisateur[5]; ?></span></p>
						<p>Nom : <span id="nom"><?php echo $utilisateur[3]; ?></span></p>
						<p>Prenom : <span id="prenom"><?php echo $utilisateur[4]; ?></span></p>
						<input type="hidden" name="idutil" value="<?php echo $utilisateur[0] ?>" />
						<p id="modifier">
							<a href="" onclick="modifInfoCompte();return false;">Modifier</a> 
							-- <a href="" onclick="modifMDP();return false;">Modifier mot de passe</a>
						</p>
						<input type="hidden" name="type" value="modifutil" />
					</form>
			</div>

			<div>
				<h1>Vos favoris</h1>
				<?php 
					$util = $_SESSION['iduser'];
					$a = new ControleurConnexion();

					$favoris = $a->consulter("*", "favori, musee", "", "util = $util AND musee = idmusee", "", "", "", "","");

					while($tab = mysql_fetch_array($favoris)){
						?>

						<p id="fav-<?php echo $tab['idmusee']; ?>"><?php echo $tab['nom']; ?> -- <a href="" onclick="deleteFavori(<?php echo $tab['idmusee']; ?>, <?php echo $tab['util']; ?>);return false;">Supprimer de vos favoris</a> </p>

						
						<?php
					}
				?>
			</div>

			<div>
				<h1>Vos commentaires</h1>

				<?php
					$commentaire = $a->consulter("*", "commentaire, musee", "", "iduser = '$util' AND musee.idmusee = commentaire.idmusee", "", "commentaire.idmusee", "", "", "");
					while($all_com = mysql_fetch_array($commentaire)){
						?>
						<p><?php echo $all_com['nom']; ?> -- <a href="./musees-<?php echo prepareString($all_com['nom']); ?>.html">Voir votre commentaire</a></p>
						<?php
					}
				?>	
			</div>
		</div>

	<?php
	}else{
		header("Location: connexion.html");
	}
?>