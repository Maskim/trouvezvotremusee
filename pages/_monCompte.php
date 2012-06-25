<?php
	if(isset($_SESSION['niveau']) && !empty($_SESSION['niveau']) ) {
	?>
		<div id="Compte">
			<div>
				<h1>Vos Informations</h1>
				<form>
				</form>
			</div>

			<div>
				<h1>Vos favoris</h1>
			</div>

			<div>
				<h1>Vos commentaires</h1>
			</div>
		</div>

	<?php
	}else{
		header("Location: connexion.html");
	}
?>