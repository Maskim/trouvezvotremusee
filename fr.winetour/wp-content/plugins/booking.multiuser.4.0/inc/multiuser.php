<?php
/*
This is COMMERCIAL SCRIPT
We are do not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

if (  (! isset( $_GET['merchant_return_link'] ) ) && (! isset( $_GET['payed_booking'] ) ) && (!function_exists ('get_option')  )  ) { die('You do not have permission to direct access to this file !!!'); }
//require_once(WPDEV_BK_PLUGIN_DIR. '/inc/lib_mu.php' );

if (!class_exists('wpdev_bk_multiuser')) {
    class wpdev_bk_multiuser {

                var $current_user;    // active user
                var $activated_user;  // active user

                var $client_side_active_params_of_user;  // active user

                var $super_admin_id;

                function wpdev_bk_multiuser(){

                        // Get first administrator user ID and set it as superadmin
                        $admin_id = 1;
                        global $wpdb;
                        $sql_check_table =    "SELECT ID FROM " . $wpdb->prefix . "users as u LEFT JOIN " . $wpdb->prefix . "usermeta as m ON u.ID = m.user_id  WHERE m.meta_key='" . $wpdb->prefix . "capabilities'" ;
                        $res = $wpdb->get_results($wpdb->prepare($sql_check_table) .  "AND m.meta_value LIKE '%administrator%' ORDER BY ID ASC LIMIT 0,1" );
                        if (count($res)>0) {
                            $admin_id = $res[0]->ID;
                        } /////////////////////////////////////////////////////////


                        $this->super_admin_id = array($admin_id);                       // ID of SUPER Admins
                        $this->activated_user = false;
                        $this->client_side_active_params_of_user = false;

                        add_bk_action('wpdev_booking_activation', array(&$this, 'pro_activate'));
                        add_bk_action('wpdev_booking_deactivation', array(&$this, 'pro_deactivate'));

                        add_bk_filter('wpdev_show_top_menu_line', array(&$this, 'wpdev_show_top_menu_line'));
                        add_bk_action('wpdev_booking_settings_show_content', array(&$this, 'settings_menu_content'));


                        add_bk_action('check_for_resources_of_notsuperadmin_in_booking_listing', array(&$this, 'check_for_resources_of_notsuperadmin_in_booking_listing') );

                        // Show childs count at top line of selection booking resources
                        add_bk_filter('showing_user_name_in_top_line', array(&$this, 'showing_user_name_in_top_line'));
                        add_bk_action('wpdev_show_users_selection_for_bk_resources', array(&$this, 'wpdev_show_users_selection_for_bk_resources'));
                        add_bk_action('show_users_header_at_settings', array(&$this, 'show_users_header_at_settings'));
                        add_bk_action('show_users_footer_at_settings', array(&$this, 'show_users_footer_at_settings'));
                        add_bk_action('show_users_collumn_at_settings', array(&$this, 'show_users_collumn_at_settings'));


                        add_action('wpdev_bk_js_define_variables', array(&$this, 'js_define_variables') );           // Write JS variables
                        add_action('wpdev_bk_js_write_files',      array(&$this, 'js_write_files') );                // Write JS files

                        add_bk_filter('multiuser_resource_list', array($this, 'multiuser_resource_list'));           // Reduce type lists to only of current user
                        add_bk_filter('multiuser_modify_SQL_for_current_user', array($this, 'multiuser_modify_SQL_for_current_user'));           // Reduce type lists to only of current user
                        add_bk_action('added_new_booking_resource', array(&$this, 'added_new_booking_resource') );      // ADD - New booking resource
                        add_bk_action('added_new_season_filter', array(&$this, 'added_new_season_filter') );      // ADD - New booking resource
                        add_bk_action('added_new_coupon', array(&$this, 'added_new_coupon') );      // ADD - New booking resource


                        add_bk_filter('multiuser_is_user_can_be_here', array($this, 'multiuser_is_user_can_be_here'));           // check Admin pages, if some user can be there.
                        add_bk_filter('is_user_super_admin', array($this, 'is_user_super_admin'));                               // check if user is Super admin or no
                        add_bk_filter('get_default_bk_resource_for_user', array($this, 'get_default_bk_resource_for_user'));                               // check if user is Super admin or no
                        add_bk_filter('get_bk_resources_of_user', array($this, 'get_bk_resources_of_user'));                               // check if user is Super admin or no

                        add_bk_filter('get_user_of_this_bk_resource', array($this, 'get_user_of_this_bk_resource'));                       // Get USER ID of specific booking resource


                        add_bk_filter('get_max_res_num_for_user_in_multiuser', array($this, 'get_max_res_num_for_user'));    // Get MAX Nem of Resource for this user

                        // Set Temporary USER ID for some client actions
                        add_bk_action('check_multiuser_params_for_client_side_by_user_id', array(&$this, 'check_multiuser_params_for_client_side_by_user_id') );
                        add_bk_action('check_multiuser_params_for_client_side', array(&$this, 'check_multiuser_params_for_client_side') );
                        add_bk_action('finish_check_multiuser_params_for_client_side', array(&$this, 'finish_check_multiuser_params_for_client_side') );


                        add_bk_filter('get_sql_4_insert_bk_resources_fields_m', array(&$this, 'get_sql_4_insert_bk_resources_fields'));
                        add_bk_filter('get_sql_4_insert_bk_resources_values_m', array(&$this, 'get_sql_4_insert_bk_resources_values'));

                        add_bk_filter('get_sql_for_checking_new_bookings_multiuser', array(&$this, 'get_sql_for_checking_new_bookings_multiuser'));
                        add_bk_filter('update_sql_for_checking_new_bookings', array(&$this, 'update_sql_for_checking_new_bookings'));

                        /**/
                        add_bk_filter('wpdev_bk_get_option', array(&$this, 'wpdev_bk_get_option'));
                        add_bk_filter('wpdev_bk_update_option', array(&$this, 'wpdev_bk_update_option'));
                        add_bk_filter('wpdev_bk_delete_option', array(&$this, 'wpdev_bk_delete_option'));
                        add_bk_filter('wpdev_bk_add_option', array(&$this, 'wpdev_bk_add_option'));
                        /**/
                }



// S U P P O R T       F u n c t i o n s    //////////////////////////////////////////////////////////////////////////////////////////////////

        // If the booking resources is not set, and current user  is not superadmin, so then get only the booking resources of the current user
        function check_for_resources_of_notsuperadmin_in_booking_listing(){

            $my_resources = '';
            $is_superadmin = $this->multiuser_is_user_can_be_here(true, 'only_super_admin');
            $user = wp_get_current_user();
            $user_bk_id = $user->ID;
            if (! $is_superadmin) { // User not superadmin
                $bk_ids = $this->get_bk_resources_of_user(false);
                if ($bk_ids !== false) {
                  $my_res_id = array(); foreach ($bk_ids as $bk_id) { $my_res_id[]= $bk_id->ID ; }

                  $my_resources = implode(',', $my_res_id );
                  if (! isset($_REQUEST['wh_booking_type'])) {
                        $_REQUEST['wh_booking_type'] = $my_resources;
                  } else {
                        if (in_array($_REQUEST['wh_booking_type'], $my_res_id)) {
                            //todo: Check that passed request  of booking type from the resource(s), which  are belong to this user
                        }
                  }

                }
            }

        }


        // write JS variables
        function js_define_variables(){
              $this->current_user = wp_get_current_user();  // Its executed when wp_head hook is executed
              ?>
                    <script  type="text/javascript">
                       // var is_use_visitors_number_for_availability =  false;
                    </script>
            <?php
        }

        // write JS Scripts
        function js_write_files(){
               /* ?> <script type="text/javascript" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/inc/js/multiuser.js"></script>  <?php /**/
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


        // Check if this USER  is Super (booking) Admin or not
        function is_user_super_admin($user_bk_id = 0) {
            if ($user_bk_id === 0) {
                $user = wp_get_current_user();
                $user_bk_id = $user->ID;
            }

            $u_value = get_user_option( 'booking_user_role', $user_bk_id );
            if ($u_value == 'super_admin') return true;

            if ( in_array($user_bk_id, $this->super_admin_id) ) return true;       // User ID inside of SUper Admin ID
            else                                             return false;
        }


        // Check if user LOW LEVEL
        function is_user_low_level($user_bk_id = 0) {
            if ($user_bk_id === 0) {
                $user = wp_get_current_user();
                $user_bk_id = $user->ID;
            }

            $u_value = get_user_option( 'booking_user_role', $user_bk_id );
            if ($u_value == 'low_level_user') return true;
            // if ( in_array($user_bk_id, $this->super_admin_id) ) return true;       // User ID inside of SUper Admin ID
            else                                                return false;
        }


        // Get default booking resource for this active user
        function get_default_bk_resource_for_user( $blank= false,  $user_bk_id = false ){

                if ($user_bk_id === false) {
                    if ($this->is_user_super_admin()) return false;
                    global $wpdb;
                    $user = wp_get_current_user();
                    $user_bk_id = $user->ID;
                }

                global $wpdb;

                $wp_q = "SELECT booking_type_id as ID FROM ".$wpdb->prefix ."bookingtypes WHERE users = ". $user_bk_id ." ORDER BY parent, prioritet LIMIT 0, 1;" ;
                $res = $wpdb->get_results($wpdb->prepare($wp_q));
                if ( count($res) > 0 ) {
                    return $res[0]->ID;
                }  else return false;

        }


        // Get default booking resource for this active user
        function get_bk_resources_of_user( $blank= false,  $user_bk_id = false ){

                if ($user_bk_id === false) {
                    if ($this->is_user_super_admin()) return false;
                    global $wpdb;
                    $user = wp_get_current_user();
                    $user_bk_id = $user->ID;
                }

                global $wpdb;

                $wp_q = "SELECT booking_type_id as ID FROM ".$wpdb->prefix ."bookingtypes WHERE users = ". $user_bk_id ." ORDER BY parent, prioritet;" ;
                $res = $wpdb->get_results($wpdb->prepare($wp_q));
                if ( count($res) > 0 ) {
                    return $res;
                }  else return false;

        }


        function get_user_of_this_bk_resource($blank= false, $bk_res_id){
                global $wpdb;

                $wp_q = "SELECT users  FROM ".$wpdb->prefix ."bookingtypes WHERE booking_type_id = ". $bk_res_id ." ;" ;
                $res = $wpdb->get_results($wpdb->prepare($wp_q));
                if ( count($res) > 0 ) {
                    return $res[0]->users;
                }  else return false;
        }



        // Get Fields and Values for Insert new resource
        function get_sql_4_insert_bk_resources_fields( $blank ){
          return ', users ';
        }
        function get_sql_4_insert_bk_resources_values( $blank  , $sufix){

              $user = wp_get_current_user();
              $u_id = $user->ID;
          return ' , '. $u_id . ' ';

        }



        function get_sql_for_checking_new_bookings_multiuser($sql_req){
            $user = wp_get_current_user();
            $user_bk_id = $user->ID;
            global $wpdb;
            $my_resources = '';
            $bk_ids = $this->get_bk_resources_of_user();
            if ($bk_ids !== false) {
              foreach ($bk_ids as $bk_id) { $my_resources .= $bk_id->ID . ','; }
              $my_resources = substr($my_resources,0,-1);
            }
            if ($my_resources!='')
                $sql_req = "SELECT bk.booking_id FROM ".$wpdb->prefix ."booking as bk
                        WHERE  bk.is_new = 1 AND bk.booking_type IN ($my_resources)";
            else
                $sql_req = "SELECT bk.booking_id FROM ".$wpdb->prefix ."booking as bk
                        WHERE  bk.is_new = 1";

            return $sql_req;
        }

        function update_sql_for_checking_new_bookings($update_sql, $tid){
            $user = wp_get_current_user();
            $user_bk_id = $user->ID;
            global $wpdb;


            if ($tid<=0) {
                        $sql_req = "SELECT bk.booking_id as id FROM ".$wpdb->prefix ."booking as bk
                        INNER JOIN ".$wpdb->prefix ."bookingtypes as bt
                        ON    bk.booking_type = bt.booking_type_id   WHERE  bk.is_new = 1 and bt.users = " . $user_bk_id;

                        
                        $booking_str_id = '';
                        $bookings = $wpdb->get_results( $wpdb->prepare($sql_req ));
                        if (count($bookings)>0) {
                            foreach ($bookings as $key=>$value) {
                                $booking_str_id .= $value->id . ',';
                            }
                            if (strlen($booking_str_id)>0) $booking_str_id = substr($booking_str_id,0,-1);
                            $update_sql = "UPDATE ".$wpdb->prefix ."booking AS bk SET bk.is_new = 0 WHERE bk.is_new = 1 AND bk.booking_id IN (". $booking_str_id ."); ";                           
                        }
            }

            return $update_sql;
        }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


// G e n e r a l    H o o k    E n g i n e    ////////////////////////////////////////////////////////////////////////////////////////////////

        // Get
        function wpdev_bk_get_option($blank, $option, $default){

            $user_bk_id = 1;
            if ($this->client_side_active_params_of_user !== false) {           // Client side get STD option for this user
                $user_bk_id = $this->client_side_active_params_of_user;
            }

            if ( defined('WP_ADMIN') )
                if ( WP_ADMIN === true )  {
                    $user = wp_get_current_user();
                    $user_bk_id = $user->ID;
                }

            if ( $this->is_user_super_admin($user_bk_id) )  return $blank;                                 // Standard Way

            // Exeptions /////////////////////////////////////////////////////////////////////////////////////////
            $exception_value = $this->check_get_option_exception($blank, $option, $default);
            if ( $exception_value !== 'no-exceptions' ) return $exception_value;
            //////////////////////////////////////////////////////////////////////////////////////////////////////

            $u_value = get_user_option( $option, $user_bk_id );
            if (empty($u_value)) return $blank;

             return $u_value;
        }

        // Update
        function wpdev_bk_update_option($blank, $option, $newvalue){

            $user_bk_id = 1;
            if ($this->client_side_active_params_of_user !== false) {           // Client side get STD option for this user
                $user_bk_id = $this->client_side_active_params_of_user;
            }

            if (defined('WP_ADMIN'))
            if ( WP_ADMIN === true )  {
                $user = wp_get_current_user();
                $user_bk_id = $user->ID;
            }

            if ($this->activated_user !== false) {                              // Get ID for Activation process at admin panel
                $user_bk_id = $this->activated_user;
            }

            if ( $this->is_user_super_admin($user_bk_id) )  return $blank;                                 // Standard Way

            return update_user_option( $user_bk_id, $option, $newvalue ) ;

        }

        // Delete
        function wpdev_bk_delete_option($blank, $option){

            $user_bk_id = 1;
            if ($this->client_side_active_params_of_user !== false) {           // Client side get STD option for this user
                $user_bk_id = $this->client_side_active_params_of_user;
            }

            if ( WP_ADMIN === true )  {
                $user = wp_get_current_user();
                $user_bk_id = $user->ID;
            }

            if ( $this->is_user_super_admin($user_bk_id) )  return $blank;                                 // Standard Way

            return delete_user_option( $user_bk_id, $option);

        }

        // Add
        function wpdev_bk_add_option($blank, $option, $value, $deprecated,  $autoload){

            $user_bk_id = 1;
            if ($this->client_side_active_params_of_user !== false) {           // Client side get STD option for this user
                $user_bk_id = $this->client_side_active_params_of_user;
            }

            if ( WP_ADMIN === true )  {
                $user = wp_get_current_user();
                $user_bk_id = $user->ID;
            }

            if ($this->activated_user === false) {                              // Get ID for Activation process at admin panel

                            //debuge($_SERVER, WP_ADMIN, $user_bk_id, $this->is_user_super_admin($user_bk_id));die;
                            // If we make activation from cron and do not know about user
                            if ( wpdev_bk_is_this_demo() )
                                return $blank;
                             

                $user = wp_get_current_user();
                $user_bk_id = $user->ID;

            } else {    // Right now is Activation process is go on.
                $user_bk_id = $this->activated_user;
            }

            if ( $this->is_user_super_admin($user_bk_id) )  return $blank;                                 // Standard Way

            return update_user_option( $user_bk_id, $option, $value );
            //return add_user_meta($user_bk_id, $option, $value);
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Exceptions
        ////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Get exeption for some option to common users
        function check_get_option_exception($blank, $option, $default){
            if ($option == 'booking_default_booking_resource') return $this->get_default_bk_resource_for_user();
            if ($option == 'booking_is_show_legend' ) return $blank;
            return 'no-exceptions';
        }

        // Delete common users options After ACTIVATION - needed that user get this option from superadmin
        function delete_options_for_users_after_activation($us_id) {

            $options_for_delete = array();
            $options_for_delete[] = 'booking_skin';
            $options_for_delete[] = 'booking_max_monthes_in_calendar' ;
            $options_for_delete[] = 'bookings_num_per_page';
            $options_for_delete[] = 'booking_sort_order';
            $options_for_delete[] = 'booking_sort_order_direction';
            $options_for_delete[] = 'booking_admin_cal_count' ;
            $options_for_delete[] = 'booking_title_after_reservation' ;
            $options_for_delete[] = 'booking_title_after_reservation_time' ;
            $options_for_delete[] = 'booking_type_of_thank_you_message' ;
            $options_for_delete[] = 'booking_thank_you_page_URL' ;
            $options_for_delete[] = 'booking_date_format';
            $options_for_delete[] = 'booking_date_view_type';
            $options_for_delete[] = 'booking_client_cal_count' ;
            $options_for_delete[] = 'booking_start_day_weeek' ;
            $options_for_delete[] = 'booking_is_use_hints_at_admin_panel' ;
            $options_for_delete[] = 'booking_multiple_day_selections' ;
            $options_for_delete[] = 'booking_is_delete_if_deactive' ;
            $options_for_delete[] = 'booking_wpdev_copyright' ;
            $options_for_delete[] = 'booking_is_use_captcha' ;
            $options_for_delete[] = 'booking_is_show_legend' ;
            $options_for_delete[] = 'booking_is_use_autofill_4_logged_user' ;
            $options_for_delete[] = 'booking_unavailable_day0' ;
            $options_for_delete[] = 'booking_unavailable_day1' ;
            $options_for_delete[] = 'booking_unavailable_day2' ;
            $options_for_delete[] = 'booking_unavailable_day3' ;
            $options_for_delete[] = 'booking_unavailable_day4' ;
            $options_for_delete[] = 'booking_unavailable_day5' ;
            $options_for_delete[] = 'booking_unavailable_day6' ;
            
            $options_for_delete[] = 'booking_user_role_booking' ;
            $options_for_delete[] = 'booking_user_role_addbooking' ;
            $options_for_delete[] = 'booking_user_role_resources' ;
            $options_for_delete[] = 'booking_user_role_settings' ;

            $options_for_delete[] = 'booking_unavailable_days_num_from_today' ;
            $options_for_delete[] = 'booking_is_show_powered_by_notice' ;


            // Personal
            $options_for_delete[] = 'booking_url_bookings_edit_by_visitors';
            $options_for_delete[] = 'booking_default_booking_resource';
            $options_for_delete[] = 'booking_is_change_hash_after_approvement';
            $options_for_delete[] = 'booking_resourses_num_per_page';
            // Business Small
            $options_for_delete[] = 'booking_range_selection_type';
            $options_for_delete[] = 'booking_range_selection_is_active';
            $options_for_delete[] = 'booking_range_selection_days_count';
            $options_for_delete[] = 'booking_range_start_day';
            $options_for_delete[] = 'booking_range_selection_days_count_dynamic';
            $options_for_delete[] = 'booking_range_start_day_dynamic';
            $options_for_delete[] = 'booking_range_selection_time_is_active';
            $options_for_delete[] = 'booking_range_selection_start_time';
            $options_for_delete[] = 'booking_range_selection_end_time';
            $options_for_delete[] = 'booking_time_format';
            $options_for_delete[] = 'booking_recurrent_time';

            $options_for_delete[] = 'booking_auto_approve_new_bookings_is_active';
            $options_for_delete[] = 'booking_auto_cancel_pending_unpaid_bk_is_active' ;
            $options_for_delete[] = 'booking_auto_cancel_pending_unpaid_bk_time' ;
            $options_for_delete[] = 'booking_auto_cancel_pending_unpaid_bk_is_send_email' ;
            $options_for_delete[] = 'booking_auto_cancel_pending_unpaid_bk_email_reason' ;

//            $options_for_delete[] = 'booking_paypal_ipn_use_ssl';
//            $options_for_delete[] = 'booking_paypal_ipn_use_curl';

            // Business Medium
            $options_for_delete[] = 'booking_is_show_cost_in_tooltips';
            $options_for_delete[] = 'booking_highlight_cost_word';
            // Hotel
            $options_for_delete[] =  'booking_is_use_visitors_number_for_availability' ;
            $options_for_delete[] =  'booking_is_show_availability_in_tooltips' ;
            $options_for_delete[] =  'booking_highlight_availability_word'    ;
            $options_for_delete[] =  'booking_availability_based_on'  ;
            $options_for_delete[] =  'booking_search_form_show' ;
            $options_for_delete[] =  'booking_found_search_item' ;
            $options_for_delete[] =  'booking_cache_expiration';
            $options_for_delete[] =  'booking_cache_content'  ;
            $options_for_delete[] =  'booking_cache_created'  ;
            $options_for_delete[] =  'booking_is_dissbale_booking_for_different_sub_resources' ;


            foreach ($options_for_delete as $value) {
                delete_user_option($us_id, $value);
            }


        }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// M a i n   f u n c t i o n s     ///////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Funct. Admin function call of ACTIVATION of user
        function reactivate_user($us_id, $is_delete_options = true) {
            global $wpdb;

            $us_status = get_user_option( 'booking_is_active',$us_id);

             $us_data = get_userdata($us_id);
             $nicename = $us_data->user_nicename;

            if ( $us_status == 'Off') { // Its mean that user already was perviosly activated, so have all options. so just set On this option
                 update_user_option($us_id, 'booking_is_active','On');               // Now user is active
                 return ;
            }
            //////////////////////////////////////////////////////////////////////////////////////////////////
            // Need to check if this user exist
            $this->activated_user = $us_id;                                     // Activation is started


            make_bk_action('wpdev_booking_activate_user');                      // General hook for activation of plugin


            // Set Access for this user /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $metavalues = get_user_option( 'capabilities', $us_id );            // Set access level as has this user
            foreach ($metavalues as $key=>$value) {
                if ($value == 1) {
                    $my_role= $key;
                    update_user_option( $us_id, 'booking_user_role_booking', $my_role );
                    update_user_option( $us_id, 'booking_user_role_addbooking', $my_role );
                    update_user_option( $us_id, 'booking_user_role_resources', $my_role );
                    update_user_option( $us_id, 'booking_user_role_settings', $my_role );
                    break;
                }
            }

            // Update to user Emails Adress //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $user_bk_email = htmlspecialchars('"Booking system" <' . get_user_option('user_email',  $us_id ) .'>') ;
            // All
            update_user_option($us_id, 'booking_email_reservation_adress', $user_bk_email);          // htmlspecialchars('"Booking system" <' .get_option('admin_email').'>'));
            update_user_option($us_id, 'booking_email_reservation_from_adress', $user_bk_email);     // htmlspecialchars('"Booking system" <' .get_option('admin_email').'>'));
            update_user_option($us_id, 'booking_email_approval_adress', $user_bk_email);             //htmlspecialchars('"Booking system" <' .get_option('admin_email').'>'));
            update_user_option($us_id, 'booking_email_deny_adress', $user_bk_email);                 //htmlspecialchars('"Booking system" <' .get_option('admin_email').'>'));
            // Pro
            update_user_option($us_id, 'booking_email_modification_adress', $user_bk_email );        //htmlspecialchars('"Booking system" <' .get_option('admin_email').'>'));
            update_user_option($us_id, 'booking_email_newbookingbyperson_adress', $user_bk_email );  //htmlspecialchars('"Booking system" <' .get_option('admin_email').'>'));
            // Business Small
            update_user_option($us_id, 'booking_email_payment_request_adress', $user_bk_email);      //  htmlspecialchars('"Booking system" <' .get_option('admin_email').'>'));
            update_user_option($us_id, 'booking_paypal_emeil', get_user_option('user_email',  $us_id ));      //  htmlspecialchars('"Booking system" <' .get_option('admin_email').'>'));

            update_user_option($us_id, 'booking_paypal_ipn_verified_email', get_user_option('user_email',  $us_id ));
            update_user_option($us_id, 'booking_paypal_ipn_invalid_email' , get_user_option('user_email',  $us_id ));
            update_user_option($us_id, 'booking_paypal_ipn_error_email' , get_user_option('user_email',  $us_id ));

            // Delete options from standard boking settings - Its mean that this settings have to getted from SUPER admin initial options
            if ($is_delete_options)
                if (! $this->is_user_super_admin($us_id) )
                    $this->delete_options_for_users_after_activation($us_id);


            // Create 1 default booking resource for this user //////////////////////////////////////////////////////////////////////////////////////////////////
            if (  $this->get_default_bk_resource_for_user(false, $us_id ) === false ) { // No booking resource, so create some one
                $wp_query = "INSERT INTO ".$wpdb->prefix ."bookingtypes ( title, cost, users ) VALUES ( '". __('Default', 'wpdev-booking') . ' ('.$nicename.')' ."',50 , ". $us_id ." );";
                if ( false === $wpdb->query( $wpdb->prepare($wp_query ) ) ) {    // All users data
                    ?> <script type="text/javascript"> document.getElementById('ajax_working').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during creating default booking resource for user' ,__FILE__,__LINE__ ); ?></div>'; </script> <?php
                }
            }


            $wp_queries[] = 'INSERT INTO '.$wpdb->prefix .'booking_seasons ( title, filter, users ) VALUES ( "'. __('Weekend', 'wpdev-booking') . ' ('.$nicename.')' .'", \'a:4:{s:8:"weekdays";a:7:{i:0;s:2:"On";i:1;s:3:"Off";i:2;s:3:"Off";i:3;s:3:"Off";i:4;s:3:"Off";i:5;s:3:"Off";i:6;s:2:"On";}s:4:"days";a:31:{i:1;s:2:"On";i:2;s:2:"On";i:3;s:2:"On";i:4;s:2:"On";i:5;s:2:"On";i:6;s:2:"On";i:7;s:2:"On";i:8;s:2:"On";i:9;s:2:"On";i:10;s:2:"On";i:11;s:2:"On";i:12;s:2:"On";i:13;s:2:"On";i:14;s:2:"On";i:15;s:2:"On";i:16;s:2:"On";i:17;s:2:"On";i:18;s:2:"On";i:19;s:2:"On";i:20;s:2:"On";i:21;s:2:"On";i:22;s:2:"On";i:23;s:2:"On";i:24;s:2:"On";i:25;s:2:"On";i:26;s:2:"On";i:27;s:2:"On";i:28;s:2:"On";i:29;s:2:"On";i:30;s:2:"On";i:31;s:2:"On";}s:7:"monthes";a:12:{i:1;s:2:"On";i:2;s:2:"On";i:3;s:2:"On";i:4;s:2:"On";i:5;s:2:"On";i:6;s:2:"On";i:7;s:2:"On";i:8;s:2:"On";i:9;s:2:"On";i:10;s:2:"On";i:11;s:2:"On";i:12;s:2:"On";}s:4:"year";a:14:{i:2009;s:2:"On";i:2010;s:2:"On";i:2011;s:3:"Off";i:2012;s:3:"Off";i:2013;s:3:"Off";i:2014;s:3:"Off";i:2015;s:3:"Off";i:2016;s:3:"Off";i:2017;s:3:"Off";i:2018;s:3:"Off";i:2019;s:3:"Off";i:2020;s:3:"Off";i:2021;s:3:"Off";i:2022;s:3:"Off";}}\', '.$us_id.' );';
            $wp_queries[] = 'INSERT INTO '.$wpdb->prefix .'booking_seasons ( title, filter, users ) VALUES ( "'. __('Work days', 'wpdev-booking') . ' ('.$nicename.')' .'", \'a:4:{s:8:"weekdays";a:7:{i:0;s:3:"Off";i:1;s:2:"On";i:2;s:2:"On";i:3;s:2:"On";i:4;s:2:"On";i:5;s:2:"On";i:6;s:3:"Off";}s:4:"days";a:31:{i:1;s:2:"On";i:2;s:2:"On";i:3;s:2:"On";i:4;s:2:"On";i:5;s:2:"On";i:6;s:2:"On";i:7;s:2:"On";i:8;s:2:"On";i:9;s:2:"On";i:10;s:2:"On";i:11;s:2:"On";i:12;s:2:"On";i:13;s:2:"On";i:14;s:2:"On";i:15;s:2:"On";i:16;s:2:"On";i:17;s:2:"On";i:18;s:2:"On";i:19;s:2:"On";i:20;s:2:"On";i:21;s:2:"On";i:22;s:2:"On";i:23;s:2:"On";i:24;s:2:"On";i:25;s:2:"On";i:26;s:2:"On";i:27;s:2:"On";i:28;s:2:"On";i:29;s:2:"On";i:30;s:2:"On";i:31;s:2:"On";}s:7:"monthes";a:12:{i:1;s:2:"On";i:2;s:2:"On";i:3;s:2:"On";i:4;s:2:"On";i:5;s:2:"On";i:6;s:2:"On";i:7;s:2:"On";i:8;s:2:"On";i:9;s:2:"On";i:10;s:2:"On";i:11;s:2:"On";i:12;s:2:"On";}s:4:"year";a:14:{i:2009;s:2:"On";i:2010;s:2:"On";i:2011;s:3:"Off";i:2012;s:3:"Off";i:2013;s:3:"Off";i:2014;s:3:"Off";i:2015;s:3:"Off";i:2016;s:3:"Off";i:2017;s:3:"Off";i:2018;s:3:"Off";i:2019;s:3:"Off";i:2020;s:3:"Off";i:2021;s:3:"Off";i:2022;s:3:"Off";}}\', '.$us_id.' );';
            $wp_queries[] = 'INSERT INTO '.$wpdb->prefix .'booking_seasons ( title, filter, users ) VALUES ( "'. __('High season', 'wpdev-booking') . ' ('.$nicename.')' .'", \'a:4:{s:8:"weekdays";a:7:{i:0;s:2:"On";i:1;s:2:"On";i:2;s:2:"On";i:3;s:2:"On";i:4;s:2:"On";i:5;s:2:"On";i:6;s:2:"On";}s:4:"days";a:31:{i:1;s:2:"On";i:2;s:2:"On";i:3;s:2:"On";i:4;s:2:"On";i:5;s:2:"On";i:6;s:2:"On";i:7;s:2:"On";i:8;s:2:"On";i:9;s:2:"On";i:10;s:2:"On";i:11;s:2:"On";i:12;s:2:"On";i:13;s:2:"On";i:14;s:2:"On";i:15;s:2:"On";i:16;s:2:"On";i:17;s:2:"On";i:18;s:2:"On";i:19;s:2:"On";i:20;s:2:"On";i:21;s:2:"On";i:22;s:2:"On";i:23;s:2:"On";i:24;s:2:"On";i:25;s:2:"On";i:26;s:2:"On";i:27;s:2:"On";i:28;s:2:"On";i:29;s:2:"On";i:30;s:2:"On";i:31;s:2:"On";}s:7:"monthes";a:12:{i:1;s:3:"Off";i:2;s:3:"Off";i:3;s:3:"Off";i:4;s:3:"Off";i:5;s:2:"On";i:6;s:2:"On";i:7;s:2:"On";i:8;s:2:"On";i:9;s:2:"On";i:10;s:3:"Off";i:11;s:3:"Off";i:12;s:3:"Off";}s:4:"year";a:14:{i:2009;s:2:"On";i:2010;s:2:"On";i:2011;s:3:"Off";i:2012;s:3:"Off";i:2013;s:3:"Off";i:2014;s:3:"Off";i:2015;s:3:"Off";i:2016;s:3:"Off";i:2017;s:3:"Off";i:2018;s:3:"Off";i:2019;s:3:"Off";i:2020;s:3:"Off";i:2021;s:3:"Off";i:2022;s:3:"Off";}}\', '.$us_id.' );';

            foreach ($wp_queries as $wp_q) $wpdb->query($wpdb->prepare($wp_q));


            // Activate this user ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            update_user_option($us_id, 'booking_is_active','On');               // Now user is active
            update_user_option($us_id, 'booking_max_num_of_resources', 99 );    // Max num of resources for this user

            $this->activated_user = false;                                      // Activation id Finished
            return;
            /////////////////////////////////////////////////////////////////////////////////////////////
            ?> <script type="text/javascript">
					if (document.getElementById('ajax_working') != null) {
						document.getElementById('ajax_working').innerHTML =
                            '<div class="info_message ajax_message" id="ajax_message">\n\
                                            <div style="float:left;"><?php echo __('Updating...', 'wpdev-booking'); ?></div> \n\
                                            <div  style="float:left;width:80px;margin-top:-3px;">\n\
                                                   <img src="'+wpdev_bk_plugin_url+'/img/ajax-loader.gif">\n\
                                            </div>\n\
                                        </div>';
						document.getElementById('ajax_message').innerHTML = '<?php echo __('User  is Activated', 'wpdev-booking'); ?>';
						jQuery('#ajax_message').fadeOut(2000);
					}
            </script> <?php
        }

        // DEACTIVATE and delete all settings for specific user.
        function deactivate_user($us_id, $is_delete_options_also = true, $is_delete_user_bookings = false) {
            global $wpdb;

            if ($is_delete_options_also == true) {

                // Delete all meta bookings values for specific user
                if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM ". $wpdb->usermeta ." WHERE user_id = " . $us_id . "" ) . " AND meta_key LIKE '%booking_%' " )) {    // All users data
                    ?> <script type="text/javascript"> document.getElementById('ajax_working').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during deleting user meta at DB' ,__FILE__,__LINE__ ); ?></div>'; </script> <?php
                    die();
                }

                if ($is_delete_user_bookings == true) {                         // Delete User BK Resources and user bookings of this resources

                    // First select IDs of resources, which we need to delete
                    $bk_res_IDs = $wpdb->get_results($wpdb->prepare("SELECT booking_type_id as ID FROM ".$wpdb->prefix ."bookingtypes WHERE users =  " . $us_id));
                    $string_res_id = '';
                    foreach ($bk_res_IDs as $br_ID) {
                        if ($string_res_id!='') $string_res_id.=',';
                        $string_res_id .= $br_ID->ID;
                    }
                    // secondly select bookings ID
                    $bk_IDs = $wpdb->get_results($wpdb->prepare("SELECT booking_id as ID FROM ".$wpdb->prefix ."booking WHERE booking_type IN (" . $string_res_id .") " ));
                    $string_id = '';
                    foreach ($bk_IDs as $bk_ID) {
                        if ($string_id!='') $string_id.=',';
                        $string_id .= $bk_ID->ID;
                    }
                    // D E L E T E     D a t a
                    // Dates
                    if ($string_id!='') if ( false === $wpdb->query( $wpdb->prepare("DELETE FROM ".$wpdb->prefix ."bookingdates WHERE booking_id IN ($string_id)" ) ) ) { ?> <script type="text/javascript"> document.getElementById('ajax_working').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during updating exist booking for deleting dates in BD' ,__FILE__,__LINE__); ?></div>'; </script> <?php die(); }
                    // Bookings
                    if ($string_id!='') if ( false === $wpdb->query( $wpdb->prepare("DELETE FROM ".$wpdb->prefix ."booking WHERE booking_id IN ($string_id)") ) ){ ?> <script type="text/javascript"> document.getElementById('ajax_working').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during deleting booking at DB',__FILE__,__LINE__ ); ?></div>'; </script> <?php die(); }
                    // Meta data of Resources
                    if ($string_res_id!='') if ( false === $wpdb->query( $wpdb->prepare("DELETE FROM ".$wpdb->prefix ."booking_types_meta WHERE type_id IN ($string_res_id)") ) ) { ?> <script type="text/javascript"> document.getElementById('ajax_working').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during deleting booking at DB',__FILE__,__LINE__ ); ?></div>'; </script> <?php die(); }
                    // Resources
                    if ($string_res_id!='') if ( false === $wpdb->query( $wpdb->prepare("DELETE FROM ".$wpdb->prefix ."bookingtypes WHERE booking_type_id IN ($string_res_id)") ) ) { ?> <script type="text/javascript"> document.getElementById('ajax_working').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during deleting booking resources at DB' ,__FILE__,__LINE__); ?></div>'; </script> <?php die(); }
                    // Season filters
                    if ( false === $wpdb->query( $wpdb->prepare("DELETE FROM ".$wpdb->prefix ."booking_seasons WHERE users =  " . $us_id) )) { ?> <script type="text/javascript"> document.getElementById('ajax_working').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during deleting season filters at DB',__FILE__,__LINE__ ); ?></div>'; </script> <?php die(); }
                }

            } else { // Just turn Off user
                update_user_option($us_id, 'booking_is_active','Off');
            }
            ?> <script type="text/javascript">
                    document.getElementById('ajax_working').innerHTML =
                            '<div class="info_message ajax_message" id="ajax_message">\n\
                                            <div style="float:left;"><?php echo __('Updating...', 'wpdev-booking'); ?></div> \n\
                                            <div  style="float:left;width:80px;margin-top:-3px;">\n\
                                                   <img src="'+wpdev_bk_plugin_url+'/img/ajax-loader.gif">\n\
                                            </div>\n\
                                        </div>';
                    document.getElementById('ajax_message').innerHTML = '<?php echo __('User  is Deactivated', 'wpdev-booking'); ?>';
                    jQuery('#ajax_message').fadeOut(2000);
            </script> <?php

        }


        // Filter. Modify of some SQL request for having users inside belong request
        function multiuser_modify_SQL_for_current_user( $where ) {

            if ( defined('WP_ADMIN') ) {                                        // If at client side so then return default
                if ( WP_ADMIN !== true )  return $where;
            } else                        return $where; 

            $user = wp_get_current_user();
            $user_bk_id = $user->ID;
            if ( $this->is_user_super_admin($user_bk_id) )  return $where; // Standard Way

            $where .=  ' users = ' . $user_bk_id . ' ';
            return $where;

        }

        // Filter. Skip some booking resources at the BK Types, if they do not belong to user
        function multiuser_resource_list($types_list) {

            $user = wp_get_current_user();
            $user_bk_id = $user->ID;
            if ( $this->is_user_super_admin($user_bk_id) )  return $types_list; // Standard Way


            $types_list_new = array();
            //$res_number = 0;
            foreach ($types_list as $single_type) {
                if ($single_type->users == $user_bk_id) {
                    $types_list_new[] = $single_type;
                    //$res_number++;
                    //if ($res_number>=$this->get_max_res_num_for_user($user_bk_id) ) break;
                }
            }

            return $types_list_new;
        }

        // Hook.  ADD new BK Resource, reupdate user_ID, if its not main admin
        function added_new_booking_resource($bk_type_id) {

            global $wpdb;

            $user = wp_get_current_user();
            $user_bk_id = $user->ID;

            // check for administrator
            if ( $this->is_user_super_admin($user_bk_id) )  return false; // Standard Way


            $wp_q = "UPDATE ".$wpdb->prefix ."bookingtypes SET users = ".$user_bk_id." WHERE booking_type_id = ". $bk_type_id ." ;";
            if ( false === $wpdb->query( $wpdb->prepare( $wp_q )) ) {
                ?> <script type="text/javascript"> document.getElementById('ajax_message').innerHTML = '<?php bk_error('Error during updating to DB booking resources' ,__FILE__,__LINE__); ?>'; </script> <?php
            }
        }

        // Hook.  ADD new BK Resource, reupdate user_ID, if its not main admin
        function added_new_season_filter($bk_filter_id) {
            global $wpdb;

            $user = wp_get_current_user();
            $user_bk_id = $user->ID;

            // check for administrator
            if ( $this->is_user_super_admin($user_bk_id) )  return false; // Standard Way


            $wp_q = "UPDATE ".$wpdb->prefix ."booking_seasons SET users = ".$user_bk_id." WHERE booking_filter_id = ". $bk_filter_id ." ;";
            if ( false === $wpdb->query( $wpdb->prepare($wp_q ) )) {
                ?> <script type="text/javascript"> document.getElementById('ajax_message').innerHTML = '<?php bk_error('Error during updating to DB season filter' ,__FILE__,__LINE__); ?>'; </script> <?php
            }
        }

        // Hook.   reupdate user_ID, if its not main admin
        function added_new_coupon($bk_filter_id) {
            global $wpdb;

            $user = wp_get_current_user();
            $user_bk_id = $user->ID;

            // check for administrator
            if ( $this->is_user_super_admin($user_bk_id) )  return false; // Standard Way


            $wp_q = "UPDATE ".$wpdb->prefix ."booking_coupons SET users = ".$user_bk_id." WHERE coupon_id = ". $bk_filter_id ." ;";
            if ( false === $wpdb->query( $wpdb->prepare($wp_q ) ) ) {
                ?> <script type="text/javascript"> document.getElementById('ajax_message').innerHTML = '<?php bk_error('Error during updating to DB season filter' ,__FILE__,__LINE__); ?>'; </script> <?php
            }
        }

        // Check if user can be at some admin panel, which belong to specific booking resource
        function multiuser_is_user_can_be_here($blank, $bk_resource_type){
            global $wpdb;

            $user = wp_get_current_user();
            $user_bk_id = $user->ID;

            // 1. Check if user ACTIVE
            if ($bk_resource_type == 'check_for_active_users') {

                if ($this->is_user_super_admin($user_bk_id)) return true;       // User is superadmin

                $is_user_active = get_user_option('booking_is_active',$user_bk_id);

                if ($is_user_active != 'On') {
                        echo '<div id="no-reservations"  class="warning_message textleft">';
                        printf(__('%sYou do not have permissions for this page.%s Your account is not active, please contact administrator.%s', 'wpdev-booking'), '<h2>', '<br />','</h2>');
                        echo '</div><div style="clear:both;height:10px;"></div>';
                        return false;                                           // User is not active
                } else  return true;                                            // User is active
            }

            // 2. Check on SUPER ADMIN
            if ($bk_resource_type == 'only_super_admin') {
                return $this->is_user_super_admin($user_bk_id);
            }

            // 2. Check on SUPER ADMIN
            if ($bk_resource_type == 'not_low_level_user') {

                if ($this->is_user_low_level($user_bk_id))   // If user low level then return     FALSE
                    return false;
                else                                         // if user NOT low level then return TRUE
                    return true;
            }


            // 3. CHECK on RESOURCES (if user can open specific booking resource or not

            if ($bk_resource_type<=0) return true;

            // check for administrator
            if ( $this->is_user_super_admin($user_bk_id) )  return true; // Standard Way

            $wp_q = "SELECT users FROM ".$wpdb->prefix ."bookingtypes WHERE booking_type_id = ". $bk_resource_type ." ;" ;
            $res = $wpdb->get_results($wpdb->prepare($wp_q));
            if (  count($res) > 0 ) {
                if ($res[0]->users == $user_bk_id) return true;
                else {

                echo '<div id="no-reservations"  class="warning_message textleft">';
                printf(__('%sYou do not have permissions for this booking resources.%s', 'wpdev-booking'), '<h2>', '</h2>');
                echo '</div><div style="clear:both;height:10px;"></div>';

                    return false; }
            } else {
                echo '<div id="no-reservations"  class="warning_message textleft">';
                printf(__('%sNo this booking resources.%s', 'wpdev-booking'), '<h2>', '</h2>');
                echo '</div><div style="clear:both;height:10px;"></div>';

                return false; } // no such bk resource

        }



        // Get maximum number of resources for specific user
        function get_max_res_num_for_user($blank){

            if ($this->is_user_super_admin()) return $blank;

            $user = wp_get_current_user();
            $user_bk_id = $user->ID;

            $booking_max_num_res = get_user_option( 'booking_max_num_of_resources', $user_bk_id );

            return $booking_max_num_res;
        }



        // Set Active user - from now all options will get for this user
        function check_multiuser_params_for_client_side_by_user_id( $user_bk_id ) {
            $this->client_side_active_params_of_user =  $user_bk_id;
        }
        // Activation of process INIT at CLIENT side options, which belong to USER
        function check_multiuser_params_for_client_side($bk_type_id) {
            global $wpdb;
            $wp_q = "SELECT users FROM ".$wpdb->prefix ."bookingtypes WHERE booking_type_id = ". $bk_type_id ." ;" ;
            $res = $wpdb->get_results($wpdb->prepare($wp_q));
            if (  count($res) == 0 ) {
                ?> <script type="text/javascript">
                    if (document.getElementById('submiting<?php echo $bk_type_id; ?>') !== null)
                        document.getElementById('submiting<?php echo $bk_type_id; ?>').innerHTML = '<?php bk_error('Error during searching this booking resources',__FILE__,__LINE__ ); ?>';
                </script> <?php
                return;
            } else {
               $this->check_multiuser_params_for_client_side_by_user_id($res[0]->users);
               //debuge($this->client_side_active_params_of_user);die;
            }

        }
        // Finish activating of Client side belong bkresource
        function finish_check_multiuser_params_for_client_side($bk_type) {
            $this->client_side_active_params_of_user = false;
        }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Show types with max counts of items for this types
        function showing_user_name_in_top_line($title, $bk_type, $count){
            //debuge();
            if (  $this->is_user_super_admin()) {
                if (isset($bk_type->users)) {

                    global $wpdb;
                    // Get actual User and BK Resource, after page load
                    $bk_user_selected = false;
                    if ( isset($_GET['booking_type'])  )  $bk_type_selected = $_GET['booking_type'];
                    else                                 $bk_type_selected = $this->get_default_bk_resource_for_user();
                    if ($bk_type_selected!== false) {
                        $wp_q = "SELECT users as user FROM ".$wpdb->prefix ."bookingtypes WHERE booking_type_id = ". $bk_type_selected ." LIMIT 0, 1;" ;
                        $res = $wpdb->get_results($wpdb->prepare($wp_q));
                        if (count($res)>0)
                            $bk_user_selected = $res[0]->user;
                    }


                    $return_value = ' all_users_bk_resource user_res_'  . $bk_type->users .' ' ;
                    if (! $this->is_user_super_admin($bk_type->users)) $return_value .= ' regulat_user_bk_resource ';
                    else                                               $return_value .= ' superadmin_user_bk_resource ';

                    if (($bk_user_selected !== $bk_type->users ) && ($bk_user_selected>0)) $return_value .= '   hide_user_bk_res ';
                    
                    return $return_value;


                 /*



                  $us_data = get_userdata($bk_type->users);
                  return ' <span class="bktypecount" style="-moz-border-radius:3px 0 0 3px;
    background:none repeat scroll 0 0 #ADB0B0;
    border-right:1px solid #ccE0E7;
    margin:0 0 0 -5px;">
                           <a style="font-weight:normal;font-size:9px;color:#fff;"  href="admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-optiontab=users" title="'.__('Owner of this resource','wpdev-booking').'" rel="tooltip" class="tooltip_bottom"  >' .
                              $us_data->user_nicename . '</a></span>' ;*/
                }
            }
            return '';
        }

        // Show user selection filter for booking resources
        function wpdev_show_users_selection_for_bk_resources (){
            if (!  $this->is_user_super_admin()) return ;

            global $wpdb;

            // Get actual User and BK Resource, after page load
            $bk_user_selected = false;
            if ( isset($_GET['booking_type']) )  $bk_type_selected = $_GET['booking_type'];
            else                                 $bk_type_selected = $this->get_default_bk_resource_for_user();
            if ($bk_type_selected!== false) {
                $wp_q = "SELECT users as user FROM ".$wpdb->prefix ."bookingtypes WHERE booking_type_id = ". $bk_type_selected ." LIMIT 0, 1;" ;
                $res = $wpdb->get_results($wpdb->prepare($wp_q));
                if (count($res)>0)
                    $bk_user_selected = $res[0]->user;
            }
             
            $sql_check_table =    "SELECT * FROM " . $wpdb->prefix . "users as u LEFT JOIN " . $wpdb->prefix . "usermeta as m ON u.ID = m.user_id  WHERE m.meta_key='" . $wpdb->prefix . "capabilities' ORDER BY user_nicename ASC" ;
            $res = $wpdb->get_results($wpdb->prepare($sql_check_table));
            ?>
                <div style="float:left;line-height: 32px;font-size:11px;font-weight: bold;text-shadow:1px 1px 0px #fff;color:#555;vertical-align: top;">
                     <label for="calendar_type" style="vertical-align: top;"><?php _e('Show resources of user:','wpdev-booking'); ?>&nbsp;</label>
                <select id="users_selection_for_res" name="users_selection_for_res" style="margin:5px 5px 0 0;width:120px;"

                        onchange="javascript:
                                jQuery('.all_users_bk_resource').addClass('hide_user_bk_res');
                                jQuery('#childs_bk_resources').addClass('hide_user_bk_res');
                            if (this.value == '')
                                jQuery('.superadmin_user_bk_resource'+ this.value).removeClass('hide_user_bk_res');
                            else
                                jQuery('.user_res_'+ this.value).removeClass('hide_user_bk_res');"

                        >
                    <option value=""><?php _e('My','wpdev-booking') ?></option>
                <?php
                    foreach ($res as $r) {
                        if (! $this->is_user_super_admin($r->ID))
                            if (get_user_option( 'booking_is_active',$r->ID) == 'On' ) {
                                if ($bk_user_selected == $r->ID) $selected_user = ' selected="SELECTED" ';
                                else $selected_user = '';
                                echo '<option value="'.$r->ID.'" '.$selected_user.' >'.$r->user_nicename.'</option>';
                            }
                    }
                ?>
                </select>
                </div>
                <?php //TODO: continue here, need to set this selection to the PRO file and remove booking resource line selection ?>
                <div style="float:left;line-height: 32px;font-size:11px;font-weight: bold;text-shadow:1px 1px 0px #fff;color:#555;">  
                            <?php
                                    $types_list =  get_bk_types(); ?>
                                    <div class="field">
                                        <div style="float:left;">
                                            <label for="calendar_type" style="vertical-align: top;"><?php _e('Booking resource:', 'wpdev-booking'); ?></label>
                                            <select id="calendar_type" name="calendar_type"
                                                onchange="javascript: location.href='<?php echo 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME . 'wpdev-booking&booking_type='; ?>' + this.value;"
                                                >
                                            <?php
                                            for ($i = 0; $i < 100; $i++)


                                            foreach ($types_list as $tl) { ?>


                                            <option value="<?php echo $tl->id; ?>"
                                                        style="<?php if  (isset($tl->parent)) if ($tl->parent == 0 ) { echo 'font-weight:bold;'; } else { echo 'font-size:11px;padding-left:20px;'; } ?>"
                                             <?php  if (isset($_GET['booking_type'])) if ($_GET['booking_type'] ==  $tl->id ) echo ' selected="SELECTED" ';  ?>
                                                    ><?php echo $tl->title; ?></option>
                                            <?php } ?>
                                        </select>
                                        </div>
                                        
                                    </div>
                            <?php  ?>

                </div>

                <div class="clear topmenuitemseparatorv0" style="height:0px;clear:both;" ></div>
            <?php
        }

        // Show header of users collumn
        function show_users_header_at_settings(){
            if (!  $this->is_user_super_admin()) return ;
            ?>
                <th width="80" style="text-align:center;"><?php _e('Users','wpdev-booking'); ?></th>
            <?php
        }

        // Show header of users collumn
        function show_users_footer_at_settings(){
            if (!  $this->is_user_super_admin()) return ;
            ?>
                <td style="width:80px;border-top: 1px solid #ccc;"></td>
            <?php
        }

        // Show users collumn
        function show_users_collumn_at_settings($bt, $alternative_color=''){

            if (!  $this->is_user_super_admin()) return ;
            if ($bt == 'blank') {
                echo '<td style=" height:35px;padding:0px 35px;border-top:1px solid #999;" ></td>';
                return;
            }
            ?>

                <td style="color: #555555;font-size: 10px;font-weight: bold;text-align: center;text-shadow: 0 -1px 0 #DDDDDD;text-transform: capitalize;" <?php echo $alternative_color; ?> >
                <?php
                $us_data = get_userdata($bt->users);
                echo $us_data->user_nicename;
                ?>
                </td>
            <?php
        }


        // Show at top menu of settings - Users menu
        function wpdev_show_top_menu_line(){
            $selected_icon = '';
            $selected_title = '';
            $is_only_icons = false;
            $user = wp_get_current_user();
            $user_bk_id = $user->ID;
            if ( $this->is_user_super_admin($user_bk_id) ) {   // Only superadmin
                ?>
                    <?php $title = __('Users', 'wpdev-booking');  $my_icon = 'users-48x48.png'; $my_tab = 'users';  ?>
                    <?php $slct_a = ''; if (isset($_GET['tab'])) if ($_GET['tab'] == 'users') { $slct_a = 'selected'; }   ?>
                    <?php if ($slct_a == 'selected') { $selected_title = $title; $selected_icon = $my_icon;  ?><span class="nav-tab nav-tab-active"><?php } else { ?><a rel="tooltip" class="nav-tab tooltip_bottom" title="<?php echo __('Management of','wpdev-booking') .' '.strtolower($title). ' ' ; ?>" href="admin.php?page=<?php echo WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME ; ?>wpdev-booking-option&tab=<?php echo $my_tab; ?>"><?php } ?><img class="menuicons" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?><?php if ($slct_a == 'selected') { ?></span><?php } else { ?></a><?php } ?>
                     <?php if ($slct_a == 'selected') { ?>
                        <script type="text/javascript">
                            jQuery('div.wrap div.icon32').attr('style','margin: 10px 25px 10px 10px;');
                        </script>
                     <?php } ?>
                <?php
            }
            return  $selected_icon .'^'. $selected_title ;
        }

        // Show General selection of Settings CONTENT
        function settings_menu_content(){

            switch ($_GET['tab']) {

                 case 'users':
                    ?> <div id="ajax_working" class="clear" style="height:20px;"></div>
                       <div id="poststuff" class="metabox-holder"> <?php $this->show_booking_settings_users(); ?> </div>
                    <?php
                    return false;

                 default:
                    return true;
                    break;
                }
        }

        // Settings page of Users managment
        function show_booking_settings_users(){

            $user = wp_get_current_user();
            $user_bk_id = $user->ID;
            if ( ! $this->is_user_super_admin($user_bk_id) ) return;            // Only superadmin

            $settings_link = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME . 'wpdev-booking-option&tab=users';

            if ( isset( $_POST['submit_users_booking'] ) ) {
                $u_ids = $_POST['wpdev_all_users_id'];
                $u_ids = explode(',',$u_ids);
                foreach ($u_ids as $u_i) {

                    if (isset($_POST['wpdev_user_bk_res_max'.$u_i])) {
                        update_user_option($u_i, 'booking_max_num_of_resources', $_POST['wpdev_user_bk_res_max'.$u_i] );
                    }

                    if (isset($_POST['wpdev_user_active'.$u_i])) {
                       // update_user_option($u_i, 'booking_is_active','On');   // this $u_i user is active for booking
                    } else {
                       // update_user_option($u_i, 'booking_is_active','Off');
                    }

                }
            }/**/

            if (isset($_GET['set_user_super'])) {                               // Set this user as SuperAdmin
                update_user_option( $_GET['set_user_super'] , 'booking_user_role', 'super_admin' );
                ?> <script type="text/javascript"> setTimeout(function ( ) { location.href='<?php echo $settings_link ;?>'; } ,100);  </script> <?php     // Reload the page
            }
            if (isset($_GET['set_user_regular'])) {                             // Set this user as a Regular user
                update_user_option( $_GET['set_user_regular'] , 'booking_user_role', 'regular' );
                 ?> <script type="text/javascript"> setTimeout(function ( ) { location.href='<?php echo $settings_link ;?>'; } ,100);  </script> <?php     // Reload the page
            }

            if (isset($_GET['set_user_low_level'])) {                             // Set this user as a Regular user
                update_user_option( $_GET['set_user_low_level'] , 'booking_user_role', 'low_level_user' );
                 ?> <script type="text/javascript"> setTimeout(function ( ) { location.href='<?php echo $settings_link ;?>'; } ,100);  </script> <?php     // Reload the page
            }

            if (isset($_GET['activate_user'])) {                                // ACTIVATE

                $this->reactivate_user($_GET['activate_user']);                 // Activate here user - Its mean set default settings for this user, and then refresh page with normal link

                ?> <script type="text/javascript"> setTimeout(function ( ) { location.href='<?php echo $settings_link ;?>'; } ,300);  </script> <?php     // Reload the page
            }

            if (isset($_GET['deactivate_user'])) {                              //DEACTIVATE
                $is_delete_user_options = false;
                $is_delete_user_bookings = false;
                if ( isset($_GET['delete_user_data']) )     if ( $_GET['delete_user_data'] == 1 )     $is_delete_user_options = true;
                if ( isset($_GET['delete_user_bookings']) ) if ( $_GET['delete_user_bookings'] == 1 ) $is_delete_user_bookings = true;

                $this->deactivate_user($_GET['deactivate_user'],$is_delete_user_options, $is_delete_user_bookings);

                ?> <script type="text/javascript"> setTimeout(function ( ) { location.href='<?php echo $settings_link ;?>'; } ,300);  </script> <?php     // Reload the page
            }

            global $wpdb;
            $sql_check_table =    "SELECT * FROM " . $wpdb->prefix . "users as u LEFT JOIN " . $wpdb->prefix . "usermeta as m ON u.ID = m.user_id  WHERE m.meta_key='" . $wpdb->prefix . "capabilities' ORDER BY user_nicename ASC" ;
            $res = $wpdb->get_results($wpdb->prepare($sql_check_table));

            ?>
              <div class='meta-box'>  <div  class="postbox" > <h3 class='hndle'><span><?php _e('Users managment', 'wpdev-booking'); ?></span></h3> <div class="inside">
                            <form  name="post_option_cost" action="" method="post" id="post_option_filter" >
                                <table style="width:99%;margin:1%;" class="resource_table0 booking_table" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <th width="15" style="text-align:center"><?php _e('Status','wpdev-booking'); ?></th>
                                        <th width="15" align="center"><?php _e('ID','wpdev-booking'); ?></th>
                                        <th width="120" align="center"><?php _e('User name','wpdev-booking'); ?></th>
                                        <th width="40" align="center"><?php _e('User role','wpdev-booking'); ?></th>

                                        <th width="85" style="text-align:center"><?php _e('Max number of resources','wpdev-booking'); ?></th>
                                        <th style="text-align: center;"><?php _e('Actions for specific user','wpdev-booking'); ?></th>
                                        <th width="220" style="text-align:center"><?php _e('Set user as','wpdev-booking'); ?></th>
                                        <!--th width="55" style="text-align:center"> </th-->
                                    </tr>
                                    <?php
                                        $alternative_color = '0';
                                        $all_users_id = '';
                                        foreach ($res as $el) {
                                          $all_users_id .=    $el->ID . ',';
                                          if ( $alternative_color == '')  $alternative_color = ' class="alternative_color" ';
                                          else $alternative_color = '';

                                          $el->meta_value = unserialize($el->meta_value);
                                          $is_booking_active_for_user = get_user_option( 'booking_is_active', $el->ID );
                                          $booking_max_num_res = get_user_option( 'booking_max_num_of_resources', $el->ID );


                                          if (isset($el->meta_value['subscriber'])) if ($el->meta_value['subscriber']== 1 )    $u_access = 'Subscriber'  ;
                                          if (isset($el->meta_value['contributor'])) if ($el->meta_value['contributor']== 1 )   $u_access = 'Contributor'  ;
                                          if (isset($el->meta_value['author'])) if ($el->meta_value['author']== 1 )        $u_access = 'Author'  ;
                                          if (isset($el->meta_value['editor'])) if ($el->meta_value['editor']== 1 )        $u_access = 'Editor'  ;
                                          if (isset($el->meta_value['administrator'])) if ($el->meta_value['administrator']== 1 ) $u_access = 'Administrator'  ;
                                      ?>
                                          <tr>
                                              <td <?php echo $alternative_color; ?> align="center" style="text-align: center;">
                                                  <input id="wpdev_user_active<?php echo $el->ID; ?>" disabled="DISABLED"  type="checkbox" <?php if (($is_booking_active_for_user == 'On') || ( $this->is_user_super_admin($el->ID) )) echo 'checked="CHECKED"'; ?>  style="margin:0px;"  value="<?php echo $el->meta_value; ?>" name="wpdev_user_active<?php echo $el->ID; ?>"/></td>
                                              <td <?php echo $alternative_color; ?> ><?php echo $el->ID; ?><input id="wpdev_user_id<?php echo $el->ID; ?>"  type="hidden"   value="<?php echo $el->ID; ?>" name="wpdev_user_id<?php echo $el->ID; ?>"/></td>
                                              <td <?php echo $alternative_color; ?> ><input id="wpdev_user_name<?php echo $el->ID; ?>"    size="15" type="text" disabled   value="<?php echo $el->user_nicename; ?>"    name="wpdev_user_name<?php echo $el->ID; ?>"/></td>
                                              <td <?php echo $alternative_color; ?> ><a href="/wp-admin/user-edit.php?user_id=2"><?php echo $u_access; ?></a></td>

                                              <td <?php echo $alternative_color; ?> style="text-align: center;">
                                                  <?php if ( ! $this->is_user_super_admin($el->ID)  ) { ?>
                                                  <input id="wpdev_user_bk_res_max<?php echo $el->ID; ?>"    size="5" type="text"    value="<?php echo $booking_max_num_res; ?>"    name="wpdev_user_bk_res_max<?php echo $el->ID; ?>"/></td>
                                              <?php } ?>
                                              <td <?php echo $alternative_color; ?> >
                                                  <?php if ( ! $this->is_user_super_admin($el->ID)  ) { ?>
                                                        <?php if ($is_booking_active_for_user != 'On') { ?>
                                                  <input type="button" class="button-primary" value="<?php _e('Activate','wpdev-booking');?>"
                                                         onclick="javascript:var answer = confirm('<?php _e('Do you really want', 'wpdev-booking'); echo ' ';  _e('make user active', 'wpdev-booking');  ?>?'); if (! answer){ return false; } location.href='<?php echo $settings_link; ?>&activate_user=<?php echo $el->ID; ?>';"
                                                         />
                                                         <?php } ?>
                                                         <?php if ($is_booking_active_for_user == 'On') { ?>
                                                  <input type="button" class="button" value="<?php _e('Deactivate','wpdev-booking');?>"
                                                         onclick="javascript:jQuery('#wpdev_user_deactivate_btn_<?php echo $el->ID; ?>').slideToggle('normal');this.style.display='none';"
                                                         />
                                                  <div style="display:none;" id="wpdev_user_deactivate_btn_<?php echo $el->ID; ?>">
                                                  <input type="button" class="button" value="<?php _e('Set as inactive','wpdev-booking');?>"
                                                         onclick="javascript:var answer = confirm('<?php _e('Do you really want', 'wpdev-booking'); echo ' ';  _e('make user inactive', 'wpdev-booking'); ?>?'); if (! answer){ return false; }  location.href='<?php echo $settings_link; ?>&deactivate_user=<?php echo $el->ID; ?>'; "
                                                         />
                                                  <input type="button" class="button" value="<?php _e('Delete configuration','wpdev-booking');?>"
                                                         onclick="javascript:var answer = confirm('<?php _e('Do you really want', 'wpdev-booking'); echo ' ';  _e('delete configuration', 'wpdev-booking'); ?>?'); if (! answer){ return false; } location.href='<?php echo $settings_link; ?>&deactivate_user=<?php echo $el->ID; ?>&delete_user_data=1';"
                                                         />
                                                  <input type="button" class="button" value="<?php _e('Delete all booking data','wpdev-booking');?>"
                                                         onclick="javascript:var answer = confirm('<?php _e('Do you really want', 'wpdev-booking'); echo ' ';  _e('delete all booking data', 'wpdev-booking'); ?>?'); if (! answer){ return false; } location.href='<?php echo $settings_link; ?>&deactivate_user=<?php echo $el->ID; ?>&delete_user_data=1&delete_user_bookings=1';"
                                                         />
                                                  </div>
                                                         <?php } ?>
                                                  <?php } else {
                                                      echo '<strong style="color:#B4B4B4;text-shadow:0 0 1px #EEEEEE;;">' , __('Super Admin','wpdev-booking'), '</strong>';
                                                  }
                                                      ?>

                                              </td>
                                              <td <?php echo $alternative_color; ?>   style="text-align:center;">
                                                  <?php if ( ! $this->is_user_super_admin($el->ID)  ) { ?>
                                                  <input type="button" class="button-primary" value="<?php _e('Super Admin','wpdev-booking');?>"
                                                         onclick="javascript:var answer = confirm('<?php _e('Do you really want', 'wpdev-booking'); echo ' ';  _e('set user as', 'wpdev-booking');  echo ' '; _e('Super Admin','wpdev-booking');  ?>?'); if (! answer){ return false; } location.href='<?php echo $settings_link; ?>&set_user_super=<?php echo $el->ID; ?>';"
                                                         />
                                             <?php if(0) { //Anxo customizarion  //START ?>
                                                     <?php if (  ($is_booking_active_for_user == 'On') && ( ! $this->is_user_low_level($el->ID) )  )
                                                            { ?>

                                                      <input type="button" class="button" value="<?php _e('Low Level user','wpdev-booking');?>"
                                                             onclick="javascript:var answer = confirm('<?php _e('Do you really want', 'wpdev-booking'); echo ' ';  _e('set user as', 'wpdev-booking');  echo ' '; _e('Low level user','wpdev-booking'); ?>?'); if (! answer){ return false; } location.href='<?php echo $settings_link; ?>&set_user_low_level=<?php echo $el->ID; ?>';"
                                                             />
                                                      <?php } ?>

                                                     <?php if (  ($is_booking_active_for_user == 'On') && (   $this->is_user_low_level($el->ID) )  )
                                                            { ?>
                                                      <input type="button" class="button" value="<?php _e('Regular user','wpdev-booking');?>"
                                                             onclick="javascript:var answer = confirm('<?php _e('Do you really want', 'wpdev-booking'); echo ' ';  _e('set user as', 'wpdev-booking');  echo ' '; _e('Regular user','wpdev-booking'); ?>?'); if (! answer){ return false; } location.href='<?php echo $settings_link; ?>&set_user_regular=<?php echo $el->ID; ?>';"
                                                             />
                                                      <?php }  ?>
                                              <?php }  //Anxo customizarion  //END ?>


                                                  <?php } else { ?>
                                                  <input type="button" class="button" value="<?php _e('Regular user','wpdev-booking');?>"
                                                         onclick="javascript:var answer = confirm('<?php _e('Do you really want', 'wpdev-booking'); echo ' ';  _e('set user as', 'wpdev-booking');  echo ' '; _e('Regular user','wpdev-booking'); ?>?'); if (! answer){ return false; } location.href='<?php echo $settings_link; ?>&set_user_regular=<?php echo $el->ID; ?>';"
                                                         />
                                                  <?php }  ?>
                                              </td>
                                              <!--td <?php echo $alternative_color; ?> >
                                                  <?php if ( ! $this->is_user_super_admin($el->ID)  ) { ?>
                                                  <input type="button" class="button" value="<?php _e('Login','wpdev-booking');?>"
                                                         onclick="javascript:location.href='<?php echo $settings_link; ?>&deactivate_user=<?php echo $el->ID; ?>';"
                                                         />
                                                  <?php }   ?>
                                              </td-->
                                          </tr>
                                    <?php }
                                        $all_users_id = substr($all_users_id,0,-1);
                                    ?>
                                </table>
                                <input id="wpdev_all_users_id"  type="hidden"   value="<?php echo $all_users_id; ?>" name="wpdev_all_users_id"/>
                                <div class="clear" style="height:5px;"></div>
                                <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="submit_users_booking"/>
                                <div class="clear" style="height:1px;"></div>

                            </form>
                      </div>
                  </div>
              </div>
            <?php
        }

//   A C T I V A T I O N   A N D   D E A C T I V A T I O N    O F   T H I S   P L U G I N  ///////////////////////////////////////////////////

        // Activate
        function pro_activate() {

                //add_bk_option( 'booking_is_use_visitors_number_for_availability', 'Off');

                $charset_collate = '';
                $wp_queries = array();
                global $wpdb;

                if  ($this->is_field_in_table_exists('bookingtypes','users') == 0){
                    $simple_sql = "ALTER TABLE ".$wpdb->prefix ."bookingtypes ADD users BIGINT(20) DEFAULT '1'";
                    $wpdb->query($wpdb->prepare($simple_sql));
                }

                if  ($this->is_field_in_table_exists('booking_seasons','users') == 0){
                    $simple_sql = "ALTER TABLE ".$wpdb->prefix ."booking_seasons ADD users BIGINT(20) DEFAULT '1'";
                    $wpdb->query($wpdb->prepare($simple_sql));
                }

                if  ($this->is_field_in_table_exists('booking_coupons','users') == 0){
                    $simple_sql = "ALTER TABLE ".$wpdb->prefix ."booking_coupons ADD users BIGINT(20) DEFAULT '1'";
                    $wpdb->query($wpdb->prepare($simple_sql));
                }


                        if ( wpdev_bk_is_this_demo() )        {

                                $wp_queries = array();
                                $wp_queries[] = 'DELETE FROM '.$wpdb->prefix .'booking_types_meta ';
                                $wp_queries[] =  'DELETE FROM '.$wpdb->prefix .'bookingtypes WHERE users=1 ;';
                                foreach ($wp_queries as $wp_q) $wpdb->query($wpdb->prepare($wp_q));

                                update_bk_option( 'booking_default_booking_resource',13);

                                if ($this->activated_user === false) { // Users activation
                                    $this->reactivate_user(2, false);
                                    update_user_option( 2 , 'booking_user_role', 'super_admin' );
                                    $this->reactivate_user(3);
                                    $this->reactivate_user(4);
                                    $this->reactivate_user(5);
                                    update_user_option(2, 'booking_max_num_of_resources',  5 );
                                    update_user_option(3, 'booking_max_num_of_resources',  5 );
                                    update_user_option(4, 'booking_max_num_of_resources',  5 );
                                    update_user_option(5, 'booking_max_num_of_resources',  5 );

                                     $wp_queries = array();
                                     $wp_queries[] =  "UPDATE ".$wpdb->prefix ."bookingtypes AS bk SET bk.cost='100' WHERE bk.booking_type_id=13;";
                                     $wp_queries[] =  "UPDATE ".$wpdb->prefix ."bookingtypes AS bk SET bk.cost='500' WHERE bk.booking_type_id=14;";
                                     $wp_queries[] =  "UPDATE ".$wpdb->prefix ."bookingtypes AS bk SET bk.cost='300' WHERE bk.booking_type_id=15;";
                                     $wp_queries[] =  "UPDATE ".$wpdb->prefix ."bookingtypes AS bk SET bk.cost='50' WHERE bk.booking_type_id=16;";


                                     $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET title = '". __('Royal Villa', 'wpdev-booking') ."' WHERE booking_type_id=14 ;";
                                     $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET title = '". __('Suite', 'wpdev-booking') ."' WHERE booking_type_id=15 ;";
                                     $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET title = '". __('Appartment #1', 'wpdev-booking') ."' WHERE booking_type_id=16 ;";
                                     $wp_queries[] =  "INSERT INTO ".$wpdb->prefix ."bookingtypes ( title, cost, users ) VALUES ( '". __('Appartment #2', 'wpdev-booking')  ."',75 , 5 );";

                                     $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET visitors = '2' WHERE booking_type_id=17 ;";
                                     $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET visitors = '2' WHERE booking_type_id=16 ;";
                                     $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET visitors = '3' WHERE booking_type_id=15 ;";
                                     $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET visitors = '5' WHERE booking_type_id=14 ;";                                     
                                     foreach ($wp_queries as $wp_q) $wpdb->query($wpdb->prepare($wp_q));


                                     $us_form = get_user_option( 'booking_form',  3 );
                                     update_user_option( 3, 'booking_form',  'Its individual booking form of Owner1: <br/> ' . $us_form );
                                     $us_form = get_user_option( 'booking_form',  4 );
                                     update_user_option( 4, 'booking_form',  'Its individual booking form of Owner2: <br/> ' . $us_form );
                                     $us_form = get_user_option( 'booking_form',  5 );
                                     update_user_option( 5, 'booking_form',  'Its individual booking form of Owner3: <br/> ' . $us_form );
                                }


			}
        }


        //Decativate
        function pro_deactivate(){
            /**/
            //delete_bk_option( 'booking_is_use_visitors_number_for_availability');
            /**/


            global $wpdb;
            //$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'bookingsubtypes'));

          }



    }
}

?>