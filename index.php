<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<title>Trouvez votre musée</title>
	<link rel="shortcut icon" type="image/x-icon" href="./core/icons/loupe.ico" />
	<meta name="keywords" content="musee, musees, musées, musée, louvre, grevin, grévin, culture, patrimoine, oeuvre, oeuvres, art, arts, sculpture, sculptures, tableau, tableaux, piece, pièce, pieces, pièces" />
	<meta name="description" content="Trouvez votre musée est un moteur de recherche sur les musées en France. Informations et présentations sont au rendez-vous." />
	<meta name="geo.placename" content="Poitiers, Poitou-Charentes, France" />
	
		<! Script et style de barre de vote !>
		<script type="text/javascript" language="javascript" src="core/js/rating_bar/behavior.js"></script>
		<script type="text/javascript" language="javascript" src="core/js/rating_bar/rating.js"></script>

		<link rel="stylesheet" type="text/css" href="core/css/rating_bar/default.css" />
		<link rel="stylesheet" type="text/css" href="core/css/rating_bar/rating.css" />
	
	<link rel="stylesheet" media="screen" href="./core/css/template.css" />
	<link rel="stylesheet" media="screen" href="./core/css/validationEngine.jquery.css"/>
</head>
<body>

	<div id="head">
		<div id="en-tete">
			<div id="logo"><a href="accueil.html" accesskey="1"></a></div>
			
			<div id="navigation">
				<ul>
					<li><a href="accueil.html" title="Accueil" accesskey="1">Accueil</a></li>
					<li><a href="musees.html" title="Liste des musées en France" accesskey="2">Les Musées</a></li>
					<li><a href="faq.html" title="Nous répondons aux questions que vous vous posez" accesskey="3">FAQ</a></li>
					<li><a href="contact.html" title="Nous Contacter" accesskey="4">Nous Contacter</a></li>
				</ul>
			</div>
		</div>
	</div>
	
	<div id="page">
		<div id="global">
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
					'inscription' => './pages/_inscription.php'
					
					// 'Accueil' => './pages/_accueil.php',
					// 'Recherche' => './pages/_recherche.php',
					// 'A-propos' => './pages/_apropos.php',
					// 'Services' => './pages/_solutions-musee.php',
					// 'Aide' => './pages/_aide.php',
					// 'Les-musees' => './pages/_musees.php',
					//'FAQ' => './pages/_FAQ.php',
					// 'Nous-contacter' => './pages/_contact.php',
					// 'Administration' => './pages/_admin.php',
					// 'Erreur' => './pages/_erreur.php'
				);
				
				if(!array_key_exists($page, $array_pages)) include('./pages/_erreur.php');
				elseif(!is_file($array_pages[$page])) include('./pages/_erreur.php');
				else include($array_pages[$page]);
			?>
		</div>
		
		<div id="pied">
			<div id="pied_centre">
				<div id="navigation_sous_recherche">
					<p>
						<a href="aide.html" title="Nous vous apportons notre aide pour tous vos problèmes" accesskey="7">Vous cherchez de l'aide ?</a>
						<a href="services.html" title="Solutions musée" accesskey="8">Solutions musée</a>
						<a href="apropos.html" title="À propos" accesskey="9">À propos</a>
					</p>
				</div>
				<div id="copyright">
					<p>
						&copy; <?php if(date('Y') >= '2010') { echo date('Y'); } else { echo '2010 - '.date('Y'); }?>
					</p>
				</div>
			</div>
		</div>
	</div>
	
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4d31657565e2636b"></script>
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.4.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="./core/js/slides.min.jquery.js"></script>
	<script type="text/javascript" src="./core/js/jquery.accordion.js"></script>
	<script type="text/javascript" src="./core/js/scroll.js"></script>
	<script type="text/javascript" src="./core/js/jquery.validationEngine-fr.js"></script>
	<script type="text/javascript" src="./core/js/jquery.validationEngine.js"></script>
	<script type="text/javascript" src="./core/js/easySlider1.5.js"></script>
	<script type="text/javascript" src="./core/js/disableAutoComplete.js"></script>
    <script type="text/javascript">
		//<![CDATA[
			$(document).ready(function(){
				$('#recherche_aide').css("margin-left:-9999px;");
				$('#page').hide();
				$('#page').fadeIn();
				$('#recherche_aide').hide();
				
				$("#slider").easySlider({
					auto: true,
					continuous: true 
				});

				
				$('#musee').keyup( function(){
					$field = $(this);
					$('#resultats').html('');			 
		
					if($field.val().length >= 1 ){
						$.ajax({
							type : 'POST', 
							url : './core/recherche_ajax.php' ,
							data : 'musee='+$(this).val() ,
							success : function(data){ 
								$('#resultats').html(data); 
							}
						});
					}		
				});
				
				$('#slides').slides({
					preload: true,
					preloadImage: './images/loading.gif',
					play: 5000,
					pause: 2500,
					hoverPause: true,
					animationStart: function(){
						$('.caption').animate({
							bottom:-35
						},100);
					},
					animationComplete: function(){
						$('.caption').animate({
							bottom:0
						},200);
					}
				});
				  
				$("#form_contact").validationEngine();
				$("#connexion_administration").validationEngine();
			});
					 
			function help_recherche_focus() {
				$('#recherche_aide').css("margin-left:0px;");
				$("#recherche_aide").slideDown();
			}
			
			function recherche_avancee(){
				
			}
			
			$('#question').accordion({
				header: 'div.title',
				autoheight: false
			});
		//]]>	
	</script>
</body>
</html>