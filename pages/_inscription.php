	<?php
		session_start();
		if(!isset($_SESSION['connexion']) || !$_SESSION['connexion']){
	?>
			<div id="contenu">
				<h1>Inscription</h1>
				<form method="POST" action="./core/gestionCompte.php">
					<p><label for="util">Votre nom d'utilisateur : </label>
					<input type="text" name="util" id="util" /></p>
					<br />
					<p><label for="mdp">Votre mot de passe : </label>
					<input type="password" name="mdp" id="mdp" /></p>
					<br />
					<p><label for="vmdp">Validez votre mot de passe : </label>
					<input type="password" name="vmdp" id="vmdp" /></p>
					<br />
					<p><label for="nom">Votre nom : </label>
					<input type="text" name="nom" id="nom" /></p>
					<br />
					<p><label for="prenom">Votre prenom : </label>
					<input type="text" name="prenom" id="prenom" /></p>
					<br />
					<p><label for="mail">Votre mail : </label>
					<input type="text" name="mail" id="mail" /></p>
					
					<p><label for="antibot">Veuillez recopier la phrase : "Je souhaite m'inscrire." : </label>
					<input type="text" name="antibot" id="antibot"/> <p/>
					
					<input type="hidden" name="type" value="inscription" />
					<input type="submit" name="Inscr_env" value="S'inscrire" />
				</form>
			</div>
	<?php
		}else{
	?>
			<p>Vous étes déjà connecté !</p>
			<script language="javascript" type="text/javascript">window.location.replace("accueil.html");</script>
	<?php
		}
	?>