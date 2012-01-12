		<div id="contenu">
			<h1>Connexion</h1>
			
			<div id="log">
				<form action="./core/gestionCompte.php" id="connexion_administration" method="post" >
					<p>
						<label for="util">Login</label><br />
						<input type="text" class="validate[required]" name="util" id="util" tabindex="10" /><br />
					</p>
					<p>
						<label for="mdp">Mot de passe</label><br />
						<input type="password" class="validate[required]" name="mdp" id="mdp" tabindex="10" /><br />
					</p>
					<p>	
						<input type="hidden" name="type" value="connexion" />
						<input type="submit" value="Valider" />
					</p>
				</form>
			</div>
			
		</div>