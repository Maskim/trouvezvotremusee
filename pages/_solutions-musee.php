		<div id="marketing">
			<ul>
				<li><h3 class="ombre bleu">Diversifiez la culture</h3></li>
				<li><h3 class="ombre vert">Atteignez un plus<br/>large public</h3></li>
				<li><h3 class="ombre orange">Soyons partenaires,<br/>Nous pouvons vous aider</h3></li>
			</ul>

			<div class="clear"></div><br/>
			<?php if(isset($_GET['Nom']) && !empty($_GET['Nom']) && $_GET['Nom'] == 'soumettre'){ ?>
				<form method="POST" action="">
					<p>
						<label for='name'>Nom du musée</label>
						<input type="text" name="name" />
					</p>

					<p>
						<label for='add'>Adresse</label>
						<input type="text" name="add" />
					</p>

					<p>
						<label for='tel'>Téléphone</label>
						<input type="text" name="name" />
					</p>

					<p>
						<label for='mail'>Mail</label>
						<input type="text" name="mail" />
					</p>

					<p>
						<label for='ville'>Ville</label>
						<input type="text" name="ville" />
					</p>

					<p>
						<label for='motcle'>Mot Clé</label>
						<input type="text" name="motcle" />
					</p>

					<p>
						<label for='desc'>Description</label>
						<textarea name="desc"></textarea>
					</p>

					<input type="submit" name="soumettre" value="Soumettre" />
				</form>
			<?php }else{ ?>
				<form method="POST" action="http://localhost/trouvezvotremusee/services-soumettre.html">
					<p class="centrerBouton">
						<input type="submit" name="soumettre" value="Soumettre un musée" class="soumettre" />
					</p>
				</form>
			<?php } ?>
		</div>
