<?php
/*
This is COMMERCIAL SCRIPT
We are do not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

if (  (! isset( $_GET['merchant_return_link'] ) ) && (! isset( $_GET['payed_booking'] ) ) && (!function_exists ('get_option')  )  ) { die('You do not have permission to direct access to this file !!!'); }
require_once(WPDEV_BK_PLUGIN_DIR. '/inc/lib_l.php' );
if (file_exists(WPDEV_BK_PLUGIN_DIR. '/inc/multiuser.php')) { require_once(WPDEV_BK_PLUGIN_DIR. '/inc/multiuser.php' ); }
if (file_exists(WPDEV_BK_PLUGIN_DIR. '/lib/winetourbooking-functions.php')) { require_once(WPDEV_BK_PLUGIN_DIR. '/lib/winetourbooking-functions.php' ); }

if (!class_exists('wpdev_bk_biz_l')) {
    class wpdev_bk_biz_l {

                var $wpdev_bk_multiuser;
		
                function wpdev_bk_biz_l(){

                    // Activation
                    add_bk_action('wpdev_booking_activation', array($this, 'pro_activate'));
                    add_bk_action('wpdev_booking_deactivation', array($this, 'pro_deactivate'));

                    // Javascript Declaration
                    add_action('wpdev_bk_js_define_variables', array(&$this, 'js_define_variables') );      // Write JS variables
                    add_action('wpdev_bk_js_write_files', array(&$this, 'js_write_files') );                // Write JS files

                    // Coupons advanced cost customization option.
                    add_bk_filter('coupons_discount_apply', array(&$this, 'coupons_discount_apply'));
                    add_bk_filter('get_coupons_discount_info', array(&$this, 'get_coupons_discount_info'));
                    add_bk_filter('wpdev_get_additional_description_about_coupons', array(&$this, 'wpdev_get_additional_description_about_coupons'));


                    // JS - Tooltip
                    add_filter('wpdev_booking_show_availability_at_calendar', array(&$this, 'show_availability_at_calendar') , 10, 2 );                // Write JS files

                    // INSERT - UPDATE    --   ID   or  Dates
                    add_bk_action('wpdev_booking_reupdate_bk_type_to_childs', array(&$this, 'reupdate_bk_type_to_childs')); // Main function

                    // Filters for changing view of Dates...
                    add_bk_filter('get_bk_dates_sql', array(&$this, 'get_sql_bk_dates_for_all_resources'));  // Modify SQL
                    add_bk_filter('get_bk_dates', array(&$this,     'get_bk_dates_for_all_resources'));  // Modify Result of dates

                    //Booking Table Admin Page -- Show also bookins, where SOME dates belong to this Type
                    // SQL Modification for Admin Panel dates:  (situation, when some bookings dates exist at several resources )
                    add_bk_filter('get_sql_4_dates_from_other_types', array(&$this,     'get_sql_4_dates_from_other_types'));


                    // For some needs
                    add_bk_filter('get_booking_types_hierarhy_linear', array(&$this,     'get_booking_types_hierarhy_linear'));  // Modify Result of dates


                    // Admin panel, show resource nearly dates
                    add_bk_action('show_diferent_bk_resource_of_this_date', array(&$this, 'show_diferent_bk_resource_of_this_date'));                    
                    add_bk_action('wpdev_show_subtypes_selection', array(&$this, 'wpdev_show_subtypes_selection'));

                    // Show childs count at top line of selection booking resources
                    add_bk_filter('showing_capacity_of_bk_res_in_top_line', array(&$this, 'showing_capacity_of_bk_res_in_top_line'));


                    // Require for footer JS declaration at clicking parent resource
                    add_bk_filter('get_bk_resources_in_hotel', array(&$this, 'get_booking_types')); 



                    // Booking Page  - Show only for PARENT booking resource
                    add_bk_action('show_all_bookings_for_parent_resource', array(&$this, 'show_all_bookings_for_parent_resource'));
                    add_bk_action('check_if_bk_res_parent_with_childs_set_parent_res', array(&$this, 'check_if_bk_res_parent_with_childs_set_parent_res'));

                    // Settings Page
                    add_bk_action('wpdev_booking_settings_show_content', array(&$this, 'settings_menu_content')); // Settings
                    add_bk_action('wpdev_booking_settings_show_coupons', array(&$this, 'settings_show_coupons')); // Settings

                    add_bk_action('show_additional_shortcode_help_for_form', array($this, 'show_additional_shortcode_help_for_form'));


                    // Resources settings //
                    add_bk_action('resources_settings_after_title', array($this, 'resources_settings_after_title'));
                    add_bk_action('resources_settings_table_headers', array($this, 'resources_settings_table_headers'));
                    add_bk_action('resources_settings_table_footers', array($this, 'resources_settings_table_footers'));
                    add_bk_action('resources_settings_table_collumns', array($this, 'resources_settings_table_collumns'));
                    add_bk_action('resources_settings_table_info_collumns', array($this, 'resources_settings_table_info_collumns'));
                    add_bk_action('resources_settings_table_add_bottom_button', array($this, 'resources_settings_table_add_bottom_button'));
                    add_bk_filter('get_sql_4_update_bk_resources', array(&$this, 'get_sql_4_update_bk_resources'));
                    add_bk_filter('get_sql_4_insert_bk_resources_fields_h', array(&$this, 'get_sql_4_insert_bk_resources_fields'));
                    add_bk_filter('get_sql_4_insert_bk_resources_values_h', array(&$this, 'get_sql_4_insert_bk_resources_values'));
                    add_bk_action('insert_bk_resources_recheck_max_visitors', array($this, 'insert_bk_resources_recheck_max_visitors'));

                    // Search functionality
                    add_bk_filter('wpdev_get_booking_search_form', array(&$this, 'wpdev_get_booking_search_form'));
                    add_bk_action('wpdev_ajax_booking_search', array($this, 'show_booking_search_results'));


                    add_action('wpdev_bk_general_settings_advanced_section', array(&$this, 'show_advanced_settings_in_general_settings_menu') );
                    add_action('settings_set_show_availability_in_tooltips', array(&$this, 'settings_set_show_availability_in_tooltips') );


                    if ( class_exists('wpdev_bk_multiuser')) {  $this->wpdev_bk_multiuser = new wpdev_bk_multiuser();
                    } else {                                $this->wpdev_bk_multiuser = false; }

//REMOVE TODAT DEBUG //TODO:DEBUG
//add_shortcode('booking_debug', array(&$this, 'reupdate_bk_type_to_childs'));
                }


    // <editor-fold defaultstate="collapsed" desc=" S U P P O R T       F u n c t i o n s ">
    
    // S U P P O R T       F u n c t i o n s    //////////////////////////////////////////////////////////////////////////////////////////////////

                // Reset to Payment form
                function reset_to_default_form($form_type ){
                       return '[calendar] \n\
        <div style="text-align:left;line-height:28px;"><p>'. __('The cost for payment', 'wpdev-booking').': [cost_hint]</p></div>   \n\
        <div style="text-align:left"> \n\
        [cost_corrections] \n\
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
        <p>'. __('Coupon', 'wpdev-booking').':<br />  [coupon coupon] </p> \n\    
        \n\
        <p>[captcha]</p> \n\
        \n\
        <p>[submit "'. __('Send', 'wpdev-booking').'"]</p> \n\
        </div>';
                 }


        // write JS variables
        function js_define_variables() {
            ?>
                         <script  type="text/javascript">
                             var message_verif_visitors_more_then_available   =   '<?php echo esc_js(__('Probably number of selected visitors more, then available at selected day(s)!', 'wpdev-booking')); ?>';
                             var message_verif_visitors_per_hour   =   '<?php echo esc_js(__('The number of person is to heavy!', 'wpdev-booking')); ?>';
            <?php   if (get_bk_option( 'booking_is_use_visitors_number_for_availability') == 'On') { ?>
                                 is_use_visitors_number_for_availability =  true;
                <?php } else { ?>
                                 is_use_visitors_number_for_availability =  false;
                <?php } ?>
                                var availability_based_on   = '<?php echo get_bk_option( 'booking_availability_based_on'  ); ?>';
                                var max_visitors_4_bk_res = [];
								var max_visitor_per_visit = [];
                        <?php  
                            $my_page = 'client';                                            // Get a page
                            if (        strpos($_SERVER['REQUEST_URI'],'wpdev-booking.phpwpdev-booking-reservation') !== false )  $my_page = 'add';
                            else if ( strpos($_SERVER['REQUEST_URI'],'wpdev-booking.phpwpdev-booking')!==false)                   $my_page = 'booking';
                            
                            if (
                                    ($my_page != 'add') ||
                                    (isset($_GET['parent_res'])) ||

                                    (   ($my_page == 'add') &&                  // For situation, when default bk resource is not set and this is parent resource
                                        ( ! isset($_GET['booking_type']) )  &&
                                        (  $this->check_if_bk_res_have_childs(  get_bk_option( 'booking_default_booking_resource') ) )  )
                                ) {

                                //// Define Parent BK Resources (types) for JS
                                $arr = $this->get_booking_types_hierarhy_linear();
                                foreach ($arr as $bk_res) {
                                        if ($bk_res['count'] > 1 )
                                            if (isset($bk_res['obj']->id))
                                                echo ' parent_booking_resources[parent_booking_resources.length] = ' . $bk_res['obj']->id . '; ';
                                }

                                $max_visitor_per_visit = $this->get_max_visitor_per_visit();
                                foreach($max_visitor_per_visit as $key=>$value){
									if(! empty($key)){ ?>
                                        max_visitor_per_visit[<?php echo $key; ?>] = <?php echo $value; ?>;
                                    <?php }
                                }
                            }
                        ?>
                        </script>
            <?php
        }

        // write JS Scripts
        function js_write_files() {
            // wp_enqueue_script ('biz_l', WPDEV_BK_PLUGIN_URL . '/inc/js/biz_l.js');
			?> <script type="text/javascript" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/inc/js/biz_l.js"></script>  <?php
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


        function get_available_spots_for_bk_res( $type_id ){

            $availability_based_on_visitors   = get_bk_option( 'booking_availability_based_on');

            if ($availability_based_on_visitors == 'visitors') {                // Based on Visitors
                // $max_visitors_in_bk_res = $this->get_max_visitors_for_bk_resources($type_id);
                $max_visitors_in_bk_res_summ=$this->get_summ_max_visitors_for_bk_resources($type_id);
                return $max_visitors_in_bk_res_summ;
            } else {                                                            // Based on Items.
                $max_visit_std         = $this->get_max_available_items_for_resource($type_id);
                return $max_visit_std;
            }

        }

    // </editor-fold>


    // <editor-fold defaultstate="collapsed" desc=" C O U P O N S  ">

            // Apply advanced cost to the cost from paypal form
            function coupons_discount_apply( $summ , $form , $bktype  ){

                $original_summ = $summ;                                         // Original cost for booking

                $this->delete_expire_coupons();  // Delete some coupons if they are expire already

                $coupons = $this->get_coupons_for_this_resource($bktype);
                
                if ( count($coupons) <= 0) return $original_summ;               // No coupons so return as it is

                $booking_form_show = get_form_content ($form, $bktype);

                if (isset($booking_form_show['coupon']))
                    if (! empty($booking_form_show['coupon'])) {

                            $entered_code =$booking_form_show['coupon'];
                            foreach ($coupons as $coupon) {

                              if ($entered_code == $coupon->coupon_code)
                                if ($summ >= $coupon->coupon_min_sum) {
                                    if ($coupon->coupon_type == 'fixed') {      // Fixed discount
                                        // modif wtb vivien 04/07/12 - prend en compte le fait que le coupon soit supérieur (ou égal) à la somme a regler.
										// modif wtb vivien 04/07/12 - Met à jour dans la BDD la valeur du coupon
										if ($coupon->coupon_value < $summ) {
                                            //$this->wtb_update_coupon_values ($coupon->coupon_id, 0);
											return ($original_summ - $coupon->coupon_value);
                                        }
										else {
											//$this->wtb_update_coupon_values ($coupon->coupon_id, $coupon->coupon_value - $original_summ);
											return 0;
										}
                                    }
                                    if ($coupon->coupon_type == '%') {          // Procent of
                                        if ($coupon->coupon_value <= 100) {
                                            return ($original_summ - $coupon->coupon_value * $original_summ / 100 );
                                        }
                                    }
                                }

                            }

                    }

                return  $original_summ ;
            }

			// modif wtb vivien 04/07/12 - Met à jour dans la BDD la valeur du coupon
			function wtb_update_coupon_values ($coupon_id, $newValue) {
				$update_sql = "UPDATE ".$wpdb->prefix ."booking_coupons AS bkcp SET bkcp.coupon_value='$newValue' WHERE bkcp.coupon_id=$coupon_id;";
                $wpdb->query($wpdb->prepare( $update_sql ));
			}

            // Get > Array discount info,   if it can be apply to the specific bk_resource and summ or return FALSE
            function get_coupons_discount_info( $summ , $form , $bktype  ){

                $original_summ = $summ;                                         // Original cost for booking

                $coupons = $this->get_coupons_for_this_resource($bktype);

                if ( count($coupons) <= 0) return false;               // No coupons so return as it is

                $booking_form_show = get_form_content ($form, $bktype);

                if (isset($booking_form_show['coupon']))
                    if (! empty($booking_form_show['coupon'])) {

                            $entered_code =$booking_form_show['coupon'];
                            foreach ($coupons as $coupon) {

                              if ($entered_code == $coupon->coupon_code)
                                if ($summ >= $coupon->coupon_min_sum) {
                                    if ($coupon->coupon_type == 'fixed') {      // Fixed discount
                                        // modif wtb vivien 03/07/12 - prend en compte le fait que le coupon soit supérieur (ou égal) à la somme a regler.
										if ($coupon->coupon_value < $summ) {
                                            $currency = apply_bk_filter('get_currency_info', 'paypal');
                                            return (array($original_summ, $coupon->coupon_value, $coupon->coupon_code ,   $currency  . $coupon->coupon_value ));
                                        }
										// cas où le montant du cheque cadeau est superieur à celui du montant à régler
										else {
											return (array($original_summ, $coupon->coupon_value, $coupon->coupon_code ,   $currency  . $original_summ));
										}
                                    }
                                    if ($coupon->coupon_type == '%') {          // Procent of
                                        if ($coupon->coupon_value < 100) {
                                            return (array($original_summ , $coupon->coupon_value * $original_summ / 100,  $coupon->coupon_code,    round($coupon->coupon_value,0)  . '%' ));
                                        }
                                    }
                                }

                            }

                    }

                return  false ;
            }

            // Get Line with description according Coupon Discount, which is apply
            function wpdev_get_additional_description_about_coupons($blank, $bk_type , $dates,  $time_array, $form_post ){

                // get COST without discount
                $summ_without_discounts   = apply_bk_filter('wpdev_get_bk_booking_cost', $bk_type , $dates, $time_array , $form_post , false );

                // Get Array with info according discount
                $additional_discount_info = $this->get_coupons_discount_info( $summ_without_discounts , $form_post , $bk_type  );

                if ($additional_discount_info !== false) {                      // If discount is exist

                    $currency = apply_bk_filter('get_currency_info', 'paypal'); // Get currency
                    
                    if (strpos($additional_discount_info[3], '%') !== false)    // % or $
                         $coupon_value = $additional_discount_info[3] . ' (' . $currency  . $additional_discount_info[1] . ') '; //  % with currency
                    else $coupon_value = $additional_discount_info[3] ;                                                          // Only currency
					
					$currency = apply_bk_filter('get_currency_info', 'paypal');
                    $blank = '<span style="font-style:italic;font-size:85%;" class="coupon_description">[' .
                                          __('coupon','wpdev-booking') .  ' <strong>' . $additional_discount_info[2] .'</strong>: ' . $currency . 
                                           $coupon_value .
                                          ' ' . __('discount','wpdev-booking');
                    if ($additional_discount_info[1] > $summ_without_discounts) {
						$valeurCouponRestant = $additional_discount_info[1] - $summ_without_discounts;
						$blank .= '  -  ' . $currency . $valeurCouponRestant . ' ' . __('left','wpdev-booking');
					}
					$blank .= ' ]</span>';
                }

                return $blank;
            }



                  // Delete all expire coupons
                  function delete_expire_coupons() {
                     global $wpdb;
                      $sql = "DELETE FROM ".$wpdb->prefix ."booking_coupons WHERE expiration_date < CURDATE() ";
                      if ( false === $wpdb->query($wpdb->prepare($sql)) ){
                           echo '<div class="error_message ajax_message textleft" style="font-size:12px;font-weight:bold;">';
                           bk_error('Error during deleting from DB coupon' ,__FILE__,__LINE__); echo  '</div>';
                      }
                  }

                  // Check if some coupons exist or not
                  function is_exist_coupons($my_bk_type_id=''){
                      global $wpdb;
                      $sql = "SELECT * FROM ".$wpdb->prefix ."booking_coupons WHERE expiration_date >= CURDATE()";


                      if ($my_bk_type_id != '' )
                         $additional_where = " AND (support_bk_types='all' OR support_bk_types LIKE '%,".$my_bk_type_id.",%' ) LIMIT 0,1" ;
                      else
                         $additional_where = " AND (support_bk_types='all' ) LIMIT 0,1" ;

                      if ($my_bk_type_id == 'any' ) $additional_where = "  LIMIT 0,1" ;

                      $result = $wpdb->get_results($wpdb->prepare( $sql ) . $additional_where );

                      if ( count($result) > 0 ) return true;
                      else return false;
                  }

                  // Get coupons for specific resource
                  function get_coupons_for_this_resource($my_bk_type_id=''){
                      global $wpdb;
                      $sql = "SELECT * FROM ".$wpdb->prefix ."booking_coupons WHERE expiration_date >= CURDATE()";
                      $result = $wpdb->get_results($wpdb->prepare( $sql ) .  " AND (support_bk_types='all' OR support_bk_types LIKE '%,".$my_bk_type_id.",%')" );
                      return $result;
                  }

     // </editor-fold>


    // <editor-fold defaultstate="collapsed" desc=" S E A R C H  ">


        function show_booking_search_results(){ 
		
			global $wpdb ;

            // Initial Search Parameters ///////////////////////////////////////
            $bk_types_id_include = array();
            $bk_types_id_exclude = array();
            $bk_category = '';
            $bk_tag = '';

            if (isset($_POST['bk_check_in'])) $date_start     = $_POST['bk_check_in']  . ' 00:00:00';   //'2010-12-05 00:00:00';
            if (isset($_POST['bk_check_in'])) $date_finish    = $_POST['bk_check_out'] . ' 00:00:00';   //'2011-01-30 00:00:00';
            if (isset($_POST['bk_check_in'])) $min_free_items = $_POST['bk_visitors'];
            if (isset($_POST['bk_category'])) $bk_category = $_POST['bk_category'];
            if (isset($_POST['bk_tag'])) $bk_tag = $_POST['bk_tag'];



            // if (isset($_GET['bk_check_in'])) $date_start     = $_GET['bk_check_in']  . ' 00:00:00';   //'2010-12-05 00:00:00';
            // if (isset($_GET['bk_check_in'])) $date_finish    = $_GET['bk_check_out'] . ' 00:00:00';   //'2011-01-30 00:00:00';
            // if (isset($_GET['bk_check_in'])) $min_free_items = $_GET['bk_visitors'];
            ////////////////////////////////////////////////////////////////////

            // Get all Booking types ///////////////////////////////////////////

            // Include  IDs
            $bk_type_additional_id = '';
            foreach ($bk_types_id_include as $bk_t)  $bk_type_additional_id .= $bk_t . ',';


            // ALL    IDs
            $booking_types1      = $this->get_booking_types(0, ' 1=1 ');
            $booking_types2      = $this->get_booking_types_hierarhy($booking_types1);
            $booking_types       = $this->get_booking_types_hierarhy_linear($booking_types2) ;

            // Get All booking resource ID
            foreach ($booking_types as $bk_t) {
                if ( in_array($bk_t['obj']->id, $bk_types_id_exclude ) === false )
                    $bk_type_additional_id .= $bk_t['obj']->id . ',';
            }

            $bk_type_additional_id = substr($bk_type_additional_id, 0, -1);

            ////////////////////////////////////////////////////////////////////


            //    G e t    B U S Y    D a t e s   //////////////////////////////
            $sql_req = "SELECT DISTINCT dt.booking_date, dt.type_id as date_res_type, dt.booking_id, dt.approved, bk.form, bt.parent, bt.prioritet, bt.booking_type_id as type, bk.cost
                            FROM ".$wpdb->prefix ."bookingdates as dt
                                     INNER JOIN ".$wpdb->prefix ."booking as bk
                                     ON    bk.booking_id = dt.booking_id
                                             INNER JOIN ".$wpdb->prefix ."bookingtypes as bt
                                             ON    bk.booking_type = bt.booking_type_id

                         WHERE dt.booking_date >= '". $date_start ."'  AND dt.booking_date <= '". $date_finish ."' AND
                                                  (  bk.booking_type IN ($bk_type_additional_id) ";   // All bookings from PARENT TYPE
                $sql_req .=                           "OR bt.parent  IN ($bk_type_additional_id) ";   // Bookings from CHILD Type
                $sql_req .=                           "OR dt.type_id IN ($bk_type_additional_id) ";   // Bk. Dates from OTHER TYPEs, which belong to This TYPE
                $sql_req .=                        ") " .
                         " ORDER BY dt.booking_date " ;

            $booking_dates = $wpdb->get_results($wpdb->prepare( $sql_req ));

            ////////////////////////////////////////////////////////////////////

//debuge($bk_type_additional_id, $booking_dates);

            // Create BK Resources ID array for future assigning busy dates.
            $booked_dates_of_bk_resources = array();
            $bk_type_additional_id_arr = explode(',', $bk_type_additional_id);
            foreach ($bk_type_additional_id_arr as $bk_id) { $booked_dates_of_bk_resources[$bk_id] = array(); }

            // Assign busy dates to the BK Res array
            $simple_date_start  = substr( $date_start  ,0,10);
            $simple_date_finish = substr( $date_finish ,0,10);
            foreach ($booking_dates as $dt_obj) {
                $bk_time = substr($dt_obj->booking_date,11);
                $bk_date = substr($dt_obj->booking_date,0,10);
                if ($bk_time  == '00:00:00') {
                    if (! empty($dt_obj->date_res_type) )  $booked_dates_of_bk_resources[ $dt_obj->date_res_type ][] = $dt_obj->booking_date;
                    else                                   $booked_dates_of_bk_resources[ $dt_obj->type ][]          = $dt_obj->booking_date;
                } else {

                    $is_start_time = substr($bk_time,7);
                    if ($is_start_time == '1') $is_start_time = 1;
                    else $is_start_time = 0;
                    if (  ( ($bk_date == $simple_date_start) &&  ( $is_start_time ) ) ||       // Start search date is at date with start time, so this day is BUSY
                          ( ($bk_date == $simple_date_finish) && (! $is_start_time) ) ||       // Finish search date is at date with check out time, so this day is BUSY
                          ( ($bk_date != $simple_date_start) && ($bk_date != $simple_date_finish) )   // Some day is busy inside of search days interval, so this day is BUSY
                        ) {

                            if (! empty($dt_obj->date_res_type) )  $booked_dates_of_bk_resources[ $dt_obj->date_res_type ][] = $dt_obj->booking_date;
                            else                                   $booked_dates_of_bk_resources[ $dt_obj->type ][]          = $dt_obj->booking_date;
                    }

                }
            }

            // Recehck  Dates for the availability based on the season filters.
            $search_dates = wpdevbkGetDaysBetween($date_start, $date_finish);
            
            foreach ($booked_dates_of_bk_resources as $bk_type_id=>$value) {

                foreach ($search_dates as $search_date) {
                    $is_date_available = apply_bk_filter('is_this_day_available_on_season_filters', true , $search_date, $bk_type_id);
                    if (! $is_date_available) {
                        $booked_dates_of_bk_resources[ $bk_type_id ][] = date_i18n( 'Y-m-d H:i:s' , strtotime($search_date)   ) ;
                    }
                }
            }

            // Get only parents and single BK Resources:
            $parents_or_single = $this->get_booking_types_hierarhy( $booking_types1 );
            
            // Remove all busy elements ////////////////////////////////////////////////////////////////////////////////////
            $free_objects = array();
            foreach ($parents_or_single as $key=>$value) {
                
                //check all CHILDS objects, if its booked in this dates interval or not
                if (count($value['child'])>0) { 
                    foreach ($value['child'] as $ch_key=>$ch_value) {
                        if ( count($booked_dates_of_bk_resources[$ch_value->id])> 0 ) { // Some dates are booked for this booking resource at search date interval
                            unset($parents_or_single[$key]['child'][$ch_key]);  // Remove this child oject
                            $parents_or_single[$key]['count']--;                // Reduce the count of child objects
                        }
                    }
                }

                // Check PARENT object if its booked or not
                if ( count($booked_dates_of_bk_resources[  $parents_or_single[$key]['obj']->id  ])> 0 ) { // This item is also booked
                        $parents_or_single[$key]['obj']->is_booked = 1;         // Its booked
                        $parents_or_single[$key]['count']--;                    // Reduce items count
                } else  $parents_or_single[$key]['obj']->is_booked = 0;         // Free
                
                // Set number of available items
                $parents_or_single[$key]['obj']->items_count = $parents_or_single[$key]['count'];

                // If this bk res. available so then add it to new free archive
                if ( ($parents_or_single[$key]['obj']->is_booked != 1) || ($parents_or_single[$key]['obj']->items_count>0) )
                    $free_objects[$key] = $parents_or_single[$key]['obj'];
            }


            // Get SETTINGS, how visitors apply to availability number.
            $is_vis_apply       = get_bk_option( 'booking_is_use_visitors_number_for_availability');  // On  | Off
			$availability_for   = get_bk_option( 'booking_availability_based_on'  );                  // items | visitors
			
            if ($is_vis_apply == 'On') {
                if ($availability_for == 'items') { // items
                    $availability_base = 'items';
                } else {                            // visitors
                    $availability_base = 'visitors';
                }
            } else { // visitors = 'Off'
                    $availability_base = 'off';
            }

            // Remove some items, if availabilty less then number of visitors in search form
            if ( $availability_base !== 'off' ) // check only if visitors apply to availability
                foreach ($free_objects as $key=>$value) {
                    if ($availability_base == 'visitors') {     // visitors
                        // Modif valeur visitors par maxvisitor - wtb vivien 16/06/12
						if ( ($value->items_count * $value->maxvisitor) < $min_free_items ) {
                            // Total number of VISITORS in all available ITEMS less then num of visitors in search form
                            // So remove this item
                            unset($free_objects[$key]);
                        }

                    } else {                                    // items
                        // Modif valeur visitors par maxvisitor - wtb vivien 16/06/12
						if ( ( $value->items_count <= 0 ) || ($value->maxvisitor < $min_free_items ) ) {
                            // we have that items have capacity of visitors less then in search form
                            // or
                            // all items is booked
                            // So remove this item
                            unset($free_objects[$key]);
                        }
                    }
                }


            // Show results ///////////////////////////////////////////////////////////////////////////////////////////////
            // modif wtb vivien - a retirer apres test
			$this->regenerate_booking_search_cache();
			
            $booking_cache_content = get_bk_option( 'booking_cache_content');
            if ( ( empty($booking_cache_content) ) || ( $this->is_booking_search_cache_expire() ) ) {
                $this->regenerate_booking_search_cache();
                $booking_cache_content = get_bk_option( 'booking_cache_content');
            }
//$booking_cache_content = 'a:1:{i:14;O:8:"stdClass":10:{s:2:"ID";s:3:"375";s:10:"post_title";s:23:"Test booking page (de).";s:4:"guid";s:44:"http://holidayformentera.com/wp/?page_id=375";s:12:"post_content";s:29:"[booking type=14 nummonths=1]";s:12:"post_excerpt";s:0:"";s:7:"booking";a:2:{s:4:"type";s:2:"14";s:9:"nummonths";s:1:"1";}s:16:"booking_resource";s:2:"14";s:7:"picture";i:0;s:8:"category";a:0:{}s:4:"tags";a:0:{}}}' ;           ;
//$booking_cache_content = stripslashes($booking_cache_content);
//$booking_cache_content = str_replace("\n","",$booking_cache_content);
//debuge($booking_cache_content);
            if ( is_serialized( $booking_cache_content ) ) $booking_cache_content = unserialize( $booking_cache_content );

//debuge($booking_cache_content);

            // In Category search functionality
            if (! empty($bk_category))
                foreach ($booking_cache_content as $key_c=>$value_c) {
                    $cats = $value_c->category;
                     $is_exist = false;
                    foreach ($cats as $cats_c) {
                        if ( strtolower(trim($cats_c['category'])) == strtolower(trim($bk_category))) $is_exist = true;
                    }
                    if (!  $is_exist ){
                        unset($booking_cache_content[$key_c]);
                    }
                }
//debuge($booking_cache_content, $bk_category);

            // In TAGS search functionality
            if (! empty($bk_tag))
                foreach ($booking_cache_content as $key_c=>$value_c) {
                    $cats = $value_c->tags;
                     $is_exist = false;
                    foreach ($cats as $cats_c) {
                        if (strtolower(trim($cats_c['tag'])) == strtolower(trim($bk_tag))) $is_exist = true;
                    }
                    if (! $is_exist ){
                        unset($booking_cache_content[$key_c]);
                    }
                }
			
			// Modif wtb vivien 27/06/12 - enleve de la liste les propriétés fr ou anglaises suivant la langue actuelle
			// On utilise $_COOKIE['_icl_visitor_lang'] car par $_POST, ICL_LANGUAGE_CODE ça ne fonctionnait pas (peut etre car appel en ajax apres chargement de page) 
			$wtb_langue = $_COOKIE['_icl_visitor_lang'];
			foreach ($booking_cache_content as $key_c=>$value_c) {
				// si on est en langue anglaise et que le suffixe est != '-en' ou que l'on est en francais et que le suffixe = '-en' on retire de la liste
				if (($wtb_langue == 'en' && substr($key_c, -3, 3) != '-en') 
				 || ($wtb_langue != 'en' && substr($key_c, -3, 3) == '-en'))
					unset($booking_cache_content[$key_c]);
			}
			
			// Recupere la forme de l'item (ici propriete) qui sera affichee. Cette forme est parametrable via le back office
            $booking_found_search_item = get_bk_option( 'booking_found_search_item');
            $booking_found_search_item = apply_bk_filter('wpdev_check_for_active_language', $booking_found_search_item );
			
            if (count($free_objects)>0) {
				
				echo '<script type="text/javascript" src="'. WPDEV_BK_PLUGIN_URL . '/inc/js/jquery.quicksand_main.js"></script>';
				echo '<center><h2>'.__('Search results','wpdev-booking') . '</h2></center>';
				// modif wtb - affiche liste categories pour quickSand
				echo  get_quicksand_categories_hmtl ($booking_cache_content);
				echo '<hr />';
				echo '<ul class="ourHolder">';
            }
			else
                echo '<center><h2>'.__('Nothing found','wpdev-booking') . '</h2></center>';

            $bk_date_start  = explode(' ', $date_start);   $bk_date_start = $bk_date_start[0];
            $bk_date_finish = explode(' ', $date_finish);  $bk_date_finish = $bk_date_finish[0];
			
			// modif wtb vivien 26/06/12 - gestion des places en anglais
			if ($wtb_langue == 'en')
				$langue = '-en';
			else
				$langue = '';
				
			// Pour chaque propriete de la liste, modifie les shortCode par le code definitif et l'affiche
            foreach ($free_objects as $key=>$value) {
				// modif wtb vivien 26/06/12 - on remplace '$value->id' par '$value->id'-en si anglais
				$id_value = $value->id . $langue;
				
				$booking_found_search_item_echo = $booking_found_search_item;

				if ( (isset( $booking_cache_content[ $id_value ]->picture)) && ( $booking_cache_content[ $id_value ]->picture != 0) ){
					$image_src = $booking_cache_content[ $id_value ]->picture[0];
					$image_w   = $booking_cache_content[ $id_value ]->picture[1];
					$image_h   = $booking_cache_content[ $id_value ]->picture[2];

					$booking_found_search_item_echo = str_replace('[booking_featured_image]', '<img class="booking_featured_image" src="'.$image_src.'" />', $booking_found_search_item_echo);
				} else
					$booking_found_search_item_echo = str_replace('[booking_featured_image]', '', $booking_found_search_item_echo);

				if ( (isset( $booking_cache_content[ $id_value ]->post_excerpt)) && ( $booking_cache_content[ $id_value ]->post_excerpt != '' ) ) {
					$booking_info = $booking_cache_content[ $id_value ]->post_excerpt;
					$booking_info = str_replace('"','',$booking_info);
					$booking_info = str_replace("'",'',$booking_info);
					// modif wtb vivien 28/06/12 - ajout pour interpreter html en plus du simple texte
					$booking_info = html_entity_decode($booking_info);
					$booking_found_search_item_echo = str_replace('[booking_info]', '<div class="booking_search_result_info">'.$booking_info.'</div>', $booking_found_search_item_echo);
				} else
				$booking_found_search_item_echo = str_replace('[booking_info]', '', $booking_found_search_item_echo);
				
				
				$booking_found_search_item_echo = str_replace('[booking_resource_title]', '<div class="booking_search_result_title">' . $value->title . '</div>', $booking_found_search_item_echo);
				$booking_found_search_item_echo = str_replace('[num_available_resources]', '<span class="booking_search_result_items_num">'.$value->items_count .'</span>', $booking_found_search_item_echo);
				$booking_found_search_item_echo = str_replace('[max_visitors]', '<span class="booking_search_result_visitors_num">'.$value->visitors .'</span>', $booking_found_search_item_echo);
				$cost_currency = apply_bk_filter('get_currency_info', 'paypal');
				$booking_found_search_item_echo = str_replace('[standard_cost]', '<span class="booking_search_result_cost">'.$cost_currency .  $value->cost .'</span>', $booking_found_search_item_echo);


				// if this bk rsource is inserted in some page so then show it
				if (isset($booking_cache_content[ $id_value ])) {

					$my_link = $booking_cache_content[ $id_value ]->guid;
					if (strpos($my_link,'?')=== false) $my_link .= '?';
					else                               $my_link .= '&';
					
					// modif wtb vivien 02/07/12 - si page anglaise alors on affiche le lien vers la propriété anglaise
					if ($wtb_langue == 'en') {
						if (strpos($my_link, $_SERVER['HTTP_HOST'] . '/place') === false) 
							$my_link = str_replace($_SERVER['HTTP_HOST'], $_SERVER['HTTP_HOST'] . '/en/place', $my_link);	
						else
							$my_link = str_replace($_SERVER['HTTP_HOST'], $_SERVER['HTTP_HOST'] . '/en', $my_link);	
					}
					
					$booking_found_search_item_echo = str_replace('[link_to_booking_resource]', 
							'<a href="'.$my_link.'bk_check_in='.$bk_date_start.'&bk_check_out='.$bk_date_finish.'&bk_type='.$value->id.'#bklnk'.$value->id.'" >'.__('Book now','wpdev-booking').'</a>', $booking_found_search_item_echo);		
					
					// modif wtb pour quicksand
					echo '<li class="item" data-id="id-' . $booking_cache_content[ $id_value ]->ID . '" data-type="'. get_quicksand_slug_categories_of_post($booking_cache_content[ $id_value ]->ID) .'">
					<div  class="booking_search_result_item">' . $booking_found_search_item_echo.'</div>
					</li>';
				}
            }
			// ajout wtb
			echo '</ul>';
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

            ?><script type="text/javascript" >
                document.getElementById('booking_search_results' ).innerHTML = '';
            </script> <?php
        }


        // Get Search form results
        function wpdev_get_booking_search_form($search_form, $attr){
                global $wpdb;
                ?>
                        <style type="text/css">
                            #datepick-div .datepick-header {
                                   width: 172px !important;
                            }
                            #datepick-div {
                                -border-radius: 3px;
                                -box-shadow: 0 0 2px #888888;
                                -webkit-border-radius: 3px;
                                -webkit-box-shadow: 0 0 2px #888888;
                                -moz-border-radius: 3px;
                                -moz-box-shadow: 0 0 2px #888888;
                                width: 172px !important;
                            }
                            #datepick-div .datepick .datepick-days-cell a{
                                font-size: 12px;
                            }
                            #datepick-div table.datepick tr td {
                                border-top: 0 none !important;
                                line-height: 24px;
                                padding: 0 !important;
                                width: 24px;
                            }
                            #datepick-div .datepick-control {
                                font-size: 10px;
                                text-align: center;
                            }

                        </style>
                        <script type="text/javascript" >
                             var search_emty_days_warning = '<?php echo esc_js(__('Please select check in and check out days!', 'wpdev-booking')); ?>';

                            function selectCheckInDay(date) {

                                 if (document.getElementById('booking_search_check_out') != null) {

                                    var start_bk_month_4_check_out = document.getElementById('booking_search_check_in').value.split('-');
                                    var myDate = new Date();
                                    myDate.setFullYear( (1*start_bk_month_4_check_out[0]+0), (1*start_bk_month_4_check_out[1]) ,  (1*start_bk_month_4_check_out[2]) );
                                    var my_date = myDate.getDate(); if (my_date < 10 ) my_date = '0' + my_date;
                                    var my_month = myDate.getMonth(); if (my_month < 10 ) my_month = '0' + my_month;
                                    document.getElementById('booking_search_check_out').value = myDate.getFullYear() + '-' + my_month + '-' + my_date ;
                                }
                                
                            }

                            function setDaysForCheckOut(date){
                                
                                if ( (document.getElementById('booking_search_check_in') != null) && (document.getElementById('booking_search_check_in').value != '') ) {

                                     var value = document.getElementById('booking_search_check_in').value;
                                     var year_m_d = value.split("-");
                                     var checkInDate = new Date();
                                     checkInDate.setFullYear( year_m_d[0], (year_m_d[1]-1) , (year_m_d[2]-1) );
                                     if(checkInDate <= date ) return [true, ''];     // Available
                                     else                     return [false, ''];    // Unavailable

                                } else return [true, ''];
                            }
						
                        jQuery(document).ready( function(){
                            jQuery('#booking_search_check_in').datepick(
                                {   onSelect: selectCheckInDay,
                                    showOn: 'focus',
                                    multiSelect: 0,
                                    numberOfMonths: 1,
                                    stepMonths: 1,
                                    prevText: '<<',
                                    nextText: '>>',
                                    dateFormat: 'yy-mm-dd',
                                    changeMonth: false,
                                    changeYear: false,
                                    minDate: 0, maxDate: booking_max_monthes_in_calendar, //'1Y',
                                    showStatus: false,
                                    multiSeparator: ', ',
                                    closeAtTop: false,
                                    firstDay:<?php echo get_bk_option( 'booking_start_day_weeek' ); ?>,
                                    gotoCurrent: false,
                                    hideIfNoPrevNext:true,
                                    //rangeSelect:wpdev_bk_is_dynamic_range_selection,
                                    //calendarViewMode:wpdev_bk_calendarViewMode,
                                    useThemeRoller :false,
                                    mandatory: true/**/
                                }
                          );
                          jQuery('#booking_search_check_out').datepick(
                                {   beforeShowDay: setDaysForCheckOut,
                                    showOn: 'focus',
                                    multiSelect: 0,
                                    numberOfMonths: 1,
                                    stepMonths: 1,
                                    prevText: '<<',
                                    nextText: '>>',
                                    dateFormat: 'yy-mm-dd',
                                    changeMonth: false,
                                    changeYear: false,
                                    minDate: 0, maxDate: booking_max_monthes_in_calendar, //'1Y',
                                    showStatus: false,
                                    multiSeparator: ', ',
                                    closeAtTop: false,
                                    firstDay:<?php echo get_bk_option( 'booking_start_day_weeek' ); ?>,
                                    gotoCurrent: false,
                                    hideIfNoPrevNext:true,
                                    //rangeSelect:wpdev_bk_is_dynamic_range_selection,
                                    //calendarViewMode:wpdev_bk_calendarViewMode,
                                    useThemeRoller :false,
                                    mandatory: true/**/
                                }
                          );
                              });
                        </script>

            <?php
            // Get   shortcode   parameters ////////////////////////////////////
            //if ( isset( $attr['param'] ) )   { $my_boook_count = $attr['param'];  }

			// *************************** Modif WineTourBooking *********************************
			
			if ($_POST['good'] == 'yes') {
				// ajout wtb scripts
				wp_enqueue_script('jquery.quicksand', WPDEV_BK_PLUGIN_URL . '/inc/js/jquery.quicksand.js');
				wp_enqueue_script('jquery.quicksand', WPDEV_BK_PLUGIN_URL . '/inc/js/jquery.easing.1.3.js');
				//wp_enqueue_script('jquery.quicksand_main', WPDEV_BK_PLUGIN_URL . '/inc/js/jquery.quicksand_main.js');
				// echo '<script type="text/javascript" src="'. WPDEV_BK_PLUGIN_URL . '/inc/js/jquery.quicksand_main.js"></script>';
				// echo '<script type="text/javascript" src="' . WPDEV_BK_PLUGIN_URL . '/inc/js/jquery.quicksand.js"></script>';
			}
			
			$valeur_category	= $_POST['category'];
			$valeur_tag			= $_POST['tag'];
			$valeur_check_in 	= $_POST['check_in'];
			$valeur_check_out 	= $_POST['check_out'];
			
            $booking_search_form_show = get_bk_option( 'booking_search_form_show');
            $booking_search_form_show =  apply_bk_filter('wpdev_check_for_active_language', $booking_search_form_show );

            $booking_search_form_show = str_replace( '[search_category]',
                      '<input type="text" size="10" value="'.$valeur_category.'" style="margin-right:20px;" name="category" id="booking_search_category" >',
                                                       $booking_search_form_show);

            $booking_search_form_show = str_replace( '[search_tag]',
                      '<input type="text" size="10" value="'.$valeur_tag.'" style="margin-right:20px;" name="tag" id="booking_search_tag" >',
                                                       $booking_search_form_show);
			
            $booking_search_form_show = str_replace( '[search_check_in]',
                      '<input type="text" size="14" value="'.$valeur_check_in.'" style="margin-right:20px;" name="check_in" id="booking_search_check_in" >',
                                                       $booking_search_form_show);
            
			$booking_search_form_show = str_replace( '[search_check_out]',
                      '<input type="text" size="14" value="'.$valeur_check_out.'" style="margin-right:20px;"  name="check_out"  id="booking_search_check_out">',
                                                       $booking_search_form_show);

            $st = strpos($booking_search_form_show, '[search_visitors');
            if ( $st !== false ) {
                
                $search_visitors_options = '';
                $fin = strpos($booking_search_form_show, ']', $st+16) ;


                $selected_values = substr($booking_search_form_show, $st+16, $fin - $st - 16);
                $selected_values = trim($selected_values);
                
                if (empty($selected_values) )     $selected_values = array("1", "2", "3", "4", "5", "6");
                else                              $selected_values = explode(' ',$selected_values);

                foreach ($selected_values as $v)  {
                    $v = str_replace('"', '', $v);$v = str_replace("'", '', $v);
                    // Condition wtb
					if ($_POST['visitors'] == $v)
						$search_visitors_options .= '<option selected value="'.$v.'">'.$v.'</option>';
					else
						$search_visitors_options .= '<option value="'.$v.'">'.$v.'</option>';
                }


                $booking_search_form_show = substr($booking_search_form_show, 0, $st) .
                                            '<select style="width:50px;"  name="visitors">'.$search_visitors_options.'</select>' .
                                            substr($booking_search_form_show, $fin+1);
                //$booking_search_form_show = str_replace( '[search_visitors]',
                  //    '<select style="width:50px;"  name="visitors">'.$search_visitors_options.'</select>',
                    //                                   $booking_search_form_show);
            }

            
			// Si page disponibilité, on affiche un bouton de recherche, sinon c'est un submit  
			if ($_POST['good'] == 'yes')
				$debutCode = '<input type="button" onclick="searchFormClck(this.form);"';
			else
				$debutCode = '<input type="submit"';
			
			
				$booking_search_form_show = str_replace( '[search_button]',
						  $debutCode.' value="'.__('Suggest me some visits','wpdev-booking').'" class="search_booking">', $booking_search_form_show);
														   
			// Le formulaire renvoie toujours sur la page de disponibilité
            // modif wtb vivien - 20/06/12 Renvoie sur la page anglaise si on parcourt le site en anglais
			global $wtb_langue;
			$wtb_langue = ICL_LANGUAGE_CODE;
			if (ICL_LANGUAGE_CODE == 'en')
				$suffixe = '/en/availabilities';
			else
				$suffixe = '/disponibilites';
			$search_form = '<div  id="booking_search_form" class="booking_form_div booking_search_form">
                    <form name="booking_search_form" action="http://' . $_SERVER['HTTP_HOST'] . $suffixe . '" id="booking_form" method="post">
					<input name="good" type="hidden" id="good" value="yes">'.
                         $booking_search_form_show .
                        '<div style="clear:both;"></div>
                    </form>
                </div>
				<div id="booking_search_results"></div>
				<div id="booking_search_ajax"></div>';
				
            // Recherche à l'initialisation ssi page disponibilité
            if ($_POST['good'] == 'yes')
				$search_form .= '<script>searchFormClck(document.getElementById("booking_form"));</script>';
			
            return $search_form;
        }


                  // Generate NEW booking search cache
                  function regenerate_booking_search_cache(){

					$available_booking_resources = array();
					global $wpdb;
					$sql = $wpdb->prepare("SELECT ID, post_title, guid, post_content, post_excerpt FROM ".$wpdb->posts." WHERE post_status = 'publish'  AND ( post_type != 'revision' )") . " AND post_content LIKE '%[booking %' ";
					$list_posts = $wpdb->get_results($sql);

					if( !empty($list_posts))
					  foreach ($list_posts as $value) {
						  $post_id = $value->ID;
						  $image_src = false;
						  if ( 	$post_id &&
								function_exists('has_post_thumbnail') &&
								has_post_thumbnail( $post_id ) &&
								($image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'post-thumbnail' ) )
							 )
							  {
								  if (count($image)>2) {
									  $image_src = $image[0];
									  $image_w   = $image[1];
									  $image_h   = $image[2];
								  }
							  }

						  
						  $shortcode_start   = strpos($value->post_content,     '[booking ');
						  $shortcode_end     = strpos($value->post_content, ']',$shortcode_start);
						  $shortcode_content = substr($value->post_content, $shortcode_start+9, $shortcode_end - $shortcode_start-9);

						  $shortcode_content_attr = explode(' ', $shortcode_content);
						  $shortcode_attributes = array();
						  
						  foreach ($shortcode_content_attr as $attr) {
							  $attr_key_value = explode('=', $attr);
							  if (count($attr_key_value)>1)
									$shortcode_attributes[ $attr_key_value[0] ] = $attr_key_value[1];
						  }
						  if (! isset($shortcode_attributes['type'])) $shortcode_attributes['type']=1;
						  $value->booking = $shortcode_attributes;
						  $value->booking_resource = $shortcode_attributes['type'];
							
						  if ($image_src !== false)
							$value->picture = array($image_src,$image_w,$image_h);
						  else $value->picture = 0;

						  $us_id = apply_bk_filter('get_user_of_this_bk_resource', false, $value->booking_resource );
						  if ($us_id !== false) {
							  $value->user = $us_id;
						  }
						  
						  $categories = get_the_terms($post_id,'category');
						  $post_cats = array();
						  if (! empty($categories))
							  foreach ($categories as $cat) {
								  $post_cats[]=array('category'=>$cat->name, 'slug'=>$cat->slug, 'ID'=>$cat->term_id);
							  }
						  $value->category = $post_cats;

						  // modif wtb vivien 26/06/12 - récuperation des placetags au lieu des post_tag inutiles ici car on utilise des places et non des posts
						  $tags = get_the_terms($post_id,'placetags');
						  $post_tags = array();
						  if (! empty($tags))
							  foreach ($tags as $cat) {
								  $post_tags[]=array('tag'=>$cat->name, 'slug'=>$cat->slug, 'ID'=>$cat->term_id);
							  }
						  $value->tags = $post_tags;

						  // modif wtb vivien - recupere les categories des places (pour le filtre des disponibilités)
						  $place_cats = get_the_terms($post_id,'placecategory');
						  $post_place_cats = array();
						  if (! empty($place_cats))
							  foreach ($place_cats as $cat) {
								  $post_place_cats[]=array('tag'=>$cat->name, 'slug'=>$cat->slug, 'ID'=>$cat->term_id);
							  }
						  $value->place_cats = $post_place_cats;

						  $value->post_title    = htmlspecialchars($value->post_title, ENT_QUOTES);
						  $value->post_content  = htmlspecialchars($value->post_content, ENT_QUOTES);
						  $value->post_excerpt  = htmlspecialchars($value->post_excerpt, ENT_QUOTES);

						  // modif wtb vivien 26/06/12 - afin d'obtenir les places anglaises on modifie la clé car sinon confrontation clé place anglaise et francaise
						  $compteur++;
						  $tag_anglais = 'en';
						  if ($this->contient_tag($value->tags, $tag_anglais))
							  $id_value = $shortcode_attributes['type'] . '-en';
						  else 
							  $id_value = $shortcode_attributes['type'];
						  if (! isset($available_booking_resources[$id_value]))
							  $available_booking_resources[$id_value] = $value;
					  }
					  $available_booking_resources_serilized = serialize($available_booking_resources);
					  update_bk_option( 'booking_cache_content' ,  $available_booking_resources_serilized );
					  update_bk_option( 'booking_cache_created' ,    date_i18n('Y-m-d H:i:s'   ) );
                  }

				  // modif wtb vivien 27/06/12 - ajout fonction verifiant si un tag apparait dans une liste
				  function contient_tag($liste_tags, $tagATrouver){
					  foreach ($liste_tags as $tag) {
						  if ($tag ['slug'] == $tagATrouver)
							  return true;
					  }
					  return false;
				  }
					
                  function is_booking_search_cache_expire(){


                      $previos = get_bk_option( 'booking_cache_created'     );
                      $previos = explode(' ',$previos);
                      $previos_time = explode(':',$previos[1]);
                      $previos_date = explode('-',$previos[0]);
                      
                      $previos_sec = mktime($previos_time[0], $previos_time[1], $previos_time[2], $previos_date[1], $previos_date[2], $previos_date[0]);
                      $now_sec = mktime();

                      $period =  get_bk_option( 'booking_cache_expiration'     );

                    if (substr($period,-1,1) == 'd' ) {
                        $period = substr($period,0,-1);
                        $period = $period * 24 * 60 * 60;
                    }

                    if (substr($period,-1,1) == 'h' ) {
                        $period = substr($period,0,-1);
                        $period = $period * 60 * 60;
                    }

                      $now_tm = explode(' ',date_i18n('Y-m-d H:i:s'   ) );
                      $now_tm_time = explode(':',$now_tm[1]);
                      $now_tm_date = explode('-',$now_tm[0]);
                      $now_tm_sec = mktime($now_tm_time[0], $now_tm_time[1], $now_tm_time[2], $now_tm_date[1], $now_tm_date[2], $now_tm_date[0]);
                      
                    if( ($previos_sec + $period ) > $now_tm_sec )
                        return  0;
                    else return  1;
                  }

     // </editor-fold>



    // <editor-fold defaultstate="collapsed" desc=" C L I E N T   S I D E ">

    //   C L I E N T   S I D E        //////////////////////////////////////////////////////////////////////////////////////////////////

        // JavaScript TOOLTIP - Availability  arrays with variables
        function show_availability_at_calendar($blank, $type_id, $max_days_count = 365 ) {
            $start_script_code = '';

            // Save at the Advnaced settings these 3 parameters
            $is_show_availability_in_tooltips =    get_bk_option( 'booking_is_show_availability_in_tooltips' );
            $highlight_availability_word      =    get_bk_option( 'booking_highlight_availability_word');
            $highlight_availability_word      =  apply_bk_filter('wpdev_check_for_active_language', $highlight_availability_word );
            
            $is_bookings_depends_from_selection_of_number_of_visitors = get_bk_option( 'booking_is_use_visitors_number_for_availability');

            global $wpdb;
            $sql_req = $this->get_sql_bk_dates_for_all_resources('', $type_id, 'all', '') ;
            $dates_approve   = $wpdb->get_results($wpdb->prepare( $sql_req ));


            $busy_dates = array();          // Busy dates and booking ID as values for each day
            $busy_dates_bk_type = array();  // Busy dates and booking TYPE ID as values for each day
            $temp_time_checking_arr = array();

            // Get DAYS Array with bookings ID inside of each day. So COUNT of day will be number of booked childs
            foreach ($dates_approve as $date_object) {
                $date_without_time = explode(' ', $date_object->booking_date);
                $date_only_time = $date_without_time[1];
                $date_without_time = $date_without_time[0];
                
                //if ( substr($date_only_time,-2) == '02')  continue;  //Bence customization
                //if ( substr($date_only_time,-2) == '01')  continue;  //Bence customization

                if (!isset( $busy_dates[ $date_without_time ] )) {
                    $temp_time_checking_arr[$date_without_time][$date_object->booking_id] = $date_only_time; // For checking single day selection
                    $busy_dates[ $date_without_time ] = array($date_object->booking_id);

                    if (! empty($date_object->date_res_type)) $busy_dates_bk_type[ $date_without_time ] = array($date_object->date_res_type);
                    else                                      $busy_dates_bk_type[ $date_without_time ] = array($date_object->type);

                    
                } else {

                    if (   ( isset($temp_time_checking_arr[$date_without_time][$date_object->booking_id])  ) &&
                           (  $temp_time_checking_arr[$date_without_time][$date_object->booking_id]  != $date_only_time )
                       ){
                        // Skip Here is situation, when same booking at the same day and in dif time, so skip it, we are leave only start date
                    } else {
                        $busy_dates[ $date_without_time ][] = $date_object->booking_id ;
                        $temp_time_checking_arr[$date_without_time][$date_object->booking_id] = $date_only_time;

                        if (! empty($date_object->date_res_type)) $busy_dates_bk_type[ $date_without_time ][] = $date_object->date_res_type ;
                        else                                      $busy_dates_bk_type[ $date_without_time ][] = $date_object->type ;
                        
                    }
                }
            }
//debuge($dates_approve);
//debuge($is_show_availability_in_tooltips);
//debuge($busy_dates, $busy_dates_bk_type);
            $max_visit_std         = $this->get_max_available_items_for_resource($type_id);
            $is_availability_based_on_items_not_visitors = true;

            $is_use_visitors_number_for_availability   = get_bk_option( 'booking_is_use_visitors_number_for_availability');
            $availability_based_on_visitors   = get_bk_option( 'booking_availability_based_on');
            if ($is_use_visitors_number_for_availability == 'On')
                if ($availability_based_on_visitors == 'visitors')
                    $is_availability_based_on_items_not_visitors = false;
            $max_visitors_in_bk_res = $this->get_max_visitors_for_bk_resources($type_id);
            $max_visitors_in_bk_res_summ=$this->get_summ_max_visitors_for_bk_resources($type_id);


//debuge($busy_dates_bk_type);

            if ( ($is_show_availability_in_tooltips !== 'On')  )  $start_script_code .= ' is_show_availability_in_tooltips = false; ';
            else                                                  $start_script_code .= ' is_show_availability_in_tooltips = true; ';

            $start_script_code .= " highlight_availability_word =  '". esc_js($highlight_availability_word) .  " '; ";

            $start_script_code .= "  availability_per_day[". $type_id ."] = [] ;  ";

            $my_day =  date('m.d.Y' );          // Start days from TODAY
            for ($i = 0; $i < $max_days_count; $i++) {

                $my_day_arr = explode('.',$my_day);

                $day0 = $day = ($my_day_arr[1]+0);
                $month0 = $month= ($my_day_arr[0]+0);
                $year0 = $year = ($my_day_arr[2]+0);
                
                if  ($day< 10) $day0 = '0' . $day;
                if  ($month< 10) $month0 = '0' . $month;

                $my_day_tag =   $month . '-' . $day . '-' . $year ;
                $my_day_tag0 =   $month . '-' . $day0 . '-' . $year0 ;

                if ($is_availability_based_on_items_not_visitors) { // Calculate availability based on ITEMS

                    if (isset($busy_dates[  $year . '-' . $month0 . '-' . $day0   ]))   $my_max_visit = $max_visit_std - count($busy_dates[  $year . '-' . $month0 . '-' . $day0   ]);
                    else                                                                $my_max_visit = $max_visit_std;

                } else {                                             // Calculate availability based on VISITORS

                    if (isset($busy_dates_bk_type[ $year.'-'.$month0.'-'. $day0   ])) {

                            $already_busy_visitors_summ = 0 ;
                            foreach ($busy_dates_bk_type[ $year.'-'.$month0.'-'. $day0   ] as $busy_type_id) {
                                if (isset($max_visitors_in_bk_res[ $busy_type_id ]))
                                    $already_busy_visitors_summ += $max_visitors_in_bk_res[ $busy_type_id ];
                            }
                            $my_max_visit = $max_visitors_in_bk_res_summ - $already_busy_visitors_summ;

                    } else  $my_max_visit = $max_visitors_in_bk_res_summ;

                }

                 $start_script_code .= "  availability_per_day[". $type_id ."]['".$my_day_tag."'] = '".$my_max_visit."' ;  ";

                $my_day =  date('m.d.Y' , mktime(0, 0, 0, $month, ($day+1), $year ));   // Next day
            }
//debuge($start_script_code);


            //$max_visitors_in_bk_res = $this->get_max_visitors_for_bk_resources($type_id);
            foreach ($max_visitors_in_bk_res as $key=>$value) {
                if(! empty($key))
                 $start_script_code .= "  max_visitors_4_bk_res[". $key ."] = ".$value." ;  ";
            }           

            return $start_script_code;
        }

    // </editor-fold>


    // <editor-fold defaultstate="collapsed" desc=" S U P P O R T     A D M I N     F u n c t i o n s ">

    // S U P P O R T     A D M I N     F u n c t i o n s    ///////////////////////////////////////////////////////////////////////////////////

            // Just Get ALL booking types from DB
            function get_booking_types($booking_type_id = 0, $where = '') {
                global $wpdb;                
                $additional_fields = '';

                if ($where === '') {
                    $where = apply_bk_filter('multiuser_modify_SQL_for_current_user', $where);
                    $us_id = apply_bk_filter('get_user_of_this_bk_resource', false, $booking_type_id );
                    if ($us_id !== false)
                        $where =  ' users = ' . $us_id . ' ';

                }

                if ($booking_type_id != 0 ) {
                    
                    $where1 = ' WHERE ( booking_type_id = '. $booking_type_id . ' OR parent = '. $booking_type_id . ') ';
                    
                    if ($where != '') $where = $where1 . ' AND ' . $where;
                    else $where = $where1;
                    
                } else {
                    if ($where != '') $where = ' WHERE ' . $where;
                }

                if ( class_exists('wpdev_bk_multiuser')) {  // If Business Large then get resources from that
                    $additional_fields = ', users ';
                }
                // Ajout parametre maxvisitor - wtb vivien 16/06/12
				$types_list = $wpdb->get_results($wpdb->prepare(
                        "SELECT booking_type_id as id, title, parent, prioritet, cost, visitors, maxvisitor ".$additional_fields." FROM ".$wpdb->prefix ."bookingtypes ". $where ." ORDER BY parent, prioritet" ));
                return $types_list;
            }

                    // Get hierarhy structure TREE of booking resources
                    function get_booking_types_hierarhy($bk_types=array()) {

                        if ( count($bk_types)==0) $bk_types = $this->get_booking_types();

                        $res= array( );

                        foreach ($bk_types as $bt) {
                            if ( $bt->parent == '0' ) {
                                $res[$bt->id] = array( 'obj'=> $bt,  'child'=>array() , 'count'=>1 );
                            }
                        }

                        foreach ($bk_types as $bt) {
                            if ( $bt->parent != '0' ) {
                                if (! isset($res[$bt->parent]['child'][$bt->prioritet])) $res[$bt->parent]['child'][$bt->prioritet] = $bt;
                                else $res[$bt->parent]['child'][ 100* count($res[$bt->parent]['child']) ] = $bt;
                                $res[$bt->parent]['count'] = count($res[$bt->parent]['child'])+1;
                            }
                        }
                        return $res;
                    }

                            // FUNCTION  FOR SETTINGS ////////////////////////////////////////////////////////////
                            // Get linear structure of resources from hierarhy for showing it at the settings page
                            function get_booking_types_hierarhy_linear($bk_types=array()) {
                                if ( count($bk_types)==0) $bk_types = $this->get_booking_types_hierarhy();

                                $res= array();

                                foreach ($bk_types as $bt) {
                                    if (isset($bt['obj']))
                                        $res[] = array( 'obj' => $bt['obj'], 'count' => $bt['count'] );
                                    foreach ($bt['child'] as $b) {
                                        $res[] = array( 'obj' => $b, 'count' => '1' );
                                    }
                                }

                                return $res;
                            }


            // Get Maximum available of items for this resource. Based on capacity.
            function get_max_available_items_for_resource($bk_type) {
                $bk_types =  $this->get_booking_types($bk_type);
                $bk_types =  $this->get_booking_types_hierarhy($bk_types);
                if (isset($bk_types[$bk_type]))
                    if (isset($bk_types[$bk_type]['count']))
                        $max_available_items = $bk_types[$bk_type]['count']  ;

                if (isset($max_available_items))
                    return $max_available_items;
                else
                    return 1;
            }


            // Get NUM of Visitors, which was filled at booking form, if USE VISITORS NUM is Active
            function get_num_visitors_from_form($formdata, $bktype){

                if (get_bk_option( 'booking_is_use_visitors_number_for_availability') == 'On')
                     $is_use_visitors_number_for_availability =  true;
                else $is_use_visitors_number_for_availability =  false;

                $visitors_number = 1;

                if ($is_use_visitors_number_for_availability) {
                    if (isset($formdata)) {
                        $form_data =  get_form_content($formdata, $bktype) ;
                        if ( isset($form_data['visitors']) ) {
                            $visitors_number = $form_data['visitors'];
                        }
                    }
                    return $visitors_number;
                } else return 1;
                

            }

            //MAXIME : New Fonction tous les booking type avec leurs capacité max par visite
            function get_max_visitor_per_visit($booking_type_id = 0){
                $bk_types = $this->get_booking_types($booking_type_id);
                $bk_types = $this->get_booking_types_hierarhy($bk_types);
                $bk_types = $this->get_booking_types_hierarhy_linear($bk_types);

                $return = array();
                foreach($bk_types as $value)
                {
                    $return[$value->id] = $value->maxvisitor;
                }

                return $return;
            }

            // Get Array with ID of booking resources and MAX visitors for each of BK Resources
            function get_max_visitors_for_bk_resources($booking_type_id = 0){

                    $bk_types = $this->get_booking_types($booking_type_id);
                    $bk_types = $this->get_booking_types_hierarhy($bk_types);
                    $bk_types = $this->get_booking_types_hierarhy_linear($bk_types);        // Get linear array sorted by Priority

                    $max_visitors_for_bk_types = array();
                    foreach ($bk_types as $value) {
                        if (isset($value['obj']->visitors))
                            $max_visitors_for_bk_types[  $value['obj']->id  ] = $value['obj']->visitors ;
                        else
                            $max_visitors_for_bk_types[  $value['obj']->id  ] = 1;
                    }

                    return $max_visitors_for_bk_types;
            }

            // Just MAX Number of visitors
            function get_summ_max_visitors_for_bk_resources($booking_type_id = 0){
                $max_visitors_in_bk_res = $this->get_max_visitors_for_bk_resources($booking_type_id);
                $max_visitors_in_bk_res_summ=0;
                foreach ($max_visitors_in_bk_res as $value_element) {
                    $max_visitors_in_bk_res_summ += $value_element;
                }
                return $max_visitors_in_bk_res_summ;
            }
  // </editor-fold>

            

    // <editor-fold defaultstate="collapsed" desc="A d m i n   D A T E S    F u n c t i o n s">

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // A d m i n   D A T E S    F u n c t i o n s     ////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //
            //    Check at which child BK RES this Booking resource have to be  - SQL UPDATE resource
            //
            // Params: 'wpdev_booking_reupdate_bk_type_to_childs', $booking_id, $bktype, str_replace('|',',',$dates),  array($start_time, $end_time )
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //      TODO: 2. May be We need to set for each item (child resource) maximum support of visitors
            function reupdate_bk_type_to_childs($booking_id, $bktype, $dates, $start_end_time_arr , $formdata  ) {
                global $wpdb;

//TODO: DEBUG
//REMOVE TODAT DEBUG
//$booking_id = 8;
//$bktype =2;
//$dates = '15.02.2011, 16.02.2011, 17.02.2011';
//$start_end_time_arr = array( array('00','00','00') , array('00','00','00') );
//$formdata = 'text^name2^Dima2~text^secondname2^SDD~email^email2^email@server.com~text^address2^adress~text^city2^city~text^postcode2^post code~select-one^country2^GB~select-one^visitors2^3';
//debuge($booking_id, $bktype, $dates, $formdata );


                $bk_types =  $this->get_booking_types($bktype);                 // Get Hierarhy structure of BK Resource
                $bk_types =  $this->get_booking_types_hierarhy($bk_types);
                $max_available_items = 0;

                if (isset($bk_types[$bktype]))
                    if (isset($bk_types[$bktype]['count']))
                        $max_available_items = $bk_types[$bktype]['count']  ;           // Max Childs count

                $dates = explode(',',$dates);                                   // Dates
                //if (count($dates)==1)   // We have only 1 day and DIF Time, so duplicate this one day
                    //if ($start_end_time_arr[0]!=$start_end_time_arr[1])
                        //$dates[] = $dates[0]  ;
//debuge($bktype, $bk_types, $max_available_items);
//debuge($dates);

                $dates_new =  array();
                foreach ($dates as $d) {
                    $d1 = explode('.',trim($d));
                    $day =    $d1[0]  ;
                    $month =  $d1[1]  ;
                    $year =   $d1[2]  ;
                    $dates_new[] = intval($month) .'-'. intval($day) .'-'. intval($year)  ;// Month-Day-Year   Dates for normal using inside of this Funcion
                }
//REMOVE TODAT DEBUG debuge($dates_new);

                $my_page = 'client';                                            // Get a page
                if (        strpos($_SERVER['HTTP_REFERER'],'wpdev-booking.phpwpdev-booking-reservation') !== false )  $my_page = 'add';
                else if ( strpos($_SERVER['HTTP_REFERER'],'wpdev-booking.phpwpdev-booking')!==false)                   $my_page = 'booking';

            if ( strpos($_SERVER['HTTP_REFERER'],'parent_res') !== false  ) $my_page = 'client'; // admin add page and we add at parent res
            if ($my_page == 'add') if ( $this->check_if_bk_res_have_childs($bktype) ) { $my_page = 'client'; }
//REMOVE TODAT DEBUG debuge($my_page);

            if ( ($my_page == 'client')  ) // If client so go on

                if ($max_available_items > 1) {                                 // Make change only if we have some childs - capacity

                   $visitors_number = $this->get_num_visitors_from_form($formdata, $bktype);     // Get NUM Visitors from bk form, if use visitors num for availability is active else return false
                   sort($dates_new);                                            // Sort Days
                   $updated_type_id = $bktype;                                  // Bk TYPE ID
                   $bk_types        = $this->get_booking_types_hierarhy_linear($bk_types);        // Get linear array sorted by Priority

//REMOVE TODAT DEBUG
//debuge($visitors_number, $dates_new, $bk_types);
//die;
//REMOVE TODAT DEBUG
//debuge('O - Get Matrix for Bk Types');
                   // 0. Get Matrix for Bk Types with busy days for each types.
                   // Example: [ [TYPE_ID] => [  [DATE]=>BK_ID, [10-23-2010]=>22  ], [5] => .....  ]
                   /*     [1] => Array (
                                [1-12-2011] => 5
                                [1-13-2011] => 5
                                [1-14-2011] => 5
                                [1-17-2011] => 2
                                [1-18-2011] => 2 )
                          [6] => Array (
                                [1-13-2011] => 7
                                [1-14-2011] => 7
                                [1-15-2011] => 7
                                [1-18-2011] => 4
                                [1-19-2011] => 4 )
                   /**/
                   $bk_types_busy_dates_matrix = array();
                   foreach ($bk_types as $res_obj) {
                       $r_id = $res_obj['obj']->id;
                       $bk_types_busy_dates_matrix[$r_id] = $this->get_all_reserved_day_for_bk_type('all', $r_id, $booking_id );
                   }
//REMOVE TODAT DEBUG
//debuge('$bk_types_busy_dates_matrix',$bk_types_busy_dates_matrix);

// $dates_for_each_visitors - reduce the size of this archive based on visitors number of specific type


                    // 0. Create INIT arrays for visitors:
                    // Example: $is_this_visitor_setup  = [ [0]=false, [1]=false ...]
                    //          $dates_for_each_visitors= [ [0]=>['11-20-2010'=>false, '11-20-2010'=>false], [1]=>['11-20-2010'=>false, '11-20-2010'=>false]...]
                    $dates_for_each_visitors = array();
                    $is_this_visitor_setup = array();
                    for ($i = 0; $i < $visitors_number; $i++) {
                        $dates_for_each_visitors[$i]=array();
                        foreach ($dates_new as $selected_date) {
                            $dates_for_each_visitors[$i][$selected_date] = false;               // this date is not set up to some room (bk type)
                        }
                        $is_this_visitor_setup[$i]=false;                       // this visitor is not SETUP yet
                    }
//REMOVE TODAT DEBUG
//debuge( '$is_this_visitor_setup, $dates_for_each_visitors',  $is_this_visitor_setup, $dates_for_each_visitors);



                    // Get Max number of visitors for each booking type from linear array
                    // Exmaple: [bk_ID] => MAX_VISITORS
                    /*          [1] => 1
                                [6] => 1
                                [7] => 3
                                [8] => 2
                    /**/
                    $max_visitors_for_bk_types = array();
                    foreach ($bk_types as $value) {
                        if (isset($value['obj']->visitors))
                            $max_visitors_for_bk_types[  $value['obj']->id  ] = $value['obj']->visitors ;
                        else
                            $max_visitors_for_bk_types[  $value['obj']->id  ] = 1;
                    }
                    
//REMOVE TODAT DEBUG
//debuge('$max_visitors_for_bk_types',$max_visitors_for_bk_types);
//die;
//debuge('$bk_types_busy_dates_matrix',$bk_types_busy_dates_matrix);
                    // 1. Check availability of days in BK Resource, WITHOUT JUMPING ONE BOOKING TO DIF Resources
                    $vis_num = 0;                                                     // Visitor NUMber
                    foreach ($bk_types_busy_dates_matrix as $bk_type_id => $busy_dates) {       //Example: [ [5] => [  [DATE]=>BK_ID, [10-23-2010]=>22  ], .....  ]


                        if ($vis_num>= count($is_this_visitor_setup)) break;         //  All visitor -> Return
                        while ($is_this_visitor_setup[$vis_num] !== false) {          // Next visitor. This visitor is setuped.
                              $vis_num++;
                              if ($vis_num>= count($is_this_visitor_setup)) break;   //  All visitor -> Return
                        }
                        if ($vis_num>= count($is_this_visitor_setup)) break;         // All visitor -> Return



                        $is_some_dates_busy_in_this_type = false;               // Check all SELECTED dates  line inside of this TYPE (room)
                        foreach ( $dates_for_each_visitors[$vis_num] as $selected_date_for_visitor=>$is_day_setup ) {
                            if (isset($busy_dates[$selected_date_for_visitor]))  { $is_some_dates_busy_in_this_type = true ; break;  } // Some day  is busy, get next bk type
                        }

                        if ($is_some_dates_busy_in_this_type === false ) { // All days is FREE inside this type
                            $is_this_visitor_setup[$vis_num] = 1;                                 // This visitor is SETUPED
                            foreach ( $dates_for_each_visitors[$vis_num] as $selected_date_for_visitor=>$is_day_setup ) {
                                $dates_for_each_visitors[$vis_num][$selected_date_for_visitor] = $bk_type_id;

                                $bk_types_busy_dates_matrix[$bk_type_id][$selected_date_for_visitor] =  $booking_id; // MARK ALSO MATRIX
                            }

                            // Reduce the number of visitors based on visitor capacity for this booking resource (MAX VIS NUMBER)
                            $reduce_value_based_on_max_visitors = $max_visitors_for_bk_types[$bk_type_id] - 1 ;
                            for ($re = 0; $re < $reduce_value_based_on_max_visitors; $re++) {
                               // array_pop( $dates_for_each_visitors );          //decrese the number of visitors
                               // array_pop( $is_this_visitor_setup );
                                $vis_num++;                                                           // Next visitor
                                if ($vis_num>= count($is_this_visitor_setup)) break;
                                $is_this_visitor_setup[$vis_num] = 1;                                 // This visitor is SETUPED
                                $dates_for_each_visitors[$vis_num] = array();
                            }

                            $vis_num++;                                                           // Next visitor
                        }
                    }
//array_pop( $dates_for_each_visitors );
//REMOVE TODAT DEBUG
//debuge($is_this_visitor_setup, $dates_for_each_visitors, $bk_types_busy_dates_matrix);
//REMOVE TODAT DEBUG
//die;



                    // Continue Check availability of days in BK Resource, WITH JUMPING
                    // (  One visitor can be start in one resource then go to other resource )
                    while ($vis_num< count($is_this_visitor_setup)) {                    // Check if we proceed all visitors if not so go inside

                            if ($vis_num>= count($is_this_visitor_setup)) break;         // We are proceed all visitor, so return
                            while ($is_this_visitor_setup[$vis_num] !== false) {          // This visitor is setuped so get next one
                                  $vis_num++;
                                  if ($vis_num>= count($is_this_visitor_setup)) break;   // We are proceed all visitor, so return
                            }
                            if ($vis_num>= count($is_this_visitor_setup)) break;         // We are proceed all visitor, so return



                            foreach ( $dates_for_each_visitors[$vis_num] as $selected_date_for_visitor=>$is_day_setup ) {

                                foreach ($bk_types_busy_dates_matrix as $bk_type_id => $busy_dates) {  //Example: [ [5] => [  [DATE]=>BK_ID, [10-23-2010]=>22  ], .....  ]

                                    if (! isset($busy_dates[$selected_date_for_visitor]))  { // DATE is FREE in This Resource

                                        if (isset($dates_for_each_visitors[$vis_num][$selected_date_for_visitor])) {

                                            $dates_for_each_visitors[$vis_num][$selected_date_for_visitor] = $bk_type_id;        // Set Room for selected day of visitor
                                            $bk_types_busy_dates_matrix[$bk_type_id][$selected_date_for_visitor] = $booking_id;  // MARK MATRIX

                                        }

                                        // Reduce the number of visitors based on visitor capacity for this booking resource (MAX VIS NUMBER)
                                        $reduce_value_based_on_max_visitors = $max_visitors_for_bk_types[$bk_type_id] - 1 ;
                                        for ($re = 1; $re <= $reduce_value_based_on_max_visitors; $re++) {

                                            if ( isset($dates_for_each_visitors[ $vis_num + $re ]) )  // Check if this visitor is exist
                                                if ( isset( $dates_for_each_visitors[ $vis_num + $re ][$selected_date_for_visitor] ) ) {  // Check if this date is exist
                                                    //unset( $dates_for_each_visitors[ $vis_num + $re ][$selected_date_for_visitor] );     // Unset

                                                    if ( ($vis_num + $re) >= count($is_this_visitor_setup)) break;
                                                    $is_this_visitor_setup[$vis_num + $re] = 1;                                 // This visitor is SETUPED
                                                    $dates_for_each_visitors[$vis_num + $re] = array();

                                                }
                                        }


                                        break; // Get next date of visitor
                                    }

                                }
                            } // Process all days from visitor

                            $is_this_visitor_setup[$vis_num] = 1; // Mark this visitor as setuped and recheck below in loop this
                            foreach ( $dates_for_each_visitors[$vis_num] as $selected_date_for_visitor=>$is_day_setup ) {
                                if ($is_day_setup === false ) $is_this_visitor_setup[$vis_num] = false;
                            }

                            $vis_num++; // Get next visitor
                    }



                        ////////////////////////////////////////////////////////
                        // MAKE UPDATE OF    DB
                        ////////////////////////////////////////////////////////

                        // Get default bk Resource  - Type   (  first visitor, first day type)
                        foreach ($dates_for_each_visitors[0] as $value) { $updated_type_id=$value; break; }


                       //  Updated ID with NEW - UPDATE Booking TABLE    with new bk. res. type
                       if ( $updated_type_id != $bktype ) {

                            // Fix the booking form ID of elements /////////////////////////////////////////////////////////////////
                            $formdata_new = '';
                            $formdata_array = explode('~',$formdata);
                            $formdata_array_count = count($formdata_array);
                            for ( $i=0 ; $i < $formdata_array_count ; $i++) {
                                $elemnts = explode('^',$formdata_array[$i]);

                                $type = $elemnts[0];
                                $element_name = $elemnts[1];
                                $value = $elemnts[2];

                                $element_name = substr($element_name, 0, -1 * strlen($bktype) ) . $updated_type_id  ;  // Change bk RES. ID in elemnts of FORM

                                if ($formdata_new!='') $formdata_new.= '~';
                                $formdata_new .= $type . '^' . $element_name . '^' . $value;
                            } ////////////////////////////////////////////////////////////////////////////////////////////////

                            // Update
                            $update_sql = "UPDATE ".$wpdb->prefix ."booking AS bk SET bk.form='$formdata_new', bk.booking_type=$updated_type_id WHERE bk.booking_id=$booking_id;";
                            if ( false === $wpdb->query($wpdb->prepare( $update_sql ) ) ){
                                ?> <script type="text/javascript"> document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during updating exist booking type in BD',__FILE__,__LINE__ ); ?></div>'; </script> <?php
                                die();
                            }
                       }/////////////////////////////////////////////////////////////

//debuge('$updated_type_id , $bktype, $dates_for_each_visitors',$updated_type_id , $bktype, $dates_for_each_visitors);
                       // Update Dates:

                       // Firstly delete all dates, from Basic insert for future clean work
                       if ( false === $wpdb->query($wpdb->prepare( "DELETE FROM ".$wpdb->prefix ."bookingdates WHERE booking_id IN ($booking_id)") ) ){
                             ?> <script type="text/javascript"> document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during booking dates cleaning in BD',__FILE__,__LINE__ ); ?></div>'; </script> <?php
                            die();
                       }


                       // If we have situation with bookings in diferent resource so we are delete current booking and need to show error message. ////////
                       $booking_is_dissbale_booking_for_different_sub_resources = get_bk_option( 'booking_is_dissbale_booking_for_different_sub_resources');
                       if( $booking_is_dissbale_booking_for_different_sub_resources == 'On') {

                               $is_dates_inside_one_resource = true;            // We will recheck if all days inside of one resource, or there is exist some jumping.
                               foreach ($dates_for_each_visitors as $vis_num => $array_dates_for_each_visitors) {
                                   foreach ($array_dates_for_each_visitors as $k_day => $v_type_id) {
                                       $type_id_for_this_user = $v_type_id;
                                       break;
                                   }
                                   foreach ($array_dates_for_each_visitors as $k_day => $v_type_id) {
                                      if ($v_type_id != $type_id_for_this_user) {
                                          $is_dates_inside_one_resource = false;
    //debuge( '$updated_type_id, $k_day,  $v_type_id,  $is_dates_inside_one_resource', $updated_type_id, $k_day,  $v_type_id,  $is_dates_inside_one_resource)                                      ;
                                          break;
                                      }
                                   }
                               }
                               if ( ! $is_dates_inside_one_resource ) {
                                   if ( false === $wpdb->query($wpdb->prepare( "DELETE FROM ".$wpdb->prefix ."booking WHERE booking_id IN ($booking_id)") ) ){
                                         ?> <script type="text/javascript"> document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during booking dates cleaning in BD',__FILE__,__LINE__ ); ?></div>'; </script> <?php
                                        die();
                                   }
                                   echo ' ';
                                   ?> <script type="text/javascript">
                                             if (type_of_thank_you_message == 'page') {      // Page
                       //                         thank_you_page_URL = window.location.href;
                       //                         location.href= thank_you_page_URL;
                                                  clearTimeout(timeoutID_of_thank_you_page)
                                             }
                                             document.getElementById('paypalbooking_form<?php echo $bktype; ?>').style.display='none';
                                             document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php printf(__('Sorry, the booking is not done, because these days are already booked, please %srefresh%s the page and try other days.' ) ,'<a href="javascript:location.reload();">','</a>'); ?></div>';
                                        </script>
                                   <?php
                                   exit;
                               }
                       }
                       
                       // Now insert all new dates
                       $insert='';
                       $start_time = $start_end_time_arr[0];
                       $end_time   = $start_end_time_arr[1];
                        $is_approved_dates = '0';
                        $auto_approve_new_bookings_is_active       =  get_bk_option( 'booking_auto_approve_new_bookings_is_active' );
                        if ( trim($auto_approve_new_bookings_is_active) == 'On')
                            $is_approved_dates = '1';

//debuge($dates_for_each_visitors);
                       foreach ($dates_for_each_visitors as $vis_num => $value_dates) {

                           // We have selection only one day and times is diferent
                           if ( ( count($value_dates)==1 ) && ( $start_time != $end_time ) ) $value_dates[]='previos_day';


                           $i=0;
                           foreach ($value_dates as $my_date_init => $bk_type_for_date_init) { $i++;

                                if ($bk_type_for_date_init != 'previos_day' ) {              // Checking for one day selection situation
                                    $my_date          = $my_date_init;
                                    $my_date = explode('-',$my_date);
                                    $bk_type_for_date = $bk_type_for_date_init;
                                }

                                if ( get_bk_option( 'booking_recurrent_time' ) !== 'On') {

                                    if ($i == 1) {
                                        $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[2], $my_date[0], $my_date[1], $start_time[0], $start_time[1], $start_time[2] );
                                    }elseif ($i == count($value_dates)) {
                                        $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[2], $my_date[0], $my_date[1], $end_time[0], $end_time[1], $end_time[2] );
                                    }else {
                                        $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[2], $my_date[0], $my_date[1], '00', '00', '00' );
                                    }

                                    if ( !empty($insert) ) $insert .= ', ';
                                    if ($bk_type_for_date !== $updated_type_id) $insert .= "('$booking_id', '$date', '$is_approved_dates', '$bk_type_for_date')";
                                    else                                        $insert .= "('$booking_id', '$date', '$is_approved_dates', NULL)";

                                } else {

                                    //if ($my_date_previos  == $my_date) continue; // escape for single day selections.
                                    $my_date_previos = $my_date;
                                    
                                    $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[2], $my_date[0], $my_date[1], $start_time[0], $start_time[1], $start_time[2] );
                                    $my_dates4emeil .= $date . ',';
                                    if ( !empty($insert) ) $insert .= ', ';
                                    if ($bk_type_for_date !== $updated_type_id) $insert .= "('$booking_id', '$date', '$is_approved_dates', '$bk_type_for_date')";
                                    else                                        $insert .= "('$booking_id', '$date', '$is_approved_dates', NULL)";

                                    $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[2], $my_date[0], $my_date[1], $end_time[0], $end_time[1], $end_time[2] );
                                    $my_dates4emeil .= $date . ',';
                                    if ( !empty($insert) ) $insert .= ', ';
                                    if ($bk_type_for_date !== $updated_type_id) $insert .= "('$booking_id', '$date', '$is_approved_dates', '$bk_type_for_date')";
                                    else                                        $insert .= "('$booking_id', '$date', '$is_approved_dates', NULL)";

                                }

                           }
                       }
//debuge($insert);
//die;
                       if ( !empty($insert) )
                            if ( false === $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix ."bookingdates (booking_id, booking_date, approved, type_id) VALUES " . $insert) ) ){
                                ?> <script type="text/javascript"> document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during inserting into BD - Dates' ,__FILE__,__LINE__); ?></div>'; </script> <?php
                                die();
                            }

                 } // end if of $max_available_items > 1

            }

                    // Get Array with busy dates of BK Resource with values as bk_IDs  [9-21-2010] => bk_id, ...
                    function get_all_reserved_day_for_bk_type($approved = 'all', $bk_type = 1, $skip_booking_id = '') {
                       global $wpdb;
                        $dates_array = $time_array = array();

                        // Get all reserved dates for $bk_type, including childs But skiped this booking: $skip_booking_id
                        $sql_req = $this->get_sql_bk_dates_for_all_resources('', $bk_type, $approved, $skip_booking_id) ;
                        $dates_approve   = $wpdb->get_results($wpdb->prepare( $sql_req ));

                        // Get Array with MAX available days
                        // $available_dates = $this->get_bk_dates_for_all_resources( $dates_approve, $approved, 1, $bk_type) ;
                        $return_dates = array();

                        foreach ($dates_approve as $my_date) {
                            if (  ($my_date->date_res_type == $bk_type) || ( is_null($my_date->date_res_type) )   ){ // Dates belong only to this BK Res (type)
                                $my_dat = explode(' ',$my_date->booking_date);
                                //if (substr($my_dat[1],-2) == '01') continue; //Bence customization
                                //if (substr($my_dat[1],-2) == '02') continue; //Bence customization

                                $my_dt = explode('-',$my_dat[0]);
                                $my_key =  $my_dt[0].'-'.$my_dt[1].'-'.$my_dt[2] ;
                                $my_key_new =  ($my_dt[1]+0).'-'.($my_dt[2]+0).'-'.($my_dt[0]+0) ;

                                $return_dates[$my_key_new]  =  $my_date->booking_id;// $available_dates[$my_key]['max'];
                            }
                        }
                        // TODO, later booking ID have to be NUM of availabe seats at this type
                        return $return_dates;       // Return array each KEY - its Day, Value - booking ID
                    }


            // Get UnAvailable days (availability == 0) from - $dates_approve and return only them for client side
            // OR  return availability array (MAX available items array) if $is_return_available_days_array = 1 at client side page
            function get_bk_dates_for_all_resources($dates_approve, $approved, $is_return_available_days_array = 0, $bk_type = 1) {  //return $dates_approve;

                if (count($dates_approve) == 0 )  return array();               // If emty so then return empty


                $max_available_items = $this->get_max_available_items_for_resource($bk_type);   // Get MAX aavailable Number
                
                $my_page = 'client';                                            // Get a page
                if (        strpos($_SERVER['REQUEST_URI'],'wpdev-booking.phpwpdev-booking-reservation') !== false )  $my_page = 'add';
                else if ( strpos($_SERVER['REQUEST_URI'],'wpdev-booking.phpwpdev-booking')!==false)                   $my_page = 'booking';


                if  ( ( $my_page == 'add' ) && ( isset($_GET['parent_res'])) ) $my_page = 'client';

                // If NOT Client page so then return this dates
                if (
                        ( $my_page == 'booking' ) ||
                        ( ( $my_page == 'add' ) && (! isset($_GET['parent_res'])) )
                   ) {
                    return $dates_approve;
                   }


                

                $available_dates = array();
                $return_dates = array();

                
                
                // check correct sort of dates with times: /////////////////////
                // For exmaple if we have 2 bookings for same date at [1]09:00-10:00 and [2]10:00-11:00 the sort we will have:
                // [1]09:00 , [2]10:00 , [1]10:00 , [2]11:00 
                // but need to have
                // [1]09:00 , [1]10:00 , [2]10:00 , [2]11:00 
                ////////////////////////////////////////////////////////////////
//debuge($dates_approve);
                $dates_correct_sort = array();
                foreach ($dates_approve as $my_date) {

                    $formated_date = $my_date->booking_date;                                            // Nice Time & Date
                    $formated_date = explode(' ',$formated_date);
                    $curr_nice_date = $formated_date[0];
                    $curr_nice_time = $formated_date[1];
                    $formated_date[0] = explode('-',$formated_date[0]);
                    $formated_date[1] = explode(':',$formated_date[1]);

                    //if ($my_page == 'client') if ($formated_date[1][2] == '2')  continue;  //Bence customization
                    //if ($my_page == 'client') if ($formated_date[1][2] == '1')  continue;  //Bence customization
                    
//debuge($formated_date[1][2]);
                    
                    if ( empty($my_date->date_res_type) )   $curr_bk_type = $my_date->type;             // Nice Type
                    else                                    $curr_bk_type = $my_date->date_res_type;
                    
                    $curr_bk_id = $my_date->booking_id;                                                 //Nice bk ID


                    if (! isset($dates_correct_sort[ $curr_bk_type ]))          // Type
                        $dates_correct_sort[ $curr_bk_type ] = array();

                    if (! isset($dates_correct_sort[ $curr_bk_type ][ $curr_nice_date ]))          // Date
                        $dates_correct_sort[ $curr_bk_type ][ $curr_nice_date ] = array();

                    if (! isset($dates_correct_sort[ $curr_bk_type ][ $curr_nice_date ][ $curr_bk_id ]))          // ID
                        $dates_correct_sort[ $curr_bk_type ][ $curr_nice_date ][ $curr_bk_id ] = array();

                    $dates_correct_sort[ $curr_bk_type ][ $curr_nice_date ][ $curr_bk_id ][ $curr_nice_time ] = $my_date;    // Time
                }


                // Change ID key to Time key
                foreach ($dates_correct_sort as $k_type=>$bt_value) {
                    foreach ($bt_value as $k_date=>$bd_value) {

                        foreach ($bd_value as $k_id=>$bid_value) {
                            ksort($dates_correct_sort[ $k_type ][ $k_date ][ $k_id ]);  // Sort time inside of single booking
                            foreach ($bid_value as $k_start_time => $date_finish_value) {
                                $dates_correct_sort[ $k_type ][ $k_date ][ $k_start_time ] = $dates_correct_sort[ $k_type ][ $k_date ][ $k_id ];
                                unset($dates_correct_sort[ $k_type ][ $k_date ][ $k_id ]);
                                break;
                            }
                        }
                      ksort($dates_correct_sort[ $k_type ][ $k_date ]);         // Sort inside of date by time
                    }
                }


                // Compress to linear array
                $linear_dates_array = array();
                foreach ($dates_correct_sort as $bt_value) {
                    foreach ($bt_value as  $bd_value) {
                        foreach ($bd_value as $bstarttime_value) {
                            foreach ($bstarttime_value as $bstart_end_time_value) {
                                $linear_dates_array[] = $bstart_end_time_value;
                            }
                        }
                    }
                }
                $dates_approve = $linear_dates_array;

//debuge($dates_approve, $dates_correct_sort);
                if ($max_available_items == 1) {

                     $booking_id_arr = array();
                     foreach ($dates_approve as $my_date) {

                            if ($my_date->approved == $approved) {
                                $booking_id_arr[]=$my_date->booking_id;
                                array_push($return_dates, $my_date);
                            }
                     }
//debuge($return_dates);
                    return $return_dates;
                }
                



                // Get max available items for specific date.
                // $max_available_items

//debuge($max_available_items);

                // Sort all bookings by dates
                $bookings_in_dates = array();
                foreach ($dates_approve as $my_date) {
                    $formated_date = $my_date->booking_date;                                            // Nice Time & Date
                    $formated_date = explode(' ',$formated_date);
                    $curr_nice_date = $formated_date[0];
                    $curr_nice_time = $formated_date[1];
                    $formated_date[0] = explode('-',$formated_date[0]);
                    $formated_date[1] = explode(':',$formated_date[1]);

                    if (! isset($bookings_in_dates[ $curr_nice_date ])) $bookings_in_dates[ $curr_nice_date ] = array();
                    if (! isset($bookings_in_dates[ $curr_nice_date ][ 'id' ])) $bookings_in_dates[ $curr_nice_date ][ 'id' ] = array();

                    if (! isset($bookings_in_dates[ $curr_nice_date ][ 'id' ][ $my_date->booking_id ]))
                        $bookings_in_dates[ $curr_nice_date ][ 'id' ][ $my_date->booking_id ] = array();

                    $bookings_in_dates[ $curr_nice_date ][ 'id' ][ $my_date->booking_id ][] = $curr_nice_time;
                    
                }

//debuge($bookings_in_dates);
                // check time intersections

                // Set for dates $available_dates -> MAX number of available ITEMS per day inside of loop
                foreach ($dates_approve as $my_date) {
                            
                            // Date KEY ////////////////////////////////////
                            $my_dat = explode(' ',$my_date->booking_date);
                            $my_dt = explode('-',$my_dat[0]);
                            $my_tm = explode(':',$my_dat[1]);
                            $my_key =  $my_dt[0].'-'.$my_dt[1].'-'.$my_dt[2] ;

                            // GET AVAILABLE DAYS ARRAY ////////////////////
                            if ( isset($available_dates[$my_key]) )  {          // Get all booked days in array and add id and last id (its will show)

                                if ( ! in_array($my_date->booking_id, $available_dates[$my_key]['id']) ) {

                                    $available_dates[$my_key]['max']--;

                                    array_push( $available_dates[$my_key]['id'], $my_date->booking_id);
                                    $available_dates[$my_key]['last_id'] = $my_date->booking_id;
                                    $available_dates[$my_key]['approved'] += $my_date->approved;

                                } elseif ( ($my_date->date_res_type > 0 ) && ($my_date->type !== $my_date->date_res_type ) ) {
                                    $available_dates[$my_key]['max']--;
                                }

                            } else {
                                $my_max_show = $max_available_items - 1;                                
                                $available_dates[$my_key] = array('id' => array($my_date->booking_id), 'max' => $my_max_show, 'last_id' => $my_date->booking_id, 'approved' => $my_date->approved);
                            }
 
                 }  // Date loop

//debuge($available_dates);

                // If need just return Array with MAX available ITEMS per day so then return it
                if ( $is_return_available_days_array == 1) {return $available_dates;}

                // Get Unavailable days and return them
                foreach ($dates_approve as $my_date) {
                    $my_dat = explode(' ',$my_date->booking_date);
                    $my_dt = explode('-',$my_dat[0]);
                    $my_key =  $my_dt[0].'-'.$my_dt[1].'-'.$my_dt[2] ;

                    // Get Unavailable days, based on MAX availability
                    if (( $available_dates[$my_key]['max'] <= 0 ) && ($available_dates[$my_key]['last_id'] == $my_date->booking_id )) {
                        if ($available_dates[$my_key]['approved'] > 0 ) $available_dates[$my_key]['approved'] = 1;
                        if ($approved == $available_dates[$my_key]['approved'] )
                            array_push($return_dates, $my_date);
                    }
                }
//die;
//debuge($return_dates);
                return $return_dates;
            }


            // S Q L    Modify SQL request according Dates - Get rows, from resource of childs and other dates, which partly belong to bk_type
            function get_sql_bk_dates_for_all_resources($mysql, $bk_type, $approved, $skip_booking_id = '' ) {
                 global $wpdb;
                 $my_page = 'client';
                 if (        strpos($_SERVER['REQUEST_URI'],'wpdev-booking.phpwpdev-booking-reservation') !== false ) {
                     $my_page = 'add';
                 } else if ( strpos($_SERVER['REQUEST_URI'],'wpdev-booking.phpwpdev-booking')!==false) {
                     $my_page = 'booking';
                 }


                 if  ( ( $my_page == 'add' ) && ( isset($_GET['parent_res'])) ) $my_page = 'client';


                if (  (isset($_GET['booking_hash'])) ||  ($skip_booking_id != '')   ){

                    if (($skip_booking_id != '')) { $my_booking_id = $skip_booking_id;
                    } else {
                        $my_booking_id_type = apply_bk_filter('wpdev_booking_get_hash_to_id',false, $_GET['booking_hash'] );
                        if ($my_booking_id_type !== false)  $my_booking_id = $my_booking_id_type[0];
                    }
                    $skip_bookings = ' AND bk.booking_id <>' .$my_booking_id . ' ';

                } else { $skip_bookings = ''; }

                $my_approve_rule = '';
                if ( ($my_page == 'booking') || ( $my_page=='add') )            // For client side this checking DISABLE coloring of dates in CAPACITY DATES
                    if ($approved == 'all') $my_approve_rule = '';              // Otherwize, if booking will approved it will not calculate those days, and during availability = 0 , the days will be possible to book and this is WRONG
                    else                    $my_approve_rule = 'dt.approved = '.$approved.' AND ';


                $sql_req = "SELECT DISTINCT dt.booking_date, dt.type_id as date_res_type, dt.booking_id, dt.approved, bk.form, bt.parent, bt.prioritet, bt.booking_type_id as type
                            FROM ".$wpdb->prefix ."bookingdates as dt
                                     INNER JOIN ".$wpdb->prefix ."booking as bk
                                     ON    bk.booking_id = dt.booking_id
                                             INNER JOIN ".$wpdb->prefix ."bookingtypes as bt
                                             ON    bk.booking_type = bt.booking_type_id

                         WHERE ".$my_approve_rule." dt.booking_date >= CURDATE() AND
                                                  (      bk.booking_type IN ($bk_type) ";   // All bookings from PARENT TYPE
                if (($skip_bookings == '') && ($my_page =='client'))
                    $sql_req .=                           "OR bt.parent  IN ($bk_type) ";   // Bookings from CHILD Type
                $sql_req .=                               "OR dt.type_id IN ($bk_type) ";   // Bk. Dates from OTHER TYPEs, which belong to This TYPE
                $sql_req .=                        ") "
                         .$skip_bookings."
                         ORDER BY dt.booking_date" ;
//debuge($sql_req);
                return $sql_req;

              }

            // Booking Table Admin Page -- Show also bookins, where SOME dates belong to this Type
            // S Q L    Modification for Admin Panel dates:  (situation, when some bookings dates exist at several resources ) - Booking Tables
            function get_sql_4_dates_from_other_types($blank_sql  , $bk_type, $approved ){
                global $wpdb;
                $sql = " OR  bk.booking_id IN ( SELECT DISTINCT booking_id FROM ".$wpdb->prefix ."bookingdates as dtt WHERE  dtt.approved IN ( $approved ) AND dtt.type_id = ". $bk_type .") ";

                $where = '';
                $where = apply_bk_filter('multiuser_modify_SQL_for_current_user', $where);
                if ($where !='' ) {
                    //$sql = $sql . ' AND ' . $where;
                }

                return $sql;
            }

    // </editor-fold>


            

    // <editor-fold defaultstate="collapsed" desc=" A d m i n   B O O K I N G   P a g e ">

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // A d m i n   B O O K I N G   P a g e     ////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            // at Admin panel, show RESOURCE links nearly Dates    // $bk['type_id'][$ad], $bk['booking_type'], $bk_type
            function show_diferent_bk_resource_of_this_date( $bk_type_id_of_date, $current_booking_type, $bk_type_original , $type_items, $outColorClass ){
                //debuge($bk_type_id_of_date, $current_booking_type, $bk_type_original , $type_items, $outColorClass);//die;
                if (
                     (! empty($bk_type_id_of_date)) ||
                     ( (  $current_booking_type !== $bk_type_original ) && ($bk_type_original>0) )
                   ) {

                    if  ( ! (  (! empty($bk_type_id_of_date)) &&  ($bk_type_id_of_date == $bk_type_original) ) )

                        if (! empty($bk_type_id_of_date))
                            echo '<a href="admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME . 'wpdev-booking'.'&booking_type='.$bk_type_id_of_date.'"
                                class="bktypetitle0  bk_res_link_from_dates booking_overmause'. $outColorClass.'"  >' . $type_items[ $bk_type_id_of_date ]  . '</a>';
                        else
                            echo '<a href="admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME . 'wpdev-booking'.'&booking_type='.$current_booking_type.'"
                                class="bktypetitle0  bk_res_link_from_dates booking_overmause'. $outColorClass.'"  >' . $type_items[ $current_booking_type ]  . '</a>';

                   }
            }

            // Show subtimes - CHILDS from parent BK Resource at Admin panel.
            function wpdev_show_subtypes_selection($default_id, $is_edit ){
                 if ($default_id <= 0 ) return;
                 global $wpdb;

                 $where_multiuser = '';
                 $where_multiuser = apply_bk_filter('multiuser_modify_SQL_for_current_user', $where_multiuser);
                 if ($where_multiuser!='') $where_multiuser = ' AND ' . $where_multiuser;

                 $additional_fields = '';
                 if ( class_exists('wpdev_bk_multiuser')) {  // If Business Large then get resources from that
                //    $additional_fields = ', users ';
                 }
                 $mysql=  "SELECT booking_type_id as id, title, parent, prioritet ".$additional_fields."  FROM ".$wpdb->prefix ."bookingtypes WHERE ( parent=".$default_id." OR booking_type_id=".$default_id." ) ".$where_multiuser."  ORDER BY prioritet";

                 $types_list = $wpdb->get_results($wpdb->prepare( $mysql ));

                 if (count($types_list) == 1) {
                     if ($types_list[0]->parent != 0) {
                         $mysql=  "SELECT booking_type_id as id, title, parent, prioritet  FROM ".$wpdb->prefix ."bookingtypes WHERE parent=".$types_list[0]->parent." OR booking_type_id=".$types_list[0]->parent."  ORDER BY prioritet";
                         $types_list = $wpdb->get_results($wpdb->prepare( $mysql ));
                     }
                 }

                 if (count($types_list) > 1) {
                      ?>  <div id="childs_bk_resources"> <?php  
                      foreach ($types_list as $bk_type ) {


                             

                            $selected_bk_typenew = '';


                            if ( isset($_GET['booking_type']) ) {
                                       if ($_GET['booking_type'] == $bk_type->id) $selected_bk_typenew = ' selected_bk_typenew ';
                                       if (($_GET['booking_type'] == '') && ($default_id == $bk_type->id)) $selected_bk_typenew = ' selected_bk_typenew ';
                            } else {   if ($default_id == $bk_type->id)           $selected_bk_typenew = ' selected_bk_typenew '; }

                            if ( isset($_GET['parent_res']) ) if ( $_GET['parent_res'] == 1 ) $selected_bk_typenew = '';

                            if ( $selected_bk_typenew == ' selected_bk_typenew ' )  $selected_title = $bk_type->title;

                         //$this->echoResourceMenuItem($bk_type,$selected_bk_typenew, true );
                         make_bk_action('echoResourceMenuItem', $bk_type,$selected_bk_typenew, $is_edit  );
                         //                  debuge($types_list);
                    }

                    ?>
                          <div id="child_objects_legend" style="float:right;margin-right:5px;" > <span style="font-size:18px;line-height:15px;"> &larr; </span><?php _e('Child booking resources', 'wpdev-booking'); ?></div>
                          <script type="text/javascript">jQuery('#child_objects_legend').animate({opacity:1},10000).fadeOut(2000);</script>
                      </div><div class="clear topmenuitemseparatorv" style="height:0px;clear:both;" ></div> <?php
                 }
            }

            // Show types with max counts of items for this types
            function showing_capacity_of_bk_res_in_top_line($title, $bk_type, $count){
                return ' <span class="bktypecount"><a  href="admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-option&tab=resources" title="'.__('Max available items inside of booking resource','wpdev-booking').'" class="" >' . $count . '</a></span>'
                         . '<style type="text/css"> #parents_bk_resources #bktype'.$bk_type->id.' {padding:3px 0px 3px 4px;}</style>' ;
            }


            //Show booking page for PARENT booking resource
            function show_all_bookings_for_parent_resource($parent_id){

                // Get all exist booking resources into array in format: stdClass Object(   [id] => 13, [title] => test, [parent] => 0, [prioritet] => 0, [cost] => 115, [visitors] => 1, [users] => 1 )
                $bk_resources = $this->get_booking_types();

//debuge('$bk_resources, $parent_id',$parent_id);

                $resource_list_sort_by_priority = array();

                foreach ($bk_resources as $value) {
                    if (  ($value->id != $parent_id) && ($value->parent != $parent_id) )  continue;         // Skip this resource
                                                                                // Sort booking resources by priority
                    if (($value->id == $parent_id)) {
                        if (! isset($resource_list_sort_by_priority[ 0 ]))
                             $resource_list_sort_by_priority[ 0 ]   = array($value);
                        else $resource_list_sort_by_priority[ 0 ]   = array_merge( array($value) , $resource_list_sort_by_priority[ 0 ] ) ;
                    } else {
                        if (! isset($resource_list_sort_by_priority[ $value->prioritet ]))
                             $resource_list_sort_by_priority[ $value->prioritet ]   = array($value);
                        else $resource_list_sort_by_priority[ $value->prioritet ][] = $value;
                    }
                }
                $resource_list_sorted_by_priority = array();                    // Get final sorted by priority array of resources
                foreach ($resource_list_sort_by_priority as $value) {
                    foreach ($value as $v) {
                        $resource_list_sorted_by_priority[]=$v;
                    }
                }

//debuge($resource_list_sorted_by_priority);

                ?>
              <table style="width:100%;margin:10px 0px;border:0px solid #ccc;" cellpadding="0" cellspacing="0">
                  <tr>
                      <td style="width:100px;"></td>
                      <td>
                          <table style="width:100%;margin:10px 0px 0px;" cellpadding="0" cellspacing="0">
                              <tr>
                                  <th colspan="1" style="font-size:24px;font-weight: normal;text-shadow:0px 1px 2px #bbb;padding:10px;height:30px;">
                                      &lt;&lt;
                                  </th>
                                  <th colspan="29" style="font-size:26px;font-weight: normal;text-shadow:0px 1px 2px #bbb;padding:10px;">
                                      September, 2011
                                  </th>
                                  <th colspan="1"style="font-size:24px;font-weight: normal;text-shadow:0px 1px 2px #bbb;padding:10px;">
                                      &gt;&gt;
                                  </th>
                              </tr>
                              <tr>
                                  <?php for ($i = 1; $i < 32; $i++) { ?>
                                  <td style="background: #eee;border:1px solid #ccc; text-align: center; padding:2px;width:3.22%;    font-size:22px;font-weight: bold;text-shadow:0px 1px 2px #bbb;padding:5px 5px 7px;" >
                                      <span style="font-size:12px;"><?php echo 'Su'; ?></span>
                                      <br/> <?php echo $i; ?>
                                      
                                      
                                  </td>
                                  <?php } ?>
                              </tr>
                          </table>
                      </td>
                  </tr>
                  <?php foreach ($resource_list_sorted_by_priority as $value) { ?>
                  <tr>
                      <th><?php echo $value->title; ?></th>

                      <td>

                          <table style="width:100%;margin:0px;" cellpadding="0" cellspacing="0">
                              <tr>
                                  <?php for ($i = 1; $i < 32; $i++) { ?>
                                  <td style="border:1px solid #ccc; text-align: center; padding:2px;width:3.22%;padding:5px 5px 7px;" >
                                      <?php if ( rand(0,1) ) echo ' '; else echo 'X'; ?>
                                  </td>
                                  <?php } ?>
                              </tr>
                          </table>

                      </td>
                  </tr>
                  <?php } ?>
              </table>
                <?php
            }


            function check_if_bk_res_have_childs($bk_type_id) {
                if ($bk_type_id<1) return false;
                global $wpdb;
                $mysql=  "SELECT booking_type_id as id, prioritet  FROM ".$wpdb->prefix ."bookingtypes WHERE ( parent=".$bk_type_id." )  ORDER BY prioritet";
                $types_list = $wpdb->get_results($wpdb->prepare( $mysql ));
                if (count($types_list)>0)  return count($types_list);
                else return false;
            }
            // check if this resource Parent and have some childs, so then assign to $_GET['parent_res'] = 1
            function check_if_bk_res_parent_with_childs_set_parent_res($bk_type_id) {
                
                if ( $this->check_if_bk_res_have_childs($bk_type_id) ) {
                    $_GET['parent_res'] = 1;
                }
            }
     // </editor-fold>
            

    // <editor-fold defaultstate="collapsed" desc=" A d m i n   S E T T I N G S    M E N U ">

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // A d m i n   S E T T I N G S    M E N U     ////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
          function settings_menu_content() {
$is_can = apply_bk_filter('multiuser_is_user_can_be_here', true, 'not_low_level_user'); //Anxo customizarion
if (! $is_can) return; //Anxo customizarion

              switch ($_GET['tab']) {

                  case 'resources': return;
                      ?> <div id="ajax_working" class="clear" style="height:0px;"></div>
                               <div id="poststuff" class="metabox-holder"> <?php
                      $is_can = apply_bk_filter('multiuser_is_user_can_be_here', true, 'only_super_admin');
                      if ($is_can) $this->show_resources_advanced_settings(true);         // Make only UPDATE

                      $this->show_resources_settings_page();
                      
                      if ($is_can) $this->show_resources_advanced_settings();

                      ?> </div> <?php
                      return false;
                  case 'search':
                      ?> <div id="ajax_working" class="clear" style="height:0px;"></div>
                         <div id="poststuff" class="metabox-holder"> <?php
                      $is_can = apply_bk_filter('multiuser_is_user_can_be_here', true, 'only_super_admin');
                      if ($is_can) {
                          $this->show_search_settings();
                          $this->show_search_cache_settings();
                      }
                      ?> </div> <?php
                      return false;
                  default:
                      return true;
                      break;
              }

          }

                  // Show Settings pahe for resources
                  function show_resources_settings_page() {

                      global $wpdb;
                      $is_use_visitors_number_for_availability   = get_bk_option( 'booking_is_use_visitors_number_for_availability');

                      if ( ( isset( $_POST['submit_resources'] ) )  ||  (isset($_POST['submit_resources_button']))  ) {

                          if ($_POST['type_title_new'] != '') { // Insert

                              $cost = $this->get_booking_types( $_POST['type_parent_new'] ) ;
                              if (count($cost)>0) $cost = $cost[0]->cost;
                              else $cost = '0';

                               $useres_title = '';
                               $users_values = '';
                               if ( class_exists('wpdev_bk_multiuser')) {  // If Business Large then get resources from that
                                    $useres_title = ', users';
                                    $user = wp_get_current_user();
                                    $u_id = $user->ID;
                                    $users_values = ', ' . $u_id;
                               }


                              if ( false === $wpdb->query($wpdb->prepare(
                                      'INSERT INTO '.$wpdb->prefix .'bookingtypes ( title, parent, cost, prioritet '.$useres_title.' ) VALUES ( "'.
                                      $_POST['type_title_new'] .'", "' .
                                      $_POST['type_parent_new'] .'", "'.
                                      $cost .'", "'.
                                      $_POST['type_prioritet_new'] . '" '. $users_values .') ' ) ) ){
                                  bk_error('Error during updating to DB booking resources' ,__FILE__,__LINE__);
                              } else {
                                  if (isset($_POST['type_max_visitors' . $_POST['type_parent_new'] ])) {
                                      $booking_id = (int) $wpdb->insert_id;       //Get ID
                                      $booking_visitor_num = $_POST['type_max_visitors' . $_POST['type_parent_new'] ] ;
                                      if ( false === $wpdb->query($wpdb->prepare(
                                              "UPDATE ".$wpdb->prefix ."bookingtypes SET visitors = '". $booking_visitor_num ."' WHERE booking_type_id = " .  $booking_id) ) ){
                                          bk_error('Error during updating to DB booking resources' ,__FILE__,__LINE__);
                                      }
                                  }
                              }


                          } else {

                              $bk_types = $this->get_booking_types();
                              $is_deleted = false;

                              foreach ($bk_types as $bt) { // Delete
                                  if (isset($_POST['type_delete'.$bt->id])) {
                                      $is_deleted = true;
                                      $delete_sql = "DELETE FROM ".$wpdb->prefix ."bookingtypes WHERE booking_type_id IN (".$bt->id.")";
                                      if ( false === $wpdb->query($wpdb->prepare($delete_sql) ) ){
                                          bk_error('Error during deleting booking resources',__FILE__,__LINE__ );
                                      }
                                  }
                              }

                              if ($is_deleted == false)
                                  foreach ($bk_types as $bt) { // Update

                                      if ($is_use_visitors_number_for_availability == 'On') {

                                          if ( $_POST['type_parent'.$bt->id] != 0 )     // Set for Child objects, value of Parent objects
                                              $vis_update_string = " , visitors = '". $_POST['type_max_visitors'. $_POST['type_parent'.$bt->id] ] ."' " ;
                                          else                                          // Set for Parent objects - normal value
                                              $vis_update_string = " , visitors = '".$_POST['type_max_visitors'.$bt->id] ."' " ;

                                      } else  $vis_update_string = '';

                                      if ( false === $wpdb->query($wpdb->prepare(
                                              "UPDATE ".$wpdb->prefix ."bookingtypes SET title = '".$_POST['type_title'.$bt->id]."', parent = '".$_POST['type_parent'.$bt->id]."' , prioritet = '".$_POST['type_prioritet'.$bt->id]."' ".$vis_update_string."  WHERE booking_type_id = " .  $bt->id) ) ){
                                          bk_error('Error during updating to DB booking resources' ,__FILE__,__LINE__);
                                      }

                                  }
                          }

                      }
                        
                      ?>
                                        <div class='meta-box'>
                                          <div <?php $my_close_open_win_id = 'bk_settings_resource_management'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');"><br></div>
                                                <h3 class='hndle'><span><?php _e('Booking resources managment', 'wpdev-booking'); ?></span></h3> <div class="inside">

                                            <form  name="post_option_resources" action="" method="post" id="post_option_resources" >
                                                <table style="width:100%;" class="resource_table0 booking_table" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <th style="width:10px;height:35px;"> <?php _e('ID', 'wpdev-booking'); ?> </th>

                                                        <th style="width:220px;height:35px;"> <?php _e('Resource name', 'wpdev-booking'); ?> </th>

                                                        <th style="width:50px; " rel="tooltip" class="tooltip_bottom"  title="<?php _e('Number of resource items inside of parent resource', 'wpdev-booking');?>"> <?php _e('Capacity', 'wpdev-booking'); ?>  </th>
                                                        <th style="width:100px;text-align: center; "> <?php _e('Parent', 'wpdev-booking');   ?>  </th>
                                                        <th style="width:50px; "> <?php _e('Priority', 'wpdev-booking'); ?> </th>
                                                        <?php if ($is_use_visitors_number_for_availability == 'On') { ?>
                                                        <th style="width:50px;white-space: nowrap; " rel="tooltip" class="tooltip_bottom"  title="<?php _e('Maximum number of visitors for resource', 'wpdev-booking');?>"> <?php _e('Max', 'wpdev-booking'); echo ' '; _e('visitors', 'wpdev-booking'); ?> </th>
                                                        <?php } ?>                                                                                                                
                                                        <th style="text-align: center;"> <?php _e('Actions', 'wpdev-booking'); ?> </th>
                                                        <?php make_bk_action('show_users_header_at_settings' ); ?>
                                                    </tr>



                      <?php
                      $alternative_color = '0';
                      $bk_types =  $this->get_booking_types();
                      $all_id = array(array('id'=>0,'title'=>' - '));
                      foreach ($bk_types as $bt) {
                          if ($bt->parent==0)
                              $all_id[] = array('id'=>$bt->id, 'title'=> $bt->title);
                      }
                      $bk_types =  $this->get_booking_types_hierarhy($bk_types);
                      $bk_types =  $this->get_booking_types_hierarhy_linear($bk_types);

                      $link = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-option&tab=resources';

                      foreach ($bk_types as $bt) {

                          $my_count = $bt['count'];
                          $bt = $bt['obj'];
                          if ( $alternative_color == '')  $alternative_color = ' class="alternative_color" ';
                          else $alternative_color = '';
                          ?>
                                                        <tr>
                                                            <th style="font-size:11px;border-top: 1px solid #aaa;text-align: center;" <?php echo $alternative_color; ?> ><?php echo $bt->id; ?></th>
                                                            <td style="font-size:11px;<?php if ($bt->parent != 0 ) { 
                              echo 'padding-left:50px;';
                          } ?>" <?php echo $alternative_color; ?> >
                                                                <input  style="<?php if ($bt->parent == 0 ) { 
                              echo 'width:210px;font-weight:bold;';
                          } else {
                              echo 'width:170px;font-size:11px;';
                          } ?>" maxlength="17" type="text" value="<?php echo $bt->title; ?>" name="type_title<?php echo $bt->id; ?>" id="type_title<?php echo $bt->id; ?>">
                          <?php //$this->get_available_days($bt->id); ?>
                                                            </td>

                                                            <td style="text-align:center;font-weight: bold;" <?php echo $alternative_color; ?> ><?php if ($bt->parent == 0 ) { 
                              echo $my_count;
                          }?></td>
                                                            <td style="text-align:center;" <?php echo $alternative_color; ?> >
                                                                <select  style="width:90px;"  name="type_parent<?php echo $bt->id; ?>" id="type_parent<?php echo $bt->id; ?>">
                          <?php foreach ($all_id as $m_id) { ?>
                                                                    <option <?php if ( $bt->parent==$m_id['id']) echo 'selected="SELECTED"' ?> value="<?php echo $m_id['id']; ?>"><?php echo $m_id['title'] ?></option>
                              <?php } ?>
                                                                </select>
                                                                <!--input  style="width:40px;" maxlength="17" type="text" value="<?php echo $bt->parent; ?>" name="type_parent<?php echo $bt->id; ?>" id="type_parent<?php echo $bt->id; ?>" -->
                                                            </td>
                                                            <td style="text-align:center;" <?php echo $alternative_color; ?> >
                                                                <select  style="width:50px;"   name="type_prioritet<?php echo $bt->id; ?>" id="type_prioritet<?php echo $bt->id; ?>">
                          <?php for ($m_id = 0; $m_id < 50; $m_id++) { ?>
                                                                    <option <?php if ( $bt->prioritet==$m_id) echo 'selected="SELECTED"' ?> value="<?php echo $m_id; ?>"><?php echo $m_id ?></option>
                              <?php } ?>
                                                                </select>
                                                                <!--input  style="width:40px;" maxlength="17" type="text" value="<?php echo $bt->prioritet; ?>" name="type_prioritet<?php echo $bt->id; ?>" id="type_prioritet<?php echo $bt->id; ?>" -->
                                                            </td>
                                                            <?php if ($is_use_visitors_number_for_availability == 'On') { ?>
                                                            <td style="text-align:center;" <?php echo $alternative_color; ?> >
                                                                <?php if ($bt->parent == 0 ) { ?>
                                                                <select <?php if ($bt->parent != 0 ) { echo ' disabled="DISABLED" '; } ?>  style="width:50px;"   name="type_max_visitors<?php echo $bt->id; ?>" id="type_max_visitors<?php echo $bt->id; ?>">
                                                                    <?php for ($m_id = 1; $m_id < 13; $m_id++) { ?>
                                                                        <option <?php if ( $bt->visitors==$m_id) echo 'selected="SELECTED"' ?> value="<?php echo $m_id; ?>"><?php echo $m_id ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                                <?php } else { ?>
                                                                <span style="font-size:10px;font-weight:bold;"><?php echo $bt->visitors; ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            <?php } ?>
                                                            <td style="font-size:11px;text-align: center;" <?php echo $alternative_color; ?>
                          <?php

                        $max_num = apply_bk_filter('get_max_res_num_for_user_in_multiuser', false );
                        if ( ($max_num === false) || ($max_num > count($bk_types) ) )

                          if ($bt->parent==0) { ?>
                                                                <div style="height:20px;">
                                                                <input class="button" style="margin:0px 10px;" type="button" value="+ <?php _e('Add', 'wpdev-booking'); ?>"
                                                                   onclick="javascript:
                                                                           document.getElementById('type_title_new').value='<?php echo $bt->title . '-' . ($my_count+1) ; ?>';
                                                                       document.getElementById('type_parent_new').value='<?php echo $bt->id  ; ?>';
                                                                       document.getElementById('type_prioritet_new').value='<?php echo  ($my_count+1)   ; ?>';
                                                                       document.getElementById('submit_resources_button').click();
                                                                           " /> </div>
                              <?php } ?>

                                                                <span style="line-height:25px;"><?php _e('Delete','wpdev-booking'); ?>: </span><input class="checkbox"  type="checkbox"   name="type_delete<?php echo $bt->id; ?>" id="type_delete<?php echo $bt->id; ?>"/>
                                                            </td>
                                                            <?php make_bk_action('show_users_collumn_at_settings', $bt , $alternative_color ); ?>
                                                       </tr>
                          <?php } 
                          
                          if ( ($max_num === false) || ($max_num > count($bk_types) ) ) {
                          ?>
                                                    <tr>
                                                        <td colspan="<?php if ($is_use_visitors_number_for_availability == 'On')  echo '7'; else echo '6'; ?>" style=" height:35px;padding:0px 35px;border-top:1px solid #999;">
                                                            <div style="float:left;line-height: 25px;font-weight:bold;margin:0px 5px;" ><?php _e('Title','wpdev-booking'); ?>:&nbsp;</div>
                                                            <input  style="float:left;width:125px;" maxlength="17" type="text" value="" name="type_title_new" id="type_title_new">
                                                            <div style="float:left;line-height: 25px;font-weight:normal;margin:0px 0px 0px 7px;" ><?php _e('Parent','wpdev-booking'); ?>:&nbsp;</div>
                                                            <select  style="float:left;width:90px;"  name="type_parent_new" id="type_parent_new">
                      <?php foreach ($all_id as $m_id) { ?>
                                                                    <option  value="<?php echo $m_id['id']; ?>"><?php echo $m_id['title'] ?></option>
                          <?php } ?>
                                                            </select>
                                                            <!--input  style="float:left;width:30px;" maxlength="17" type="text" value="0" name="type_parent_new" id="type_parent_new"-->
                                                            <div style="float:left;line-height: 25px;font-weight:normal;margin:0px 0px 0px 7px;" ><?php _e('Priority','wpdev-booking'); ?>:&nbsp;</div>
                                                            <select  style="float:left;width:50px;"   name="type_prioritet_new" id="type_prioritet_new">
                                                                <?php for ($m_id = 0; $m_id < 50; $m_id++) { ?>
                                                                    <option value="<?php echo $m_id; ?>"><?php echo $m_id ?></option>
                                                                <?php } ?>
                                                            </select>
                                                            <!--input  style="float:left;width:30px;" maxlength="17" type="text" value="0" name="type_prioritet_new" id="type_prioritet_new"-->

                                                            <input class="button" style="float:left;margin:0px 20px;" type="submit" value="+ <?php _e('Add new resource', 'wpdev-booking'); ?>" name="submit_resources_button" id="submit_resources_button"/>
                                                        </td>
                                                        <?php make_bk_action('show_users_collumn_at_settings', 'blank' ); ?>
                                                    </tr>
                           <?php } ?>
                                                </table>
                                                <div class="clear" style="height:10px;"></div>
                                                <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="submit_resources"/>
                                                <div class="clear" style="height:10px;"></div>

                                            </form>

                                       </div> </div> </div>
                      <?php
                  }

                  // Show Advanced settings at the bootom of Resource Settings page
                  function show_resources_advanced_settings( $is_only_post = false ) {
                     
                      if ($is_only_post) {
                          if(isset($_POST['submit_advanced_resources_settings'])) {
                              if (isset( $_POST['booking_is_use_visitors_number_for_availability'] ))     $is_use_visitors_number_for_availability = 'On';
                              else                                                                        $is_use_visitors_number_for_availability = 'Off';
                              update_bk_option( 'booking_is_use_visitors_number_for_availability' ,  $is_use_visitors_number_for_availability );

                              if (isset( $_POST['booking_is_show_availability_in_tooltips'] ))     $booking_is_show_availability_in_tooltips = 'On';
                              else                                                         $booking_is_show_availability_in_tooltips = 'Off';
                              update_bk_option( 'booking_is_show_availability_in_tooltips' ,  $booking_is_show_availability_in_tooltips );

                              if (isset( $_POST['booking_is_dissbale_booking_for_different_sub_resources'] ))
                                   update_bk_option( 'booking_is_dissbale_booking_for_different_sub_resources', 'On' );
                              else update_bk_option( 'booking_is_dissbale_booking_for_different_sub_resources', 'Off' );

                              update_bk_option( 'booking_highlight_availability_word' ,  $_POST['booking_highlight_availability_word'] );
                              //update_bk_option( 'booking_maximum_selection_days_for_one_resource' ,  $_POST['maximum_selection_days_for_one_resource'] );
                              update_bk_option( 'booking_availability_based_on' ,  $_POST['availability_based_on'] );
                          }
                          return ;
                      }

                      $is_use_visitors_number_for_availability   = get_bk_option( 'booking_is_use_visitors_number_for_availability');
                      $booking_is_show_availability_in_tooltips   = get_bk_option( 'booking_is_show_availability_in_tooltips');
                      $booking_highlight_availability_word        = get_bk_option( 'booking_highlight_availability_word');
                      //$maximum_selection_days_for_one_resource    = get_bk_option( 'booking_maximum_selection_days_for_one_resource');
                      $availability_based_on_visitors   = get_bk_option( 'booking_availability_based_on');
                      $booking_is_dissbale_booking_for_different_sub_resources = get_bk_option( 'booking_is_dissbale_booking_for_different_sub_resources');
                      ?>
                                        <div class='meta-box'>
                                          <div <?php $my_close_open_win_id = 'bk_settings_resources_advanced_options'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');"><br></div>
                                                <h3 class='hndle'><span><?php _e('Advanced Settings', 'wpdev-booking'); ?></span></h3> <div class="inside">
                                                <form  name="post_option_resources_adv" action="" method="post" id="post_option_resources_adv" >
                                                    <table class="form-table"><tbody>
                                                        <?php /* ?>
                                                        <tr valign="top">
                                                            <th scope="row"><label for="admin_cal_count" ><?php _e('Max days for booking inside of one resource', 'wpdev-booking'); ?>:</label></th>
                                                            <td><input id="maximum_selection_days_for_one_resource"  name="maximum_selection_days_for_one_resource" class="regular-text code" type="text" style="width:350px;" size="145" value="<?php echo $maximum_selection_days_for_one_resource; ?>" /><br/>
                                                                <span class="description"><?php printf(__('Type the %smaximum number days%s of selection,  which garanteed will be storeed inside of one booking sub resource', 'wpdev-booking'),'<b>','</b>');?></span>
                                                            </td>
                                                        </tr>
                                                        <?php /**/ ?>

                      
                                                       <tr valign="top" class="ver_premium_hotel">
                                                            <th scope="row">
                                                                <label for="is_use_visitors_number_for_availability" ><?php _e('Visitors number apply to capacity', 'wpdev-booking'); ?>:</label>
                                                            </th>
                                                            <td>
                                                                <input <?php if ($is_use_visitors_number_for_availability == 'On') echo "checked"; ?>  value="<?php echo $is_use_visitors_number_for_availability; ?>" name="booking_is_use_visitors_number_for_availability" id="booking_is_use_visitors_number_for_availability" type="checkbox"
                                                                        onclick="javascript: if (this.checked) jQuery('#togle_settings_availability_based_on_visitors').slideDown('normal'); else  jQuery('#togle_settings_availability_based_on_visitors').slideUp('normal');"
                                                                 />
                                                                <span class="description"><?php printf(__(' Check this checkbox if you want that availability of the day (capacity) depends from number of selected visitors %s', 'wpdev-booking'), '[visitors]');?></span>
                                                            </td>
                                                        </tr>


                                                        <tr valign="top" class="ver_premium_hotel"><td colspan="2">
                                                            <table id="togle_settings_availability_based_on_visitors" style="width:100%;<?php if ($is_use_visitors_number_for_availability != 'On') echo "display:none;";/**/ ?>" class="hided_settings_table">
                                                                <tr>
                                                                <td scope="row">
                                                                    <div style="width:100%;">

                                                                        <div style="margin:10px 25px 10px 0px; font-weight: normal;"><label for="range_start_day" ><?php
                                                                        printf(__(
                                                                              "Show at tooltip on calendar availability based on free booking resource items. %s" .
                                                                              "Check maximum support of visitors at %sone booking resource%s with selected number of visitors from booking form"
                                                                        , 'wpdev-booking'),'<br />','<strong>','</strong>'); ?>: </label>
                                                                            <input style="margin:-25px 50px 0px;"  <?php if ($availability_based_on_visitors == 'items') echo 'checked="checked"';/**/ ?> value="items" type="radio" id="availability_based_on_items"  name="availability_based_on"    />
                                                                        </div>
                                                                        <div style="border-bottom: 1px solid #ccc;"></div>
                                                                        <div style="margin:10px 25px 10px 0px; font-weight: normal;"><label for="range_start_day" ><?php
                                                                        printf(__(
                                                                         "Show at tooltip on calendar availability based on summ number of visitors, which can be at free booking resource items. %s" .
                                                                         "Check maximum support of visitors at %sall booking resources%s with selected number of visitors from booking form"
                                                                        , 'wpdev-booking'),'<br />','<strong>', '</strong>'); ?>: </label>
                                                                            <input style="margin:-25px 50px 0px;"    <?php if ($availability_based_on_visitors == 'visitors') echo 'checked="checked"';/**/ ?> value="visitors" type="radio" id="availability_based_on_visitors"  name="availability_based_on"    />
                                                                        </div>

                                                                    </div>
                                                                </td>
                                                                </tr>
                                                            </table>
                                                        </td></tr>


                                                       <tr valign="top" class="ver_premium_hotel">
                                                            <th scope="row">
                                                                <label for="range_selection_time_is_active" ><?php _e('Show availability', 'wpdev-booking'); ?>:</label>
                                                            </th>
                                                            <td>
                                                                <input <?php if ($booking_is_show_availability_in_tooltips == 'On') echo "checked";/**/ ?>  value="<?php echo $booking_is_show_availability_in_tooltips; ?>" name="booking_is_show_availability_in_tooltips" id="booking_is_show_availability_in_tooltips" type="checkbox"
                                                                     onclick="javascript: if (this.checked) jQuery('#togle_settings_availability_day_show').slideDown('normal'); else  jQuery('#togle_settings_availability_day_show').slideUp('normal');"
                                                                                                                                                  />
                                                                <span class="description"><?php _e(' Check this checkbox if you want to show availability number of booking resource at the tooltip, when mouse over the day of calendar.', 'wpdev-booking');?></span>
                                                            </td>
                                                        </tr>

                                                        <tr valign="top" class="ver_premium_hotel"><td colspan="2">
                                                            <table id="togle_settings_availability_day_show" style="<?php if ($booking_is_show_availability_in_tooltips != 'On') echo "display:none;";/**/ ?>" class="hided_settings_table">
                                                                <tr>
                                                                <th scope="row"><label for="booking_highlight_availability_word" ><?php _e('Availability description', 'wpdev-booking'); ?>:</label></th>
                                                                    <td><input value="<?php echo $booking_highlight_availability_word; ?>" name="booking_highlight_availability_word" id="booking_highlight_availability_word"  type="text"    />
                                                                        <span class="description"><?php printf(__('Type your %savailability%s description', 'wpdev-booking'),'<b>','</b>');?></span>
                                                                        <?php make_bk_action('show_additional_translation_shortcode_help'); ?>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td></tr>


                                                       <tr valign="top" class="ver_premium_hotel">
                                                            <th scope="row">
                                                                <label for="booking_is_dissbale_booking_for_different_sub_resources" ><?php _e('Disable bookings in different booking resources', 'wpdev-booking'); ?>:</label>
                                                            </th>
                                                            <td>
                                                                <input <?php if ($booking_is_dissbale_booking_for_different_sub_resources == 'On') echo "checked";/**/ ?>  value="<?php echo $booking_is_dissbale_booking_for_different_sub_resources; ?>" name="booking_is_dissbale_booking_for_different_sub_resources" id="booking_is_dissbale_booking_for_different_sub_resources" type="checkbox" />
                                                                <span class="description"><?php _e(' Check this checkbox if you want to dissable booking, which can be stored in different booking resources. So if this checkbox is checked, the booking is allowed only, if all days of booking are at same booking resources, otherwise the error message will show.', 'wpdev-booking');?></span>
                                                            </td>
                                                        </tr>


                                                    </tbody></table>
                                                <div class="clear" style="height:10px;"></div>
                                                <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="submit_advanced_resources_settings"/>
                                                <div class="clear" style="height:10px;"></div>

                                            </form>

                                       </div> </div> </div>

                      <?php
                  }


          // Show Advanced settings at the bootom of Resource Settings page
          function show_advanced_settings_in_general_settings_menu(  ) {


                  if(isset($_POST['availability_based_on'])) {
                      if (isset( $_POST['booking_is_use_visitors_number_for_availability'] ))     $is_use_visitors_number_for_availability = 'On';
                      else                                                                        $is_use_visitors_number_for_availability = 'Off';
                      update_bk_option( 'booking_is_use_visitors_number_for_availability' ,  $is_use_visitors_number_for_availability );


                      if (isset( $_POST['booking_is_dissbale_booking_for_different_sub_resources'] ))
                           update_bk_option( 'booking_is_dissbale_booking_for_different_sub_resources', 'On' );
                      else update_bk_option( 'booking_is_dissbale_booking_for_different_sub_resources', 'Off' );


                      update_bk_option( 'booking_availability_based_on' ,  $_POST['availability_based_on'] );
                  }

              $is_use_visitors_number_for_availability   = get_bk_option( 'booking_is_use_visitors_number_for_availability');

              $availability_based_on_visitors   = get_bk_option( 'booking_availability_based_on');
              $booking_is_dissbale_booking_for_different_sub_resources = get_bk_option( 'booking_is_dissbale_booking_for_different_sub_resources');
              ?>
                                <div class='meta-box'>
                                  <div <?php $my_close_open_win_id = 'bk_settings_resources_advanced_options'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');"><br></div>
                                        <h3 class='hndle'><span><?php _e('Advanced', 'wpdev-booking'); ?></span></h3> <div class="inside">
                                        
                                            <table class="form-table"><tbody>

                                               <tr valign="top" class="ver_premium_hotel">
                                                    <th scope="row">
                                                        <label for="is_use_visitors_number_for_availability" ><?php _e('Visitors number apply to capacity', 'wpdev-booking'); ?>:</label>
                                                    </th>
                                                    <td>
                                                        <input <?php if ($is_use_visitors_number_for_availability == 'On') echo "checked"; ?>  value="<?php echo $is_use_visitors_number_for_availability; ?>" name="booking_is_use_visitors_number_for_availability" id="booking_is_use_visitors_number_for_availability" type="checkbox"
                                                                onclick="javascript: if (this.checked) jQuery('#togle_settings_availability_based_on_visitors').slideDown('normal'); else  jQuery('#togle_settings_availability_based_on_visitors').slideUp('normal');"
                                                         />
                                                        <span class="description"><?php printf(__(' Check this checkbox if you want that availability of the day (capacity) depends from number of selected visitors %s', 'wpdev-booking'), '[visitors]');?></span>
                                                    </td>
                                                </tr>


                                                <tr valign="top" class="ver_premium_hotel"><td colspan="2">
                                                    <table id="togle_settings_availability_based_on_visitors" style="width:100%;<?php if ($is_use_visitors_number_for_availability != 'On') echo "display:none;";/**/ ?>" class="hided_settings_table">
                                                        <tr>
                                                        <td scope="row">
                                                            <div style="width:100%;">

                                                                <div style="margin:10px 25px 10px 0px; font-weight: normal;"><label for="range_start_day" ><?php
                                                                printf(__(
                                                                      "Show at tooltip on calendar availability based on free booking resource items. %s" .
                                                                      "Check maximum support of visitors at %sone booking resource%s with selected number of visitors from booking form"
                                                                , 'wpdev-booking'),'<br />','<strong>','</strong>'); ?>: </label>
                                                                    <input style=""  <?php if ($availability_based_on_visitors == 'items') echo 'checked="checked"';/**/ ?> value="items" type="radio" id="availability_based_on_items"  name="availability_based_on"    />
                                                                </div>
                                                                <div style="border-bottom: 1px solid #ccc;"></div>
                                                                <div style="margin:10px 25px 10px 0px; font-weight: normal;"><label for="range_start_day" ><?php
                                                                printf(__(
                                                                 "Show at tooltip on calendar availability based on summ number of visitors, which can be at free booking resource items. %s" .
                                                                 "Check maximum support of visitors at %sall booking resources%s with selected number of visitors from booking form"
                                                                , 'wpdev-booking'),'<br />','<strong>', '</strong>'); ?>: </label>
                                                                    <input style=""    <?php if ($availability_based_on_visitors == 'visitors') echo 'checked="checked"';/**/ ?> value="visitors" type="radio" id="availability_based_on_visitors"  name="availability_based_on"    />
                                                                </div>

                                                            </div>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td></tr>



                                               <tr valign="top" class="ver_premium_hotel">
                                                    <th scope="row">
                                                        <label for="booking_is_dissbale_booking_for_different_sub_resources" ><?php _e('Disable bookings in different booking resources', 'wpdev-booking'); ?>:</label>
                                                    </th>
                                                    <td>
                                                        <input <?php if ($booking_is_dissbale_booking_for_different_sub_resources == 'On') echo "checked";/**/ ?>  value="<?php echo $booking_is_dissbale_booking_for_different_sub_resources; ?>" name="booking_is_dissbale_booking_for_different_sub_resources" id="booking_is_dissbale_booking_for_different_sub_resources" type="checkbox" />
                                                        <span class="description"><?php _e(' Check this checkbox if you want to dissable booking, which can be stored in different booking resources. So if this checkbox is checked, the booking is allowed only, if all days of booking are at same booking resources, otherwise the error message will show.', 'wpdev-booking');?></span>
                                                    </td>
                                                </tr>


                                            </tbody></table>
                                        <div class="clear" style="height:10px;"></div>

                                    

                               </div> </div> </div>

              <?php
          }



          function settings_set_show_availability_in_tooltips(){
                  if(isset($_POST['booking_highlight_availability_word'])) {
                      if (isset( $_POST['booking_is_show_availability_in_tooltips'] ))     $booking_is_show_availability_in_tooltips = 'On';
                      else                                                         $booking_is_show_availability_in_tooltips = 'Off';
                      update_bk_option( 'booking_is_show_availability_in_tooltips' ,  $booking_is_show_availability_in_tooltips );
                      update_bk_option( 'booking_highlight_availability_word' ,  $_POST['booking_highlight_availability_word'] );


                  }
              $booking_is_show_availability_in_tooltips   = get_bk_option( 'booking_is_show_availability_in_tooltips');
              $booking_highlight_availability_word        = get_bk_option( 'booking_highlight_availability_word');

           ?>

               <tr valign="top" class="ver_premium_hotel">
                    <th scope="row">
                        <label for="range_selection_time_is_active" ><?php _e('Show availability in tooltip', 'wpdev-booking'); ?>:</label>
                    </th>
                    <td>
                        <input <?php if ($booking_is_show_availability_in_tooltips == 'On') echo "checked";/**/ ?>  value="<?php echo $booking_is_show_availability_in_tooltips; ?>" name="booking_is_show_availability_in_tooltips" id="booking_is_show_availability_in_tooltips" type="checkbox"
                             onclick="javascript: if (this.checked) jQuery('#togle_settings_availability_day_show').slideDown('normal'); else  jQuery('#togle_settings_availability_day_show').slideUp('normal');"
                                                                                                          />
                        <span class="description"><?php _e(' Check this checkbox if you want to show availability number of booking resource at the tooltip, when mouse over the day of calendar.', 'wpdev-booking');?></span>
                    </td>
                </tr>

                <tr valign="top" class="ver_premium_hotel"><td colspan="2">
                    <table id="togle_settings_availability_day_show" style="<?php if ($booking_is_show_availability_in_tooltips != 'On') echo "display:none;";/**/ ?>" class="hided_settings_table">
                        <tr>
                        <th scope="row"><label for="booking_highlight_availability_word" ><?php _e('Availability description', 'wpdev-booking'); ?>:</label></th>
                            <td><input value="<?php echo $booking_highlight_availability_word; ?>" name="booking_highlight_availability_word" id="booking_highlight_availability_word"  type="text"    />
                                <span class="description"><?php printf(__('Type your %savailability%s description', 'wpdev-booking'),'<b>','</b>');?></span>
                                <?php make_bk_action('show_additional_translation_shortcode_help'); ?>
                            </td>
                        </tr>
                    </table>
                </td></tr>
                <?php
          }



          // Show coupons settings block
          function settings_show_coupons() {

              global $wpdb;
              $link = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-resources&tab=coupons';

              $this->delete_expire_coupons();



              if (isset($_GET['delete_coupon'])) {
                    $sql = "DELETE FROM ".$wpdb->prefix ."booking_coupons WHERE coupon_id = ". $_GET['delete_coupon']  ;
                    if ( false === $wpdb->query($wpdb->prepare($sql)) ){
                       echo '<div class="error_message ajax_message textleft" style="font-size:12px;font-weight:bold;">'. bk_error('Error during deleting from DB coupon',__FILE__,__LINE__ ). '</div>';
                    }
                    ?>
                      <script type="text/javascript">
                        document.getElementById('ajax_working').innerHTML = '<?php echo __('Deleted', 'wpdev-booking'); ?>';
                        jQuery('#ajax_working').fadeOut(1000);
                        window.location.href='<?php echo $link ;?>';
                    </script>
                    <?php
                    return;
              }

              if ( isset( $_POST['add_coupon_button'] ) ) {
//debuge($_POST);
                 
                              $users_values = '';
                              $useres_title = '';
                              
                              $my_date = $_POST["year_coupon_new"] . '-' .$_POST["month_coupon_new"] . '-' .$_POST["day_coupon_new"];
                              $my_resources = implode(',',  $_POST['coupon_resources_new']);
                              if ($my_resources != 'all') $my_resources = ','.$my_resources.',';
                              else {
                                  // if multiuser and its not superadmin so then chnage all to ,9,8,....
                                  //$my_resources = ''

                                    if ( class_exists('wpdev_bk_multiuser')) {  // If MultiUser so
                                        $is_superadmin = apply_bk_filter('multiuser_is_user_can_be_here', true, 'only_super_admin');
                                        if (! $is_superadmin) { // User not superadmin
                                            $bk_ids = apply_bk_filter('get_bk_resources_of_user',false);
                                            if ($bk_ids !== false) {
                                              $my_resources = ',';
                                              foreach ($bk_ids as $bk_id) {
                                                  $my_resources .= $bk_id->ID . ',';
                                              }
                                            } else {
                                              return; // Need to create booking resources for this user firstly!
                                            }
                                        }
                                    }
                              }

                              if ( false === $wpdb->query($wpdb->prepare(
                                      'INSERT INTO '.$wpdb->prefix .'booking_coupons ( coupon_code, coupon_value, coupon_type, expiration_date,coupon_min_sum, support_bk_types  '.$useres_title.' ) VALUES ( "'.
                                      $_POST["coupon_name_new"] .'", "' .
                                      $_POST["coupon_value_new"] .'", "' ) .
                                      $_POST["coupon_type_new"] .'", "'.
                                      $my_date .'", "'.
                                      $_POST["coupon_minimum_new"] .'", "'.
                                      $my_resources .
                                       '" '. $users_values .') ' )  ){
                                  bk_error('Error during updating to DB booking resources' ,__FILE__,__LINE__);
                              } else {

                                        $newid = (int) $wpdb->insert_id;
                                        make_bk_action('added_new_coupon',$newid);
                                     
                                    echo '<div class="info_message ajax_message textleft" style="font-size:12px;font-weight:bold;">'.__('Coupon saved', 'wpdev-booking').'</div>';
                              }

              }

              $alternative_color = ' class="alternative_color " ';
          ?>
            <div class='meta-box'>
              <div <?php $my_close_open_win_id = 'bk_settings_coupons_management'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');"><br></div>
                <h3 class='hndle'><span><?php _e('Booking coupons managment', 'wpdev-booking'); ?></span></h3> <div class="inside">

                    <?php  // List all coupons here  ?>

                <form  name="post_coupons_management" action="" method="post" id="post_coupons_management" >
<?php
$is_exist_coupons = $this->is_exist_coupons('any');
if( $is_exist_coupons ) { ?>
                    <table style="width:100%;" class="resource_table0 booking_table" cellpadding="0" cellspacing="0" style="color:#444444;text-shadow:1px 1px 0 #FFFFFF;width:100%;">

                        <tr>
                            <th style="width:20px;height:24px;text-align: center;cursor: default;"> <?php _e('ID', 'wpdev-booking'); ?> </th>
                            <th style="width:90px;text-align: center;cursor: default;" rel="tooltip" class="tooltip_bottom"  title='<?php echo __('The code your customers will be using to receive the discount.','wpdev-booking'); ?>'> <?php _e('Coupon Code', 'wpdev-booking'); ?></th>
                            <th style="width:60px;text-align: center;cursor: default;" rel="tooltip" class="tooltip_bottom"  title='<?php echo __('The amount, which be saved. Enter only digits.','wpdev-booking'); ?>'> <?php _e('Savings', 'wpdev-booking'); ?></th>
                            <th style="width:125px;text-align: center;cursor: default;" rel="tooltip" class="tooltip_bottom"  title='<?php echo __('The minimum total cost required to use the coupon','wpdev-booking'); ?>'> <?php _e('Minimum Purchase', 'wpdev-booking'); ?></th>
                            <th style="width:110px;text-align: center;cursor: default;" rel="tooltip" class="tooltip_bottom"  title='<?php echo __('The date your coupon will expire','wpdev-booking'); ?>'> <?php _e('Expiration Date', 'wpdev-booking'); ?></th>
                            <th style="text-align: center;cursor: default;" rel="tooltip" class="tooltip_bottom"  title='<?php echo __('Resource list, which support this coupon','wpdev-booking'); ?>'> <?php _e('Resources', 'wpdev-booking'); ?></th>
                            <th style="width:75px;text-align: center;cursor: default;" rel="tooltip" class="tooltip_bottom"  title='<?php echo __('Delete this coupon','wpdev-booking'); ?>'> <?php _e('Delete', 'wpdev-booking'); ?></th>
                        </tr>

                        <?php
                            $where = '';
                            $where = apply_bk_filter('multiuser_modify_SQL_for_current_user', $where);
                            if ($where != '') $where = ' WHERE ' . $where;

                        $sql = "SELECT
                                         bc.coupon_id AS id,
                                         bc.coupon_active AS active,
                                         bc.coupon_code AS code,
                                         bc.coupon_value AS value,
                                         bc.coupon_type AS type,
                                         bc.expiration_date AS date,
                                         bc.coupon_min_sum AS min,
                                         bc.support_bk_types  AS resource
                               FROM ".$wpdb->prefix ."booking_coupons as bc   ".$where." 
                               ORDER BY  bc.expiration_date  ";
                        $result = $wpdb->get_results($wpdb->prepare( $sql ));

                        foreach ($result as $res) {
                                if ( $alternative_color == '')  $alternative_color = ' class="alternative_color " ';
                                else $alternative_color = '';
                                $coupon_type = $res->type;
                                $coupon_date = explode(' ',  $res->date);
                                $coupon_date = $coupon_date[0];
                                $coupon_date = explode('-',  $coupon_date);

                                    $cost_currency = get_bk_option( 'booking_paypal_curency' );
                                    $init_cost_currency = $cost_currency;
                                    if ($cost_currency == 'USD' ) $cost_currency = '$';
                                    elseif ($cost_currency == 'EUR' ) $cost_currency = '&euro;';
                                    elseif ($cost_currency == 'GBP' ) $cost_currency = '&#163;';
                                    elseif ($cost_currency == 'JPY' ) $cost_currency = '&#165;';

                                    if ($init_cost_currency == $cost_currency) $init_cost_currency = true;
                                    else $init_cost_currency = false;

                                    $my_res = $res->resource;
                                    if ($my_res == 'all') $my_res = '<span style="font-size:13px;">' . __('All','wpdev-booking') . '</span>';
                                    else {
                                        $my_res_ids = explode(',',$my_res);
                                        $my_res = '';

                                        foreach ($my_res_ids as $res_id) {
                                            if ($res_id !='') $my_res .= get_booking_title($res_id) . ', ';
                                        }
                                        if (strlen($my_res)>1) $my_res = substr($my_res, 0, -2);
                                    }

                        ?>
                        <tr>
                            <td <?php echo $alternative_color; ?> style="width:20px;height:20px;text-align: center;font-size:11px;"><?php echo $res->id; ?></td>
                            <td style="text-align: center;font-size:12px;font-weight:bold;" <?php echo $alternative_color; ?> > <?php echo $res->code; ?> </td>
                            <td style="text-align: center;font-size:11px;" <?php echo $alternative_color; ?> > <?php
                                    if( ($coupon_type == 'fixed') && (! $init_cost_currency) ) echo $cost_currency;
                                    echo $res->value , ' ';
                                    if( ($coupon_type == 'fixed') && ($init_cost_currency) ) echo $cost_currency;
                                    if( $coupon_type == '%') echo '%'
                            ?> </td>
                            <td style="text-align: center;font-size:11px;" <?php echo $alternative_color; ?> > <?php
                                    if (! $init_cost_currency) echo $cost_currency;
                                    echo $res->min , ' ';
                                    if  ($init_cost_currency) echo $cost_currency;
                            ?> </td>
                            <td style="text-align: center;font-size:11px;" <?php echo $alternative_color; ?> > <?php echo $coupon_date[0] . ' / ' . $coupon_date[1] . ' / '.$coupon_date[2] ; ?> </td>
                            <td style="text-align: center;font-size:10px;" <?php echo $alternative_color; ?> > <?php echo $my_res; ?> </td>
                            <td style="text-align: center;font-size:11px;" <?php echo $alternative_color; ?> >
                                <input type="button" onclick="javascript:window.location.href='<?php echo $link . '&delete_coupon=' . $res->id; ?>';"  value="Remove" class="button-secondary" name="coupon_is_delete<?php echo $res->id; ?>" id="coupon_is_delete<?php echo $res->id; ?>">
                            </td>
                        </tr>
                        <?php } ?>

                    </table>
                    <div class="clear" style="height:10px;"></div>
                    <input class="button-primary" style="float:left;" type="button" value="<?php _e('Add new coupon', 'wpdev-booking'); ?>" name="submit_coupons_management" onclick="javascript:jQuery('#new_coupon_table').slideToggle('normal');makeScroll('#new_coupon_table');this.style.display='none';"/>
                    <div class="clear" style="height:10px;"></div>

<?php } ?>
                    <?php  // Add new coupons form  ?>

                    <div id="new_coupon_table" <?php if ( $is_exist_coupons ) echo 'style="display:none;"'; ?> >
                    <div style="height:1px;clear:both;border-top:1px solid #bbc;margin-bottom:10px;"></div>

                    <table style="width:100%;" class="resource_table0 booking_table0" cellpadding="0" cellspacing="0" >

                        <tr style="margin:10px 0px;">
                            <td colspan="" style="width:130px;line-height: 35px;"> <?php _e('Coupon Code','wpdev-booking'); ?>:</td>
                            <td style="width:250px;text-align:left;">
                                <input id="coupon_name_new" name="coupon_name_new" type="text" value="" style="width:85px;float:left;margin-right:20px;" maxlength="20" >
                            </td>
                            <td rowspan="4" valign="top">
                                <div style="float:left;line-height: 25px;font-weight:normal;margin:0px 5px;" >
                                <?php _e('Resources','wpdev-booking'); ?>:&nbsp;</div>
                                <div class="clear"></div>
                                <select id="coupon_resources_new" name="coupon_resources_new[]" multiple="MULTIPLE" style="height:110px;width:200px;float:left;" >
                                    <option value="all" selected="SELECTED"><?php _e('All', 'wpdev-booking'); ?></option>
                                    <?php  $bk_resources = $this->get_booking_types_hierarhy_linear();
                                    //debuge($bk_resources); die;


                                    ?>
                                    <?php foreach ($bk_resources as $mm) { 
                                        $mm = $mm['obj'];
                                        ?>

                                    <option value="<?php echo $mm->id; ?>"
                                          style="<?php if  (isset($mm->parent)) if ($mm->parent == 0 ) { echo 'font-weight:bold;'; } else { echo 'font-size:11px;padding-left:20px;'; } ?>"
                                        ><?php echo $mm->title; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>

                        <tr style="margin:10px 0px;">
                            <td style="line-height: 35px;"> <?php _e('Savings','wpdev-booking'); ?> </td>
                            <td>
                                <input id="coupon_value_new" name="coupon_value_new" type="text" value="" style="width:85px;float:left;margin-right:5px;" maxlength="10"  >
                                <select id="coupon_type_new" name="coupon_type_new" style="width:120px;float:left;margin-right:20px;" >
                                     <option value="fixed"><?php _e('Fixed Amount', 'wpdev-booking'); ?></option>
                                     <option value="%"><?php _e('Percentage Off', 'wpdev-booking'); ?></option>
                                </select>
                            </td>
                        </tr>
                            
                        <tr style="margin:10px 0px;">
                            <td style="line-height: 35px;"> <?php _e('Expiration Date','wpdev-booking'); ?>:&nbsp;</td>
                            <td>
                                <div style="float:left;line-height: 25px;font-weight:normal;margin-right:20px;" id="dates_for_coupons_new">
                                    <?php $cur_date = date('Y-m-d'); $cur_date = explode('-',$cur_date); ?>
                                    <select  id="year_coupon_new"  name="year_coupon_new" style="width:65px;margin-right:5px;" > <?php    for ($mi = $cur_date[0]; $mi < 2030; $mi++) {   echo '<option value="'.$mi.'" >'.$mi.'</option>';   } ?> </select> /
                                    <select  id="month_coupon_new"  name="month_coupon_new" style="width:50px;margin-right:7px;" > <?php for ($mi = 1; $mi < 13; $mi++) { if ($mi<10) {$mi ='0'.$mi;}  echo '<option value="'.$mi.'"  '; if ($mi ==$cur_date[1]) echo ' selected="SELECTED" ';  echo ' >'.$mi.'</option>';   } ?> </select> /
                                    <select  id="day_coupon_new"  name="day_coupon_new" style="width:50px;" > <?php for ($mi = 1; $mi < 32; $mi++) { if ($mi<10) {$mi ='0'.$mi;}   echo '<option value="'.$mi.'" ';  if ($mi ==$cur_date[2]) echo ' selected="SELECTED" ';  echo '>'.$mi.'</option>';   } ?> </select>
                                </div>
                            </td>
                        </tr>
                            
                        <tr style="margin:10px 0px;">
                            <td style="line-height: 35px;"> <?php _e('Minimum Purchase','wpdev-booking'); ?>: </td>
                            <td>
                                <input id="coupon_minimum_new" name="coupon_minimum_new" type="text" value="" style="width:85px;float:left;margin-right:20px;" maxlength="10"  >
                            </td>
                        </tr>

                        <tr style="margin:10px 0px;">
                            <td style="line-height: 55px;" colspan="2"></td>
                            <td style="text-align:left;">
                                <input class="button" style="margin:10px 0px 0px 40px;" type="submit" value="+ <?php _e('Add new coupon', 'wpdev-booking'); ?>" name="add_coupon_button" id="add_coupon_button"/>
                            </td>
                        </tr>

                    </table>
                    
                    </div>
                    


                </form>

           </div> </div> </div>
          <?php
         }

                // Show help hint of shortcode at the admin panel
                function show_additional_shortcode_help_for_form(){
                    ?><span class="description"><?php printf(__('%s - coupon field, ', 'wpdev-booking'),'<code>[coupon]</code>');?></span>
                      <span class="description example-code"><?php printf(__('Example: %s ', 'wpdev-booking'),'[coupon* my_coupon]');?></span><br/><?php
                }


          //Show Search settings //
          function show_search_settings(){

                        if ( isset( $_POST['booking_search_form_show'] ) ) {
                             $_POST['booking_search_form_show'] = str_replace('\"', '"', $_POST['booking_search_form_show']);
                             update_bk_option( 'booking_search_form_show' , $_POST['booking_search_form_show'] );
                        }
                        $booking_search_form_show = get_bk_option( 'booking_search_form_show');

                        if ( isset( $_POST['booking_found_search_item'] ) ) {
                             $_POST['booking_found_search_item'] = str_replace('\"', '"', $_POST['booking_found_search_item']);
                             update_bk_option( 'booking_found_search_item' , $_POST['booking_found_search_item'] );
                        }
                        $booking_found_search_item = get_bk_option( 'booking_found_search_item');

                    ?>

                    <script type="text/javascript">
                        function reset_to_def_search_form() {
                                document.getElementById('booking_search_form_show').value = '<?php echo $this->get_default_booking_search_form_show(); ?>';
                        }
                        function reset_to_def_found_search_item() {
                                document.getElementById('booking_found_search_item').value = '<?php echo $this->get_default_booking_found_search_item(); ?>';
                        }
                    </script>

                    <div class='meta-box'>
                        <div <?php $my_close_open_win_id = 'bk_settings_search_form_fields'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');"><br></div>
                            <h3 class='hndle'><span><?php _e('Search form customization', 'wpdev-booking'); ?></span></h3><div class="inside">
                            <form  name="post_option" action="" method="post" id="post_search_option" >

                                    <div style="float:left;margin:10px 0px;width:58%;">
                                    <textarea id="booking_search_form_show" name="booking_search_form_show" class="darker-border" style="width:100%;" rows="22"><?php echo htmlspecialchars($booking_search_form_show, ENT_NOQUOTES ); ?></textarea>
                                    </div>
                                    <div style="float:right;margin:10px 0px;width:40%;" class="code_description">
                                        <div class="shortcode_help_section">
                                          <span class="description"><?php printf(__('Use these shortcodes for customization: ', 'wpdev-booking'));?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - search inside of posts/pages, which are inside of this category, ', 'wpdev-booking'),'<code>[search_category]</code>');?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - search inside of posts/pages, which are have this tag, ', 'wpdev-booking'),'<code>[search_tag]</code>');?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - check in date, ', 'wpdev-booking'),'<code>[search_check_in]</code>');?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - check out date, ', 'wpdev-booking'),'<code>[search_check_out]</code>');?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - default selection number of visitors, ', 'wpdev-booking'),'<code>[search_visitors]</code>');?></span></br>
                                          <br/><span class="description example-code"><?php echo (sprintf(__('Example: %s - custom number of visitor selections"', 'wpdev-booking'),'[search_visitors "1" "2" "3" "4" "5" "6" "7" "8" "9" "10"]')); ?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - search button, ', 'wpdev-booking'),'<code>[search_button]</code>');?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - new line, ', 'wpdev-booking'),'<code>&lt;br/&gt;</code>');?></span><br/><br/>
                                          <span class="description"><?php printf(__('use any other HTML tags (carefully).', 'wpdev-booking'),'<code>','</code>');?></span><br/><br/>
                                          <?php make_bk_action('show_additional_translation_shortcode_help'); ?>
                                        </div>
                                    </div>

                                    <div style="clear:both;margin:10px 0px;width:100%;" class="code_description">
                                        <div class="shortcode_help_section" style="padding:10px;">
                                            <span class="description"><?php _e('Additional customization style of this element you can make at this file', 'wpdev-booking'); echo ': <code>', WPDEV_BK_PLUGIN_URL, '/css/client.css</code>' ?></span>
                                        </div>
                                    </div>

                                    <div class="clear" style="height:1px;"></div>
                                    <input class="button-secondary" style="float:left;" type="button" value="<?php _e('Reset to default search form content', 'wpdev-booking'); ?>" onclick="javascript:reset_to_def_search_form();" name="reset_form"/>
                                    <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="Submit"/>
                                    <div class="clear" style="height:5px;"></div>


                            </form>
                     </div></div></div>


                    <div class='meta-box'>
                        <div <?php $my_close_open_win_id = 'bk_settings_search_form_fields_show'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');"><br></div>
                            <h3 class='hndle'><span><?php printf(__('Customization of found booking resource items', 'wpdev-booking'),'[content]'); ?></span></h3><div class="inside">
                                <form  name="post_option" action="" method="post" id="post_serach_item_option" >

                                    <div style="float:left;margin:10px 0px;width:58%;">
                                    <textarea id="booking_found_search_item" name="booking_found_search_item" class="darker-border" style="width:100%;" rows="22"><?php echo htmlspecialchars($booking_found_search_item, ENT_NOQUOTES ); ?></textarea>
                                    </div>
                                    <div style="float:right;margin:10px 0px;width:40%;" class="code_description">
                                        <div  class="shortcode_help_section">
                                          <span class="description"><?php printf(__('Use these shortcodes for customization: ', 'wpdev-booking'));?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - resource title, ', 'wpdev-booking'),'<code>[booking_resource_title]</code>');?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - link to the page with booking form, ', 'wpdev-booking'),'<code>[link_to_booking_resource]</code>');?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - availability of booking resource, ', 'wpdev-booking'),'<code>[num_available_resources]</code>');?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - maximum support visitors for booking resource, ', 'wpdev-booking'),'<code>[max_visitors]</code>');?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - cost of booking resource, ', 'wpdev-booking'),'<code>[standard_cost]</code>');?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - featured image, getted as a featured image from post, ', 'wpdev-booking'),'<code>[booking_featured_image]</code>');?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - booking info, getted as a excerpt from post, ', 'wpdev-booking'),'<code>[booking_info]</code>');?></span><br/><br/>

                                          <span class="description"><?php printf(__('%s - new line, ', 'wpdev-booking'),'<code>&lt;br/&gt;</code>');?></span> 
                                          <span class="description"><?php printf(__('use any other HTML tags (carefully).', 'wpdev-booking'),'<code>','</code>');?></span><br/><br/>
                                          <?php make_bk_action('show_additional_translation_shortcode_help'); ?>
                                        </div>
                                    </div>

                                    <div style="clear:both;margin:10px 0px;width:100%;" class="code_description">
                                        <div class="shortcode_help_section" style="padding:10px;">
                                            <span class="description"><?php _e('Additional customization style of this element you can make at this file', 'wpdev-booking'); echo ': <code>', WPDEV_BK_PLUGIN_URL, '/css/client.css</code>' ?></span>
                                        </div>
                                    </div>

                                    <div class="clear" style="height:1px;"></div>
                                    <input class="button-secondary" style="float:left;" type="button" value="<?php _e('Reset to default value', 'wpdev-booking'); ?>" onclick="javascript:reset_to_def_found_search_item();" name="reset_form"/>
                                    <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="Submit"/>
                                    <div class="clear" style="height:5px;"></div>

                                </form>
                     </div></div></div>
                    <?php

          }

                  // Get default search forms 
                  function get_default_booking_search_form_show(){
                      return '<h3>'.__('Search form', 'wpdev-booking').'</h3><br />\n\
\n\
<label>'.__('Check in', 'wpdev-booking').':</label> [search_check_in]\n\
\n\
<label>'.__('Check out', 'wpdev-booking').':</label> [search_check_out]\n\
\n\
<label>'.__('Number of visitors', 'wpdev-booking').':</label> [search_visitors] <br />\n\
\n\
[search_button]';
                  }

                  function get_default_booking_found_search_item(){
                      return    '<div style="float:right;"><div>from [standard_cost]</div> [link_to_booking_resource]</div>\n\
\n\
[booking_resource_title]\n\
\n\
[booking_featured_image]\n\
\n\
[booking_info]\n\
\n\
<div>Availability: [num_available_resources] item(s). Max. persons: [max_visitors]</div>';
                  }



                  // Show Advanced settings at the bootom of Resource Settings page
                  function show_search_cache_settings() {

                      global $wpdb;

                      if(isset($_POST['cache_expiration'])) {
                          update_bk_option( 'booking_cache_expiration' ,  $_POST['cache_expiration'] );
                      }

                      $cache_expiration   = get_bk_option( 'booking_cache_expiration');


                      // Cache Reset                      
                      if (isset($_GET['cache_reset']))
                          if ($_GET['cache_reset'] == '1') 
                            $this->regenerate_booking_search_cache();
                              
                      $link = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-option&tab=search';
                      ?>
                                        <div class='meta-box'>
                                          <div <?php $my_close_open_win_id = 'bk_settings_search_cache_settings'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');"><br></div>
                                                <h3 class='hndle'><span><?php _e('Search Cache Settings', 'wpdev-booking'); ?></span></h3> <div class="inside">
                                                <form  name="post_option_resources_adv" action="" method="post" id="post_option_resources_adv" >

                                                <table class="form-table"><tbody>

                                                <tr valign="top">
                                                    <th scope="row"><label for="start_day_weeek" ><?php _e('Cache expiration', 'wpdev-booking'); ?>:</label></th>
                                                    <td>
                                                        <select id="cache_expiration" name="cache_expiration">

                            <?php for ($mm = 1; $mm < 25; $mm++) { ?>
                                                            <option <?php if($cache_expiration == $mm .'h') echo "selected"; ?> value="<?php echo $mm; ?>h"><?php echo $mm ,' ';
                                _e('hour(s)', 'wpdev-booking'); ?></option>
                                <?php } ?>

                            <?php for ($mm = 1; $mm < 32; $mm++) { ?>
                                                            <option <?php if($cache_expiration == $mm .'d') echo "selected"; ?> value="<?php echo $mm; ?>d"><?php echo $mm ,' ';
                                _e('day(s)', 'wpdev-booking'); ?></option>
                                <?php } ?>

                                                        </select>
                                                        <span class="description"><?php _e('Select time of cache expiration', 'wpdev-booking');?></span>
                                                    </td>
                                                </tr>

                                                </tbody></table>


                                    <div style="float:right;margin:10px 0px;width:100%;" class="code_description">
                                        <div class="shortcode_help_section">
                                          <span class="description"><?php printf(__('Cache is expire: ', 'wpdev-booking'));?></span><?php 
                                                /*echo get_bk_option( 'booking_cache_created'), ' - ',
                                                     get_bk_option( 'booking_cache_expiration'     ), ' - ',
                                                     mktime(), ' - ',
                                                     $this->is_booking_search_cache_expire();*/

                                            $period =  get_bk_option( 'booking_cache_expiration'     );
                                            if (substr($period,-1,1) == 'd' ) {
                                                $period = substr($period,0,-1);
                                                $period = $period * 24 * 60 * 60;
                                            }
                                            if (substr($period,-1,1) == 'h' ) {
                                                $period = substr($period,0,-1);
                                                $period = $period * 60 * 60;
                                            }

        
                                              $previos = get_bk_option( 'booking_cache_created'     );
                                              $previos = explode(' ',$previos);
                                              $previos_time = explode(':',$previos[1]);
                                              $previos_date = explode('-',$previos[0]);
                                              $previos_sec = mktime($previos_time[0], $previos_time[1], $previos_time[2], $previos_date[1], $previos_date[2], $previos_date[0]);

                                              $expire_sec = ($previos_sec+$period);

                                              $cache_epire_on = date_i18n('Y-m-d H:i:s T', $expire_sec  ) ;
                                              echo $cache_epire_on;

                                              //$booking_time_format = get_bk_option( 'booking_time_format');
                                              //echo '\n' . $booking_time_format . ' ' . date_i18n( $booking_time_format ) . "\n";
                                            ?><br />
                                          <?php
                                                $found_records =  get_bk_option( 'booking_cache_content');
                                                if (! empty($found_records)) {
                                                        if (is_serialized($found_records)) $found_records = @unserialize($found_records);
                                                        $found_records = count($found_records);
                                                } else  $found_records = 0;
                                          ?>
                                          <span class="description"><?php printf(__('Found: %s booking forms inside of posts or pages ', 'wpdev-booking'),'<code>'.$found_records.'</code>');?></span><br/><br/>
                                        </div>
                                    </div>



                                                <div class="clear" style="height:10px;"></div>
                                                <input class="button-secondary" style="float:left;" type="button" value="<?php _e('Reset cache', 'wpdev-booking'); ?>" onclick="javascript:window.location.href='<?php echo $link ;?>&cache_reset=1';" name="reset_form"/>
                                                <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="submit_advanced_resources_settings"/>
                                                <div class="clear" style="height:10px;"></div>

                                            </form>

                                       </div> </div> </div>

                      <?php
                  }



           // Resources settings MENU //
           function resources_settings_after_title( $bt, $all_id, $alternative_color ){
                
                
           }

           // Show headers collumns
           //MAXIME RAJOUT CHAMPS MAX-VISITOR
           function resources_settings_table_headers(){
                if (isset($_GET['tab'])) if ( ($_GET['tab']=='availability') || ($_GET['tab']=='cost')  ) return ;
                $is_use_visitors_number_for_availability   = get_bk_option( 'booking_is_use_visitors_number_for_availability');
              ?>
                <!--th style="width:50px; " rel="tooltip" class="tooltip_bottom"  title="<?php _e('Number of resource items inside of parent resource', 'wpdev-booking');?>"> <?php _e('Capacity', 'wpdev-booking'); ?>  </th-->
                <th style="width:100px;text-align: center; "> <?php _e('Parent', 'wpdev-booking');   ?>  </th>
                <th style="width:50px; "> <?php _e('Priority', 'wpdev-booking'); ?> </th>
                <?php if ($is_use_visitors_number_for_availability == 'On') { ?>
                <th style="width:50px;white-space: nowrap; " rel="tooltip" class="tooltip_bottom"  title="<?php _e('Maximum number of visitors for resource', 'wpdev-booking');?>"> <?php _e('Max', 'wpdev-booking'); echo ' '; _e('visitors', 'wpdev-booking'); ?> </th>
                
                <th style="width:50px;white-space: nowrap;" rel="tooltip" class="tooltip_bottom"  title="<?php _e('Maximum number of visitors per Visit', 'wpdev-booking');?>">
                    <?php _e('Max', 'wpdev-booking'); echo ' '; _e('visitors per Visit', 'wpdev-booking'); ?>
                </th>

                <?php } ?>
                <!--th style="text-align: center;"> <?php _e('Actions', 'wpdev-booking'); ?> </th-->
                <?php make_bk_action('show_users_header_at_settings' ); ?>

              <?php
           }

           // Show headers collumns
           function resources_settings_table_footers(){
                if (isset($_GET['tab'])) if ( ($_GET['tab']=='availability') || ($_GET['tab']=='cost')  ) return ;
                $is_use_visitors_number_for_availability   = get_bk_option( 'booking_is_use_visitors_number_for_availability');
              ?>
                <td style="border-top: 1px solid #ccc;text-align:center;font-weight:bold;"></td>
                <td style="border-top: 1px solid #ccc;text-align:center;font-weight:bold;"></td>

                <!--td style="width:50px;border-top: 1px solid #ccc;"></td-->
                <!--td style="width:100px;border-top: 1px solid #ccc;"></tdresources_settings_table_collumns>
                <td style="width:50px;border-top: 1px solid #ccc;"></td-->
                <?php if ($is_use_visitors_number_for_availability == 'On') { ?>
                <td style="width:50px;border-top: 1px solid #ccc;"></td>
                <?php } ?>
                <?php make_bk_action('show_users_footer_at_settings' ); ?>

              <?php
           }

           // Show Resources Collumns
           //MAXIME RAJOUT CHAMPS MAX-VISITOR
           function resources_settings_table_collumns( $bt, $all_id, $alternative_color ){
               if (isset($_GET['tab'])) if ( ($_GET['tab']=='availability') || ($_GET['tab']=='cost')  ) return ;
                $is_use_visitors_number_for_availability   = get_bk_option( 'booking_is_use_visitors_number_for_availability');
                $my_count = $bt->count;
                ?>

                    <?php // Show CAPACITY  ?>
                    <!--td style="text-align:center;font-weight: bold;" <?php echo $alternative_color; ?> ><?php if ($bt->parent == 0 ) { echo $my_count; }?></td-->

                    <?php // Show Parent selection  ?>
                    <td style="text-align:center;border-left:1px solid #ccc;" <?php echo $alternative_color; ?> >
                        <select  style="width:90px;"  name="type_parent<?php echo $bt->id; ?>" id="type_parent<?php echo $bt->id; ?>">
                            <?php foreach ($all_id as $m_id) { ?>
                                <option <?php if ( $bt->parent==$m_id['id']) echo 'selected="SELECTED"' ?> value="<?php echo $m_id['id']; ?>"><?php echo $m_id['title'] ?></option>
                            <?php } ?>
                        </select>
                    </td>


                    <?php // Show Priority  ?>
                    <td style="text-align:center;" <?php echo $alternative_color; ?> >

                        <input  maxlength="17" type="text"
                                        style="width:50px;font-size:11px;"
                                        value="<?php echo $bt->prioritet; ?>"
                                        name="type_prioritet<?php echo $bt->id; ?>" id="type_prioritet<?php echo $bt->id; ?>" />

                    </td>


                    <?php // Show MAX Visitors  ?>
                    <?php if ($is_use_visitors_number_for_availability == 'On') { ?>

                    <td style="text-align:center;" <?php echo $alternative_color; ?> >
                        <?php if ($bt->parent == 0 ) { ?>
                            <select <?php if ($bt->parent != 0 ) { echo ' disabled="DISABLED" '; } ?>  style="width:50px;"   name="type_max_visitors<?php echo $bt->id; ?>" id="type_max_visitors<?php echo $bt->id; ?>">
                                <?php for ($m_id = 1; $m_id < 13; $m_id++) { ?>
                                    <option <?php if ( $bt->visitors==$m_id) echo 'selected="SELECTED"' ?> value="<?php echo $m_id; ?>"><?php echo $m_id ?></option>
                                <?php } ?>
                            </select>
                        <?php } else { ?>
                            <span style="font-size:10px;font-weight:bold;"><?php echo $bt->visitors; ?></span>
                        <?php } ?>
                    </td>

                    <?php } ?>


                    <?php /*/ Show Add / Delete Button  ?>
                    <td style="font-size:11px;text-align: center;" <?php echo $alternative_color; ?>

                        <?php
                        $max_num = apply_bk_filter('get_max_res_num_for_user_in_multiuser', false );
                        if ( ($max_num === false) || ($max_num > count($bk_types) ) )
                            if ($bt->parent==0) { ?>

                                <div style="height:20px;">
                                    <input class="button" style="margin:0px 10px;" type="button" value="+ <?php _e('Add', 'wpdev-booking'); ?>"
                                       onclick="javascript:
                                               document.getElementById('type_title_new').value='<?php echo $bt->title . '-' . ($my_count+1) ; ?>';
                                           document.getElementById('type_parent_new').value='<?php echo $bt->id  ; ?>';
                                           document.getElementById('type_prioritet_new').value='<?php echo  ($my_count+1)   ; ?>';
                                           document.getElementById('submit_resources_button').click();
                                               " />
                                </div>
                              <?php } ?>

                        <span style="line-height:25px;"><?php _e('Delete','wpdev-booking'); ?>: </span>
                        <input class="checkbox"  type="checkbox"   name="type_delete<?php echo $bt->id; ?>" id="type_delete<?php echo $bt->id; ?>"/>

                    </td>
                    <?php /**/ ?>

                    <?php // Show Max Visitor per Visit  ?>
                    <td style="text-align:center;border-left:1px solid #ccc;" <?php echo $alternative_color; ?> >
                        <input type="text" style="width:50px;"  name="number_of_visitor_max<?php echo $bt->id; ?>" id="number_of_visitor_max<?php echo $bt->id; ?>" value="<?php echo $bt->maxvisitor; ?>" />
                        <script type="text/javascript">new SUC(document.getElementById("number_of_visitor_max<?php echo $bt->id; ?>") );</script>
                    </td>

                    <?php make_bk_action('show_users_collumn_at_settings', $bt , $alternative_color ); ?>

                    <?php
           }

           function resources_settings_table_info_collumns( $bt, $all_id, $alternative_color ){
                //if (isset($_GET['tab'])) if ( ($_GET['tab']=='availability') || ($_GET['tab']=='cost')  ) return ;
                if ( ($bt->parent == 0 ) && ($bt->count>1) ) {
                    ?><span style="
    background:none repeat scroll 0 0 #E67622;
    border:1px solid #E8712D;
    border-radius:7px 7px 7px 7px;
    -moz-border-radius:7px 7px 7px 7px;
    -webkit-border-radius:7px 7px 7px 7px;
    box-shadow:0 1px 0 #CCCCCC;
    -moz-box-shadow:0 1px 0 #CCCCCC;
    -webkit-box-shadow:0 1px 0 #CCCCCC;
    color:#FFFFFF;
    font-size:10px;
    font-weight:bold;
    letter-spacing:1px;
    line-height:25px;
    padding:1px 5px;
    text-shadow:0 -1px 0 #855050;"><?php
                    _e('Capacity: ', 'wpdev-booking');
                    echo $bt->count;
                    ?></span><?php
                }

           }

           // Show fields for ADD Button at the bottom of table
           //MAXIME RAJOUT CHAMPS MAX-VISITOR
           function resources_settings_table_add_bottom_button($all_id){
                ?>
                    <tr> <td colspan="2">

                    <table style="width:100%;border:none;border-top: 1px solid #ccc;margin-top:10px;" cellpadding="0" cellspacing="0" id="resource-add-new-advanced-options">

                    <tr>
                        <th style="width:16%;">
                        <h2 class="settings-resource-title"  style="font-size:17px !important;line-height:15px !important;margin:0 !important;"><?php _e('Parent','wpdev-booking'); ?>:</h2>
                        <div  class="settings-resource-label"><?php _e('Select parent resource, if you want that parent resource is increase capacity.', 'wpdev-booking'); ?></div>
                      </th>
                        <td style="width:17%;">
                          <select   name="type_parent_new" id="type_parent_new" >
                            <?php foreach ($all_id as $m_id) { ?>
                                <option  value="<?php echo $m_id['id']; ?>"><?php echo $m_id['title'] ?></option>
                            <?php } ?>
                          </select>
                        </td>


                      <th style="width:16%;">
                        <h2 class="settings-resource-title"  style="font-size:17px !important;line-height:15px !important;margin:0 !important;"><?php _e('Priority','wpdev-booking'); ?>:</h2>
                        <div  class="settings-resource-label"><?php _e('Set priority of resource - resource with higher priority will be resrved firstly.', 'wpdev-booking'); ?></div>
                      </th>
                      <td style="width:16%;">
                        <select  style="width:50px;"   name="type_prioritet_new" id="type_prioritet_new">
                            <?php for ($m_id = 0; $m_id < 500; $m_id++) { ?>
                                <option value="<?php echo $m_id; ?>"><?php echo $m_id ?></option>
                            <?php } ?>
                        </select>
                      </td>


                    <?php
                        $types_list = $this->get_booking_types();
                        $max_num = apply_bk_filter('get_max_res_num_for_user_in_multiuser', false );
                        if  ($max_num === false) $max_num = 501;
                        else {
                            $max_num = $max_num - count($types_list)+1;
                        }
                    ?>

                      <th style="width:16%;">
                        <h2 class="settings-resource-title"  style="font-size:17px !important;line-height:15px !important;margin:0 !important;"><?php _e('Resources count','wpdev-booking'); ?>:</h2>
                        <div  class="settings-resource-label"><?php _e('Create seeral booking resources for one time', 'wpdev-booking'); ?></div>
                      </th>
                      <td >
                        <select  style="width:50px;"   name="type_number_of_resources" id="type_number_of_resources">
                            <?php for ($m_id = 1; $m_id < $max_num; $m_id++) { ?>
                                <option value="<?php echo $m_id; ?>"><?php echo $m_id ?></option>
                            <?php } ?>
                        </select>
                      </td>

                      <td>
                    </tr>

                    <tr>
                        <th style="width:16%;"></th>
                        <td></td>

                        <th style="width:16%;">
                            <h2 class="settings-resource-title"  style="font-size:17px !important;line-height:15px !important;margin:0 !important;"><?php _e('Max visitor','wpdev-booking'); ?>:</h2>
                            <div  class="settings-resource-label"><?php _e('Choose the max visitor for one visit', 'wpdev-booking'); ?></div>
                        </th>
                        <td >
                            <input type="text" style="width:100px;" maxlength="3" name="type_number_of_max_visitor" id="type_number_of_max_visitor" />
                        </td>
                    </tr>



                    </table>
                           
                    </td> </tr>
            <?php
           }


                    // Update SQL dfor editing bk resources
           //MAXIME RAJOUT CHAMPS MAX-VISITOR
                    function get_sql_4_update_bk_resources($blank, $bt){

                        $sql_res = '';
                        $is_use_visitors_number_for_availability   = get_bk_option( 'booking_is_use_visitors_number_for_availability');

                        if ($is_use_visitors_number_for_availability == 'On') {

                          if ( $_POST['type_parent'.$bt->id] != 0 )     // Set for Child objects, value of Parent objects
                              $vis_update_string = " , visitors = '". $_POST['type_max_visitors'. $_POST['type_parent'.$bt->id] ] ."' " ;
                          else                                          // Set for Parent objects - normal value
                              $vis_update_string = " , visitors = '".$_POST['type_max_visitors'.$bt->id] ."' " ;

                        } else  $vis_update_string = '';

                        $sql_res = " , parent = '".$_POST['type_parent'.$bt->id]."' , prioritet = '".$_POST['type_prioritet'.$bt->id]."', maxvisitor = '".$_POST['number_of_visitor_max'.$bt->id]."' ".$vis_update_string."  ";

                        return $sql_res;
                    }

                    // Get Fields and Values for Insert new resource
                    //MAXIME RAJOUT CHAMPS MAX-VISITOR
                    function get_sql_4_insert_bk_resources_fields( $blank ){
                      return ', parent, prioritet, maxvisitor ';
                    }
                    //MAXIME RAJOUT CHAMPS MAX-VISITOR
                    function get_sql_4_insert_bk_resources_values( $blank  , $sufix){

                      if (empty($sufix)) {
                        return  ' , "'. $_POST['type_parent_new'] .'" , "' . $_POST['type_prioritet_new'] .'", "'.$_POST['type_number_of_max_visitor'].'"';
                      } else {

                          $prio = $_POST['type_prioritet_new'] + $sufix;
                          return  ' , "'. $_POST['type_parent_new'] .'" , "' . $prio .'"';
                      }

                    }

                    function insert_bk_resources_recheck_max_visitors(){
                          global $wpdb;
                          if (isset(  $_POST['type_parent_new'] )) {
                              $booking_id = (int) $wpdb->insert_id;       //Get ID

                              $booking_visitor_num = $this->get_max_visitors_for_bk_resources($_POST['type_parent_new']);
                              if (isset($booking_visitor_num[$_POST['type_parent_new']]))
                                  $booking_visitor_num  = $booking_visitor_num[$_POST['type_parent_new']];
                              else $booking_visitor_num =1;

                              if ( false === $wpdb->query($wpdb->prepare(
                                      "UPDATE ".$wpdb->prefix ."bookingtypes SET visitors = '". $booking_visitor_num ."' WHERE booking_type_id = " .  $booking_id) ) ){
                                  bk_error('Error during updating to DB booking resources' ,__FILE__,__LINE__);
                              }
                          }

                    }
        /////////////////////////////////////////////////////////////////////////////////////

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="A C T I V A T I O N   A N D   D E A C T I V A T I O N">

    //   A C T I V A T I O N   A N D   D E A C T I V A T I O N    O F   T H I S   P L U G I N  ///////////////////////////////////////////////////

        // Activate
        function pro_activate() {

               // add_bk_option( 'booking_maximum_selection_days_for_one_resource', 'Off');
               // add_bk_option( 'booking_maximum_selection_days_for_one_resource', 4);
                add_bk_option( 'booking_is_use_visitors_number_for_availability','Off');
                add_bk_option( 'booking_is_show_availability_in_tooltips','Off');
                add_bk_option( 'booking_highlight_availability_word' , __('Available: ','wpdev-booking')  );
                add_bk_option( 'booking_availability_based_on' ,  'items' );
                add_bk_option( 'booking_is_dissbale_booking_for_different_sub_resources', 'Off' );

                add_bk_option( 'booking_search_form_show' , str_replace('\\n\\','',$this->get_default_booking_search_form_show()));
                add_bk_option( 'booking_found_search_item' ,str_replace('\\n\\','',$this->get_default_booking_found_search_item()));
                add_bk_option( 'booking_cache_expiration', '2d');

                $this->regenerate_booking_search_cache();

                if ( wpdev_bk_is_this_demo() )
                    update_bk_option( 'booking_form', str_replace('\\n\\','', $this->reset_to_default_form('payment') ) );

                global $wpdb;
                $charset_collate = '';
                //if ( $wpdb->has_cap( 'collation' ) ) {
                            if ( ! empty($wpdb->charset) ) $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
                            if ( ! empty($wpdb->collate) ) $charset_collate .= " COLLATE $wpdb->collate";
                //}

               if  ($this->is_field_in_table_exists('bookingtypes','prioritet') == 0){
                    $simple_sql = "ALTER TABLE ".$wpdb->prefix ."bookingtypes ADD prioritet INT(4) DEFAULT '0'";
                    $wpdb->query($wpdb->prepare($simple_sql));
               }
               if  ($this->is_field_in_table_exists('bookingtypes','parent') == 0){
                    $simple_sql = "ALTER TABLE ".$wpdb->prefix ."bookingtypes ADD parent bigint(20) DEFAULT '0'";
                    $wpdb->query($wpdb->prepare($simple_sql));
               }
               if  ($this->is_field_in_table_exists('bookingtypes','visitors') == 0){
                    $simple_sql = "ALTER TABLE ".$wpdb->prefix ."bookingtypes ADD visitors bigint(20) DEFAULT '1'";
                    $wpdb->query($wpdb->prepare($simple_sql));
               }
               if  ($this->is_field_in_table_exists('bookingdates','type_id') == 0){
                    $simple_sql = "ALTER TABLE ".$wpdb->prefix ."bookingdates ADD type_id bigint(20)";
                    $wpdb->query($wpdb->prepare($simple_sql));
               }
                if  ($this->is_index_in_table_exists('bookingdates','booking_id_dates_types') == 0) {
                    $simple_sql = "CREATE UNIQUE INDEX booking_id_dates_types ON ".$wpdb->prefix ."bookingdates (booking_id, booking_date, type_id);";
                    $wpdb->query($wpdb->prepare($simple_sql));
                }

                // Booking Types   M E T A  table
                if ( ( ! $this->is_table_exists('booking_coupons')  )) { // Cehck if tables not exist yet

                        $wp_queries=array();
                        $wp_queries[] = "CREATE TABLE ".$wpdb->prefix ."booking_coupons (
                             coupon_id bigint(20) unsigned NOT NULL auto_increment,
                             coupon_active int(10) NOT NULL default 1,
                             coupon_code varchar(200) NOT NULL default '',
                             coupon_value FLOAT(7,2) NOT NULL DEFAULT 0.00,
                             coupon_type varchar(200) NOT NULL default '',
                             expiration_date datetime,
                             coupon_min_sum FLOAT(7,2) NOT NULL DEFAULT 0.00,
                             support_bk_types text ,
                             PRIMARY KEY  (coupon_id)
                            ) $charset_collate;";

                        foreach ($wp_queries as $wp_q) $wpdb->query($wpdb->prepare($wp_q));
                }



            // Demo settings
            if ( wpdev_bk_is_this_demo() )          {

                    update_bk_option( 'booking_is_show_availability_in_tooltips' , 'On' );
                    update_bk_option( 'booking_is_use_visitors_number_for_availability','On');
                    update_bk_option( 'booking_skin', WPDEV_BK_PLUGIN_URL . '/inc/skins/premium-marine.css');
                    
                    $wp_queries = array();
                    $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET title = '". __('Standard', 'wpdev-booking') ."' WHERE title = '". __('Default', 'wpdev-booking') ."' ;";
                    $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET title = '". __('Superior', 'wpdev-booking') ."' WHERE title = '". __('Resource #1', 'wpdev-booking') ."' ;";
                    $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET title = '". __('Presidential Suite', 'wpdev-booking') ."' WHERE title = '". __('Resource #2', 'wpdev-booking') ."' ;";
                    $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET title = '". __('Royal Villa', 'wpdev-booking') ."' WHERE title = '". __('Resource #3', 'wpdev-booking') ."' ;";


                    $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET visitors = '2' WHERE title = '". __('Standard', 'wpdev-booking') ."' ;";
                    $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET visitors = '3' WHERE title = '". __('Superior', 'wpdev-booking') ."' ;";
                    $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET cost = '150', visitors = '4' WHERE title = '". __('Presidential Suite', 'wpdev-booking') ."' ;";
                    $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET cost = '500', visitors = '5' WHERE title = '". __('Royal Villa', 'wpdev-booking') ."' ;";

                    $wp_queries[] = 'DELETE FROM '.$wpdb->prefix .'booking_types_meta ';

                    $wp_queries[] = 'INSERT INTO '.$wpdb->prefix .'booking_types_meta (  type_id, meta_key, meta_value ) VALUES ( 4, "rates", "a:3:{s:6:\"filter\";a:3:{i:3;s:3:\"Off\";i:2;s:3:\"Off\";i:1;s:2:\"On\";}s:4:\"rate\";a:3:{i:3;s:1:\"0\";i:2;s:1:\"0\";i:1;s:3:\"200\";}s:9:\"rate_type\";a:3:{i:3;s:1:\"%\";i:2;s:1:\"%\";i:1;s:1:\"%\";}}" );';
                    $wp_queries[] = 'INSERT INTO '.$wpdb->prefix .'booking_types_meta (  type_id, meta_key, meta_value ) VALUES ( 3, "costs_depends", "a:3:{i:0;a:6:{s:6:\"active\";s:2:\"On\";s:4:\"type\";s:1:\">\";s:4:\"from\";s:1:\"1\";s:2:\"to\";s:1:\"2\";s:4:\"cost\";s:3:\"250\";s:13:\"cost_apply_to\";N;}i:1;a:6:{s:6:\"active\";s:2:\"On\";s:4:\"type\";s:1:\"=\";s:4:\"from\";s:1:\"3\";s:2:\"to\";s:1:\"3\";s:4:\"cost\";s:3:\"200\";s:13:\"cost_apply_to\";s:5:\"fixed\";}i:2;a:6:{s:6:\"active\";s:2:\"On\";s:4:\"type\";s:4:\"summ\";s:4:\"from\";s:1:\"4\";s:2:\"to\";s:1:\"2\";s:4:\"cost\";s:3:\"875\";s:13:\"cost_apply_to\";s:5:\"fixed\";}}" );';
                                                                                                                                                       

                    //$wp_queries[] = 'INSERT INTO '.$wpdb->prefix .'booking_types_meta (  type_id, meta_key, meta_value ) VALUES ( 2, "availability", "a:2:{s:7:\"general\";s:2:\"On\";s:6:\"filter\";a:3:{i:3;s:3:\"Off\";i:2;s:3:\"Off\";i:1;s:2:\"On\";}}" );';

                    foreach ($wp_queries as $wp_q) $wpdb->query($wp_q);

            }




             // Insert Default child objects

             $my_sql = array();

             $child_resources = $wpdb->get_results($wpdb->prepare( "SELECT booking_type_id FROM ".$wpdb->prefix ."bookingtypes  WHERE parent = 1"));
             $child_1 = $wpdb->get_results($wpdb->prepare( "SELECT title FROM ".$wpdb->prefix ."bookingtypes  WHERE booking_type_id = 1"));

             if ( (count($child_resources)==0) && (count($child_1)>0)  )
                for ($i = 1; $i < 6; $i++)
                    $my_sql[] = 'INSERT INTO '.$wpdb->prefix .'bookingtypes ( title, parent, cost, prioritet ) VALUES ( "'.
                                                                  $child_1[0]->title .'-'.$i  .'", "1", "25", "'.$i.'") ' ;


             $child_resources = $wpdb->get_results($wpdb->prepare( "SELECT booking_type_id FROM ".$wpdb->prefix ."bookingtypes  WHERE parent = 2"));
             $child_2 = $wpdb->get_results($wpdb->prepare( "SELECT title FROM ".$wpdb->prefix ."bookingtypes  WHERE booking_type_id = 2"));
             
             if ( (count($child_resources)==0) && (count($child_2)>0)  ){
                for ($i = 1; $i < 4; $i++)
                    $my_sql[] = 'INSERT INTO '.$wpdb->prefix .'bookingtypes ( title, parent, cost, prioritet ) VALUES ( "'.
                                                                  $child_2[0]->title .'-'.$i  .'", "2", "50", "'.$i.'") ' ;
                if ( $child_2[0]->title == __('Superior', 'wpdev-booking') )
                    $my_sql[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET cost = '50' WHERE title = '". __('Superior', 'wpdev-booking') ."' ;";

             }
             
             foreach ($my_sql as $wp_q)
                if ( false === $wpdb->query($wpdb->prepare( $wp_q )) ) { bk_error('Error during updating to DB booking resources',__FILE__,__LINE__ ); }


             // Set default number of support visitors at child objects for demo site
             if ( wpdev_bk_is_this_demo() )         {
                    $wp_queries = array();
                    $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET visitors = '2' WHERE title LIKE '". __('Standard', 'wpdev-booking') ."-%' ;";
                    $wp_queries[] = "UPDATE ".$wpdb->prefix ."bookingtypes SET visitors = '3' WHERE title LIKE '". __('Superior', 'wpdev-booking') ."-%' ;";
                    foreach ($wp_queries as $wp_q) $wpdb->query($wp_q);
              }

        }

        //Decativate
        function pro_deactivate(){
            global $wpdb;
           // delete_bk_option( 'booking_maximum_selection_days_for_one_resource');
                delete_bk_option( 'booking_is_use_visitors_number_for_availability');
                delete_bk_option( 'booking_is_show_availability_in_tooltips');
                delete_bk_option( 'booking_highlight_availability_word');
                delete_bk_option( 'booking_availability_based_on'  );
                delete_bk_option( 'booking_is_dissbale_booking_for_different_sub_resources'  );

                delete_bk_option( 'booking_search_form_show' );
                delete_bk_option( 'booking_found_search_item' );
                delete_bk_option( 'booking_cache_expiration');

                delete_bk_option( 'booking_cache_content'  );
                delete_bk_option( 'booking_cache_created'  );

                $wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'booking_coupons'));
       }

    // </editor-fold>

    }
}
?>