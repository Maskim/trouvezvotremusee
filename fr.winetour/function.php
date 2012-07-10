<?php 
function findAttenteForm($form, $sizeBooking){
    $curseur = curseurOfString($form, $sizeBooking, 'amateur');
    $finCurseur = endCurseurOfString($curseur, $form);
    if($finCurseur == 1){
        $curseur = curseurOfString($form, $sizeBooking, 'ludique');
        $finCurseur = endCurseurOfString($curseur, $form);

        if($finCurseur == 1){
            $curseur = curseurOfString($form, $sizeBooking, 'decouverte');
            $finCurseur = endCurseurOfString($curseur, $form);

            if ($finCurseur == 1) {
                return '';
            }else{
                return substr($form, $curseur, $finCurseur-1);
            }
        }else{
            return substr($form, $curseur, $finCurseur-1);
        }
    }else{
        if(strlen(substr($form, $curseur, $finCurseur-1)) > 1){
            return substr($form, $curseur, $finCurseur-1);
        }
        else{
            return "";
        }
    }

    return "";
}

function findConnaitForm($form, $sizeBooking){
    $curseur = curseurOfString($form, $sizeBooking, 'connais');
    $finCurseur = endCurseurOfString($curseur, $form);
    if($finCurseur == 1){
        $curseur = curseurOfString($form, $sizeBooking, 'adore');
        $finCurseur = endCurseurOfString($curseur, $form);

        if($finCurseur == 1){
            $curseur = curseurOfString($form, $sizeBooking, 'curieux');
            $finCurseur = endCurseurOfString($curseur, $form);

            if ($finCurseur == 1) {
                return '';
            }else{
                return substr($form, $curseur, $finCurseur-1);
            }
        }else{
            return substr($form, $curseur, $finCurseur-1);
        }
    }else{
        if(strlen(substr($form, $curseur, $finCurseur-1)) > 1){
            return substr($form, $curseur, $finCurseur-1);
        }
        else{
            return "";
        }
    }

    return "";
}

function curseurOfString($form, $sizeBooking, $string){
    return (stripos($form, $string) + strlen($string) +$sizeBooking + 3);
}

function endCurseurOfString($curseur, $form){
    $lettre = substr($form, $curseur, 1);
    $i = 1;

    $curseur_temp = $curseur;

    while ($lettre != "~") {

        $curseur_temp++;
        $lettre = substr($form, $curseur_temp, 1);
        $i++;
    }

    return $i;
}

function isAnglais($form, $sizeBooking){
    $curseur = curseurOfString($form, $sizeBooking, 'anglais');
    $finCurseur = endCurseurOfString($curseur, $form);
    if($finCurseur == 1)
        return false;

    return true;
}

?>