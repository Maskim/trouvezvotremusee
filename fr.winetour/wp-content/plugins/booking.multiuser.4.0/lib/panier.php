<?php

class Panier{

	private $count;
	private $panier;

	function Panier(){
		$this->setPanier( $this->recupPanier() );
		$this->setCount( $this->recupCount() );
		wp_enqueue_script('panier', './wp-content/plugins/booking.multiuser.4.0/lib/js/panier.js', '', true);
	}

	//GET/SETTEUR
	function getCount(){
		return $this->count;
	}

	function setCount($newCount){
		$this->count = $newCount;

		setcookie('countPanier', $this->count, time() + (3600 * 24 * 7), '/');
	}

	function getPanier(){
		return $this->panier;
	}

	function setPanier($newPanier){
		$this->panier = $newPanier;
	}

	//Method
	function affiche(){
		$panier = $this->getPanier();
		$panier = $this->ordonnePanier($panier);
		$last = "";
		$first = true;
		?>
		<div id="sousTotal">
			<p class="total">Sous total ( <?php echo $this->getCount(); ?> article(s) ) : <?php echo $this->getTotalPanier(); ?> €</p>
			<p><a href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/commande/' ?>">Passer la commande</a></p>
		</div>
		<div id="panier">
			<?php
				for($i = 0;$i < count($panier); $i++){
					$cost = $this->getCost($panier[$i][0], $panier[$i][4]);
					$lien = 'http://'. $_SERVER['HTTP_HOST'] . '/place/' . $this->getIdPropriete($id);
					if($last != $panier[$i][2]){
					?>
						<?php if($first){ ?>
							<div id="entree" class="entree<?php echo $i; ?>">
							<?php $first = false;
						}else{ ?> 
							</div><div id="entree" class="entree<?php echo $i; ?>">
						<?php } ?>
						<div class="date">
							<h3>Le <?php $this->setFormatDate($panier[$i][2]); ?></h3>
						</div>
					<?php } ?>
						<div class="visite">
							<p class="image"><?php echo $this->imagePropriete($panier[$i][0]); ?></p>
							<div class="info">
								<p class="prix"><?php echo $cost. ' €'; ?></p>
								<p class="propriete"><a href="<?php echo $lien; ?>"><?php $this->getPropriete($panier[$i][0]); ?></a></p>
								<p>Visite en <?php $this->setLangue($panier[$i][1]); ?> à <?php echo $panier[$i][3]; ?> pour <?php echo $panier[$i][4]; ?> personne(s).</p>
								<form method="POST" name="supprimer" id="supprimer<?php echo $i; ?>" action="http://<?php echo $_SERVER['HTTP_HOST']; ?>/panier">
									<input type="hidden" name="id_cookie" value="<?php echo $i; ?>" />
									<input type="hidden" name="type" value="supprimer" />
									<p><a href="#" onClick="removeFromPanier(<?php echo $i; ?>);">Supprimer</a></p>
								</form>
							</div>
						</div>
					<?php $last = $panier[$i][2];
				}
			?>
				</div>
				<div id="footerPanier">
					<p class="total">Total articles TTC : <?php echo $this->getTotalPanier(); ?> €</p>
				</div>
			</div>
		<?php
	}

	function recupPanier(){
		if (isset($_COOKIE['panier']) && $_COOKIE['panier'] != "") {
			$panier = $_COOKIE['panier'];
			$panier = explode("~", $panier);

	        for($i = 0; $i < count($panier); $i++)
	        {
	            $panier[$i] = explode("---", $panier[$i]);
	        }
	    }else{
	    	$panier = "";
	    	setcookie('panier', "", time() - 3600);
	    	setcookie('countPanier', "", time() - 3600);
	    }

        return $panier;
	}

	function recupCount(){
		if ($_COOKIE['countPanier'] && $_COOKIE['panier'] != "") {
			$count = $_COOKIE['countPanier'];
			if($count != count($this->panier))
	    		$count = count($this->panier);
	    }else{
	    	$count = 0;
	    }

        return $count;
	}

	function ordonnePanier($panier){
		for($i = 0; $i < count($panier); $i++){
			for($j = $i; $j < count($panier); $j++){
				if($this->setFormatForTri($panier[$i][2]) > $this->setFormatForTri($panier[$j][2]) ){
					$temp = $panier[$i];
					$panier[$i] = $panier[$j];
					$panier[$j] = $temp;
				}
			}
		}

		return $panier;
	}

	function setFormatForTri($date){
		$date = substr($date, 4, 4) . substr($date, 2, 2) . substr($date, 0, 2);

		return $date;
	}

	function panierExist(){
		if($this->panier != "" && $this->count != 0)
			return true;
		else
			return false;
	}

	function addToPanier(){
		$booking_id = $_POST['booking_type'];
		$date = $_POST['date_booking'.$booking_id];
		$heure = $_POST['starttime'.$booking_id];
		$visitor = $_POST['visitors'.$booking_id];
		$numCommande = $_POST['NumCommande'.$booking_id];

		if(isset($_POST['anglais'.$booking_id]))
			$anglais = 'on';
		else
			$anglais = 'off';

		$date = str_replace('.', '', $date);

		$panier = $booking_id . '---' . $anglais . '---' . $date . '---' . $heure . '---' . $visitor . '---' . $numCommande;
		$this->updateCookie($panier, $booking_id, $date, $heure, $numCommande);
		$this->addCommande($booking_id, $anglais, $date, $heure, $visitor, $numCommande);

		$this->afficheAddPanier();
	}

	function addCommande($booking_id, $anglais, $date, $heure, $visitor, $numCommande){
		$commande[] = $booking_id;
		$commande[] = $anglais;
		$commande[] = $date;
		$commande[] = $heure;
		$commande[] = $visitor;
		$commande[] = $numCommande;

		$panier = $this->getPanier();
		$panier[] = $commande;

		$this->setPanier($panier);
	}

	function updateCookie($panier, $booking_type, $date, $heure, $numCommande){
		if(isset($_COOKIE['panier']) && !empty($_COOKIE['panier'])){

			$count = $_COOKIE['countPanier'];
			$count++;

			$this->setCount($count);

			$cookie = $this->getPanier();
			for($i = 0; $i < count($cookie); $i++){
				if($numCommande == $cookie[$i][5]){
					echo 'fail';
					return;
				}else{
					if($booking_id = $cookie[$i][0] && $date == $cookie[$i][2] && $heure == $cookie[$i][3]){
						$cookie[$i][3] = $cookie[$i][3] + $visitor;
					}
				}
			}

			$panier = $this->reconstructionPanier($cookie, $panier);
		}else{
			$count = 1;
			$this->setCount($count);
		}

		setcookie('panier', $panier, time() + (3600 * 24 * 7), '/');
		setcookie('countPanier', $count, time() + (3600 * 24 * 7), '/');
	}

	function afficheAddPanier(){
		$id = $_POST['booking_type'];

		$visitor = $_POST['visitors'.$id];
		$heure = $_POST['starttime'.$id];
		$date = $_POST['date_booking'.$id];

		$date = str_replace('.', '', $date);

		$cost = $this->getCost($id, $visitor);
		$cost = str_replace('.', ',', $cost);

		$lien = 'http://'. $_SERVER['HTTP_HOST'] . '/place/' . $this->getIdPropriete($id);

		$total = $this->getTotalPanier();

		$nbArticle = $this->getCount();

		if($nbArticle > 1)
			$s = 's';
		else
			$s = '';
		?>
			<h2>La Visite a bien été ajoutée à votre panier.</h2>

			<div id="recapDemande">
				<div class="panier">
					<p class="titre"><strong>Sous-total de la commande :</strong></p>
					<p class="totalPanier"><?php echo $total; ?> EUR</p>
					<p class="infoPanier"><?php echo $nbArticle; ?> article<?php echo $s; ?> dans votre panier</p>
					<a href="<?php echo 'http://'. $_SERVER['HTTP_HOST'] . '/'; ?>">Continuer votre visite</a>
					<a href="<?php echo 'http://'. $_SERVER['HTTP_HOST'] . '/panier'?>">Passer la commande</a>
				</div>
				<div class="article">
					<p class="image"><?php echo $this->imagePropriete($_POST['booking_type']); ?></p>
					<p class="propriete"><a href="<?php echo $lien;?>"><?php $this->getPropriete($id); ?></a></p>
					<p class="info"><?php echo $cost. " EUR"; ?> pour <?php echo $visitor?> personne(s).</p>
					<p class="date">le <?php $this->setFormatDate($date); ?> à <?php echo $heure; ?></p>
				</div>
			</div>

			</div>
			<script type="text/javascript">
				var countPanier = document.getElementById('countPanier');
				if(countPanier.innerHTML != '<?php echo ' : ' . $this->getCount(); ?>')
					countPanier.innerHTML = ' : <?php echo $this->getCount(); ?>';
			</script>
		<?php
			$this -> afficheSuggestion($id);
	}

	function afficheSuggestion($id){
		$id_post = $this->getIdPost($id);

		$terms = get_the_terms($id_post, 'placecategory');
		$id_term = $terms[0]->term_id;

		$place = $this->findPlaceByTerm($id_term, $id_post);
		$bookingtypes = $this->getBookingTypeFromPlace($place);

		?>
		<div class="related_listing">
			<h3>Les propriétés en relation avec votre visite : </h3>
			<ul>
				<?php
					for ($i=0; $i < count($bookingtypes); $i++) { 
						$this->getPubFromBookingType($bookingtypes[$i]);
					}
				?>	
			</ul>
		</div>
		<?php
	}

	function getPubFromBookingType($id){
		global $wpdb;

		$prefix = $wpdb->prefix;
		$requete = "SELECT * FROM ".$prefix."bookingtypes WHERE booking_type_id = '$id'";
		$result = $wpdb->get_results($requete);

		$lien = 'http://'. $_SERVER['HTTP_HOST'] . '/place/' . $this->getIdPropriete($id);

		?>
			<li class="clearfix">
				<a class="post_img" href="<?php echo $lien; ?>">
					<?php echo $this->imagePropriete($id); ?>
				</a>
				<h3><a href="<?php echo $lien;?>"><?php echo $result[0]->title; ?></a></h3>
				<p><?php echo $this->getResumePropriete($id); ?></p>
				<p class="review clearfix">
					<a class="read_more" href="<?php echo $lien; ?>">Lire la suite</a>
				</p>
			</li>
		<?php
	}

	function getIdPost($id){
		global $wpdb;

		$prefix = $wpdb->prefix;
		$requete = "SELECT id_place FROM ".$prefix."bookingtypes WHERE booking_type_id = '".$id."'";
		$result = $wpdb->get_results($requete);

		return $result[0]->id_place;
	}

	function findPlaceByTerm($id_term, $current){
		global $wpdb;

		$prefix = $wpdb->prefix;

		$where = "WHERE ".$prefix."term_taxonomy.term_taxonomy_id = ".$prefix."term_relationships.term_taxonomy_id AND ".$prefix."term_taxonomy.term_id = ".$prefix."terms.term_id AND ".$prefix."terms.term_id = '$id_term' AND object_id != '$current'";
		$requete = "SELECT object_id FROM ".$prefix."terms, ".$prefix."term_relationships, ".$prefix."term_taxonomy $where LIMIT 3";
		$result = $wpdb->get_results($requete);

		return $result;
	}

	function getBookingTypeFromPlace($place){
		global $wpdb;

		$return;

		$prefix = $wpdb->prefix;
		for($i = 0 ; $i < count($place); $i++){
			$requete = "SELECT booking_type_id as id FROM ".$prefix."bookingtypes WHERE id_place = '".$place[$i]->object_id."' ";
			$result = $wpdb->get_results($requete);
			$return[] = $result[0]->id;
		}

		return $return;
			
	}

	function getPropriete($idprop){
		global $wpdb;

		$prefix = $wpdb->prefix;
		$requete = "SELECT title FROM ".$prefix."bookingtypes WHERE booking_type_id = '".$idprop."'";
		$result = $wpdb->get_results($requete);

		echo $result[0]->title;
	}

	function setFormatDate($date){
		$date = substr($date, 0, 2) . ' ' . replaceMonthNumberToString(substr($date, 2, 2)) . ' ' . substr($date, 4, 4);

		echo $date;
	}

	function setLangue($langue){
		if ($langue == 'on') {
			echo 'Anglais';
		}else{
			echo 'Français';
		}
	}

	function imagePropriete($booking_type){
		global $wpdb;

		$prefix = $wpdb->prefix;
		$requete = "SELECT guid, post_mime_type FROM ".$prefix."posts, ".$prefix."bookingtypes WHERE (ID = id_place OR post_parent = id_place) AND booking_type_id = '".$booking_type."' AND post_type= 'attachment'";

		$result = $wpdb->get_results($requete);
		if(!empty( $result[0]->post_mime_type)){
			$ext = str_replace('image/', '.', $result[0]->post_mime_type);
			$ext = str_replace('jpeg', 'jpg', $ext);
			$lien = explode($ext, $result[0]->guid);
			$lien = $lien[0] . '-150x105' . $ext;

			$lien = '<img src="'.$lien.'" alt="test" />';
		}else{
			$lien = "";
		}

		return $lien;
	}

	function getCost($id, $nbPers){
		global $wpdb;

		$prefix = $wpdb->prefix;
		$requete = "SELECT cost FROM ".$prefix."bookingtypes WHERE booking_type_id='".$id."'";
		$result = $wpdb->get_results($requete);

		$cost = $nbPers * ($result[0]->cost);

		return $cost;
	}

	function reconstructionPanier($tableau, $addpanier){
		$return = "";
		for($i = 0; $i < count($tableau); $i++){
			if($i != 0)
				$return .=  "~";

			$return .= $tableau[$i][0] . '---' . $tableau[$i][1] . '---' . $tableau[$i][2] . '---' . $tableau[$i][3] . '---' . $tableau[$i][4] . '---' . $tableau[$i][5];
		}

		if($addpanier != "")
			$return .= "~" . $addpanier;

		return $return; 
	}

	function getIdPropriete($booking_id){
		global $wpdb;

		$prefix = $wpdb->prefix;
		$requete = "SELECT id_place FROM ".$prefix."bookingtypes WHERE booking_type_id = '".$booking_id."'";
		$result = $wpdb->get_results($requete);

		$id_place = $result[0]->id_place;

		$requete = "SELECT post_name FROM " .$prefix. "posts WHERE ID = '".$id_place."'";
		$result = $wpdb->get_results($requete);

		return $result[0]->post_name;
	}

	function getResumePropriete($id){
		global $wpdb;

		$prefix = $wpdb->prefix;
		$requete = "SELECT id_place FROM ".$prefix."bookingtypes WHERE booking_type_id = '".$id."'";
		$result = $wpdb->get_results($requete);

		$id_place = $result[0]->id_place;

		$requete = "SELECT post_excerpt FROM " .$prefix. "posts WHERE ID = '".$id_place."'";
		$result = $wpdb->get_results($requete);

		return $result[0]->post_excerpt;
	}

	function getTotalPanier(){
		$panier = $this->getPanier();

		$total = 0;

		for($i = 0; $i < count($panier); $i++){
			$total = $total + $this->getCost($panier[$i][0], $panier[$i][4]);
		}

		return $total;
	}

	function getNbArticle(){
		return $_COOKIE['countPanier'];
	}

	function removeArticle($id){
		$panier = $this->getPanier();
		if(isset($panier[$id])){
			unset($panier[$id]);
			$panier = array_values($panier);


			$this->count --;
			$this->setPanier($panier);

			setcookie('panier', $this->reconstructionPanier($this->panier, ""), time() + (3600 * 24 * 7), '/');
			setcookie('countPanier', $this->count, time() + (3600 * 24 * 7), '/');

			?>
			<script type="text/javascript">
				var countPanier = document.getElementById('countPanier');
				if(countPanier.innerHTML != '<?php echo ' : ' . $this->getCount(); ?>'){
					if($this->getCount() != 0)
						countPanier.innerHTML = ' : <?php echo $this->getCount(); ?>';
					else
						countPanier.innerHTML = '';
				}
			</script>
			<?php
		}
	}
}

?>