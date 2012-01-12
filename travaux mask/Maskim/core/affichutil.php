<?php include("../header.php"); ?>
<?php require_once("./classes/ControleurConnexionPers.php"); ?>
<body>
	<?php 
		$a = new ControleurConnexion;
		$sql = $a-> consulter("*","utilisateur","","","","","","");?>
		<table>
		<tr>
			<th> Utilisateur </th> <th> Nom </th> <th> prenom </th> <th> mail <th/> <th>action</th>
		</tr>
		<?php while($tab=mysql_fetch_array($sql)){
			echo "
			<tr>
				<form method=\"POST\" action=\"./gestioncompte.php\">
					<td>".$tab['utilisateur']."</td> <td>".$tab['nom']."</td><td>".$tab['prenom']."</td><td>".$tab['mail']."</td>
					<input type=\"hidden\" name=\"idutil\" value=\"".$tab['idutil']."\" />
					<input type=\"hidden\" name=\"nom\" value=\"".$tab['nom']."\" />
					<input type=\"hidden\" name=\"prenom\" value=\"".$tab['prenom']."\" />
					<input type=\"hidden\" name=\"mail\" value=\"".$tab['mail']."\" />
					<input type=\"hidden\" name=\"utilisateur\" value=\"".$tab['utilisateur']."\" />
					<input type=\"hidden\" name=\"niveau\" value=\"".$tab['niveau']."\" />
					<td><input type=\"submit\" name=\"type\" value=\"modifier\" /></td>
				</form>
			</tr>";
		}
	?>
	
	</table>
	le retour :<a href="../index.php"> c'est là </a>
</body>
</html>