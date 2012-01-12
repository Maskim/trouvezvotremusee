		<?php
			session_start();
		?>
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
				<p><a href="./inscription.html">Pas encore inscrit ?</a> <a>Mot de passe oublié ?</a></p>
				<?php
					if(isset($_SESSION['connexion']) AND $_SESSION['connexion']){
						echo "<p><a href=\"./deconnexion.html\">deconnexion</a></p>";
					}else{
						echo "<pre>".$_SESSION."</pre>";
					}
				?>
			</div>
			
		</div>