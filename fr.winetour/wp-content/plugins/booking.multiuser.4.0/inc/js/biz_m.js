var list_booking_id_for_show = [];
var prices_per_day = [];
var cost_curency = '';


function addBKForm(param){
                    document.getElementById('bk_form_plus').style.display='none';
                    document.getElementById('bk_form_addbutton').style.display='block';
                    setTimeout(function ( ) {
                                jQuery('#booking_form_new_name').focus();
                                }
                                ,100);

}


function delete_bk_form(form_name){
                        var answer = confirm("Do you really want to delete this booking form ?");
                        if (! answer){
                            return false;
                        }
                        var wpdev_ajax_path = wpdev_bk_plugin_url+'/' + wpdev_bk_plugin_filename ;

                        //Ajax adding new type to the DB
                        document.getElementById('ajax_working').innerHTML =
                        '<div class="info_message ajax_message" id="ajax_message" >\n\
                            <div style="float:left;"> Deleting... </div> \n\
                            <div  style="float:left;width:80px;margin-top:-3px;">\n\
                                <img src="'+wpdev_bk_plugin_url+'/img/ajax-loader.gif">\n\
                            </div>\n\
                        </div>';

                        jQuery.ajax({                                           // Start Ajax Sending
                            url: wpdev_ajax_path,
                            type:'POST',
                            success: function (data, textStatus){if( textStatus == 'success')   jQuery('#ajax_respond').html( data )},
                            error:function (XMLHttpRequest, textStatus, errorThrown){window.status = 'Ajax sending Error status:'+ textStatus;alert(XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText);if (XMLHttpRequest.status == 500) {alert('Please check at this page according this error:' + ' http://wpbookingcalendar.com/faq/#ajax-sending-error');}},
                            // beforeSend: someFunction,
                            data:{
                                ajax_action : 'DELETE_BK_FORM',
                                formname : form_name
                            }
                        });
                        return false;
}

function add_bk_form() {

                    var wpdev_ajax_path = wpdev_bk_plugin_url+'/' + wpdev_bk_plugin_filename ;
                    var is_delete= false;
                    var ajax_type_action='';
                    if (is_delete) {ajax_type_action =  'DELETE_APPROVE';var ajax_bk_message = 'Deleting...';}
                    else           {ajax_type_action =  'UPDATE_APPROVE';var ajax_bk_message = 'Updating...';};

                    var type_str = document.getElementById('new_bk_form').value;
                    if (type_str == '') return;
                    document.getElementById('new_bk_form').value = '';
                    jQuery('#bk_types_line').append('<div id="last_book_type" class="bk_types">' + type_str + '</div>' + '<div id="last_book_type_separator" class="bk_types"> | </div>' );
                    document.getElementById('bk_form_plus').style.display='block';
                    document.getElementById('bk_form_addbutton').style.display='none';

                        //Ajax adding new type to the DB
                        document.getElementById('ajax_working').innerHTML =
                        '<div class="info_message ajax_message" id="ajax_message" >\n\
                            <div style="float:left;">'+ajax_bk_message+'</div> \n\
                            <div  style="float:left;width:80px;margin-top:-3px;">\n\
                                <img src="'+wpdev_bk_plugin_url+'/img/ajax-loader.gif">\n\
                            </div>\n\
                        </div>';

                        return false;

}




function changeBookingForm(selectObj){
     var idx = selectObj.selectedIndex;
     // get the value of the selected option
     var my_form = selectObj.options[idx].value;
     document.getElementById('new_booking_form').style.display = 'none';

     if (my_form == '+') {
         document.getElementById('new_booking_form').style.display = 'block';
     } else {


         var loc = location.href;
         if ( loc.indexOf('booking_form=') == -1 ) {
            loc = location.href + '&booking_form=' +my_form;}
         else { // Alredy have this paremeter at URL
             var start = loc.indexOf('&booking_form=');
             var fin = loc.indexOf('&', (start+15));
             if (fin == -1) {loc = loc.substr(0,start) + '&booking_form=' +my_form;} // at the end of row
             else { // at the middle of the row
                  var loc1 = loc.substr(0,start) + '&booking_form=' +my_form;//alert(loc)
                  loc = loc1 + loc.substr(fin);
             }
         }
         location.href = loc;


     }

}


function changeFilter(selectObj){
     var idx = selectObj.selectedIndex;
     // get the value of the selected option
     var which = selectObj.options[idx].value;
     var loc = location.href;
     if ( loc.indexOf('sybtypefilter=') == -1 ) {
        loc = location.href + '&sybtypefilter=' +which;}
     else { // Alredy have this paremeter at URL
         var start = loc.indexOf('&sybtypefilter=');
         var fin = loc.indexOf('&', (start+15));
         if (fin == -1) {loc = loc.substr(0,start) + '&sybtypefilter=' +which;} // at the end of row
         else { // at the middle of the row
              var loc1 = loc.substr(0,start) + '&sybtypefilter=' +which;//alert(loc)
              loc = loc1 + loc.substr(fin);
         }
     }
     location.href = loc;
}


function filterBookingRowsApply(){
    //  alert(list_booking_id_for_show);
    hide_bk_rows = [];
    for(var i=0; i<list_booking_id_for_show.length;i++){
        if (list_booking_id_for_show[i] == 'hide') {
            hide_bk_rows[hide_bk_rows.length] = 'booking_row'+i;
            jQuery('#booking_appr_'+ i).removeClass('booking_appr0');
            jQuery('#booking_appr_'+ i).removeClass('booking_appr1');
            jQuery('#booking_row'+i).hide();
        }
        // alert(i + '  ' + list_booking_id_for_show[i])
    }
    // alert(hide_bk_rows);
}

if (location.href.indexOf( 'sybtypefilter=') > 0 ) jQuery(document).ready(filterBookingRowsApply);

function setavailabilitycontent(contnt){
    document.getElementById('selectword').innerHTML = contnt;
}



function is_this_day_available( date, bk_type){  //TODO continue here according time

                    function in_array(what, where) {
                        var a=false;
                        for(var i=0; i<where.length; i++) {
                            if(what == where[i]) {
                                a=true;
                                break;
                            }
                        }
                        return a;
                    }

                    var filters_cnt = avalaibility_filters[ bk_type ].length;
                    var filter_week_days = [];
                    var filter_days = [];
                    var filter_monthes = [];
                    var filter_years = [];

                    var d_w = date.getDay();
                    var d_m = ( date.getMonth()+1 );
                    var d_d = date.getDate();
                    var d_y = date.getFullYear();
//alert(date+ ' ' +d_w+ ' ' +d_m+ ' ' +d_d+ ' ' +d_y);
                    var is_day_inside_filters = 0 ;

                    for(var k=0; k<filters_cnt; k++ ) {
                        filter_week_days = avalaibility_filters[ bk_type ][k][0];
                        filter_days = avalaibility_filters[ bk_type ][k][1];
                        filter_monthes = avalaibility_filters[ bk_type ][k][2];
                        filter_years = avalaibility_filters[ bk_type ][k][3];

                        is_day_inside_filters = '';
                        if ( in_array( d_w , filter_week_days ) ) {is_day_inside_filters += 'week ';}
                        if ( in_array( d_d , filter_days ) )      {is_day_inside_filters += 'day ';}
                        if ( in_array( d_m , filter_monthes ) )   {is_day_inside_filters += 'month ';}
                        if ( in_array( d_y , filter_years ) )     {is_day_inside_filters += 'year ';}

                        if (is_day_inside_filters == 'week day month year ') {break;} // the days is apply to filter (apply to week days monthes and years of filter)

                    }
//alert( is_day_inside_filters );
                    if (is_day_inside_filters == 'week day month year ') {is_day_inside_filters = true;} else {is_day_inside_filters = false;}

                    var is_this_day_available = true;

                    if (is_day_inside_filters) {
                        if ( is_all_days_available[ bk_type ] ) is_this_day_available = false;
                        else                                    is_this_day_available = true;
                    } else {
                        if ( is_all_days_available[ bk_type ] ) is_this_day_available = true;
                        else                                    is_this_day_available = false;
                    }
//alert(is_this_day_available);
                    return is_this_day_available;
}



// Time availability
function check_global_time_availability(date, bk_type){

    if (typeof( global_avalaibility_times[bk_type] ) !== 'undefined'){
/*
        var class_day = (date.getMonth()+1) + '-' + date.getDate() + '-' + date.getFullYear();
        if (typeof( date2approve[bk_type][ class_day ] ) == 'undefined'){  date2approve[bk_type][ class_day ] = []; }

        for (var i=0;i<global_avalaibility_times[bk_type].length;i++) {
            date2approve[bk_type][ class_day ][ date2approve[bk_type][class_day].length ] = [(date.getMonth()+1), date.getDate(), date.getFullYear(),
            global_avalaibility_times[bk_type][i][0][0], global_avalaibility_times[bk_type][i][0][1], 1];
            date2approve[bk_type][ class_day ][ date2approve[bk_type][class_day].length ] = [(date.getMonth()+1), date.getDate(), date.getFullYear(),
            global_avalaibility_times[bk_type][i][1][0], global_avalaibility_times[bk_type][i][1][1], 2];
        }/**/
    }

}

// Time availability
function hover_day_check_global_time_availability( date, bk_type ,times_array ) {
   new_times_array = times_array;
   if(typeof( global_avalaibility_times[ bk_type ]) !== 'undefined') {

       var td_class =  (date.getMonth()+1) + '-' + date.getDate() + '-' + date.getFullYear();

       for (var iii=0;iii<global_avalaibility_times[bk_type].length;iii++) {

            h = global_avalaibility_times[bk_type][iii][0][0];if (h < 10) h = '0' + h;if (h == 0) h = '00';
            m = global_avalaibility_times[bk_type][iii][0][1];if (m < 10) m = '0' + m;if (m == 0) m = '00';
            s = '01';
            times_array[ times_array.length ] = [h,m,s];

            h = global_avalaibility_times[bk_type][iii][1][0];if (h < 10) h = '0' + h;if (h == 0) h = '00';
            m = global_avalaibility_times[bk_type][iii][1][1];if (m < 10) m = '0' + m;if (m == 0) m = '00';
            s = '02';

            times_array[ times_array.length ] = [h,m,s];
       }

       times_array.sort();
    //   alert(times_array);
       var new_times_array=[];
       previos_is_start_or_end_time = 0;
       for (iii=0;iii<times_array.length;iii++) {
           if (( previos_is_start_or_end_time != parseInt(times_array[iii][2]) ) ) {
            previos_is_start_or_end_time = parseInt(times_array[iii][2]);
            new_times_array[new_times_array.length] = times_array[iii];
           } else if ( ( parseInt(previos_is_start_or_end_time) == 2 ) ) {
            previos_is_start_or_end_time = parseInt(times_array[iii][2]);
            new_times_array[new_times_array.length-1] = times_array[iii];
           }
       }
    //   alert(new_times_array);
   }

   return new_times_array;
}

// Time availability
// check availability of entered time by visitor according global available time only
var global_start_time_checking = false;
function check_entered_time_to_global_availability_time(mytime, is_start_time, bk_type){

       if ( typeof(global_avalaibility_times[bk_type]) !== 'undefined' ) {
     //      mytime
           var times_array = [ mytime.split(":")  ];
           times_array[0][2] = 'check';

           if (is_start_time) {global_start_time_checking = times_array[0];global_start_time_checking[2] = 'starttime';}
           else {times_array[1] = [global_start_time_checking[0],global_start_time_checking[1],global_start_time_checking[2]];global_start_time_checking = false;}

           for (var iii=0;iii<global_avalaibility_times[bk_type].length;iii++) {
                //Start time
                h = global_avalaibility_times[bk_type][iii][0][0];if (h < 10) h = '0' + h;if (h == 0) h = '00';
                m = global_avalaibility_times[bk_type][iii][0][1];if (m < 10) m = '0' + m;if (m == 0) m = '00';
                times_array[ times_array.length ] = [h,m,true];
                //End Time
                h = global_avalaibility_times[bk_type][iii][1][0];if (h < 10) h = '0' + h;if (h == 0) h = '00';
                m = global_avalaibility_times[bk_type][iii][1][1];if (m < 10) m = '0' + m;if (m == 0) m = '00';
                times_array[ times_array.length ] = [h,m,false];
           }
           times_array.sort();

           var is_previos_time_start = false;
           for ( iii=0;iii<times_array.length;iii++) {
               if (is_start_time) { // START TIME
                   if (times_array[iii][2] == 'check') {
                        // check  here
                        if (is_previos_time_start) return false; // Wrong Time
                        else                       return true; // Good  Time
                   }
               } else { // END TIME //during the same day
                   if (times_array[iii][2] == 'check') {
                        // check  here
                        if (is_previos_time_start == 'starttime') return true; // Good
                        else                                      return false; // Wrong
                   }
               }
               is_previos_time_start = times_array[iii][2];
           }
       }
       return true; // Good  Time
}



function getDayPrice4Show(bk_type, tooltip_time, td_class){

    if (is_show_cost_in_tooltips) {

       if(typeof(  prices_per_day[bk_type] ) !== 'undefined')
           if(typeof(  prices_per_day[bk_type][td_class] ) !== 'undefined') {
                if (tooltip_time!== '') tooltip_time = tooltip_time + '<br/>';
                return  tooltip_time + cost_curency + prices_per_day[bk_type][td_class] ;

           }

    }

    return  tooltip_time   ;

}



// Admin panel - Add additional row with cost, which is depends from number of selected days
function addRowForCustomizationCostDependsFromNumSellDays(row__id) {
   jQuery('#cost_days_row_help'+row__id ).html( getRowForCustomizationCostDependsFromNumSellDays(row__id)) ;
}
function getRowForCustomizationCostDependsFromNumSellDays(row__id) {
    return '<select name="cost_apply_to'+row__id+'" id="cost_apply_to'+row__id+'" style="width:220px;padding:3px 1px 1px 1px !important;" >\n\
     <option value="fixed">'+bk_cost_depends_from_selection_line1+'</option>\n\
     <option value="%">'+bk_cost_depends_from_selection_line2+'</option>\n\
     <option value="add">'+bk_cost_depends_from_selection_line3+'</option>\n\
     </select>';
}

function addRowForCustomizationCostDependsFromNumSellDays4Summ(row__id) {
   jQuery('#cost_days_row_help'+row__id ).html( getRowForCustomizationCostDependsFromNumSellDays4Summ(row__id)) ;
}
function getRowForCustomizationCostDependsFromNumSellDays4Summ(row__id) {
    return '<select name="cost_apply_to'+row__id+'" id="cost_apply_to'+row__id+'" style="width:220px;padding:3px 1px 1px 1px !important;" >\n\
     <option value="fixed">'+bk_cost_depends_from_selection_line14summ+'</option>\n\
     <option value="%">'+bk_cost_depends_from_selection_line24summ+'</option>\n\
     </select>';
}



function getBookingFormElements(bk_type){

        var submit_form = document.getElementById('booking_form' +  bk_type );
        var formdata = '';

        if (submit_form != null) {
                var count = submit_form.elements.length;
                var inp_value;
                var element;
                var el_type;
                // Serialize form here
                for (i=0; i<count; i++)   {
                    element = submit_form.elements[i];

                    if ( (element.type !=='button') && (element.type !=='hidden') && ( element.name !== ('date_booking' + bk_type) )   ) {           // Skip buttons and hidden element - type

                        if (element.type !=='checkbox') {inp_value = element.value;}                      // if checkbox so then just check checked
                        else                            {
                            if (element.value == '') inp_value = element.checked;
                            else {
                                if (element.checked) inp_value = element.value;
                                else inp_value = '';
                            }
                        }

                        if ( element.name !== ('captcha_input' + bk_type) ) {
                            if (formdata !=='') formdata +=  '~';                                                // next field element

                            el_type = element.type
                            if ( element.className.indexOf('wpdev-validates-as-email') !== -1 )  el_type='email';
                            if ( element.className.indexOf('wpdev-validates-as-coupon') !== -1 ) el_type='coupon';

                            formdata +=  el_type + '^' + element.name + '^' + inp_value ;                    // element attr
                        }
                    }
                }
        }
        return formdata;

}


function showCostHintInsideBkForm( bk_type ){

            if (document.getElementById('parent_of_additional_calendar' + bk_type) != null) { // Its mean that we get cost hint clicking at additional calendar
                bk_type = document.getElementById('parent_of_additional_calendar' + bk_type).value; // Get parent bk type from additional calendar
            }

            if (document.getElementById('booking_hint' + bk_type) == null) return false;


            var all_dates = jQuery('#date_booking' + bk_type).val();
            var formdata  = getBookingFormElements(bk_type);

            // if user select a date so need to show calculation in process
            document.getElementById('booking_hint' + bk_type).innerHTML =
                '<span style=""><img style="vertical-align:middle;" src="'+wpdev_bk_plugin_url+'/img/ajax-loader.gif"><//span>';


            if (document.getElementById('additional_cost_hint' + bk_type) !== null)
                document.getElementById('additional_cost_hint' + bk_type).innerHTML =
                    '<span style=""><img style="vertical-align:middle;" src="'+wpdev_bk_plugin_url+'/img/ajax-loader.gif"><//span>';

            if (document.getElementById('original_booking_hint' + bk_type) !== null)
                document.getElementById('original_booking_hint' + bk_type).innerHTML =
                    '<span style=""><img style="vertical-align:middle;" src="'+wpdev_bk_plugin_url+'/img/ajax-loader.gif"><//span>';


            var wpdev_ajax_path = wpdev_bk_plugin_url+'/' + wpdev_bk_plugin_filename ;
            var ajax_type_action='CALCULATE_THE_COST';
            var my_booking_form='';
            if (document.getElementById('booking_form_type' + bk_type) != undefined)
                my_booking_form =document.getElementById('booking_form_type' + bk_type).value;


            jQuery.ajax({                                           // Start Ajax Sending
                url: wpdev_ajax_path,
                type:'POST',
                success: function (data, textStatus){if( textStatus == 'success')   jQuery('#ajax_respond_insert' + bk_type).html( data ) ;},
                error:function (XMLHttpRequest, textStatus, errorThrown){window.status = 'Ajax sending Error status:'+ textStatus;alert(XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText);if (XMLHttpRequest.status == 500) {alert('Please check at this page according this error:' + ' http://wpbookingcalendar.com/faq/#ajax-sending-error');}},
                // beforeSend: someFunction,
                data:{
                    ajax_action : ajax_type_action,
                    form: formdata,
                    all_dates : all_dates,
                    bk_type : bk_type,
                    booking_form_type:my_booking_form
                }
            });

            return false;

}