//Customization of bufer time for DAN
var time_buffer_value = 0;

// Highlighting range days at calendar
var td_mouse_over = '';
var payment_request_id = 0;




// Check is this day booked or no
function is_this_day_booked(bk_type, td_class, i){ // is is not obligatory parameter

    if ( ( jQuery('#calendar_booking'+bk_type+' .cal4date-' + td_class).hasClass('date_user_unavailable') ) || ( jQuery('#calendar_booking'+bk_type+' .cal4date-' + td_class).hasClass('datepick-unselectable') )){ // If we find some unselect option so then make no selection at all in this range
                     document.body.style.cursor = 'default';return true;
    }

    //Check if in selection range are reserved days, if so then do not make selection
    if(typeof(date_approved[ bk_type ]) !== 'undefined')
        if(typeof(date_approved[ bk_type ][ td_class ]) !== 'undefined') { //alert(date_approved[ bk_type ][ td_class ][0][5]);
              for (var j=0; j < date_approved[ bk_type ][ td_class ].length ; j++) {
                    if ( ( date_approved[ bk_type ][ td_class ][j][3] == 0) &&  ( date_approved[ bk_type ][ td_class ][j][4] == 0) )  {document.body.style.cursor = 'default';return true;}
                    if ( ( (date_approved[ bk_type ][ td_class ][j][5] * 1) == 2 ) && (i!=0)) {document.body.style.cursor = 'default';return true;}
              }
        }

    if(typeof( date2approve[ bk_type ]) !== 'undefined')
        if(typeof( date2approve[ bk_type ][ td_class ]) !== 'undefined') {
              for ( j=0; j < date2approve[ bk_type ][ td_class ].length ; j++) {
                    if ( ( date2approve[ bk_type ][ td_class ][j][3] == 0) &&  ( date2approve[ bk_type ][ td_class ][j][4] == 0) )  {document.body.style.cursor = 'default';return true;}
                    if ( ( (date2approve[ bk_type ][ td_class ][j][5] * 1) == 2 ) && (i!=0)) {document.body.style.cursor = 'default';return true;}
              }
        }

    return false;
}



function hoverDayPro(value, date, bk_type) {

    if (date == null) return;

    var i=0 ;var j=0;
    var td_class;
    var td_overs = new Array();
    var td_element=0;

    if (is_select_range == 1) {
        if ( date == null) {return;}

        jQuery('.datepick-days-cell-over').removeClass('datepick-days-cell-over');                          // clear all selections
        if (range_start_day != -1) {
            if (date.getDay() !=  range_start_day) {
                date.setDate(date.getDate() -  ( date.getDay() -  range_start_day )  );
            }
        }
        for( i=0; i < days_select_count ; i++) {
            td_class =  (date.getMonth()+1) + '-' + date.getDate() + '-' + date.getFullYear();

            if (   is_this_day_booked(bk_type, td_class, i)   ) return ;   // check if day is booked

            td_overs[td_overs.length] = '#calendar_booking'+bk_type+ ' .cal4date-' + td_class;              // add to array for later make selection by class
            date.setDate(date.getDate() + 1);                                                               // Add 1 day to current day
        }

        for ( i=0; i < td_overs.length ; i++) {                                                             // add class to all elements
            td_element = jQuery( td_overs[i] );
            td_element.addClass('datepick-days-cell-over');
        }
        return ;
    }



    if ( wpdev_bk_is_dynamic_range_selection ) {
        if ( date == null) {return;}
        jQuery('.datepick-days-cell-over').removeClass('datepick-days-cell-over');                          // clear all highlight days selections

        var inst = jQuery.datepick._getInst(document.getElementById('calendar_booking'+bk_type));

        if ( (inst.dates.length == 0) || (inst.dates.length>1)  ) {  // Initial HIGHLIGHTING days in Dynamic range selection mode depends from start day and minimum numbers of days
            var selceted_first_day = new Date();
            selceted_first_day.setFullYear(date.getFullYear(),(date.getMonth()), (date.getDate() ) );
            if (range_start_day_dynamic != -1) {
                if (date.getDay() !=  range_start_day_dynamic) {
                    selceted_first_day.setDate(date.getDate() -  ( date.getDay() -  range_start_day_dynamic )  );
                }
            }i=0;
            while(    ( i < days_select_count_dynamic ) ) {
               i++;
               td_class =  (selceted_first_day.getMonth()+1) + '-' + selceted_first_day.getDate() + '-' + selceted_first_day.getFullYear();
               if (   is_this_day_booked(bk_type, td_class, (i-1))   ) return ;   // check if day is booked
               td_overs[td_overs.length] = '#calendar_booking'+bk_type+ ' .cal4date-' + td_class;              // add to array for later make selection by class
               selceted_first_day.setFullYear(selceted_first_day.getFullYear(),(selceted_first_day.getMonth()), (selceted_first_day.getDate() + 1) );
            }
        }

        // First click on days
        if (inst.dates.length == 1) {  // select start date in Dynamic range selection, after first days is selected
            var selceted_first_day = new Date();
            selceted_first_day.setFullYear(inst.dates[0].getFullYear(),(inst.dates[0].getMonth()), (inst.dates[0].getDate() ) ); //Get first Date

            var is_check = true;
            i=0;
            while(  (is_check ) || ( i < days_select_count_dynamic ) ) { // Untill rich MIN days number.
               i++;
               td_class =  (selceted_first_day.getMonth()+1) + '-' + selceted_first_day.getDate() + '-' + selceted_first_day.getFullYear();

                if (   is_this_day_booked(bk_type, td_class, (i-1))   ) return ;   // check if day is booked

                td_overs[td_overs.length] = '#calendar_booking'+bk_type+ ' .cal4date-' + td_class;              // add to array for later make selection by class


                var is_discreet_ok = true;
                if (bk_discreet_days_in_range_slections.length>0) {              // check if we set some discreet dates
                    is_discreet_ok = false;
                    for (var di = 0; di < bk_discreet_days_in_range_slections.length; di++) {   // check if current number of days inside of discreet one
                         if ( (  i % bk_discreet_days_in_range_slections[di] ) == 0 ) {
                             is_discreet_ok = true;
                             di = (bk_discreet_days_in_range_slections.length + 1);
                         }
                    }
                }

                if (   ( date.getMonth() == selceted_first_day.getMonth() )  &&
                       ( date.getDate() == selceted_first_day.getDate() )  &&
                       ( date.getFullYear() == selceted_first_day.getFullYear() )  && ( is_discreet_ok )  )
                {is_check =  false;}

                if ((selceted_first_day > date ) && ( i >= days_select_count_dynamic ) && ( i < bk_max_days_in_range_slections )  && (is_discreet_ok)  )   {
                    is_check =  false;
                }
                if ( i >= bk_max_days_in_range_slections ) is_check =  false;
                selceted_first_day.setFullYear(selceted_first_day.getFullYear(),(selceted_first_day.getMonth()), (selceted_first_day.getDate() + 1) );
            }
        }
        for ( i=0; i < td_overs.length ; i++) {                                                             // add class to all elements
            td_element = jQuery( td_overs[i] );
            td_element.addClass('datepick-days-cell-over');
        }
        return ;
    }



}

// select a day
function selectDayPro(all_dates,   bk_type){

    // Help with range selection
    selectDayPro_rangeSelection(all_dates,   bk_type);

    //Calculate the cost and show inside of form
    if(typeof( showCostHintInsideBkForm ) == 'function') {  showCostHintInsideBkForm(   bk_type); }

}


// Check if this IE and get version of IE otherwise setversion of IE to 0
var isIE_4_bk = (navigator.appName=="Microsoft Internet Explorer");
var IEversion_4_bk = navigator.appVersion;
if(isIE_4_bk) { IEversion_4_bk = parseInt(IEversion_4_bk.substr(IEversion_4_bk.indexOf("MSIE")+4));
} else { IEversion_4_bk = 0; }


// Make range select
function selectDayPro_rangeSelection(all_dates,   bk_type){

     if(typeof( prepare_tooltip ) == 'function') {setTimeout("prepare_tooltip("+bk_type+");",1000);}

     var inst = jQuery.datepick._getInst(document.getElementById('calendar_booking'+bk_type));
     var td_class;

     if ((is_select_range == 1) || (wpdev_bk_is_dynamic_range_selection == true) ) {  // Start range selections checking

        var internal_days_select_count = days_select_count;

        if ( all_dates.indexOf(' - ') != -1 ){                  // Dynamic selections
            var start_end_date = all_dates.split(" - ");

            var is_dynamic_startdayequal_to_last = true;
            if (inst.dates.length>1){
                if (is_select_range == 0) { // Dinamic
                    is_dynamic_startdayequal_to_last = false;
                }
            }


            if ( ( start_end_date[0] == start_end_date[1] ) && (is_dynamic_startdayequal_to_last ===true)   ) {    // First click at day
              if (range_start_day_dynamic != -1) {             // Activated some specific week day start range selectiosn
                    var start_dynamic_date = start_end_date[0].split(".");
                    var real_start_dynamic_date=new Date();
                    real_start_dynamic_date.setFullYear( start_dynamic_date[2],  start_dynamic_date[1]-1,  start_dynamic_date[0] );    // get date of click

                    if (real_start_dynamic_date.getDay() !=  range_start_day_dynamic) {
                                real_start_dynamic_date.setDate(real_start_dynamic_date.getDate() -  ( real_start_dynamic_date.getDay() -  range_start_day_dynamic )  );

                                all_dates = jQuery.datepick._formatDate(inst, real_start_dynamic_date );
                                all_dates += ' - ' + all_dates ;
                                jQuery('#date_booking' + bk_type).val(all_dates); // Fill the input box

                                // check this day for already booked
                                var selceted_first_day = new Date;
                                selceted_first_day.setFullYear(real_start_dynamic_date.getFullYear(),(real_start_dynamic_date.getMonth()), (real_start_dynamic_date.getDate() + 1) );
                                i=0;
                                while(    ( i < days_select_count_dynamic ) ) {
                                   i++;
                                   td_class =  (selceted_first_day.getMonth()+1) + '-' + selceted_first_day.getDate() + '-' + selceted_first_day.getFullYear();
                                   if (   is_this_day_booked(bk_type, td_class, (i))   ) {
                                               inst.dates=[];
                                               jQuery.datepick._updateDatepick(inst);
                                               return false;   // check if day is booked
                                   }
                                   selceted_first_day.setFullYear(selceted_first_day.getFullYear(),(selceted_first_day.getMonth()), (selceted_first_day.getDate() + 1) );
                                }

                                // Selection of the day
                                inst.cursorDate.setFullYear(real_start_dynamic_date.getFullYear(),(real_start_dynamic_date.getMonth()), (real_start_dynamic_date.getDate() ) );
                                inst.dates=[inst.cursorDate];
                                jQuery.datepick._updateDatepick(inst);
                     }
              } else { // Set correct date, if only single date is selected, and possible press send button then.
                                var start_dynamic_date = start_end_date[0].split(".");
                                var real_start_dynamic_date=new Date();
                                real_start_dynamic_date.setFullYear( start_dynamic_date[2],  start_dynamic_date[1]-1,  start_dynamic_date[0] );    // get date of click
                                inst.cursorDate.setFullYear(real_start_dynamic_date.getFullYear(),(real_start_dynamic_date.getMonth()), (real_start_dynamic_date.getDate() ) );
                                inst.dates=[inst.cursorDate];
                                jQuery.datepick._updateDatepick(inst);
                                jQuery('#date_booking' + bk_type).val(start_end_date[0]);
              }

              jQuery('#booking_form_div'+bk_type+' input[type="button"]').attr('disabled', 'disabled'); // Disbale the submit button
              submit_bk_color = jQuery('#booking_form_div'+bk_type+' input[type="button"]').css('color');
              jQuery('#booking_form_div'+bk_type+' input[type="button"]').css('color', '#aaa');

              return false;
            } else {  // Last day click

                    jQuery('#booking_form_div'+bk_type+' input[type="button"]').removeAttr('disabled');  // Activate the submit button
                    jQuery('#booking_form_div'+bk_type+' input[type="button"]').css('color',  submit_bk_color );

                    var start_dynamic_date = start_end_date[0].split(".");
                    var real_start_dynamic_date=new Date();
                    real_start_dynamic_date.setFullYear( start_dynamic_date[2],  start_dynamic_date[1]-1,  start_dynamic_date[0] );    // get date

                    var end_dynamic_date = start_end_date[1].split(".");
                    var real_end_dynamic_date=new Date();
                    real_end_dynamic_date.setFullYear( end_dynamic_date[2],  end_dynamic_date[1]-1,  end_dynamic_date[0] );    // get date

                    internal_days_select_count = 2; // need to count how many days right now

                    var temp_date_for_count = new Date();

                    for( var j1=1; j1 < 365 ; j1++) {
                        temp_date_for_count = new Date();
                        temp_date_for_count.setFullYear(real_start_dynamic_date.getFullYear(),(real_start_dynamic_date.getMonth()), (real_start_dynamic_date.getDate() + j1) );

                        if ( (temp_date_for_count.getFullYear() == real_end_dynamic_date.getFullYear()) && (temp_date_for_count.getMonth() == real_end_dynamic_date.getMonth()) && (temp_date_for_count.getDate() == real_end_dynamic_date.getDate()) )  {
                            internal_days_select_count = j1;
                            j1=1000;
                        }
                    }
                    internal_days_select_count++;
                    all_dates =  start_end_date[0];
                    if (internal_days_select_count < days_select_count_dynamic ) internal_days_select_count = days_select_count_dynamic;

                    var is_backward_direction = false;
                    if (bk_discreet_days_in_range_slections.length>0) {              // check if we set some discreet dates

                        var is_discreet_ok = false;
                        while (  is_discreet_ok === false ) {

                            for (var di = 0; di < bk_discreet_days_in_range_slections.length; di++) {   // check if current number of days inside of discreet one
                                 if ( 
                                    ( (  internal_days_select_count % bk_discreet_days_in_range_slections[di] ) == 0 ) &&
                                      (internal_days_select_count <= bk_max_days_in_range_slections) ) {
                                     is_discreet_ok = true;
                                     di = (bk_discreet_days_in_range_slections.length + 1);
                                 }
                            }
                            if (is_backward_direction === false)
                                if (  is_discreet_ok === false )
                                    internal_days_select_count++;


                            // BackWard directions, if we set more than maximum days
                            if (internal_days_select_count >= bk_max_days_in_range_slections) is_backward_direction = true;

                            if (is_backward_direction === true)
                                if (  is_discreet_ok === false )
                                    internal_days_select_count--;

                            if (internal_days_select_count < days_select_count_dynamic )  is_discreet_ok = true;

                        }

                    } else {
                        if (internal_days_select_count > bk_max_days_in_range_slections) internal_days_select_count = bk_max_days_in_range_slections;
                    }

                    
            }
        } // And Range selections checking


         var temp_is_select_range = is_select_range;
         is_select_range = 0;
         var temp_wpdev_bk_is_dynamic_range_selection = wpdev_bk_is_dynamic_range_selection;
         wpdev_bk_is_dynamic_range_selection = false;



        inst.dates = [];                                        // Emty dates in datepicker
        var all_dates_array;
        var date_array;
        var date;
        var date_to_ins;

        // Get array of dates
        if ( all_dates.indexOf(',') == -1 ) {all_dates_array = [all_dates];}
        else                                {all_dates_array = all_dates.split(",");}

        var original_array = [];
        var isMakeSelection = false;

        if (! temp_wpdev_bk_is_dynamic_range_selection ) {
                // Gathering original (already selected dates) date array
                for( var j=0; j < all_dates_array.length ; j++) {                           //loop array of dates
                    all_dates_array[j] = all_dates_array[j].replace(/(^\s+)|(\s+$)/g, "");  // trim white spaces in date string

                    date_array = all_dates_array[j].split(".");                             // get single date array

                    date=new Date();
                    date.setFullYear( date_array[2],  date_array[1]-1,  date_array[0] );    // get date

                    if ( (date.getFullYear() == inst.cursorDate.getFullYear()) && (date.getMonth() == inst.cursorDate.getMonth()) && (date.getDate() == inst.cursorDate.getDate()) )  {
                        isMakeSelection = true;
                                if (range_start_day != -1) {
                                    if (inst.cursorDate.getDay() !=  range_start_day) {
                                        inst.cursorDate.setDate(inst.cursorDate.getDate() -  ( inst.cursorDate.getDay() -  range_start_day )  );
                                    }
                                }
                    }
                    //original_array.push( jQuery.datepick._restrictMinMax(inst, jQuery.datepick._determineDate(inst, date, null))  ); //add date
                }
        } else {
            // dynamic range selection
            isMakeSelection = true;
        }
        var isEmptySelection = false;
        if (isMakeSelection) {
                    var date_start_range = inst.cursorDate;

                    if (! temp_wpdev_bk_is_dynamic_range_selection ) {
                        original_array.push( jQuery.datepick._restrictMinMax(inst, jQuery.datepick._determineDate(inst, inst.cursorDate , null))  ); //add date
                    } else {
                        original_array.push( jQuery.datepick._restrictMinMax(inst, jQuery.datepick._determineDate(inst, real_start_dynamic_date , null))  ); //set 1st date from dynamic range
                        date_start_range = real_start_dynamic_date;
                    }
                    var dates_array = [];
                    var range_array = [];
                    var td;
                    // Add dates to the range array
                    for( var i=1; i < internal_days_select_count ; i++) {

                        dates_array[i] = new Date();
                        // dates_array[i].setDate( (date_start_range.getDate() + i) );

                        dates_array[i].setFullYear(date_start_range.getFullYear(),(date_start_range.getMonth()), (date_start_range.getDate() + i) );

                        td_class =  (dates_array[i].getMonth()+1) + '-'  +  dates_array[i].getDate() + '-' + dates_array[i].getFullYear();
                        td =  '#calendar_booking'+bk_type+' .cal4date-' + td_class;
                         if (jQuery(td).hasClass('datepick-unselectable') ){ // If we find some unselect option so then make no selection at all in this range
                             isEmptySelection = true;
                        }

                        //Check if in selection range are reserved days, if so then do not make selection
                        if (   is_this_day_booked(bk_type, td_class, i)   ) isEmptySelection = true;
                        /////////////////////////////////////////////////////////////////////////////////////

                        date_to_ins =  jQuery.datepick._restrictMinMax(inst, jQuery.datepick._determineDate(inst, dates_array[i], null));

                        range_array.push( date_to_ins );
                    }

                    // check if some dates are the same in the arrays so the remove them from both
                    for( i=0; i < range_array.length ; i++) {
                        for( j=0; j < original_array.length ; j++) {       //loop array of dates

                        if ( (original_array[j] != -1) && (range_array[i] != -1) )
                            if ( (range_array[i].getFullYear() == original_array[j].getFullYear()) && (range_array[i].getMonth() == original_array[j].getMonth()) && (range_array[i].getDate() == original_array[j].getDate()) )  {
                                range_array[i] = -1;
                                original_array[j] = -1;
                            }
                        }
                    }

                    // Add to the dates array
                    for( j=0; j < original_array.length ; j++) {       //loop array of dates
                            if (original_array[j] != -1) inst.dates.push(original_array[j]);
                    }
                    for( i=0; i < range_array.length ; i++) {
                            if (range_array[i] != -1) inst.dates.push(range_array[i]);
                    }
        }
        if (! isEmptySelection) isEmptySelection = checkIfSomeDaysUnavailable(inst.dates, bk_type);
        if (isEmptySelection) inst.dates=[];

        //jQuery.datepick._setDate(inst, dates_array);
        if (! temp_wpdev_bk_is_dynamic_range_selection ) {
            jQuery.datepick._updateInput('#calendar_booking'+bk_type);
        } else {
           if (isEmptySelection) jQuery.datepick._updateInput('#calendar_booking'+bk_type);
           else {       // Dynamic range selections, transform days from jQuery.datepick
                       dateStr = (inst.dates.length == 0 ? '' : jQuery.datepick._formatDate(inst, inst.dates[0])); // Get first date
                        for ( i = 1; i < inst.dates.length; i++)
                             dateStr += jQuery.datepick._get(inst, 'multiSeparator') +  jQuery.datepick._formatDate(inst, inst.dates[i]);  // Gathering all dates
                        jQuery('#date_booking' + bk_type).val(dateStr); // Fill the input box
           }
        }
        jQuery.datepick._notifyChange(inst);
        jQuery.datepick._adjustInstDate(inst);
        jQuery.datepick._showDate(inst);
        //jQuery.datepick._updateDatepick(inst);
         wpdev_bk_is_dynamic_range_selection = temp_wpdev_bk_is_dynamic_range_selection;
         is_select_range =temp_is_select_range;

     } else { // HERE WE WILL DISABLE ALL OPTIONS IN RANGE TIME INTERVALS FOR SINGLE DAYS SELECTIONS FOR THAT DAYS WHERE HOURS ALREADY BOOKED
         //here is not range selections
         if (multiple_day_selections == 0){   // Only single day selections here
//alert(all_dates); alert(   bk_type);
                var current_single_day_selections  = all_dates.split('.');
                td_class =  (current_single_day_selections[1]*1) + '-' + (current_single_day_selections[0]*1) + '-' + (current_single_day_selections[2]*1);
                var times_array = [];

                jQuery('select[name="rangetime' + bk_type + '"] option:disabled').removeAttr('disabled');  // Make active all times

               if ( jQuery('select[name="rangetime' + bk_type + '"]').length == 0 ) return;  // WE DO NOT HAVE RANGE SELECTIONS AT THIS FORM SO JUST RETURN

               var range_time_object = jQuery('select[name="rangetime' + bk_type + '"] option:first' ) ;
               if (range_time_object == undefined) return;  // WE DO NOT HAVE RANGE SELECTIONS AT THIS FORM SO JUST RETURN

               // Get dates and time from aproved dates
               if(typeof(date_approved[ bk_type ]) !== 'undefined')
               if(typeof(date_approved[ bk_type ][ td_class ]) !== 'undefined') {
                 if( ( date_approved[ bk_type ][ td_class ][0][3] != 0) ||  ( date_approved[ bk_type ][ td_class ][0][4] != 0) ) {
                     for ( i=0; i< date_approved[ bk_type ][ td_class ].length; i++){
                        h = date_approved[ bk_type ][ td_class ][i][3];if (h < 10) h = '0' + h;if (h == 0) h = '00';
                        m = date_approved[ bk_type ][ td_class ][i][4];if (m < 10) m = '0' + m;if (m == 0) m = '00';
                        s = date_approved[ bk_type ][ td_class ][i][5];if (s == 2) s = '02';
                        times_array[ times_array.length ] = [h,m,s];
                     }
                 }
               }

               // Get dates and time from pending dates
               if(typeof( date2approve[ bk_type ]) !== 'undefined')
               if(typeof( date2approve[ bk_type ][ td_class ]) !== 'undefined')
                 if( ( date2approve[ bk_type ][ td_class ][0][3] != 0) ||  ( date2approve[ bk_type ][ td_class ][0][4] != 0) ) //check for time here
                   {for ( i=0; i< date2approve[ bk_type ][ td_class ].length; i++){
                        h = date2approve[ bk_type ][ td_class ][i][3];if (h < 10) h = '0' + h;if (h == 0) h = '00';
                        m = date2approve[ bk_type ][ td_class ][i][4];if (m < 10) m = '0' + m;if (m == 0) m = '00';
                        s = date2approve[ bk_type ][ td_class ][i][5];if (s == 2) s = '02';
                        times_array[ times_array.length ] = [h,m,s];
                      }
                   }
/**/


                       var removed_time_slots = is_time_slot_booked_for_this_time_array( bk_type, times_array );

                       var my_time_value = jQuery('select[name="rangetime' + bk_type + '"] option');

                       for ( j=0; j< my_time_value.length; j++){
                           if (  wpdev_in_array( removed_time_slots, j ) ) {
                               jQuery('select[name="rangetime' + bk_type + '"] option:eq('+j+')').attr('disabled', 'disabled'); // Make disable some options
                               if(  jQuery('select[name="rangetime' + bk_type + '"] option:eq('+j+')').attr('selected')  ){  // iF THIS ELEMENT IS SELECTED SO REMOVE IT FROM THIS TIME
                                   jQuery('select[name="rangetime' + bk_type + '"] option:eq('+j+')').removeAttr('selected');

                                   if (IEversion_4_bk == 7) { // Emulate disabling option in selectboxes for IE7 - its set selected option, which is not disabled
                                        set_selected_first_not_disabled_option_IE7(document.getElementsByName("rangetime" + bk_type )[0] );
                                   }
                               }

                           }
                       }
                       
                       if (IEversion_4_bk == 7) { // Emulate disabling option in selectboxes for IE7 - its set grayed text options, which is disabled
                           emulate_disabled_options_to_gray_IE7( document.getElementsByName("rangetime" + bk_type )[0] );
                       }

         }
     }

 }



function checkIfSomeDaysUnavailable(selected_dates, bk_type) {

    var i, j, td_class;

    for ( j=0; j< selected_dates.length; j++){
         // Check among availbaility filters
         if (typeof( is_this_day_available ) == 'function') {
            var is_day_available = is_this_day_available( selected_dates[j], bk_type);
            if (! is_day_available) {return true;}
        }

       td_class =  (selected_dates[j].getMonth()+1) + '-' + selected_dates[j].getDate() + '-' + selected_dates[j].getFullYear();

       // Get dates and time from pending dates
       if(typeof( date2approve[ bk_type ]) !== 'undefined')
       if(typeof( date2approve[ bk_type ][ td_class ]) !== 'undefined')
         if( ( date2approve[ bk_type ][ td_class ][0][3] == 0) &&  ( date2approve[ bk_type ][ td_class ][0][4] == 0) ) //check for time here
               {return true;} // day fully booked

       // Get dates and time from aproved dates
       if(typeof(date_approved[ bk_type ]) !== 'undefined')
       if(typeof(date_approved[ bk_type ][ td_class ]) !== 'undefined')
         if( ( date_approved[ bk_type ][ td_class ][0][3] == 0) &&  ( date_approved[ bk_type ][ td_class ][0][4] == 0) )
               {return true;} // day fully booked

    }

    return  false;
}


// IE7 select box emulate functions for disabling select boxes:
if (IEversion_4_bk == 7) {

            window.onload = function() {
                    if (document.getElementsByTagName) {
                            var s = document.getElementsByTagName("select");

                            if (s.length > 0) {
                                    window.select_current = new Array();

                                    for (var i=0, select; select = s[i]; i++) {
                                            select.onfocus = function(){ window.select_current[this.id] = this.selectedIndex; }
                                            select.onchange = function(){ set_selected_previos_selected_option_IE7(this); }
                                            emulate_disabled_options_to_gray_IE7(select);
                                    }
                            }
                    }
            }

            function set_selected_previos_selected_option_IE7(e) {
                    if (e.options[e.selectedIndex].disabled) {
                            e.selectedIndex = window.select_current[e.id];
                    }
            }

            function set_selected_first_not_disabled_option_IE7(e) {

                    if (e.options[e.selectedIndex].disabled) {
                        for (var i=0, option; option = e.options[i]; i++) {
                                if (! option.disabled) {
                                    e.selectedIndex = i;
                                    return 0;
                                }
                        }
                    }
                    return 0;
            }

            function emulate_disabled_options_to_gray_IE7(e) {
                    for (var i=0, option; option = e.options[i]; i++) {

                            if (option.disabled) { option.style.color = "graytext";}
                            else {                 option.style.color = "menutext";}
                    }
            }
}



// Times

function isDayFullByTime(bk_type, td_class ) {

   var times_array = [];

   // Get dates and time from aproved dates
   if(typeof(date_approved[ bk_type ]) !== 'undefined')
   if(typeof(date_approved[ bk_type ][ td_class ]) !== 'undefined') {
      for ( i=0; i< date_approved[ bk_type ][ td_class ].length; i++){
         if( ( date_approved[ bk_type ][ td_class ][0][3] != 0) ||  ( date_approved[ bk_type ][ td_class ][0][4] != 0) ) {
            h = date_approved[ bk_type ][ td_class ][i][3];if (h < 10) h = '0' + h;if (h == 0) h = '00';
            m = date_approved[ bk_type ][ td_class ][i][4];if (m < 10) m = '0' + m;if (m == 0) m = '00';
            s = date_approved[ bk_type ][ td_class ][i][5];if (s == 2) s = '02';
            times_array[ times_array.length ] = [h,m,s];
         }
     }
   }

   // Get dates and time from pending dates
   if(typeof( date2approve[ bk_type ]) !== 'undefined')
   if(typeof( date2approve[ bk_type ][ td_class ]) !== 'undefined')
      for ( i=0; i< date2approve[ bk_type ][ td_class ].length; i++){
        if( ( date2approve[ bk_type ][ td_class ][0][3] != 0) ||  ( date2approve[ bk_type ][ td_class ][0][4] != 0) ) {
            h = date2approve[ bk_type ][ td_class ][i][3];if (h < 10) h = '0' + h;if (h == 0) h = '00';
            m = date2approve[ bk_type ][ td_class ][i][4];if (m < 10) m = '0' + m;if (m == 0) m = '00';
            s = date2approve[ bk_type ][ td_class ][i][5];if (s == 2) s = '02';
            times_array[ times_array.length ] = [h,m,s];
          }
       }

    times_array.sort();

    //Customization Bence - make day with start and end time - unavailable
    //var is_start_here = false;
    //var is_end_here = false;
    //for (var jj=0; jj< times_array.length; jj++){
    //    if (times_array[jj][2]=='01' ) is_start_here = true;
    //    if (times_array[jj][2]=='02' ) is_end_here = true;
    //}
    //if ( (is_start_here) && (is_end_here) ) return true;

// check here according time ranges selection
// and check all slots for reserVATION.
// IF ALL SLOTS ARE RESERVED, INSIDE OF times_array
// SO THEN RETURN TRUE

    var is_element_exist = jQuery('select[name="rangetime' + bk_type + '"]').length;
    if (is_element_exist) {
        var my_timerange_value = jQuery('select[name="rangetime' + bk_type + '"] option');
        var my_st_en_times;
        var my_temp_time;
        var times_ranges_array=[];

        for (var j=0; j< my_timerange_value.length; j++){

            my_st_en_times = my_timerange_value[j].value.split(' - ');

            my_temp_time = my_st_en_times[0].split(':');
            times_ranges_array[ times_ranges_array.length ] = [ my_temp_time[0], my_temp_time[1], '01' ]; //Start time

            my_temp_time = my_st_en_times[1].split(':');
            times_ranges_array[ times_ranges_array.length ] = [ my_temp_time[0], my_temp_time[1], '02' ]; //End time
        }

        // check if all time slots from the selectbox are the booked inside of this day. Simple checking for the same
        if (times_array.length ==  times_ranges_array.length) {
            var is_all_same = true;
            for ( var i=0; i< times_array.length; i++){
                 if (
                      ( times_array[i][0] != times_ranges_array[i][0] ) ||
                      ( times_array[i][1] != times_ranges_array[i][1] ) ||
                      ( times_array[i][2] != times_ranges_array[i][2] )
                    )
                  is_all_same = false;
            }
            if ( is_all_same) return true;
        }

        //Check may be its not possible to select any other time slots from the selectbox, because its already booked, sothen mark this day as booked.
        if ((my_timerange_value.length > 0 ) && (multiple_day_selections == 0)  ){  // Only if range selections exist and we are have single days selections
           var removed_time_slots = is_time_slot_booked_for_this_time_array( bk_type, times_array );
           var some_exist_time_slots = [];
           var my_time_value = jQuery('select[name="rangetime' + bk_type + '"] option');

           for ( j=0; j< my_time_value.length; j++){

               if (  wpdev_in_array( removed_time_slots, j ) ) {

               } else {
                   some_exist_time_slots[some_exist_time_slots.length] = j;
               }
           }
           if (some_exist_time_slots.length == 0 ) return true;
        }

    }

    for ( var i=0; i< times_array.length; i++){  // s = 2 - end time,   s = 1 - start time
       s = parseInt( times_array[i][2] );

       if  (i == 0)
            if  (s !== 2)  {return false;} // Its not start at the start of day

       if ( i > 0 ) {

            if ( s == 1 )
                if  ( !( ( times_array[i-1][0] == times_array[i][0] ) &&  ( times_array[i-1][1] == times_array[i][1] ) ) ) {
                        return false; // previos time is not equal to current so we have some free interval
                }

       }

       if (i == ( times_array.length-1))
               if (s !== 1)   {return false;} // Its not end  at the end of day

    }
    return true;
}





function is_time_slot_booked_for_this_time_array( bk_type, times_array ){


        times_array.sort();
        var my_time_value = '';var j; var bk_time_slot_selection = ''; var minutes_booked; var minutes_slot; var my_range_time;

        var removed_time_slots = [];

        for ( var i=0; i< times_array.length; i++){  // s = 2 - end time,   s = 1 - start time
           s = parseInt( times_array[i][2] );

           if ( i > 0 ) {

                if ( s == 2 )
                    {
                       my_range_time = times_array[i-1][0] + ':' + times_array[i-1][1] + ' - ' + times_array[i][0] + ':' + times_array[i][1]  ;
                       my_time_value = jQuery('select[name="rangetime' + bk_type + '"] option');

                       for ( j=0; j< my_time_value.length; j++){

                          if (my_time_value[j].value == my_range_time ) {  // Mark as disable this option

                            removed_time_slots[ removed_time_slots.length ] = j;
                            //return  true;

                          } else {
                              // We will recheck here if, may  be some interval here inside of already booked intervals, so then we need to disable it.
                              bk_time_slot_selection = my_time_value[j].value;
                              bk_time_slot_selection = bk_time_slot_selection.split('-');
                              bk_time_slot_selection[0] = jQuery.trim(bk_time_slot_selection[0]);
                              bk_time_slot_selection[1] = jQuery.trim(bk_time_slot_selection[1]);

                              bk_time_slot_selection[0] = bk_time_slot_selection[0].split(':');
                              bk_time_slot_selection[1] = bk_time_slot_selection[1].split(':');

                              // Get only minutes
                              minutes_booked = [ (parseInt(times_array[i-1][0]*60) +  parseInt(times_array[i-1][1] )) ,                  (parseInt( times_array[i][0]*60) +  parseInt(times_array[i][1] ) ) ] ;
                              minutes_slot   = [ (parseInt(bk_time_slot_selection[0][0]*60) +  parseInt(bk_time_slot_selection[0][1] )), (parseInt(bk_time_slot_selection[1][0]*60) +  parseInt(bk_time_slot_selection[1][1] ) ) ] ;


                              if (
                                   ( ( minutes_booked[0] >= minutes_slot[0] ) && ( minutes_booked[0] < minutes_slot[1] ) ) ||
                                   ( ( minutes_booked[1] > minutes_slot[0] ) && ( minutes_booked[1] <= minutes_slot[1] ) )
                               ||
                                   ( ( minutes_slot[0] >= minutes_booked[0] ) && ( minutes_slot[0] < minutes_booked[1] ) ) ||
                                   ( ( minutes_slot[1] > minutes_booked[0] ) && ( minutes_slot[1] <= minutes_booked[1] ) )
                                 )
                              {
                                  removed_time_slots[ removed_time_slots.length ] = j;
                                  //return  true;
                              }


                          }


                       }
                    }

           }

        }



    return  removed_time_slots ;


}







function hoverDayTime(value, date, bk_type) {

    if (date == null) return;

    var i=0 ;var h ='' ;var m ='' ;var s='';
    var td_class;
    var my_date;


   // Gathering information hint for tooltips ////////////////////////////////
   var tooltip_time = '';
   var times_array = [];
   td_class =  (date.getMonth()+1) + '-' + date.getDate() + '-' + date.getFullYear();

   var my_day = date.getDate();
   if(my_day < 10)
    my_day = '0' + my_day;

  var my_month = date.getMonth()+1;
  if(my_month < 10)
    my_month = '0' + my_month;

  my_date =  date.getFullYear() +''+ my_month + '' + my_day;

  var affiche_date = date.getDate() + " " + my_month + ' ' + date.getFullYear(); 

   // Get dates and time from aproved dates
   if(typeof(date_approved[ bk_type ]) !== 'undefined')
   if(typeof(date_approved[ bk_type ][ td_class ]) !== 'undefined') {
     if( ( date_approved[ bk_type ][ td_class ][0][3] != 0) ||  ( date_approved[ bk_type ][ td_class ][0][4] != 0) ) {
         for ( i=0; i< date_approved[ bk_type ][ td_class ].length; i++){
            h = date_approved[ bk_type ][ td_class ][i][3];if (h < 10) h = '0' + h;if (h == 0) h = '00';
            m = date_approved[ bk_type ][ td_class ][i][4];if (m < 10) m = '0' + m;if (m == 0) m = '00';
            s = date_approved[ bk_type ][ td_class ][i][5];if (s == 2) s = '02';
            times_array[ times_array.length ] = [h,m,s];
         }
     }
   }

   // Get dates and time from pending dates
   if(typeof( date2approve[ bk_type ]) !== 'undefined')
   if(typeof( date2approve[ bk_type ][ td_class ]) !== 'undefined')
     if( ( date2approve[ bk_type ][ td_class ][0][3] != 0) ||  ( date2approve[ bk_type ][ td_class ][0][4] != 0) ) //check for time here
       {for ( i=0; i< date2approve[ bk_type ][ td_class ].length; i++){
            h = date2approve[ bk_type ][ td_class ][i][3];if (h < 10) h = '0' + h;if (h == 0) h = '00';
            m = date2approve[ bk_type ][ td_class ][i][4];if (m < 10) m = '0' + m;if (m == 0) m = '00';
            s = date2approve[ bk_type ][ td_class ][i][5];if (s == 2) s = '02';
            times_array[ times_array.length ] = [h,m,s];
          }
       }

//alert(times_array);
   // Time availability
   if (typeof( hover_day_check_global_time_availability ) == 'function') {times_array = hover_day_check_global_time_availability( date, bk_type ,times_array);}

    times_array.sort();
// if (times_array.length>0) alert(times_array);
    for ( i=0; i< times_array.length; i++){  // s = 2 - end time,   s = 1 - start time
       s = parseInt( times_array[i][2] );
       if (s == 2) {if (tooltip_time == '') tooltip_time = '&nbsp;&nbsp;&nbsp;&nbsp;...&nbsp;&nbsp;&nbsp; - ';}      // End time and before was no dates so its start from start of date
       if ( (tooltip_time == '') && (times_array[i][0]=='00') && (times_array[i][1]=='00') )
           tooltip_time = '&nbsp;&nbsp;&nbsp;&nbsp;...&nbsp;&nbsp;&nbsp;';  //start date at the midnight
       else if ( (i == ( times_array.length-1)) && (times_array[i][0]=='23') && (times_array[i][1]=='59') )
        tooltip_time += ' &nbsp;&nbsp;&nbsp;&nbsp;... ';
       else {
        var hours_show = times_array[i][0];
        var hours_show_sufix = '';
        if (is_am_pm_inside_time) {
            if (hours_show>=12) {
                hours_show = hours_show - 12;
                if (hours_show==0) hours_show = 12;
                hours_show_sufix = ' pm';
            } else {
                hours_show_sufix = ' am';
            }
        }
//Customization of bufer time for DAN
if (times_array[i][2] == '02' ) {
    times_array[i][1] = ( times_array[i][1]*1)  + time_buffer_value ;
    if (times_array[i][1] > 59 ) {
        times_array[i][1] = times_array[i][1] - 60;
        hours_show = (hours_show*1) + 1;
    }
    if (times_array[i][1] < 10 ) times_array[i][1] = '0'+times_array[i][1];
}

        tooltip_time += hours_show + ':' + times_array[i][1] + hours_show_sufix;
       }


       if (s == 1) {tooltip_time += ' - ';if (i == ( times_array.length-1)) tooltip_time += ' &nbsp;&nbsp;&nbsp;&nbsp;... ';}
       if (s == 2) {
           tooltip_time += get_additional_info_for_tooltip( bk_type , td_class , times_array[i][0] + ':' + times_array[i][1] );
           tooltip_time += '<br />';
       } /**/
    }

    // jQuery( '#calendar_booking'+bk_type+' td.cal4date-'+td_class )  // TODO: continue working here, check unshow times at full booked days
    if ( tooltip_time.indexOf("undefined") > -1 ) {tooltip_time = '';}
    if(typeof( getDayPrice4Show ) == 'function') {tooltip_time = getDayPrice4Show(bk_type, tooltip_time, td_class);}  
    if(typeof( getDayAvailability4Show ) == 'function') {tooltip_time = getDayAvailability4Show(bk_type, tooltip_time, td_class);}  

    tooltip_time = "Liste des horaires pour le " + affiche_date + "<br />";

    var i;
    for(i = 0; i < tout_horaire.length; i++){
      if(typeof(nb_pers_per_hour[bk_type][my_date]) != 'undefined'){
        if(typeof(nb_pers_per_hour[bk_type][my_date][tout_horaire[i]]) != 'undefined'){
          var nb_pers = nb_pers_per_hour[bk_type][my_date][tout_horaire[i]];
        }else
          nb_pers = 0;
      }else
        nb_pers = 0;

      var placerestante = maxvisitor[bk_type] - nb_pers;
      if(placerestante <= 0){
        tooltip_time += "- <strong>" + tout_horaire[i] + "</strong> : <span class=\"horaireComplet\"><em>L'horaire est complet ! </strong></span><br/>";
      }else if(placerestante == 1){
        tooltip_time += "- <strong>" + tout_horaire[i] + "</strong> : <span class=\"visitorInVisit\">Nombre de place restante : <strong><em>" + placerestante + "</em></strong>.</span><br/>";
      }else if(placerestante != maxvisitor[bk_type]){
        tooltip_time += "- <strong>" + tout_horaire[i] + "</strong> : <span class=\"visitorInVisit\">Nombre de places restantes : <strong><em>" + placerestante + "</em></strong>.</span><br/>";
      }else{
        tooltip_time += "- <strong>" + tout_horaire[i] + "</strong> : Nombre de places restantes : <strong><em>" + placerestante + "</em></strong>.<br/>";
      }
    }

    jQuery( '#calendar_booking'+bk_type+' td.cal4date-'+td_class ).attr('data-content', tooltip_time ) ;
    
    ////////////////////////////////////////////////////////////////////////

}

function get_additional_info_for_tooltip( bk_type , td_class , times_array ){
return '';
    // TODO: stop working here according names in tooltips
    //var id_was_here = [];

    var return_variable = '<span style=\"font-weight:normal !important;font-size:11px !important;\">';

    var posi = 0;
    //  posi = dates_additional_info[ bk_type ][ td_class ][0][ 'rangetime' ].indexOf( ' - ' ); // returns -1
    // alert(  dates_additional_info[ bk_type ][ td_class ][0][ 'rangetime' ].substr(posi + 3 ) );

    for(var ik=0 ; ik< dates_additional_info[ bk_type ][ td_class ].length; ik++) {

           //if (dates_additional_info[ bk_type ][ td_class ][ik][ 'endtime' ] == times_array ) {
        posi = dates_additional_info[ bk_type ][ td_class ][ik][ 'rangetime' ].indexOf( ' - ' ); // returns -1
        if ( dates_additional_info[ bk_type ][ td_class ][ik][ 'rangetime' ].substr(posi + 3 )== times_array ) {


           return_variable =  ' - ';
           if (dates_additional_info[ bk_type ][ td_class ][ik][ 'name' ] != undefined)
                return_variable +=  dates_additional_info[ bk_type ][ td_class ][ik][ 'name' ] ;

           if (dates_additional_info[ bk_type ][ td_class ][ik][ 'secondname' ] != undefined)
                return_variable += ' ' + dates_additional_info[ bk_type ][ td_class ][ik][ 'secondname' ] ;


           if (dates_additional_info[ bk_type ][ td_class ][ik] [ 'details2' ] != undefined)
                return_variable +='<br /> ' + dates_additional_info[ bk_type ][ td_class ][ik] [ 'details2' ] + '';
           return_variable += '</span>'


           return return_variable;
       }
       /* if ( ! wpdev_in_array(id_was_here, dates_additional_info[ bk_type ][ td_class ][ik] [ 'id' ] ) ) {
         id_was_here[id_was_here.length] =  dates_additional_info[ bk_type ][ td_class ][ik] [ 'id' ];
         tooltip_time +=  dates_additional_info[ bk_type ][ td_class ][ik] [ 'name' ] + '>' + dates_additional_info[ bk_type ][ td_class ][ik] [ 'endtime' ];
       }/**/
    }
    return '';
}

function isTimeTodayGone(myTime, sort_date_array){
    if (parseInt(sort_date_array[0][0]) < parseInt(wpdev_bk_today[0])) return true;
    if (( parseInt(sort_date_array[0][0]) == parseInt(wpdev_bk_today[0])  ) && ( parseInt(sort_date_array[0][1]) < parseInt(wpdev_bk_today[1])  ) )
        return true;
    if (( parseInt(sort_date_array[0][0]) == parseInt(wpdev_bk_today[0])  ) && ( parseInt(sort_date_array[0][1]) == parseInt(wpdev_bk_today[1])  ) && ( parseInt(sort_date_array[0][2]) < parseInt(wpdev_bk_today[2])  ) )
        return true;
    if (( parseInt(sort_date_array[0][0]) == parseInt(wpdev_bk_today[0])  ) &&
        ( parseInt(sort_date_array[0][1]) == parseInt(wpdev_bk_today[1])  ) &&
        ( parseInt(sort_date_array[0][2]) == parseInt(wpdev_bk_today[2])  )) {
        var mytime_value = myTime.split(":");
        mytime_value = mytime_value[0]*60 + parseInt(mytime_value[1]);

        var current_time_value = wpdev_bk_today[3]*60 + parseInt(wpdev_bk_today[4]);

        if ( current_time_value  > mytime_value ) return true;

    }
    return false;
}


var start_time_checking_index;

function checkTimeInside( mytime, is_start_time, bk_type ) {

        // Check time availability for global filters
        if(typeof( check_entered_time_to_global_availability_time ) == 'function') {if (! check_entered_time_to_global_availability_time(mytime, is_start_time, bk_type) ) return false;}

        var my_dates_str = document.getElementById('date_booking'+ bk_type ).value;                 // GET DATES From TEXTAREA

        return checkTimeInsideProcess( mytime, is_start_time, bk_type, my_dates_str );

}

function checkRecurentTimeInside( my_rangetime,  bk_type ) {

   var valid_time = true;
   var my_dates_str = document.getElementById('date_booking'+ bk_type ).value;                 // GET DATES From TEXTAREA
    // recurent time check for all days in loop

    var date_array = my_dates_str.split(", ");
    if (date_array.length == 2) { // This recheck is need for editing booking, with single day
        if (date_array[0]==date_array[1]) {
            date_array = [ date_array[0] ];
        }
    }
    var temp_date_str = '';
    for (var i=0; i< date_array.length; i++) {  // Get SORTED selected days array
            temp_date_str = date_array[i];
            if ( checkTimeInsideProcess( my_rangetime[0], true, bk_type, temp_date_str ) == false )   valid_time = false;
            if ( checkTimeInsideProcess( my_rangetime[1], false, bk_type, temp_date_str ) == false )  valid_time = false;

    }

    return valid_time;
}




// Function check start and end time at selected days
function checkTimeInsideProcess( mytime, is_start_time, bk_type, my_dates_str ) {


    var date_array = my_dates_str.split(", ");
    if (date_array.length == 2) { // This recheck is need for editing booking, with single day
        if (date_array[0]==date_array[1]) {
            date_array = [ date_array[0] ];
        }
    }

    var temp_elemnt;var td_class;var sort_date_array = [];var work_date_array = [];var times_array = [];var is_check_for_time;

    for (var i=0; i< date_array.length; i++) {  // Get SORTED selected days array
        temp_elemnt = date_array[i].split(".");
        sort_date_array[i] = [ temp_elemnt[2], temp_elemnt[1] + '', temp_elemnt[0] + '' ]; // [2009,7,1],...
    }
    sort_date_array.sort();                                                                   // SORT    D a t e s
    for (i=0; i< sort_date_array.length; i++) {                                  // trnasform to integers
        sort_date_array[i] = [ parseInt(sort_date_array[i][0]*1), parseInt(sort_date_array[i][1]*1), parseInt(sort_date_array[i][2]*1) ]; // [2009,7,1],...
    }

    if (is_start_time) {

        if ( isTimeTodayGone(mytime, sort_date_array) )  return false;
    }
    //  CHECK FOR BOOKING INSIDE OF     S E L E C T E D    DAY RANGE AND FOR TOTALLY BOOKED DAYS AT THE START AND END OF RANGE
    work_date_array =  sort_date_array;
    for (var j=0; j< work_date_array.length; j++) {
        td_class =  work_date_array[j][1] + '-' + work_date_array[j][2] + '-' + work_date_array[j][0];

        if ( (j==0) || (j == (work_date_array.length-1)) ) is_check_for_time = true;         // Check for time only start and end time
        else                                               is_check_for_time = false;

        // Get dates and time from pending dates
        if(typeof( date2approve[ bk_type ]) !== 'undefined') {
          if ( (typeof( date2approve[ bk_type ][ td_class ]) !== 'undefined') ) {
             if (! is_check_for_time) {return false;} // its mean that this date is booked inside of range selected dates
             if( ( date2approve[ bk_type ][ td_class ][0][3] != 0) ||  ( date2approve[ bk_type ][ td_class ][0][4] != 0) ) {
                 // Evrything good - some time is booked check later
             } else {return false;} // its mean that this date tottally booked
          }
        }

        // Get dates and time from pending dates
        if(typeof( date_approved[ bk_type ]) !== 'undefined') {
          if ( (typeof( date_approved[ bk_type ][ td_class ]) !== 'undefined') ) {
             if (! is_check_for_time) {return false;} // its mean that this date is booked inside of range selected dates
             if( ( date_approved[ bk_type ][ td_class ][0][3] != 0) ||  ( date_approved[ bk_type ][ td_class ][0][4] != 0) ) {
                 // Evrything good - some time is booked check later
             } else {return false;} // its mean that this date tottally booked
          }
        }
    }  ///////////////////////////////////////////////////////////////////////////////////////////////////////


     // Check    START   OR    END   time for time no in correct fee range
     if (is_start_time ) work_date_array =  sort_date_array[0] ;
     else                work_date_array =  sort_date_array[sort_date_array.length-1] ;

     td_class =  work_date_array[1] + '-' + work_date_array[2] + '-' + work_date_array[0];

        // Get dates and time from pending dates
        if(typeof( date2approve[ bk_type ]) !== 'undefined')
          if(typeof( date2approve[ bk_type ][ td_class ]) !== 'undefined')
              for ( i=0; i< date2approve[ bk_type ][ td_class ].length; i++){
                h = date2approve[ bk_type ][ td_class ][i][3];if (h < 10) h = '0' + h;if (h == 0) h = '00';
                m = date2approve[ bk_type ][ td_class ][i][4];if (m < 10) m = '0' + m;if (m == 0) m = '00';
                s = date2approve[ bk_type ][ td_class ][i][5];

//Customization of bufer time for DAN
if (s == '02') {
    m = ( m*1 )  + time_buffer_value ;
    if (m > 59 ) {
        m = m - 60;
        h = (h*1) + 1;
    }
    if (m < 10 ) m = '0'+m;
}

                times_array[ times_array.length ] = [h,m,s];
              }

        // Get dates and time from pending dates
        if(typeof( date_approved[ bk_type ]) !== 'undefined')
          if(typeof( date_approved[ bk_type ][ td_class ]) !== 'undefined')
              for ( i=0; i< date_approved[ bk_type ][ td_class ].length; i++){
                h = date_approved[ bk_type ][ td_class ][i][3];if (h < 10) h = '0' + h;if (h == 0) h = '00';
                m = date_approved[ bk_type ][ td_class ][i][4];if (m < 10) m = '0' + m;if (m == 0) m = '00';
                s = date_approved[ bk_type ][ td_class ][i][5];

//Customization of bufer time for DAN
if (s == '02') {
    m = ( m*1 )  + time_buffer_value ;
    if (m > 59 ) {
        m = m - 60;
        h = (h*1) + 1;
    }
    if (m < 10 ) m = '0'+m;
}


                times_array[ times_array.length ] = [h,m,s];
              }


        times_array.sort();                     // SORT TIMES

        var times_in_day = [];                  // array with all times
        var times_in_day_interval_marks = [];   // array with time interval marks 1- stsrt time 2 - end time


        for ( i=0; i< times_array.length; i++){s = times_array[i][2];         // s = 2 - end time,   s = 1 - start time
           // Start close interval
           if ( (s == 2) &&  (i == 0) ) {times_in_day[ times_in_day.length ] = 0;times_in_day_interval_marks[times_in_day_interval_marks.length]=1;}
           // Normal
           times_in_day[ times_in_day.length ] = times_array[i][0] * 60 + parseInt(times_array[i][1]);
           times_in_day_interval_marks[times_in_day_interval_marks.length]=s;
           // End close interval
           if ( (s == 1) &&  (i == (times_array.length-1)) ) {times_in_day[ times_in_day.length ] = (24*60);times_in_day_interval_marks[times_in_day_interval_marks.length]=2;}
        }

        // Get time from entered time
        var mytime_value = mytime.split(":");
        mytime_value = mytime_value[0]*60 + parseInt(mytime_value[1]);

//alert('My time:'+ mytime_value + '  List of times: '+ times_in_day + '  Saved indexes: ' + start_time_checking_index + ' Days: ' + sort_date_array ) ;

        var start_i = 0;
        if (start_time_checking_index != undefined)
            if (start_time_checking_index[0] != undefined)
                if ( (! is_start_time) && (sort_date_array.length == 1) ) {start_i = start_time_checking_index[0]; /*start_i++;*/}
        i=start_i;

        // Main checking inside a day
        for ( i=start_i; i< times_in_day.length; i++){
            times_in_day[i] = parseInt(times_in_day[i]);
            mytime_value = parseInt(mytime_value);
            if (is_start_time ) {
                if ( mytime_value > times_in_day[i] ){
                    // Its Ok, lets Loop to next item
                } else if ( mytime_value == times_in_day[i] ) {
                    if (times_in_day_interval_marks[i] == 1 ) {return false;     //start time is begin with some other interval
                    } else {
                        if ( (i+1) <= (times_in_day.length-1) ) {
                            if ( times_in_day[i+1] <= mytime_value ) return false;  //start time  is begin with next elemnt interval
                            else  {                                                 // start time from end of some other
                                if (sort_date_array.length > 1)
                                    if ( (i+1) <= (times_in_day.length-1) ) return false;   // Its mean that we make end booking at some other day then this and we have some booking time at this day after start booking  - its wrong
                                start_time_checking_index = [i, td_class,mytime_value];
                                return true;
                            }
                        }
                        if (sort_date_array.length > 1)
                            if ( (i+1) <= (times_in_day.length-1) ) return false;   // Its mean that we make end booking at some other day then this and we have some booking time at this day after start booking  - its wrong
                        start_time_checking_index = [i, td_class,mytime_value];
                        return true;                                            // start time from end of some other
                    }
                } else if ( mytime_value < times_in_day[i] ) {
                    if (times_in_day_interval_marks[i] == 2 ){return false;     // start time inside of some interval
                    } else {
                        if (sort_date_array.length > 1)
                            if ( (i+1) <= (times_in_day.length-1) ) return false;   // Its mean that we make end booking at some other day then this and we have some booking time at this day after start booking  - its wrong
                        start_time_checking_index = [i, td_class,mytime_value];
                        return true;
                    }
                }
            } else {
                if (sort_date_array.length == 1) {

                   if (start_time_checking_index !=undefined)
                       if (start_time_checking_index[2]!=undefined)

                            if ( ( start_time_checking_index[2] == times_in_day[i] ) && ( times_in_day_interval_marks[i] == 2) ) {    // Good, because start time = end of some other interval and we need to get next interval for current end time.
                            } else if ( times_in_day[i] < mytime_value ) return false;                 // some interval begins before end of curent "end time"
                            else {
                                if (start_time_checking_index[2]>= mytime_value) return false;  // we are select only one day and end time is earlythe starttime its wrong
                                return true;                                                    // if we selected only one day so evrything is fine and end time no inside some other intervals
                            }
                } else {
                    if ( times_in_day[i] < mytime_value ) return false;                 // Some other interval start before we make end time in the booking at the end day selection
                    else                                  return true;
                }

            }
        }

        if (is_start_time )  start_time_checking_index = [i, td_class,mytime_value];
        else {
           if (start_time_checking_index !=undefined)
               if (start_time_checking_index[2]!=undefined)
                    if ( (sort_date_array.length == 1) && (start_time_checking_index[2]>= mytime_value) ) return false;  // we are select only one day and end time is earlythe starttime its wrong
        }
        return true;
}





function save_this_booking_cost(booking_id, cost){

    if (cost!='') {


            var ajax_bk_message = 'Updating...';
            
            document.getElementById('ajax_working').innerHTML =
            '<div class="info_message ajax_message" id="ajax_message">\n\
                <div style="float:left;">'+ajax_bk_message+'</div> \n\
                <div  style="float:left;width:80px;margin-top:-3px;">\n\
                       <img src="'+wpdev_bk_plugin_url+'/img/ajax-loader.gif">\n\
                </div>\n\
            </div>';

            var wpdev_ajax_path = wpdev_bk_plugin_url+'/' + wpdev_bk_plugin_filename ;
            var ajax_type_action='SAVE_BK_COST';

            jQuery.ajax({                                           // Start Ajax Sending
                url: wpdev_ajax_path,
                type:'POST',
                success: function (data, textStatus){if( textStatus == 'success')   jQuery('#ajax_respond' ).html( data ) ;},
                error:function (XMLHttpRequest, textStatus, errorThrown){window.status = 'Ajax sending Error status:'+ textStatus;alert(XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText);if (XMLHttpRequest.status == 500) {alert('Please check at this page according this error:' + ' http://wpbookingcalendar.com/faq/#ajax-sending-error');}},
                // beforeSend: someFunction,
                data:{
                    ajax_action : ajax_type_action,
                    booking_id : booking_id,
                    cost : cost
                }
            });
            return false;
        }
        return true;

}

function sendPaymentRequestByEmail(payment_request_id , request_reason, wpdev_active_locale) {
 
            var ajax_bk_message = 'Sending...';

            document.getElementById('ajax_working').innerHTML =
            '<div class="info_message ajax_message" id="ajax_message">\n\
                <div style="float:left;">'+ajax_bk_message+'</div> \n\
                <div  style="float:left;width:80px;margin-top:-3px;">\n\
                       <img src="'+wpdev_bk_plugin_url+'/img/ajax-loader.gif">\n\
                </div>\n\
            </div>';

            var wpdev_ajax_path = wpdev_bk_plugin_url+'/' + wpdev_bk_plugin_filename ;
            var ajax_type_action='SEND_PAYMENT_REQUEST';

            jQuery.ajax({                                           // Start Ajax Sending
                url: wpdev_ajax_path,
                type:'POST',
                success: function (data, textStatus){if( textStatus == 'success')   jQuery('#ajax_respond' ).html( data ) ;},
                error:function (XMLHttpRequest, textStatus, errorThrown){window.status = 'Ajax sending Error status:'+ textStatus;alert(XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText);if (XMLHttpRequest.status == 500) {alert('Please check at this page according this error:' + ' http://wpbookingcalendar.com/faq/#ajax-sending-error');}},
                // beforeSend: someFunction,
                data:{
                    ajax_action : ajax_type_action,
                    booking_id : payment_request_id,
                    reason : request_reason,
                    wpdev_active_locale:wpdev_active_locale
                }
            });
            return false;


}

// Chnage the booking status of booking
function chnage_booking_payment_status(booking_id, payment_status, payment_status_show) {

            var ajax_bk_message = 'Updating...';

            document.getElementById('ajax_working').innerHTML =
            '<div class="info_message ajax_message" id="ajax_message">\n\
                <div style="float:left;">'+ajax_bk_message+'</div> \n\
                <div  style="float:left;width:80px;margin-top:-3px;">\n\
                       <img src="'+wpdev_bk_plugin_url+'/img/ajax-loader.gif">\n\
                </div>\n\
            </div>';

            var wpdev_ajax_path = wpdev_bk_plugin_url+'/' + wpdev_bk_plugin_filename ;
            var ajax_type_action='CHANGE_PAYMENT_STATUS';

            jQuery.ajax({                                           // Start Ajax Sending
                url: wpdev_ajax_path,
                type:'POST',
                success: function (data, textStatus){if( textStatus == 'success')   jQuery('#ajax_respond' ).html( data ) ;},
                error:function (XMLHttpRequest, textStatus, errorThrown){window.status = 'Ajax sending Error status:'+ textStatus;alert(XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText);if (XMLHttpRequest.status == 500) {alert('Please check at this page according this error:' + ' http://wpbookingcalendar.com/faq/#ajax-sending-error');}},
                // beforeSend: someFunction,
                data:{
                    ajax_action : ajax_type_action,
                    booking_id : booking_id,
                    payment_status : payment_status,
                    payment_status_show: payment_status_show
                }
            });
}