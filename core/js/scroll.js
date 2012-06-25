$(document).ready(function() {
	$("a.c").anchorAnimate();

	$('.zoneConnexion').click(function(){
		if($('#connexion').css("margin-top") >= '0px'){
			$('#connexion').animate({'margin-top': '-=52'}, 600);
			$('.formError').fadeOut(150,function(){		$(this).remove()	});
		}else
			$('#connexion').animate({'margin-top': '+=52'}, 600);
		return false;
	});

});

jQuery.fn.anchorAnimate = function(settings) {

 	settings = jQuery.extend({
		speed : 500
	}, settings);	
	
	return this.each(function(){
		var caller = this
		$(caller).click(function (event) {	
			event.preventDefault()
			var locationHref = window.location.href
			var elementClick = $(caller).attr("href")
			
			var destination = $(elementClick).offset().top;
			$("html:not(:animated),body:not(:animated)").animate({ scrollTop: destination}, settings.speed, function() {
				window.location.hash = elementClick
			});
		  	return false;
		})
	})
}