jQuery(function($) {
	var jQversion = jQuery.fn.jquery;
	jQversion = jQversion.replace(/\./g,'');
	if (jQversion > 114) {
	
	 $(document).ready(function(){
	 	// Prepare the panel of spacers type
		var element = $('#caticonspanel')[0];
		if (element) {
			if ($("#radioimage:checked").val() == 'image') 
				$("#caticonspanel").slideDown("fast");
			else 
				$("#caticonspanel").slideUp("fast");
			$("input[type=radio][name=igcaticons_spacerstype]").change(function(){
				if ($('input[type=radio][name=igcaticons_spacerstype]:checked').attr('value') == 'image') {
					$("#caticonspanel").slideDown("fast");
				} else {
					$("#caticonspanel").slideUp("fast");
				}
			});
		}
		
		// Sort table 
		$("#caticons_table").tablesorter({ 
			// pass the headers argument and assing a object 
			headers: { 
				// assign the secound column (we start counting zero) 
				0: { 
					// disable it by setting the property sorter to false 
					sorter: false 
				}
			} 
		});

		// use load() method to make an Ajax request 
		if ($('#caticons_loading').length >0 && jQversion >= 126 ) { // wordpress 2.7+
    		$('#caticons_loading').load( CatIconsSettings.plugin_url + '/category_icons_feed.php', { error : CatIconsSettings.error } ); 
    	}	
    	
	});
	} else {
		$(document).ready(function(){
		if ($("#radioimage:checked").val() == 'image') // jQuery 1.1.4 in order to be compatible with WordPress 2.3
			$("#caticonspanel").slideDown("fast");
		else 
			$("#caticonspanel").slideUp("fast");
		
		$("input[@name='igcaticons_spacerstype'][@value='image']").click(function(){
			$("#caticonspanel").slideDown("fast");
			return true;
		});
		
		$("input[@name='igcaticons_spacerstype'][@value='css']").click(function(){
			$("#caticonspanel").slideUp("fast");
			return true;
		});
	});
	}
});

