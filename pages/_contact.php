		<div id="contenu">
			<h1>Nous contacter</h1>
			
			<form method="post" id="form_contact" action="./core/nous_contacter.php">
				<p>
					<label for="nom">Votre Nom *</label><br />
					<input type="text" name="nom" id="nom" class="validate[required,custom[onlyLetter]]" tabindex="10" /><br />
				</p>
				
				<p>
					<label for="mail">Votre Email *</label><br />
					<input type="text" name="mail" id="mail" class="validate[required,custom[email]]" tabindex="30" /><br />   
				</p>
				
				<p>
					<label for="objet">Sujet *</label><br />
					<input type="text" name="objet" id="objet" class="validate[required]" tabindex="30" /><br />   
				</p>
				
				<p>
					<label for="precisions">Message *</label><br />
					<textarea name="precisions" id="precisions" class="validate[required]" cols="40" rows="4" tabindex="80"></textarea>
				</p>
   
			    <p>
				    <input type="submit" />
			    </p>
			</form>
		</div>
