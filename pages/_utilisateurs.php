	<?php
		if(isset($_SESSION['connexion']) AND isset($_SESSION['niveau']) AND $_SESSION['niveau']== 5){ 
			require_once("./core/classes/ControleurConnexionPers.php");

			$a = new ControleurConnexion;
			$sql = $a-> consulter("*","utilisateur","","","","","","","");
	?>
		<div id="contenu">
			<h1>Les utilisateurs</h1>
			
			<p>
				<a href="./administration.html">Retour à l'accueil</a>
			</p>
			
			<table>
				<tr>
					<th>Utilisateur</th>
					<th>Nom</th>
					<th>Prénom</th>
					<th>Email<th/>
					<th>Action</th>
				</tr>
			<?php while($tab=mysql_fetch_array($sql)){
					echo "
					<tr>
						<form method=\"POST\" action=\"./core/gestionCompte.php\">
							<td>".$tab['utilisateur']."</td> 
							<td>".$tab['nom']."</td>
							<td>".$tab['prenom']."</td>
							<td>".$tab['mail']."</td>
							<input type=\"hidden\" name=\"idutil\" value=\"".$tab['idutil']."\" />
							<input type=\"hidden\" name=\"nom\" value=\"".$tab['nom']."\" />
							<input type=\"hidden\" name=\"prenom\" value=\"".$tab['prenom']."\" />
							<input type=\"hidden\" name=\"mail\" value=\"".$tab['mail']."\" />
							<input type=\"hidden\" name=\"utilisateur\" value=\"".$tab['utilisateur']."\" />
							<input type=\"hidden\" name=\"niveau\" value=\"".$tab['niveau']."\" />
							<td>
								<input type=\"submit\" name=\"type\" value=\"modifier\" /> 
								<input type=\"submit\" name=\"type\" value=\"supprimer\" />
							</td>
						</form>
					</tr>";
				}
			?>
			</table>
		</div>
	
	<?php
		}
		else {
	?>
		<script type="text/javascript">
			document.location.href = "connexion.html";
		</script>
	<?php
		}
	?>