jQuery(document).ready( function(){   
   if( jQuery('.wpdev-validates-as-time').length > 0 ) {
       jQuery('.wpdev-validates-as-time').attr('alt','time');
       jQuery('.wpdev-validates-as-time').setMask();
   }

   jQuery('a.poplight').click(function() {

        idbooking = bktype;

        if(!verifChampPopup(idbooking)){
            return false;
        }

        document.getElementById('booking_form' + idbooking).submit();
    });

    //Fermeture de la pop-up et du fond
    jQuery('a.close, #fade').live('click', function() { //Au clic sur le bouton ou sur le calque...
        jQuery('#fade , .popup_block').fadeOut(function() {
            jQuery('#fade, a.close').remove();  //...ils disparaissent ensemble
        });
        return false;
    });

    jQuery('input.close, #fade').live('click', function() { //Au clic sur le bouton ou sur le calque...
        jQuery('#fade , .popup_block').fadeOut(function() {
            jQuery('#fade, a.close').remove();  //...ils disparaissent ensemble
        });
    });
});

function showErrorTimeMessage(my_message, element){
            var element_name = element.name
            jQuery("[name='"+ element_name +"']")
                    .css( {'border' : '1px solid red'} )
                    .fadeOut( 350 )
                    .fadeIn( 500 )
                    .animate( {opacity: 1}, 4000 )
                    .animate({border : '1px solid #DFDFDF'},100)
            ;  // mark red border
            jQuery("[name='"+ element_name +"']")
                    .after('<div class="wpdev-help-message">'+ my_message +'</div>'); // Show message
            jQuery(".wpdev-help-message")
                    .css( {'color' : 'red'} )
                    .animate( {opacity: 1}, 10000 )
                    .fadeOut( 2000 );   // hide message
            element.focus();    // make focus to elemnt
            return true;
}


function isValidTimeTextField(timeStr) {
        // Checks if time is in HH:MM AM/PM format.
        // The seconds and AM/PM are optional.

        var timePat = /^(\d{1,2}):(\d{2})(\s?(AM|am|PM|pm))?$/;

        var matchArray = timeStr.match(timePat);
        if (matchArray == null) {
            return false; //("<?php _e('Time is not in a valid format. Use this format HH:MM or HH:MM AM/PM'); ?>");
        }
        var hour = matchArray[1];
        var minute = matchArray[2];
        var ampm = matchArray[4];

        if (ampm=="") {ampm = null}

        if (hour < 0  || hour > 23) {
            return  false; //("<?php _e('Hour must be between 1 and 12. (or 0 and 23 for military time)'); ?>");
        }
        if  (hour > 12 && ampm != null) {
            return  false; //("<?php _e('You can not specify AM or PM for military time.'); ?>");
        }
        if (minute<0 || minute > 59) {
            return  false; //("<?php _e('Minute must be between 0 and 59.'); ?>");
        }
        return true;
    }


function is_this_time_selections_not_available(bk_type,  form_elements ) {
    
    var count = form_elements.length;
    var start_time = false;
    var end_time   = false;
    var element; var element_start=false; var element_end=false; var element_duration=false; var element_rangetime=false;
    var duration = false;


    // Get Start and End time from this form, if they exist.
    for (var i=0; i<count; i++)   {

        element = form_elements[i];

        if (element.name.indexOf('rangetime') !== -1 ){                         // Range - time selectbox
               var my_rangetime = element.value.split('-');
               start_time = my_rangetime[0].replace(/(^\s+)|(\s+$)/g, ""); // TRim
               end_time   = my_rangetime[1].replace(/(^\s+)|(\s+$)/g, ""); // TRim
               element_rangetime  = element;
        }

        if ( (element.name.indexOf('durationtime') !== -1 )   ){                // Duration
                duration = element.value;
                element_duration = element;
        }

        if (element.name.indexOf('starttime') !== -1 ) {                        // Start Time
                start_time    = element.value;
                element_start = element;
        }

        if (element.name.indexOf('endtime') !== -1 )   {                        // End Time
                end_time     =  element.value;
                element_end  = element;
        }

    } // End form elemnts loop





    // Duration get Values
    if ( (duration !== false) && (start_time !== false) ) {  // we have Duration and Start time so  try to get End time

        var mylocalstarttime = start_time.split(':');
        var d = new Date(1980, 1, 1, mylocalstarttime[0], mylocalstarttime[1], 0);

        var my_duration = duration.split(':');
        my_duration = my_duration[0]*60*60*1000 + my_duration[1]*60*1000;
        d.setTime(d.getTime() + my_duration);

        var my_hours   = d.getHours();   if (my_hours < 10)   my_hours =   '0' + ( my_hours + '' );
        var my_minutes = d.getMinutes(); if (my_minutes < 10) my_minutes = '0' + ( my_minutes + '' );

        // We are get end time
        end_time = ( my_hours + '' ) + ':' + ( my_minutes + '' ) ;
    }



    if ( (start_time === false) || (end_time === false) ) {                     // We do not have Start or End time or Both of them, so do not check it

           return false ;

    } else {

           var valid_time = true;
           if ( (start_time == '') || (end_time == '') ) valid_time = false;

           if (! isValidTimeTextField(start_time) )  valid_time = false;
           if (! isValidTimeTextField(end_time  ) )  valid_time = false;

           if( valid_time === true )
               if (
                     ( typeof( checkRecurentTimeInside ) == 'function' )  &&
                     (typeof( is_booking_recurrent_time) !== 'undefined') &&
                     (is_booking_recurrent_time == true)
                   ) {                                                                // Recheck Time here !!!
                       valid_time = checkRecurentTimeInside( [ start_time , end_time ],  bk_type );
               } else {

                       if( typeof( checkTimeInside ) == 'function' ) { valid_time = checkTimeInside( start_time , true, bk_type) ; }

                       if( valid_time === true ) {
                           if(typeof( checkTimeInside ) == 'function') {valid_time = checkTimeInside( end_time , false, bk_type) ;}
                       }
               }

           if( valid_time !== true ) {


               if (element_rangetime !== false ) showErrorTimeMessage(message_rangetime_error,    element_rangetime);
               if (element_duration !== false )  showErrorTimeMessage(message_durationtime_error, element_duration);
               if (element_start !== false )     showErrorTimeMessage(message_starttime_error,    element_start);
               if (element_end !== false )       showErrorTimeMessage(message_endtime_error,      element_end);

               return true;
               
           } else  {
               return false;
           }

    }


}


function wpdev_add_remark(id, text){
    document.getElementById("remark_row" + id ).style.display="none";

    var ajax_bk_message = 'Adding remark...';

    document.getElementById('ajax_working').innerHTML =
    '<div class="info_message ajax_message" id="ajax_message">\n\
        <div style="float:left;">'+ajax_bk_message+'</div> \n\
        <div  style="float:left;width:80px;margin-top:-3px;">\n\
               <img src="'+wpdev_bk_plugin_url+'/img/ajax-loader.gif">\n\
        </div>\n\
    </div>';

    var wpdev_ajax_path = wpdev_bk_plugin_url+'/' + wpdev_bk_plugin_filename ;
    
    jQuery.ajax({                                           // Start Ajax Sending
        url: wpdev_ajax_path,
        type:'POST',
        success: function (data, textStatus){if( textStatus == 'success')   jQuery('#ajax_respond').html( data );},
        error:function (XMLHttpRequest, textStatus, errorThrown){ window.status = 'Ajax sending Error status:'+ textStatus; alert(XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText); if (XMLHttpRequest.status == 500) { alert('Please check at this page according this error:' + ' http://wpbookingcalendar.com/faq/#ajax-sending-error'); } },
        // beforeSend: someFunction,
        data:{
            ajax_action : 'UPDATE_REMARK',
            remark_id : id,
            remark_text : text
        }
    });
    return false;

}


function wpdev_change_bk_resource (booking_id, resource_id){
    document.getElementById("changing_bk_res_in_booking" + booking_id ).style.display="none";

    var ajax_bk_message = 'Changing resource...';

    document.getElementById('ajax_working').innerHTML =
    '<div class="info_message ajax_message" id="ajax_message">\n\
        <div style="float:left;">'+ajax_bk_message+'</div> \n\
        <div  style="float:left;width:80px;margin-top:-3px;">\n\
               <img src="'+wpdev_bk_plugin_url+'/img/ajax-loader.gif">\n\
        </div>\n\
    </div>';

    var wpdev_ajax_path = wpdev_bk_plugin_url+'/' + wpdev_bk_plugin_filename ;

    jQuery.ajax({                                           // Start Ajax Sending
        url: wpdev_ajax_path,
        type:'POST',
        success: function (data, textStatus){if( textStatus == 'success')   jQuery('#ajax_respond').html( data );},
        error:function (XMLHttpRequest, textStatus, errorThrown){ window.status = 'Ajax sending Error status:'+ textStatus; alert(XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText); if (XMLHttpRequest.status == 500) { alert('Please check at this page according this error:' + ' http://wpbookingcalendar.com/faq/#ajax-sending-error'); } },
        // beforeSend: someFunction,
        data:{
            ajax_action : 'UPDATE_BK_RESOURCE_4_BOOKING',
            booking_id : booking_id,
            resource_id : resource_id
        }
    });
    return false;

}


//Print
function print_booking_listing(){
    jQuery("#print_loyout_content").html( jQuery("#booking_print_loyout").html()  ) ;
    jQuery("#printLoyoutModal").modal("show");
}

jQuery.fn.print = function(){
	// NOTE: We are trimming the jQuery collection down to the
	// first element in the collection.
	if (this.size() > 1){
		this.eq( 0 ).print();
		return;
	} else if (!this.size()){
		return;
	}

	// ASSERT: At this point, we know that the current jQuery
	// collection (as defined by THIS), contains only one
	// printable element.

	// Create a random name for the print frame.
	var strFrameName = ("printer-" + (new Date()).getTime());

	// Create an iFrame with the new name.
	var jFrame = jQuery( "<iframe name='" + strFrameName + "'>" );

	// Hide the frame (sort of) and attach to the body.
	jFrame
		.css( "width", "1px" )
		.css( "height", "1px" )
		.css( "position", "absolute" )
		.css( "left", "-9999px" )
		.appendTo( jQuery( "body:first" ) )
	;

	// Get a FRAMES reference to the new frame.
	var objFrame = window.frames[ strFrameName ];

	// Get a reference to the DOM in the new frame.
	var objDoc = objFrame.document;

	// Grab all the style tags and copy to the new
	// document so that we capture look and feel of
	// the current document.

	// Create a temp document DIV to hold the style tags.
	// This is the only way I could find to get the style
	// tags into IE.
	var jStyleDiv = jQuery( "<div>" ).append(
		jQuery( "style" ).clone()
		);

	// Write the HTML for the document. In this, we will
	// write out the HTML of the current element.
	objDoc.open();
	objDoc.write( "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">" );
	objDoc.write( "<html>" );

	objDoc.write( "<head>" );
	objDoc.write( "<title>" );
	objDoc.write( document.title );
	objDoc.write( "</title>" );

        // objDoc.write( jStyleDiv.html() );
        objDoc.write(  "<link href='" + wpdev_bk_plugin_url + "/interface/bs/css/bs.min.css' rel='stylesheet' type='text/css' />" );
        objDoc.write(  "<link href='" + wpdev_bk_plugin_url + "/css/admin.css' rel='stylesheet' type='text/css' />" );

	objDoc.write( "</head>" );
        objDoc.write( "<body>" );
	objDoc.write( this.html() );
	objDoc.write( "</body>" );
	objDoc.write( "</html>" );
	objDoc.close();

	// Print the document.
	objFrame.focus();
	objFrame.print();

	// Have the frame remove itself in about a minute so that
	// we don't build up too many of these frames.
	setTimeout(
		function(){
			jFrame.remove();
		},
		(60 * 1000)
		);
}


// Export 
var csv_content;
//<![CDATA[
function export_booking_listing(export_type){

        var wpdev_ajax_path     = wpdev_bk_plugin_url+'/' + wpdev_bk_plugin_filename ;
        var ajax_type_action    = 'EXPORT_BOOKINGS_TO_CSV';
        var ajax_bk_message     = 'Start exporting...';
        var bk_request_params     = document.getElementById('bk_request_params').value;




            document.getElementById('ajax_working').innerHTML =
            '<div class="info_message ajax_message" id="ajax_message">\n\
                <div style="float:left;">'+ajax_bk_message+'</div> \n\
                <div  style="float:left;width:80px;margin-top:-3px;">\n\
                       <img src="'+wpdev_bk_plugin_url+'/img/ajax-loader.gif">\n\
                </div>\n\
            </div>';


            // Ajax POST here
            jQuery.ajax({                                           // Start Ajax Sending
                url: wpdev_ajax_path,
                type:'POST',
                success: function (data, textStatus){  if( textStatus == 'success')   jQuery('#ajax_respond').html( data );},
                error:function (XMLHttpRequest, textStatus, errorThrown){ window.status = 'Ajax sending Error status:'+ textStatus; alert(XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText); if (XMLHttpRequest.status == 500) { alert('Please check at this page according this error:' + ' http://wpbookingcalendar.com/faq/#ajax-sending-error'); } },
                // beforeSend: someFunction,
                data:{
                    ajax_action : ajax_type_action,
                    csv_data:bk_request_params,
                    export_type:export_type
                }
            });
}
//]]>

//MODIF WTB MAXIME 25/06/2012 ajout fonction verification champ pour poppup
function verifChampPopup(booking_id){
    var submit_form = document.getElementById('booking_form' + booking_id);

    //Show message if no selected days
        if (document.getElementById('date_booking' + booking_id).value == '')  {

            if ( document.getElementById('additional_calendars' + booking_id) != null ) { // Checking according additional calendars.

                var id_additional_str = document.getElementById('additional_calendars' + booking_id).value; //Loop have to be here based on , sign
                var id_additional_arr = id_additional_str.split(',');
                var is_all_additional_days_unselected = true;
                for (var ia=0;ia<id_additional_arr.length;ia++) {
                    if (document.getElementById('date_booking' + id_additional_arr[ia] ).value != '' ) {
                        is_all_additional_days_unselected = false;
                    }
                }

                if (is_all_additional_days_unselected) {
                    alert(message_verif_selectdts);
                    return;
                }

            } else {
                alert(message_verif_selectdts);
                return;
            }
        }

    ////////////////////////////////////// Modification by WTB //////////////////
    var starttime_samedi = document.getElementById("starttime_samedi" + booking_id) || 0;

    //nb_personne verify
    if(!starttime_samedi){
        if(document.getElementById("starttime" + booking_id).value == " "){
            alert('Vous n\'avez pas selectionné l\' heure de votre visite');
            return;
        }
    }else{
        if((document.getElementById("starttime" + booking_id).value == " " || document.getElementById("starttime" + booking_id).value == '') && (document.getElementById("starttime_samedi" + booking_id).value == " " || document.getElementById("starttime_samedi" + booking_id).value == '')){
            alert('Vous n\'avez pas selectionné l\' heure de votre visite !');
            return;
        }else if(document.getElementById("starttime" + booking_id).value != ' ' && document.getElementById("starttime_samedi" + booking_id).value != ' '){
            alert('Vous ne pouvez pas choisir un horaire pour la semaine et le samedi.');
            return;
        }
    }

    //nombre de visiteur verify
    var test_visitors = document.getElementById("visitors") || 0;
    if(test_visitors){
        var visitors = document.getElementById("visitors" + booking_id).value;
        if(visitors == ""){
            alert('Vous n\'avez pas selectionné le nombre de personne de votre visite');
            return;
        }

        var selectedDay = document.getElementById('date_booking' + booking_id).value;
        var selectedDay = transformDate(selectedDay);
        var selectedHour = document.getElementById('starttime' + booking_id).value;

        var dateReservation = selectedDay + " " + selectedHour;
        var nbVisitorSelected = document.getElementById('visitors' + booking_id).value;

        if(Visiteur[dateReservation] != ''){
            var nombreDeVisiteurRestant = MaxVisitor - Visiteur[dateReservation];
            if(nombreDeVisiteurRestant <= nbVisitorSelected){
                alert('Nous sommes désolé, mais l\'horaire que vous avez selectionnez est complet ou ne dispose pas de place suffisante pour vous acceuillir... Veuillez selectionner un nouvel horaire s\'il vous plait');
                return;
            }
        }
    }

    return true;
}

