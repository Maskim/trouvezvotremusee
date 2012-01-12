<?php require_once("../header.php"); ?>

<body>
	<p>Voici la zone d'inscription : </p>
	<form method="POST" action="./gestioncompte.php">
		Votre nom d'utilisateur : <input type="text" name="nomutilisateur" /><br />
		Votre mot de passe <input type="password" name="mdp" /><br />
		Confirmation du mot de passe : <input type="password" name="validmdp" /><br />
		
		
		Votre nom : <input type="text" name="nom" /><br />
		Votre prénom : <input type="text" name="prenom" /><br />
		Votre adresse mail : <input type="text" name="adressemail" /><br />
		Veuillez taper cette phrase : "Je souhaite m'inscrire." <br />
		<input type="text" name="antibot" /><br />
		<input type="hidden" name="type" value="inscription" />
		<input type="submit" name="inscription" value="s'inscrire" />
	</form>
</body>
</html>