<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Trouvez votre musée</title>

	<link rel="shortcut icon" type="image/x-icon" href="./core/icons/loupe.ico" />
	<meta name="keywords" content="musee, musees, musées, musée, louvre, grevin, grévin, culture, patrimoine, oeuvre, oeuvres, art, arts, sculpture, sculptures, tableau, tableaux, piece, pièce, pieces, pièces" />
	<meta name="description" content="Trouvez votre musée est un moteur de recherche sur les musées en France. Informations et présentations sont au rendez-vous." />
	<meta name="geo.placename" content="Poitiers, Poitou-Charentes, France" />

	<link rel="stylesheet" media="screen" href="core/css/template.css" />
	<link rel="stylesheet" media="screen" href="core/css/validationEngine.jquery.css"/>
	<link rel="stylesheet" type="text/css" href="core/css/rating_bar/rating.css" />

	<script type="text/javascript" src="core/js/jquery.js"></script>
	<script type="text/javascript" src="core/js/navigation.js"></script>
	<script type="text/javascript" language="javascript" src="core/js/rating_bar/behavior.js"></script>
	<script type="text/javascript" language="javascript" src="core/js/rating_bar/rating.js"></script>

	<?php require_once('./core/fonctions.php'); ?>
</head>
<body>

	<div id="header_holder">
		<div id="header">
			<div id="logo"><a href="#" title="Trouvezvotremusee : Entre vous et la culture, il n'y a qu'un pas.">Trouvezvotremusee</a></div>

			<div id="navigation_header">
				<ul>
					<li class="accueil"><a href="accueil.html" title="Accueil">Accueil</a>
					<li class="musees"><a href="musees.html" title="Les musées">Les musées</a>
					<li class="contact"><a href="contact.html" title="Nous contacter">Nous contacter</a>
				</ul>
			</div>
		</div>
	</div>

	<div id="content_holder">
		<div id="content">
			<div id="connexion">
				<div id="connexion_content">
					<?php if(!isset($_SESSION['util']) || !$_SESSION['connexion']){ ?>
					<form action="./core/gestionCompte.php" id="connexion_administration" method="post">
						<p class="input">
							<input type="text" class="validate[required]" id="login" name="login" value="Identifiant" onFocus="if(this.value == 'Identifiant') { this.value=''; }" onBlur="if(this.value == '') { this.value='Identifiant'; }"/>
						</p>

						<p class="input">
							<input type="password" class="validate[required]" id="mdp" name="mdp" value="Mot de passe" onFocus="if(this.value == 'Mot de passe') { this.value=''; }" onBlur="if(this.value == '') { this.value='Mot de passe'; }" />
						</p>
						
						<p class="submit">
							<input type="hidden" name="type" value="connexion" />
							<input type="submit" name="connexion" value="Se connecter" />
						</p>

						<p class="input mot_de_passe_oublie_p">
							<a href="#" class="mot_de_passe_oublie" title="Mot de passe oublié ?"></a>
						</p>
					</form>
					<?php } if(isset($_SESSION['util']) && $_SESSION['connexion']){ ?>
					<ul>
						<li class="mon_compte"><a href="moncompte.html">Mon compte</a></li>
						<li class="mes_notifications"><a href="mesnotifications.html"><span>753</span></a></li>
						<li class="musees_favoris"><a href="mesfavoris.html"><span>2</span></a></li>
						<li class="ajout_musee"><a href="ajoutmusee.html">Ajouter un musée</a></li>
					</ul>
					<?php } ?>
				</div>

				<div id="fermer">
					<span>Fermer</span>
				</div>

				<div class="zone_connexion">
					<span>Connexion</span>
				</div>
			</div>

			<div id="contenu">
				<?php 
					$page = (!empty($_GET['page'])) ? htmlentities($_GET['page']) : 'accueil';
					$array_pages = array(
						'accueil' => './pages/_accueil.php',
						'recherche' => './pages/_recherche.php',
						'apropos' => './pages/_apropos.php',
						'services' => './pages/_solutions-musee.php',
						'aide' => './pages/_aide.php',
						'musees' => './pages/_musees.php',
						'recherchepar' => './pages/_recherchepar.php',
						'faq' => './pages/_FAQ.php',
						'contact' => './pages/_contact.php',
						'administration' => './pages/_admin.php',
						'utilisateurs' => './pages/_utilisateurs.php',
						'connexion' => './pages/_connexion.php',
						'affichage' => './pages/_affichage.php',
						'affichagemusee' => './pages/_affichage-musee.php',
						'ajout' => './pages/_ajout.php',
						'deconnexion' => './pages/_deconnexion.php',
						'searcherror' => './pages/_erreur_recherche.php',
						'erreur' => './pages/_erreur.php',
						'inscription' => './pages/_inscription.php',
						'ville' => './pages/_ville.php',
						'departement' => './pages/_departement.php',
						'region' => './pages/_region.php',
						'moncompte' => './pages/_monCompte.php',
						'mesfavoris' => './pages/_favori.php',
						'csvtobdd' => './pages/_csvTobdd.php'
					);
					
					if(!array_key_exists($page, $array_pages)) include('./pages/_erreur.php');
					elseif(!is_file($array_pages[$page])) include('./pages/_erreur.php');
					else include($array_pages[$page]);
				?>
				
			</div>

			<div id="populaires">
				<h1><span>Les plus</span> populaires</h1>

				<div id="selection_populaire">
					<div class="selection_line">
						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="clear"></div>
					</div>

					<div class="selection_line">
						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="selection_un_musee">
							<a href="#" class="infobulle"><img src="images/musees/musee.png" alt="Le Louvre" /><span>Le Louvre</span></a>
						</div>

						<div class="clear"></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="footer_holder">
		<div id="footer">
			<ul id="navigation_footer">
				<li>© <?php if(date('Y') >= '2010') { echo date('Y'); } else { echo '2010 - '.date('Y'); }?> -</li>
				<li><a href="#" title="Nos mentions légales">Mentions légales</a> -</li>
				<li><a href="#" title="Versions de Trouvezvotremusee">Changelog</a> -</li>
				<li><a href="#" title="A propos de nous">A propos</a></li>
			</ul>
		</div>
	<div>

</body>
</html>