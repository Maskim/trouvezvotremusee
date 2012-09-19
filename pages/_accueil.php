			<div id="recherche">
				<div id="recherche_aide">
					<div class="help_top"></div>
					<div class="help_recherche">
						<p>
							Rechercher en ne tapant que le nom du musée, de la ville, du département ou de la région que vous souhaitez visiter.
							<em>Des suggestions vous sauront faites automatiquement.</em>
						</p>
					</div>
					<div class="help_bottom"></div>
				</div>
				
				<form method="post" action="recherche.html">
				    <div class="gauche">
						<p>
							<input type="text" class="recherche_musee" name="musee" id="musee" value="" accesskey="5" tabindex="30" autocomplete="off" />
						</p>
					</div>

					<p>
						<input class="envoyer" value="Rechercher ce musée" accesskey="6" type="submit" />
					</p>   

					<div id="supplement_recherche">
						<div class="exemple">
							Ex. : <a href="#" title="">Le Louvre</a>, <a href="#" title="">Paris</a>, <a href="#" title="">Grévin</a>, <a href="#" title="">86000</a>...
						</div>
						
						<div class="aleatoire"><a href="#" title="Un musée aléatoirement sélectionné vous est proposé">Aléatoire</a></div>
					</div>

					<div id="resultats"></div>
				</form>
			</div>