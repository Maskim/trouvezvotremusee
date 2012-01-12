<?php require_once("../header.php"); ?>
<body>
	<p> Pour se connecter, c'est içi ! </p>
	<form method="POST" action="./gestioncompte.php">
		Votre identifiant : <input type="input" name="util" /><br />
		Votre mot de passe : <input type="password" name="mdp" /><br />
		<input type="hidden" name="type" value="connexion" />
		<input type="submit" name="connexion" value="se connecter" />
	</form>
</body>
</html>