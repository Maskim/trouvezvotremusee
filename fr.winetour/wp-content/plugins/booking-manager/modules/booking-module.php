<?php

if (!class_exists('wpdev_booking_module_for_orders')) {
    class wpdev_booking_module_for_orders  {
            
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Constructor ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
         function wpdev_booking_module_for_orders() {
              add_action('wpdev_new_booking', array(&$this, 'add_booking_order'),1,5); // Save order from booking
         }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // A C T I O N S
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Add  order   only for    B O O K I N G s        // Original params: $booking_id, $bktype, str_replace('|',',',$dates), array($start_time, $end_time ) ,$sdform );
        function add_booking_order( $booking_id, $booking_type, $booking_days, $times_array , $booking_form ){
           add_booking_order_single( $booking_id, $booking_type, $booking_days, $times_array , $booking_form );
        }



    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // F U N C T I O N S
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        function get_booking_short_days( $long_days ) {

                $last_day = '';
                $last_show_day = '';
                $short_days = array();

                foreach ($long_days as $dte) {

                    if (empty($last_day)) { // First date
                        $short_days[]= $dte;
                        $last_show_day = $dte;
                    } else {                // All other days
                        if ( apply_filters('wpdev_bk_is_next_day',  $dte ,$last_day) ){
                            if ($last_show_day != '-') $short_days[]= '-';
                            $last_show_day = '-';
                        } else {
                            if ($last_show_day !=$last_day) $short_days[]= $last_day;
                            $short_days[]= ',';
                            $short_days[]= $dte;
                            $last_show_day = $dte;
                        }
                    }
                    $last_day = $dte;
                }
                if($last_show_day != $dte) $short_days[]= $dte;

                $short_days_string = '';
                foreach ($short_days as $day_string) { $short_days_string .= $day_string . ' '; }

                return array($short_days, $short_days_string);
       }



   }
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Support functions
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Check if table exist //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (!function_exists ('is_wpdev_table_exists')) {
     function is_wpdev_table_exists( $tablename ) {
    global $wpdb;
    if (strpos($tablename, $wpdb->prefix) ===false) $tablename = $wpdb->prefix . $tablename ;
    $sql_check_table = "
        SELECT COUNT(*) AS count
        FROM information_schema.tables
        WHERE table_schema = '". DB_NAME ."'
        AND table_name = '" . $tablename . "'";

    $res = $wpdb->get_results($sql_check_table);
    return $res[0]->count;

}
}

// Show Ajax message at the top of page //////////////////////////////////////////////////////////////////////////////////////////////////////
if (!function_exists ('show_top_message_wpdev')) {
     function show_top_message_wpdev($mess, $show_time = 3000, $is_hide=false) {
    ?> <script type="text/javascript">
        document.getElementById('ajax_working').innerHTML = '<div class="info_message ajax_message" id="ajax_message">\n\
            <div style="text-align:center;">'+'<?php echo $mess; ?>'+'</div> \n\
            <div  style="float:left;width:80px;margin-top:-3px;">\n\
            </div>\n\
            </div>';
        jWPDev('.info_message').animate({opacity: 1},<?php echo $show_time; ?>);
        <?php if($is_hide) { ?> jWPDev('.info_message').fadeOut(2000); <?php } ?>

    </script> <?php
}
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// AJAX
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Import all exist bookings
function wpdev_import_booking_crm_ajax( $is_hide = false ){  global $wpdb;
            if (! $is_hide) {
            ?> <script type="text/javascript">
                       jQuery('.this_td_empty').fadeOut(500);
                       var $my_row = '';
              </script>
            <?php
            }

            $fin_message = __('Done','wpdev-crm');
            // Import here
            if ( is_wpdev_table_exists('booking')) {

            $where = '';
            $sql = "SELECT ordr.customer_id, ordr.internal_id
                            FROM ".$wpdb->prefix ."wpdev_crm_orders as ordr " ;
                           //$sql .= "INNER JOIN ".$wpdb->prefix ."wpdev_crm_customers as custmr " ;
                           //$sql .= "ON    ordr.customer_id = custmr.customer_id " ;
                            $sql .= " " . $where ." ";
                            $sql .= " ORDER BY ordr.order_id ";
 //debuge($sql);die;
            $result_orders = $wpdb->get_results( $sql );

            $exist_bookings_id = '';
            foreach ($result_orders as $in_id) {
                $exist_bookings_id .= $in_id->internal_id .', ';
            }
//debuge($exist_bookings_id);
            if ($exist_bookings_id != '') {
                $exist_bookings_id = substr($exist_bookings_id,0,-2);
                $exist_bookings_id = " WHERE  bk.booking_id NOT IN (". $exist_bookings_id .")";
            }
//debuge($exist_bookings_id);die;
                //Set here order by some field
            $sql = "SELECT *
                        FROM ".$wpdb->prefix ."booking as bk
                        INNER JOIN ".$wpdb->prefix ."bookingdates as dt
                        ON    bk.booking_id = dt.booking_id " .
                        $exist_bookings_id .
                       " ORDER BY bk.booking_id ASC, dt.booking_date ASC
                   ";
            $result = $wpdb->get_results( $sql );



            if (! $is_hide)  show_top_message_wpdev(  __('Processing...','wpdev-crm') , 3000, false );

//adebug(count($result), $result);

            $final_structure = array();
            $previos_id = ''; $previos_date = '';
            $fsi = -1;
            // Loop all bookings for getting final strucutre with dates.
            foreach ($result as $single_booking) {
                if ($previos_id == $single_booking->booking_id ) {              // if ID the same so we are getting  only date
                    $final_structure[$fsi]['booking_date'] .= ', ' .$single_booking->booking_date;
                    $previos_date = $single_booking->booking_date;
                } else {
                    if ( ($fsi!==-1) && ($previos_date!=='') ) {                // Get end time from previos record
                        $end_time = explode(' ', $previos_date);
                        $end_time= explode(':', $end_time[1]);
                        $final_structure[$fsi]['end_time'] =  $end_time;
                    }
                    $previos_date = '';                                         // New booking
                    $fsi++;
                    $previos_id = $single_booking->booking_id;
                    $final_structure[$fsi] = array();
                    $final_structure[$fsi]['booking_id'] =  $single_booking->booking_id;
                    $final_structure[$fsi]['form'] =  $single_booking->form;
                    $final_structure[$fsi]['booking_type'] =  $single_booking->booking_type;
                    $final_structure[$fsi]['remark'] =  $single_booking->remark;
                    if (isset($single_booking->pay_status)) $final_structure[$fsi]['pay_status'] =  $single_booking->pay_status; else  $final_structure[$fsi]['pay_status'] =  '';
                    if (isset($single_booking->cost)) $final_structure[$fsi]['cost'] =  $single_booking->cost; else  $final_structure[$fsi]['cost'] =  '';
                    $final_structure[$fsi]['booking_date'] =  $single_booking->booking_date;
                    $final_structure[$fsi]['approved'] =  $single_booking->approved;
                    $start_time = explode(' ', $single_booking->booking_date);
                    $start_time= explode(':', $start_time[1]);
                    $final_structure[$fsi]['start_time'] =  $start_time;
                }
            }
            if ( ($fsi!==-1) && ($previos_date!=='') ) {                        // Get end time for the last booking
                $end_time = explode(' ', $previos_date);
                $end_time= explode(':', $end_time[1]);
                $final_structure[$fsi]['end_time'] =  $end_time;
            }
            $togather_cont= count($final_structure);
            if (! $is_hide)  show_top_message_wpdev(  sprintf(__('Found %s new bookings...','wpdev-crm'), $togather_cont ) , 3000, true );
//adebug($final_structure);

            // Loop all bookings
            $my_cnt = 0;
            $alternative_color = '';
            foreach ($final_structure as $single_booking) { $my_cnt++;
                if ( $alternative_color == '') {$alternative_color = ' class="alternative_color" ';} else { $alternative_color = '';}

                // Function for adding new orders from booking

                 $booking_id = $single_booking['booking_id'] ;
                 $booking_type = $single_booking['booking_type'];
                 $booking_days = $single_booking['booking_date'] ;
                 $times_array = array( $single_booking['start_time'], $single_booking['end_time'] );
                 $booking_form = $single_booking['form'] ;
                 $booking_cost = $single_booking['cost'] ;

                $added_booking = add_booking_order_single( $booking_id, $booking_type, $booking_days, $times_array , $booking_form , $booking_cost );


                //$added_booking['date_of_order'] = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d'), '1970') );// set old date and not import date for not showing it, during import

                if (! $is_hide)  show_top_message_wpdev(  sprintf(__('Added %s  bookings...','wpdev-crm'), $my_cnt . '/'. $togather_cont ) , 3000, true );
                if (! $is_hide)
                  if ($added_booking !== false) {
                ?>
                    <script type="text/javascript">
                       $my_row = '<tr>';
                       //$my_row += '<td colspan="6" <?php echo $alternative_color ?> style="text-align:left;" > <?php echo __('Record','wpdev-crm'), ' #',$added_booking['id'] , ' ', __('imported successfully','wpdev-crm'); ?> </td>';
                       $my_row += '<td <?php echo $alternative_color ?> style="text-align:left;" > <?php echo $added_booking['id'] ; ?> </td>';
                       $my_row += '<td <?php echo $alternative_color ?> style="font-size:11px;"> <?php echo $added_booking['info']; ?> </td>';
                       $my_row += '<td <?php echo $alternative_color ?> ><a  class="bktypetitle" style="line-height: 25px; background-color:#888;text-shadow:0 -1px 0 #CCCCCC;color:#fff; padding: 2px 5px; font-size: 10px; white-space: nowrap;" href="javascript:;"><?php echo $added_booking['customer'] ?></a></td>';
                       $my_row += '<td <?php echo $alternative_color ?> ><a class="bktypetitle" style="line-height: 25px; background-color:#fff; padding: 2px 5px; font-size: 10px; white-space: nowrap;"  href="javascript:;" ><?php echo $added_booking['type'] ?></a></td>';


                       $my_row += '<td <?php echo $alternative_color ?> >';
                                   //$my_row += '<span><a href="javascript:;" class="booking_overmause1" ><?php
                          echo apply_filters('wpdev_bk_get_showing_date_format',  mysql2date('U', $added_booking['date_of_order'] ));
                          ?></a></span>';
                       $my_row += '</td>';
                       $my_row += '<th <?php echo $alternative_color ?> > <?php echo $added_booking['cost'] ?> </th>';
                       $my_row += '</tr>';

                       jQuery('#wpdev_order_table tr:first').after($my_row)
                    </script>
                <?php }
                }
                if (! $is_hide)
                    if ( $my_cnt != 0) {
                        ?> <center><h3><a href="javascript:;" onclick="javascript:location.reload(true);"><?php _e('Reload page','wpdev-crm'); ?></a></h3></center> <?php
                    }
            } else { $fin_message = __('Can not find installed Booking Calendar plugin inside of system','wpdev-crm'); }
//            show_top_message_wpdev(  $fin_message  , 2000, true );
            if (! $is_hide) { die(); }
}


// Adding / import one new bookings
function add_booking_order_single( $booking_id, $booking_type, $booking_days, $times_array , $booking_form , $booking_cost =0 ){
// debuge($booking_cost);
   global $wpdb;
   if ($booking_cost != 0 ) $summ = $booking_cost;
   else {
       if ($booking_cost === '0.00') $summ = 0;
       else {
           $summ = 0;
           if ( class_exists('wpdev_bk_premium')) // Get COST if possible.
                $summ = apply_filters('wpdev_get_booking_cost', $booking_type, $booking_days, $times_array, $booking_form );
       }
   }
   //$now_time = gmdate( 'Y-m-d H:i:s' );
   $now_time = date_i18n("Y-m-d H:i:s", mktime()) ;

   // Get dates with Time /////////////////////////////////////////////////////////////
    $start_time = $times_array[0];
    $end_time   = $times_array[1];
    $my_dates = explode(',', $booking_days);

    $my_dates4DB = '';
    if ( count($my_dates) == 1 ) {
        if ( ( $start_time != array('00', '00', '00') ) && ( $end_time != array('00', '00', '00') ) ) {
            $my_dates[]=$my_dates[0];
        }
    }
    for ($i = 0; $i < count($my_dates); $i++) { $my_dates[$i] = trim($my_dates[$i]); }  // Trim dates
    sort($my_dates);

//debuge($my_dates);
    $my_date = trim($my_dates[0]);
    $my_date = explode(' ', $my_date); $my_date = $my_date[0];
    if ( strpos($my_date,'-') !== false ) {
        $my_date = explode('-',$my_date);
        $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], $start_time[0], $start_time[1], $start_time[2] );
    } else {
        $my_date = explode('.',$my_date);
        $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[2], $my_date[1], $my_date[0], $start_time[0], $start_time[1], $start_time[2] );
    }
    $start_single_date = $date;

    $my_date = trim($my_dates[ (count($my_dates) - 1 )]);
    $my_date = explode(' ', $my_date); $my_date = $my_date[0];
    if ( strpos($my_date,'-') !== false ) {
        $my_date = explode('-',$my_date);
        $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], $end_time[0], $end_time[1], $end_time[2] );
    } else {
        $my_date = explode('.',$my_date);
        $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[2], $my_date[1], $my_date[0], $end_time[0], $end_time[1], $end_time[2] );
    }
    $fin_single_date = $date;

    

    $i=0;
    foreach ($my_dates as $my_date) {
        $my_date = trim($my_date);
        $my_date = explode(' ', $my_date); $my_date = $my_date[0];
        $i++;
        if ( strpos($my_date,'-') !== false ) {
            $my_dates4DB .= $my_date . ',';
        } else {
            // Loop through all dates
            if (strpos($my_date,'.')!==false) {
                $my_date = explode('.',$my_date);
                if ($i == 1) {  $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[2], $my_date[1], $my_date[0], $start_time[0], $start_time[1], $start_time[2] );
                } elseif ($i == count($my_dates)) { $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[2], $my_date[1], $my_date[0], $end_time[0], $end_time[1], $end_time[2] );
                } else {                            $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[2], $my_date[1], $my_date[0], '00', '00', '00' ); }
                $my_dates4DB .= $date . ',';
            }
        }
    }
    if (strpos($my_dates4DB,',')!==false) $my_dates4DB = substr($my_dates4DB,0,-1);
    ///////////////////////////////////////////////////////////////////////////////////

   $form_content_array = '';
   if ( function_exists ('get_form_content')) { $form_content_array = get_form_content($booking_form, $booking_type) ;  }  // TODO: check in Free version

   if ($form_content_array != '') {

        if ( ! isset($form_content_array['_all_']['email' . $booking_type ]) ) return;  // Email must be set if not set so return from here

        $booking_form .= "~wpdev_booking_dates^booking_dates^" . $my_dates4DB ;         // Add dates of reservation in correct format

        // ADD or SELECT    C u s t o m e r //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $my_sql   = "SELECT customer_id as id FROM ".$wpdb->prefix . "wpdev_crm_customers WHERE email = '".$form_content_array['_all_']['email' . $booking_type ]."'";
        $my_result = $wpdb->get_results( $my_sql );
        if (count($my_result)>0) {                            //Check if this customer already exist using EMAIL as uniq field
                $customer_id = (int) $my_result[0]->id;       // Customer is exist here is ID
        } else {                                              // Not Exist
                // Insert customer
                $customer_details_info = array();             // Get customer fields if they exist

                if ( isset($form_content_array['_all_']['name' . $booking_type ]) )  $customer_details_info[] = $form_content_array['_all_']['name' . $booking_type ];
                else  $customer_details_info[] = '';
                if ( isset($form_content_array['_all_']['secondname' . $booking_type ]) )  $customer_details_info[] = $form_content_array['_all_']['secondname' . $booking_type ];
                else  $customer_details_info[] = '';
                if ( isset($form_content_array['_all_']['email' . $booking_type ]) ) $customer_details_info[] = $form_content_array['_all_']['email' . $booking_type ];
                else  $customer_details_info[] = '';
                if ( isset($form_content_array['_all_']['phone' . $booking_type ]) ) $customer_details_info[] = $form_content_array['_all_']['phone' . $booking_type ];
                else  $customer_details_info[] = '';
                if ( isset($form_content_array['_all_']['address' . $booking_type ]) ) $customer_details_info[] = $form_content_array['_all_']['address' . $booking_type ];
                else  $customer_details_info[] = '';
                if ( isset($form_content_array['_all_']['city' . $booking_type ]) )  $customer_details_info[] = $form_content_array['_all_']['city' . $booking_type ];
                else  $customer_details_info[] = '';
                if ( isset($form_content_array['_all_']['country' . $booking_type ]) ) $customer_details_info[] = $form_content_array['_all_']['country' . $booking_type ];
                else  $customer_details_info[] = '';

                $customer_details_info[0] = str_replace("'", '', $customer_details_info[0]);
                $customer_details_info[1] = str_replace("'", '', $customer_details_info[1]);
                $customer_details_info[2] = str_replace("'", '', $customer_details_info[2]);
                $customer_details_info[3] = str_replace("'", '', $customer_details_info[3]);
                $customer_details_info[4] = str_replace("'", '', $customer_details_info[4]);
                $customer_details_info[5] = str_replace("'", '', $customer_details_info[5]);
                $customer_details_info[6] = str_replace("'", '', $customer_details_info[6]);
                $booking_form  = str_replace("'", '',  $booking_form );

                $insert = "('".$customer_details_info[0]."', '".$customer_details_info[1]."', '".$customer_details_info[2]."', '".$customer_details_info[3]."', '".$customer_details_info[4]."', '".$customer_details_info[5]."', '".$customer_details_info[6]."',
                            '".$booking_form."',
                            NOW()
                          )";
if(1)
                if ( !empty($insert) )
                    if ( false === $wpdb->query("INSERT INTO ".$wpdb->prefix .
                       "wpdev_crm_customers (name, second_name, email, phone, adress,  city,  country,  info, customer_date) VALUES " . $insert) ) {
                              ?> <script type="text/javascript"> document.getElementById('ajax_working').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php echo __('Error during inserting into BD', 'wpdev-crm'), ' wpdev_crm_customers'; ?></div>'; </script> <?php
//adebug($insert);
                              die();
                    }
                $customer_id = (int) $wpdb->insert_id;       //Get ID  of reservation
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


        // Insert    O R D E R ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //$my_order_date = 'NOW()';
        if (   $_POST['ajax_action_crm'] == 'IMPORT_BOOKINGS' ) { // Its mean import, so we set old YEAR
            $my_order_date = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d'), '1970') );
        } else {
            $my_order_date = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y')) );
        }


        $booking_form  = str_replace("'", '',  $booking_form );

        $insert = "('". $booking_form ."',
                    '". $customer_id ."',
                    '". serialize( array( 'booking'=>$booking_type ) ) ."',
                    '". $summ ."',
                    '".$my_order_date."',
                    '". $booking_id ."',
                     '".$my_dates4DB."',
                    '".$booking_type."',
                    '".$start_single_date."',
                    '".$fin_single_date."'
                  )";
if(1)
        if ( !empty($insert) )
            if ( false === $wpdb->query("INSERT INTO ".$wpdb->prefix .
               "wpdev_crm_orders (order_info, customer_id, type, cost, order_date, internal_id, internal_filters, internal_filters1, internal_filters2, internal_filters3) VALUES " . $insert) ) {
                    ?> <script type="text/javascript"> document.getElementById('ajax_working').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php echo __('Error during inserting into BD', 'wpdev-crm'), ' wpdev_crm_orders '; ?></div>'; </script> <?php
                    die();
            }
        $order_id = (int) $wpdb->insert_id;
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //return;
        if ( function_exists ('get_form_content')) {

            $content_fomr_show =  get_form_content($booking_form, $booking_type);
/*            $content_fomr_show = str_replace('<br/>', '', $content_fomr_show['content']);
            $content_fomr_show = str_replace('<br />', '', $content_fomr_show);
            $content_fomr_show = str_replace('"', '', $content_fomr_show);
            $content_fomr_show = str_replace("'", '', $content_fomr_show);
            $content_fomr_show = str_replace('/', '', $content_fomr_show);
            $content_fomr_show = str_replace('/n/', '', $content_fomr_show);/**/
            // Remove all shortcodes, which is not replaced early.
            $content_fomr_show = $content_fomr_show['content'];
            $content_fomr_show = preg_replace ('/[\s]{0,}\[[a-zA-Z0-9.,-_]{0,}\][\s]{0,}/', '', $content_fomr_show);
            //$content_fomr_show = preg_replace ('/[\s]*<[\s]*br[\s]*[\/]*[\s]*>[\s]*/', '', $content_fomr_show);
            $content_fomr_show = preg_replace ('/<br[\s]*[\/]*[\s]*>/', '', $content_fomr_show);
            $content_fomr_show = preg_replace ('/[\s]*\n[\s]*/', ' ', $content_fomr_show); // Remove all hided line breaks \n
//debuge( htmlentities($content_fomr_show) );
            if ( function_exists ('get_booking_title'))  $title = get_booking_title( $booking_type );
            else $title = 'Standard';
            return (array(
                'id'=> $order_id,
                'info' => $content_fomr_show,
                'customer' => $customer_details_info[0]." " .$customer_details_info[1],
                'type' => $title ,
                'date_of_order' => date('Y-m-d H:i:s'),
                'cost' => $summ
            ) );
        } else {
            return false;
        }
   }

}



?>
