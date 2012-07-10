 <?php
    $theCaddie = array();

    $prob = false;

    if(isset($_POST['booking_type']) AND !empty($_POST['booking_type'])){
        $booking_type = $_POST['booking_type'];
    }
    if(isset($_POST['summ']) AND !empty($_POST['summ'])){
        $summ = $_POST['summ'];
    }
    if(isset($_POST['email']) AND !empty($_POST['email'])){
        $email = $_POST['email'];
    }
    if(isset($_POST['return']) AND !empty($_POST['return'])){
        $normal_return_url = $_POST['return'];
    }
    if(isset($_POST['cancel_return']) AND !empty($_POST['cancel_return'])){
        $cancel_return_url = $_POST['cancel_return'];
    }


    if($prob){
        print("<p>Nous sommes désolés, un problème c'est manifesté ! Nous vous prions de bien vouloir recommencer.</p>");
        echo "<p align='center'><a href='javascript:history.go(-1)'>RETOUR</a></p>";
    }else{

        $theCaddie[] = $booking_type;

        //on crée un numéro de commande
        if(isset($_POST['NumCommande']) AND !empty($_POST['NumCommande'])){
            $NumCmd = htmlspecialchars($_POST['NumCommande']);
        }
        $theCaddie[] =  $NumCmd ;

        if(isset($_POST['first_name']) AND !empty($_POST['first_name'])){
            $theCaddie[] = htmlspecialchars($_POST['first_name']);
            $first_name = $_POST['first_name'];
        }
        if(isset($_POST['last_name']) AND !empty($_POST['last_name'])){
            $theCaddie[] = htmlspecialchars($_POST['last_name']);
            $last_name = $_POST['last_name'];
        }
        if(isset($_POST['address1']) AND !empty($_POST['address1'])){
            $theCaddie[] = htmlspecialchars($_POST['address1']);
        }
        if(isset($_POST['city']) AND !empty($_POST['city'])){
            $theCaddie[] = htmlspecialchars($_POST['city']);
        }
        if(isset($_POST['country']) AND !empty($_POST['country'])){
            $theCaddie[] = htmlspecialchars($_POST['country']);
        }
        if(isset($_POST['zip']) AND !empty($_POST['zip'])){
            $theCaddie[] = htmlspecialchars($_POST['zip']);
        }
        if(isset($_POST['phone']) AND !empty($_POST['phone'])){
            $theCaddie[] = htmlspecialchars($_POST['phone']);
        }
        if(isset($_POST['nb_pers']) AND !empty($_POST['nb_pers'])){
            $theCaddie[] = htmlspecialchars($_POST['nb_pers']);
            $nb_pers = $_POST['nb_pers'];
        }
        if(isset($_POST['propriete']) AND !empty($_POST['propriete'])){
            $theCaddie[] = htmlspecialchars($_POST['propriete']);
            $propriete = $_POST['propriete'];
        }
        if(isset($_POST['heure_visite']) AND !empty($_POST['heure_visite'])){
            $theCaddie[] = htmlspecialchars($_POST['heure_visite']);
            $heure = $_POST['heure_visite'];
        }

        //Chemin du fichier pathfile + executable request
        $pathfile = "pathfile=/homez.478/winetour/cgi-bin/pathfile";
        $path_bin = '/homez.478/winetour/cgi-bin/bin/static/request';

        //id du marchand test
        //$parm = "merchant_id=014213245611111";

        //id marchant
        $parm = "merchant_id=053779919900014";

        //url de retour du client après le paiement
        $parm = "$parm normal_return_url=http://bordeaux.winetourbooking.com/merci/";

        //url en cas d'annulation
        $parm = "$parm cancel_return_url=$cancel_return_url";

        //url réponse automatique
        $parm = "$parm automatic_response_url=http://bordeaux.winetourbooking.com/autoresponse.php";

        //Le montant de la transaction
        $amount = number_format($summ,2)*100;
        $parm = "$parm amount=" .str_pad($amount,3,"0",STR_PAD_LEFT);
        //$parm = "$parm amount=100";

        //Pays du commerçant
        $parm = "$parm merchant_country=fr";

        //code pour l'euro
        $parm = "$parm currency_code=978";

        //Langage de l'interface de paiement
        $parm = "$parm language=fr";

        //Id de la transaction (6chiffres)
        $parm = "$parm transaction_id=" . date ("His");

        //Label sur le reçu de paiement
        //Ce paramètre est limité à 3072 caractères et ne doit pas contenir d'espace
        //( vous les remplacez par leur équivalent html : &nbsp;)
        $Produit = "<tr><td>WineTourBooking</td></tr>";
        $parm = "$parm receipt_complement=" . $Produit;

        //Email client (no comment)
        $parm = "$parm customer_email=" . $email;

        //IP client
        //$parm .= " customer_ip_address=" . $REMOTE_ADDR;

        //caddie
        //Ce champ est limité à 2048 caractères
        //on serrialize en un string et on l'encode pour supprimer certain caractère interdit
        $xCaddie = base64_encode(serialize($theCaddie));
        $parm = "$parm caddie=" . $xCaddie;

        //le template
        //$parm = "$parm templatefile=le_template_de_mon_site";

        //phpinfo();
        $message = escapeshellcmd($parm);
        //Appel du binaire request
        $result = exec("$path_bin $pathfile $message");

        //On separe les differents champs et on les met dans un  tableau
        $tableau = explode ("!", "$result");

        //Récupération des paramètres
        $code    = $tableau[1];
        $error   = $tableau[2];
        $message = $tableau[3];

        //Analyse du code retour

        if (( $code == "" ) && ( $error == "" ) )
        {
            //Si nous n'obtenons aucun retour de l'API c'est qu'il n'a pas été exécuté (CQFD)
            //Il s'agit la plupart du temps d'un problème dans le chemin vers le binaire request
            //Il peut s'agir d'un problème de droits : vérifiez qu'il ait bien les droits d'exécution

            print ("<center><h1>Erreur appel request</h1></center>");
            print ("<p>&nbsp;</p>");
            print ("Executable request non trouve : $path_bin");
        }


        else if ($code != 0){

            //Erreur, affiche le message d'erreur
            //Ici le binaire request a bien été exécuté mais un ou plusieurs paramètres ne sont pas valides
            //En cas de doute, n'hésitez pas à consulter le Dictionnaire des données
            
            print ("<center><h1>Erreur appel API de paiement.</h1></center>");
            print ("<p>&nbsp;</p>");
            print ("<p>Message erreur : $error </p>");
        }

        else {

            //Ici, tout est OK, on affiche les cartes bancaires
            //Comme on est des programmeurs consciencieux, on va également afficher
            //quelques infos supplémentaire pour que le client se sente en confiance
            //en lui rappellant le montant de la transaction et le numéro de commande
            //ainsi qu'un lien de retour au cas où celui-ci changerait d'avis
            ?>
                <h3>Récupitulatif de votre Commande : </h3>
                <p align='center'>
                    <strong><?php echo "$first_name $last_name"; ?></strong>, vous avez réservé une visite pour la propriété :
                    <strong><?php echo $propriete; ?></strong> pour <strong><?php echo $nb_pers; ?> personne(s)</strong> pour le 
                    <strong><?php echo $date; ?></strong> à <strong><?php echo $heure; ?></strong>.
                </p>
                <p align='center'>
                    <strong>Montant à payer : </strong> <?php echo $summ; ?>,00 Euros  - 
                    <strong>Numéro de la commande : </strong> <?php echo $NumCmd; ?>
                </p>
                
                <p>
                    <?php echo $message ?>
                </p>

                <p align='center'>
                    <a href='javascript:history.go(-1)'>RETOUR</a>
                </p>

            <?php
            //Ici on peut vérifier le contenu du parm caddie que l'on va envoyer
            //pour nous assurer que nous envoyons bien tout ce dont nous aurons besoin au retour
            //de la transaction (pour les tests ONLY !!!)
            //Décommentez les lignes ci-dessous pendant les tests
            //n'oubliez pas de les repasser en commentaire ou de les effacer ensuite
            
        }
    }
?>