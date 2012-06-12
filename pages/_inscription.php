<?php session_start(); ?>
	<?php
		if(!isset($_SESSION['connexion']) || !$_SESSION['connexion']){
	?>
		<div id="contenu">
			<h1>Inscription</h1>

			<div id="log">
				<form method="POST" action="./core/gestionCompte.php" id="formInscription">
					<p>
						<label for="util">Votre nom d'utilisateur : </label><br />
						<input type="text" class="validate[required]" name="util" id="util" />
					</p>
					<p>
						<label for="mdp">Votre mot de passe : </label><br />
						<input type="password" class="validate[required]" name="mdp" id="mdp" />
					</p>
					<p>
						<label for="vmdp">Validez votre mot de passe : </label><br />
						<input type="password" class="validate[required,confirm[mdp]]" name="vmdp" id="vmdp" />
					</p>
					<p>
						<label for="nom">Votre nom : </label><br />
						<input type="text" class="validate[required]" name="nom" id="nom" />
					</p>
					<p>
						<label for="prenom">Votre prenom : </label><br />
						<input type="text" class="validate[required]" name="prenom" id="prenom" />
					</p>
					<p>
						<label for="mail">Votre mail : </label><br />
						<input type="text" class="validate[required,custom[email]]" name="mail" id="mail" />
					</p>
					
					<p>
						<label for="antibot">Veuillez recopier la phrase : "Je souhaite m'inscrire." : </label><br />
						<input type="text" class="validate[required]" name="antibot" id="antibot"/>
					<p/>
					
					<input type="hidden" name="type" value="inscription" />
					<input type="submit" name="Inscr_env" value="S'inscrire" />
				</form>
	<?php
		}else{
	?>
			<p>Vous étes déjà connecté !</p>
			<script language="javascript" type="text/javascript">window.location.replace("accueil.html");</script>
	<?php
		}
	?>