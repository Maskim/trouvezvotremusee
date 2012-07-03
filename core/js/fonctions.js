function modifInfoCompte(){
	$('.error').hide();
	var login = document.getElementById('login');
	var mail = document.getElementById('mail');
	var nom = document.getElementById('nom');
	var prenom = document.getElementById('prenom');
	var lien = document.getElementById('modifier');

	login.innerHTML = '<input type="text" class="validate[required]" name="login" id="login" value="'+ login.innerHTML +'" />';
	mail.innerHTML = '<input type="text" class="validate[required,custom[email]]" name="mail" id="mail" value="'+ mail.innerHTML +'" />';
	nom.innerHTML = '<input type="text" class="validate[required]" name="nom" id="nom" value="'+ nom.innerHTML +'" />';
	prenom.innerHTML = '<input type="text" class="validate[required]" name="prenom" id="prenom" value="'+ prenom.innerHTML +'" />';
	lien.innerHTML = '<input type="submit" value="modifier" />';

	var height = $('#formModifUser').height() + 60;

	$('#info').css('height', 'auto').animate({
		height : height
	}, 'slow', 'linear');
}

function modifMDP(){
	$('.error').hide();
	$('#modifmdp').fadeToggle();
	$('#info').css('height', 'auto').animate({
		height : '170px'
	}, 'slow', 'linear');
}

$(document).ready(function(){
	$('#modifmdp').hide();
});