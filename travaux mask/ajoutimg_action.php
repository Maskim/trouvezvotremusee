<?php
	//Testons le fichier :
	if(isset($_FILES['img-musees']) AND $_FILES['img-musees']['error']==0)
	{
		echo "fichier ok";
		if($_FILES['img-musees']['size'] <= 20000000){
			echo "taille ok, et voici le nom du fichier".$_FILES['img-musees']['name']."<br />";
			
			$infosfichier = pathinfo($_FILES['img-musees']['name']);
			echo "voici l'extension du fichier : ";
			echo $infosfichier['extension'];
			$extension_upload = $infosfichier['extension'];
			$extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png');
			if(in_array($extension_upload, $extensions_autorisees))
			{
				move_uploaded_file($_FILES['img-musees']['tmp_name'], '../images/musees/' . basename($_FILES['img-musees']['name']));
				echo "action effectuée";
			}else{
				echo "pas la bonne extension !";
			}
		}
	}else{
		echo "Il y a une erreur !";
	}
?>