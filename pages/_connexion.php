	<div id="contenu">
		<h1>Connexion</h1>
		
		<?php if(isset($_SESSION['redirect_to'])){
			if(isset($_SESSION['type_redirect']) && $_SESSION['type_redirect'] == 'addToMyFave'){
				?>
				<p class="attention">Pour ajouter ce mus?e ? vos favoris, il faut tout d'abord vous connecter.</p>
				<?php
			}
		} ?>

		<div id="log">
			<form action="./core/gestionCompte.php" id="connexion_administration" method="post" >
				<p>
					<label for="util">Login</label><br />
					<input type="text" class="validate[required]" name="login" id="util" tabindex="10" /><br />
				</p>
				<p>
					<label for="mdp">Mot de passe</label><br />
					<input type="password" class="validate[required]" name="mdp" id="mdp" tabindex="10" /><br />
				</p>
				<p>	
					<input type="hidden" name="type" value="connexion" />
					<input type="submit" value="Valider" />
				</p>
				<?php if(isset($_SESSION['redirect_to'])){ ?>
					<input type="hidden" name="redirect_to" value="<?php echo $_SESSION['redirect_to']; ?>" />
				<?php } 
				if(isset($_SESSION['id_musee'])){ ?>
					<input type="hidden" name="id_musee" value="<?php echo $_SESSION['id_musee']; ?>" />
				<?php } ?>
			</form>
			<p><a href="./inscription.html">Pas encore inscrit ?</a> <a>Mot de passe oubli? ?</a></p>
		</div>
		
	</div>