<?php
/*
This is COMMERCIAL SCRIPT
We are do not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

if (  (! isset( $_GET['merchant_return_link'] ) ) && (! isset( $_GET['payed_booking'] ) ) && (!function_exists ('get_option')  )  ) { die('You do not have permission to direct access to this file !!!'); }
//require_once(WPDEV_BK_PLUGIN_DIR. '/inc/lib_m.php' );
if (file_exists(WPDEV_BK_PLUGIN_DIR. '/inc/biz_l.php')) { require_once(WPDEV_BK_PLUGIN_DIR. '/inc/biz_l.php' ); }

if (!class_exists('wpdev_bk_biz_m')) {
    class wpdev_bk_biz_m {

        var $wpdev_bk_biz_l;

        function wpdev_bk_biz_m(){
                    add_bk_action('wpdev_booking_activation', array($this, 'pro_activate'));
                    add_bk_action('wpdev_booking_deactivation', array($this, 'pro_deactivate'));

                    add_bk_action('wpdev_ajax_show_cost', array($this, 'wpdev_ajax_show_cost'));
                    add_bk_filter('wpdev_reapply_bk_form', array(&$this, 'wpdev_reapply_bk_form'));
                    add_bk_action('show_additional_shortcode_help_for_form', array($this, 'show_additional_shortcode_help_for_form'));

                    add_bk_filter('check_if_cost_exist_in_field', array(&$this, 'check_if_cost_exist_in_field'));
                     


                    // Resources settings //
                    add_bk_action('resources_settings_table_headers', array($this, 'resources_settings_table_headers'));
                    add_bk_action('resources_settings_table_footers', array($this, 'resources_settings_table_footers'));
                    add_bk_action('resources_settings_table_collumns', array($this, 'resources_settings_table_collumns'));

                    add_bk_action('wpdev_bk_booking_resource_page_before', array($this, 'wpdev_bk_booking_resource_page_before'));
                    
                    add_bk_action('wpdev_booking_resources_show_content', array(&$this, 'wpdev_booking_resources_show_content'));



                    add_bk_filter('wpdev_season_rates', array(&$this, 'apply_season_rates'));

                    add_bk_action('wpdev_booking_settings_show_content', array(&$this, 'settings_menu_content'));

                    add_bk_action('wpdev_booking_resources_top_menu', array($this, 'wpdev_booking_resources_top_menu'));



                    add_bk_action('show_settings_for_activating_fixed_deposit', array(&$this, 'show_settings_for_activating_fixed_deposit'));
                    add_bk_filter('fixed_deposit_amount_apply', array(&$this, 'fixed_deposit_amount_apply'));

                    add_bk_action('advanced_cost_management_settings', array(&$this, 'advanced_cost_management_settings'));
                    add_bk_filter('advanced_cost_apply', array(&$this, 'advanced_cost_apply'));


                    add_bk_filter('show_select_box_for_several_forms', array(&$this, 'show_select_box_for_several_forms'));
                    add_bk_action('update_booking_form_at_settings', array(&$this, 'update_booking_form_at_settings'));
                    add_bk_filter('wpdev_get_booking_form', array(&$this, 'wpdev_get_booking_form'));
                    add_bk_action('wpdev_show_bk_form_selection', array(&$this, 'wpdev_show_bk_form_selection'));
                    add_bk_action('wpdev_delete_booking_form', array(&$this, 'wpdev_delete_booking_form'));

                    add_bk_action('wpdev_show_booking_form_selection', array(&$this, 'wpdev_show_booking_form_selection'));



                    add_action('wpdev_bk_js_define_variables', array(&$this, 'js_define_variables') );      // Write JS variables
                    add_action('wpdev_bk_js_write_files', array(&$this, 'js_write_files') );                // Write JS files

                    add_filter('wpdev_booking_availability_filter', array(&$this, 'js_availability_filter') , 10, 2 );                // Write JS files
                    add_filter('wpdev_booking_show_rates_at_calendar', array(&$this, 'show_rates_at_calendar') , 10, 2 );                // Write JS files

                    add_bk_filter('get_unavailbale_dates_of_season_filters', array(&$this, 'get_unavailbale_dates_of_season_filters'));
                    add_bk_filter('is_this_day_available_on_season_filters', array(&$this, 'is_this_day_available_on_season_filters'));


                    add_action('settings_set_show_cost_in_tooltips', array(&$this, 'settings_set_show_cost_in_tooltips'));    // Write General Settings


                     add_bk_filter('wpdev_check_for_additional_calendars_in_form', array(&$this, 'wpdev_check_for_additional_calendars_in_form'));
                     add_bk_filter('check_cost_for_additional_calendars', array(&$this, 'check_cost_for_additional_calendars'));

                    if ( class_exists('wpdev_bk_biz_l')) {  $this->wpdev_bk_biz_l = new wpdev_bk_biz_l();
                    } else {                                $this->wpdev_bk_biz_l = false; }



                }


        
       // Possible to book many different items / rooms / facilties via. one form
       function wpdev_check_for_additional_calendars_in_form($form, $my_boook_type) {

            $calendars = array(); $cal_num = -1;$additional_calendars = '';
            while ( strpos($form, '[calendar') !== false ) { $cal_num++; $calendars[$cal_num] = array();
                 $cal_start = strpos($form, '[calendar');
                 $cal_end = strpos($form, ']' , $cal_start+1);

                 $new_cal = substr($form, ($cal_start+9),  ($cal_end - $cal_start-9) );
                 $new_cal = trim($new_cal);
                 $params = explode(' ', $new_cal);
                 foreach ($params as $param) {
                     $param = explode('=',$param);
                     $calendars[$cal_num][$param[0]] = $param[1];
                 }

                 
                 if (isset($calendars[$cal_num]['id'])) {

                     $bk_type = $calendars[$cal_num]['id'];
                     $additional_calendars .= $bk_type . ',';
                     $bk_cal  = '<div id="calendar_booking'.$bk_type.'">&nbsp;</div>';
                     $bk_cal .= '<textarea rows="3" cols="50" id="date_booking'.$bk_type.'" name="date_booking'.$bk_type.'" style="display:none;"></textarea>';   // Calendar code
                     $bk_cal .= '<input type="hidden" name="parent_of_additional_calendar'.$bk_type.'" id="parent_of_additional_calendar'.$bk_type.'" value="'.$my_boook_type.'" /> ';

                     
                     $additional_bk_types = array();
                     $my_selected_dates_without_calendar = '';
                     $my_boook_count =1;

                     $start_script_code = apply_bk_filter('get_script_for_calendar',$bk_type, $additional_bk_types, $my_selected_dates_without_calendar, $my_boook_count );


                     $form = substr_replace($form,  $bk_cal  .$start_script_code  , $cal_start, ($cal_end - $cal_start+1) );
                     //$form .= '<div  id="paypalbooking_form'.$bk_type.'"></div>';

                     //Todo: this element is add showhint elemnts, think how to make it in more good way, 2 lines above is added showhint shortcode
                     // its also not really correct thing
                     //$form = $this->wpdev_reapply_bk_form($form, $bk_type);     //cost hint
                 }

             }
             if (isset($additional_calendars))
                 if ($additional_calendars!=''){
                     $additional_calendars = substr($additional_calendars, 0, -1);
                     $form .= ' <input type="hidden" name="additional_calendars'.$my_boook_type.'" id="additional_calendars'.$my_boook_type.'" value="'.$additional_calendars.'" /> ';
                     
                 }




             return $form;
       }

       // Get Form with replaced old ID to new  one
       function get_bk_form_with_correct_id($bk_form, $correct_id,  $replace_id) {
           
                    $bk_form_arr = explode('~',$bk_form);
                    $formdata_additional = '';
                    for ($i = 0; $i < count($bk_form_arr); $i++) {
                        $my_form_field = explode( '^', $bk_form_arr[$i] );
                        if ($formdata_additional !=='') $formdata_additional .=  '~';

                         if ( substr( $my_form_field[1],  strlen($my_form_field[1]) -2 ,2) == '[]' )
                             $my_form_field[1] = substr( $my_form_field[1], 0, ( strlen($my_form_field[1]) - strlen('' .$replace_id)  ) - 2 ) . $correct_id . '[]';
                         else
                             $my_form_field[1] = substr( $my_form_field[1], 0, ( strlen($my_form_field[1]) - strlen('' .$replace_id)  )  ) . $correct_id  ;

                         $formdata_additional .= $my_form_field[0] . '^' . $my_form_field[1] . '^' . $my_form_field[2];
                    }

                    return $formdata_additional;
       }


       // Get total and costs for each other calendars, which are inside of this form
       function check_cost_for_additional_calendars($summ, $post_form, $post_bk_type,  $time_array , $is_discount_calculate = true ){

            $summ_total = $summ;

                // Check for additional calendars:
                $send_form_content = $post_form;
                $offset = 0;
                $summ_additional = array();
                $dates_additional = array();
                while ( strpos( $send_form_content , 'textarea^date_booking' , $offset) !== false ) {
                    $offset = strpos( $send_form_content , 'textarea^date_booking' , $offset)+1;
                    $offset_end = strpos( $send_form_content , '^' , $offset+20);
                    $other_bk_id = substr($send_form_content, $offset+20, $offset_end - $offset -20 ) ;                             // ID

                    $offset_end_dates_data = strpos( $send_form_content , '~' ,  $offset_end );
                    $other_bk_dates = substr($send_form_content, $offset_end+1 , $offset_end_dates_data - $offset_end-1  );         // Dates

                    // Replace inside of form old ID to the new correct ID
                    $send_form_content = $this->get_bk_form_with_correct_id($send_form_content, $other_bk_id,  $post_bk_type );   //Form

                    if (empty($other_bk_dates) ) $summ_add = 0;
                    else $summ_add = apply_bk_filter('wpdev_get_bk_booking_cost', $other_bk_id , $other_bk_dates , $time_array , $send_form_content , $is_discount_calculate );
                    $summ_add = floatval( $summ_add );
                    $summ_add = round($summ_add,2);
                    $summ_additional[ $other_bk_id ]= $summ_add;
                    $dates_additional[ $other_bk_id ]= $other_bk_dates;

                    $send_form_content = $post_form;
                }

//debuge($summ, $summ_additional);
                foreach ($summ_additional as $ss) { $summ_total += $ss; }           // Summ all costs

//debuge(array($summ_total, $summ_additional, $dates_additional));
            return array($summ_total, $summ_additional, $dates_additional) ;

       }


 // S U P P O R T       F u n c t i o n s    //////////////////////////////////////////////////////////////////////////////////////////////////

        // Reset to Payment form
        function reset_to_default_form($form_type ){
               return '[calendar] \n\
<div style="text-align:left;line-height:28px;"><p>'. __('The cost for payment', 'wpdev-booking').': [cost_hint]</p></div>   \n\
<div style="text-align:left"> \n\
[cost_corrections] \n\
<p>'. __('Start time', 'wpdev-booking').': [starttime]  '. __('End time', 'wpdev-booking').': [endtime]</p> \n\
\n\
<p>'. __('First Name (required)', 'wpdev-booking').':<br />  [text* name] </p> \n\
\n\
<p>'. __('Last Name (required)', 'wpdev-booking').':<br />  [text* secondname] </p> \n\
\n\
<p>'. __('Email (required)', 'wpdev-booking').':<br />  [email* email] </p> \n\
\n\
<p>'. __('Address (required)', 'wpdev-booking').':<br />  [text* address] </p>  \n\
 \n\
<p>'. __('City(required)', 'wpdev-booking').':<br />  [text* city] </p>  \n\
 \n\
<p>'. __('Post code(required)', 'wpdev-booking').':<br />  [text* postcode] </p>  \n\
 \n\
<p>'. __('Country(required)', 'wpdev-booking').':<br />  [country] </p>  \n\
 \n\
<p>'. __('Phone', 'wpdev-booking').':<br />  [text phone] </p> \n\
\n\
<p>'. __('Visitors', 'wpdev-booking').':<br />  [select visitors "1" "2" "3" "4"] '. __('Children', 'wpdev-booking').': [checkbox children ""]</p> \n\
\n\
<p>'. __('Details', 'wpdev-booking').':<br /> [textarea details] </p> \n\
\n\
<p>[captcha]</p> \n\
\n\
<p>[submit "'. __('Send', 'wpdev-booking').'"]</p> \n\
</div>';
         }


        // write JS variables
        function js_define_variables(){
              ?>
                    <script  type="text/javascript">
                        var bk_cost_depends_from_selection_line1 = '<?php echo get_bk_option( 'booking_paypal_curency' ). ' '.   esc_js(__('per 1 day','wpdev-booking')); ?>';
                        var bk_cost_depends_from_selection_line2 = '<?php echo '% '. esc_js(__('from the cost of 1 day ','wpdev-booking')); ?>';
                        var bk_cost_depends_from_selection_line3 = '<?php echo sprintf( esc_js(__('Additional cost in %s per 1 day','wpdev-booking')),get_bk_option( 'booking_paypal_curency' )); ?>';

                        var bk_cost_depends_from_selection_line14summ = '<?php echo get_bk_option( 'booking_paypal_curency' ). ' '.   esc_js(__(' for all days!','wpdev-booking')); ?>';
                        var bk_cost_depends_from_selection_line24summ = '<?php echo '% '. esc_js(__(' for all days!','wpdev-booking')); ?>';

                    </script>
            <?php /**/

        }

        // write JS Scripts
        function js_write_files(){
              wp_enqueue_script ('biz_m', WPDEV_BK_PLUGIN_URL . '/inc/js/biz_m.js');
        }

        // Check if table exist
        function is_table_exists( $tablename ) {
            global $wpdb;
            if (strpos($tablename, $wpdb->prefix) ===false) $tablename = $wpdb->prefix . $tablename ;
            $sql_check_table = "
                SELECT COUNT(*) AS count
                FROM information_schema.tables
                WHERE table_schema = '". DB_NAME ."'
                AND table_name = '" . $tablename . "'";

            $res = $wpdb->get_results($wpdb->prepare($sql_check_table));
            return $res[0]->count;

        }

        // Check if table exist
        function is_field_in_table_exists( $tablename , $fieldname) {
            global $wpdb;
            if (strpos($tablename, $wpdb->prefix) ===false) $tablename = $wpdb->prefix . $tablename ;
            $sql_check_table = "SHOW COLUMNS FROM " . $tablename ;

            $res = $wpdb->get_results($wpdb->prepare($sql_check_table));

            foreach ($res as $fld) {
                if ($fld->Field == $fieldname) return 1;
            }
            return 0;
        }
        // Check if index exist
        function is_index_in_table_exists( $tablename , $fieldindex) {
            global $wpdb;
            if (strpos($tablename, $wpdb->prefix) ===false) $tablename = $wpdb->prefix . $tablename ;
            $sql_check_table = "SHOW INDEX FROM ". $tablename ." WHERE Key_name = '".$fieldindex."'; ";
            $res = $wpdb->get_results($wpdb->prepare($sql_check_table));
            if (count($res)>0) return 1;
            else               return 0;
        }


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        function get_default_booking_form($bk_type){
            global $wpdb;
            $res_view_max = $wpdb->get_results($wpdb->prepare( "SELECT default_form FROM ".$wpdb->prefix ."bookingtypes  WHERE booking_type_id = " .  $bk_type ));
            $default_form =  $res_view_max[0]->default_form;
            if ($default_form == '') return 'standard';
            else return $default_form;
        }


         // Just Get ALL booking types from DB
        function get_standard_cost_for_bk_resource($booking_type_id = 0) {

            $res = $this->get_booking_types($booking_type_id);

            if (count($res)>0) {
                return $res[0]->cost;
            } else return 0;

        }

        // Just Get ALL booking types from DB
        function get_booking_types($booking_type_id = 0) {
            global $wpdb;
            $max_stringg_sql='';
            $order_type = 'title';
            
            if ( class_exists('wpdev_bk_biz_l')) {  // If Business Large then get resources from that
                $types_list = apply_bk_filter('get_booking_types_hierarhy_linear',array() );
                //$types_list = apply_bk_filter('multiuser_resource_list', $types_list);

                for ($i = 0; $i < count($types_list); $i++) {
                    $types_list[$i]['obj']->count = $types_list[$i]['count'];
                    $types_list[$i] = $types_list[$i]['obj'];
                    if ( (isset($booking_type_id)) &&(isset($types_list[$i]->booking_type_id)) && ($booking_type_id != 0) && ($booking_type_id == $types_list[$i]->booking_type_id ) ) return $types_list[$i];
                }
                if ($booking_type_id == 0) return $types_list;
            }

            if ($booking_type_id == 0 ) {  // Normal getting
                $types_list = $wpdb->get_results($wpdb->prepare( "SELECT booking_type_id as id, title, cost".$max_stringg_sql." FROM ".$wpdb->prefix ."bookingtypes  ORDER BY " . $order_type ));
            } else {
                $types_list = $wpdb->get_results($wpdb->prepare( "SELECT booking_type_id as id, title, cost".$max_stringg_sql." FROM ".$wpdb->prefix ."bookingtypes  WHERE booking_type_id = ". $booking_type_id ));
            }
            //$types_list = apply_bk_filter('multiuser_resource_list', $types_list);
            
            return $types_list;
        }

        // Get meta data from booking type
        function get_bk_type_meta($type_id, $meta_key){
            global $wpdb;
            $result = $wpdb->get_results($wpdb->prepare( "SELECT meta_id as id, meta_value as value FROM ".$wpdb->prefix ."booking_types_meta
                WHERE type_id = " .  $type_id . " AND meta_key ='".$meta_key ."'"));
            return $result;
        }

        // Set meta data from booking type
        function set_bk_type_meta($type_id, $meta_key, $meta_value){
            global $wpdb;

            $result = $wpdb->get_results($wpdb->prepare( "SELECT count(type_id) as cnt FROM ".$wpdb->prefix ."booking_types_meta
                 WHERE type_id = " .  $type_id . " AND meta_key ='".$meta_key."'" ));
//debuge($type_id, $meta_key, $meta_value, $result);
            if ( $result[0]->cnt > 0 ) {
                if ( false === $wpdb->query(( "UPDATE ".$wpdb->prefix ."booking_types_meta SET meta_value = '".$meta_value."' WHERE type_id = " .  $type_id . " AND meta_key ='".$meta_key."'") ) ){
//debuge($type_id,$meta_key, $meta_value);
                   bk_error('Error during updating to DB booking availability of booking resource',__FILE__,__LINE__ );
                   return false;
                }
            } else {
                if ( false === $wpdb->query(( "INSERT INTO ".$wpdb->prefix ."booking_types_meta ( type_id, meta_key, meta_value) VALUES ( " .  $type_id . ", '" .  $meta_key . "', '" .  $meta_value . "' );") ) ){
//debuge($type_id,$meta_key, $meta_value);
                    bk_error('Error during updating to DB booking availability of booking resource' ,__FILE__,__LINE__);
                   return false;
                }
            }
            return true;
        }

        //Get available days depends from seaosn filter
        function get_available_days( $type_id ){
            $filters = array(); global $wpdb;
            $return_result = array('available'=>true,'days'=> $filters ) ;

            $availability_res = $this->get_bk_type_meta($type_id,'availability');
            if ( count($availability_res)>0 ) {
                if ( is_serialized( $availability_res[0]->value ) )   $availability = unserialize($availability_res[0]->value);
                else                                                  $availability = $availability_res[0]->value;

                $days_avalaibility = $availability['general'];
                $seasonfilter      = $availability['filter'];
                if (is_array($seasonfilter))
                    foreach ($seasonfilter as $key => $value) {
                        if ($value == 'On') {
                            $result = $wpdb->get_results($wpdb->prepare( "SELECT filter FROM ".$wpdb->prefix ."booking_seasons WHERE booking_filter_id = " . $key ));
                            foreach($result as $filter) {
                                if ( is_serialized( $filter->filter ) )    $filter = unserialize($filter->filter);
                                else                                                   $filter = $filter->filter;
                                $filters[]=$filter;
                            }
                        }
                    }
            }
              else  $days_avalaibility = 'On';


            if ( $days_avalaibility == 'On' ) $return_result['available'] = true;
            else                              $return_result['available'] = false;
            $return_result['days'] = $filters;
            //debuge($return_result);
            return $return_result;
        }

        // Set available and unavailable days into calendar form using JS variables.
        function js_availability_filter($blank, $type_id ) { $script = '';
            $res_days = $this->get_available_days( $type_id );
//debuge($res_days);

            $script .= ' is_all_days_available['.$type_id.'] = ' . ($res_days['available']+0) . '; ';
            $script .= ' avalaibility_filters['.$type_id.'] = []; ';

            foreach ($res_days['days'] as $value) { // loop all assign filters


                $value_js =  '[ [';
                foreach ($value['weekdays'] as $key => $val) { if ($val == 'On') $value_js .= $key . ', '; }// loop week days
                $value_js =  substr($value_js, 0, -2); //Delete last ", "
                $value_js .=  '], [';
                foreach ($value['days'] as $key => $val) { if ($val == 'On') $value_js .= $key . ', '; }// loop all days numbers
                $value_js =  substr($value_js, 0, -2); //Delete last ", "
                $value_js .=  '], [';
                foreach ($value['monthes'] as $key => $val) { if ($val == 'On') $value_js .= $key . ', '; }// loop all monthes nums
                $value_js =  substr($value_js, 0, -2); //Delete last ", "
                $value_js .=  '], [';
                foreach ($value['year'] as $key => $val) { if ($val == 'On') $value_js .= $key . ', '; } // loop all years nums
                $value_js =  substr($value_js, 0, -2); //Delete last ", "
                $value_js .=  '] ]';

                // Time availability

                if (  (! empty($value['start_time']))  &&   (! empty($value['end_time']))  )  {
                    $strt_time = explode(':',$value['start_time']);
                    $fin_time = explode(':',$value['end_time']);
                    $script .= ' if(typeof( global_avalaibility_times['.$type_id.']) == "undefined") {  global_avalaibility_times['.$type_id.'] = [];  }';

                    

                    if (  (count($res_days['days']) ==1 ) &&  (($res_days['available']+0) == 0)  ){
                        $script .= ' is_all_days_available['.$type_id.'] = ' . 1 . '; '; // Set all days available
                        // set start unavailable hours
                        $script .= ' global_avalaibility_times['.$type_id.'][ global_avalaibility_times['.$type_id.'].length ]= [ ["00","00"],  ["'.$strt_time[0] . '", "'.$strt_time[1] . '"]  ]; ';
                        // set end unavailable hours
                        $script .= ' global_avalaibility_times['.$type_id.'][ global_avalaibility_times['.$type_id.'].length ]= [ ["'.$fin_time[0] . '", "'.$fin_time[1] . '"], ["23","59"]  ]; ';

                    } else
                      $script .= ' global_avalaibility_times['.$type_id.'][ global_avalaibility_times['.$type_id.'].length ]= [ ["'.$strt_time[0] . '", "'.$strt_time[1] . '"],  ["'.$fin_time[0] . '", "'.$fin_time[1] . '"]  ]; ';

                } else 
                 $script .= ' avalaibility_filters['.$type_id.'][ avalaibility_filters['.$type_id.'].length ]= '.$value_js . '; ';
            }


            return $script;//' alert('.$type_id.'); ';
        }



        // Check  if this date available for specific booking resource, depend from the season filter.
        function is_this_day_available_on_season_filters( $blank, $date, $bk_type){

            $season_filters = $this->get_available_days( $bk_type );

            $date_obj = getdate( strtotime($date) );

            $is_all_days_available = $season_filters['available'];
            $season_filters_dates = $season_filters['days'];

            $is_day_inside_filters = false ;

            for ($filter_num = 0; $filter_num < count($season_filters_dates); $filter_num++) {

                $is_day_inside_filter = '';
                if (  $season_filters_dates[$filter_num]['weekdays'][ $date_obj['wday'] ] == 'On' ) $is_day_inside_filter .= 'week ';
                if (  $season_filters_dates[$filter_num]['days'][ $date_obj['mday'] ] == 'On' )     $is_day_inside_filter .= 'day ';
                if (  $season_filters_dates[$filter_num]['monthes'][ $date_obj['mon'] ] == 'On' )   $is_day_inside_filter .= 'month ';
                if (  $season_filters_dates[$filter_num]['year'][ $date_obj['year'] ] == 'On' )     $is_day_inside_filter .= 'year ';
                if ($is_day_inside_filter == 'week day month year ') {$is_day_inside_filters = true; break;}
            }

            if ($is_day_inside_filters) {
                if ($is_all_days_available) return false;
                else                        return true;
            } else {
                if ($is_all_days_available) return true;
                else                        return false;
            }
     }

        function get_unavailbale_dates_of_season_filters($blank, $type_id ){
            $res_days = $this->get_available_days( $type_id );
return($res_days);

            $script .= ' is_all_days_available['.$type_id.'] = ' . ($res_days['available']+0) . '; ';
            $script .= ' avalaibility_filters['.$type_id.'] = []; ';

            foreach ($res_days['days'] as $value) { // loop all assign filters


                $value_js =  '[ [';
                foreach ($value['weekdays'] as $key => $val) { if ($val == 'On') $value_js .= $key . ', '; }// loop week days
                $value_js =  substr($value_js, 0, -2); //Delete last ", "
                $value_js .=  '], [';
                foreach ($value['days'] as $key => $val) { if ($val == 'On') $value_js .= $key . ', '; }// loop all days numbers
                $value_js =  substr($value_js, 0, -2); //Delete last ", "
                $value_js .=  '], [';
                foreach ($value['monthes'] as $key => $val) { if ($val == 'On') $value_js .= $key . ', '; }// loop all monthes nums
                $value_js =  substr($value_js, 0, -2); //Delete last ", "
                $value_js .=  '], [';
                foreach ($value['year'] as $key => $val) { if ($val == 'On') $value_js .= $key . ', '; } // loop all years nums
                $value_js =  substr($value_js, 0, -2); //Delete last ", "
                $value_js .=  '] ]';
            }


        }

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // B o o k i n g   F O R M S    customization
        // ////////////////////////////////////////////////
        // Show select box for selection of several booking forms at the settings page of form fields customisation page
        function show_select_box_for_several_forms($content_temp){ //return;

            $booking_forms_extended = get_bk_option( 'booking_forms_extended');
            if ($booking_forms_extended !== false) {
                if ( is_serialized( $booking_forms_extended ) )    $booking_forms_extended = unserialize($booking_forms_extended);

            } else {
                $booking_forms_extended = array();
                //debuge('Ha ha');
            }
            $booking_form_content = '';
            $booking_form_content = get_bk_option( 'booking_form' );
            ?>
<?php /** ?>
              <div style="float:left;">
                  <label style="font-weight:bold;" ><?php _e('Select Booking Form for customization or add new one', 'wpdev-booking'); ?>:</label>
                  <select style="width:155px;" name="select_booking_form" id="select_booking_form" onchange="javascript:changeBookingForm(this);">
                        <option value="+"       <?php if ( true ) {  echo 'selected="selected"'; } ?>  ><?php _e('Add new booking form', 'wpdev-booking'); ?></option>
                        <option value="standard"       <?php if ( ($_GET['booking_form'] == 'standard') || (! isset($_GET['booking_form'])) ) { $booking_form_content = get_bk_option( 'booking_form' );  echo 'selected="selected"'; } ?>  ><?php _e('Standard', 'wpdev-booking'); ?></option>
                        <?php
                        foreach ($booking_forms_extended as $value) { ?>
                            <option value="<?php echo $value['name']; ?>"       <?php if ( ($_GET['booking_form'] == $value['name'] ) ) { $booking_form_content = $value['form'];  echo 'selected="selected"'; } ?>  ><?php echo $value['name']; ?></option>
                        <?php  }
                        ?>
                  </select>
              </div>
              <!--div id="new_booking_form" style="float:left;margin:0px 10px;display:none;">
                    <div for="booking_form_new_name"  style="width:150px;padding:11px 0;" class="inside_hint"><?php printf(__('%sForm%s name', 'wpdev-booking'),'<b>','</b>'); ?><br/></div>
                    <input type="text" style="width:150px;margin:2px;" class="has-inside-hint" name="booking_form_new_name" id="booking_form_new_name" value="">
                    <span class="description"><?php printf(__('Type your name for %snew booking form%s here', 'wpdev-booking'),'<b>','</b>'); ?></span>
              </div-->
              <div style="clear:both;border-bottom: 1px solid #ccc;width:100%;height:1px;margin:0px 0px;"></div>
<?php /**/ ?>
              <div style="height:auto;">
                    <?php if ( (! isset($_GET['booking_form'])) || ($_GET['booking_form'] == 'standard') ) { $selected_bk_typenew = ' selected_bk_typenew '; } else { $selected_bk_typenew = ''; } ?>
                    <div id="bktype0" class="topmenuitemborder <?php echo $selected_bk_typenew; ?>">
                        <?php echo '<a href="admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME . 'wpdev-booking-option&tab=form&booking_form=standard" class="bktypetitlenew '.$selected_bk_typenew.' ">' .  __('Standard', 'wpdev-booking')  . '</a>'; ?>
                    </div>


                  <?php $bkf = 0 ; foreach ($booking_forms_extended as $value) { $bkf++;?>

                  <?php if ( (isset($_GET['booking_form']) ) && ($_GET['booking_form'] == $value['name'] ) ) { $selected_bk_typenew = ' selected_bk_typenew ';  $booking_form_content = $value['form']; } else { $selected_bk_typenew = ''; } ?>

                    <div id="bktype<?php echo $bkf; ?>" class="topmenuitemborder <?php echo $selected_bk_typenew; ?>">
                        <?php echo '<a href="admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME . 'wpdev-booking-option&tab=form&booking_form=' . $value['name']. '" class="bktypetitlenew '.$selected_bk_typenew.' ">' .  $value['name']  . '</a>'; ?>
                        <?php echo ' <a href="#" class="bktype_delete"  title="'. __('Delete', 'wpdev-booking') .'" style="text-decoration:none;" onclick="javascript:delete_bk_form(\''. $value['name'].'\');"><img src="'.WPDEV_BK_PLUGIN_URL.'/img/delete_type.png" width="8" height="8"   /></a>'; ?>
                    </div>

                  <?php  } ?>


                    <div class="topmenuitemseparator"></div>
                    <div class="topmenuitemborder topmenuitemborder_plus" id="bk_form_plus" style="">
                        <?php echo '<a class="bktypetitlenew" href="#" onMouseDown="addBKForm(\'Plus\');"  title="'.__('Add new booking form','wpdev-booking').'"     >' .  '+'  . '</a>'; ?>
                    </div>
                     <div style="float:left;border:0px dotted green;display:none;line-height:32px;" id="bk_form_addbutton">
                        <input type="text" name="booking_form_new_name" id="booking_form_new_name" class="add_type_field"  value="" />
                        <input  type="button" class="button-secondary" onclick="javascript:document.getElementById('post_option').submit();" value=" <?php _e('Add', 'wpdev-booking'); ?> " />
                    </div>

              </div>
              <div class="clear topmenuitemseparatorv" style="height:0px;clear:both;" ></div>


            <?php

            return $booking_form_content;
        }

        // Update at settings page of form fields customisation page -- booking form(s)
        function update_booking_form_at_settings(){ //return;
//debuge($_POST);
            $booking_form = $new_name ='';
            if ( isset($_GET['booking_form'])  )         $new_name = $_GET['booking_form'];
            if ( (isset($_POST['booking_form_new_name'])) && (! empty($_POST['booking_form_new_name'])) )  $new_name = $_POST['booking_form_new_name'];
//debuge($new_name)            ;
            if (isset($_POST['booking_form'])) {
                     $booking_form =  ($_POST['booking_form']);
                     $booking_form = str_replace('\"','"',$booking_form);
                     $booking_form = str_replace("\'","'",$booking_form);
            }

            if ( ( ! empty($new_name) ) && ( ! empty($new_name) ) ) {
                $booking_forms_extended = get_bk_option( 'booking_forms_extended');
                if ($booking_forms_extended !== false) {
                    if ( is_serialized( $booking_forms_extended ) ) $booking_forms_extended = unserialize($booking_forms_extended);
//debuge($booking_forms_extended);
                    $i = 0;
                    // Check already exist names for rewrite it
                    foreach ($booking_forms_extended as $value) {
                        if ($value['name'] == $new_name){
                            $booking_forms_extended[$i]['form'] = $booking_form;
                            $i = 'modified';
                            break;
                        } $i++;
                    }
                    if ($i !== 'modified') {  // add new booking form
                        $booking_forms_extended[count($booking_forms_extended)] = array('name'=>$new_name, 'form'=>$booking_form);
                    }

                } else {
                    $booking_forms_extended = array( array('name'=>$new_name, 'form'=>$booking_form) );
                }


                update_bk_option( 'booking_forms_extended' , serialize($booking_forms_extended) );
            }
        }

        // Delete specific booking form
        function wpdev_delete_booking_form(){
            if (isset($_POST['formname'])) {
                $form_name = $_POST['formname'];

                $booking_forms_extended = get_bk_option( 'booking_forms_extended');
                if ($booking_forms_extended !== false) {
                    if ( is_serialized( $booking_forms_extended ) ) $booking_forms_extended = unserialize($booking_forms_extended);

                    $booking_forms_extended_new = array();
                    // Check already exist names for rewrite it
                    foreach ($booking_forms_extended as $value) {

                        if ($value['name'] == $form_name){   continue;  //skip it
                        } else {                             $booking_forms_extended_new[] = $value; }

                    }
                    update_bk_option( 'booking_forms_extended' , serialize($booking_forms_extended_new) );
                    ?>
                    <script type="text/javascript">
                        document.getElementById('ajax_message').innerHTML = '<?php echo __('Deleted', 'wpdev-booking'); ?>';
                        jQuery('#ajax_message').fadeOut(1000);
                        window.location.href='<?php echo 'admin.php?page='.WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME .'wpdev-booking-option&tab=form&booking_form=standard';?>';
                    </script> <?php
                } else {
                    ?>
                    <script type="text/javascript">
                        document.getElementById('ajax_message').innerHTML = '<?php echo __('There are no extended booking forms', 'wpdev-booking'); ?>';
                        jQuery('#ajax_message').fadeOut(1000);
                        window.location.href='<?php echo 'admin.php?page='.WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME .'wpdev-booking-option&tab=form&booking_form=standard';?>';
                    </script> <?php
                }
            }
        }

        // Get Booking form content
        function wpdev_get_booking_form($booking_form_def_value, $my_booking_form_name){

                $booking_forms_extended = get_bk_option( 'booking_forms_extended');

                if ($booking_forms_extended !== false) {
                    if ( is_serialized( $booking_forms_extended ) ) $booking_forms_extended = unserialize($booking_forms_extended);
//debuge($my_booking_form_name, $booking_forms_extended)                    ; die;
                    // Check already exist names for rewrite it
                    foreach ($booking_forms_extended as $value) {
                        if ($value['name'] == $my_booking_form_name){
                            return $value['form'];
                        }
                    }
                }

            return $booking_form_def_value;
        }

        // Show selection box at the insertion shorcode modal window
        function wpdev_show_bk_form_selection(){

                $booking_forms_extended = get_bk_option( 'booking_forms_extended');

                if ($booking_forms_extended !== false) {
                    if ( is_serialized( $booking_forms_extended ) ) $booking_forms_extended = unserialize($booking_forms_extended);
                    // Check already exist names for rewrite it


                        ?>
                        <div class="field">
                            <div style="float:left;">
                            <label for="calendar_type"><?php _e('Booking form type:', 'wpdev-booking'); ?></label>
                            <!--input id="calendar_type"  name="calendar_type" class="input" type="text" -->
                            <select id="booking_form_type" name="booking_form_type">
                                <option value="standard"><?php _e('Standard','wpdev-booking'); ?></option>
                            <?php foreach ($booking_forms_extended as $value) { ?>
                                <option value="<?php echo $value['name']; ?>"><?php echo $value['name']; ?></option>
                            <?php } ?>
                            </select>
                            </div>
                            <div class="description"><?php _e('Please, select your type of booking form', 'wpdev-booking'); ?></div>
                        </div>
            <?php
            }
        }


        function wpdev_show_booking_form_selection(){
            $booking_forms_extended = get_bk_option( 'booking_forms_extended');

            $link_base = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME . 'wpdev-booking-reservation' ;

            if (isset($_GET['booking_hash'])) $link_base .= '&booking_hash=' . $_GET['booking_hash']  ;
            if (isset($_GET['parent_res'])) $link_base .= '&parent_res=' . $_GET['parent_res']  ;



            if (isset($_GET['booking_type']))
                if ($_GET['booking_type'] > 0 )
                $link_base .= '&booking_type=' . $_GET['booking_type']  ;

            $link_base .= '&booking_form=' ;

            if ($booking_forms_extended !== false) {
                if ( is_serialized( $booking_forms_extended ) ) $booking_forms_extended = unserialize($booking_forms_extended);
            ?>
                <div style="float:left;margin:0px 10px;line-height: 32px;font-size:13px;font-weight: bold;text-shadow:0px -1px 0px #fff;color:#555;">
                    <label for="calendar_type"><?php _e('Booking form:', 'wpdev-booking'); ?></label>
                    <select id="booking_form_type" name="booking_form_type" style="width:200px;"
                        onchange="javascript: location.href='<?php echo $link_base; ?>' + this.value;"

                            >
                        <option value="standard"><?php _e('Standard','wpdev-booking'); ?></option>
                        <?php foreach ($booking_forms_extended as $value) { ?>
                        <option value="<?php echo $value['name']; ?>"   <?php if ((isset($_GET['booking_form'])) && ($_GET['booking_form'] == $value['name']) ) { echo ' selected="SELECTED" '; } ?>   ><?php echo $value['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php
            }
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Check if this day inside of filter  , return TRUE  or FALSE   or   array( 'hour', 'start_time', 'end_time']) if HOUR filter this FILTER ID
        function is_day_inside_of_filter($day , $month, $year, $filter_id){
            $week_day_num =  date('w', mktime(0, 0, 0, $month, $day, $year) );
            $weekdays = array(); $days = array(); $monthes =  array(); $years = array();
            global $wpdb;
            $result = $wpdb->get_results($wpdb->prepare( "SELECT filter FROM ".$wpdb->prefix ."booking_seasons WHERE booking_filter_id = " . $filter_id ));
            if (count($result)>0){
                foreach($result as $filter) {
                    if ( is_serialized( $filter->filter ) ) $filter = unserialize($filter->filter);
                    else $filter = $filter->filter ;
                    }

                foreach ($filter['weekdays'] as $key => $value) {
                    if ($value == 'On')  $weekdays[] = $key;
                }
                foreach ($filter['days'] as $key => $value) {
                    if ($value == 'On')  $days[] = $key;
                }
                foreach ($filter['monthes'] as $key => $value) {
                    if ($value == 'On')  $monthes[] = $key;
                }
                foreach ($filter['year'] as $key => $value) {
                    if ($value == 'On')  $years[] = $key;
                }
                if ( ( ! empty($filter['start_time'])) && ( ! empty($filter['end_time'])) ) {
                    // Its hourly filter, so its apply to all days
                    return array( 'hour', $filter['start_time'], $filter['end_time']);
                }
                if ( ! in_array($week_day_num, $weekdays) ) return false;  // there are no in filter
                if ( ! in_array($day, $days) )              return false;
                if ( ! in_array($month, $monthes) )         return false;
                if ( ! in_array($year, $years) )            return false;

                return true; // Its inside of filter
            }
            return false;    // there are no filter so not inside of filter
        }



 // C O S T   H I N T    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Check if total cost field exist, and if its exist get cost from it
        function check_if_cost_exist_in_field( $blank , $formmy, $booking_type ){

            $form_elements = get_form_content ($formmy, $booking_type);
            if (isset($form_elements['_all_']))
               if (isset($form_elements['_all_']['total_bk_cost' . $booking_type ])) {
                    $fin_cost = $form_elements['_all_']['total_bk_cost' . $booking_type ];
                    return $fin_cost;
               }

            return false;
        }


        // Set fields inside of form for editing total cost
        function wpdev_reapply_bk_form_for_cost_input($return_form, $bk_type){
                       
            $my_form = '';

            if ( strpos($_SERVER['REQUEST_URI'],'wpdev-booking.phpwpdev-booking-reservation')!==false ) {
                $my_form =  '<div id="show_edit_cost_fields"><p><div class="legendspan">'.__('Standard booking resource cost','wpdev-booking') . ':</div> '. '<input type="text" disabled="disabled" value="'.$this->get_standard_cost_for_bk_resource($bk_type).'" id="standard_bk_cost'.$bk_type.'"  name="standard_bk_cost'.$bk_type.'" /></p>';
                $my_form .= '<p><div class="legendspan">'.__('Total booking resource cost','wpdev-booking') . ':</div>  '. '<input type="text" value="0" id="total_bk_cost'.$bk_type.'"  name="total_bk_cost'.$bk_type.'" /></p>';
                $my_form .= '<script>jQuery(document).ready( function(){ if(typeof( showCostHintInsideBkForm ) == "function") { show_cost_init=setTimeout(function(){ showCostHintInsideBkForm('.$bk_type.'); },2500);  } });</script></div>';
            }
            $return_form = str_replace('[cost_corrections]', $my_form, $return_form);
            
            return $return_form ;
        }


        // Check the form according show Hint and modificate it
        function wpdev_reapply_bk_form($return_form, $bk_type){

            $cost_currency = get_bk_option( 'booking_paypal_curency' );
            if ($cost_currency == 'USD' ) $cost_currency = '$';
            elseif ($cost_currency == 'EUR' ) $cost_currency = '&euro;';
            elseif ($cost_currency == 'GBP' ) $cost_currency = '&#163;';
            elseif ($cost_currency == 'JPY' ) $cost_currency = '&#165;';


            $return_form = str_replace('[cost_hint]', '<span id="booking_hint'.$bk_type.'">'.$cost_currency.' 0.00</span>', $return_form);
            $return_form = str_replace('[original_cost_hint]', '<span id="original_booking_hint'.$bk_type.'">'.$cost_currency.' 0.00</span>', $return_form);
            $return_form = str_replace('[additional_cost_hint]', '<span id="additional_cost_hint'.$bk_type.'">'.$cost_currency.' 0.00</span>', $return_form);

            $return_form = $this->wpdev_reapply_bk_form_for_cost_input($return_form, $bk_type);

            return $return_form ;
        }

        // Ajax function call, for showing cost
        function wpdev_ajax_show_cost(){

            make_bk_action('check_multiuser_params_for_client_side', $_POST[ "bk_type"] );

// TODO: Set for multiuser - user ID (ajax request do not transfear it
//$this->client_side_active_params_of_user

            $cost_currency = apply_bk_filter('get_currency_info', 'paypal');
            $sdform = $_POST['form'];
            $dates = $_POST[ "all_dates" ];

            if (strpos($dates,' - ')!== FALSE) {
                $dates =explode(' - ', $dates );
                $dates = createDateRangeArray($dates[0],$dates[1]);
            }
            $my_dates = explode(", ",$dates);

            $start_end_time = get_times_from_bk_form($sdform, $my_dates, $_POST[ "bk_type"] );
            $start_time = $start_end_time[0];
            $end_time = $start_end_time[1];
            $my_dates = $start_end_time[2];

            // Get cost of main calendar with all rates discounts and  so on...
            $summ = apply_filters('wpdev_get_booking_cost', $_POST['bk_type'], $dates, array($start_time, $end_time ), $_POST['form'] );
            $summ = floatval( $summ );
            $summ = round($summ,2);

            $summ_original = apply_bk_filter('wpdev_get_bk_booking_cost', $_POST['bk_type'], $dates, array($start_time, $end_time ), $_POST['form'], true , true );
            $summ_original = floatval( $summ_original );
            $summ_original = round($summ_original,2);
             

            // Get description according coupons discount for main calendar if its exist
            $coupon_info_4_main_calendar = apply_bk_filter('wpdev_get_additional_description_about_coupons', '', $_POST['bk_type'], $dates, array($start_time, $end_time ), $_POST['form']   );





            // Check additional cost based on several calendars inside of this form //////////////////////////////////////////////////////////////
            $additional_calendars_cost = $this->check_cost_for_additional_calendars($summ, $_POST['form'], $_POST['bk_type'],  array($start_time, $end_time)   );
            $summ_total       = $additional_calendars_cost[0];
            $summ_additional  = $additional_calendars_cost[1];
            $dates_additional = $additional_calendars_cost[2];

            $additional_description = '';           
            if ( count($summ_additional)>0 ) {  // we have additional calendars inside of this form

                // Main calendar description and discount info //
                $additional_description .= '<br />' . get_booking_title($_POST['bk_type']) . ': ' . $cost_currency   . $summ  ;
                if ($coupon_info_4_main_calendar != '')
                    $additional_description .=   $coupon_info_4_main_calendar ;
                $coupon_info_4_main_calendar = '';
                $additional_description .= '<br />' ;



                // Additional calendars - info and discounts //
                foreach ($summ_additional as $key=>$ss) {
                    
                    $additional_description .= get_booking_title($key) . ': ' . $cost_currency  . $ss ;

                    // Discounts info ///////////////////////////////////////////////////////////////////////////////////////////////////////
                    $form_content_for_specific_calendar = $this->get_bk_form_with_correct_id($_POST['form'], $key ,  $_POST['bk_type'] );
                    $dates_in_specific_calendar = $dates_additional[$key];
                    $coupon_info_4_calendars = apply_bk_filter('wpdev_get_additional_description_about_coupons', '', $key , $dates_in_specific_calendar , array($start_time, $end_time ), $form_content_for_specific_calendar );
                    if ($coupon_info_4_calendars != '')
                        $additional_description .=   $coupon_info_4_calendars ;
                    $coupon_info_4_calendars = '';
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                    $additional_description .= '<br />' ;
                }

            }
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            $summ_additional_hint = $summ_total - $summ_original;

            $summ_original         = wpdev_bk_cost_number_format( $summ_original );
            $summ_additional_hint  = wpdev_bk_cost_number_format( $summ_additional_hint );
            $summ_total            = wpdev_bk_cost_number_format( $summ_total );


            // JavaScript setup //
            ?> <script type="text/javascript">
                  if (document.getElementById('booking_hint<?php echo $_POST['bk_type']; ?>' ) !== null)
                    document.getElementById('booking_hint<?php echo $_POST['bk_type']; ?>' ).innerHTML = '<?php
                       echo  $cost_currency  . $summ_total .
                             $coupon_info_4_main_calendar .
                             $additional_description;
                       ?>';

                  if (document.getElementById('additional_cost_hint<?php echo $_POST['bk_type']; ?>' ) !== null)
                    document.getElementById('additional_cost_hint<?php echo $_POST['bk_type']; ?>' ).innerHTML = '<?php
                       echo  $cost_currency  . $summ_additional_hint ; ?>';
                           
                  if (document.getElementById('original_booking_hint<?php echo $_POST['bk_type']; ?>' ) !== null)
                    document.getElementById('original_booking_hint<?php echo $_POST['bk_type']; ?>' ).innerHTML = '<?php
                       echo  $cost_currency  . $summ_original ; ?>';



                  if (document.getElementById('total_bk_cost<?php echo $_POST['bk_type']; ?>') != null)
                    document.getElementById('total_bk_cost<?php echo $_POST['bk_type']; ?>' ).value = '<?php echo  $summ_total; ?>';
               </script> <?php
        }


        // Show help hint of shortcode at the admin panel
        function show_additional_shortcode_help_for_form(){
            ?><span class="description"><?php printf(__('%s - show cost hint for full booking in real time, depends from selection of days and form elements.', 'wpdev-booking'),'<code>[cost_hint]</code>');?></span>
              <span class="description example-code"><?php printf(__('Example: %sThe full cost for payment: %s ', 'wpdev-booking'),'&lt;div  style="text-align:left;line-height:28px;"&gt;&lt;p&gt;', '[cost_hint]&lt;/p&gt;&lt;/div&gt;');?></span><br/><?php
            ?><span class="description"><?php printf(__('%s - show cost hint of original booking cost without additional costs for full booking in real time, depends only from days selection.', 'wpdev-booking'),'<code>[original_cost_hint]</code>');?></span>
              <span class="description example-code"><?php printf(__('Example: %sThe original cost for payment: %s ', 'wpdev-booking'),'&lt;div  style="text-align:left;line-height:28px;"&gt;&lt;p&gt;', '[original_cost_hint]&lt;/p&gt;&lt;/div&gt;');?></span><br/><?php
            ?><span class="description"><?php printf(__('%s - show cost hint of additional booking cost, ehich depends from selection of form elements.', 'wpdev-booking'),'<code>[additional_cost_hint]</code>');?></span>
              <span class="description example-code"><?php printf(__('Example: %sThe additional cost for payment: %s ', 'wpdev-booking'),'&lt;div  style="text-align:left;line-height:28px;"&gt;&lt;p&gt;', '[additional_cost_hint]&lt;/p&gt;&lt;/div&gt;');?></span><br/><?php
            ?><span class="description"><?php printf(__('%s - enter direct cost at admin panel at page: ', 'wpdev-booking'),'<code>[cost_corrections]</code>'); echo '"'; _e("Add booking",'wpdev-booking'); echo '". '; ?></span>
              <span class="description example-code"><?php printf(__('Example: %s', 'wpdev-booking'), '[cost_corrections]');?></span><br/><?php
        }

        /////////////////////////////////////////////////////////////////////////////////////


  // R A T E S  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



        // Define JavaScript variable for showing tooltip rates for 1 day
        function show_rates_at_calendar($blank, $type_id ) {  $start_script_code = '';

            // Save at the Advnaced settings these 2 parameters
            $is_show_cost_in_tooltips =    get_bk_option( 'booking_is_show_cost_in_tooltips' );
            $highlight_cost_word = get_bk_option( 'booking_highlight_cost_word'); ;
            $highlight_cost_word      =  apply_bk_filter('wpdev_check_for_active_language', $highlight_cost_word );

            if ($is_show_cost_in_tooltips !== 'On') return $start_script_code;

            $start_script_code .= ' is_show_cost_in_tooltips = true; ';


            $cost_currency = get_bk_option( 'booking_paypal_curency' );
            if ($cost_currency == 'USD' ) $cost_currency = '$';
            elseif ($cost_currency == 'EUR' ) $cost_currency = '&euro;';
            $start_script_code .= " cost_curency =  '". esc_js($highlight_cost_word .$cost_currency) . " '; ";

            // Get cost of 1 time unit
            $cost = 0;
            $result = $this->get_booking_types($type_id); // Main info according booking type
            if ( count($result)>0 )  $cost = $result[0]->cost;

            // Get period of costs - multiplier
            $price_period        =  get_bk_option( 'booking_paypal_price_period' );

            if ($price_period == 'day') {
                $cost_multiplier = 1;
            } elseif ($price_period == 'night') {
                $cost_multiplier = 1;
            } elseif ($price_period == 'hour') {
                $cost_multiplier = 24;
            } else {
                $cost_multiplier = 1;
            }

            $my_day =  date('m.d.Y' );          // Start days from TODAY
            $prices_per_day = array();                                          // PHP Debug
            $prices_per_day[$type_id] = array();                                // PHP Debug

            $start_script_code .= "  prices_per_day[". $type_id ."] = [] ;  ";

            for ($i = 0; $i < 365; $i++) {

                $my_day_arr = explode('.',$my_day);

                $day = ($my_day_arr[1]+0);
                $month= ($my_day_arr[0]+0);
                $year = ($my_day_arr[2]+0);

                $my_day_tag =   $month . '-' . $day . '-' . $year ;

                $fin_day_cost =  $this->get_1_day_cost_apply_rates($type_id, $cost, $day , $month, $year );
                $fin_day_cost = round($fin_day_cost,2);

                $prices_per_day[$type_id][$my_day_tag] = $fin_day_cost;         // PHP Debug

                $start_script_code .= "  prices_per_day[". $type_id ."]['".$my_day_tag."'] = '".$fin_day_cost."' ;  ";

                $my_day =  date('m.d.Y' , mktime(0, 0, 0, $month, ($day+1), $year ));
            }

            //debuge($prices_per_day); die;                                     // PHP Debug

            return $start_script_code;
        }

        // Apply season rates to D A Y S array with/without $time_array   -   send from P A Y P A L form
        function apply_season_rates( $paypal_dayprice, $days_array, $booking_type, $times_array ) {

         if ($times_array[0] ==  array('00','00','00') ) $times_array[0] =  array('00','00','01');
         if ($times_array[1] ==  array('00','00','02') ) $times_array[1] =  array('24','00','02');
//debuge('season rates',$times_array,$days_array,$paypal_dayprice);
            $one_night = 0;
            $paypal_price_period        =  get_bk_option( 'booking_paypal_price_period' );
            $costs_depends_from_selection_new = array();
            if ($paypal_price_period == 'day') {

                $costs_depends_from_selection = $this->get_all_days_cost_depends_from_selected_days_count($booking_type, $days_array, $times_array );
//debuge($costs_depends_from_selection);
                if ($costs_depends_from_selection !== false) {
                    $costs_depends_from_selection[0]=0;                    
                    for ($ii = 1; $ii < count($costs_depends_from_selection); $ii++) {
                        $costs_depends_from_selection_new[] = $costs_depends_from_selection[$ii];
                    }
                    //return $costs_depends_from_selection_new;
                }

            }elseif ($paypal_price_period == 'night') {
                                                if (count($days_array)>1) {
                                                    if (  ( ($times_array[0] == array('00','00','01') )  && ($times_array[1] == array('00','00','00') ))  ||
                                                          ( ($times_array[0] == array('00','00','01') )  && ($times_array[1] == array('24','00','02') ))
                                                       )
                                                    { // No times is set
                                                      $one_night = 1; }
                                                }
            }elseif ($paypal_price_period == 'hour') {

            } else{
                    //return array($paypal_dayprice); //fixed
            }
//debuge($one_night)            ;
            $days_rates = array();
            // $i=0;
            for($i=0;$i<(count($days_array) - $one_night );$i++){ $den = $days_array[$i];

               if (! empty($den)) {
    //            foreach ($days_array as $den) { $i++;
                   $times_array_check = array(array('00','00','01'),array('24','00','02'));
                   if ( $i==0 )                   { $times_array_check[0] =  $times_array[0]; }
                   if ( $i == (count($days_array) -1- $one_night )) { $times_array_check[1] =  $times_array[1]; }

                    //$times_array_check = array($times_array[0],$times_array[1]);  // Its will make cost calculation only between entered times, even on multiple days
                   $den = explode('.',$den);
                   $day =  ($den[0]+0); $month =  ($den[1]+0); $year = ($den[2]+0);
                   $week =  date('w', mktime(0, 0, 0, $month, $day, $year) );
                   $days_rates[] = $this->get_1_day_cost_apply_rates($booking_type, $paypal_dayprice, $day , $month, $year, $times_array_check );
                }
            }
            //if (count($days_rates)>1) $days_rates[count($days_rates)-1] = 0;
            // If fixed deposit so take only for first day cost

            if ($paypal_price_period == 'fixed') { if (count($days_rates)>0) { $days_rates = array($days_rates[0]); } else {$days_rates = array();} }

//debuge('Days rates', $days_rates);
//debuge($costs_depends_from_selection_new)            ;

            /**/

            if ( count($costs_depends_from_selection_new)>0) {
                $rates_with_procents = array();
                // check is some value of $costs_depends_from_selection_new consist % if its true so then apply this procents to days
                $is_rates_with_procents = false;
                for ($iii = 0; $iii < count($costs_depends_from_selection_new); $iii++) {
                    if ( strpos($costs_depends_from_selection_new[$iii], 'add') !== false ) {
                        $my_vvalue = floatval(str_replace('add','',$costs_depends_from_selection_new[$iii] ) );
                        $rates_with_procents[]= $my_vvalue + $days_rates[$iii];
                    } elseif ( strpos($costs_depends_from_selection_new[$iii], '%') !== false ) {
                        $is_rates_with_procents = true;
                        $proc = str_replace('%','',$costs_depends_from_selection_new[$iii] ) * 1;
                        if (isset($days_rates[$iii]))
                                $rates_with_procents[]= $proc*$days_rates[$iii]/100;
                    } else {
                        $rates_with_procents[]= floatval($costs_depends_from_selection_new[$iii]);// $days_rates[$iii]; // just cost
                    }

                }
//debuge('$rates_with_procents, $costs_depends_from_selection_new', $is_rates_with_procents, $rates_with_procents, $costs_depends_from_selection_new)                ;
//die;
                if ($is_rates_with_procents) return $rates_with_procents;               // Rates with procents from cost depends from number of days
                else                         return $costs_depends_from_selection_new;  // Cost depends from number of days
            } else                           return $days_rates;                        // Just pure rates
            /**/
//debuge($costs_depends_from_selection_new, $days_rates);die;
            
        }

                // Get count of MINUTES from time in format "17:20" or array(17, 20)
                function get_minutes_num_from_time($time_array){
                    if (is_string($time_array)) {
                        $time_array = explode(':',$time_array);
                    }
                    if (is_array($time_array)) {
                        return  ($time_array[0]*60+ intval($time_array[1]));
                    }
                    return $time_array;
                }

                // Get COST based on hourly rate - $hour_cost and start and end time during 1 day
                /*  $times_array                        (its arrayin fomat
                //  (start_minutes, end minutes)                        or
                //  ("12:00", "17:30")                                  or
                //  (array("12","00","00"), array("22", "00", "00"))    /**/
                function get_cost_between_times($times_array, $hour_cost) {
                        $start_time = $times_array[0];      // Get Times
                        if (count($times_array)>1) $end_time   = $times_array[1];
                        else                       $end_time = array('24','00','00');
                        if (is_string($start_time)) { $start_time = explode(':', $start_time);$start_time[2] = '00'; }
                        if (is_string($end_time))   { $end_time   = explode(':', $end_time);  $end_time[2] = '00'; }

                        if ( (is_int($end_time)) && (is_int($start_time)) ) {   // 1000000 correction need to make.

                            if ($end_time > 1000000) { $ostatok = $end_time % 1000000;
                                if ($ostatok == 0) $end_time = $end_time  / 1000000;
                                else               $end_time = ( $end_time + ( 1000000 - $ostatok ) )  / 1000000;
                            }
                            if ($start_time > 1000000) { $ostatok = $start_time  % 1000000;
                                if ($ostatok == 0) $start_time = $start_time  / 1000000;
                                else               $start_time = ( $start_time + ( 1000000 - $ostatok ) )  / 1000000;
                            }
                            return round(  ( ($end_time - $start_time) * ($hour_cost / 60 ) ) , 2 );
                        }

                        if (empty($start_time[0]) ) $start_time[0] = '00';
                        if (empty($end_time[0]) ) $end_time[0] = '00';

                        if (! isset($start_time[1])) $start_time[1] = '00';
                        if (! isset($end_time[1])) $end_time[1] = '00';



                        if ( ($end_time[0] == '00') && ($end_time[1] == '00') ) $end_time[0] = '24';


                        $m_dif =  ($end_time[0] * 60 + intval($end_time[1]) ) - ($start_time[0] * 60 + intval($start_time[1]) ) ;
                        $h_dif = intval($m_dif / 60) ;
                        $m_dif = ($m_dif - ($h_dif*60) ) / 60 ;

                        $summ = round( ( 1 * $h_dif * $hour_cost ) + ( 1 * $m_dif * $hour_cost ) , 2);

                        return $summ;
                }

                // Get seson filter info
                function get_season_filter($filter_id){
                    global $wpdb;
                    $result = $wpdb->get_results($wpdb->prepare( "SELECT filter FROM ".$wpdb->prefix ."booking_seasons WHERE booking_filter_id = " . $filter_id ));
                    if (count($result)>0){

                        if ( ( ! empty($filter['start_time'])) && ( ! empty($filter['end_time'])) ) {   // Its hourly filter, so its apply to all days
                            return array( 'hour' => array($filter['start_time'], $filter['end_time']) );
                        }

                        $weekdays = array(); $days = array(); $monthes =  array(); $years = array();

                        foreach ($filter['weekdays'] as $key => $value) {
                            if ($value == 'On')  $weekdays[] = $key;
                        }
                        foreach ($filter['days'] as $key => $value) {
                            if ($value == 'On')  $days[] = $key;
                        }
                        foreach ($filter['monthes'] as $key => $value) {
                            if ($value == 'On')  $monthes[] = $key;
                        }
                        foreach ($filter['year'] as $key => $value) {
                            if ($value == 'On')  $years[] = $key;
                        }

                        return array('weekdays'=>$weekdays, 'days'=>$days, 'monthes'=>$monthes, 'year'=>$years );

                    } else return false; // No filter with such ID
                }

        // Get 1 DAY cost OR cost from time to  time at  $times_array
        function get_1_day_cost_apply_rates( $type_id, $base_cost, $day , $month, $year, $times_array=false ) {


 // debuge('Start', $type_id, $base_cost, $day , $month, $year, $times_array);

            $price_period =  get_bk_option( 'booking_paypal_price_period' );       // Get cost period and set multiplier for it.

            if ($price_period == 'day') {         $cost_multiplier = 1;
            } elseif ($price_period == 'night') { $cost_multiplier = 1;
            } elseif ($price_period == 'hour')  { $cost_multiplier = 24;        // Day have a 24 hours
            } else {                              $cost_multiplier = 1;   }     // fixed  // return $base_cost;

            $rate_meta_res = $this->get_bk_type_meta($type_id,'rates');         // Get all RATES for this bk resource

            if ( count($rate_meta_res)>0 ) {
                if ( is_serialized( $rate_meta_res[0]->value ) )  $rate_meta = unserialize($rate_meta_res[0]->value);
                else                                              $rate_meta = $rate_meta_res[0]->value;

                $rate              = $rate_meta['rate'];                        // Rate values                           (key -> ID)
                $seasonfilter      = $rate_meta['filter'];                      // If this filter assign to rate On/Off  (key -> ID)
//debuge($rate_meta);
                if (isset($rate_meta['rate_type']))   $rate_type = $rate_meta['rate_type'];       // is rate curency or %
                else                                  $rate_type = array();


                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                // Get    B A S E    C O S T   with   Rates  and get    H O U R L Y   R a t e s
                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                $base_cost_with_rates = $base_cost;
                $hourly_rates = array();
                ////////////////////////////////////////////////////////////////
                // Get here Cost of the day with rates - $base_cost_with_rates, If curency rate is assing for this day so then just assign it and stop
                // also get all hour filters rates
                foreach ($seasonfilter as $filter_id => $is_filter_ON) {  // Id_filter => On  || Id_filter => Off
                    if ($is_filter_ON == 'On') {                                       // Only activated filters
                        $is_day_inside_of_filter = $this->is_day_inside_of_filter($day , $month, $year, $filter_id);  // Check  if this day inside of filter

                        if ( $is_day_inside_of_filter === true ) {              // If return true then Only D A Y filters here
                            if ( isset($rate_type[$filter_id]) ) {                    // It Can be situation that in previos version is not set rate_type so need to check its
                                if ($rate_type[$filter_id] == '%') $base_cost_with_rates =  ( ($base_cost_with_rates * $rate[$filter_id] / 100) ) ; // %
                                else {                                          // Here is the place where we need in future create the priority of rates according direct curency value
                                       $base_cost_with_rates =  $rate[$filter_id]; break;} //here rate_type  == 'curency so we return direct value and break all other rates
                            } else $base_cost_with_rates =  ( ($base_cost_with_rates * $rate[$filter_id] / 100) ) ; // Default - %
                        }

                        if( is_array($is_day_inside_of_filter) ) {              // Its HOURLY filter, save them for future work
                          if ($is_day_inside_of_filter[0] == 'hour') { $hourly_rates[$filter_id]=array( 'rate'=>$rate[$filter_id], 'rate_type'=>$rate_type[$filter_id], 'time'=>array($is_day_inside_of_filter[1],$is_day_inside_of_filter[2]) ); }
                        }

                    } // close ON if
                }  // close foreach
                /////////////////////////////////////////////////////////////////////////////////////////////////////////
//debuge(array( '$base_cost'=>$base_cost, '$base_cost_with_rates'=>$base_cost_with_rates,'$hourly_rates'=>$hourly_rates));

                if ( ( count($hourly_rates) == 0 ) && ($price_period == 'fixed') ) {
                    return $base_cost_with_rates;
                }

                // H O U R s ///////////////////////////////////////////////////
                $general_hours_arr = array();

                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                // Get   S T A R T  and   E N D   T i m e   for this day (or 0-24 or from function params $starttime)
                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                if ($times_array === false) {                                   // Time is not pass to the function
                    $global_start_time = array('00','00','00');
                    $global_finis_time = array('24','00','00');
                } else {                                                        // Time is set and we need calculate cost between it
                    $global_start_time = $times_array[0];
                    if (count($times_array)>1) $global_finis_time   = $times_array[1];
                    else                       $global_finis_time = array('24','00','00');
                    if (is_string($global_start_time))   { $global_start_time = explode(':', $global_start_time);$global_start_time[2] = '00'; }
                    if (is_string($global_finis_time))   { $global_finis_time   = explode(':', $global_finis_time);  $global_finis_time[2] = '00'; }
                    if ($global_finis_time == array('00','00','00')) $global_finis_time = array('24','00','00');
                 }
                 $general_hours_arr[ $this->get_minutes_num_from_time($global_start_time)*1000000 ] = array('start' , $base_cost_with_rates, '' );  // start glob work times array
                 $general_hours_arr[ $this->get_minutes_num_from_time($global_finis_time)*1000000 ] = array('end'   , $base_cost_with_rates, '' );  // end glob work times array
                 /////////////////////////////////////////////////////////////////////////////////////////////////////////


                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                // Get   all   H O U R L Y    R A T E S    in    S o r t e d    by  Minutes*100   array
                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                foreach ($hourly_rates as $hour_filter_id => $hour_rate) {
                   if (! isset($hour_rate['rate_type']) ) $hour_rate['rate_type'] ='%';

                   $r__start = 1000000 * $this->get_minutes_num_from_time($hour_rate['time'][0]);
                   $r__fin   = 1000000 * $this->get_minutes_num_from_time($hour_rate['time'][1]);
                   while( isset($general_hours_arr[$r__start]) ) {$r__start--;}
                   while( isset($general_hours_arr[$r__fin]) )   {$r__fin--;}

                   $general_hours_arr[$r__start] = array('rate_start' , $hour_rate['rate'] , $hour_rate['rate_type'] );
                   $general_hours_arr[$r__fin]   = array('rate_end'   , $hour_rate['rate'] , $hour_rate['rate_type'] );
                }
                ksort( $general_hours_arr );                                    // SORT time(rate) arrays with start/end time
                /////////////////////////////////////////////////////////////////////////////////////////////////////////

 //debuge(array('$general_hours_arr'=>$general_hours_arr));

                if (    ($price_period == 'hour') ||                            // Get hour rates, already based on cost with applying rates for days not hours
                        ( ($price_period == 'fixed') && ( count($hourly_rates)>0 ) )
                   )                                                               $base_hour_cost = $base_cost_with_rates ;
                else                                                               $base_hour_cost = $base_cost_with_rates / 24 ;

 //debuge(array('$base_hour_cost'=>$base_hour_cost));

                $is_continue = false;                                           // Calculate cost for our times in array segments
                $general_summ_array = array();
                $cur_rate = $base_hour_cost;
                $cur_type = 'curency';
                foreach ($general_hours_arr as $minute_time => $rate_value) {

                    if ($is_continue) {                                         // Calculation
                        if ($cur_type == 'curency') {
                            if ($price_period == 'fixed')  $general_summ_array[] = $cur_rate;
                            else                           $general_summ_array[] = $this->get_cost_between_times( array($previos_time[0] ,$minute_time), $cur_rate);
                        } else {
                            $procent_base =  $this->get_cost_between_times( array($previos_time[0] ,$minute_time), $base_hour_cost);
                            $general_summ_array[] =  ( ($procent_base * $cur_rate / 100) ) ; // %
                        }
                    }

                    if ( $rate_value[0] == 'start' ) { $is_continue = true; }   // start calculate from this time
                    if ( $rate_value[0] == 'end'   ) { break; }                 // Finish calculation

                    $previos_time = array($minute_time, $rate_value);           // Save previos time and rate

                    if ( $rate_value[0] == 'rate_start' ) {                              // RATE start so get type and value of rate
                        $cur_type = $rate_value[2];
                        if ( ($price_period == 'hour') || ($price_period == 'fixed') )
                              $cur_rate = $rate_value[1];
                        else  {
                            if ($cur_type == 'curency') $cur_rate = $rate_value[1] / 24;
                            else $cur_rate = $rate_value[1];
                        }

                    }
                    if ( $rate_value[0] == 'rate_end'   ) {                              // Rate end so set standard  type and rate
                        $cur_rate = $base_hour_cost;
                        $cur_type = 'curency';
                    }
                } // close foreach time cost array

// debuge( array('$general_summ_array' =>  $general_summ_array )  );//die;

                if ( count($general_hours_arr) > 0 ) {                          // summ all costs into one variable - its 1 day cost ( or cost between times), with already aplly day rates filters
                       if ($price_period == 'fixed')  $return_cost = $general_summ_array[0];
                       else {
                            $return_cost = 0;
                            foreach ($general_summ_array as $vv) { $return_cost += $vv;  }
                       }
                } else                      $return_cost = $base_cost_with_rates;

                ////////////////////////////////////////////////////////////////
//debuge('$return_cost, $price_period, $hourly_rates', $return_cost, $price_period, $hourly_rates);
                return $return_cost;   // Evrything is calculated based on hours
                /*
                if( ($times_array !== false) && (count($hourly_rates)==0) ) {   //hourly rates do not exist BUT we set time from one time to end time
                    if ($price_period == 'hour')        $hour_cost = $return_cost ;
                    else {
                        if ($price_period == 'fixed')   return $return_cost;
                        elseif ($price_period == 'night')   return $return_cost; // alredy calculated, because time is exist //FIXED now
                        elseif ($price_period == 'day')   return $return_cost; // alredy calculated, because time is exist //FIXED now
                        else                            $hour_cost = $base_cost / 24 ;
                    }
                        return $this->get_cost_between_times($times_array, $hour_cost);
                } else  return  $return_cost;    // Return day price after assigning of rates
                /**/

            } // Finish R A T E S  work


            // There    N o    R A T E S  at all
            if ($times_array === false)                 return  $cost_multiplier * $base_cost;      // No times, cost for 1 day
            else { // Also need to check according times hour
                if ($price_period == 'hour')            $hour_cost = $base_cost ;
                else {
                        if ($price_period == 'fixed')   return $base_cost;
                        else                            $hour_cost = $base_cost / 24 ;
                }
                return $this->get_cost_between_times($times_array, $hour_cost);                     // Cost for some time interval
            }

        }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Apply advanced cost to the cost from paypal form
        function advanced_cost_apply( $summ , $form , $bktype , $days_array ){

            $booking_form_name='';
            if (isset($_POST['booking_form_type']) ){
                if (! empty($_POST['booking_form_type'])) {
                    $booking_form_name = $_POST['booking_form_type'];
                }
            }


            $additional_cost = 0;                                               // advanced cost, which will apply
            $booking_form_show = get_form_content ($form, $bktype);


            if ($booking_form_name === '') { $field__values = get_bk_option( 'booking_advanced_costs_values' ); }     // Get saved advanced cost structure for STANDARD form
            else { $field__values = get_bk_option( 'booking_advanced_costs_values_for' . $booking_form_name ); }

            $full_procents = 1;
            if ( $field__values !== false ) {                                   // Its exist
                if ( is_serialized( $field__values ) )   $field__values_unserilize = unserialize($field__values);
                else                                               $field__values_unserilize = $field__values;
            $booking_form_show['content'] ='';



                if (! empty($field__values_unserilize)) {                       // Checking
                    if (is_array($field__values_unserilize)) {
                        foreach ($field__values_unserilize as $key_name => $value) {    // repeat in format "visitors"  =>  array ("1"=>25, "2"=>"200%")
                            $key_name= trim($key_name);                         // Get trim visitors name (or some other)

                            if (isset( $booking_form_show[$key_name] )) {       // Get value sending from booking form like this $booking_form_show["visitors"]
                                $selected_value = $booking_form_show[$key_name];

                                if ( is_array($selected_value) )  $selected_value_array = $selected_value;
                                else                              $selected_value_array = array($selected_value);

                                foreach ($selected_value_array as $selected_value ) {

                                            $selected_value = str_replace(' ','_',$selected_value);
                                            if ($selected_value == '') $selected_value = 'checkbox';

                                            if ( isset($value[$selected_value]) ) {         // check how its value for selected value in cash or procent
                                                $additional_single_cost = $value[$selected_value];                      
                                                $additional_single_cost = str_replace(',','.',$additional_single_cost);
                                                if ( strpos($additional_single_cost, '%') !== false ) {                     // %
                                                    $additional_single_cost = str_replace('%','',$additional_single_cost);
                                                    $additional_single_cost = floatval($additional_single_cost);                                                                                                       
                                                    $full_procents =  ( ( $additional_single_cost * $full_procents /100)  );
                                                }elseif ( strpos($additional_single_cost, '/day') !== false ) {             // per day
                                                    $additional_single_cost = str_replace('/day','',$additional_single_cost);
                                                    $additional_single_cost = floatval($additional_single_cost);                                                   
                                                    $additional_cost += floatval($additional_single_cost)*count($days_array);
                                                }else{                                                                      // cashe
                                                    $additional_cost += floatval($additional_single_cost);
                                                }
                                            }
                                }

                            }

                        }
                    }
                }

            }

            if ( get_bk_option( 'booking_advanced_costs_calc_fixed_cost_with_procents' ) == 'On' ) return ($summ + $additional_cost) * $full_procents;
            else                                                                              return $summ * $full_procents + $additional_cost ;
            
        }



  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Apply fixed deposit cost to the cost from paypal form
        function fixed_deposit_amount_apply($summ , $post_form, $booking_type ) {
            $original_summ = $summ;                                             // Original cost for booking
            $is_resource_deposit_payment_active   = get_bk_option( 'booking_is_resource_deposit_payment_active');
            if ($is_resource_deposit_payment_active == 'On') {

                    $fixed_deposit = $this->get_bk_type_meta( $booking_type ,'fixed_deposit');

                    if ( count($fixed_deposit) > 0 ) {
                        if ( is_serialized( $fixed_deposit[0]->value ) ) $fixed_deposit = unserialize($fixed_deposit[0]->value);
                        else                                             $fixed_deposit = $fixed_deposit[0]->value;
                    }
                    else $fixed_deposit = array('amount'=>'100',
                                                'type'=>'%',
                                                'active' => 'Off' );

                    $resource_deposit_amount            = $fixed_deposit['amount'];
                    $resource_deposit_amount_apply_to   = $fixed_deposit['type'];
                    $resource_deposit_is_active         = $fixed_deposit['active'];

                    if ($resource_deposit_is_active == 'On') {

                        if ($resource_deposit_amount_apply_to == '%') $summ = $summ * $resource_deposit_amount / 100 ;
                        else $summ = $resource_deposit_amount;
                        
                    }

            }
            return ($summ );
        }
  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




     //  R E S O U R C E     T A B L E     C O S T    C o l l  u m n    ////////////////////////////////////////////////////////////////////////////

           // Show headers collumns
           function resources_settings_table_headers(){
                
               if (isset($_GET['tab'])) if (  ($_GET['tab']=='cost')  ) { ?>
              
                <th style="text-align:center;width:320px" rel="tooltip" class="tooltip_bottom"  title="<?php _e('Setting rate or cost,which  is depend from number of selected days for the resource', 'wpdev-booking');?>">
                 <?php _e('Rates', 'wpdev-booking'); echo " | "; _e('Valuation days', 'wpdev-booking'); echo " | "; _e('Deposit', 'wpdev-booking'); ?>
                </th>
                <?php } if (isset($_GET['tab'])) if ( ($_GET['tab']=='availability')  ) { ?>
                <th style="width:60px;text-align:center;" rel="tooltip" class="tooltip_bottom"  title="<?php _e('Setting rate or cost,which  is depend from number of selected days for the resource', 'wpdev-booking');?>">
                 <?php _e('Availability', 'wpdev-booking'); ?>
                </th>
              <?php
                }
           }

           // Show footers collumns
           function resources_settings_table_footers(){
              if (isset($_GET['tab'])) if ( ($_GET['tab']=='availability') || ($_GET['tab']=='cost')  ) { ?>
              
                <td style="border-top: 1px solid #ccc;text-align:center;font-weight:bold;"></td>
              <?php  } else {
              ?>
              <?php
              }
           }


           // Show Resources Collumns
           function resources_settings_table_collumns( $bt, $all_id, $alternative_color ){
                $page_num = ''; $wh_resource_id='';
                if (isset($_REQUEST['page_num'])) $page_num = '&page_num='.$_REQUEST['page_num'];
                if (isset($_REQUEST['wh_resource_id'])) $wh_resource_id =  '&wh_resource_id='.$_REQUEST['wh_resource_id'];

                $link = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-resources' . $page_num . $wh_resource_id ;
              if (isset($_GET['tab'])) if (  ($_GET['tab']=='cost')  ) {  $link .= '&tab=cost'; ?>
                    <?php // Show Costs  ?>
                    <td style="text-align:center;font-size: 11px;border-left:1px solid #ccc;" <?php echo $alternative_color; ?> >
                        <a rel="tooltip" class="tooltip_bottom button"  style="font-size: 11px !important;"
                           title="<?php echo __('Settings rates for days, depends from filter settings.','wpdev-booking'); ?>"
                           href="<?php echo $link.'&wpdev_edit_rates=' . $bt->id ; ?>"><?php _e('Rates', 'wpdev-booking'); ?></a>

                        <?php  if( get_bk_option( 'booking_paypal_price_period' ) == 'day') { ?>
                         <a  rel="tooltip" class="tooltip_bottom button"  style="font-size: 11px !important;"
                             title="<?php echo __('Setting cost, which depends from number of selected days for booking','wpdev-booking'); ?>"
                             href="<?php echo $link.'&wpdev_edit_costs_from_days=' . $bt->id ; ?>"><?php _e('Valuation days', 'wpdev-booking'); ?></a>
                        <?php } else { ?>
                          <a   rel="tooltip" class="tooltip_top button disabled"  style="font-size: 11px !important;"
                             title="<?php echo __('Setting cost, which depends from number of selected days for booking','wpdev-booking'); ?>"
                             href="javascript:alert('<?php _e('Activation of this feature is require setting cost per day','wpdev-booking'); ?>');"><?php _e('Valuation days', 'wpdev-booking'); ?></a>
                        <?php } ?>

                           <span style="margin:7px 10px 7px 5px;  border-left:1px solid #EEEEEE; border-right:1px solid #CCCCCC; height:12px; line-height:14px; padding:3px 0; width:0;"></span>

                        <?php
                              $is_resource_deposit_payment_active   = get_bk_option( 'booking_is_resource_deposit_payment_active');
                              if ($is_resource_deposit_payment_active == 'On') {  ?>                                
                                <a   style="font-size: 11px !important;" rel="tooltip" class="tooltip_bottom button" title="<?php echo __('Setting amount of deposit payment for payment form','wpdev-booking'); ?>"  href="<?php echo $link.'&wpdev_edit_costs_deposit_payment=' . $bt->id ; ?>"><?php _e('Deposit amount', 'wpdev-booking'); ?></a>
                              <?php } else { ?>
                                <a   style="font-size: 11px !important;"  rel="tooltip" class="tooltip_top button disabled" title="<?php echo __('Setting amount of deposit payment for payment form','wpdev-booking'); ?>"
                                     href="javascript:alert('<?php _e('Please make active of this feature at the cost section of general booking settings page','wpdev-booking') ; ?>');"><?php _e('Deposit amount', 'wpdev-booking'); ?></a>
                              <?php }  ?>
                    </td>
                <?php } if (isset($_GET['tab'])) if ( ($_GET['tab']=='availability')  ) {  $link .= '&tab=availability'; ?>
                        <td style="text-align:center;font-size: 11px;border-left:1px solid #ccc;" <?php echo $alternative_color; ?> >

                            <a rel="tooltip" class="tooltip_bottom button" style="font-size: 11px !important;"
                               title="<?php echo __('Setting availability for days, depends from filter settings.','wpdev-booking'); ?>"
                               href="<?php echo $link.'&wpdev_edit_avalaibility=' . $bt->id ; ?>"><?php _e('Availability', 'wpdev-booking'); ?></a>
                        </td>
                <?php
                    }
           }


           //Show Rates, Availbaility and other sections for resource configurations
           function wpdev_bk_booking_resource_page_before(){

                    $this->show_specific_type_avalaibility_filter();
                    $this->show_specific_type_rate();
                    $this->show_specific_cost_depends_from_days_count();
                    $is_resource_deposit_payment_active   = get_bk_option( 'booking_is_resource_deposit_payment_active');
                    if ($is_resource_deposit_payment_active == 'On')
                        $this->show_setings_for_deposit_cost_amount();
           }


           
  // A d m i n   S E T T I N G S    M E N U     ////////////////////////////////////////////////////////////////////////////////////////////////

        // Show General selection of Settings CONTENT
        function settings_menu_content(){

            switch ($_GET['tab']) {

                 case 'cost':
$is_can = apply_bk_filter('multiuser_is_user_can_be_here', true, 'not_low_level_user'); //Anxo customizarion
if (! $is_can) return; //Anxo customizarion

                    ?> <div id="ajax_working" class="clear" style="height:0px;"></div>
                       <div id="poststuff" class="metabox-holder"> <?php $this->show_booking_types_cost(); ?> </div>
                    <?php
                    return false;

                 case 'filter':
                    ?> <div id="ajax_working" class="clear" style="height:0px;"></div>
                       <div id="poststuff" class="metabox-holder"> <?php $this->show_booking_date_filter(); ?> </div>
                    <?php
                    return false;

                 default:
                    return true;
                    break;
                }
        }


                // A v a i l a b i l i t y   Settings of BK Resource SubPage - Edit availability for specific booking resource
                function show_specific_type_avalaibility_filter(){ if (! isset($_GET['wpdev_edit_avalaibility'])) return;
        //debuge($_POST);
                    global $wpdb;
                    $edit_id = $_GET['wpdev_edit_avalaibility'];
                    if ( isset( $_POST['submit_availabilitytypefilter'] ) ) {
                        $availability = array();
                        $days_avalaibility = $_POST['days_avalaibility'];

                            $where = '';
                            $where = apply_bk_filter('multiuser_modify_SQL_for_current_user', $where);
                            if ($where != '') $where = ' WHERE ' . $where;

                        $filter_list = $wpdb->get_results($wpdb->prepare( "SELECT booking_filter_id as id, title, filter FROM ".$wpdb->prefix ."booking_seasons  ".$where."  ORDER BY booking_filter_id DESC" ));
                        foreach ($filter_list as $value) {
                            if ( isset( $_POST['seasonfilter'.$value->id] ) )  $seasonfilter[$value->id] = 'On' ;
                            else                                               $seasonfilter[$value->id] = 'Off' ;
                        }
                        $availability['general'] = $days_avalaibility;
                        $availability['filter']  = $seasonfilter;
                        $this->set_bk_type_meta($edit_id,'availability',serialize($availability));
                    } else{
                        $availability_res = $this->get_bk_type_meta($edit_id,'availability');
        //debuge($availability_res);
                        if ( count($availability_res)>0 ) {

                            if ( is_serialized( $availability_res[0]->value ) )   $availability = unserialize($availability_res[0]->value);
                            else                                                  $availability = $availability_res[0]->value;

                            $days_avalaibility = $availability['general'];
                            $seasonfilter      = $availability['filter'];
                        } else {
                            $days_avalaibility = 'On';
                        }
                    }

                    $result = $this->get_booking_types($edit_id);
                    if ( count($result)>0 ) $title = $result[0]->title;


                    if (! isset($days_avalaibility)) $days_avalaibility = 'On';

                    if ($days_avalaibility =='On') $days_avalaibility_word = __('unavailable', 'wpdev-booking');
                    else                           $days_avalaibility_word = __('available', 'wpdev-booking');
                    ?>
                               <div class='meta-box'>  <div  class="postbox" > <h3 class='hndle'><span><?php _e('Avalaibility booking type', 'wpdev-booking'); ?></span></h3> <div class="inside">

                                    <form  name="avalaibilitytypefilter" action="" method="post" id="avalaibilitytypefilter" >


                                        <div style="padding:0px 20px; font-size:12px;">
                                            <h2 for="days_avalaibility" ><?php printf(__('All days for %s are', 'wpdev-booking'),'<span style="color:#F00">'.$title.'</span>'); ?>:

                                                <input onchange="javascript:setavailabilitycontent('<?php _e('unavailable', 'wpdev-booking'); ?>');" type="radio" name="days_avalaibility" <?php if ($days_avalaibility == 'On') echo 'checked="checked"'; ?>  value="On"> <span class="description" style="background:#5b1;padding:1px 5px 2px;color:#FFF;font-size:16px;font-weight:bold;font-family:Georgia,Times,serif;"><?php _e('available', 'wpdev-booking');?></span>
                                                <input onchange="javascript:setavailabilitycontent('<?php _e('available', 'wpdev-booking'); ?>');" type="radio" name="days_avalaibility" <?php if ($days_avalaibility == 'Off') echo 'checked="checked"'; ?> value="Off"> <span class="description" style="background:#e33;padding:1px 5px 2px;color:#FFF;font-size:16px;font-weight:bold;font-family:Georgia,Times,serif;"><?php _e('unavailable', 'wpdev-booking');?></span>
                                            </h2>
                                        </div>
                                        <div style="clear: both;"></div>
                                        <div style="padding:0px 10px; font-size:12px;">
                                                   <h2 style="padding:0px 10px;line-height:24px;">
                                                        <?php printf(__('Select %s days below or %sadd new season filter%s', 'wpdev-booking'), '<span id="selectword" style="background:#f90;padding:1px 5px 2px;color:#FFF;font-size:16px;font-weight:bold;font-family:Georgia,Times,serif;">'.$days_avalaibility_word .'</span>' ,'<a href="admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-resources&tab=filter&filterdisplay=1">','</a>'); ?>
                                                   </h2>
                                                  <table class="booking_table" cellpadding="0" cellspacing="0">
                                                      <tr>
                                                          <th style="width:15px;height:30px;">
                                                              <input name="seasonfilter_all" id="seasonfilter_all" type="checkbox"  onclick="javascript:setCheckBoxInTable(this.checked, 'filter_avalaibility');" />
                                                          </th>
                                                          <th style="width:200px;"><?php _e('Name','wpdev-booking') ?></th>
                                                          <th><?php _e('Filters','wpdev-booking') ?></th>
                                                      </tr>
                                              <?php
                                                    $where = '';
                                                    $where = apply_bk_filter('multiuser_modify_SQL_for_current_user', $where);
                                                    if ($where != '') $where = ' WHERE ' . $where;

                                                $filter_list = $wpdb->get_results($wpdb->prepare( "SELECT booking_filter_id as id, title, filter FROM ".$wpdb->prefix ."booking_seasons ".$where." ORDER BY booking_filter_id DESC" ));
                                                $link = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-option&tab=filter';
                                                $td_class = ' class="alternative_color" ';
                                                foreach ($filter_list as $value) {
                                                    if ( $td_class == '') $td_class = ' class="alternative_color" ';
                                                    else                  $td_class = '';
                                                ?>
                                                      <tr>
                                                          <td <?php echo $td_class; ?>>
                                                              <input <?php if(isset($seasonfilter)) if(isset($seasonfilter[$value->id])) if($seasonfilter[$value->id] == 'On') echo 'checked="checked"'; ?> value="<?php  if(isset($seasonfilter)) if(isset($seasonfilter[$value->id])) echo $seasonfilter[$value->id];?>" class="filter_avalaibility" name="seasonfilter<?php echo $value->id; ?>" id="seasonfilter<?php echo $value->id; ?>" type="checkbox"/>
                                                          </td>
                                                          <td <?php echo $td_class; ?>><?php echo $value->title; ?></td>
                                                          <td <?php echo $td_class; ?>><?php echo $this->get_filter_description($value->filter); ?></td>
                                                      </tr>
                                                <?php    echo '<div>';
                                                }
                                               ?></table>
                                        </div>




                                        <div class="clear" style="height:10px;"></div>
                                        <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="submit_availabilitytypefilter"/>
                                        <div class="clear" style="height:10px;"></div>

                                    </form>

                               </div> </div> </div>

                    <?php
                }

                // R a t e s   Settings of Rates for specific resource -- Subpage of Bk resources
                function show_specific_type_rate(){ if (! isset($_GET['wpdev_edit_rates'])) return;

                    $seasonfilter = array();
                    $rate = array();
                    $rate_type = array();
                    global $wpdb;
                    $edit_id = $_GET['wpdev_edit_rates'];

                    if ( isset( $_POST['submit_rate_filter'] ) ) {
        //debuge($_POST);
                        $rates_meta = array();
                        $where = '';
                        $where = apply_bk_filter('multiuser_modify_SQL_for_current_user', $where);
                        if ($where != '') $where = ' WHERE ' . $where;

                        $filter_list = $wpdb->get_results($wpdb->prepare( "SELECT booking_filter_id as id, title, filter FROM ".$wpdb->prefix ."booking_seasons ".$where." ORDER BY booking_filter_id DESC" ));
                        foreach ($filter_list as $value) {
                            if ( isset( $_POST['rates_is_active'.$value->id] ) )  $seasonfilter[$value->id] = 'On' ;
                            else                                                  $seasonfilter[$value->id] = 'Off' ;

                            if ( isset( $_POST['rate'.$value->id] ) )  $rate[$value->id] = $_POST['rate'.$value->id] ;
                            else                                       $rate[$value->id] = '0' ;

                            if ( isset( $_POST['rate_type'.$value->id] ) )  $rate_type[$value->id] = $_POST['rate_type'.$value->id] ;
                            else                                            $rate_type[$value->id] = '%' ;

                        }
                        $rates_meta['filter'] = $seasonfilter;
                        $rates_meta['rate']  = $rate;
                        $rates_meta['rate_type']  = $rate_type;
        //debuge($rates_meta);
                        $this->set_bk_type_meta($edit_id,'rates',serialize($rates_meta));

                    } else{
                        $rates_res = $this->get_bk_type_meta($edit_id,'rates');
        //debuge($rates_res);
                        if ( count($rates_res)>0 ) {
                            if ( is_serialized( $rates_res[0]->value ) )        $rates_meta = unserialize($rates_res[0]->value);
                            else                                                $rates_meta = $rates_res[0]->value;
        //debuge($rates_meta);
                            $rate         = $rates_meta['rate'];
                            $seasonfilter = $rates_meta['filter'];
                            if (isset($rates_meta['rate_type'])) $rate_type    = $rates_meta['rate_type'];
                            else                                 $rate_type    = array();
                        } else {
                        }
                    }

                    $result = $this->get_booking_types($edit_id); // Main info according booking type
                    if ( count($result)>0 ) {
                        $title = $result[0]->title;
                        $cost = $result[0]->cost;
                    }

                    ?>
                               <div class='meta-box'>  <div  class="postbox" > <h3 class='hndle'><span><?php _e('Seson rates of booking resource', 'wpdev-booking'); ?></span></h3> <div class="inside">

                                    <form  name="sesonratesfilter" action="" method="post" id="sesonratesfilter" >

                                        <div style="padding:0px 10px; font-size:12px;">
                                                   <h2 style="padding:0px 10px;line-height:24px;">
                                                        <?php printf(__('Enter season rate(s) (costs diference in %s from standard cost %s or fixed cost) of booking resource %s or %sadd new season filter%s', 'wpdev-booking'),'%' , '<span style="color:#F90;">' . $cost . '</span>' ,' <span style="color:#F90;">' . $title .'</span>' ,'<a href="admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-resources&tab=filter&filterdisplay=1">','</a>');

                                                        ?>

                                                   </h2>
                                                  <table class="booking_table" cellpadding="0" cellspacing="0">
                                                      <tr>
                                                          <th style="width:15px;height:30px;">
                                                              <input name="seasonfilter_all" id="seasonfilter_all" type="checkbox"  onclick="javascript:setCheckBoxInTable(this.checked, 'filter_avalaibility');" />
                                                          </th>
                                                          <th style="width:140px;"><?php _e('Rates','wpdev-booking') ?></th>
                                                          <th style="width:150px;"><?php _e('Final cost','wpdev-booking') ?></th>
                                                          <th style="width:200px;text-align: left;"><?php _e('Name','wpdev-booking') ?></th>
                                                          <th><?php _e('Filters','wpdev-booking') ?></th>
                                                      </tr>
                                              <?php
                                                $where = '';
                                                $where = apply_bk_filter('multiuser_modify_SQL_for_current_user', $where);
                                                if ($where != '') $where = ' WHERE ' . $where;

                                                $filter_list = $wpdb->get_results($wpdb->prepare( "SELECT booking_filter_id as id, title, filter FROM ".$wpdb->prefix ."booking_seasons ".$where." ORDER BY booking_filter_id DESC" ));
                                                $link = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-option&tab=filter';
                                                $td_class = ' class="alternative_color" ';
                                                foreach ($filter_list as $value) {
                                                    // Skip hour filter for rates
                                                    if ( is_serialized( $value->filter ) ) $starttimes = unserialize($value->filter);
                                                    else                                         $starttimes = $value->filter;
        // Skip hourly rate if not per hour
        //if ( (!empty ($starttimes['start_time'])) && (!empty ($starttimes['end_time'])) && ( get_bk_option( 'booking_paypal_price_period' ) !== 'hour') ) { continue; }

                                                    if ( $td_class == '') $td_class = ' class="alternative_color" ';
                                                    else                  $td_class = '';
                                                ?>
                                                      <tr>
                                                          <td <?php echo $td_class; ?>>
                                                              <input <?php if (isset($seasonfilter[$value->id])) if($seasonfilter[$value->id] == 'On') echo 'checked="checked"'; ?> value="<?php if (isset($seasonfilter[$value->id])) echo $seasonfilter[$value->id];?>" class="filter_avalaibility" name="rates_is_active<?php echo $value->id; ?>" id="rates_is_active<?php echo $value->id; ?>" type="checkbox"/>
                                                          </td>
                                                          <td <?php echo $td_class; ?>><input value="<?php if ( isset($rate[$value->id]) ) $rate_now = $rate[$value->id]; else $rate_now = '0'; echo $rate_now; ?>" maxlength="7" style="width: 75px;text-align:right;" type="text" name="rate<?php echo $value->id; ?>" id="rate<?php echo $value->id; ?>" >
                                                              <span style="font-weight:bold;">
                                                                  <select style="width:55px;" name="rate_type<?php echo $value->id; ?>" id="rate_type<?php echo $value->id; ?>">
                                                                        <option value="%"       <?php if ( isset($rate_type[$value->id]) ) { if ($rate_type[$value->id] == '%')       echo 'selected="selected"'; } else  echo 'selected="selected"'; ?>  >%</option>
                                                                        <option value="curency" <?php if ( isset($rate_type[$value->id]) ) { if ($rate_type[$value->id] == 'curency') echo 'selected="selected"'; } ?> >
                                                                        <?php echo get_bk_option( 'booking_paypal_curency' ); ?>
                                                                          <?php //if ( (!empty ($starttimes['start_time'])) && (!empty ($starttimes['end_time'])) ) {  _e('for 1 hour', 'wpdev-booking');  } else { ?>
                                                                              <?php if( get_bk_option( 'booking_paypal_price_period' ) == 'day')    _e('for 1 day', 'wpdev-booking');    ?>
                                                                              <?php if( get_bk_option( 'booking_paypal_price_period' ) == 'night')  _e('for 1 night', 'wpdev-booking');  ?>
                                                                              <?php if( get_bk_option( 'booking_paypal_price_period' ) == 'fixed')  _e('fixed deposit', 'wpdev-booking');?>
                                                                              <?php if( get_bk_option( 'booking_paypal_price_period' ) == 'hour')   _e('for 1 hour', 'wpdev-booking');   ?>
                                                                          <?php //} ?>
                                                                        </option>
                                                                  </select>
                                                              </span>
                                                          </td>
                                                          <td <?php echo $td_class; ?> style="text-align: center;font-weight: bold;">
                                                            <?php
                                                                    if ( isset($rate_type[$value->id]) ) {
                                                                        if ($rate_type[$value->id] == 'curency') {
                                                                            echo $rate_now ;
                                                                        } else echo  ($cost*$rate_now/100);  // + $cost;
                                                                    } else echo  ($cost*$rate_now/100) ; // + $cost;

                                                                    echo ' <span style="font-weight:normal;">', get_bk_option( 'booking_paypal_curency' ); ?>                                                                          
                                                                              <?php if( get_bk_option( 'booking_paypal_price_period' ) == 'day')    _e('for 1 day', 'wpdev-booking');    ?>
                                                                              <?php if( get_bk_option( 'booking_paypal_price_period' ) == 'night')  _e('for 1 night', 'wpdev-booking');  ?>
                                                                              <?php if( get_bk_option( 'booking_paypal_price_period' ) == 'fixed')  _e('fixed deposit', 'wpdev-booking');?>
                                                                              <?php if( get_bk_option( 'booking_paypal_price_period' ) == 'hour')   _e('for 1 hour', 'wpdev-booking');   ?>
                                                                   <?php echo '</span>'; ?>
                                                                   
                                                          </td>
                                                          <td <?php echo $td_class; ?>><?php echo $value->title; ?></td>
                                                          <td <?php echo $td_class; ?>><?php echo $this->get_filter_description($value->filter); ?></td>
                                                      </tr>
                                                <?php    echo '<div>';
                                                }
                                               ?></table>
                                        </div>

                                        <div class="clear" style="height:10px;"></div>
                                        <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="submit_rate_filter"/>
                                        <div class="clear" style="height:10px;"></div>

                                    </form>

                               </div> </div> </div>

                    <?php
                }

                // Get Costs, which depends from NUMBER of SELECTED D A Y S
                function get_all_days_cost_depends_from_selected_days_count($booking_type, $days_array , $times_array ){

                        $maximum_days_count = count($days_array) ;
                        $costs_depends = $this->get_bk_type_meta($booking_type,'costs_depends');



                        if (count($costs_depends) > 0 ) {
                            if ( is_serialized( $costs_depends[0]->value ) )  $costs_depends = unserialize($costs_depends[0]->value);
                            else                                              $costs_depends = $costs_depends[0]->value;
                        }
                        else return false;

                        $days_costs = array();

                        $cost = $this->get_standard_cost_for_bk_resource($booking_type); // Get cost

//debuge('0: $costs_depends',$costs_depends, $cost, $days_array);

                        foreach ($costs_depends as $value) {
                            if ($value['active'] == 'On') {         // Get only active items


                                // Check  COST_DEPEND settings according SEASON FILTERS : //////////////////////
                                // Only if all days is inside of this filter so then apply it
                                $is_can_continue_with_this_item = true;
                                $one_night = 0;
                                if (get_bk_option( 'booking_paypal_price_period' ) == 'night') $one_night = 1;

                                if (! empty($value['season_filter']) ) {   // THIS item  have  some filters s recheck  it.

                                    for($i=0;$i<(count($days_array) - $one_night );$i++){
                                        $den = $days_array[$i];
                                        if (! empty($den)) {
                                           $times_array_check = array(array('00','00','01'),array('24','00','02'));
                                           if ( $i==0 )                   { $times_array_check[0] =  $times_array[0]; }
                                           if ( $i == (count($days_array) -1- $one_night )) { $times_array_check[1] =  $times_array[1]; }

                                           $den = explode('.',$den);
                                           $day =  ($den[0]+0); $month =  ($den[1]+0); $year = ($den[2]+0);
                                           $week =  date('w', mktime(0, 0, 0, $month, $day, $year) );

                                           $is_day_inside_of_filter = $this->is_day_inside_of_filter($day , $month, $year, $value['season_filter']);  // Check  if this day inside of filter

                                           if ( ! $is_day_inside_of_filter) $is_can_continue_with_this_item = false;
                                        }
                                    }

                                }
                                if (! $is_can_continue_with_this_item) continue;
                                ////////////////////////////////////////////////////////////////////////////////


                                if ($value['type'] == 'summ') {

                                    if ($value['cost_apply_to'] == '%')  $value['cost']  .= '%';

                                    if ( $value['from'] == ($maximum_days_count)) {
                                        //$days_costs = array();

                                        $days_costs[ $value['from']  ] =   $value['cost'];

                                        if ( strpos($value['cost'] , '%') !== false ) $assign_value= $value['cost'];
                                        else $assign_value = 0;

                                        for ($ii = 1; $ii < $value['from']; $ii++) {
                                             $days_costs[$ii] = $assign_value;
                                        }
//debuge('[1]: $days_costs ', $days_costs);
                                        return $days_costs;
                                    } elseif ( $value['from'] < ($maximum_days_count)) {
                                        //$days_costs = array();
                                        $days_costs[ $value['from']  ] =   $value['cost'];
                                        if ( strpos($value['cost'] , '%') !== false ) $assign_value= $value['cost'];
                                        else $assign_value = 0;
                                        for ($ii = 1; $ii < $value['from']; $ii++) {
                                             $days_costs[$ii] = $assign_value;
                                        }
//debuge('1: $days_costs ', $days_costs);
                                    }
                                }elseif ($value['type'] == '=') {
                                    if ( $value['from'] <= $maximum_days_count) {

                                      if ($value['cost_apply_to'] == 'add') $days_costs[ $value['from']  ] = 'add' .$value['cost'];
                                      elseif ($value['cost_apply_to'] == '%') $days_costs[ $value['from']  ] = $value['cost'] . '%';
                                      elseif ($value['cost_apply_to'] == 'fixed') $days_costs[ $value['from']  ] = $value['cost'];
                                      else $days_costs[ $value['from']  ] = $value['cost'];

                                    }
                                } elseif ($value['type'] == '>') {
                                  for ($i = $value['from']; $i <= $value['to']; $i++) {
                                      if ( $i <= $maximum_days_count)
                                        if ( ! isset($days_costs[$i]) ) {

                                              if ($value['cost_apply_to'] == 'add') $days_costs[  $i   ] = 'add' . $value['cost'];
                                              elseif ($value['cost_apply_to'] == '%') $days_costs[  $i   ] = $value['cost'] . '%';
                                              elseif ($value['cost_apply_to'] == 'fixed') $days_costs[  $i   ] = $value['cost'];
                                              else $days_costs[ $i ] = $value['cost'];
                                        }
                                  }
//debuge('2: $days_costs ', $days_costs);
                                }

                            }
                        }
//debuge($days_costs);
                       

                        for ($i = 1; $i <= $maximum_days_count ; $i++) {
                            if ( ! isset($days_costs[$i]) ) {
                                $days_costs[$i] = '100%';
                            }
                        }
                        ksort($days_costs);
//debuge($days_costs); die;
                        return $days_costs;
                }

                // C o s t s   depends from  c o u n t   of   d a y s   - Settings
                function show_specific_cost_depends_from_days_count(){
//debuge($_POST);
                    if (! isset($_GET['wpdev_edit_costs_from_days']))           return;     // If we do not edit costs so then exit
                    if ( get_bk_option( 'booking_paypal_price_period' ) != 'day')  return;     // If cost not per day so then exit

                    global $wpdb;
                    $edit_id = $_GET['wpdev_edit_costs_from_days'];
                    $result = $this->get_booking_types($edit_id); // Main info according booking type
                    if ( count($result)>0 ) {
                        $title = $result[0]->title;
                        $cost = $result[0]->cost;
                    }

                    if ( isset( $_POST['submit_cost_from_days'] ) ) {
//debuge($_POST);

                        $post_all_indexes = $_POST['all_indexes'];
                        if (substr($post_all_indexes,0,1) == ',') $post_all_indexes = substr($post_all_indexes,1);      // delete first ','
                        if (substr($post_all_indexes,-1) == ',')  $post_all_indexes = substr($post_all_indexes,0,-1);   // delete last  ','
                        $post_all_indexes=explode(',',$post_all_indexes);

//debuge($_POST,$post_all_indexes);
                        $costs_depends = array();
                        foreach ($post_all_indexes as $ind) {
                            if ( isset($_POST[ 'dayscost_type' . $ind ])) {
                                $new_array_line = array();
                                if ( isset($_POST[ 'dayscost_is_active' . $ind ]) ) $new_array_line['active'] = 'On';
                                else                                                $new_array_line['active'] = 'Off';
                                $new_array_line['type'] = $_POST[ 'dayscost_type' . $ind ];
                                $new_array_line['from'] = $_POST[ 'dayscost_from' . $ind ];
                                $new_array_line['to']   = $_POST[ 'dayscost_to'   . $ind ];
                                $new_array_line['cost'] = str_replace('%','',$_POST[ 'dayscost'      . $ind ]);
                                $new_array_line['cost_apply_to'] = (isset($_POST[ 'cost_apply_to'      . $ind ])) ? $_POST[ 'cost_apply_to'      . $ind ]: '';
                                if (! isset($_POST[ 'season_filter'   . $ind ])) $new_array_line['season_filter'] = 0;
                                else                                             $new_array_line['season_filter']   = $_POST[ 'season_filter'   . $ind ];
                                $costs_depends[] = $new_array_line;
                            }
                        }
                        $this->set_bk_type_meta($edit_id,'costs_depends', serialize($costs_depends));

                    } else{
                        $costs_depends = $this->get_bk_type_meta($edit_id,'costs_depends');

                        if (count($costs_depends) > 0 ) {
                            if ( is_serialized( $costs_depends[0]->value ) ) $costs_depends = unserialize($costs_depends[0]->value);
                            else                                             $costs_depends = $costs_depends[0]->value;
                        }
                        else $costs_depends = array();
                    }
                    $link = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-option&tab=cost';

                    // Get seson filters.
                    $where = '';
                    $where = apply_bk_filter('multiuser_modify_SQL_for_current_user', $where);
                    if ($where != '') $where = ' WHERE ' . $where;
                    $filter_list = $wpdb->get_results($wpdb->prepare( "SELECT booking_filter_id as id, title, filter FROM ".$wpdb->prefix ."booking_seasons ".$where." ORDER BY booking_filter_id DESC" ));

                    ?>
                               <div class='meta-box'>  <div  class="postbox" > <h3 class='hndle'><span><?php _e('Set cost of booking resource depends from number of selected booking days', 'wpdev-booking'); ?></span></h3> <div class="inside">
                                    <!--span style="font-weight:normal;color:#e41;font-size:13px;font-style:italic;line-height:30px;padding:1px;"><?php printf(__('If you add costs here, so %srates%s for this booking resource will not take effect !!!','wpdev-booking'), '<a href="'. $link.'&wpdev_edit_rates=' . $edit_id .'">','</a>'); ?></span><br/-->
                                    <span style="font-weight:normal;font-size:12px;font-style:italic;line-height:19px;padding:1px;"><?php printf(__('%sFor and together%s have higher priority then range %sfrom - to%s days. Items at the bottom have higher priority then items at the top.','wpdev-booking'), '<b>','</b>', '<b>','</b>'); ?></span><br />
                                    <span style="font-weight:normal;font-size:12px;font-style:italic;line-height:39px;padding:1px;"><?php printf(__('Please note, if you are select some season filter for this cost configuration, its will %sapply to the days, only if all days are inside of this season filter%s.','wpdev-booking'), '<span style="font-weight:bold;color:red;">','</span>', '<b>','</b>'); ?></span>
                                    <form  name="cost_day_count" action="" method="post" id="cost_day_count" >

                                       <table class="booking_table" cellpadding="0" cellspacing="0">
                                          <tr>
                                              <th style="width:15px;height:30px;">
                                                  <input name="seasonfilter_all" id="seasonfilter_all" type="checkbox"  onclick="javascript:setCheckBoxInTable(this.checked, 'costs_depends');" />
                                              </th>
                                              <th style="width:340px;"><?php _e('Number of days','wpdev-booking') ?></th>
                                              <th style="width:230px;"><?php _e('Cost of 1 day','wpdev-booking') ?></th>
                                              <th style="width:230px;"><?php _e('Season filter','wpdev-booking') ?></th>
                                              <th style=""></th>
                                           </tr>
                                           <tr>
                                            <td colspan="5" id="costs_days_container" style="padding:0px;">
                                              <?php
                                                $td_class = ' class="alternative_color" ';
                                                $all_indexes =',';
                                                for ($i = 0; $i < count($costs_depends); $i++)

                                                {   if ( $td_class == '') $td_class = ' class="alternative_color" ';
                                                    else                  $td_class = '';
                                                ?>
                                                    <div id="cost_days_row<?php echo $i; ?>" <?php echo $td_class; ?> style="width:100%;height:24px;clear: both;padding:3px 0px;font-weight:bold;">

                                                          <div style="float:left;width:15px;margin:5px;">
                                                              <input <?php if($costs_depends[$i]['active'] == 'On') echo 'checked="checked"'; ?>
                                                                  value="" class="costs_depends"
                                                                  name="dayscost_is_active<?php echo $i; ?>"
                                                                  id="dayscost_is_active<?php echo $i; ?>" type="checkbox"/>
                                                          </div>

                                                          <div style="float:left;width:340px;">
                                                              <div  style="float:left;margin:0px 5px;">

                                                                  <select style="width:90px;font-weight:bold;" name="dayscost_type<?php echo $i; ?>" id="dayscost_type<?php echo $i; ?>"
                                                                          onchange="javascript:
                                                                                  if ( ( this.options[this.selectedIndex].value != '=' )
                                                                                    && ( this.options[this.selectedIndex].value != 'summ' ))
                                                                                    document.getElementById('additional_costs_limit<?php echo $i; ?>').style.display = 'block';
                                                                                  else
                                                                                    document.getElementById('additional_costs_limit<?php echo $i; ?>').style.display = 'none';
                                                                                  if ( this.options[this.selectedIndex].value != 'summ' )
                                                                                       jQuery('#cost_days_row_help<?php echo $i; ?>').html( '<?php                                                                                        
                                                                                       //echo get_bk_option( 'booking_paypal_curency' ) ,' ' , __('per 1 day','wpdev-booking');
                                                                                       echo '<select name=&quote;cost_apply_to'. $i.'&quote; id=&quote;cost_apply_to'. $i.'&quote; style=&quote;width:220px;padding:3px 1px 1px 1px !important;&quote; >';
                                                                                       echo '<option value=&quote;fixed&quote; >'. get_bk_option( 'booking_paypal_curency' ). ' '.  __('per 1 day','wpdev-booking').'</option>';
                                                                                       echo '<option value=&quote;%&quote;     >% '.__('from the cost of 1 day ','wpdev-booking').'</option>';
                                                                                       echo '<option value=&quote;add&quote;   >'.sprintf(__('Additional cost in %s per 1 day','wpdev-booking'),get_bk_option( 'booking_paypal_curency' )).'</option>';
                                                                                       echo '</select>';  ?>' );
                                                                                  else  jQuery('#cost_days_row_help<?php echo $i; ?>').html( '<?php 
                                                                                       echo '<select name=&quote;cost_apply_to'. $i.'&quote; id=&quote;cost_apply_to'. $i.'&quote; style=&quote;width:220px;padding:3px 1px 1px 1px !important;&quote; >';
                                                                                       echo '<option value=&quote;fixed&quote; >'. get_bk_option( 'booking_paypal_curency' ). ' '.  __(' for all days!','wpdev-booking').'</option>';
                                                                                       echo '<option value=&quote;%&quote;     >% '.__(' for all days!','wpdev-booking').'</option>';
                                                                                       //echo '<option value=&quote;add&quote;   >'.sprintf(__('Additional cost in %s per 1 day','wpdev-booking'),get_bk_option( 'booking_paypal_curency' )).'</option>';
                                                                                       echo '</select>';  ?>' );
                                                                                       //echo __('For all days!','wpdev-booking'); ?>' ); " >
                                                                        <option value="=" <?php if ( $costs_depends[$i]['type'] == '=') echo 'selected="selected"'; ?>  ><?php _e('For','wpdev-booking'); ?></option>
                                                                        <option value=">" <?php if ( $costs_depends[$i]['type'] == '>') echo 'selected="selected"'; ?>   ><?php _e('From','wpdev-booking'); ?></option>
                                                                        <option value="summ" <?php if ( $costs_depends[$i]['type'] == 'summ') echo 'selected="selected"'; ?>  ><?php _e('Together','wpdev-booking'); ?></option>
                                                                  </select>

                                                                 <input value="<?php echo $costs_depends[$i]['from']; ?>" maxlength="7" style="width: 75px;text-align:right;" type="text" name="dayscost_from<?php echo $i; ?>" id="dayscost_from<?php echo $i; ?>" >

                                                              </div>

                                                              <div id="additional_costs_limit<?php echo $i; ?>" style="<?php if ( ( $costs_depends[$i]['type'] == '=') || ( $costs_depends[$i]['type'] == 'summ') ) echo "display:none;";?>float:left;">
                                                                  <?php _e('to','wpdev-booking'); ?>
                                                                  <input value="<?php echo $costs_depends[$i]['to']; ?>" maxlength="7" style="width: 75px;text-align:right;" type="text" name="dayscost_to<?php echo $i; ?>" id="dayscost_to<?php echo $i; ?>" >
                                                              </div>

                                                              <div  style="float:left;margin:0px 5px;line-height: 24px;">
                                                                  <?php
                                                                    if ($costs_depends[$i]['type'] == '=')  _e('day','wpdev-booking');
                                                                    else                                    _e('days','wpdev-booking');
                                                                  ?>
                                                              </div>
                                                          </div>

                                                          <div style="float:left;width:330px;text-align: left;"> =
                                                            <input value="<?php echo $costs_depends[$i]['cost']; ?>" maxlength="7" style="width: 75px;text-align:right;" type="text" name="dayscost<?php echo $i; ?>" id="dayscost<?php echo $i; ?>" >
                                                            <?php echo ' <span style="font-weight:normal;"  id="cost_days_row_help'.$i.'" >';
                                                                    if ($costs_depends[$i]['type'] != 'summ') {
                                                                        ?>
                                                                            <select name="cost_apply_to<?php echo $i; ?>" id="cost_apply_to<?php echo $i; ?>" style="width:220px;padding:3px 1px 1px 1px !important;" >
                                                                                <option <?php if ( $costs_depends[$i]['cost_apply_to'] == 'fixed') echo 'selected="selected"'; ?> value="fixed" ><?php echo get_bk_option( 'booking_paypal_curency' ), ' ';  _e('per 1 day','wpdev-booking');?></option>
                                                                                <option <?php if ( $costs_depends[$i]['cost_apply_to'] == '%') echo 'selected="selected"'; ?> value="%"     ><?php echo '% ';  _e('from the cost of 1 day ','wpdev-booking');?></option>
                                                                                <option <?php if ( $costs_depends[$i]['cost_apply_to'] == 'add') echo 'selected="selected"'; ?> value="add"   ><?php _e('Additional cost in USD per 1 day','wpdev-booking');?></option>
                                                                            </select>
                                                                        <?php
                                                                    } else  {
                                                                        ?>
                                                                            <select name="cost_apply_to<?php echo $i; ?>" id="cost_apply_to<?php echo $i; ?>" style="width:220px;padding:3px 1px 1px 1px !important;" >
                                                                                <option <?php if ( $costs_depends[$i]['cost_apply_to'] == 'fixed') echo 'selected="selected"'; ?> value="fixed" ><?php echo get_bk_option( 'booking_paypal_curency' ), ' ';  _e(' for all days!','wpdev-booking');?></option>
                                                                                <option <?php if ( $costs_depends[$i]['cost_apply_to'] == '%') echo 'selected="selected"'; ?> value="%"     ><?php echo '% ';  _e(' for all days!','wpdev-booking');?></option>
                                                                                <!--option <?php if ( $costs_depends[$i]['cost_apply_to'] == 'add') echo 'selected="selected"'; ?> value="add"   ><?php _e('Additional cost in USD per 1 day','wpdev-booking');?></option-->
                                                                            </select>
                                                                        <?php
                                                                        //echo __('for all days','wpdev-booking');
                                                                    }
                                                                  echo '</span>';
                                                            ?>
                                                          </div>


<div style="float:left;width:330px;text-align: left;">
    <select name="season_filter<?php echo $i; ?>" id="season_filter<?php echo $i; ?>" style="width:150px;padding:3px 1px 1px 1px !important;" >
           <option <?php if ($costs_depends[$i]['season_filter'] == 0 ) echo ' selected="SELECTED" '; ?>   value="0" ><?php echo __('Any days','wpdev-booking'); ?></option>
        <?php foreach ($filter_list as $value_filter) { ?>
           <option <?php if ($costs_depends[$i]['season_filter'] == $value_filter->id ) echo ' selected="SELECTED" '; ?>  value="<?php echo $value_filter->id; ?>" >
                <?php echo $value_filter->title; echo ' - '; echo strip_tags($this->get_filter_description($value_filter->filter)); ?>
           </option>
        <?php } ?>
    </select>
</div>

                                                          <div style="float:right;margin:0px 10px;">
                                                              <input class="button-secondary" type="button" value="<?php _e('Remove', 'wpdev-booking'); ?>"
                                                                       name="new_cost<?php echo $i; ?>"
                                                                       onclick="javascript:remove_new_days_cost_row(<?php echo $i; ?>);"/>
                                                          </div>

                                                       </div>

                                                <?php
                                                  $all_indexes .= $i .',';
                                                }
                                               ?>
                                            </td>
                                           </tr>
                                       </table>

                                        <div class="clear" style="height:10px;"></div>
                                        <input class="button-secondary" style="float:left;" type="button" value="<?php _e('Add new cost', 'wpdev-booking'); ?>" name="new_cost" onclick="javascript:add_new_days_cost_row();"/>
                                        <input class="button-primary"   style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="submit_cost_from_days"/>
                                        <input type="hidden" value="<?php echo $all_indexes; ?>" name="all_indexes"  id="all_indexes" />
                                        <div class="clear" style="height:10px;"></div>

                                    </form>
                                           <script type="text/javascript">
                                               var row__id = <?php echo $i; ?>;

                                               function remove_new_days_cost_row(row_id){
                                                    jQuery('#cost_days_row' + row_id ).remove();
                                                    var all_indexes = jQuery('#all_indexes').val();
                                                    var temp = all_indexes.split(',' + row_id + ',');
                                                    all_indexes =  temp.join(',');
                                                    jQuery('#all_indexes').val(  all_indexes  );
                                               }

                                               function add_new_days_cost_row(){

                                                   var d_html = ''

                                                   if ( (row__id % 2) == 1 )  d_html += '<div id="cost_days_row'+row__id+'" class="alternative_color" style="width:100%;height:24px;clear: both;padding:3px 0px;font-weight:bold;" >';
                                                   else                       d_html += '<div id="cost_days_row'+row__id+'" style="width:100%;height:24px;clear: both;padding:3px 0px;font-weight:bold;">';

                                                       d_html += '       <div style="float:left;width:15px;margin:5px;">';
                                                       d_html += '           <input  value="" class="costs_depends" type="checkbox" checked="CHECKED" ';
                                                       d_html += '               name="dayscost_is_active'+row__id+'"';
                                                       d_html += '               id="dayscost_is_active'+row__id+'" />';
                                                       d_html += '       </div>';

                                                      d_html += '       <div style="float:left;width:340px;">';
                                                      d_html += '           <div  style="float:left;margin:0px 5px;">';

                                                      d_html += '               <select style="width:90px;font-weight:bold;" name="dayscost_type'+row__id+'" id="dayscost_type'+row__id+'"';
                                                      d_html += '                       onchange="javascript:if ( ( this.options[this.selectedIndex].value != \'=\' ) && ( this.options[this.selectedIndex].value != \'summ\' ) ) document.getElementById(\'additional_costs_limit'+row__id+'\').style.display = \'block\';  else  document.getElementById(\'additional_costs_limit'+row__id+'\').style.display = \'none\';   \n\
                                                                                if ( this.options[this.selectedIndex].value != \'summ\' ) addRowForCustomizationCostDependsFromNumSellDays('+row__id+');   \n\
                                                                                else  addRowForCustomizationCostDependsFromNumSellDays4Summ('+row__id+');   \n\ //jQuery(\'#cost_days_row_help'+row__id+'\').html( \'<?php echo get_bk_option( 'booking_paypal_curency' ) ,' ' , __('for all days','wpdev-booking'); ?>\' );   \n\
" >';
                                                      d_html += '                     <option value="="><?php _e('For','wpdev-booking'); ?></option>';
                                                      d_html += '                     <option value=">" <?php echo 'selected="selected"'; ?>  ><?php _e('From','wpdev-booking'); ?></option>';
                                                      d_html += '                     <option value="summ"  ><?php _e('Together','wpdev-booking'); ?></option>';
                                                      d_html += '               </select>';

                                                      d_html += '              <input value="1" maxlength="7" style="width: 75px;text-align:right;" type="text" name="dayscost_from'+row__id+'" id="dayscost_from'+row__id+'" >';

                                                      d_html += '           </div>';

                                                      d_html += '           <div id="additional_costs_limit'+row__id+'" style="display:block;float:left;">';
                                                      d_html += '               <?php _e('to','wpdev-booking'); ?>';
                                                      d_html += '               <input value="2" maxlength="7" style="width: 75px;text-align:right;" type="text" name="dayscost_to'+row__id+'" id="dayscost_to'+row__id+'" >';
                                                      d_html += '           </div>';

                                                      d_html += '           <div  style="float:left;margin:0px 5px;line-height: 24px;">';
                                                      d_html += '               <?php _e('day(s)','wpdev-booking'); ?>';
                                                      d_html += '           </div>';
                                                      d_html += '       </div>';



                                                       d_html += '       <div style="float:left;width:330px;text-align: left;"> =';
                                                       d_html += '         <input value="<?php echo $cost; ?>" maxlength="7" style="width: 75px;text-align:right;" type="text" name="dayscost'+row__id+'" id="dayscost'+row__id+'" >';
                                                       d_html += '         <?php
                                                                                   echo ' <span style="font-weight:normal;" id="cost_days_row_help'?>'+row__id+'<?php
                                                                                   echo '" >'; ?>'+getRowForCustomizationCostDependsFromNumSellDays(row__id)+'<?php echo '</span>';
                                                                            ?>';
                                                       d_html += '       </div>';





                                                       d_html += '       <div style="float:left;width:330px;text-align: left;">';
                                                       d_html += '       <select name="season_filter'+row__id+'" id="season_filter'+row__id+'" style="width:150px;padding:3px 1px 1px 1px !important;" >';
                                                       d_html += '       <option selected="SELECTED" value="0" ><?php echo esc_js(__('Any days','wpdev-booking')); ?></option>';
                                                                         <?php foreach ($filter_list as $value_filter) { ?>
                                                       d_html += '       <option value="<?php echo $value_filter->id; ?>" >';
                                                       d_html += '       <?php echo esc_js($value_filter->title . ' - ' . strip_tags($this->get_filter_description($value_filter->filter)) ) ; ?>';
                                                       d_html += '       </option>';
                                                                         <?php } ?>
                                                       d_html += '       </select>';
                                                       d_html += '       </div>';



                                                       d_html += '        <div style="float:right;margin:0px 10px;">';
                                                       d_html += '            <input class="button-secondary" type="button" value="<?php _e('Remove', 'wpdev-booking'); ?>"';
                                                       d_html += '                   name="new_cost'+row__id+'"';
                                                       d_html += '                   onclick="javascript:remove_new_days_cost_row('+row__id+');"/>';
                                                       d_html += '        </div>';


                                                   d_html += '</div>';
                                                   jQuery('#all_indexes').val( jQuery('#all_indexes').val() + +row__id+ ','  );
                                                   jQuery('#costs_days_container').append(d_html);
                                                   row__id++;
                                               }
                                               </script>
                               </div> </div> </div>

                    <?php
                }


                // Show settings for saving deposit amount
                function show_setings_for_deposit_cost_amount(){

                    if (! isset($_GET['wpdev_edit_costs_deposit_payment']))           return;     // If we do not edit costs so then exit
                    
                    $edit_id = $_GET['wpdev_edit_costs_deposit_payment'];
                    $result = $this->get_booking_types($edit_id); // Main info according booking type
                    if ( count($result)>0 ) {
                       $title = $result[0]->title;
                       $cost = $result[0]->cost;
                    }

                    if ( isset( $_POST['submit_resource_deposit'] ) ) {
                            $resource_deposit_amount =                          $_POST['resource_deposit_amount'];
                            $resource_deposit_amount_apply_to =                 $_POST['resource_deposit_amount_apply_to'];  // fixed, %
                            if (isset($_POST['resource_deposit_is_active']))    $resource_deposit_is_active = 'On';
                            else                                                $resource_deposit_is_active = 'Off';

                            $fixed_deposit = array(
                                                    'amount'=>$resource_deposit_amount,
                                                    'type'=>$resource_deposit_amount_apply_to,
                                                    'active' => $resource_deposit_is_active
                                                  );
                            $this->set_bk_type_meta($edit_id,'fixed_deposit', serialize($fixed_deposit));

                    } else {
                            $fixed_deposit = $this->get_bk_type_meta($edit_id,'fixed_deposit');

                            if (count($fixed_deposit) > 0 ) {
                                if ( is_serialized( $fixed_deposit[0]->value ) ) $fixed_deposit = unserialize($fixed_deposit[0]->value);
                                else                                             $fixed_deposit = $fixed_deposit[0]->value;
                            }
                            else $fixed_deposit = array('amount'=>'100',
                                                        'type'=>'%',
                                                        'active' => 'On' );
                    }
                    $resource_deposit_amount            = $fixed_deposit['amount'];
                    $resource_deposit_amount_apply_to   = $fixed_deposit['type'];
                    $resource_deposit_is_active         = $fixed_deposit['active'];

                    $link = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-option&tab=cost';
                    ?>
                               <div class='meta-box'>  <div  class="postbox" > <h3 class='hndle'><span><?php _e('Set amount of deposit payment', 'wpdev-booking'); ?></span></h3> <div class="inside">
                                           <br />
                                    <form  name="cost_day_count" action="" method="post" id="cost_day_count" >
                                        <strong>
                                          <?php _e('Deposit amount','wpdev-booking'); ?>:
                                          <input value="<?php echo $resource_deposit_amount; ?>" maxlength="7" style="width: 75px;text-align:right;" type="text"
                                                 name="resource_deposit_amount"
                                                 id="resource_deposit_amount" >


                                           <select name="resource_deposit_amount_apply_to"
                                                   id="resource_deposit_amount_apply_to" >
                                                <option <?php if ( $resource_deposit_amount_apply_to == 'fixed') echo 'selected="selected"'; ?> value="fixed" ><?php  _e('fixed summ in','wpdev-booking');  echo ' ', get_bk_option( 'booking_paypal_curency' ); ?></option>
                                                <option <?php if ( $resource_deposit_amount_apply_to == '%') echo 'selected="selected"'; ?> value="%"     ><?php echo '% ';  _e('of payment','wpdev-booking');?></option>
                                           </select>
                                           &nbsp;&nbsp;&nbsp;&nbsp;
                                          <?php _e('Active/inactive','wpdev-booking'); ?>:
                                          <input <?php if($resource_deposit_is_active == 'On') echo 'checked="checked"'; ?>
                                              value="<?php echo $resource_deposit_is_active; ?>" class="costs_depends"
                                              name="resource_deposit_is_active"
                                              id="resource_deposit_is_active" type="checkbox"/>
                                          </strong>
                                          <?php echo ' '; _e('deposit payment for booking resource','wpdev-booking'); echo ': <strong>', $title, '</strong> ';   ?>
                                        
                                        <br /><br />
                                        <span>
                                             <?php
                                             if ($resource_deposit_is_active=='On') {
                                                $cost_dep = $cost;
                                                if ($resource_deposit_amount_apply_to == '%') $cost_dep = $cost_dep * $resource_deposit_amount / 100 ;
                                                else $cost_dep = $resource_deposit_amount;
                                                echo ' '; _e('Deposit payment summ','wpdev-booking'); echo ' ', $cost_dep, ' ', get_bk_option( 'booking_paypal_curency' );
                                             } else {
                                                 _e('Deposit payment is not active for booking resource','wpdev-booking'); echo ': <strong>', $title, '</strong> ';   
                                             }
                                                ?>
                                        </span>
                                        <div class="clear" style="height:10px;"></div>
                                        <input class="button-primary"   style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="submit_resource_deposit"/>
                                        <div class="clear" style="height:10px;"></div>

                                    </form>

                               </div> </div> </div>

                    <?php
                }

                function show_settings_for_activating_fixed_deposit(){
                    $link = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-resources&tab=cost';

                    if ( isset( $_POST['paypal_price_period'] ) ) {
                        if (isset($_POST['is_resource_deposit_payment_active'])) $is_resource_deposit_payment_active = 'On';
                        else                                                     $is_resource_deposit_payment_active = 'Off';
                        update_bk_option( 'booking_is_resource_deposit_payment_active' ,  $is_resource_deposit_payment_active );
                    }
                    $is_resource_deposit_payment_active   = get_bk_option( 'booking_is_resource_deposit_payment_active');


                    ?>
                       <tr valign="top" class="ver_premium_hotel">
                            <th scope="row">
                                <label for="is_resource_deposit_payment_active" ><?php _e('Deposit payment', 'wpdev-booking'); ?>:</label>
                            </th>
                            <td>
                                <input <?php if ($is_resource_deposit_payment_active == 'On') echo "checked";/**/ ?>  value="<?php echo $is_resource_deposit_payment_active; ?>" name="is_resource_deposit_payment_active" id="is_resource_deposit_payment_active" type="checkbox" style="margin:-3px 3px 0 0;" />
                                    <span class="description"><?php printf(__(' Check this checkbox if you want to use %sdeposit%s summ %spayment%s at the payment form, instead of full summ of booking.', 'wpdev-booking'),'<strong>','</strong>','<strong>','</strong>');
                                    printf(__(' You can make configuration of deposit summs for your booking resources %shere%s.', 'wpdev-booking'), '<a href="'.$link.'">', '</a>');?></span>
                            </td>
                        </tr>


                    <?php
                }

        //   B o o k i n g    r e s o u r c e     menu page from Settings booking menu ///////////////////////////////////////////////////
        function show_booking_types_cost(){
            global $wpdb;
            if ( isset( $_POST['submit_costs'] ) ) {
                $bk_types = $this->get_booking_types();

                foreach ($bk_types as $bt) {
                    if ( false === $wpdb->query($wpdb->prepare(
                            "UPDATE ".$wpdb->prefix ."bookingtypes SET title = '".$_POST['type_title'.$bt->id]."', cost = '".$_POST['type_price'.$bt->id]."' WHERE booking_type_id = " .  $bt->id) ) ){
                           bk_error('Error during updating to DB booking costs' ,__FILE__,__LINE__);
                    }

                }
                update_bk_option( 'booking_paypal_price_period' , $_POST['paypal_price_period'] );
            }


                    $this->show_specific_type_avalaibility_filter();
                    $this->show_specific_type_rate();
                    $this->show_specific_cost_depends_from_days_count();
                    $is_resource_deposit_payment_active   = get_bk_option( 'booking_is_resource_deposit_payment_active');
                    if ($is_resource_deposit_payment_active == 'On')
                        $this->show_setings_for_deposit_cost_amount();
            ?>

                      <div class='meta-box'>  <div  class="postbox" > <h3 class='hndle'><span><?php _e('Cost of each booking type', 'wpdev-booking'); ?></span></h3> <div class="inside">

                            <form  name="post_option_cost" action="" method="post" id="post_option_cost" >
                                <table style="width:100%;" class="resource_table0 booking_table" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <th style="width:220px;height:35px;"> <?php _e('Resource name', 'wpdev-booking'); ?> </th>
                                        <th style="width:200px;"> <?php _e('Resource Cost', 'wpdev-booking'); ?> </th>
                                         <?php if ( 0 /*class_exists('wpdev_bk_biz_l') */) { ?>
                                        <th style="width:100px; "> <?php _e('Capacity', 'wpdev-booking'); ?>  </th>
                                        <th style=""> <?php _e('Names of items from resource', 'wpdev-booking'); ?> </th>
                                         <?php } ?>
                                        <th style="text-align: center;"> <?php _e('Actions', 'wpdev-booking'); ?> </th>
                                         <?php make_bk_action('show_users_header_at_settings' ); ?>
                                    </tr>
                                <?php
                                    $alternative_color = '0';
                                    $bk_types = $this->get_booking_types();
//debuge($bk_types);
                                    $link = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-option&tab=cost';
                                    foreach ($bk_types as $bt) {
                                        if ( $alternative_color == '')  $alternative_color = ' class="alternative_color" ';
                                        else $alternative_color = '';
                                        if (isset($_GET['wpdev_edit_avalaibility'])) if ($_GET['wpdev_edit_avalaibility'] == $bt->id) $alternative_color = ' class="edited_row_now" ';
                                        if (isset($_GET['wpdev_edit_rates'])) if ($_GET['wpdev_edit_rates'] == $bt->id) $alternative_color = ' class="edited_row_now" ';
                                        if (isset($_GET['wpdev_edit_costs_from_days'])) if ($_GET['wpdev_edit_costs_from_days'] == $bt->id) $alternative_color = ' class="edited_row_now" ';
                                        if (isset($_GET['wpdev_edit_costs_deposit_payment'])) if ($_GET['wpdev_edit_costs_deposit_payment'] == $bt->id) $alternative_color = ' class="edited_row_now" ';
                                        ?>
                                        <tr>
                                            <td style="font-size:11px;<?php if ( (isset($bt->parent)) && ($bt->parent != 0 ) ) { echo 'padding-left:50px;'; } ?>" <?php echo $alternative_color; ?> >
                                                <input  style="<?php if ( (isset($bt->parent)) && ($bt->parent == 0 ) ){
                              echo 'width:210px;font-weight:bold;';
                          } else {
                              echo 'width:170px;font-size:11px;';
                          } ?>" maxlength="17" type="text" value="<?php echo $bt->title; ?>" name="type_title<?php echo $bt->id; ?>" id="type_title<?php echo $bt->id; ?>">
                                            </td>
                                            <td <?php echo $alternative_color; ?> ><input  style="width:75px;" maxlength="7" type="text" value="<?php echo $bt->cost; ?>" name="type_price<?php echo $bt->id; ?>" id="type_price<?php echo $bt->id; ?>">
 <?php echo ' <span style="font-weight:bold;color:#444;font-size:11px;text-shadow:1px 0px 0px #FFF;">', get_bk_option( 'booking_paypal_curency' ); ?>                                                                          
                                                                              <?php if( get_bk_option( 'booking_paypal_price_period' ) == 'day')    _e('for 1 day', 'wpdev-booking');    ?>
                                                                              <?php if( get_bk_option( 'booking_paypal_price_period' ) == 'night')  _e('for 1 night', 'wpdev-booking');  ?>
                                                                              <?php if( get_bk_option( 'booking_paypal_price_period' ) == 'fixed')  _e('fixed deposit', 'wpdev-booking');?>
                                                                              <?php if( get_bk_option( 'booking_paypal_price_period' ) == 'hour')   _e('for 1 hour', 'wpdev-booking');   ?>
                                                                          <?php echo "</span>"; ?>
                                                                   
                                            </td>
                                            
                                            <?php make_bk_action('show_hotel_number_of_subtypes', $bt, $alternative_color) ?>


                                            <td style="font-size:11px;text-align: center;" <?php echo $alternative_color; ?> <a> </a>
                                                <a rel="tooltip" class="tooltip_bottom button" title="<?php echo __('Settings rates for days, depends from filter settings.','wpdev-booking'); ?>" href="<?php echo $link.'&wpdev_edit_rates=' . $bt->id ; ?>"><?php _e('Rates', 'wpdev-booking'); ?></a>&nbsp;&nbsp;
                                                <?php  if( get_bk_option( 'booking_paypal_price_period' ) == 'day') { ?>
                                                 <a  rel="tooltip" class="tooltip_bottom button" title="<?php echo __('Setting cost, which depends from number of selected days for booking','wpdev-booking'); ?>"  href="<?php echo $link.'&wpdev_edit_costs_from_days=' . $bt->id ; ?>"><?php _e('Cost for selected days', 'wpdev-booking'); ?></a>&nbsp;&nbsp;
                                                 <?php } ?>
<?php
                    $is_resource_deposit_payment_active   = get_bk_option( 'booking_is_resource_deposit_payment_active');
                    if ($is_resource_deposit_payment_active == 'On') {

?>                                                <span style="margin:7px 15px 7px 0px;  border-left:1px solid #EEEEEE; border-right:1px solid #CCCCCC; height:12px; line-height:14px; padding:3px 0; width:0;"></span>
                                                 <a  rel="tooltip" class="tooltip_bottom button" title="<?php echo __('Setting amount of deposit payment for payment form','wpdev-booking'); ?>"  href="<?php echo $link.'&wpdev_edit_costs_deposit_payment=' . $bt->id ; ?>"><?php _e('Deposit amount', 'wpdev-booking'); ?></a>&nbsp;&nbsp;
<?php } ?>                                                <span style="margin:7px 15px 7px 0px;  border-left:1px solid #EEEEEE; border-right:1px solid #CCCCCC; height:12px; line-height:14px; padding:3px 0; width:0;"></span>
                                                <a rel="tooltip" class="tooltip_bottom button" title="<?php echo __('Setting availability for days, depends from filter settings.','wpdev-booking'); ?>" href="<?php echo $link.'&wpdev_edit_avalaibility=' . $bt->id ; ?>"><?php _e('Availability', 'wpdev-booking'); ?></a>&nbsp;&nbsp;
                                            </td>
                                            <?php make_bk_action('show_users_collumn_at_settings', $bt , $alternative_color ); ?>
                                       </tr>
                                <?php } ?>
                                </table>
                                <div class="clear" style="height:10px;"></div>
                                 <span class="description"><?php
                                    _e('Please, enter cost', 'wpdev-booking');
                                    ?>
                                     <select id="paypal_price_period" name="paypal_price_period">
                                         <option <?php if( get_bk_option( 'booking_paypal_price_period' ) == 'day') echo "selected"; ?> value="day"><?php _e('for 1 day', 'wpdev-booking'); ?></option>
                                         <option <?php if( get_bk_option( 'booking_paypal_price_period' ) == 'night') echo "selected"; ?> value="night"><?php _e('for 1 night', 'wpdev-booking'); ?></option>
                                         <option <?php if( get_bk_option( 'booking_paypal_price_period' ) == 'fixed') echo "selected"; ?> value="fixed"><?php _e('fixed deposit', 'wpdev-booking'); ?></option>
                                         <?php //if ( class_exists('wpdev_bk_time')) { ?>
                                         <option <?php if( get_bk_option( 'booking_paypal_price_period' ) == 'hour') echo "selected"; ?> value="hour"><?php _e('for 1 hour', 'wpdev-booking'); ?></option>
                                         <?php //} ?>
                                     </select>
                                    <?php
                                    _e('of each booking resources. Enter only digits.', 'wpdev-booking');
                                 ?></span>
                                <br /><br /><span><?php printf(__('You can Add or Delete booking resources at the top of this %spage%s', 'wpdev-booking'),'<a href="'.'admin.php?page='. WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME .'wpdev-booking'.'">','</a>'); ?></span>
                                 <div class="clear" style="height:1px;"></div>
                                <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="submit_costs"/>
                                <div class="clear" style="height:10px;"></div>

                            </form>

                       </div> </div> </div>

            <?php

            

        }

                // Get description of each season filter in human language
                function get_filter_description($filter){
                    if ( is_serialized( $filter ) )  $filter = unserialize($filter);

                    $result = ''; $description = '';
                    $weekdays = array(__('Su','wpdev-booking'),__('Mo','wpdev-booking'),__('Tu','wpdev-booking'),__('We','wpdev-booking'),__('Th','wpdev-booking'),__('Fr','wpdev-booking'),__('Sa','wpdev-booking'));
                    $monthes =  array(0,__('Jan','wpdev-booking'),__( 'Feb','wpdev-booking'),__( 'Mar','wpdev-booking'),__( 'Apr','wpdev-booking'),__('May','wpdev-booking'),__('Jun','wpdev-booking'),__( 'Jul','wpdev-booking'),__( 'Aug','wpdev-booking'),__( 'Sep','wpdev-booking'),__( 'Oct','wpdev-booking'),__( 'Nov','wpdev-booking'),__( 'Dec','wpdev-booking') );

                    // Time availability
                    if (  (! empty($filter['start_time']))  &&  (! empty($filter['start_time']))  ) {
                         $description .= __('From', 'wpdev-booking') . ' <strong>' . $filter['start_time'] . '</strong> ' .  __('to', 'wpdev-booking') . ' <strong>' . $filter['end_time'] . '</strong> ' . __('time', 'wpdev-booking') ;
                    } else {
                            //debuge($filter);
                            //Week days
                            $cnt = 0;
                            foreach ($filter['weekdays'] as $key => $value) {
                                if ($value == 'On') {
                                    if ($result !=='') $result .=', ';
                                    $result .= $weekdays[$key];
                                    $cnt++;
                                }
                            }
                            if ( ($result !=='' )||($cnt == 0) ) {
                                if ($cnt ==7)
                                    $description .= '';
                                elseif ($cnt == 0)
                                    return '<span style="color:#ff0000;font-weight:bold;">' . __('No days', 'wpdev-booking') . '</span>';
                                else
                                    $description .= __('Every', 'wpdev-booking') . ' <strong>' . $result . '</strong> ';
                            }


                            //Days
                            $cnt = 0;$result='';
                            foreach ($filter['days'] as $key => $value) {
                                if ($value == 'On') {
                                    if ($result !=='') $result .=', ';
                                    $result .=  $key ;
                                    $cnt++;
                                }
                            }
                            if ( ($result !=='' )||($cnt == 0) ) {
                                if ($cnt ==31) {
                                   if ($description == '')  $description .= 'Each day ';
                                   else                     $description .= 'on each day ';
                                } elseif ($cnt == 0)
                                    return '<span style="color:#ff0000;font-weight:bold;">' . __('No days', 'wpdev-booking') . '</span>';
                                else {
                                   if ($description == '')  $description .= 'On each '. ' <strong>' . $result . '</strong> ';
                                   else                     $description .= 'on each ' . ' <strong>' . $result . '</strong> ';
                                }

                            }

                            //Monthes
                            $cnt = 0;$result='';
                            foreach ($filter['monthes'] as $key => $value) {
                                if ($value == 'On') {
                                    if ($result !=='') $result .=', ';
                                    $result .=  $monthes[$key]; ;
                                    $cnt++;
                                }
                            }
                            if ( ($result !=='' )||($cnt == 0) ) {
                                if ($cnt ==12)
                                    $description .= 'of every month ';
                                elseif ($cnt == 0)
                                    return '<span style="color:#ff0000;font-weight:bold;">' . __('No days', 'wpdev-booking') . '</span>';
                                else
                                    $description .= __('of', 'wpdev-booking') . ' <strong>' . $result . '</strong> ';
                            }


                            //Years
                            $cnt = 0;$result='';
                            foreach ($filter['year'] as $key => $value) {
                                if ($value == 'On') {
                                    if ($result !=='') $result .=', ';
                                    $result .=  $key ;
                                    $cnt++;
                                }
                            }
                            if ( ($result !=='' )||($cnt == 0) ) {
                                if ($cnt == 0)
                                    return '<span style="color:#ff0000;font-weight:bold;">' . __('No days', 'wpdev-booking') . '</span>';
                                else
                                    $description .=   ' <strong>' . $result . '</strong>';
                            }
                    }
                    return $description;

                }

        //   S e a s o n     f i l t e r          menu page from Settings booking menu ///////////////////////////////////////////////////
        function show_booking_date_filter(){
            global $wpdb;

            $filter_week_day  = array();
            $filter_month_day = array();
            $filter_month = array();
            $filter_year = array();
            $filter_start_time = '';
            $filter_end_time = '';
            $wpdev_edit_id = 0;
            // Delete filter
            if (isset($_GET['wpdev_delete'])) {
                $delete_id = $_GET['wpdev_delete'];
                $sql_list = $wpdb->get_results($wpdb->prepare( "SELECT count(booking_filter_id) as count FROM ".$wpdb->prefix ."booking_seasons WHERE booking_filter_id = ". $delete_id  ));
                if ($sql_list[0]->count > 0 ) {
                    $sql = "DELETE FROM ".$wpdb->prefix ."booking_seasons WHERE booking_filter_id = ". $delete_id  ;
                    if ( false === $wpdb->query($wpdb->prepare($sql)) ){
                       echo '<div class="error_message ajax_message textleft" style="font-size:12px;font-weight:bold;">';
                        bk_error('Error during deleting from DB booking filters' ,__FILE__,__LINE__); echo   '</div>';
                    }else echo '<div class="info_message ajax_message textleft" style="font-size:12px;font-weight:bold;">'.__('Filter deleted successfully', 'wpdev-booking').'</div>';
                }
                echo '<script type="text/javascript">jQuery(".warning_message").animate({opacity:1},10000).fadeOut(2000);jQuery(".error_message").animate({opacity:1},10000).fadeOut(2000);jQuery(".info_message").animate({opacity:1},5000).fadeOut(2000);</script>';
            }


            //Add new filter
            if ( isset( $_POST['filter_name'] ) ) {
                $filter=array(); $filter['weekdays']=array();$filter['days']=array();$filter['monthes']=array();$filter['year']=array();

                // Time  ////////////////////////////////////////////////////////////////////////////////////////////////////
                if (isset ($_POST[ 'filter_start_time' ])) $filter_start_time = $_POST[ 'filter_start_time' ];
                $filter['start_time'] = $filter_start_time;
                if (isset ($_POST[ 'filter_end_time' ]))   $filter_end_time = $_POST[ 'filter_end_time' ];
                $filter['end_time'] = $filter_end_time;
                if ( (! empty($filter_start_time)) && (! empty($filter_end_time)) )   $globalswitcher = 'On';
                else                                                                  $globalswitcher = '';
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////

                //Weekdays
                for ($k = 0; $k < 7; $k++) {  // Days of week
                   if (isset ($_POST[ 'filter_week_day' . $k ])) $filter_week_day[$k] = 'On';
                   else                                          $filter_week_day[$k] = 'Off';
                   if (! empty($globalswitcher)) $filter['weekdays'][$k] = $globalswitcher;               //Use Globalswitcher, when time is set, so all other have to be On
                   else                          $filter['weekdays'][$k] = $filter_week_day[$k];
                }

                //Days
                for ($k = 1; $k < 32; $k++) {  // Days of month
                   if (isset ($_POST[ 'filter_month_day' . $k ])) $filter_month_day[$k] = 'On';
                   else                                           $filter_month_day[$k] = 'Off';
                   if (! empty($globalswitcher)) $filter['days'][$k] = $globalswitcher;               //Use Globalswitcher, when time is set, so all other have to be On
                   else                          $filter['days'][$k] = $filter_month_day[$k];
                }

                //Monthes
                for ($k = 1; $k < 13; $k++) {  // Days of month
                   if (isset ($_POST[ 'filter_month' . $k ])) $filter_month[$k] = 'On';
                   else                                       $filter_month[$k] = 'Off';
                   if (! empty($globalswitcher)) $filter['monthes'][$k] = $globalswitcher;               //Use Globalswitcher, when time is set, so all other have to be On
                   else                          $filter['monthes'][$k] = $filter_month[$k];
                }

                //Years
                for ($k = 2009; $k < 2023; $k++) {  // Days of month
                   if (isset ($_POST[ 'filter_year' . $k ])) $filter_year[$k] = 'On';
                   else                                      $filter_year[$k] = 'Off';
                   if (! empty($globalswitcher)) $filter['year'][$k] = $globalswitcher;               //Use Globalswitcher, when time is set, so all other have to be On
                   else                          $filter['year'][$k] = $filter_year[$k];
                }




                $ser_filter = serialize  ($filter);
                $is_insert = false;
                if ($_POST['wpdev_edit_id']>0)
                    $sql = "UPDATE  ".$wpdb->prefix ."booking_seasons SET title = '".$_POST['filter_name']."', filter = '".$ser_filter."' WHERE booking_filter_id = ".$_POST['wpdev_edit_id'];
                else {
                    $sql = "INSERT INTO ".$wpdb->prefix ."booking_seasons ( title, filter ) VALUES ( '".$_POST['filter_name']."', '".$ser_filter."')";
                    $is_insert = true;
                }
                if ( false === $wpdb->query($wpdb->prepare($sql)) ){
                   echo '<div class="error_message ajax_message textleft" style="font-size:12px;font-weight:bold;">'; bk_error('Error during updating to DB booking filters',__FILE__,__LINE__ ); echo  '</div>';
                }else {
                    if ($is_insert) {
                        $newid = (int) $wpdb->insert_id;
                        make_bk_action('added_new_season_filter',$newid);                    
                    }
                    echo '<div class="info_message ajax_message textleft" style="font-size:12px;font-weight:bold;">'.__('Filter saved', 'wpdev-booking').'</div>';
                }

                //JS for hide messages
                echo '<script type="text/javascript">jQuery(".warning_message").animate({opacity:1},10000).fadeOut(2000);jQuery(".error_message").animate({opacity:1},10000).fadeOut(2000);jQuery(".info_message").animate({opacity:1},5000).fadeOut(2000);</script>';
            }

            //Edit Filter
            if (isset($_GET['wpdev_edit'])) {
                $wpdev_edit_id = $_GET['wpdev_edit'];
                $sql_list = $wpdb->get_results($wpdb->prepare( "SELECT booking_filter_id as id, title, filter FROM ".$wpdb->prefix ."booking_seasons WHERE booking_filter_id = ". $wpdev_edit_id  ));
                if (count($sql_list) == 0 ) {
                    $wpdev_edit_id = 0;
                 } else {
                     $filter_name = $sql_list[0]->title;
                     if ( is_serialized( $sql_list[0]->filter ) )  $my_edit_filter = unserialize($sql_list[0]->filter);
                     else                             $my_edit_filter = $sql_list[0]->filter;
                        $filter_week_day  = $my_edit_filter['weekdays'];
                        $filter_month_day = $my_edit_filter['days'];
                        $filter_month     = $my_edit_filter['monthes'];
                        $filter_year      = $my_edit_filter['year'];
                        if (isset($my_edit_filter['start_time'])) $filter_start_time = $my_edit_filter['start_time'];
                        if (isset($my_edit_filter['end_time']))   $filter_end_time   = $my_edit_filter['end_time'];
                 }
            }
            ?>
              <div class='meta-box'>  <div  class="postbox" > <h3 class='hndle'><span><?php _e('Date filter', 'wpdev-booking'); ?></span></h3> <div class="inside">
                            <form  name="post_option_cost" action="" method="post" id="post_option_filter" >
                                <input id="wpdev_edit_id"  type="hidden"   value="<?php echo $wpdev_edit_id; ?>" name="wpdev_edit_id"/>
                                <div style="margin:10px 3px;">
                                    <a  <?php if ($wpdev_edit_id ==0 ) { ?>
                                        href="#" onclick="javascript: jQuery('#new_filter_div').slideToggle('normal');"
                                        <?php } else { ?>
                                        href="admin.php?page=<?php echo WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME; ?>wpdev-booking-option&tab=filter&filterdisplay=1"
                                        <?php } ?>
                                        style="text-decoration: none;font-weight: bold;font-size: 11px;">+ <span style="text-decoration: underline;"><?php _e('Add new filter', 'wpdev-booking'); ?></span></a>
                                </div>

                                <div id="new_filter_div" <?php if (($wpdev_edit_id ==0 ) && (! isset($_GET['filterdisplay'])) ) { ?> style="display:none;" <?php } ?> >
                                    <div style="margin:15px 10px;">
                                        <label for="filter_name" style="font-weight:bold;font-size:13px;" ><?php _e('Name of filter', 'wpdev-booking'); ?>:</label>
                                        <input id="filter_name" class="regular-text code" type="text" size="45" value="<?php if (isset($filter_name)) echo $filter_name; ?>" name="filter_name"/>
                                        <span class="description"><?php printf(__('Type your %sfilter name%s ', 'wpdev-booking'),'<b>','</b>');?></span>
                                    </div>

                                    <div id="hour_days_switcher">
                                        <h2 for="hour_days_switcher" ><?php _e('Time or days filter', 'wpdev-booking'); ?>:
                                            <input onchange="javascript:if (this.value == 'days') { document.getElementById('filter_start_time').value='';  document.getElementById('filter_end_time').value=''; document.getElementById('days_filters').style.display='block';  document.getElementById('hour_filters').style.display='none'; }" type="radio" name="time_days_switcher" <?php if ( ( empty($filter_start_time)) && ( empty($filter_end_time)) ) echo 'checked="checked"'; ?>  value="days"> <span class="description" style="background:#5b1;padding:1px 5px 2px;color:#FFF;font-size:16px;font-weight:bold;font-family:Georgia,Times,serif;"><?php _e('days', 'wpdev-booking');?></span>
                                            <input onchange="javascript:if (this.value == 'time') { document.getElementById('days_filters').style.display='none';  document.getElementById('hour_filters').style.display='block'; }" type="radio" name="time_days_switcher" <?php if ( (! empty($filter_start_time)) && (! empty($filter_end_time)) ) echo 'checked="checked"'; ?> value="time"> <span class="description" style="background:#e33;padding:1px 5px 2px;color:#FFF;font-size:16px;font-weight:bold;font-family:Georgia,Times,serif;"><?php _e('time', 'wpdev-booking') ; echo ' (Beta)' ;?></span>
                                        </h2>
                                    </div>

                                    <div <?php if ( (! empty($filter_start_time)) && (! empty($filter_end_time)) ) echo 'style="display:none;"'; ?> id="days_filters">

                                        <div style="width:140px;" class="filter_div">
                                            <div style="width:120px;border:0px solid red;font-weight: bold;background:#f3f3f3;font-size:12px;margin:0px 0px 10px;border-bottom: 1px solid #ccc;width:120px;padding:10px 10px 10px;">
                                                <input id="filter_week_day_all" name="filter_week_day_all"   type="checkbox"
                                                       onclick="javascript:setCheckBoxInTable(this.checked, 'filter_week');" />
                                                    <?php _e('Days of week', 'wpdev-booking'); ?>
                                            </div>
                                            <div class="filter_inner">
                                                    <input id="filter_week_day0" class="filter_week" name="filter_week_day0" <?php for($nnn=0;$nnn<7;$nnn++){ if (! isset($filter_week_day[$nnn])) {$filter_week_day[$nnn]='Off';} }  if ($filter_week_day[0] == 'On') echo "checked"; ?>  value="<?php echo $filter_week_day[0]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('Sunday', 'wpdev-booking'); ?></span><br/>
                                                    <input id="filter_week_day1" class="filter_week" name="filter_week_day1" <?php if ($filter_week_day[1] == 'On') echo "checked"; ?>  value="<?php echo $filter_week_day[1]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('Monday', 'wpdev-booking'); ?></span><br/>
                                                    <input id="filter_week_day2" class="filter_week" name="filter_week_day2" <?php if ($filter_week_day[2] == 'On') echo "checked"; ?>  value="<?php echo $filter_week_day[2]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('Tuesday', 'wpdev-booking'); ?></span><br/>
                                                    <input id="filter_week_day3" class="filter_week" name="filter_week_day3" <?php if ($filter_week_day[3] == 'On') echo "checked"; ?>  value="<?php echo $filter_week_day[3]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('Wednesday', 'wpdev-booking'); ?></span><br/>
                                                    <input id="filter_week_day4" class="filter_week" name="filter_week_day4" <?php if ($filter_week_day[4] == 'On') echo "checked"; ?>  value="<?php echo $filter_week_day[4]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('Thursday', 'wpdev-booking'); ?></span><br/>
                                                    <input id="filter_week_day5" class="filter_week" name="filter_week_day5" <?php if ($filter_week_day[5] == 'On') echo "checked"; ?>  value="<?php echo $filter_week_day[5]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('Friday', 'wpdev-booking'); ?></span><br/>
                                                    <input id="filter_week_day6" class="filter_week" name="filter_week_day6" <?php if ($filter_week_day[6] == 'On') echo "checked"; ?>  value="<?php echo $filter_week_day[6]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('Saturday', 'wpdev-booking'); ?></span>
                                            </div>
                                        </div>

                                        <div style="width:240px;" class="filter_div">
                                            <div style="width:220px;" class="filter_div_title">
                                                <input id="filter_month_day_all" name="filter_month_day_all"   type="checkbox"
                                                       onclick="javascript:setCheckBoxInTable(this.checked, 'filter_month_day');" />
                                                    <?php _e('Days of month', 'wpdev-booking'); ?>
                                            </div>
                                            <div class="filter_inner">
                                                <div style="float:left;margin-right:10px;">
                                                    <input id="filter_month_day1" class="filter_month_day" name="filter_month_day1" <?php for($nnn=1;$nnn<32;$nnn++){ if (! isset($filter_month_day[$nnn])) {$filter_month_day[$nnn]='Off';} }  if ($filter_month_day[1] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[1]; ?>"  type="checkbox" />
                                                    <span class="description">1</span><br/>
                                                    <input id="filter_month_day2" class="filter_month_day" name="filter_month_day2" <?php if ($filter_month_day[2] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[2]; ?>"  type="checkbox" />
                                                    <span class="description">2</span><br/>
                                                    <input id="filter_month_day3" class="filter_month_day" name="filter_month_day3" <?php if ($filter_month_day[3] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[3]; ?>"  type="checkbox" />
                                                    <span class="description">3</span><br/>
                                                    <input id="filter_month_day4" class="filter_month_day" name="filter_month_day4" <?php if ($filter_month_day[4] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[4]; ?>"  type="checkbox" />
                                                    <span class="description">4</span><br/>
                                                    <input id="filter_month_day5" class="filter_month_day" name="filter_month_day5" <?php if ($filter_month_day[5] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[5]; ?>"  type="checkbox" />
                                                    <span class="description">5</span><br/>
                                                    <input id="filter_month_day6" class="filter_month_day" name="filter_month_day6" <?php if ($filter_month_day[6] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[6]; ?>"  type="checkbox" />
                                                    <span class="description">6</span><br/>
                                                    <input id="filter_month_day7" class="filter_month_day" name="filter_month_day7" <?php if ($filter_month_day[7] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[7]; ?>"  type="checkbox" />
                                                    <span class="description">7</span><br/>
                                                </div><div style="float:left;margin-right:10px;">
                                                    <input id="filter_month_day8" class="filter_month_day" name="filter_month_day8" <?php if ($filter_month_day[8] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[8]; ?>"  type="checkbox" />
                                                    <span class="description">8</span><br/>
                                                    <input id="filter_month_day9" class="filter_month_day" name="filter_month_day9" <?php if ($filter_month_day[9] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[9]; ?>"  type="checkbox" />
                                                    <span class="description">9</span><br/>
                                                    <input id="filter_month_day10" class="filter_month_day" name="filter_month_day10" <?php if ($filter_month_day[10] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[10]; ?>"  type="checkbox" />
                                                    <span class="description">10</span><br/>
                                                    <input id="filter_month_day11" class="filter_month_day" name="filter_month_day11" <?php if ($filter_month_day[11] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[11]; ?>"  type="checkbox" />
                                                    <span class="description">11</span><br/>
                                                    <input id="filter_month_day12" class="filter_month_day" name="filter_month_day12" <?php if ($filter_month_day[12] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[12]; ?>"  type="checkbox" />
                                                    <span class="description">12</span><br/>
                                                    <input id="filter_month_day13" class="filter_month_day" name="filter_month_day13" <?php if ($filter_month_day[13] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[13]; ?>"  type="checkbox" />
                                                    <span class="description">13</span><br/>
                                                    <input id="filter_month_day14" class="filter_month_day" name="filter_month_day14" <?php if ($filter_month_day[14] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[14]; ?>"  type="checkbox" />
                                                    <span class="description">14</span><br/>
                                                </div><div style="float:left;margin-right:10px;">
                                                    <input id="filter_month_day15" class="filter_month_day" name="filter_month_day15" <?php if ($filter_month_day[15] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[15]; ?>"  type="checkbox" />
                                                    <span class="description">15</span><br/>
                                                    <input id="filter_month_day16" class="filter_month_day" name="filter_month_day16" <?php if ($filter_month_day[16] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[16]; ?>"  type="checkbox" />
                                                    <span class="description">16</span><br/>
                                                    <input id="filter_month_day17" class="filter_month_day" name="filter_month_day17" <?php if ($filter_month_day[17] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[17]; ?>"  type="checkbox" />
                                                    <span class="description">17</span><br/>
                                                    <input id="filter_month_day18" class="filter_month_day" name="filter_month_day18" <?php if ($filter_month_day[18] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[18]; ?>"  type="checkbox" />
                                                    <span class="description">18</span><br/>
                                                    <input id="filter_month_day19" class="filter_month_day" name="filter_month_day19" <?php if ($filter_month_day[19] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[19]; ?>"  type="checkbox" />
                                                    <span class="description">19</span><br/>
                                                    <input id="filter_month_day20" class="filter_month_day" name="filter_month_day20" <?php if ($filter_month_day[20] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[20]; ?>"  type="checkbox" />
                                                    <span class="description">20</span><br/>
                                                    <input id="filter_month_day21" class="filter_month_day" name="filter_month_day21" <?php if ($filter_month_day[21] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[21]; ?>"  type="checkbox" />
                                                    <span class="description">21</span><br/>
                                                 </div><div style="float:left;margin-right:10px;">
                                                    <input id="filter_month_day22" class="filter_month_day" name="filter_month_day22" <?php if ($filter_month_day[22] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[22]; ?>"  type="checkbox" />
                                                    <span class="description">22</span><br/>
                                                    <input id="filter_month_day23" class="filter_month_day" name="filter_month_day23" <?php if ($filter_month_day[23] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[23]; ?>"  type="checkbox" />
                                                    <span class="description">23</span><br/>
                                                    <input id="filter_month_day24" class="filter_month_day" name="filter_month_day24" <?php if ($filter_month_day[24] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[24]; ?>"  type="checkbox" />
                                                    <span class="description">24</span><br/>
                                                    <input id="filter_month_day25" class="filter_month_day" name="filter_month_day25" <?php if ($filter_month_day[25] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[25]; ?>"  type="checkbox" />
                                                    <span class="description">25</span><br/>
                                                    <input id="filter_month_day26" class="filter_month_day" name="filter_month_day26" <?php if ($filter_month_day[26] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[26]; ?>"  type="checkbox" />
                                                    <span class="description">26</span><br/>
                                                    <input id="filter_month_day27" class="filter_month_day" name="filter_month_day27" <?php if ($filter_month_day[27] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[27]; ?>"  type="checkbox" />
                                                    <span class="description">27</span><br/>
                                                    <input id="filter_month_day28" class="filter_month_day" name="filter_month_day28" <?php if ($filter_month_day[28] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[28]; ?>"  type="checkbox" />
                                                    <span class="description">28</span><br/>
                                                 </div><div style="float:left;margin-right:10px;">
                                                    <input id="filter_month_day29" class="filter_month_day" name="filter_month_day29" <?php if ($filter_month_day[29] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[29]; ?>"  type="checkbox" />
                                                    <span class="description">29</span><br/>
                                                    <input id="filter_month_day30" class="filter_month_day" name="filter_month_day30" <?php if ($filter_month_day[30] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[30]; ?>"  type="checkbox" />
                                                    <span class="description">30</span><br/>
                                                    <input id="filter_month_day31" class="filter_month_day" name="filter_month_day31" <?php if ($filter_month_day[31] == 'On') echo "checked"; ?>  value="<?php echo $filter_month_day[31]; ?>"  type="checkbox" />
                                                    <span class="description">31</span><br/>
                                                 </div>
                                            </div>
                                        </div>

                                        <div style="width:200px;" class="filter_div">
                                            <div style="width:180px;" class="filter_div_title">
                                                <input id="filter_month_all" name="filter_month_all"   type="checkbox"
                                                       onclick="javascript:setCheckBoxInTable(this.checked, 'filter_month');" />
                                                    <?php _e('Months', 'wpdev-booking'); ?>
                                            </div>
                                            <div class="filter_inner">
                                                <div style="float:left;margin-right:10px;">
                                                    <input id="filter_month1" class="filter_month" name="filter_month1" <?php for($nnn=1;$nnn<13;$nnn++){ if (! isset($filter_month[$nnn])) {$filter_month[$nnn]='Off';}}  if ($filter_month[1] == 'On') echo "checked"; ?>  value="<?php echo $filter_month[1]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('January', 'wpdev-booking') ?></span><br/>
                                                    <input id="filter_month2" class="filter_month" name="filter_month2" <?php if ($filter_month[2] == 'On') echo "checked"; ?>  value="<?php echo $filter_month[2]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('February', 'wpdev-booking') ?></span><br/>
                                                    <input id="filter_month3" class="filter_month" name="filter_month3" <?php if ($filter_month[3] == 'On') echo "checked"; ?>  value="<?php echo $filter_month[3]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('March', 'wpdev-booking') ?></span><br/>
                                                    <input id="filter_month4" class="filter_month" name="filter_month4" <?php if ($filter_month[4] == 'On') echo "checked"; ?>  value="<?php echo $filter_month[4]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('April', 'wpdev-booking') ?></span><br/>
                                                    <input id="filter_month5" class="filter_month" name="filter_month5" <?php if ($filter_month[5] == 'On') echo "checked"; ?>  value="<?php echo $filter_month[5]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('May', 'wpdev-booking') ?></span><br/>
                                                    <input id="filter_month6" class="filter_month" name="filter_month6" <?php if ($filter_month[6] == 'On') echo "checked"; ?>  value="<?php echo $filter_month[6]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('June', 'wpdev-booking') ?></span><br/>
                                                    &nbsp;<br/>
                                                </div><div style="float:left;margin-right:10px;">
                                                    <input id="filter_month7" class="filter_month" name="filter_month7" <?php if ($filter_month[7] == 'On') echo "checked"; ?>  value="<?php echo $filter_month[7]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('July', 'wpdev-booking') ?></span><br/>
                                                    <input id="filter_month8" class="filter_month" name="filter_month8" <?php if ($filter_month[8] == 'On') echo "checked"; ?>  value="<?php echo $filter_month[8]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('August', 'wpdev-booking') ?></span><br/>
                                                    <input id="filter_month9" class="filter_month" name="filter_month9" <?php if ($filter_month[9] == 'On') echo "checked"; ?>  value="<?php echo $filter_month[9]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('September', 'wpdev-booking') ?></span><br/>
                                                    <input id="filter_month10" class="filter_month" name="filter_month10" <?php if ($filter_month[10] == 'On') echo "checked"; ?>  value="<?php echo $filter_month[10]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('October', 'wpdev-booking') ?></span><br/>
                                                    <input id="filter_month11" class="filter_month" name="filter_month11" <?php if ($filter_month[11] == 'On') echo "checked"; ?>  value="<?php echo $filter_month[11]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('November', 'wpdev-booking') ?></span><br/>
                                                    <input id="filter_month12" class="filter_month" name="filter_month12" <?php if ($filter_month[12] == 'On') echo "checked"; ?>  value="<?php echo $filter_month[12]; ?>"  type="checkbox" />
                                                    <span class="description"><?php _e('December', 'wpdev-booking') ?></span><br/>
                                                </div>
                                            </div>
                                        </div>

                                        <div style="width:150px;" class="filter_div">
                                            <div style="width:130px;" class="filter_div_title">
                                                <input id="filter_year_all" name="filter_year_all"   type="checkbox"
                                                       onclick="javascript:setCheckBoxInTable(this.checked, 'filter_year');" />
                                                    <?php _e('Years', 'wpdev-booking'); ?>
                                            </div>
                                            <div class="filter_inner">
                                                <div style="float:left;margin-right:10px;">
                                                    <?php  for($nnn=2009;$nnn<2023;$nnn++){ if (! isset($filter_year[$nnn])) {$filter_year[$nnn]='Off';}}   ?>

                                                    <input id="filter_year2011" class="filter_year" name="filter_year2011" <?php if ($filter_year[2011] == 'On') echo "checked"; ?>  value="<?php echo $filter_year[2011]; ?>"  type="checkbox" />
                                                    <span class="description">2011</span><br/>
                                                    <input id="filter_year2012" class="filter_year" name="filter_year2012" <?php if ($filter_year[2012] == 'On') echo "checked"; ?>  value="<?php echo $filter_year[2012]; ?>"  type="checkbox" />
                                                    <span class="description">2012</span><br/>
                                                    <input id="filter_year2013" class="filter_year" name="filter_year2013" <?php if ($filter_year[2013] == 'On') echo "checked"; ?>  value="<?php echo $filter_year[2013]; ?>"  type="checkbox" />
                                                    <span class="description">2013</span><br/>
                                                    <input id="filter_year2014" class="filter_year" name="filter_year2014" <?php if ($filter_year[2014] == 'On') echo "checked"; ?>  value="<?php echo $filter_year[2014]; ?>"  type="checkbox" />
                                                    <span class="description">2014</span><br/>
                                                    <input id="filter_year2015" class="filter_year" name="filter_year2015" <?php if ($filter_year[2015] == 'On') echo "checked"; ?>  value="<?php echo $filter_year[2015]; ?>"  type="checkbox" />
                                                    <span class="description">2015</span><br/>

                                                    <input id="filter_year2016" class="filter_year" name="filter_year2016" <?php if ($filter_year[2016] == 'On') echo "checked"; ?>  value="<?php echo $filter_year[2016]; ?>"  type="checkbox" />
                                                    <span class="description">2016</span><br/>
                                                </div><div style="float:left;margin-right:10px;">
                                                    <input id="filter_year2017" class="filter_year" name="filter_year2017" <?php if ($filter_year[2017] == 'On') echo "checked"; ?>  value="<?php echo $filter_year[2017]; ?>"  type="checkbox" />
                                                    <span class="description">2017</span><br/>

                                                    <input id="filter_year2018" class="filter_year" name="filter_year2018" <?php if ($filter_year[2018] == 'On') echo "checked"; ?>  value="<?php echo $filter_year[2018]; ?>"  type="checkbox" />
                                                    <span class="description">2018</span><br/>
                                                    <input id="filter_year2019" class="filter_year" name="filter_year2019" <?php if ($filter_year[2019] == 'On') echo "checked"; ?>  value="<?php echo $filter_year[2019]; ?>"  type="checkbox" />
                                                    <span class="description">2019</span><br/>
                                                    <input id="filter_year2020" class="filter_year" name="filter_year2020" <?php if ($filter_year[2020] == 'On') echo "checked"; ?>  value="<?php echo $filter_year[2020]; ?>"  type="checkbox" />
                                                    <span class="description">2020</span><br/>
                                                    <input id="filter_year2021" class="filter_year" name="filter_year2021" <?php if ($filter_year[2021] == 'On') echo "checked"; ?>  value="<?php echo $filter_year[2021]; ?>"  type="checkbox" />
                                                    <span class="description">2021</span><br/>
                                                    <input id="filter_year2022" class="filter_year" name="filter_year2022" <?php if ($filter_year[2022] == 'On') echo "checked"; ?>  value="<?php echo $filter_year[2022]; ?>"  type="checkbox" />
                                                    <span class="description">2022</span><br/>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div <?php if ( ( empty($filter_start_time)) && ( empty($filter_end_time)) ) echo 'style="display:none;"'; ?>  id="hour_filters">
                                        <div style="margin:15px 10px;">
                                            <div for="filter_start_time" style="font-weight:bold;font-size:13px;width:90px;line-height:24px;float:left;" ><?php _e('Start time', 'wpdev-booking'); ?>:</div>
                                            <input id="filter_start_time" class="wpdev-validates-as-time" type="text" size="45" value="<?php echo $filter_start_time; ?>" name="filter_start_time"/>
                                            <span class="description"><?php printf(__('Type your %sstart time%s ', 'wpdev-booking'),'<b>','</b>');?></span>
                                        </div>

                                        <div style="margin:15px 10px;">
                                            <div for="filter_end_time" style="font-weight:bold;font-size:13px;width:90px;line-height:24px;float:left;" ><?php _e('End time', 'wpdev-booking'); ?>:</div>
                                            <input id="filter_end_time" class="wpdev-validates-as-time" type="text" size="45" value="<?php echo $filter_end_time; ?>" name="filter_end_time"/>
                                            <span class="description"><?php printf(__('Type your %send time%s ', 'wpdev-booking'),'<b>','</b>');?></span>
                                        </div>

                                    </div>


                                    <div style="clear:both;width: 100%;height:1px;"></div>
                                    <input class="button-primary" style="float:right;margin-right: 20px;" type="submit" value="<?php if ($wpdev_edit_id ==0 ) _e('Add new filter', 'wpdev-booking'); else  _e('Save changes', 'wpdev-booking');  ?>" name="Submit"/>
                                </div>
                                <div class="clear" style="height:10px;"></div>

                            </form>

                          <div>
                              <table class="booking_table" cellpadding="0" cellspacing="0">
                                  <tr>
                                      <th style="width:15px;height:30px;"><?php _e('ID','wpdev-booking') ?></th>
                                      <th style="width:200px;"><?php _e('Name','wpdev-booking') ?></th>
                                      <th><?php _e('Filters','wpdev-booking') ?></th>
                                      <th style="width:40px;"><?php _e('Actions','wpdev-booking') ?></th>
                                  </tr>
                          <?php

                            $where = '';
                            $where = apply_bk_filter('multiuser_modify_SQL_for_current_user', $where);
                            if ($where != '') $where = ' WHERE ' . $where;

                            $filter_list = $wpdb->get_results($wpdb->prepare( "SELECT booking_filter_id as id, title, filter FROM ".$wpdb->prefix ."booking_seasons  ".$where." ORDER BY booking_filter_id DESC" ));
                            $link = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-resources&tab=filter';
                            foreach ($filter_list as $value) {
                                $td_class = '';
                                if ($wpdev_edit_id == $value->id) $td_class = ' class="edited_row_now" ';
                            ?>
                                  <tr>
                                      <td <?php echo $td_class; ?>><?php echo $value->id; ?></td>
                                      <td <?php echo $td_class; ?>><?php echo $value->title; ?></td>
                                      <td <?php echo $td_class; ?>><?php echo $this->get_filter_description($value->filter); ?></td>
                                      <td  <?php echo $td_class; ?> style="text-align:center;">
                                         <a href="<?php echo $link.'&wpdev_edit=' . $value->id ; ?>"  title="<?php  _e('Edit', 'wpdev-booking'); ?>" style="text-decoration:none;" ><img src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/edit_type.png" width="8" height="8" /></a>&nbsp;&nbsp;
                                         <a href="#"  title="<?php  _e('Delete', 'wpdev-booking'); ?>"
                                            onclick="javascript: var answer = confirm('<?php _e("Do you really want to delete?", 'wpdev-booking'); ?>'); if (! answer){ return false; } else {location.href='<?php echo $link.'&wpdev_delete=' . $value->id ; ?>';return false;}"
                                            style="text-decoration:none;" ><img src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/delete_type.png" width="8" height="8" /></a>
                                     </td>
                                  </tr>
                            <?php    echo '<div>';
                            }
                           ?></table>
                          </div>

                      </div>
                  </div>
              </div>
            <?php
        }



        // B L O C K s ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //
        // S e t t i n g s /////////////////////////////////////////////////////
        //
        // Settings for selecting default booking resource
        function settings_set_show_cost_in_tooltips(){
                if (isset($_POST['booking_highlight_cost_word'])) {
                     if (isset( $_POST['booking_is_show_cost_in_tooltips'] ))     $booking_is_show_cost_in_tooltips = 'On';
                     else                                                         $booking_is_show_cost_in_tooltips = 'Off';
                     update_bk_option( 'booking_is_show_cost_in_tooltips' ,  $booking_is_show_cost_in_tooltips );
                     update_bk_option( 'booking_highlight_cost_word' ,  $_POST['booking_highlight_cost_word'] );
                }
                $booking_is_show_cost_in_tooltips   = get_bk_option( 'booking_is_show_cost_in_tooltips');
                $booking_highlight_cost_word        = get_bk_option( 'booking_highlight_cost_word');
             ?>
                   <tr valign="top" class="ver_premium_plus">
                        <th scope="row">
                            <label for="range_selection_time_is_active" ><?php _e('Showing cost in tooltip', 'wpdev-booking'); ?>:</label>
                        </th>
                        <td>
                            <input <?php if ($booking_is_show_cost_in_tooltips == 'On') echo "checked";/**/ ?>  value="<?php echo $booking_is_show_cost_in_tooltips; ?>" name="booking_is_show_cost_in_tooltips" id="booking_is_show_cost_in_tooltips" type="checkbox"
                                 onclick="javascript: if (this.checked) jQuery('#togle_settings_cost_day_show').slideDown('normal'); else  jQuery('#togle_settings_cost_day_show').slideUp('normal');"
                                                                                                              />
                            <span class="description"><?php _e(' Check this checkbox if you want to show daily cost at the tooltip, when mouse over it.', 'wpdev-booking');?></span>
                        </td>
                    </tr>

                    <tr valign="top" class="ver_premium_plus"><td colspan="2">
                        <table id="togle_settings_cost_day_show" style="<?php if ($booking_is_show_cost_in_tooltips != 'On') echo "display:none;";/**/ ?>" class="hided_settings_table">
                            <tr>
                            <th scope="row"><label for="booking_highlight_cost_word" ><?php _e('Cost description', 'wpdev-booking'); ?>:</label></th>
                                <td><input value="<?php echo $booking_highlight_cost_word; ?>" name="booking_highlight_cost_word" id="booking_highlight_cost_word"  type="text"    />
                                    <span class="description"><?php printf(__('Type your %scost%s description', 'wpdev-booking'),'<b>','</b>');?></span>
                                    <?php make_bk_action('show_additional_translation_shortcode_help'); ?>
                                </td>
                            </tr>
                        </table>
                    </td></tr>
            <?php
        }




        // Get fields from booking form at the settings page or return false if no fields
        function get_fields_from_booking_form( $booking_form_content = '' ){
            if ( empty($booking_form_content) )
                $booking_form  = get_bk_option( 'booking_form' );
            else
                $booking_form = $booking_form_content;
            $types = 'text[*]?|email[*]?|time[*]?|textarea[*]?|select[*]?|checkbox[*]?|radio|acceptance|captchac|captchar|file[*]?|quiz';
            $regex = '%\[\s*(' . $types . ')(\s+[a-zA-Z][0-9a-zA-Z:._-]*)([-0-9a-zA-Z:#_/|\s]*)?((?:\s*(?:"[^"]*"|\'[^\']*\'))*)?\s*\]%';
            $fields_count = preg_match_all($regex, $booking_form, $fields_matches) ;

            if ($fields_count>0) return array($fields_count, $fields_matches);
            else return false;
        }

        // Show advanced cost managemnt block at paypal/cost Settings page
        function advanced_cost_management_settings(){

            if ( isset( $_POST['submit_advanced_costs'] ) ) {


                    $booking_forms_extended = get_bk_option( 'booking_forms_extended');
                    if ($booking_forms_extended !== false) {
                        if ( is_serialized( $booking_forms_extended ) ) $booking_forms_extended = unserialize($booking_forms_extended);
                        $booking_forms_extended[]=array('name'=>'standard','form'=>'');
                    } else $booking_forms_extended = array(array('name'=>'standard','form'=>''));

                    foreach ($booking_forms_extended as $bk_form_ext)
                    {

                            $booking_update_fields = $this->get_fields_from_booking_form($bk_form_ext['form']);

                            if ($bk_form_ext['name'] == 'standard') {
                               // $field__values = get_bk_option( 'booking_advanced_costs_values' );
                               // if ( $field__values !== false ) $field__values_unserilize = unserialize($field__values);
                                $field_sufix = '';
                            } else {
                               // $field__values = get_bk_option( 'booking_advanced_costs_values_for' . $bk_form_ext['name'] );
                               // if ( $field__values !== false ) $field__values_unserilize = unserialize($field__values);
                                $field_sufix = $bk_form_ext['name'];
                                $field_sufix = '__' . $field_sufix;
                            }
                            $field_sufix = str_replace(' ', '', $field_sufix); $field_sufix = str_replace('"', '', $field_sufix); $field_sufix = str_replace("'" , '', $field_sufix);
//debuge(array('$_POST'=>$_POST));
                            if ($booking_update_fields !== false) {

                                $booking_additional_cost_value = array();   // General main variable for serilise and save it

                                for ($i = 0; $i < $booking_update_fields[0]; $i++) {

                                    if ( ($booking_update_fields[1][1][$i] == 'checkbox') || ($booking_update_fields[1][1][$i] == 'checkbox*') || ($booking_update_fields[1][1][$i] == 'select') || ($booking_update_fields[1][1][$i] == 'select*') ){ // Right now working only with select boxes

                                        $field_update_name = trim($booking_update_fields[1][2][$i]);
                                        $field_update_values = trim($booking_update_fields[1][4][$i]);
//debuge($booking_update_fields , $field_update_name, $field_update_values  ) ;
                                        $booking_additional_cost_value[$field_update_name] = array();

                                        $fields_update_count_values = preg_match_all( '%\s*"[a-zA-Z0-9.:\s,\[\]/\\-_!@&-=+?~]{0,}"\s*%', $field_update_values, $fields_update_matches_values) ;
//debuge($fields_update_matches_values);
                                        for ($j = 0; $j < $fields_update_count_values; $j++) {
                                            $field_update_orig_value = trim(str_replace('"','',$fields_update_matches_values[0][$j]));
                                            $field_update_orig_value = trim(str_replace(' ','_',$field_update_orig_value));
                                            if ($field_update_orig_value == '') // Its simple checkbox set 0 index
                                                $booking_additional_cost_value[ $field_update_name ]['checkbox'] = $_POST['additional_cost_value_' . $field_update_name . $field_update_orig_value . $field_sufix ] ;
                                            else
                                                $booking_additional_cost_value[ $field_update_name ][ $field_update_orig_value ] = $_POST['additional_cost_value_' . $field_update_name . $field_update_orig_value . $field_sufix ] ;
                                        }
                                    }
                                }
                               // debuge($booking_additional_cost_value);die;
                                if ($bk_form_ext['name'] == 'standard') {
                                    update_bk_option( 'booking_advanced_costs_values'   , serialize($booking_additional_cost_value) );
                                }else {
                                    update_bk_option( 'booking_advanced_costs_values_for' . $bk_form_ext['name']  , serialize($booking_additional_cost_value) );
                                }
                            }


                    } // End FOR
                     if (isset( $_POST['booking_advanced_costs_calc_fixed_cost_with_procents'] ))  update_bk_option( 'booking_advanced_costs_calc_fixed_cost_with_procents'   , 'On' );
                     else                                                                   update_bk_option( 'booking_advanced_costs_calc_fixed_cost_with_procents'   , 'Off' );
                    

            }
            $booking_advanced_costs_calc_fixed_cost_with_procents = get_bk_option( 'booking_advanced_costs_calc_fixed_cost_with_procents' );
            ?>
                      <div class='meta-box'>  
                         <div <?php $my_close_open_win_id = 'bk_settings_costs_advanced_cost_managment'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');"><br></div>
                              <h3 class='hndle'><span><?php _e('Advanced cost managment', 'wpdev-booking'); ?></span></h3> <div class="inside">

                            <form  name="post_option_cost" action="" method="post" id="post_option_cost" >
<?php

$booking_forms_extended = get_bk_option( 'booking_forms_extended');
if ($booking_forms_extended !== false) {
    if ( is_serialized( $booking_forms_extended ) ) $booking_forms_extended = unserialize($booking_forms_extended);
    $booking_forms_extended[]=array('name'=>'standard','form'=>'');
} else $booking_forms_extended = array(array('name'=>'standard','form'=>''));

?>
              <div style="height:auto;" id="form_advanced_cost_block_menu">
                  <style type="text/css">
                      a.bktypetitlenew {
                          margin-right:0px;
                      }
                  </style>
                  <?php foreach ($booking_forms_extended as $value) { ?>

                      <?php if ( ('standard' == $value['name'] ) ) { $selected_bk_typenew = 'selected_bk_typenew'; } else { $selected_bk_typenew = ''; } ?>
                      <?php
                        $div_form_name = 'form_div' . $value['name'];
                        $div_form_name = str_replace(' ', '', $div_form_name);
                        $div_form_name = str_replace('"', '', $div_form_name);
                        $div_form_name = str_replace("'" , '', $div_form_name);
                      ?>

                      <div id="bktype<?php echo $div_form_name; ?>" class="topmenuitemborder <?php echo $selected_bk_typenew; ?>">
                            <?php echo '<a href="#'.$div_form_name.'" style="text-transform:capitalize;"
                             onmousedown="javascript:
                              jQuery(\'#form_advanced_cost_block_menu .topmenuitemborder\').removeClass(\'selected_bk_typenew\');
                              jQuery(\'#bktype'.$div_form_name.'\').addClass(\'selected_bk_typenew\');
                              jQuery(\'.wpdev_forms_div\').attr(\'style\',\'display:none;\');
                              document.getElementById(\''. $div_form_name. '\').style.display=\'block\';" class="bktypetitlenew '.' ">' .  $value['name']  . '</a>'; ?>
                      </div>



                  <?php  } ?>

              </div>

              <div style="clear:both; width:100%;height:auto;margin:10px 0px;padding:10px 0 0 5px;font-style: italic;"><?php printf(__('Advanced cost customization is requre select boxes from %sform fields customization page%s (%s - shortcode). Fields are show automatically here, if exist at the form.','wpdev-booking'), '<a href="admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-option&tab=form" >','</a>', '[select]'); ?></div>
              <div class="clear topmenuitemseparatorv" style="height:0px;clear:both;" ></div>
<?php




//debuge($booking_forms_extended);
foreach ($booking_forms_extended as $bk_form_ext)
{  $div_form_name = 'form_div' . $bk_form_ext['name']; $div_form_name = str_replace(' ', '', $div_form_name); $div_form_name = str_replace('"', '', $div_form_name); $div_form_name = str_replace("'" , '', $div_form_name);
   if ($bk_form_ext['name'] == 'standard') $mystyle = ''; else  $mystyle = 'display:none;';

   echo '<div style="border-bottom:1px solid #ccc;'.$mystyle.'" class="wpdev_forms_div" id="'.$div_form_name.'"><h2>'. __('Form','wpdev-booking') . ' '. $bk_form_ext['name'] . ': </h2>';

    ?>
                                    <?php
                                    $booking_fields = $this->get_fields_from_booking_form($bk_form_ext['form']);

                                    if ($booking_fields !== false) {
                                        $fields_count   = $booking_fields[0] ;
                                        $fields_matches = $booking_fields[1] ;
                                    } else { $fields_count = 0; }
                                    ?>
                                   <table class="form-table settings-table">
                                    <tbody>
                                        <?php

                                        if ($bk_form_ext['name'] == 'standard') {
                                            $field__values = get_bk_option( 'booking_advanced_costs_values' );
                                            if ( $field__values !== false ) {
                                                if ( is_serialized( $field__values ) )   $field__values_unserilize = unserialize($field__values);
                                                else                                     $field__values_unserilize = $field__values;
                                            }
                                            $field_sufix = '';
                                        } else {
                                            $field__values = get_bk_option( 'booking_advanced_costs_values_for' . $bk_form_ext['name'] );
                                            if ( $field__values !== false ) {
                                                if ( is_serialized( $field__values ) )   $field__values_unserilize = unserialize($field__values);
                                                else                                     $field__values_unserilize = $field__values;
                                            }
                                            $field_sufix = $bk_form_ext['name'];
                                            $field_sufix = '__' . $field_sufix;
                                        }
//debuge($field__values_unserilize);
                                        $field_sufix = str_replace(' ', '', $field_sufix);
                                        $field_sufix = str_replace('"', '', $field_sufix);
                                        $field_sufix = str_replace("'" , '', $field_sufix);
//debuge($fields_matches);
                                        for ($i = 0; $i < $fields_count; $i++) {

                                            if ( ($fields_matches[1][$i] == 'checkbox*') || ($fields_matches[1][$i] == 'checkbox') || ($fields_matches[1][$i] == 'select') || ($fields_matches[1][$i] == 'select*') ){ // Right now working only with select boxes

                                            $field__name = trim($fields_matches[2][$i]);
                                            $field__orig_value = trim($fields_matches[4][$i]);
                                            $fields_count_values = preg_match_all( '%\s*"[a-zA-Z0-9.:\s$,\[\]/\\-_!@&-=+?~]{0,}"\s*%', $field__orig_value, $fields_matches_values) ;
                                            ?>
                                                <tr valign="top">
                                                    <th scope="row">
                                                        <label for="paypal_is_active" ><?php _e('Aditional cost for', 'wpdev-booking'); echo ' <span style="color:#0d5;">' , $fields_matches[2][$i], '</span>'; ?>:</label>
                                                    </th>
                                                    <td> <div style="float:left;">
                                                        <?php
                                                          for ($j = 0; $j < $fields_count_values; $j++) { ?>
                                                            <?php
                                                            $field__value = '100%';
                                                            $field_orig_val = trim(str_replace('"','',$fields_matches_values[0][$j]));
                                                            $field_orig_val = trim(str_replace(' ','_',$field_orig_val));
//if ($i==1) { debuge($fields_matches_values, $fields_matches_values[0][$j]); }

                                                            if (   ($field__values)  !== false ) { // Default


                                                                if ($field_orig_val =='') {

                                                                   if ( isset($field__values_unserilize[ $field__name ]) )
                                                                       if ( isset($field__values_unserilize[ $field__name ][ 'checkbox' ]) )
                                                                            $field__value = $field__values_unserilize[ $field__name ][  'checkbox'  ];

                                                                } else {

                                                                   if ( isset($field__values_unserilize[ $field__name ]) )
                                                                       if ( isset($field__values_unserilize[ $field__name ][ $field_orig_val ]) )
                                                                            $field__value = $field__values_unserilize[ $field__name ][ $field_orig_val ];
                                                                }
                                                            }
                                                            ?>

                                                            <span style="font-weight:bold;font-size:13px;">
                                                              <?php echo $field_orig_val ; ?> </span>
                                                              <span style="font-weight:bold;"> = </span>
                                                              <input value='<?php echo $field__value; ?>'    style="width: 100px;text-align:left;" type="text"
                                                                     name="additional_cost_value_<?php echo $field__name, $field_orig_val, $field_sufix; ?>"
                                                                       id="additional_cost_value_<?php echo $field__name, $field_orig_val, $field_sufix; ?>" >
                                                              <br/>
                                                          <?php }
//if ($i==1) { debuge($field__orig_value, $fields_count_values);die; }
 //else continue;

                                                        ?> </div>
                                                        <div style="float:left;margin:2px 10px;width:620px;"><span class="description" style="font-size:12px;"><?php printf( __('Enter cost (%s) directly in format %s or %s for the whole booking in format %s or fixed amount per each selected day in format %s For example: %s or %s or %s', 'wpdev-booking') ,'<b>' . get_bk_option( 'booking_paypal_curency' ) . '</b>', '<b>"100"</b>'  , '<b>%</b>',  '<b>"150%"</b>,' ,  '<b>"50/day"</b>.' , '<b>200%</b>,' , '<b>25</b>,', '<b>30/day</b>,' );?></span></div>
                                                    </td>
                                                </tr>
                                            <?php
                                            } //End if select
                                        } // END FOREACH
                                        ?>
                                    </tbody>
                                   </table>
<?php echo "</div>";  } ?>
                                 <div class="clear" style="height:10px;"></div>

                                <div style="margin-top:20px;">

                                        <!--label for="booking_advanced_costs_calc_fixed_cost_with_procents" ><?php //_e('Cost calculation with time impact', 'wpdev-booking'); ?>:</label-->
                                    <input <?php if ($booking_advanced_costs_calc_fixed_cost_with_procents == 'On') echo "checked";/**/ ?>  value="<?php echo $booking_advanced_costs_calc_fixed_cost_with_procents; ?>" name="booking_advanced_costs_calc_fixed_cost_with_procents" id="booking_advanced_costs_calc_fixed_cost_with_procents" type="checkbox" style="margin:-3px 3px 0 0;" />
                                        <span class="description"><?php _e(' Check this checkbox, if you want that costs, which setup here as % for some options is apply also to the costs, which  is setup as a fixed values for some options.', 'wpdev-booking');?></span>
                                </div>

                                <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="submit_advanced_costs"/>
                                <div class="clear" style="height:10px;"></div>
                            </form>

                       </div> </div> </div>
            <?php
        }


        // Show Availability and Rates resource content list in selected tab menu
        function show_booking_availability_rates_settings_page(){ global $wpdb;
              $alternative_color = '0';
              $link = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-option&tab=resources';


            if ((isset($_POST['submit_resources']))) {

                $bk_types = get_bk_types(true);

                // Edit ////////////////////////////////////////////////////////
                if ( ($_POST['bulk_resources_action'] == 'blank' ) || ($_POST['bulk_resources_action'] == 'edit' ) ) {

                    foreach ($bk_types as $bt) {
                          //$sql_res_cost = apply_bk_filter('get_sql_4_update_bk_resources_cost', ''  , $bt );
                          //$sql_res = apply_bk_filter('get_sql_4_update_bk_resources', ''  , $bt );

                          if ( false === $wpdb->query( $wpdb->prepare(
                                  "UPDATE ".$wpdb->prefix ."bookingtypes SET cost = '".$_POST['resource_cost'.$bt->id]."' WHERE booking_type_id = ".$bt->id) )  )  bk_error('Error during updating to DB booking resources' ,__FILE__,__LINE__);
                    }

                }

                // Delete //////////////////////////////////////////////////////
                if  ($_POST['bulk_resources_action'] == 'delete' ) {

                  $delete_bk_id = '';
                  foreach ($bk_types as $bt) { // Delete - Get all ID for deletion
                      if (isset($_POST['resources_items_'.$bt->id]))
                          $delete_bk_id .= $bt->id . ',';
                  }

                  if (! empty($delete_bk_id)) {
                      $delete_bk_id = substr($delete_bk_id,0,-1);                 // Remove last Comma
                      $delete_sql = "DELETE FROM ".$wpdb->prefix ."bookingtypes WHERE booking_type_id IN (".$delete_bk_id.")";

                      if ( false === $wpdb->query($wpdb->prepare($delete_sql)) )  bk_error('Error during deleting booking resources',__FILE__,__LINE__ );
                  }

                }

            }


              $bk_types = get_bk_types(true);

              $all_id = array(array('id'=>0,'title'=>' - '));
              foreach ($bk_types as $btt) {
                    if (isset($btt->parent)) if ($btt->parent==0)  $all_id[] = array('id'=>$btt->id, 'title'=> $btt->title);
              }

              make_bk_action('wpdev_bk_booking_resource_page_before');
            ?>
            <div style="clear:both;width:100%;height:1px;"></div>
                    <div style="  float: right; margin-top: -90px; margin-right: 10px;" class="wpdevbk">
                    <form  name="booking_filters_formID" action="" method="post" id="booking_filters_formID" class=" form-search">

                        <?php if (isset($_REQUEST['wh_resource_id']))  $wh_resource_id = $_REQUEST['wh_resource_id'];                  //  {'1', '2', .... }
                              else                                    $wh_resource_id      = '';                    ?>
                        <input class="input" type="text" placeholder="<?php _e('Resource ID or Title', 'wpdev-booking'); ?>" name="wh_resource_id" id="wh_resource_id" value="<?php echo $wh_resource_id; ?>" >
                        <input class="input" type="hidden"  name="page_num" id="page_num" value="1" >
                        <button class="btn small" type="submit"><?php _e('Go', 'wpdev-booking'); ?></button>
                    </form>
                    </div><?php

            $max_num = apply_bk_filter('get_max_res_num_for_user_in_multiuser', false );
            $is_show_add_resource = 0;
            if (isset($_GET['wpdev_edit_rates'])) $is_show_add_resource = $_GET['wpdev_edit_rates'];
            if (isset($_GET['wpdev_edit_costs_from_days'])) $is_show_add_resource = $_GET['wpdev_edit_costs_from_days'];
            if (isset($_GET['wpdev_edit_avalaibility'])) $is_show_add_resource = $_GET['wpdev_edit_avalaibility'];
            if (isset($_GET['wpdev_edit_costs_deposit_payment'])) $is_show_add_resource = $_GET['wpdev_edit_costs_deposit_payment'];



            ?>
            <div style="width:100%;">

                <form  name="post_option_resources" action="" method="post" id="post_option_resources" >
                    <table style="width:99%;" class="resource_table0 booking_table" cellpadding="0" cellspacing="0">
                            <?php // Headers  ?>
                        <tr>
                            <th style="width:15px;"><input type="checkbox" onclick="javascript:jQuery('.resources_items').attr('checked', this.checked);" class="resources_items" id="resources_items_all"  name="resources_items_all" /></th>
                            <th style="width:10px;height:35px;border-left: 1px solid #BBBBBB;"> <?php _e('ID', 'wpdev-booking'); ?> </th>
                            <th style="height:35px;width:220px;"> <?php _e('Resource name', 'wpdev-booking'); ?> </th>
                            <?php make_bk_action('resources_settings_table_headers' ); ?>
                            <th style="text-align:center;"> <?php _e('Info', 'wpdev-booking'); ?> </th>

                        </tr>
                        <?php
                        if (! empty($bk_types))
                          foreach ($bk_types as $bt) {
                                  if ( $alternative_color == '')    $alternative_color = ' class="alternative_color" ';
                                  else                              $alternative_color = '';

                                  if ($is_show_add_resource == $bt->id ) $alternative_color = ' class="resource_line_selected" ';
                               ?>
                               <tr>
                                    <th <?php echo $alternative_color; ?> ><input type="checkbox" class="resources_items" id="resources_items_<?php echo $bt->id; ?>"  name="resources_items_<?php echo $bt->id; ?>" /></th>
                                    <td style="font-size:10px;font-weight: bold;border-right: 0px solid #ddd;border-left: 1px solid #aaa;text-align: center;" <?php echo $alternative_color; ?> ><?php echo $bt->id; ?></td>
                                    <td style="font-size:11px;<?php if (isset($bt->parent)) if ($bt->parent != 0 ) { echo 'padding-left:50px;'; } ?>" <?php echo $alternative_color; ?> >
                                        <span style="<?php if (isset($bt->parent)) if ($bt->parent == 0 ) { echo 'font-weight:bold;'; }?>"><?php echo $bt->title; ?></span>
                                        <!--input  maxlength="17" type="text"
                                            style="<?php  if (isset($bt->parent)) if ($bt->parent == 0 ) { echo 'width:210px;font-weight:bold;'; } else { echo 'width:170px;font-size:11px;'; } ?>"
                                            value="<?php echo $bt->title; ?>"
                                            name="type_title<?php echo $bt->id; ?>" id="type_title<?php echo $bt->id; ?>" /-->
                                        <?php if (isset($bt->parent)) if ($bt->parent == 0 ) { make_bk_action('resources_settings_after_title', $bt, $all_id, $alternative_color ); } ?>
                                    </td>

                                    <?php make_bk_action('resources_settings_table_collumns', $bt, $all_id, $alternative_color ); ?>
                                    <td style="font-size:10px;font-weight: bold;border-right: 0px solid #ddd;border-left: 1px solid #aaa;text-align: center;" <?php echo $alternative_color; ?> >
                                        <?php make_bk_action('resources_settings_table_info_collumns', $bt, $all_id, $alternative_color ); ?>
                                    </td>

                               </tr>
                               <?php
                          }
                                ?>

                            <tr>
                                <td style="width:15px;border-top: 1px solid #ccc;"></td>
                                <td style="width:10px;border-top: 1px solid #ccc;"></td>
                                <td style="height:35px;border-top: 1px solid #ccc;"></td>
                                <?php make_bk_action('resources_settings_table_footers' ); ?>
                                <td style="height:35px;border-top: 1px solid #ccc;"></td>
                            </tr>

                            </table>


                            <div class="clear" style="height:10px;"></div>
                            <select name="bulk_resources_action" id="bulk_resources_action" style="float:left;width:110px;margin-right:10px;margin-top: 3px;" >
                                <option value="blank"><?php _e('Bulk Actions','wpdev-booking') ?></option>
                                <option value="edit"v><?php _e('Edit','wpdev-booking') ?></option>
                                <option value="delete"><?php _e('Delete','wpdev-booking') ?></option>
                            </select>
                            <input class="button-primary" style="float:left;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="submit_resources"/>

                            <?php if (isset($_REQUEST['page_num'])) { ?>
                                <input class="input" type="hidden"  name="page_num" id="page_num" value="<?php echo $_REQUEST['page_num']; ?>" >
                            <?php } if (isset($_REQUEST['wh_resource_id'])) { ?>
                                <input class="input" type="hidden"  name="wh_resource_id" id="wh_resource_id" value="<?php echo $_REQUEST['wh_resource_id']; ?>" >
                            <?php } ?>

                            <div class="clear" style="height:5px;"></div>

                            <?php // Show Pagination
                            echo '<div class="wpdevbk">';
                            $active_page_num = (isset($_REQUEST['page_num']))?$_REQUEST['page_num']:1;
                            $items_count_in_page=get_bk_option( 'booking_resourses_num_per_page');
                            wpdevbk_show_pagination(get_booking_resources_count(), $active_page_num, $items_count_in_page,
                                    array('page','tab', 'wh_resource_id')//,'wpdev_edit_costs_from_days','wpdev_edit_rates', 'wpdev_edit_costs_deposit_payment','wpdev_edit_avalaibility')
                                    );
                            echo '</div>';
                            ?>

                            <div class="clear" style="height:1px;"></div>

                        </form>

            </div>

            <div style="clear:both;width:100%;height:1px;"></div> <?php

        }

        //MAXIME : FONCTION SHOW SETTINGS FOR ATOS CONFIGURATION
        function show_booking_atos_configuration_settings(){ global $wpdb;
            $alternative_color = '0';
            $link = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-option&tab=resources';

            if ((isset($_POST['submit_resources']))) {

                $bk_types = get_bk_types(true);

                // Edit ////////////////////////////////////////////////////////
                if ( ($_POST['bulk_resources_action'] == 'blank' ) || ($_POST['bulk_resources_action'] == 'edit' ) ) {

                    foreach ($bk_types as $bt) {
                          //$sql_res_cost = apply_bk_filter('get_sql_4_update_bk_resources_cost', ''  , $bt );
                          //$sql_res = apply_bk_filter('get_sql_4_update_bk_resources', ''  , $bt );

                        if(isset($_POST['ApproveReservation'.$bt->id]))
                            $approve = "true";
                        else
                            $approve = "false";

                          if ( false === $wpdb->query( $wpdb->prepare(
                            "UPDATE ".$wpdb->prefix ."bookingtypes SET atosValidation = '".$approve."' WHERE booking_type_id = ".$bt->id) )  )  bk_error('Error during updating to DB booking resources' ,__FILE__,__LINE__);    
                    }

                }

                // Delete //////////////////////////////////////////////////////
                if  ($_POST['bulk_resources_action'] == 'delete' ) {

                  $delete_bk_id = '';
                  foreach ($bk_types as $bt) { // Delete - Get all ID for deletion
                      if (isset($_POST['resources_items_'.$bt->id]))
                          $delete_bk_id .= $bt->id . ',';
                  }

                  if (! empty($delete_bk_id)) {
                      $delete_bk_id = substr($delete_bk_id,0,-1);                 // Remove last Comma
                      $delete_sql = "DELETE FROM ".$wpdb->prefix ."bookingtypes WHERE booking_type_id IN (".$delete_bk_id.")";

                      if ( false === $wpdb->query($wpdb->prepare($delete_sql)) )  bk_error('Error during deleting booking resources',__FILE__,__LINE__ );
                  }

                }

            }

             $bk_types = get_bk_types(true);

              $all_id = array(array('id'=>0,'title'=>' - '));
              foreach ($bk_types as $btt) {
                    if (isset($btt->parent)) if ($btt->parent==0)  $all_id[] = array('id'=>$btt->id, 'title'=> $btt->title);
              }

              make_bk_action('wpdev_bk_booking_resource_page_before');
            ?>
            <div style="clear:both;width:100%;height:1px;"></div>
                    <div style="  float: right; margin-top: -90px; margin-right: 10px;" class="wpdevbk">
                    <form  name="booking_filters_formID" action="" method="post" id="booking_filters_formID" class=" form-search">

                        <?php if (isset($_REQUEST['wh_resource_id']))  $wh_resource_id = $_REQUEST['wh_resource_id'];                  //  {'1', '2', .... }
                              else                                    $wh_resource_id      = '';                    ?>
                        <input class="input" type="text" placeholder="<?php _e('Resource ID or Title', 'wpdev-booking'); ?>" name="wh_resource_id" id="wh_resource_id" value="<?php echo $wh_resource_id; ?>" >
                        <input class="input" type="hidden"  name="page_num" id="page_num" value="1" >
                        <button class="btn small" type="submit"><?php _e('Go', 'wpdev-booking'); ?></button>
                    </form>
                    </div><?php

            $max_num = apply_bk_filter('get_max_res_num_for_user_in_multiuser', false );
            $is_show_add_resource = 0;
            if (isset($_GET['wpdev_edit_rates'])) $is_show_add_resource = $_GET['wpdev_edit_rates'];
            if (isset($_GET['wpdev_edit_costs_from_days'])) $is_show_add_resource = $_GET['wpdev_edit_costs_from_days'];
            if (isset($_GET['wpdev_edit_avalaibility'])) $is_show_add_resource = $_GET['wpdev_edit_avalaibility'];
            if (isset($_GET['wpdev_edit_costs_deposit_payment'])) $is_show_add_resource = $_GET['wpdev_edit_costs_deposit_payment'];



            ?>
            <div style="width:100%;">

                <form  name="post_option_resources" action="" method="post" id="post_option_resources" >
                    <table style="width:99%;" class="resource_table0 booking_table" cellpadding="0" cellspacing="0">
                            <?php // Headers  ?>
                        <tr>
                            <th style="width:15px;"><input type="checkbox" onclick="javascript:jQuery('.resources_items').attr('checked', this.checked);" class="resources_items" id="resources_items_all"  name="resources_items_all" /></th>
                            <th style="width:10px;height:35px;border-left: 1px solid #BBBBBB;"> <?php _e('ID', 'wpdev-booking'); ?> </th>
                            <th style="height:35px;width:220px;"> <?php _e('Resource name', 'wpdev-booking'); ?> </th>
                            <th style="height:35px;width:120px;"><?php  _e('Use Atos Validation', 'wpdev-booking'); ?></th>
                            <th style="text-align:center;"> <?php _e('Info', 'wpdev-booking'); ?> </th>

                        </tr>
                        <?php
                        if (! empty($bk_types))
                          foreach ($bk_types as $bt) {
                                  if ( $alternative_color == '')    $alternative_color = ' class="alternative_color" ';
                                  else                              $alternative_color = '';

                                  if ($is_show_add_resource == $bt->id ) $alternative_color = ' class="resource_line_selected" ';
                               ?>
                               <tr>
                                    <th <?php echo $alternative_color; ?> ><input type="checkbox" class="resources_items" id="resources_items_<?php echo $bt->id; ?>"  name="resources_items_<?php echo $bt->id; ?>" /></th>
                                    <td style="font-size:10px;font-weight: bold;border-right: 0px solid #ddd;border-left: 1px solid #aaa;text-align: center;" <?php echo $alternative_color; ?> ><?php echo $bt->id; ?></td>
                                    <td style="font-size:11px;<?php if (isset($bt->parent)) if ($bt->parent != 0 ) { echo 'padding-left:50px;'; } ?>" <?php echo $alternative_color; ?> >
                                        <span style="<?php if (isset($bt->parent)) if ($bt->parent == 0 ) { echo 'font-weight:bold;'; }?>"><?php echo $bt->title; ?></span>
                                        <!--input  maxlength="17" type="text"
                                            style="<?php  if (isset($bt->parent)) if ($bt->parent == 0 ) { echo 'width:210px;font-weight:bold;'; } else { echo 'width:170px;font-size:11px;'; } ?>"
                                            value="<?php echo $bt->title; ?>"
                                            name="type_title<?php echo $bt->id; ?>" id="type_title<?php echo $bt->id; ?>" /-->
                                        <?php if (isset($bt->parent)) if ($bt->parent == 0 ) { make_bk_action('resources_settings_after_title', $bt, $all_id, $alternative_color ); } ?>
                                    </td>
                                    
                                    <?php $isApprove = $this->checkIsApproveReservation($bt->id); ?>

                                    <td style="font-size:10px;font-weight: bold;border-right: 0px solid #ddd;border-left: 1px solid #aaa;text-align: center;" <?php echo $alternative_color; ?> >
                                        <input type="checkbox" name="ApproveReservation<?php echo $bt->id; ?>" <?php if($isApprove){?> checked <?php } ?> id="ApproveReservation<?php echo $bt->id; ?>" />
                                    </td>

                                    <td style="font-size:10px;font-weight: bold;border-right: 0px solid #ddd;border-left: 1px solid #aaa;text-align: center;" <?php echo $alternative_color; ?> >
                                        <?php make_bk_action('resources_settings_table_info_collumns', $bt, $all_id, $alternative_color ); ?>
                                    </td>
                               </tr>
                               <?php
                          }
                                ?>

                            </table>


                            <div class="clear" style="height:10px;"></div>
                            <select name="bulk_resources_action" id="bulk_resources_action" style="float:left;width:110px;margin-right:10px;margin-top: 3px;" >
                                <option value="blank"><?php _e('Bulk Actions','wpdev-booking') ?></option>
                                <option value="edit"><?php _e('Edit','wpdev-booking') ?></option>
                                <option value="delete"><?php _e('Delete','wpdev-booking') ?></option>
                            </select>
                            <input class="button-primary" style="float:left;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="submit_resources"/>

                            <?php if (isset($_REQUEST['page_num'])) { ?>
                                <input class="input" type="hidden"  name="page_num" id="page_num" value="<?php echo $_REQUEST['page_num']; ?>" >
                            <?php } if (isset($_REQUEST['wh_resource_id'])) { ?>
                                <input class="input" type="hidden"  name="wh_resource_id" id="wh_resource_id" value="<?php echo $_REQUEST['wh_resource_id']; ?>" >
                            <?php } ?>

                            <div class="clear" style="height:5px;"></div>

                            <?php // Show Pagination
                            echo '<div class="wpdevbk">';
                            $active_page_num = (isset($_REQUEST['page_num']))?$_REQUEST['page_num']:1;
                            $items_count_in_page=get_bk_option( 'booking_resourses_num_per_page');
                            wpdevbk_show_pagination(get_booking_resources_count(), $active_page_num, $items_count_in_page,
                                    array('page','tab', 'wh_resource_id')//,'wpdev_edit_costs_from_days','wpdev_edit_rates', 'wpdev_edit_costs_deposit_payment','wpdev_edit_avalaibility')
                                    );
                            echo '</div>';
                            ?>

                            <div class="clear" style="height:1px;"></div>

                        </form>

            </div>

            <div style="clear:both;width:100%;height:1px;"></div> <?php


        }

        function checkIsApproveReservation($bk_type){
            global $wpdb;

            $requete = "SELECT atosValidation FROM ". $wpdb->prefix ."bookingtypes WHERE booking_type_id = '" .$bk_type. "'";
            $result = $wpdb->get_results($wpdb->prepare($requete));

            if($result[0]->atosValidation == "true")
                return true;
            else
                return false;
        }

    // Resources

        // Show top TAB selection menu for the Resources page:
        function wpdev_booking_resources_top_menu(){

            $is_only_icons = ! true;
            if ($is_only_icons) echo '<style type="text/css"> #menu-wpdevplugin .nav-tab { padding:4px 2px 6px 32px !important; } </style>';

            if  (! isset($_GET['tab'])) $_GET['tab'] = 'resource';
            $selected_title = $_GET['tab'];

            $selected_icon = 'Resources-64x64.png';
            ?>
             <div style="height:1px;clear:both;margin-top:20px;"></div>
             <div id="menu-wpdevplugin">
                <div class="nav-tabs-wrapper">
                    <div class="nav-tabs">

                        <?php /*$is_can = apply_bk_filter('multiuser_is_user_can_be_here', true, 'only_super_admin');
                        if ($is_can) { ?>

                            <?php $title = __('General', 'wpdev-booking');
                            $my_icon = 'General-setting-64x64.png'; $my_tab = 'main';  ?>
                            <?php if ( ($_GET['tab'] == 'main') ||($_GET['tab'] == '') || (! isset($_GET['tab'])) ) {  $slct_a = 'selected'; } else {  $slct_a = ''; } ?>
                            <?php if ($slct_a == 'selected') {  $selected_title = $title; $selected_icon = $my_icon;  ?><span class="nav-tab nav-tab-active"><?php } else { ?><a rel="tooltip" class="nav-tab tooltip_bottom" title="<?php echo __('Customization of','wpdev-booking') .' '.strtolower($title). ' '.__('settings','wpdev-booking'); ?>" href="admin.php?page=<?php echo WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME ; ?>wpdev-booking-option&tab=<?php echo $my_tab; ?>"><?php } ?><img class="menuicons" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?><?php if ($slct_a == 'selected') { ?></span><?php } else { ?></a><?php } ?>

                        <?php } else {
                                 if ( (! isset($_GET['tab'])) || ($_GET['tab']=='') )
                                     $_GET['tab'] = 'form'; // For multiuser - common user set firt selected tab -> Form
                        } /**/ ?>


                        <?php $title = __('Resources', 'wpdev-booking');
                        $my_icon = 'Resources-64x64.png'; $my_tab = 'resource';  ?>
                        <?php if ($_GET['tab'] == 'resource') {  $slct_a = 'selected'; } else {  $slct_a = ''; } ?>
                        <?php if ($slct_a == 'selected') {  $selected_title = $title; $selected_icon = $my_icon;  ?><span class="nav-tab nav-tab-active"><?php } else { ?><a rel="tooltip" class="nav-tab tooltip_bottom" title="<?php echo $title . ' '.__('managment','wpdev-booking'); ?>" href="admin.php?page=<?php echo WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME ; ?>wpdev-booking-resources&tab=<?php echo $my_tab; ?>"><?php } ?><img class="menuicons" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?><?php if ($slct_a == 'selected') { ?></span><?php } else { ?></a><?php } ?>

                        <?php $title = __('Costs and Rates', 'wpdev-booking');
                        $my_icon = 'Booking-costs-64x64.png'; $my_tab = 'cost';  ?>
                        <?php if ($_GET['tab'] == 'cost') {  $slct_a = 'selected'; } else {  $slct_a = ''; } ?>
                        <?php if ($slct_a == 'selected') {  $selected_title = $title; $selected_icon = $my_icon;  ?><span class="nav-tab nav-tab-active"><?php } else { ?><a rel="tooltip" class="nav-tab tooltip_bottom" title="<?php echo __('Customization of','wpdev-booking') .' ' .__('rates, valuation days cost and deposit amount ','wpdev-booking'); ?>" href="admin.php?page=<?php echo WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME ; ?>wpdev-booking-resources&tab=<?php echo $my_tab; ?>"><?php } ?><img class="menuicons" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?><?php if ($slct_a == 'selected') { ?></span><?php } else { ?></a><?php } ?>

                        <?php $title = __('Advanced Cost', 'wpdev-booking');
                        $my_icon = 'advanced_cost.png'; $my_tab = 'cost_advanced';  ?>
                        <?php if ($_GET['tab'] == 'cost_advanced') {  $slct_a = 'selected'; } else {  $slct_a = ''; } ?>
                        <?php if ($slct_a == 'selected') {  $selected_title = $title; $selected_icon = $my_icon;  ?><span class="nav-tab nav-tab-active"><?php } else { ?><a rel="tooltip" class="nav-tab tooltip_bottom" title="<?php echo __('Customization of','wpdev-booking') . ' '.__('additional cost, which depend from form fields','wpdev-booking'); ?>" href="admin.php?page=<?php echo WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME ; ?>wpdev-booking-resources&tab=<?php echo $my_tab; ?>"><?php } ?><img class="menuicons" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?><?php if ($slct_a == 'selected') { ?></span><?php } else { ?></a><?php } ?>

                        <?php if (class_exists('wpdev_bk_biz_l'))   { ?>
                            <?php $title = __('Coupons', 'wpdev-booking');
                            $my_icon = 'coupon.png'; $my_tab = 'coupons';  ?>
                            <?php if ($_GET['tab'] == 'coupons') {  $slct_a = 'selected'; } else {  $slct_a = ''; } ?>
                            <?php if ($slct_a == 'selected') {  $selected_title = $title; $selected_icon = $my_icon;  ?><span class="nav-tab nav-tab-active"><?php } else { ?><a rel="tooltip" class="nav-tab tooltip_bottom" title="<?php echo __('Setting','wpdev-booking') .' '.strtolower($title). ' '.__('for discount','wpdev-booking'); ?>" href="admin.php?page=<?php echo WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME ; ?>wpdev-booking-resources&tab=<?php echo $my_tab; ?>"><?php } ?><img class="menuicons" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?><?php if ($slct_a == 'selected') { ?></span><?php } else { ?></a><?php } ?>
                        <?php } ?>

                        <?php $title = __('Availability', 'wpdev-booking');
                        $my_icon = 'availability.png'; $my_tab = 'availability';  ?>
                        <?php if ($_GET['tab'] == 'availability') {  $slct_a = 'selected'; } else {  $slct_a = ''; } ?>
                        <?php if ($slct_a == 'selected') {  $selected_title = $title; $selected_icon = $my_icon;  ?><span class="nav-tab nav-tab-active"><?php } else { ?><a rel="tooltip" class="nav-tab tooltip_bottom" title="<?php echo __('Customization of','wpdev-booking') .' '.strtolower($title). ' '.__('settings','wpdev-booking'); ?>" href="admin.php?page=<?php echo WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME ; ?>wpdev-booking-resources&tab=<?php echo $my_tab; ?>"><?php } ?><img class="menuicons" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?><?php if ($slct_a == 'selected') { ?></span><?php } else { ?></a><?php } ?>

                        <?php $title = __('Season Filters', 'wpdev-booking');
                        $my_icon = 'Season-64x64.png'; $my_tab = 'filter';  ?>
                        <?php if ($_GET['tab'] == 'filter') {  $slct_a = 'selected'; } else {  $slct_a = ''; } ?>
                        <?php if ($slct_a == 'selected') {  $selected_title = $title; $selected_icon = $my_icon;  ?><span class="nav-tab nav-tab-active"><?php } else { ?><a rel="tooltip" class="nav-tab tooltip_bottom" title="<?php echo __('Setting','wpdev-booking') .' '.strtolower($title); ?>" href="admin.php?page=<?php echo WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME ; ?>wpdev-booking-resources&tab=<?php echo $my_tab; ?>"><?php } ?><img class="menuicons" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?><?php if ($slct_a == 'selected') { ?></span><?php } else { ?></a><?php } ?>

                        <?php //MAXIME RAJOUT ONGLET ATOS ?>
                        <?php
                            $title = __('Atos Configuration', 'wpdev_booking');
                            $my_icon = 'atosConfig.png' ; $my_tab = 'atosConfig';
                            if($_GET['tab'] == 'atosConfig'){ $slct_a = 'selected'; } else { $slct_a = ''; }?>
                            <?php if ($slct_a == 'selected') {  $selected_title = $title; $selected_icon = $my_icon;  ?><span class="nav-tab nav-tab-active"><?php } else { ?><a rel="tooltip" class="nav-tab tooltip_bottom" title="<?php echo __('Setting','wpdev-booking') .' '.strtolower($title); ?>" href="admin.php?page=<?php echo WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME ; ?>wpdev-booking-resources&tab=<?php echo $my_tab; ?>"><?php } ?><img class="menuicons" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?><?php if ($slct_a == 'selected') { ?></span><?php } else { ?></a><?php } ?>
                    </div>
                </div>
             </div>
             <script type="text/javascript">
                    var val1 = '<img src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $selected_icon; ?>"><br />';
                    jQuery('div.wrap div.icon32').html(val1);
                    jQuery('div.bookingpage h2').html( '<?php echo $selected_title . ' '. __('settings') ?>');
              </script>
            <div style="height:1px;clear:both;border-top:1px solid #bbc;"></div>  <?php
        }

        // Show content of specific resource tab selection
        function wpdev_booking_resources_show_content(){ global $wpdb;
          
            if  ( $_GET['tab'] == 'filter')   {
                $this->show_booking_date_filter();
            } elseif  ( $_GET['tab'] == 'availability')   {
                $this->show_booking_availability_rates_settings_page();
            } elseif  ( $_GET['tab'] == 'cost_advanced')   {
                $this->advanced_cost_management_settings();
            } elseif  ( $_GET['tab'] == 'cost')   {
                $this->show_booking_availability_rates_settings_page();
            } elseif  ( $_GET['tab'] == 'coupons')   {
              make_bk_action('wpdev_booking_settings_show_coupons');
            } elseif ($_GET['tab'] == 'atosConfig') {
                $this->show_booking_atos_configuration_settings();
            }
        }


 //   A C T I V A T I O N   A N D   D E A C T I V A T I O N    O F   T H I S   P L U G I N  ///////////////////////////////////////////////////

        // Activate
        function pro_activate() {

                

                add_bk_option( 'booking_forms_extended', serialize(array()) );

                add_bk_option( 'booking_is_resource_deposit_payment_active' , 'Off');

                add_bk_option( 'booking_advanced_costs_calc_fixed_cost_with_procents', 'Off');

                add_bk_option( 'booking_is_show_cost_in_tooltips',  'Off');
                add_bk_option( 'booking_highlight_cost_word',  __('Cost: ','wpdev-booking')  );
                add_bk_option( 'booking_visitor_number_rate', '0');
                add_bk_option( 'booking_visitor_number_rate_type', '%');
                if ( wpdev_bk_is_this_demo() )
                    update_bk_option( 'booking_form', str_replace('\\n\\','', $this->reset_to_default_form('payment') ) );
                
                global $wpdb;
                $charset_collate = '';
                //if ( $wpdb->has_cap( 'collation' ) ) {
                            if ( ! empty($wpdb->charset) ) $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
                            if ( ! empty($wpdb->collate) ) $charset_collate .= " COLLATE $wpdb->collate";
                //}
                

                if  ($this->is_field_in_table_exists('bookingtypes','default_form') == 0){
                    $simple_sql = "ALTER TABLE ".$wpdb->prefix ."bookingtypes ADD default_form varchar(249) NOT NULL default 'standard'";
                    $wpdb->query($wpdb->prepare($simple_sql));
               }
 
                // Season filter table
                if ( ( ! $this->is_table_exists('booking_seasons')  )) { // Cehck if tables not exist yet

                        $wp_queries=array();
                        $wp_queries[] = "CREATE TABLE ".$wpdb->prefix ."booking_seasons (
                             booking_filter_id bigint(20) unsigned NOT NULL auto_increment,
                             title varchar(200) NOT NULL default '',
                             filter text ,
                             PRIMARY KEY  (booking_filter_id)
                            ) $charset_collate;";

                        $wp_queries[] = 'INSERT INTO '.$wpdb->prefix .'booking_seasons ( title, filter ) VALUES ( "'. __('Weekend', 'wpdev-booking') .'", \'a:6:{s:8:"weekdays";a:7:{i:0;s:2:"On";i:1;s:3:"Off";i:2;s:3:"Off";i:3;s:3:"Off";i:4;s:3:"Off";i:5;s:3:"Off";i:6;s:2:"On";}s:4:"days";a:31:{i:1;s:2:"On";i:2;s:2:"On";i:3;s:2:"On";i:4;s:2:"On";i:5;s:2:"On";i:6;s:2:"On";i:7;s:2:"On";i:8;s:2:"On";i:9;s:2:"On";i:10;s:2:"On";i:11;s:2:"On";i:12;s:2:"On";i:13;s:2:"On";i:14;s:2:"On";i:15;s:2:"On";i:16;s:2:"On";i:17;s:2:"On";i:18;s:2:"On";i:19;s:2:"On";i:20;s:2:"On";i:21;s:2:"On";i:22;s:2:"On";i:23;s:2:"On";i:24;s:2:"On";i:25;s:2:"On";i:26;s:2:"On";i:27;s:2:"On";i:28;s:2:"On";i:29;s:2:"On";i:30;s:2:"On";i:31;s:2:"On";}s:7:"monthes";a:12:{i:1;s:2:"On";i:2;s:2:"On";i:3;s:2:"On";i:4;s:2:"On";i:5;s:2:"On";i:6;s:2:"On";i:7;s:2:"On";i:8;s:2:"On";i:9;s:2:"On";i:10;s:2:"On";i:11;s:2:"On";i:12;s:2:"On";}s:4:"year";a:14:{i:2009;s:3:"Off";i:2010;s:3:"Off";i:2011;s:2:"On";i:2012;s:2:"On";i:2013;s:3:"Off";i:2014;s:3:"Off";i:2015;s:3:"Off";i:2016;s:3:"Off";i:2017;s:3:"Off";i:2018;s:3:"Off";i:2019;s:3:"Off";i:2020;s:3:"Off";i:2021;s:3:"Off";i:2022;s:3:"Off";}s:10:"start_time";s:0:"";s:8:"end_time";s:0:"";}\' );';
                        $wp_queries[] = 'INSERT INTO '.$wpdb->prefix .'booking_seasons ( title, filter ) VALUES ( "'. __('Work days', 'wpdev-booking') .'", \'a:6:{s:8:"weekdays";a:7:{i:0;s:3:"Off";i:1;s:2:"On";i:2;s:2:"On";i:3;s:2:"On";i:4;s:2:"On";i:5;s:2:"On";i:6;s:3:"Off";}s:4:"days";a:31:{i:1;s:2:"On";i:2;s:2:"On";i:3;s:2:"On";i:4;s:2:"On";i:5;s:2:"On";i:6;s:2:"On";i:7;s:2:"On";i:8;s:2:"On";i:9;s:2:"On";i:10;s:2:"On";i:11;s:2:"On";i:12;s:2:"On";i:13;s:2:"On";i:14;s:2:"On";i:15;s:2:"On";i:16;s:2:"On";i:17;s:2:"On";i:18;s:2:"On";i:19;s:2:"On";i:20;s:2:"On";i:21;s:2:"On";i:22;s:2:"On";i:23;s:2:"On";i:24;s:2:"On";i:25;s:2:"On";i:26;s:2:"On";i:27;s:2:"On";i:28;s:2:"On";i:29;s:2:"On";i:30;s:2:"On";i:31;s:2:"On";}s:7:"monthes";a:12:{i:1;s:2:"On";i:2;s:2:"On";i:3;s:2:"On";i:4;s:2:"On";i:5;s:2:"On";i:6;s:2:"On";i:7;s:2:"On";i:8;s:2:"On";i:9;s:2:"On";i:10;s:2:"On";i:11;s:2:"On";i:12;s:2:"On";}s:4:"year";a:14:{i:2009;s:3:"Off";i:2010;s:3:"Off";i:2011;s:2:"On";i:2012;s:2:"On";i:2013;s:3:"Off";i:2014;s:3:"Off";i:2015;s:3:"Off";i:2016;s:3:"Off";i:2017;s:3:"Off";i:2018;s:3:"Off";i:2019;s:3:"Off";i:2020;s:3:"Off";i:2021;s:3:"Off";i:2022;s:3:"Off";}s:10:"start_time";s:0:"";s:8:"end_time";s:0:"";}\' );';
                        $wp_queries[] = 'INSERT INTO '.$wpdb->prefix .'booking_seasons ( title, filter ) VALUES ( "'. __('High season', 'wpdev-booking') .'", \'a:6:{s:8:"weekdays";a:7:{i:0;s:2:"On";i:1;s:2:"On";i:2;s:2:"On";i:3;s:2:"On";i:4;s:2:"On";i:5;s:2:"On";i:6;s:2:"On";}s:4:"days";a:31:{i:1;s:2:"On";i:2;s:2:"On";i:3;s:2:"On";i:4;s:2:"On";i:5;s:2:"On";i:6;s:2:"On";i:7;s:2:"On";i:8;s:2:"On";i:9;s:2:"On";i:10;s:2:"On";i:11;s:2:"On";i:12;s:2:"On";i:13;s:2:"On";i:14;s:2:"On";i:15;s:2:"On";i:16;s:2:"On";i:17;s:2:"On";i:18;s:2:"On";i:19;s:2:"On";i:20;s:2:"On";i:21;s:2:"On";i:22;s:2:"On";i:23;s:2:"On";i:24;s:2:"On";i:25;s:2:"On";i:26;s:2:"On";i:27;s:2:"On";i:28;s:2:"On";i:29;s:2:"On";i:30;s:2:"On";i:31;s:2:"On";}s:7:"monthes";a:12:{i:1;s:3:"Off";i:2;s:3:"Off";i:3;s:3:"Off";i:4;s:3:"Off";i:5;s:2:"On";i:6;s:2:"On";i:7;s:2:"On";i:8;s:2:"On";i:9;s:2:"On";i:10;s:3:"Off";i:11;s:3:"Off";i:12;s:3:"Off";}s:4:"year";a:14:{i:2009;s:3:"Off";i:2010;s:3:"Off";i:2011;s:2:"On";i:2012;s:2:"On";i:2013;s:3:"Off";i:2014;s:3:"Off";i:2015;s:3:"Off";i:2016;s:3:"Off";i:2017;s:3:"Off";i:2018;s:3:"Off";i:2019;s:3:"Off";i:2020;s:3:"Off";i:2021;s:3:"Off";i:2022;s:3:"Off";}s:10:"start_time";s:0:"";s:8:"end_time";s:0:"";}\' );';

                        foreach ($wp_queries as $wp_q) $wpdb->query($wpdb->prepare($wp_q));
                }

                // Booking Types   M E T A  table
                if ( ( ! $this->is_table_exists('booking_types_meta')  )) { // Cehck if tables not exist yet

                        $wp_queries=array();
                        $wp_queries[] = "CREATE TABLE ".$wpdb->prefix ."booking_types_meta (
                             meta_id bigint(20) unsigned NOT NULL auto_increment,
                             type_id bigint(20) NOT NULL default 0,
                             meta_key varchar(200) NOT NULL default '',
                             meta_value text ,
                             PRIMARY KEY  (meta_id)
                            ) $charset_collate;";

                        foreach ($wp_queries as $wp_q) $wpdb->query($wpdb->prepare($wp_q));
                }


                if ( wpdev_bk_is_this_demo() )          {
                    update_bk_option( 'booking_multiple_day_selections' , 'On' );
                    update_bk_option( 'booking_is_show_cost_in_tooltips',  'On');
                    update_bk_option( 'booking_skin', WPDEV_BK_PLUGIN_URL . '/inc/skins/premium-black.css');

                                    //form fields setting
                                     update_bk_option( 'booking_form',  '        [calendar]
                        <div style="text-align:left;line-height:28px;"><p>'. __('The cost for payment', 'wpdev-booking').': [cost_hint]</p></div>
                        <div style="text-align:left">
                        [cost_corrections]
                        <p>First Name (required):<br />  [text* name] </p>
                        <p>Last Name (required):<br />  [text* secondname] </p>
                        <p>Email (required):<br />  [email* email] </p>
                        <p>Address (required):<br />  [text* address] </p>
                        <p>City(required):<br />  [text* city] </p>
                        <p>Post code(required):<br />  [text* postcode] </p>
                        <p>Country(required):<br />  [country] </p>
                        <p>Phone:<br />  [text phone] </p>
                        <p>Visitors:<br />  [select visitors "1" "2" "3" "4"] Children: [checkbox children ""]</p>
                        <p>Details:<br /> [textarea details] </p>
                        <p>[captcha]</p>
                        <p>[submit "Send"]</p>
                        </div>' );

                                     update_bk_option( 'booking_form_show',  ' <div style="text-align:left">
                        <strong>First Name</strong>:<span class="fieldvalue">[name]</span><br/>
                        <strong>Last Name</strong>:<span class="fieldvalue">[secondname]</span><br/>
                        <strong>Email</strong>:<span class="fieldvalue">[email]</span><br/>
                        <strong>Address</strong>:<span class="fieldvalue">[address]</span><br/>
                        <strong>City</strong>:<span class="fieldvalue">[city]</span><br/>
                        <strong>Post code</strong>:<span class="fieldvalue">[postcode]</span><br/>
                        <strong>Country</strong>:<span class="fieldvalue">[country]</span><br/>
                        <strong>Phone</strong>:<span class="fieldvalue">[phone]</span><br/>
                        <strong>Number of visitors</strong>:<span class="fieldvalue"> [visitors]</span><br/>
                        <strong>Children</strong>:<span class="fieldvalue"> [children]</span><br/>
                        <strong>Details</strong>:<br /><span class="fieldvalue"> [details]</span>
                        </div>' );

                        $wp_queries=array();
                        $wp_queries[] = 'INSERT INTO '.$wpdb->prefix .'booking_types_meta (  type_id, meta_key, meta_value ) VALUES ( 4, "rates", "a:3:{s:6:\"filter\";a:3:{i:3;s:3:\"Off\";i:2;s:3:\"Off\";i:1;s:2:\"On\";}s:4:\"rate\";a:3:{i:3;s:1:\"0\";i:2;s:1:\"0\";i:1;s:3:\"200\";}s:9:\"rate_type\";a:3:{i:3;s:1:\"%\";i:2;s:1:\"%\";i:1;s:1:\"%\";}}" );';
                        $wp_queries[] = 'INSERT INTO '.$wpdb->prefix .'booking_types_meta (  type_id, meta_key, meta_value ) VALUES ( 3, "costs_depends", "a:3:{i:0;a:6:{s:6:\"active\";s:2:\"On\";s:4:\"type\";s:1:\">\";s:4:\"from\";s:1:\"1\";s:2:\"to\";s:1:\"2\";s:4:\"cost\";s:2:\"50\";s:13:\"cost_apply_to\";s:5:\"fixed\";}i:1;a:6:{s:6:\"active\";s:2:\"On\";s:4:\"type\";s:1:\"=\";s:4:\"from\";s:1:\"3\";s:2:\"to\";s:1:\"4\";s:4:\"cost\";s:2:\"45\";s:13:\"cost_apply_to\";s:5:\"fixed\";}i:2;a:6:{s:6:\"active\";s:2:\"On\";s:4:\"type\";s:4:\"summ\";s:4:\"from\";s:1:\"4\";s:2:\"to\";s:1:\"2\";s:4:\"cost\";s:3:\"175\";s:13:\"cost_apply_to\";s:5:\"fixed\";}}" );';
                        $wp_queries[] = 'INSERT INTO '.$wpdb->prefix .'booking_types_meta (  type_id, meta_key, meta_value ) VALUES ( 2, "availability", "a:2:{s:7:\"general\";s:2:\"On\";s:6:\"filter\";a:3:{i:3;s:3:\"Off\";i:2;s:3:\"Off\";i:1;s:2:\"On\";}}" );';

                        foreach ($wp_queries as $wp_q) $wpdb->query($wp_q);

 
                }

        }

        //Decativate
        function pro_deactivate(){

            delete_bk_option( 'booking_forms_extended');

            delete_bk_option( 'booking_is_resource_deposit_payment_active' );

            delete_bk_option( 'booking_is_show_cost_in_tooltips');
            delete_bk_option( 'booking_highlight_cost_word');

            delete_bk_option( 'booking_visitor_number_rate');
            delete_bk_option( 'booking_visitor_number_rate_type');

            delete_bk_option( 'booking_advanced_costs_values');
            delete_bk_option( 'booking_advanced_costs_calc_fixed_cost_with_procents');

            global $wpdb;
            // delete_bk_option( 'booking_form_show');

            $wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'booking_seasons'));
            $wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'booking_types_meta'));
         }


    }
}
?>