$(document).ready(function() {
	$('.zone_connexion').click(function(){
		$('#connexion').animate({'margin-top': '+=52'}, 600);
		$('.zone_connexion').animate({'margin-top': '-=100'}, 600);
	});

	$('#fermer span').click(function(){
		$('#connexion').animate({'margin-top': '-=52'}, 600);
		$('.formError').fadeOut(150,function() {	$(this).remove()	});
		$('.zone_connexion').animate({'margin-top': '+=100'}, 600);
	});
});