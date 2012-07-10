<?php
/*
This is COMMERCIAL SCRIPT
We are do not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.

*/
if (  (! isset( $_GET['merchant_return_link'] ) ) && (! isset( $_GET['payed_booking'] ) ) && (!function_exists ('get_option')  )  ) { die('You do not have permission to direct access to this file !!!'); }
require_once(WPDEV_BK_PLUGIN_DIR. '/inc/lib_p.php' );
if (file_exists(WPDEV_BK_PLUGIN_DIR. '/inc/biz_s.php')) { require_once(WPDEV_BK_PLUGIN_DIR. '/inc/biz_s.php' ); }

// Load Country list
if (file_exists(WPDEV_BK_PLUGIN_DIR. '/languages/wpdev-country-list.php')) { require_once(WPDEV_BK_PLUGIN_DIR. '/languages/wpdev-country-list.php' ); }

if (!class_exists('wpdev_bk_personal')) {
    class wpdev_bk_personal   {

        var $current_booking_type;
        var $wpdev_bk_biz_s;
        var $current_edit_booking;
        var $countries_list;

        function wpdev_bk_personal() {
            $this->current_booking_type = 1;
            $this->current_edit_booking = false;

            add_bk_filter('get_bk_dates_sql', array(&$this, 'get_bk_dates_4_edit'));  // At hotel edition already edit it

            add_bk_action('show_remark_editing_field', array(&$this, 'show_remark_editing_field'));  // Show fields for editing
            // add_bk_action('show_remark_hint', array(&$this, 'show_remark_hint'));                    // Show reamrk hints
            add_bk_action('wpdev_updating_remark', array(&$this, 'wpdev_updating_remark'));          // Ajax POST request for updating remark
            add_bk_action('wpdev_make_update_of_remark', array(&$this, 'wpdev_make_update_of_remark'));          // Ajax POST request for updating remark

            add_bk_action('wpdev_updating_bk_resource_of_booking', array(&$this, 'wpdev_updating_bk_resource_of_booking'));          // Ajax POST request for updating remark


            add_bk_action('wpdev_delete_booking_by_visitor', array(&$this, 'delete_booking_by_visitor'));          // Ajax POST request for updating remark
            add_bk_action('wpdev_booking_settings_show_content', array(&$this, 'settings_menu_content'));

            add_bk_action('wpdev_bk_general_settings_edit_booking_url', array(&$this, 'settings_edit_booking_url'));
            add_bk_action('wpdev_bk_general_settings_set_default_booking_resource', array(&$this, 'settings_set_default_booking_resource'));
            add_bk_action('wpdev_bk_general_settings_a', array(&$this, 'settings_a'));
            add_bk_action('wpdev_booking_settings_top_menu_submenu_line', array(&$this, 'wpdev_booking_settings_top_menu_submenu_line'));

            add_bk_action('wpdev_booking_resources_show_content', array(&$this, 'wpdev_booking_resources_show_content'));


            add_action('settings_advanced_set_update_hash_after_approve', array(&$this, 'settings_advanced_set_update_hash_after_approve'));    // Write General Settings
            add_bk_action('booking_aproved', array(&$this, 'booking_aproved_afteraction'));

            add_bk_action('echoResourceMenuItem', array(&$this, 'echoResourceMenuItem'));

            add_action('wpdev_bk_js_define_variables', array(&$this, 'js_define_variables') );      // Write JS variables
            add_action('wpdev_bk_js_write_files', array(&$this, 'js_write_files') );

            add_bk_action('wpdev_booking_activation', array(&$this, 'pro_activate'));
            add_bk_action('wpdev_booking_deactivation', array(&$this, 'pro_deactivate'));

            add_bk_action('show_all_bookings_at_one_page', array(&$this, 'show_all_bookings_at_one_page'));


            add_bk_filter(   'wpdev_check_for_active_language', array(&$this, 'wpdev_check_for_active_language'));   // Check content according language shortcodes


            add_bk_action('show_additional_translation_shortcode_help', array(&$this, 'show_additional_translation_shortcode_help'));


            if ( class_exists('wpdev_bk_biz_s')) {
                    $this->wpdev_bk_biz_s = new wpdev_bk_biz_s();
            } else { $this->wpdev_bk_biz_s = false; }

            global $wpdev_booking_country_list;
            $this->countries_list = $wpdev_booking_country_list;

            add_bk_filter(   'wpdev_booking_get_hash_to_id', array(&$this, 'get_hash_to_id'));  //HASH_EDIT
            add_bk_filter(   'wpdev_booking_get_hash_using_booking_id', array(&$this, 'get_id_using_hash'));  //HASH_EDIT



            add_bk_filter(   'wpdev_booking_set_booking_edit_link_at_email', array(&$this, 'set_booking_edit_link_at_email'));

            add_bk_action('wpdev_booking_post_inserted', array(&$this, 'booking_post_inserted'));   //HASH_EDIT


            add_bk_filter(   'wpdev_is_booking_resource_exist', array(&$this, 'wpdev_is_booking_resource_exist')); // Check if this booking resource exist or not exist anymore

            add_bk_filter('get_sql_for_checking_new_bookings', array(&$this, 'get_sql_for_checking_new_bookings'));


            add_bk_action('write_content_for_popups', array(&$this, 'premium_content_for_popups'));

            // Custom buttons functions
            add_bk_action('show_tabs_inside_insertion_popup_window', array(&$this, 'show_tabs_inside_insertion_popup_window'));
            add_bk_action('show_insertion_popup_css_for_tabs', array(&$this, 'show_insertion_popup_css_for_tabs'));
            add_bk_action('show_insertion_popup_shortcode_for_bookingedit', array(&$this, 'show_insertion_popup_shortcode_for_bookingedit'));
            add_bk_action('show_additional_arguments_for_shortcode', array(&$this, 'show_additional_arguments_for_shortcode'));



            add_bk_action('wpdev_ajax_export_bookings_to_csv', array($this, 'wpdev_ajax_export_bookings_to_csv'));
            add_bk_action('wpdev_ajax_save_bk_listing_filter', array($this, 'wpdev_ajax_save_bk_listing_filter'));
            
            add_bk_filter('recheck_version', array($this, 'recheck_version'));           // check Admin pages, if some user can be there.

            }


 //  C u s t o m      b u t t o n s ///////////////////////////////////////////////////////////////////////////////////////////////////////

        function show_tabs_inside_insertion_popup_window(){
            $is_only_icons = false;
            ?>
             <div style="height:1px;clear:both;margin-top:0px;"></div>
             <div id="menu-wpdevplugin">
                <div class="nav-tabs-wrapper">
                    <div class="nav-tabs">
                        <?php $title = __('New booking', 'wpdev-booking');
                        $my_icon = 'General-setting-64x64.png'; $my_tab = 'main';  ?>
                        <a rel="tooltip" class="tooltip_bottom nav-tab  nav-tab-active" title="<?php echo __('Insertion of','wpdev-booking') .' '.strtolower($title). ' '.__('settings','wpdev-booking'); ?>"  href="javascript:void(0);"
                             onclick="javascript:
                                    document.getElementById('popup_new_reservation').style.display='block';
                                    document.getElementById('popup_edit_reservation').style.display='none';
                                    if (document.getElementById('popup_search_reservation') != undefined ) { document.getElementById('popup_search_reservation').style.display='none'; }
                                    jQuery('.nav-tab').removeClass('nav-tab-active');
                                    jQuery(this).addClass('nav-tab-active');
                                    is_booking_edit_shortcode=false;
                                    is_booking_search_shortcode=false;
                             "
                        ><img class="menuicons" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?></a>

                        <?php $title = __('Modification of exist booking', 'wpdev-booking');
                        $my_icon = 'Form-fields-64x64.png'; $my_tab = 'form';  ?>
                        <a rel="tooltip" class="nav-tab tooltip_bottom" title="<?php echo __('Inserttion of','wpdev-booking') .' '.strtolower($title). ' '.__('settings','wpdev-booking'); ?>" href="javascript:void(0);"
                            onclick="javascript:
                                    document.getElementById('popup_new_reservation').style.display='none';
                                    document.getElementById('popup_edit_reservation').style.display='block';
                                    if (document.getElementById('popup_search_reservation') != undefined ) { document.getElementById('popup_search_reservation').style.display='none'; }
                                    jQuery('.nav-tab').removeClass('nav-tab-active');
                                    jQuery(this).addClass('nav-tab-active');
                                    is_booking_edit_shortcode=true;
                                    is_booking_search_shortcode=false;
                                    "
                        ><img class="menuicons" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?></a>

                        <?php if  (class_exists('wpdev_bk_biz_l') ) { ?>
                        <?php $title = __('Search', 'wpdev-booking');
                        $my_icon = 'Booking-search-64x64.png'; $my_tab = 'search';  ?>
                        <a rel="tooltip" class="nav-tab tooltip_bottom" title="<?php echo __('Inserttion of','wpdev-booking') .' '.strtolower($title). ' '.__('settings','wpdev-booking'); ?>" href="javascript:void(0);"
                            onclick="javascript:
                                    document.getElementById('popup_new_reservation').style.display='none';
                                    document.getElementById('popup_edit_reservation').style.display='none';
                                    document.getElementById('popup_search_reservation').style.display='block';
                                    jQuery('.nav-tab').removeClass('nav-tab-active');
                                    jQuery(this).addClass('nav-tab-active');
                                    is_booking_search_shortcode=true;
                                    is_booking_edit_shortcode=false;
                                    "
                        ><img class="menuicons" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div style="height:1px;clear:both;border-top:1px solid #bbc;margin-bottom:10px;"></div>
            <?php
        }


        function show_additional_arguments_for_shortcode(){
            return;
            // agregate='2;4;5'
                                    $types_list = $this->get_booking_types(); ?>
                                    <div class="field">
                                        <div style="float:left;">
                                        <label for="agregate_bk_resources"><?php _e('Agregate resources:', 'wpdev-booking'); ?></label>
                                        <select id="agregate_bk_resources" name="agregate_bk_resources">
                                            <?php foreach ($types_list as $tl) { ?>
                                            <option value="">---</option>
                                            <option value="<?php echo $tl->id; ?>"
                                                        style="<?php if  (isset($tl->parent)) if ($tl->parent == 0 ) { echo 'font-weight:bold;'; } else { echo 'font-size:11px;padding-left:20px;'; } ?>"
                                                    ><?php echo $tl->title; ?></option>
                                            <?php } ?>
                                        </select>
                                        </div>
                                        <div style="height:1px;clear:both;"></div>
                                        <div class="description"  style="float:left;margin-left:160px;width:650px;"><?php _e('Set inside of calendar as unavailable dates, which are reserved at these booking resources also', 'wpdev-booking'); ?></div>
                                    </div>
            <?php
        }

        function show_insertion_popup_shortcode_for_bookingedit() {
            ?>
            <div id="popup_edit_reservation" style="display:none;width:650px;height:110px;">
                <p>
                    <?php printf(__('This shortcode %s is using for a page, where visitors can make %smodification%s of own booking(s), %scancel%s own booking or make %spayment%s after admin email payment request','wpdev-booking'),'<code>[bookingedit]</code>','<strong>','</strong>','<strong>','</strong>','<strong>','</strong>'); ?>.
                    <br /><br /> <?php printf(__('The content of field %sURL for bookings edit%s at %sgeneral booking settings page%s have to link to this page','wpdev-booking'), '<i>"','"</i>','<a href="admin.php?page='. WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME .'wpdev-booking-option">','</a>'); ?>.
                    <br /><br /> <?php printf(__('Emails templates, which are use shortcodes: %s will be link to this page','wpdev-booking'),  '<code>[visitorbookingediturl]</code>, <code>[visitorbookingcancelurl]</code>, <code>[visitorbookingpayurl]</code>'); ?>.
                </p>
            </div>
            <?php
            ?>
            <div id="popup_search_reservation" style="display:none;width:650px;height:110px;">
                <p>
                    <?php printf(__('This shortcode %s is using for search form','wpdev-booking'),'<code>[bookingsearch]</code>' ); ?>.
                </p>
            </div>
            <?php
        }

        function show_insertion_popup_css_for_tabs(){
            ?>
                    #menu-wpdevplugin {
                    margin-right:20px;
                    margin-top:-10px;
                    margin-bottom:0px;
                    position:relative;
                    width:auto;
                    }
                    #menu-wpdevplugin .nav-tabs-wrapper {
                    height:28px;
                    margin-bottom:-1px;
                    overflow:hidden;
                    width:100%;
                    }
                    #menu-wpdevplugin .nav-tabs {
                    float:left;
                    margin-left:0;
                    margin-right:-500px;
                    padding-left:0px;
                    padding-right:10px;
                    }
                    #menu-wpdevplugin .nav-tab {
                    -moz-border-radius:5px 5px 0 0;
                    -webkit-border-top-left-radius:5px;
                    -webkit-border-top-right-radius:5px;
                    border-color:#d5d5d5 #d5d5d5 #BBBBCC #d5d5d5;
                    border-style:solid;
                    border-width:1px 1px 0;
                    color:#C1C1C1;
                    display:inline-block;
                    font-size:12px;
                    line-height:16px;
                    margin:0 0px -1px 0;
                    padding:4px 14px 6px 32px;
                    text-decoration:none;
                    text-shadow:0 1px 0 #f1f1f1;
                    background:none repeat scroll 0 0 #F4F4F4;
                    background:url("../../../../wp-admin/images/gray-grad.png") repeat-x scroll left top #DFDFDF;
                    color:#464646;
                    font-weight:bold;
                    margin-bottom:0;

                    }
                    * html #menu-wpdevplugin .nav-tab { padding:4px 14px 5px 32px; } /* IE6 */
                    #menu-wpdevplugin a.nav-tab:hover{
                    /*
                    color:#d54e21 !important;
                    background-color: #e7e7e7 !important;/**/
                    }
                    #menu-wpdevplugin .nav-tab-active {
                    background:none repeat scroll 0 0 #ECECEC;

                    border-color:#CCCCCC;
                    border-bottom-color:#aab;
                    background:none repeat scroll 0 0 #7A7A88;

                    text-shadow:0 -1px 0 #111111;
                    border-width:1px;
                    color:#FFFFFF;
                    }
                    .menuicons{
                        position: absolute;
                        height: 20px;
                        width: 20px;
                        margin: -2px 0pt 0pt -24px;
                    }
            <?php
        }

 // S U P P O R T       F u n c t i o n s    //////////////////////////////////////////////////////////////////////////////////////////////////


        // Save the filter configuration for the booking listing in Ajax request
        function wpdev_ajax_save_bk_listing_filter(){ //get_user_option( 'booking_listing_filter_' . 'default' ) ;

            // Save filter of the booking listings
            update_user_option($_POST['user_id'], 'booking_listing_filter_' . $_POST['filter_name'] ,$_POST['filter_value']);

            ?>  <script type="text/javascript">
                    document.getElementById('ajax_message').innerHTML = '<?php echo __('Saved', 'wpdev-booking');  ?>';
                    jQuery('#ajax_message').fadeOut(1000);
                </script> <?php
            die();
        }


        function get_sql_for_checking_new_bookings($sql_req){
            global $wpdb;
            $sql_req = "SELECT bk.booking_id FROM ".$wpdb->prefix ."booking as bk
                        INNER JOIN ".$wpdb->prefix ."bookingtypes as bt
                        ON    bk.booking_type = bt.booking_type_id   WHERE  bk.is_new = 1";
            return $sql_req;
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

            $res = $wpdb->get_results( $wpdb->prepare($sql_check_table));

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


        //   M o d i f y   --   S Q L
        function get_bk_dates_4_edit($mysql, $bk_type, $approved) {

//TODO: Edited after hotel version corrections            if ( class_exists('wpdev_bk_biz_l') ) { return; } // Already exist at that class

            global $wpdb;
            if (isset($_GET['booking_hash'])) {
                $my_booking_id_type = apply_bk_filter('wpdev_booking_get_hash_to_id',false, $_GET['booking_hash'] );
                if ($my_booking_id_type !== false) {
                    $my_booking_id = $my_booking_id_type[0];
                    //$bk_type        = $my_booking_id_type[1];
                }
                $skip_bookings = ' AND bk.booking_id <>' .$my_booking_id . ' ';
            } else { $skip_bookings = ''; }

            if ($approved == 'all')
                  $sql_req =   "SELECT DISTINCT dt.booking_date

                     FROM ".$wpdb->prefix ."bookingdates as dt

                     INNER JOIN ".$wpdb->prefix ."booking as bk

                     ON    bk.booking_id = dt.booking_id

                     WHERE  dt.booking_date >= CURDATE()  AND bk.booking_type  IN ($bk_type) ".$skip_bookings."

                     ORDER BY dt.booking_date";

            else
                 $sql_req = "SELECT DISTINCT dt.booking_date

                     FROM ".$wpdb->prefix ."bookingdates as dt

                     INNER JOIN ".$wpdb->prefix ."booking as bk

                     ON    bk.booking_id = dt.booking_id

                     WHERE  dt.approved = $approved AND dt.booking_date >= CURDATE() AND bk.booking_type IN ($bk_type) ".$skip_bookings."

                     ORDER BY dt.booking_date" ;
//debuge($sql_req);
            return $sql_req;
        }

        // Check if this booking resource exist or not exist anymore
        function wpdev_is_booking_resource_exist($blank, $bk_type_id, $is_echo) {
            global $wpdb;
            $wp_q = "SELECT booking_type_id as id FROM ".$wpdb->prefix ."bookingtypes WHERE booking_type_id = ". $bk_type_id ." ;" ;
            $res = $wpdb->get_results($wpdb->prepare($wp_q));
            if (  count($res) == 0 ) {
                if ($is_echo) {
                    ?> <script type="text/javascript">
                        if (document.getElementById('booking_form_div<?php echo $bk_type_id; ?>') !== null)
                            document.getElementById('booking_form_div<?php echo $bk_type_id; ?>').innerHTML = '<?php echo __('This booking resources is not exist', 'wpdev-booking'); ?>';
                    </script> <?php
                }
                return false;
            } else {
                return true;
            }

        }



        // Check according actual language
        function wpdev_check_for_active_language($content_orig){
            
            $content=$content_orig;

            $languages = array();
            $content_ex = explode('[lang',$content);

            foreach ($content_ex as $value) {
                
                if (substr($value,0,1) == '=') {
                    
                    $pos_s = strpos($value,'=');
                    $pos_f = strpos($value,']');
                    $key = trim( substr($value, ($pos_s+1), ($pos_f-$pos_s-1) ) );
                    $value_l = trim( substr($value,  $pos_f+1  ) );
                    $languages[$key] = $value_l;

                } else  $languages['default'] = $value;
            }

            
             $locale = getBookingLocale();

            //// $locale = 'fr_FR';

            if ( isset( $languages[$locale] ) ) return $languages[$locale];
            else                                return $languages['default'];

        }


    //  E x p o r t to CSV  ///////////////////////////////////////////////////////////////////////

        function wpdev_ajax_export_bookings_to_csv(){


            wpdev_bk_show_ajax_message(  __('Processing...','wpdev-crm') , 3000, false );
            $all_booking_types = wpdebk_get_keyed_all_bk_resources(array());

            $params = str_replace('\"', '"', $_POST['csv_data']) ;
            $export_type = str_replace('\"', '"', $_POST['export_type']) ;
            $args = unserialize($params);
            if ($export_type == 'all') {
                $args['page_num']         = 1;                                      // Start export from the first page
                $args['page_items_count'] = 100000;                                 // Expot ALL bookings - Maximum: 1 000 000
            }

            $bk_listing = wpdev_get_bk_listing_structure_engine( $args );           // Get Bookings structure

            $bookings       = $bk_listing[0];
            $booking_types  = $bk_listing[1];
            $bookings_count = $bk_listing[2];
            $page_num       = $bk_listing[3];
            $page_items_count= $bk_listing[4];

            $export_collumn_titles = array();

            wpdev_bk_show_ajax_message(  __('Generating collumns...','wpdev-crm') , 3000, false );

            foreach ($bookings as $key=>$value) {
                //unset($bookings[$key]->dates);
                //unset($bookings[$key]->dates_short);
                //unset($bookings[$key]->dates_short_id);
                //unset($bookings[$key]->form_show);
                
                // Set  here booking resoutrces for the dates of reservation, which in different sub resources
                for ($ibt = 0; $ibt < count($bookings[$key]->dates_short_id); $ibt++) {
                    if (! empty($bookings[$key]->dates_short_id[$ibt]) ) {
                        $bookings[$key]->dates_short[$ibt] .= ' (' . $all_booking_types[ $bookings[$key]->dates_short_id[$ibt] ]->title . ') ';
                    }
                }

                $bookings[$key]->dates_show = implode(' ',$bookings[$key]->dates_short);

                $fields = $bookings[$key]->form_data['_all_'];
                foreach ($fields as $field_key=>$field_value) {

                    $field_key = str_replace('[', '', $field_key);
                    $field_key = str_replace(']', '', $field_key);
                    if ( substr($field_key,-1* (strlen($bookings[$key]->booking_type) ),1) == $bookings[$key]->booking_type ) {
                        $field_key = substr($field_key,0,-1* (strlen($bookings[$key]->booking_type) ));
                    }
                    if (! in_array($field_key, $export_collumn_titles))
                        $export_collumn_titles[] = $field_key;
                }
            }

            wpdev_bk_show_ajax_message(  __('Exporting booking data...','wpdev-crm') , 3000, false );
            $export_bookings = array();
            foreach ($bookings as $key=>$value) {

                $export_bk_row = array();
                $export_bk_row['dates']=$value->dates_show ;
                $export_bk_row['id']=$value->booking_id ;
                $export_bk_row['modification_date']=$value->modification_date ;
                $export_bk_row['booking_type']= $all_booking_types[$value->booking_type]->title;
                $export_bk_row['remark']=$value->remark ;
                $export_bk_row['cost']=$value->cost ;
                $export_bk_row['pay_status']=$value->pay_status ;

                $is_approved = 0;   if (count($value->dates) > 0 )     $is_approved = $value->dates[0]->approved ;
                if ($is_approved) $bk_print_status =  __('Approved', 'wpdev-booking');
                else              $bk_print_status =  __('Pending', 'wpdev-booking');
                $export_bk_row['status']= $bk_print_status;

                foreach ($export_collumn_titles as $field_key=>$field_value) {
                    if (isset($value->form_data['_all_'][ $field_value . $value->booking_type ]))
                        $export_bk_row[$field_value] = $value->form_data['_all_'][ $field_value . $value->booking_type ] ;
                    else
                        $export_bk_row[$field_value] = '';
                }

                $export_bookings[]=$export_bk_row;
            }

            // Write this collumns to the begining
            array_unshift($export_collumn_titles,'id','booking_type','status','dates','modification_date','cost','pay_status');
            $export_collumn_titles[]='remark';
            
//debuge( $export_collumn_titles, $export_bookings);
            
           wpdev_bk_show_ajax_message(  __('Generating content of file','wpdev-crm') , 3000, false );
            
           $message = wp_upload_dir();
           if ( ! empty ($message['error']) ) {
               wpdev_bk_show_ajax_message( $message['error'] , 3000, true );
               die;
           }
           $bk_baseurl = $message['baseurl'];
           $bk_upload_dir = $message['basedir'];
           $line__separator = ';';
           $csv_file_content = '';
           $write_line = '';

            // Write Titles
           foreach ($export_collumn_titles as $line) { $write_line .= "\"".$line."\"". $line__separator; }
           $write_line=substr_replace($write_line,"",-1);    // replace last charcater "," in EOL
           $write_line.= "\r\n";
           $csv_file_content .= $write_line;

           // Write Values
           foreach ($export_bookings as $line) {
               $write_line = '';

               foreach ($export_collumn_titles as $key) {    // Because titles have all keys, we loop keys from titles and then get and write values

                   if (isset( $line[$key] )) $write_line .= "\"".$line[$key]."\"". $line__separator;
                   else                      $write_line .= "\"". "\"". $line__separator;
                   
               }

               $write_line=substr_replace($write_line,"",-1);    // replace last charcater "," in EOL
               $write_line.= "\r\n";
               $csv_file_content .= $write_line;

           }
           
//debuge($csv_file_content);

           wpdev_bk_show_ajax_message(  __('Saving to file','wpdev-crm') , 3000, false );

           $dir      = $bk_upload_dir; //WP_CONTENT_DIR . '/uploads';//$_SERVER['DOCUMENT_ROOT']  ;
           $filename = 'bookings_export.csv';
           $fp =    fopen(  $dir . '/' .  $filename , 'w' );                        // Write File
           fwrite($fp, trim($csv_file_content) );
           fclose($fp);

           
           ?>




               <div id="exportBookingsModal" class="modal" >
                  <div class="modal-header">
                      <a class="close" data-dismiss="modal">&times;</a>
                      <h3><?php _e('Export bookings','wpdev-booking'); ?></h3>
                  </div>
                  <div class="modal-body">
                    <label class="help-block"><?php printf(__('Download the CSV file of exported booking data', 'wpdev-booking'),'<b>',',</b>');?></label>
                  </div>
                   <div class="modal-footer" style="text-align:center;">
                    <a href="<?php echo WPDEV_BK_PLUGIN_URL . '/inc/wpdev-get-exported-csv.php'; ?>" target="_blank"
                       onclick="//javascript:jQuery('#sendPaymentRequestModal').modal('hide');"
                       class="btn btn-primary"  style="float:none;" >
                        <?php _e('Download','wpdev-booking'); ?>
                    </a>
                    <a href="#" class="btn" style="float:none;" data-dismiss="modal"><?php _e('Close','wpdev-booking'); ?></a>
                  </div>
               </div>
               <script type="text/javascript">
                        jQuery("#exportBookingsModal").modal("show");
               </script>
           <?php
           wpdev_bk_show_ajax_message(  __('Done!','wpdev-crm') , 1000, true );

        }

 //     H   A   S   H                          //HASH_EDIT /////////////////////////////////////////////////////////////////////////////////////////

        // Get booking ID and type by booking HASH    // Edit exist booking - get ID of this booking
        function get_hash_to_id($blank, $booking_hash){

                if ($booking_hash!= '') {
                    global $wpdb;
                    $sql = "SELECT booking_id as id, booking_type as type FROM ".$wpdb->prefix ."booking as bk  WHERE  bk.hash = '$booking_hash' ";
                    $res = $wpdb->get_results($wpdb->prepare($sql));
                    if (isset($res))
                        if( (count($res>0)) && (isset($res[0]->id)) && (isset($res[0]->type)) ){
                            return array($res[0]->id, $res[0]->type);
                        }
                }
                return false;
        }

        // Get booking ID and type by booking HASH    // Edit exist booking - get ID of this booking
        function get_id_using_hash($blank, $booking_id){

                if ($booking_id!= '') {
                    global $wpdb;
                    $sql = "SELECT hash, booking_type as type FROM ".$wpdb->prefix ."booking as bk  WHERE  bk.booking_id = '$booking_id' ";
                    $res = $wpdb->get_results($wpdb->prepare($sql));

                    if (count($res>0)) {
                        return array($res[0]->hash, $res[0]->type);
                    }
                }
                return false;
        }

        // Check email body for booking editing link and replace this shortcode by link
        function set_booking_edit_link_at_email($mail_body,$booking_id ){

                    $edit_url_for_visitors = get_bk_option( 'booking_url_bookings_edit_by_visitors');
                    if (strpos($edit_url_for_visitors,'?')) {
                        $edit_url_for_visitors .= '&booking_hash=';
                    } else {
                        if (substr($edit_url_for_visitors,-1,1) != '/' ) $edit_url_for_visitors .= '/';
                        $edit_url_for_visitors .= '?booking_hash=';
                    }

                    $my_booking_id_type = apply_bk_filter('wpdev_booking_get_hash_using_booking_id',false, $booking_id );
                    $my_edited_bk_hash = '';
                    if ($my_booking_id_type !== false) {
                        $my_edited_bk_hash    = $my_booking_id_type[0];
                        $my_boook_type        = $my_booking_id_type[1];
                        $edit_url_for_visitors .= $my_edited_bk_hash;
                        //if ($my_boook_type == '') return __('Wrong booking hash in URL. Probably its expired.','wpdev-booking');
                    } else { $edit_url_for_visitors = '';}

                    $mail_body = str_replace('[visitorbookingediturl]', $edit_url_for_visitors /*'<a href= "'.$edit_url_for_visitors.'" >' . __('Edit booking','wpdev-booking') . '</a>' */ , $mail_body);

                    $mail_body = str_replace('[visitorbookingcancelurl]', $edit_url_for_visitors . '&booking_cancel=1'   , $mail_body);
                    
                    //WTB MODIF MAXIME 20/06/2012 rajout du booking id dans l'url de paiement
                    $mail_body = str_replace('[visitorbookingpayurl]', 
                            ' <a href="'. $edit_url_for_visitors . '&booking_pay=1' .'&booking_id=' .$booking_id. '" >' .__('link','wpdev-booking') .'</a> ' ,
                            $mail_body);

                    $mail_body = str_replace('[bookinghash]',$my_edited_bk_hash,$mail_body);
                    
                    return $mail_body;

        }

        // Function call after booking is inserted or modificated in post request
        function booking_post_inserted($booking_id, $booking_type='', $booking_days_count='', $times_array=''){
               global $wpdb;
 
                $update_sql = "UPDATE ".$wpdb->prefix ."booking AS bk SET bk.hash = MD5('". time() . '_' . rand(1000,1000000)."') WHERE bk.booking_id=$booking_id;";
                if ( false === $wpdb->query( $wpdb->prepare($update_sql ) )) {
                    ?> <script type="text/javascript"> document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during updating hash in BD',__FILE__,__LINE__); ?></div>'; </script> <?php
                    die();
                }/**/

        }

        // chnage hash of booking after approval process
        function booking_aproved_afteraction ( $res, $booking_form_show) {
            $is_change_hash_after_approvement = get_bk_option( 'booking_is_change_hash_after_approvement');
            if( $is_change_hash_after_approvement == 'On' )
                $this->booking_post_inserted($res->booking_id);
        }




    //  P r i n t   l o y o u t  ///////////////////////////////////////////////////////////////////////

        // write print loyout
        function premium_content_for_popups(){
            ?><div id="printLoyoutModal" class="modal" >
                  <div class="modal-header">
                      <!--a class="close" data-dismiss="modal">&times;</a-->
                      
                      <div style="text-align:right;">
                            
                            <a href="javascript:void(0);"
                               onclick="javascript:
                                           jQuery( '#print_loyout_content_action' ).print();
                                           //window.print();
                                           jQuery('#printLoyoutModal').modal('hide');
                                           jQuery('#print_loyout_content').html('');
                                               "
                                           class="btn btn-primary" >
                                <?php _e('Print','wpdev-booking'); ?>
                            </a>
                            <a href="#" class="btn" data-dismiss="modal"><?php _e('Close','wpdev-booking'); ?></a>
                      </div>
                      <h3 style="margin-top:-27px;"><?php _e('Print bookings','wpdev-booking'); ?></h3>
                  </div>
                  <div class="modal-body">
                      <div id="print_loyout_content_action" class="">
                        <div id="print_loyout_content" class="wpdevbk"> ------ </div>
                      </div>
                  </div>
                  <div class="modal-footer">
                  </div>
                </div><?php
        }


    // D e l e t e
        // Delete some bookings by visitor request of CAncellation (Ajax request)
        function delete_booking_by_visitor(){   global $wpdb;

            make_bk_action('check_multiuser_params_for_client_side', $_POST[ "bk_type"] );
            
            $booking_hash = $_POST[ "booking_hash" ];
            $my_boook_type= $_POST[ "bk_type" ];
            $denyreason = __('The booking is cancel by visitor.','wpdev-booking');

            $my_edited_bk_id = false;
            $my_booking_id_type = apply_bk_filter('wpdev_booking_get_hash_to_id',false, $booking_hash );


            if ($my_booking_id_type !== false) {
                $my_edited_bk_id        = $my_booking_id_type[0];
                $my_boook_type_new      = $my_booking_id_type[1];

                if ( ($my_boook_type_new == '') || ($my_boook_type_new == false) ) {

                    ?>
                    <script type="text/javascript">
                        document.getElementById('submiting<?php echo $my_boook_type; ?>').innerHTML = '<div class=\"submiting_content\" ><?php echo __('Wrong booking hash in URL. Probably its expired.','wpdev-booking'); ?></div>';
                        document.getElementById("submiting<?php echo $my_boook_type; ?>" ).style.display="block";
                        jQuery('#submiting<?php echo $my_boook_type; ?>').fadeOut(<?php echo get_bk_option( 'booking_title_after_reservation_time'); ?>);
                    </script>
                    <?php
                    die;
                }
                $my_boook_type = $my_boook_type_new;
            } else {
                    ?>
                    <script type="text/javascript">
                        document.getElementById('submiting<?php echo $my_boook_type; ?>').innerHTML = '<div class=\"submiting_content\" ><?php echo __('Wrong booking hash in URL. Probably its expired.','wpdev-booking'); ?></div>';
                        document.getElementById("submiting<?php echo $my_boook_type; ?>" ).style.display="block";
                        jQuery('#submiting<?php echo $my_boook_type; ?>').fadeOut(<?php echo get_bk_option( 'booking_title_after_reservation_time'); ?>);
                    </script>
                    <?php
                    die();
            }


            if ( ($my_edited_bk_id !=false) && ($my_edited_bk_id !='')) {
                $approved_id_str = $my_edited_bk_id;

                $sql = "SELECT *    FROM ".$wpdb->prefix ."booking as bk
                                    WHERE bk.booking_id IN ($approved_id_str)";

                $result = $wpdb->get_results( $wpdb->prepare($sql ) );

                $mail_sender    =  htmlspecialchars_decode( get_bk_option( 'booking_email_deny_adress') ) ;
                $mail_subject   =  htmlspecialchars_decode( get_bk_option( 'booking_email_deny_subject') );
                $mail_body      =  htmlspecialchars_decode( get_bk_option( 'booking_email_deny_content') );
                $mail_subject =  apply_bk_filter('wpdev_check_for_active_language', $mail_subject );
                $mail_body    =  apply_bk_filter('wpdev_check_for_active_language', $mail_body );

                foreach ($result as $res) {
                    // Sending mail ///////////////////////////////////////////////////////
                    if (function_exists ('get_booking_title')) $bk_title = get_booking_title( $res->booking_type );
                    else $bk_title = '';

                    $booking_form_show = get_form_content ($res->form, $res->booking_type);

                    $mail_body_to_send = str_replace('[bookingtype]', $bk_title, $mail_body);
                    if (get_bk_option( 'booking_date_view_type') == 'short') $my_dates_4_send = get_dates_short_format( get_dates_str($res->booking_id) );
                    else                                                  $my_dates_4_send = change_date_format(get_dates_str($res->booking_id));
                    $mail_body_to_send = str_replace('[dates]',$my_dates_4_send , $mail_body_to_send);
                    $mail_body_to_send = str_replace('[content]', $booking_form_show['content'], $mail_body_to_send);
                    $mail_body_to_send = str_replace('[denyreason]', $denyreason, $mail_body_to_send);
                    $mail_body_to_send = str_replace('[name]', $booking_form_show['name'], $mail_body_to_send);
                    if (isset($res->cost)) $mail_body_to_send = str_replace('[cost]', $res->cost, $mail_body_to_send);
                    $mail_body_to_send = str_replace('[siteurl]', htmlspecialchars_decode( '<a href="'.site_url().'">' . site_url() . '</a>'), $mail_body_to_send);
                    $mail_body_to_send = apply_bk_filter('wpdev_booking_set_booking_edit_link_at_email', $mail_body_to_send, $res->booking_id );

                    if ( isset($booking_form_show['secondname']) ) $mail_body_to_send = str_replace('[secondname]', $booking_form_show['secondname'], $mail_body_to_send);
                    $mail_subject1 = $mail_subject;
                    $mail_subject1 = str_replace('[name]', $booking_form_show['name'], $mail_subject1);
                    if ( isset($booking_form_show['secondname']) ) $mail_subject1 = str_replace('[secondname]', $booking_form_show['secondname'], $mail_subject1);


                    $is_send_emeils = 1;

                    $mail_recipient =  $booking_form_show['email']; //$mail_sender; // Send this email to person who made cancellation
                    //$mail_headers = "From: $mail_recipient\n";
                    $mail_headers = "From: $mail_sender\n";
                    $mail_headers .= "Content-Type: text/html\n";

                    if (get_bk_option( 'booking_is_email_deny_adress'  ) != 'Off')
                        if ($is_send_emeils != 0 )
                            if ( ( strpos($mail_recipient,'@blank.com') === false ) && ( strpos($mail_body_to_send,'admin@blank.com') === false ) )
                                @wp_mail($mail_recipient, $mail_subject1, $mail_body_to_send, $mail_headers);

                    // Send COPY to the Admin also
                    $mail_recipient =  htmlspecialchars_decode( get_bk_option( 'booking_email_reservation_adress') );
                    $mail_headers = "From: $mail_recipient\n";
                    $mail_headers .= "Content-Type: text/html\n";
                    $is_email_deny_send_copy_to_admin = get_bk_option( 'booking_is_email_deny_send_copy_to_admin' );
                    if ( $is_email_deny_send_copy_to_admin == 'On')
                        if ( ( strpos($mail_recipient,'@blank.com') === false ) && ( strpos($mail_body_to_send,'admin@blank.com') === false ) )
                            if ($is_send_emeils != 0 )
                                @wp_mail($mail_recipient, $mail_subject1, $mail_body_to_send, $mail_headers);


                    /////////////////////////////////////////////////////////////////////////
                }

                if ( false === $wpdb->query( $wpdb->prepare("DELETE FROM ".$wpdb->prefix ."bookingdates WHERE booking_id IN ($approved_id_str)") ) ){
                    ?> <script type="text/javascript"> document.getElementById('submiting<?php echo $my_boook_type; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during deleting dates at DB',__FILE__,__LINE__ ); ?></div>'; </script> <?php
                    die();
                }

                if ( false === $wpdb->query( $wpdb->prepare("DELETE FROM ".$wpdb->prefix ."booking WHERE booking_id IN ($approved_id_str)") ) ){
                    ?> <script type="text/javascript"> document.getElementById('submiting<?php echo $my_boook_type; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during deleting booking at DB' ,__FILE__,__LINE__); ?></div>'; </script> <?php
                    die();
                }


                // Visitor cancellation
                ?> <script type="text/javascript">
                    document.getElementById('submiting<?php echo $my_boook_type; ?>').innerHTML = '<div class=\"submiting_content\" ><div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php echo __('The booking is cancel successfully', 'wpdev-booking'); ?></div></div>';
                    document.getElementById("booking_form_div<?php echo $my_boook_type; ?>" ).style.display="none";
                    makeScroll('#booking_form<?php echo $my_boook_type; ?>' );
                    jQuery('#submiting<?php echo $my_boook_type; ?>').fadeOut(<?php echo get_bk_option( 'booking_title_after_reservation_time'); ?>);
                   </script>
                <?php

                die();
            }

        }


    // Resources

        //TODO: make changes  and corrections here according all versions
        function wpdev_booking_resources_show_content(){ global $wpdb;
          if (  (! isset($_GET['tab'])) || ( $_GET['tab'] == 'resource')  ) {

            if ((isset($_POST['submit_resources']))) {

                $bk_types = $this->get_booking_types();

                // Edit ////////////////////////////////////////////////////////
                if ( ($_POST['bulk_resources_action'] == 'blank' ) || ($_POST['bulk_resources_action'] == 'edit' ) ) {

                    foreach ($bk_types as $bt) { 
                          $sql_res_cost = apply_bk_filter('get_sql_4_update_bk_resources_cost', ''  , $bt );
                          $sql_res = apply_bk_filter('get_sql_4_update_bk_resources', ''  , $bt );

                          if ( false === $wpdb->query( $wpdb->prepare(
                                  "UPDATE ".$wpdb->prefix ."bookingtypes SET title = '".$_POST['type_title'.$bt->id]."'" .$sql_res_cost.$sql_res. " WHERE booking_type_id = ".$bt->id) )  )  bk_error('Error during updating to DB booking resources' ,__FILE__,__LINE__);
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


            if ((isset($_POST['submit_add_resources']))) {

                // Add new res /////////////////////////////////////////////////
                  if (isset($_POST['type_number_of_resources'])) $iter = $_POST['type_number_of_resources'];
                  else $iter = 1;
                  for ($i = 0; $i < $iter; $i++) {

                          if ($iter > 1) $sufix = '-'.($i+1);
                          else $sufix = '';

                          $update_fields = 'title ';
                          $update_values =  '"'. $_POST['type_title_new'] .$sufix . '"';

                          $update_fields .= apply_bk_filter('get_sql_4_insert_bk_resources_fields_p', ''    );
                          $update_values .= apply_bk_filter('get_sql_4_insert_bk_resources_values_p', '' , $i   );
                          $update_fields .= apply_bk_filter('get_sql_4_insert_bk_resources_fields_h', ''    );
                          $update_values .= apply_bk_filter('get_sql_4_insert_bk_resources_values_h', '' , $i   );
                          $update_fields .= apply_bk_filter('get_sql_4_insert_bk_resources_fields_m', ''    );
                          $update_values .= apply_bk_filter('get_sql_4_insert_bk_resources_values_m', '' , $i   );
//debuge( $_POST['type_number_of_resources'] );die;
                          if ( false === $wpdb->query( $wpdb->prepare('INSERT INTO '.$wpdb->prefix .'bookingtypes ( '.$update_fields.' ) VALUES ( '. $update_values .') ' ) ) )
                               bk_error('Error during adding new booking resource into DB' ,__FILE__,__LINE__);
                           else
                               make_bk_action('insert_bk_resources_recheck_max_visitors' );

                  }
            }
            
              $alternative_color = '0';
              $link = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking-option&tab=resources';

              $bk_types = $this->get_booking_types(true);
              $bk_types_all_parents = get_booking_types_all_parents_and_single();
              $all_id = array(array('id'=>0,'title'=>' - '));
              foreach ($bk_types_all_parents as $btt) {
                    if (isset($btt->parent)) if ($btt->parent==0)  $all_id[] = array('id'=>$btt->booking_type_id, 'title'=> $btt->title);
              }

              make_bk_action('wpdev_bk_booking_resource_page_before');
            ?><div style="clear:both;width:100%;height:1px;"></div>


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

            if (  ( ($max_num === false) || ($max_num > count($bk_types) ) ) && ($is_show_add_resource === 0)  ) { ?>

                <div class='meta-box' style="width:99%;margin-bottom:20px;">
                    <div <?php $my_close_open_win_id = 'bk_resource_settings_add_new_resources'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');"><br></div>
                        <h3 class='hndle'><span><?php _e('Add New Booking Resource(s)', 'wpdev-booking'); ?></span></h3> <div class="inside">

                        <form  name="post_option_add_resources" action="" method="post" id="post_option_add_resources" >
                        <table class="form-table"><tbody>

                            <tr valign="top">
                                <th scope="row">
                                    <h2 class="settings-resource-title"  ><?php _e('New Resource','wpdev-booking'); ?>:</h2>
                                    <div  class="settings-resource-label"><?php _e('Enter name of booking resource', 'wpdev-booking'); ?></div>
                                </th>
                                <td>
                                    <div class="inside_hint" for="type_title_new" style="color:#BBBBBB; font-size:1.7em; padding:8px; margin:-5px 0 -36px -1px; cursor: text;"><?php _e('Enter title here', 'wpdev-booking');?><br /></div>
                                    <input type="text" value="" id="type_title_new" name="type_title_new" class="has-inside-hint settings-resource-input"  autocomplete="off"  tabindex="1" maxlength="200" >
                                </td>
                            </tr>

                            <?php make_bk_action('resources_settings_table_add_bottom_button',  $all_id  ); ?>

                            <tr>
                                <td style="height:35px;border-top: 1px solid #ccc;" colspan="2" >
                                    <!--div class="booking-advanced-shifter"> [ <span class="minus-plus-booking-advanced-shifter">-</span> ] &nbsp; <a href="javascript:;"
                                         onclick="javascript:jQuery('#resource-add-new-advanced-options').slideToggle('slow');if (jQuery('.minus-plus-booking-advanced-shifter').html() == '-') jQuery('.minus-plus-booking-advanced-shifter').html('+'); else jQuery('.minus-plus-booking-advanced-shifter').html('-');"> <?php _e('Advanced Options', 'wpdev-booking'); ?></a></div-->
                                    <input class="button-secondary" style="float:left;" type="submit" value="+ <?php _e('Add new resource(s)', 'wpdev-booking'); ?>" name="submit_add_resources"/>
                                </td>
                            </tr>

                        </tbody></table>
                        </form>
                    </div></div></div>

            <?php } ?>

            <div style="width:100%;">

                <form  name="post_option_resources" action="" method="post" id="post_option_resources" >
                    <table style="width:99%;" class="resource_table0 booking_table" cellpadding="0" cellspacing="0">
                            <?php // Headers  ?>
                        <tr>
                            <th style="width:15px;"><input type="checkbox" onclick="javascript:jQuery('.resources_items').attr('checked', this.checked);" class="resources_items" id="resources_items_all"  name="resources_items_all" /></th>
                            <th style="width:10px;height:35px;border-left: 1px solid #BBBBBB;"> <?php _e('ID', 'wpdev-booking'); ?> </th>
                            <th style="height:35px;width:215px;"> <?php _e('Resource name', 'wpdev-booking'); ?> </th>
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

                                        <input  maxlength="200" type="text"
                                            style="<?php  if (isset($bt->parent)) if ($bt->parent == 0 ) { echo 'width:210px;font-weight:bold;'; } else { echo 'width:170px;font-size:11px;'; } ?>"
                                            value="<?php echo $bt->title; ?>"
                                            name="type_title<?php echo $bt->id; ?>" id="type_title<?php echo $bt->id; ?>" />
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
            
                            <div class="clear" style="height:10px;width:100%;clear:both;"></div>
                            
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

                            <div class="clear" style="height:5px;width:100%;clear:both;"></div>
                            <?php // Show Pagination
                            echo '<div class="wpdevbk" style="clear:both;">';
                            $active_page_num = (isset($_REQUEST['page_num']))?$_REQUEST['page_num']:1;
                            $items_count_in_page=get_bk_option( 'booking_resourses_num_per_page');
                            wpdevbk_show_pagination(get_booking_resources_count(), $active_page_num, $items_count_in_page, array('page','tab', 'wh_resource_id'));
                            echo '</div>';
                            ?>

                            <div class="clear" style="height:1px;"></div>

                        </form>

            </div>

            <div style="clear:both;width:100%;height:1px;"></div><?php
            make_bk_action('wpdev_bk_booking_resource_page_after');
          }
        }



    // S e t t i n g s    

        function settings_a(){
             if ( isset( $_POST['default_booking_resource'] ) ) {
                  update_bk_option( 'booking_default_booking_resource', $_POST['default_booking_resource'] );
                  update_bk_option( 'booking_resourses_num_per_page', $_POST['resourses_num_per_page'] );

             }
             $default_booking_resource = get_bk_option( 'booking_default_booking_resource');
             $resourses_num_per_page = get_bk_option( 'booking_resourses_num_per_page');
            ?>
            <tr valign="top">
                <th scope="row"><label for="resourses_num_per_page" ><?php _e('Resources number per page', 'wpdev-booking'); ?>:</label></th>
                <td>

                    <?php  $order_array = array( 5, 10, 20, 25, 50, 75, 100 ); ?>
                    <select id="resourses_num_per_page" name="resourses_num_per_page">
                    <?php foreach ($order_array as $mm) { ?>
                        <option <?php if($resourses_num_per_page == strtolower($mm) ) echo "selected"; ?> value="<?php echo strtolower($mm); ?>"><?php echo ($mm) ; ?></option>
                    <?php } ?>
                    </select>
                    <span class="description"><?php _e('Select number of booking resources (sinle or parent) per page at Resource menu page', 'wpdev-booking');?></span>
                </td>
            </tr>

            <tr valign="top"  class="ver_pro">
                <th scope="row"><label for="default_booking_resource" ><?php _e('Default booking resource', 'wpdev-booking'); ?>:</label></th>
                <td>
                    <input id="a_o"  name="a_o" class="regular-text code" type="text" style="width:350px;" size="145" value="" />                    
                    <script  type="text/javascript">
                        function act_o(){

                               var act_c_o = document.getElementById('a_o').value;
                               var wpdev_ajax_path = wpdev_bk_plugin_url+'/' + wpdev_bk_plugin_filename ;

                               document.getElementById('ajax_a_o').innerHTML =
                                            '<div class="info_message ajax_message" id="ajax_message">\n\
                                                <div style="float:left;"> Activation </div> \n\
                                                <div  style="float:left;width:80px;margin-top:-3px;">\n\
                                                       <img src="'+wpdev_bk_plugin_url+'/img/ajax-loader.gif">\n\
                                                </div>\n\
                                            </div>';

                                jQuery.ajax({
                                    url: wpdev_ajax_path,
                                    type:'POST',
                                    success: function (data, textStatus){if( textStatus == 'success')   jQuery('#ajax_a_o').html( data ) ;},
                                    error:function (XMLHttpRequest, textStatus, errorThrown){window.status = 'Ajax sending Error status:'+ textStatus;alert(XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText);},
                                    // beforeSend: someFunction,
                                    data:{
                                        ajax_action : 'ACTIVATE',
                                        num: act_c_o ,
                                        site:  '<?php echo $_SERVER['SERVER_NAME']; ?>'
                                    }
                                });
                        }
                    </script>
                    <input type="button" class="button-primary" value="Avtivate" onclick="javascript:act_o();" />
                    <div id="ajax_a_o"></div>
                    <span class="description"><?php _e('Enter your order number for activation process.', 'wpdev-booking');?></span>
                </td>
            </tr>

            <?php
        }


        // Settings for selecting default booking resource
        function settings_set_default_booking_resource(){
             if ( isset( $_POST['default_booking_resource'] ) ) {
                  update_bk_option( 'booking_default_booking_resource', $_POST['default_booking_resource'] );
                  update_bk_option( 'booking_resourses_num_per_page', $_POST['resourses_num_per_page'] );
             }
             $default_booking_resource = get_bk_option( 'booking_default_booking_resource');
             $resourses_num_per_page = get_bk_option( 'booking_resourses_num_per_page');
            ?>
            <tr valign="top">
                <th scope="row"><label for="resourses_num_per_page" ><?php _e('Resources number per page', 'wpdev-booking'); ?>:</label></th>
                <td>

                    <?php  $order_array = array( 5, 10, 20, 25, 50, 75, 100, 500 ); ?>
                    <select id="resourses_num_per_page" name="resourses_num_per_page">
                    <?php foreach ($order_array as $mm) { ?>
                        <option <?php if($resourses_num_per_page == strtolower($mm) ) echo "selected"; ?> value="<?php echo strtolower($mm); ?>"><?php echo ($mm) ; ?></option>
                    <?php } ?>
                    </select>
                    <span class="description"><?php _e('Select number of booking resources (sinle or parent) per page at Resource menu page', 'wpdev-booking');?></span>
                </td>
            </tr>

            <tr valign="top"  class="ver_pro">
                <th scope="row"><label for="default_booking_resource" ><?php _e('Default booking resource', 'wpdev-booking'); ?>:</label></th>
                <td>

                    <?php  $bk_resources = get_booking_types_all_parents_and_single(); ?>
                    <select id="default_booking_resource" name="default_booking_resource">
                        <?php foreach ($bk_resources as $mm) { ?>
                        <option <?php if($default_booking_resource == $mm->booking_type_id ) echo "selected"; ?> value="<?php echo $mm->booking_type_id; ?>"
                              style="<?php if  (isset($mm->parent)) if ($mm->parent == 0 ) { echo 'font-weight:bold;'; } else { echo 'font-size:11px;padding-left:20px;'; } ?>"
                            ><?php echo $mm->title; ?></option>
                        <?php } ?>
                    </select>

                    <span class="description"><?php _e('Select your default booking resource.', 'wpdev-booking');?></span>
                </td>
            </tr>

            <?php
        }

        // Set field for editing of URL for bookings edit by visitor.
        function settings_edit_booking_url(){
             if ( isset( $_POST['url_bookings_edit_by_visitors'] ) ) {
                  update_bk_option( 'booking_url_bookings_edit_by_visitors', $_POST['url_bookings_edit_by_visitors'] );
             }
             $url_bookings_edit_by_visitors = get_bk_option( 'booking_url_bookings_edit_by_visitors');
            ?>
             <tr valign="top" class="ver_pro">
                <th scope="row"><label for="url_bookings_edit_by_visitors" ><?php _e('URL for bookings edit', 'wpdev-booking'); ?>:</label></th>
                <td><input id="url_bookings_edit_by_visitors"  name="url_bookings_edit_by_visitors" class="regular-text code" type="text" style="width:350px;" size="145" value="<?php echo $url_bookings_edit_by_visitors; ?>" /><br/>
                    <span class="description"><?php printf(__('Type URL for bookings edit by %svisitors%s. You must use %s shortcode, at this page.', 'wpdev-booking'),'<b>','</b>', '<code>[bookingedit]</span>');?></code>
                </td>
            </tr>
            <?php
        }

        // Set update or not of hash during approvemant of booking
        function settings_advanced_set_update_hash_after_approve(){
            if (isset($_POST['new_booking_title'])) {
                     if (isset( $_POST['is_change_hash_after_approvement'] ))       $is_change_hash_after_approvement = 'On';
                     else                                                           $is_change_hash_after_approvement = 'Off';
                     update_bk_option( 'booking_is_change_hash_after_approvement' , $is_change_hash_after_approvement );
            }
            $is_change_hash_after_approvement = get_bk_option( 'booking_is_change_hash_after_approvement');
            ?>

                <tr valign="top" class="ver_premium">
                    <th scope="row">
                        <label for="is_change_hash_after_approvement" ><?php _e('Change hash after approvment of booking', 'wpdev-booking'); ?>:</label>
                    </th>
                    <td>
                        <input <?php if ($is_change_hash_after_approvement == 'On') echo "checked";/**/ ?>  value="<?php echo $is_change_hash_after_approvement; ?>" name="is_change_hash_after_approvement" id="is_change_hash_after_approvement" type="checkbox" />
                        <span class="description"><?php _e(' Check this, if you want to change hash of booking after approvemnt. This will disable posibility for visitor to edit or cancel booking.', 'wpdev-booking');?></span>
                    </td>
                </tr>

            <?php
        }



        function show_additional_translation_shortcode_help(){ ?>
          <div class="clear" style="height:0px;clear:both;" ></div>
          <span class="description"><?php printf(__('%s - start new translation section, where %s - locale of translation', 'wpdev-booking'),'<code>[lang=LOCALE]</code>','LOCALE');?></span><br />
          <span class="description example-code"><?php printf(__('Example #1: %s - start French tranlation section', 'wpdev-booking'),'[lang=fr_FR]');?></span><br/>
          <span class="description example-code"><?php printf(__('Example #2: "%s" - English and French translation of some message', 'wpdev-booking'),'Thank you for your booking.[lang=fr_FR]Je vous remercie de votre reservation.');?></span><br/>
          <div class="clear" style="height:0px;clear:both;" ></div>      
            <?php
        }



        function wpdev_booking_settings_top_menu_submenu_line(){

            if ( (isset($_GET['tab'])) && ( $_GET['tab'] == 'email') ) {
            ?>
                <div class="booking-submenu-tab-container">
                    <div class="nav-tabs booking-submenu-tab-insidecontainer">


                        <a href="#" onclick="javascript:jQuery('.visibility_container').css('display','none');jQuery('#visibility_container_email_new_to_admin').css('display','block');jQuery('.nav-tab').removeClass('booking-submenu-tab-selected');jQuery(this).addClass('booking-submenu-tab-selected');"
                           rel="tooltip" class="tooltip_bottom nav-tab  booking-submenu-tab booking-submenu-tab-selected <?php if ( get_bk_option( 'booking_is_email_reservation_adress' ) != 'On' ) echo ' booking-submenu-tab-disabled '; ?>"
                           original-title="<?php _e('Customization of email template, which is sending to Admin after new booking', 'wpdev-booking');?>" >
                                <?php _e('New for Admin', 'wpdev-booking');?>
                            <input type="checkbox" <?php if ( get_bk_option( 'booking_is_email_reservation_adress' ) == 'On' ) echo ' checked="CHECKED" '; ?>  name="booking_is_email_reservation_adress_dublicated" id="booking_is_email_reservation_adress_dublicated"
                                   onchange="document.getElementById('is_email_reservation_adress').checked=this.checked;"
                                   >
                        </a>

                        <a href="#" onclick="javascript:jQuery('.visibility_container').css('display','none');jQuery('#visibility_container_email_new_to_visitor').css('display','block');jQuery('.nav-tab').removeClass('booking-submenu-tab-selected');jQuery(this).addClass('booking-submenu-tab-selected');"
                           rel="tooltip" class="tooltip_bottom nav-tab  booking-submenu-tab <?php if ( get_bk_option( 'booking_is_email_newbookingbyperson_adress' ) != 'On' ) echo ' booking-submenu-tab-disabled '; ?>"
                           original-title="<?php _e('Customization of email template, which is sending to Visitor after new booking', 'wpdev-booking');?>" >
                                <?php _e('New for Visitor', 'wpdev-booking');?>
                            <input type="checkbox" <?php if ( get_bk_option( 'booking_is_email_newbookingbyperson_adress' ) == 'On' ) echo ' checked="CHECKED" '; ?>  name="booking_is_email_newbookingbyperson_adress_dublicated" id="booking_is_email_newbookingbyperson_adress_dublicated"
                                   onchange="document.getElementById('is_email_newbookingbyperson_adress').checked=this.checked;"
                                   >
                        </a>

                        <a href="#" onclick="javascript:jQuery('.visibility_container').css('display','none');jQuery('#visibility_container_email_approved').css('display','block');jQuery('.nav-tab').removeClass('booking-submenu-tab-selected');jQuery(this).addClass('booking-submenu-tab-selected');"
                           rel="tooltip" class="tooltip_bottom nav-tab  booking-submenu-tab <?php if ( get_bk_option( 'booking_is_email_approval_adress' ) != 'On' ) echo ' booking-submenu-tab-disabled '; ?>"
                           original-title="<?php _e('Customization of email template, which is sending to Visitor after approvement of booking', 'wpdev-booking');?>" >
                                <?php _e('Approved', 'wpdev-booking');?>
                            <input type="checkbox" <?php if ( get_bk_option( 'booking_is_email_approval_adress' ) == 'On' ) echo ' checked="CHECKED" '; ?>  name="booking_is_email_approval_adress_dublicated" id="booking_is_email_approval_adress_dublicated"
                                   onchange="document.getElementById('is_email_approval_adress').checked=this.checked;"
                                   >
                        </a>

                        <a href="#" onclick="javascript:jQuery('.visibility_container').css('display','none');jQuery('#visibility_container_email_declined').css('display','block');jQuery('.nav-tab').removeClass('booking-submenu-tab-selected');jQuery(this).addClass('booking-submenu-tab-selected');"
                           rel="tooltip" class="tooltip_bottom nav-tab  booking-submenu-tab <?php if ( get_bk_option( 'booking_is_email_deny_adress' ) != 'On' ) echo ' booking-submenu-tab-disabled '; ?>"
                           original-title="<?php _e('Customization of email template, which is sending to Visitor after Cancellation of booking', 'wpdev-booking');?>" >
                                <?php _e('Declined', 'wpdev-booking');?>
                            <input type="checkbox" <?php if ( get_bk_option( 'booking_is_email_deny_adress' ) == 'On' ) echo ' checked="CHECKED" '; ?>  name="booking_is_email_declined_adress_dublicated" id="booking_is_email_declined_adress_dublicated"
                                   onchange="document.getElementById('is_email_deny_adress').checked=this.checked;"
                                   >
                        </a>

                        <a href="#" onclick="javascript:jQuery('.visibility_container').css('display','none');jQuery('#visibility_container_email_modification').css('display','block');jQuery('.nav-tab').removeClass('booking-submenu-tab-selected');jQuery(this).addClass('booking-submenu-tab-selected');"
                           rel="tooltip" class="tooltip_bottom nav-tab  booking-submenu-tab <?php if ( get_bk_option( 'booking_is_email_modification_adress' ) != 'On' ) echo ' booking-submenu-tab-disabled '; ?>"
                           original-title="<?php _e('Customization of email template, which is sending after modification of booking', 'wpdev-booking');?>" >
                                <?php _e('Modificated', 'wpdev-booking');?>
                            <input type="checkbox" <?php if ( get_bk_option( 'booking_is_email_modification_adress' ) == 'On' ) echo ' checked="CHECKED" '; ?>  name="booking_is_email_modification_adress_dublicated" id="booking_is_email_modification_adress_dublicated"
                                   onchange="document.getElementById('is_email_modification_adress').checked=this.checked;"
                                   >
                        </a>

                        <?php if ( class_exists('wpdev_bk_biz_s')) { ?>
                            <span class="booking-submenu-tab-separator-vertical"></span>

                            <a href="#" onclick="javascript:jQuery('.visibility_container').css('display','none');jQuery('#visibility_container_email_payment_request').css('display','block');jQuery('.nav-tab').removeClass('booking-submenu-tab-selected');jQuery(this).addClass('booking-submenu-tab-selected');"
                               rel="tooltip" class="tooltip_bottom nav-tab  booking-submenu-tab <?php if ( get_bk_option( 'booking_is_email_payment_request_adress' ) != 'On' ) echo ' booking-submenu-tab-disabled '; ?>"
                               original-title="<?php _e('Customization of email template, which is sending to Visitor after payment request', 'wpdev-booking');?>" >
                                    <?php _e('Payment request', 'wpdev-booking');?>
                                <input type="checkbox" <?php if ( get_bk_option( 'booking_is_email_payment_request_adress' ) == 'On' ) echo ' checked="CHECKED" '; ?>  name="booking_is_email_payment_request_adress_dublicated" id="booking_is_email_payment_request_adress_dublicated"
                                       onchange="document.getElementById('is_email_payment_request_adress').checked=this.checked;"
                                       >
                            </a>
                        <?php } ?>

                        <input type="button" class="button-primary" value="<?php _e('Save settings','wpdev-booking'); ?>" style="float:right;margin:0px 5px 0px 0px;"
                               onclick="document.forms['post_settings_email_templates'].submit();">

                        <script type="text/javascript">
                            function recheck_active_itmes_in_top_menu( internal_checkbox, top_checkbox ){
                                if (document.getElementById( internal_checkbox ).checked != document.getElementById( top_checkbox ).checked ) {
                                    document.getElementById( top_checkbox ).checked = document.getElementById( internal_checkbox ).checked;
                                    if ( document.getElementById( top_checkbox ).checked )
                                        jQuery('#' + top_checkbox ).parent().removeClass('booking-submenu-tab-disabled');
                                    else
                                        jQuery('#' + top_checkbox ).parent().addClass('booking-submenu-tab-disabled');
                                }
                            }

                            jQuery(document).ready( function(){
                                recheck_active_itmes_in_top_menu('is_email_reservation_adress', 'booking_is_email_reservation_adress_dublicated');
                                recheck_active_itmes_in_top_menu('is_email_newbookingbyperson_adress', 'booking_is_email_newbookingbyperson_adress_dublicated');
                                recheck_active_itmes_in_top_menu('is_email_approval_adress', 'booking_is_email_approval_adress_dublicated');
                                recheck_active_itmes_in_top_menu('is_email_deny_adress', 'booking_is_email_declined_adress_dublicated');
                                recheck_active_itmes_in_top_menu('is_email_modification_adress', 'booking_is_email_modification_adress_dublicated');
                                <?php if ( class_exists('wpdev_bk_biz_s')) { ?>
                                    recheck_active_itmes_in_top_menu('is_email_payment_request_adress', 'booking_is_email_payment_request_adress_dublicated');
                                <?php } ?>
                            });
                        </script>

                    </div>
                </div>
              <?php
            }
        }


 // C l i e n t     s i d e     f u n c t i o n s     /////////////////////////////////////////////////////////////////////////////////////////

        // Define JavaScript variables
        function js_define_variables(){
            ?>
                    <script  type="text/javascript">
                        var message_time_error = '<?php echo esc_js(__('Incorrect date format', 'wpdev-booking')); ?>';
                    </script>
            <?php
        }

        // Write Js files
        function js_write_files(){
             ?> <script type="text/javascript" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/inc/js/jquery.meio.mask.min.js"></script>  <?php
             ?> <script type="text/javascript" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/inc/js/personal.js"></script>  <?php
         }


// B o o k i n g     T y p e s              //////////////////////////////////////////////////////////////////////////////////////////////////


        // Get booking types from DB
        function get_booking_types($is_use_filter = false  ) { global $wpdb;

            ////////////////////////////////////////////////////////////////////////
            // CONSTANTS
            ////////////////////////////////////////////////////////////////////////
            /*update_bk_option( 'booking_resourses_num_per_page',10);
            $defaults = array(
                    'page_num' => '1',
                    'page_items_count' => get_bk_option( 'booking_resourses_num_per_page')
            );

            $r = wp_parse_args( $args, $defaults );
            extract( $r, EXTR_SKIP );
            /**/
            $page_num         = (isset($_REQUEST['page_num']))?$_REQUEST['page_num']:1;         // Pagination
            $page_items_count = get_bk_option( 'booking_resourses_num_per_page');
            $page_start = ( $page_num - 1 ) * $page_items_count ;


            $sql = " SELECT * FROM ".$wpdb->prefix ."bookingtypes as bt" ;
            $or_sort = 'title';
            $where = '';                                                        // Where for the different situation: BL and MU
            $where = apply_bk_filter('multiuser_modify_SQL_for_current_user', $where);
            if ($where != '') $where = ' WHERE ' . $where;
            if ( class_exists('wpdev_bk_biz_l')) {
                if ($where != '')   $where .= ' AND bt.parent = 0 ';
                else                $where .= ' WHERE bt.parent = 0 ';
                $or_sort = 'prioritet';
            }

            if (isset($_REQUEST['wh_resource_id'])) {
                 if ($where == '') $where .= " WHERE " ;
                 else $where .= " AND ";
                 $where .= " ( (bt.booking_type_id = '" . $_REQUEST['wh_resource_id'] . "') OR (bt.title like '%%".$_REQUEST['wh_resource_id']."%%') )  ";
            }
            
            if (strpos($or_sort, '_asc') !== false) {                            // Order
                   $or_sort = str_replace('_asc', '', $or_sort);
                   $sql_order = " ORDER BY " .$or_sort ." ASC ";
            } else $sql_order = " ORDER BY " .$or_sort ." DESC ";

            $sql_limit = " LIMIT $page_start, $page_items_count ";              // Pages
//debuge($sql .  $where. $sql_order . $sql_limit );
            $types_list = $wpdb->get_results( $wpdb->prepare( $sql .  $where. $sql_order . $sql_limit ) );
//debuge($types_list);


            $bk_type_id = array();                                              // Get all ID of booking resources.
            if (! empty($types_list))
            foreach ($types_list as $key=>$res) {
                $types_list[$key]->id = $res->booking_type_id;
                $bk_type_id[]=$res->booking_type_id;
            }
            

            if ( ( class_exists('wpdev_bk_biz_l')) && (count($bk_type_id)>0) ) {

                $bk_type_id = implode(',',$bk_type_id);                         // Get all ID of PARENT or SINGLE Resources.

                $sql = " SELECT * FROM ".$wpdb->prefix ."bookingtypes as bt" ;

                $where = '';                                                        // Where for the different situation: BL and MU
                $where = apply_bk_filter('multiuser_modify_SQL_for_current_user', $where);
                if ($where != '') $where = ' WHERE ' . $where;

                if ($where != '')   $where .= ' AND   bt.parent IN (' . $bk_type_id . ') ';
                else                $where .= ' WHERE bt.parent IN (' . $bk_type_id . ') ';

                $sql_order = 'ORDER BY parent, prioritet';                          // Order
                $linear_list_child_resources = $wpdb->get_results( $wpdb->prepare( $sql .  $where. $sql_order ) );  // Get  child elements

                // Transfrom them into array for the future work
                $array_by_parents_child_resources = array();
                foreach ($linear_list_child_resources as $res) {
                    if (! isset($array_by_parents_child_resources[$res->parent]))  $array_by_parents_child_resources[$res->parent] = array();
                    $res->id = $res->booking_type_id;
                    $array_by_parents_child_resources[$res->parent][] = $res;
                }


                $final_resource_array = array();
                foreach ($types_list as $key=>$res) {
                    // check if exist child resources
                    if ( isset($array_by_parents_child_resources[ $res->booking_type_id ])) {
                        $res->count = count( $array_by_parents_child_resources[ $res->booking_type_id ] )+1;
                    } else
                        $res->count = 1;

                    // Fill the parent resource
                    $final_resource_array[] = $res;

                    // Fill all child resources (its already sorted)
                    if ( isset($array_by_parents_child_resources[ $res->booking_type_id ])) {
                        foreach ($array_by_parents_child_resources[ $res->booking_type_id ] as $child_obj) {
                            $child_obj->count = 1;
                            $final_resource_array[]  = $child_obj;
                        }
                    }
                }
                $types_list = $final_resource_array;
            }

            return $types_list;

/*===========================================================================================================================================================*/

            if ( class_exists('wpdev_bk_biz_s'))  $mysql = "SELECT booking_type_id as id, title, cost FROM ".$wpdb->prefix ."bookingtypes  ORDER BY title";
            else                                  $mysql = "SELECT booking_type_id as id, title FROM ".$wpdb->prefix ."bookingtypes  ORDER BY title";

            if ( class_exists('wpdev_bk_biz_l')) {  // If Business Large then get resources from that
                $types_list = apply_bk_filter('get_booking_types_hierarhy_linear',array() );
                for ($i = 0; $i < count($types_list); $i++) {
                    $types_list[$i]['obj']->count = $types_list[$i]['count'];
                    $types_list[$i] = $types_list[$i]['obj'];
                    //if ( ($booking_type_id != 0) && ($booking_type_id == $types_list[$i]->booking_type_id ) ) return $types_list[$i];
                }
            } else
                $types_list = $wpdb->get_results( $wpdb->prepare($mysql) );

            
            $types_list = apply_bk_filter('multiuser_resource_list', $types_list);

            return $types_list;

 /**/
        }

        function get_default_booking_resource_id(){

            if ( class_exists('wpdev_bk_multiuser')) {  // If MultiUser so
                $bk_multiuser = apply_bk_filter('get_default_bk_resource_for_user',false);
                if ($bk_multiuser !== false) return $bk_multiuser;
            }

            global $wpdb;
            $mysql = "SELECT booking_type_id as id FROM  ".$wpdb->prefix ."bookingtypes ORDER BY id ASC LIMIT 1";
            $types_list = $wpdb->get_results( $wpdb->prepare($mysql) );
            if (count($types_list) > 0 ) $types_list = $types_list[0]->id;
            else $types_list =1;
            return $types_list;
        }

        // Show single menu Item
        function echoMenuItem( $title, $my_icon, $my_tab_id, $is_only_icons = 0){

            //$title = __('General', 'wpdev-booking');
            //$my_icon = 'General-setting-64x64.png';
            //$my_tab = 'main';
            
            $my_style = '';
            if ($is_only_icons == 0){ $my_style = 'style="padding:4px 14px 6px;"';}
            if ($is_only_icons == 1){ $my_style = 'style="padding:4px 5px 6px 32px;"';}


            if (    ($_GET['booking_type'] == $my_tab_id) ||
                    (  (! isset($_GET['booking_type'])) && ( (! isset($my_tab_id)) || ($my_tab_id==1)  )  )
               )  { $slct_a = 'selected'; }
            else  { $slct_a = ''; }


            //Start
            if ($slct_a == 'selected') {  $selected_title = $title;  $selected_icon = $my_icon;
                ?><span class="nav-tab nav-tab-active"  <?php echo $my_style; ?> ><?php
            } else {
                if ($my_tab_id == 'left')
                  {  ?><span class="nav-tab" <?php echo $my_style;  ?> style="cursor:finger;" 
                     onclick="javascript:var marg = document.getElementById('menu_items_slide').style.marginLeft;
                         marg = marg.replace('px'  ,'');
                         marg = ( marg +10 ) + 'px';
                         document.getElementById('menu_items_slide').style.marginLeft = marg;"
                     ><?php }
                elseif ($my_tab_id == 'right')
                  { ?><a class="nav-tab" <?php echo $my_style; ?> href="admin.php?page=<?php echo WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME ; ?>wpdev-booking&booking_type=<?php echo $my_tab_id; ?>"><?php }
                else
                  { ?><a class="nav-tab" <?php echo $my_style; ?> href="admin.php?page=<?php echo WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME ; ?>wpdev-booking&booking_type=<?php echo $my_tab_id; ?>"><?php }
            }

            if ($is_only_icons !== 0) { // Image
                if ($is_only_icons == 1) echo '&nbsp;';
                ?><img class="menuicons" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php
            }

            // Title
            if ($is_only_icons == 1) echo '&nbsp;';
            else echo $title;

            // End
            if (($slct_a == 'selected') || ($my_tab_id == 'left') || ($my_tab_id == 'right')) {
                ?></span><?php
            } else {
                ?></a><?php }
        }



        function echoResourceMenuItem($bk_type,$selected_bk_typenew, $is_edit, $is_parent=false ) {

                if($is_edit == 'noedit') $subpage = '-reservation';
                else                     $subpage = '';

                if ($is_parent===false) $parent_lnk = '';
                else                   $parent_lnk = '&parent_res=1';
            ?>
                <div id="bktype<?php echo $bk_type->id; ?>" class="topmenuitemborder <?php echo $selected_bk_typenew; ?>  <?php   echo apply_bk_filter('showing_user_name_in_top_line', '', $bk_type, $is_parent ); ?> ">
                    <?php echo '<a href="admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME . 'wpdev-booking'.$subpage.'&booking_type='.$bk_type->id.$parent_lnk.'" class="bktypetitlenew '.$selected_bk_typenew.' ">' .  $bk_type->title  . '</a>'; ?>
                    <?php if ( $is_edit !== 'noedit') echo ' <a href="#" class="bktype_edit "  title="'. __('Edit', 'wpdev-booking') .'" style="text-decoration:none;" onclick="javascript:edit_bk_type('.$bk_type->id.');"><img src="'.WPDEV_BK_PLUGIN_URL.'/img/edit_type.png" width="8" height="8"   /></a>'; ?>
                    <?php if ( $is_edit !== 'noedit') echo ' <a href="#" class="bktype_delete "  title="'. __('Delete', 'wpdev-booking') .'" style="text-decoration:none;" onclick="javascript:delete_bk_type('.$bk_type->id.');"><img src="'.WPDEV_BK_PLUGIN_URL.'/img/delete_type.png" width="8" height="8"   /></a>'; ?>
                    <?php if ( $is_edit !== 'noedit') if ($is_parent !== false) echo apply_bk_filter('showing_capacity_of_bk_res_in_top_line', '', $bk_type, $is_parent ); ?>                    
                </div>
                <div  id="bktypeedit<?php echo $bk_type->id; ?>" style="float:left;display:none;line-height:32px;">
                        <input type="text" id="edit_bk_type<?php echo $bk_type->id; ?>" name="edit_bk_type<?php echo $bk_type->id; ?>" class="add_type_field" value="<?php echo $bk_type->title; ?>" />
                        <input  type="button" class="button-secondary" onclick="javascript:save_edit_bk_type(<?php echo $bk_type->id; ?>);" value=" Edit " />
                </div>
            <?php
        }

        // Show line of adding new
        function booking_types_pages($is_edit = ''){

            $types_list = $this->get_booking_types(true);
            if ( $is_edit !== 'noedit' ) $link_base = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME . 'wpdev-booking&booking_type=' ;
            else                         $link_base = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME . 'wpdev-booking-reservation&booking_type=' ;

            $link_base_plus = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME . 'wpdev-booking-resources' ;


  ?>
            <div style="
    background:-moz-linear-gradient(center bottom , #EBEBEB, #F5F5F5) repeat scroll 0 0 transparent;
    border-radius:5px;
    -moz-border-radius:5px;
    -webkit-border-radius:5px;
    box-shadow:0 2px 3px #C8C7C7;
    -moz-box-shadow:0 2px 3px #C8C7C7;
    -webkit-box-shadow:0 2px 3px #C8C7C7;
    height:30px;
    margin-top:5px;
    padding:3px 10px;">
                <div style="float:left;line-height: 32px;font-size:13px;font-weight: bold;text-shadow:0px -1px 0px #fff;color:#555;">


                        <label for="calendar_type" style="vertical-align: top;"><?php _e('Booking resource selection:', 'wpdev-booking'); ?></label>

                        <select id="calendar_type" name="calendar_type"
                            onchange="javascript: if (this.value == '+') location.href='<?php echo $link_base_plus; ?>'; else location.href='<?php echo $link_base; ?>' + this.value;"
                            >
                        <?php
                             if ( $is_edit !== 'noedit') {  ?>

                                    <option value="-1"
                                                style="<?php  echo 'font-weight:normal';  ?>"
                                        <?php  if (isset($_GET['booking_type'])   ) if ($_GET['booking_type'] ==  '-1' ) echo ' selected="SELECTED" ';  ?>
                                    ><?php echo __('All bookings','wpdev-booking'); ?></option>
                                    <option value="0"
                                                style="<?php  echo 'font-weight:normal';  ?>"
                                        <?php  if (isset($_GET['booking_type'])   ) if ($_GET['booking_type'] ==  '0' ) echo ' selected="SELECTED" ';  ?>
                                    ><?php echo __('All incoming bookings','wpdev-booking'); ?></option><?php

                                    ?><option value="0&bk_filter=today_new"
                                                style="<?php  echo 'font-weight:normal';  ?>"
                                        <?php  if (isset($_GET['bk_filter'])   ) if ($_GET['bk_filter'] ==  'today_new' ) echo ' selected="SELECTED" ';  ?>
                                    ><?php echo __('Reservations, which was done today','wpdev-booking'); ?></option><?php

                                    ?><option value="0&bk_filter=today_all"
                                                style="<?php  echo 'font-weight:normal';  ?>"
                                        <?php  if (isset($_GET['bk_filter'])   ) if ($_GET['bk_filter'] ==  'today_all' ) echo ' selected="SELECTED" ';  ?>
                                    ><?php echo __('Agenda of bookings for today','wpdev-booking'); ?></option>
                                    <option value="0" >_____________________________</option><?php
                             }

                            foreach ($types_list as $tl) { ?>
                                <option value="<?php echo $tl->id; if  (isset($tl->parent)) if ($tl->parent == 0 )  if ($tl->count > 1 ) echo '&parent_res=1'; ?>"
                                            style="<?php if  (isset($tl->parent)) if ($tl->parent == 0 ) { echo 'font-weight:bold;'; } else { echo 'font-size:11px;padding-left:20px;'; } ?>"
                                    <?php  if (isset($_GET['booking_type'])) if ($_GET['booking_type'] ==  $tl->id ) echo ' selected="SELECTED" ';  ?>
                                ><?php echo $tl->title; ?></option>
                                <?php

                                if  (isset($tl->parent)) if ($tl->parent == 0 )  if ($tl->count > 1 ) { ?>
                                    <option value="<?php echo $tl->id; ?>"
                                                style="<?php  echo 'font-size:11px;padding-left:20px;';  ?>"
                                        <?php  if (isset($_GET['booking_type']) && (! isset($_GET['parent_res']))  ) if ($_GET['booking_type'] ==  $tl->id ) echo ' selected="SELECTED" ';  ?>
                                    ><?php echo $tl->title; ?></option><?php
                                }

                            }
                            if ( $is_edit !== 'noedit') {  ?>
                                  <option value="0" >_____________________________</option>
                                  <option value="+" style="font-weight:bold" ><?php echo '+ ',__('Add new booking resource','wpdev-booking'); ?></option>
                            <?php } ?>
                        </select>


                </div>
                <?php if ( $is_edit === 'noedit') { make_bk_action('wpdev_show_booking_form_selection' );   } ?>
                <?php if ( $is_edit === 'noedit') { make_bk_action('wpdev_show_autofill_button' );   } ?>
            </div>
            <div class="clear topmenuitemseparatorv" style="height:0px;clear:both;border-bottom:1px solid #C5C5C5;
    border-top:0 solid #EEEEEE;
    margin:0 6px;" ></div>

        <?php
        }



 // P A R S E   F o r m                      //////////////////////////////////////////////////////////////////////////////////////////////////
        function get_booking_form($my_boook_type, $my_booking_form = 'standard'){

            if ($my_booking_form == 'standard') {
                $booking_form  = get_bk_option( 'booking_form' );
                if (isset($_GET['booking_form'])) {
                    $my_booking_form = $_GET['booking_form'];
                    $booking_form  = apply_bk_filter('wpdev_get_booking_form', $booking_form, $my_booking_form);
                }

            } else {
                 $booking_form  = get_bk_option( 'booking_form' );
                 $booking_form  = apply_bk_filter('wpdev_get_booking_form', $booking_form, $my_booking_form);
            }

            $booking_form =  apply_bk_filter('wpdev_check_for_active_language', $booking_form );
            $this->current_booking_type = $my_boook_type;

                $my_edited_bk_id = false;
                if (isset($_GET['booking_hash'])) {
                    $my_booking_id_type = apply_bk_filter('wpdev_booking_get_hash_to_id',false, $_GET['booking_hash'] );
                    if ($my_booking_id_type !== false) {
                        if (   ($my_booking_id_type[1] == '') ){
                            
                        } else {
                            $my_boook_type        = $my_booking_id_type[1];
                            $my_edited_bk_id = $my_booking_id_type[0];
                            $this->current_booking_type = $my_booking_id_type[1];
                        }
                    }
                }

            if ($my_edited_bk_id !== false)  $this->current_edit_booking = $this->get_booking_data($my_edited_bk_id);
            else                             $this->current_edit_booking =  false;

            $return_res = $this->form_elements($booking_form);
            $return_res = apply_bk_filter('wpdev_reapply_bk_form',$return_res, $this->current_booking_type);
            if ( $my_edited_bk_id !== false ) { $return_res .= '<input name="edit_booking_id"  id="edit_booking_id" type="hidden" value="'.$my_edited_bk_id.'">'; }
            
            if ( $my_booking_form != 'standard' ) { $return_res .= '<input name="booking_form_type'.$my_boook_type.'"  id="booking_form_type'.$my_boook_type.'" type="hidden" value="'.$my_booking_form.'">'; }

            if ( $my_edited_bk_id !== false ){

                ?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        timeout_DSwindow=setTimeout("setDaySelections()",1500);
    });

    function setDaySelections(){
// document.getElementById('date_booking1').style.display = 'block';
        clearTimeout(timeout_DSwindow);
        
        var bk_type = <?php echo $my_boook_type; ?>;
        var inst = jQuery.datepick._getInst(document.getElementById('calendar_booking'+bk_type)); 
        inst.dates = [];  
        var original_array = []; var date;

        var bk_inputing = document.getElementById('date_booking' + bk_type);
        var bk_distinct_dates = [];
            <?php foreach ($this->current_edit_booking['dates'] as $dt) {
                    $dt = trim($dt);
                    $dta = explode(' ',$dt);
                    $tms = $dta[1];
                    $tms = explode(':' , $tms);
                    $dta = $dta[0];
                    $dta = explode('-',$dta);
             ?>
                    date=new Date();
                    date.setFullYear( <?php echo $dta[0].', '.($dta[1]-1).', '.$dta[2]; ?> );    // get date
                    original_array.push( jQuery.datepick._restrictMinMax(inst, jQuery.datepick._determineDate(inst, date, null))  ); //add date


                    if ( !  wpdev_in_array(bk_distinct_dates, '<?php echo $dta[2].'.'.($dta[1]).'.'.$dta[0]; ?>' ) ) {
                        bk_distinct_dates.push('<?php echo $dta[2].'.'.($dta[1]).'.'.$dta[0]; ?>');
                    }

        <?php     } ?>
        for(var j=0; j < original_array.length ; j++) {       //loop array of dates
            if (original_array[j] != -1) inst.dates.push(original_array[j]);
        }
        dateStr = (inst.dates.length == 0 ? '' : jQuery.datepick._formatDate(inst, inst.dates[0])); // Get first date
        for ( i = 1; i < inst.dates.length; i++)
             dateStr += jQuery.datepick._get(inst, 'multiSeparator') +  jQuery.datepick._formatDate(inst, inst.dates[i]);  // Gathering all dates
        jQuery('#date_booking' + bk_type).val(dateStr); // Fill the input box

        if (original_array.length>0) { // Set showing of start month
            inst.cursorDate = original_array[0];
            inst.drawMonth = inst.cursorDate.getMonth();
            inst.drawYear = inst.cursorDate.getFullYear();
        }
        
        // Update calendar
        jQuery.datepick._notifyChange(inst);
        jQuery.datepick._adjustInstDate(inst);
        jQuery.datepick._showDate(inst);
        jQuery.datepick._updateDatepick(inst);
        
        if (bk_inputing != null)
            bk_inputing.value = bk_distinct_dates.join(', ');

        //jQuery.datepick._updateInput('date_booking' + bk_type);

    }
    //jQuery('#ajax_message').fadeOut(1000);
    //location.reload(true);
</script>       <?php
            }
            
            return $return_res;
        }

            function get_booking_data($booking_id){
                global $wpdb;

                if (isset($booking_id)) $booking_id = ' WHERE  bk.booking_id = ' . $booking_id . ' ';
                else $booking_id = ' ';
                            $sql = "SELECT *

                            FROM ".$wpdb->prefix ."booking as bk

                            INNER JOIN ".$wpdb->prefix ."bookingdates as dt

                            ON    bk.booking_id = dt.booking_id

                            ". $booking_id ."   ORDER BY dt.booking_date ASC ";

                $result = $wpdb->get_results( $wpdb->prepare($sql) );
                $return = array( 'dates'=>array());
                foreach ($result as $res) { $return['dates'][] = $res->booking_date; }
                $return['form'] = $res->form;
                $return['type'] = $res->booking_type;
                $return['approved'] = $res->approved;
                $return['id'] = $res->booking_id;

                // Parse data from booking form ////////////////////////////////////
                $bktype = $res->booking_type;
                $parsed_form = $res->form;
                $parsed_form = explode('~',$parsed_form);

                $parsed_form_results  = array();
   
                foreach ($parsed_form as $field) {
                    $elemnts = explode('^',$field);
                    $type = $elemnts[0];
                    $element_name = $elemnts[1];
                    $value = $elemnts[2];

                    $count_pos = strlen( $bktype );
                    //debuge(substr( $elemnts[1], 0, -1*$count_pos ))                ;
                    $type_name = $elemnts[1];
                    $type_name = str_replace('[]','',$type_name);
                    if ($bktype == substr( $type_name,  -1*$count_pos ) ) $type_name = substr( $type_name, 0, -1*$count_pos );

                    if ($type_name == 'email') { $email_adress = $value; }
                    if ($type_name == 'name')  { $name_of_person = $value; }
                    if ($type == 'checkbox') {
                        if ($value == 'true')   { $value = 'on'; }
                        else {
                            if (($value == 'false') || ($value == 'Off') || ( !isset($value) ) )  $value = '';
                        }
                    }
                    if (($type == 'endtime') || ($type == 'starttime')) {
                       //str_replace(':','',$value);
                    }
                    $element_name = str_replace('[]','',$element_name);
                    if ( isset($parsed_form_results[$element_name]) ) {
                        if ($value !=='') $parsed_form_results[$element_name]['value'] .= ',' . $value;
                    } else
                        $parsed_form_results[$element_name] = array('value'=>$value, 'type'=> $type, 'element_name'=>$type_name );
                }
                $return['parsed_form'] = $parsed_form_results;
                ////////////////////////////////////////////////////////////////////
                if (isset($email_adress))   $return['email'] = $email_adress;
                if (isset($name_of_person)) $return['name'] = $name_of_person;

                return $return;
            }

                // Getted from script under GNU /////////////////////////////////////
                function form_elements($form, $replace = true) {
                        $types = 'text[*]?|email[*]?|coupon[*]?|time[*]?|textarea[*]?|select[*]?|checkbox[*]?|radio|acceptance|captchac|captchar|file[*]?|quiz|hidden';
                        $regex = '%\[\s*(' . $types . ')(\s+[a-zA-Z][0-9a-zA-Z:._-]*)([-0-9a-zA-Z:#_/|\s]*)?((?:\s*(?:"[^"]*"|\'[^\']*\'))*)?\s*\]%';
                        $regex_start_end_time = '%\[\s*(country[*]?|starttime[*]?|endtime[*]?)(\s*[a-zA-Z]*[0-9a-zA-Z:._-]*)([-0-9a-zA-Z:#_/|\s]*)*((?:\s*(?:"[^"]*"|\'[^\']*\'))*)?\s*\]%';
                        $submit_regex = '%\[\s*submit(\s[-0-9a-zA-Z:#_/\s]*)?(\s+(?:"[^"]*"|\'[^\']*\'))?\s*\]%';
                        if ($replace) {
                                $form = preg_replace_callback($regex, array(&$this, 'form_element_replace_callback'), $form);
                                // Start end time
                                $form = preg_replace_callback($regex_start_end_time, array(&$this, 'form_element_replace_callback'), $form);
                                // Submit button
                                $form = preg_replace_callback($submit_regex, array(&$this, 'submit_replace_callback'), $form);
                                return $form;
                        } else {
                                $results = array();
                                preg_match_all($regex, $form, $matches, PREG_SET_ORDER);
                                foreach ($matches as $match) {
                                        $results[] = (array) $this->form_element_parse($match);
                                }
                                return $results;
                        }
                }

                function form_element_replace_callback($matches) {
                        extract((array) $this->form_element_parse($matches)); // $type, $name, $options, $values, $raw_values
//debuge('1!!!!!', $type, $name, $options, $values, $raw_values);
                        if ( ($type == 'country') || ($type == 'country*') ) {
                            //debuge('$type, $name, $options, $values, $raw_values', $type, $name, $options, $values, $raw_values);
                            if ( empty($name) )
                                $name = $type ;
                        }
                        $name .= $this->current_booking_type ;


                        $my_edited_bk_id = false;
                        if (isset($_GET['booking_hash'])) {
                            $my_booking_id_type = apply_bk_filter('wpdev_booking_get_hash_to_id',false, $_GET['booking_hash'] );
                            if ($my_booking_id_type !== false) {
                                $my_edited_bk_id = $my_booking_id_type[0];  //$bk_type        = $my_booking_id_type[1];
                            }
                        }
                        //if (isset($_GET['booking_id'])) $my_edited_bk_id = $_GET['booking_id'];
                        //else $my_edited_bk_id = false;


                        // Edit values
                        if ( $my_edited_bk_id !== false ) {
                              if (preg_match('/^(?:select|country|checkbox|radio)[*]?$/', $type)) {
                                  
                                  if (isset($this->current_edit_booking['parsed_form'][$name]))
                                          if (isset($this->current_edit_booking['parsed_form'][$name]['value'])) {
                                            $options[0] = 'default:' . $this->current_edit_booking['parsed_form'][$name]['value'];
                                          }
                              } else {
                                    if ( ($type == 'starttime') || ($type == 'starttime*') || ($type == 'endtime') || ($type == 'endtime*') )
                                        $values[0] = $this->current_edit_booking['parsed_form'][$type . $this->current_booking_type ]['value'];
                                    elseif ( ($type == 'country') || ($type == 'country*') )
                                        $options[0] = $this->current_edit_booking['parsed_form'][$type . $this->current_booking_type ]['value'];
                                    else {
                                        $values[0] = '';
                                        if (isset($this->current_edit_booking['parsed_form'][$name]))
                                                if (isset($this->current_edit_booking['parsed_form'][$name]['value']))
                                                    $values[0] = $this->current_edit_booking['parsed_form'][$name]['value'];
                                    }
                              }

                        }
//debuge('value et option', $values,$options);
                        if (isset($this->processing_unit_tag)) {
                            if ($this->processing_unit_tag == $_POST['wpdev_unit_tag']) {
                                    $validation_error = $_POST['wpdev_validation_errors']['messages'][$name];
                                    $validation_error = $validation_error ? '<span class="wpdev-not-valid-tip-no-ajax">' . $validation_error . '</span>' : '';
                            } else {
                                    $validation_error = '';
                            }
                        } else  $validation_error = '';
                        $atts = '';

                if(isset($options[0]) && $options[0] == "NumberOnly") $SUC = true;
                else $SUC = false;

                $options = (array) $options;

                $id_array = preg_grep('%^id:[-0-9a-zA-Z_]+$%', $options);
                if ($id = array_shift($id_array)) {
                    preg_match('%^id:([-0-9a-zA-Z_]+)$%', $id, $id_matches);
                    if ($id = $id_matches[1])
                        $atts .= ' id="' . $id . $this->current_booking_type .'"';
                }

                $class_att = "";
                $class_array = preg_grep('%^class:[-0-9a-zA-Z_]+$%', $options);
                foreach ($class_array as $class) {
                    preg_match('%^class:([-0-9a-zA-Z_]+)$%', $class, $class_matches);
                    if ($class = $class_matches[1])
                        $class_att .= ' ' . $class;
                }

                if (preg_match('/^email[*]?$/', $type))
                    $class_att .= ' wpdev-validates-as-email';

                if (preg_match('/^coupon[*]?$/', $type))
                    $class_att .= ' wpdev-validates-as-coupon';

                if (preg_match('/^time[*]?$/', $type))
                    $class_att .= ' wpdev-validates-as-time';
                if (preg_match('/^starttime[*]?$/', $type))
                    $class_att .= ' wpdev-validates-as-time';
                if (preg_match('/^endtime[*]?$/', $type))
                    $class_att .= ' wpdev-validates-as-time';
                if (preg_match('/[*]$/', $type))
                    $class_att .= ' wpdev-validates-as-required';

                if (preg_match('/^checkbox[*]?$/', $type))
                    $class_att .= ' wpdev-checkbox';

                if ('radio' == $type)
                    $class_att .= ' wpdev-radio';

                if (preg_match('/^captchac$/', $type))
                    $class_att .= ' wpdev-captcha-' . $name;

                if ('acceptance' == $type) {
                    $class_att .= ' wpdev-acceptance';
                    if (preg_grep('%^invert$%', $options))
                        $class_att .= ' wpdev-invert';
                }

                if ($class_att)
                    $atts .= ' class="' . trim($class_att) . '"';

                        // Value.
                        if (   (isset($this->processing_unit_tag)) && ($this->processing_unit_tag == $_POST['wpdev_unit_tag']) ) {
                                if (isset($_POST['wpdev_mail_sent']) && $_POST['wpdev_mail_sent']['ok'])
                                        $value = '';
                                elseif ('captchar' == $type)
                                        $value = '';
                                else
                                        $value = $_POST[$name];
                        } else {
                            if (isset($values[0])) $value = $values[0];
                            else $value = '';
                        }

                // Default selected/checked for select/checkbox/radio
                if (preg_match('/^(?:select|checkbox|radio)[*]?$/', $type)) {
//debuge('$options',$options);
                    $scr_defaults = array_values(preg_grep('/^default:/', $options));
//debuge($scr_defaults);
                    if (isset($scr_defaults[0]))   preg_match('/^default:([^~]+)$/', $scr_defaults[0], $scr_default_matches);

                    if (isset($scr_default_matches[1])) $scr_default = explode('_', $scr_default_matches[1]);
                    else $scr_default = '';

                    $scr_default = str_replace( '&#37;','%', $scr_default );
//debuge('$scr_default, $scr_defaults',$scr_default,$scr_defaults);
                }

                if (preg_match('/^(?:country)[*]?$/', $type)) {
               // debuge($options);
                    $scr_defaults = array_values(preg_grep('/^default:/', $options));
//debuge($values, $value, $options);
                    if (isset($scr_defaults[0])) preg_match('/^default:([0-9a-zA-Z_:-\s]+)$/', $scr_defaults[0], $scr_default_matches);
                    if (isset($scr_default_matches[1])) $scr_default = explode('_', $scr_default_matches[1]);
                    else $scr_default = '';
              
                }


                        if ( ($type == 'starttime') || ($type == 'starttime*') )     $name = 'starttime' . $this->current_booking_type ;
                        if ( ($type == 'endtime') || ($type == 'endtime*') )         $name = 'endtime' . $this->current_booking_type ;

                        switch ($type) {
                                case 'starttime':  
                                case 'starttime*':
                                case 'endtime':
                                case 'endtime*':  
                                case 'time':
                                case 'time*':
                                case 'text':
                                case 'text*':
                                case 'email':
                                case 'email*':
                                case 'coupon':
                                case 'coupon*':
                                case 'captchar':
                                        if (is_array($options)) {
                                                $size_maxlength_array = preg_grep('%^[0-9]*[/x][0-9]*$%', $options);
                                                if ($size_maxlength = array_shift($size_maxlength_array)) {
                                                        preg_match('%^([0-9]*)[/x]([0-9]*)$%', $size_maxlength, $sm_matches);
                                                        if ($size = (int) $sm_matches[1])
                                                                $atts .= ' size="' . $size . '"';
                                else
                                    $atts .= ' size="40"';
                                                        if ($maxlength = (int) $sm_matches[2])
                                                                $atts .= ' maxlength="' . $maxlength . '"';
                                                } else {
                                $atts .= ' size="40"';
                            }
                                        }

                                        if ( ($type=='coupon') || ($type=='coupon*'))
                                            $additional_js = ' onchange="javascript:if(typeof( showCostHintInsideBkForm ) == \'function\') {  showCostHintInsideBkForm('.$this->current_booking_type.');}" ';
                                        else
                                            $additional_js = '';

                                        if($SUC){
                                            $script = "
                                                <script type=\"text/javascript\">
                                                    new SUC( document.getElementById(\"$name\") );
                                                </script>";
                                        }else{
                                            $script = "";
                                        }

                                        $html = '<input type="text" name="' . $name . '" id="' . $name . '" value="' . esc_attr($value) . '"' . $atts . $additional_js . ' />';
                                        $html = '<span class="wpdev-form-control-wrap ' . $name . '">' . $html . $validation_error . '</span>'.$script;
                                        return $html;
                                        break;
                                case 'textarea':
                                case 'textarea*':
                                        if (is_array($options)) {
                                                $cols_rows_array = preg_grep('%^[0-9]*[x/][0-9]*$%', $options);
                                                if ($cols_rows = array_shift($cols_rows_array)) {
                                                        preg_match('%^([0-9]*)[x/]([0-9]*)$%', $cols_rows, $cr_matches);
                                                        if ($cols = (int) $cr_matches[1])
                                                                $atts .= ' cols="' . $cols . '"';
                                else
                                    $atts .= ' cols="40"';
                                                        if ($rows = (int) $cr_matches[2])
                                                                $atts .= ' rows="' . $rows . '"';
                                else
                                    $atts .= ' rows="10"';
                                                } else {
                                $atts .= ' cols="40" rows="10"';
                            }
                                        }
                                        $html = '<textarea name="' . $name . '"' . $atts . '>' . $value . '</textarea>';
                                        $html = '<span class="wpdev-form-control-wrap ' . $name . '">' . $html . $validation_error . '</span>';
                                        return $html;
                                        break;
                                case 'country':
                                case 'country*':
                                        
                                        $html = '';
                                        //debuge($values, $empty_select);
                                        foreach ($this->countries_list as $key => $value_country) {
                                            $selected = '';
//debuge($key , $value, $scr_default, in_array($key , (array) $scr_default) );
                                            if ( in_array($key , (array) $scr_default)) $selected = ' selected="selected"';
                                            if ($value == $key ) { $selected = ' selected="selected"'; }
                                            //if ($this->processing_unit_tag == $_POST['wpdev_unit_tag'] && ( $multiple && in_array($value, (array) $_POST[$name]) || ! $multiple && $_POST[$name] == $value)) $selected = ' selected="selected"';
                                            $html .= '<option value="' . esc_attr($key) . '"' . $selected . '>' . $value_country . '</option>';
                                        }
                                        $html = '<select name="' . $name   . '"' . $atts . '>' . $html . '</select>';
                                        $html = '<span class="wpdev-form-control-wrap ' . $name . '">' . $html . $validation_error . '</span>';
                                        return $html;
                                        break;

                                case 'select':
                                case 'select*':
                        $multiple = (preg_grep('%^multiple$%', $options)) ? true : false;
                        $include_blank = preg_grep('%^include_blank$%', $options);

                        if ($empty_select = empty($values) || $include_blank)
                                array_unshift($values, '---');

                        $html = '';
                        if($name == 'starttime'.$this->current_booking_type || $name == 'starttime_samedi'.$this->current_booking_type){
                            $html .= '<option value=" ">selectionnez un horaire</option>';
                        }

                        if ($name == 'visitors' . $this->current_booking_type){
                            global $wpdb;

                            $requete = "SELECT maxvisitor FROM ". $wpdb->prefix."bookingtypes WHERE booking_type_id = '". $this->current_booking_type . "'";
                            $maxvisitor = $wpdb->get_results($wpdb->prepare($requete));

                            for($i = 1; $i <= $maxvisitor[0]->maxvisitor; $i++)
                                $html .= '<option value="' . $i . '">' . $i . '</option>';
                        }else{
                //debuge($values, $empty_select);
                            foreach ($values as $key => $value) {
                                $selected = '';
                                //debuge($key , $value, $scr_default, in_array($value , (array) $scr_default) );
                                if ( in_array($value , (array) $scr_default))
                                    $selected = ' selected="selected"';
                                if ( (isset($this->processing_unit_tag)) && ($this->processing_unit_tag == $_POST['wpdev_unit_tag']) && (
                                        $multiple && in_array($value, (array) $_POST[$name]) ||
                                        ! $multiple && $_POST[$name] == $value))
                                    $selected = ' selected="selected"';

                               // debuge($name, $atts);
                                if ( ($name == 'rangetime' . $this->current_booking_type ) && (strpos($atts,'hideendtime')!== false ) )
                                    $html .= '<option value="' . esc_attr($value) . '"' . $selected . '>' . substr($value,0, strpos($value,'-')) . '</option>';
                                elseif  ($name == 'rangetime' . $this->current_booking_type ) {
                                    $time_format = get_bk_option( 'booking_time_format');

                                    $value_times = explode('-', $value);
                                    $value_times[0] = trim($value_times[0]);
                                    $value_times[1] = trim($value_times[1]);

                                    $s_tm = explode(':', $value_times[0]);
                                    $e_tm = explode(':', $value_times[1]);

                                    $s_tm_value = $s_tm;
                                    $e_tm_value = $e_tm;

                                    $s_tm = date_i18n($time_format, mktime($s_tm[0], $s_tm[1]));
                                    $e_tm = date_i18n($time_format, mktime($e_tm[0], $e_tm[1]));
                                    $t_delimeter = ' - ';
                                    if (strpos($atts,'hideendtime')!== false ) {
                                       $e_tm = '';
                                       $t_delimeter = '';
                                    }

                                    // Recheck for some errors in time formating of shortcode, like whitespace or empty zero before hours less then 10am
                                    $s_tm_value[0] = trim($s_tm_value[0]);
                                    $s_tm_value[1] = trim($s_tm_value[1]);
                                    if ( ($s_tm_value[0] + 0) < 10 ) $s_tm_value[0] = '0' . ($s_tm_value[0] + 0);
                                    if ( ($s_tm_value[1] + 0) < 10 ) $s_tm_value[1] = '0' . ($s_tm_value[1] + 0);
                                    $e_tm_value[0] = trim($e_tm_value[0]);
                                    $e_tm_value[1] = trim($e_tm_value[1]);
                                    if ( ($e_tm_value[0] + 0) < 10 ) $e_tm_value[0] = '0' . ($e_tm_value[0] + 0);
                                    if ( ($e_tm_value[1] + 0) < 10 ) $e_tm_value[1] = '0' . ($e_tm_value[1] + 0);

                                    $value_time_range =  $s_tm_value[0] . ':' . $s_tm_value[1] . $t_delimeter . $e_tm_value[0] . ':' . $e_tm_value[1];

                                    $html .= '<option value="' . esc_attr($value_time_range) . '"' . $selected . '>' . $s_tm . $t_delimeter . $e_tm . '</option>';

                                } elseif  ($name == 'starttime' . $this->current_booking_type ) {
                                    $time_format = get_bk_option( 'booking_time_format');
                                    $s_tm = explode(':', $value);
                                    $s_tm = date_i18n($time_format, mktime($s_tm[0], $s_tm[1]));
                                    $html .= '<option value="' . esc_attr($value) . '"' . $selected . '>' . $s_tm  . '</option>';
                                    $tout_horaire[] = $value;

                                } elseif  ($name == 'endtime' . $this->current_booking_type ) {
                                    $time_format = get_bk_option( 'booking_time_format');
                                    $s_tm = explode(':', $value);
                                    $s_tm = date_i18n($time_format, mktime($s_tm[0], $s_tm[1]));
                                    $html .= '<option value="' . esc_attr($value) . '"' . $selected . '>' . $s_tm  . '</option>';

                                } else {
                                    $html .= '<option value="' . esc_attr($value) . '"' . $selected . '>' . $value . '</option>';
    //debuge($value, $selected, $scr_default);
                                    }
                            }
                        }

                        if ($multiple)
                            $atts .= ' multiple="multiple"';

////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////// WTB /////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
                                        if($name == "starttime" . $this->current_booking_type)
                                        {
                                            $change =  '; selectAnglais(this.options[this.selectedIndex], '.$this -> current_booking_type.')';
                                            $span_starttime = '<p id="textstarttime" class="textstarttime"> </p>';
                                            $script = "<script type=\"text/javascript\"> var tout_horaire = new Array();";
                                            for($i = 0; $i < count($tout_horaire); $i++) {
                                                $script .= "tout_horaire.push(\"$tout_horaire[$i]\");"; 
                                            }
                                            $script .= "</script>";
                                        }else if($name == "visitors" . $this->current_booking_type){
                                            $change ='';
                                            $span_starttime = '';
                                            $script = ' <script type="text/javascript">';
                                            $script .= 'if(maxvisitor != undefined){';
                                            $script .= 'maxvisitor['.$this->current_booking_type.'] = '.$maxvisitor[0]->maxvisitor.';';
                                            $script .= '}else{';
                                            $script .= 'var maxvisitor = new Array();';
                                            $script .= 'maxvisitor['.$this->current_booking_type.'] = '.$maxvisitor[0]->maxvisitor.';}';
                                            $script .= '</script>';
                                        }else
                                        {
                                            $change ='';
                                            $span_starttime = '';
                                            if(!isset($script)) $script = '';
                                        }

////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////// FIN WTB ///////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

                                        $id = $name;

                                        $html = '<select id="'.$id.'" onchange="javascript:if(typeof( showCostHintInsideBkForm ) == \'function\') {  showCostHintInsideBkForm('.$this->current_booking_type.');} '.$change.'" name="' . $name . ($multiple ? '[]' : '') . '"' . $atts . '>' . $html . '</select>';
                                        $html = '<span class="wpdev-form-control-wrap ' . $name . '">' . $html . $validation_error . '</span>';
//debuge($options, $values, $scr_default, $html);die;
                                        $html .= $span_starttime . $script;

                                        return $html;
                                        break;
                    case 'checkbox':
                    case 'checkbox*':
                    case 'radio':
                        $multiple = (preg_match('/^checkbox[*]?$/', $type) && ! preg_grep('%^exclusive$%', $options)) ? true : false;
                        $html = '';

                        if (preg_match('/^checkbox[*]?$/', $type) && ! $multiple) $onclick = ' onclick="wpdevExclusiveCheckbox(this);"';

                        $defaultOn = (bool) preg_grep('%^default:on$%', $options);
                        $defaultOn = $defaultOn ? ' checked="checked"' : '';

                        $input_type = rtrim($type, '*');

                        foreach ($values as $key => $value) {
                            $checked = '';
                            $multi_values = str_replace('default:', '', $options[0]);
                            $multi_values_array = explode(',',$multi_values);

                            foreach ($multi_values_array as $mv) {
                                if ( ( trim($mv) == trim($value) ) && ($value !=='') ) $checked = ' checked="checked"';
                            }

                            if (in_array($key + 1, (array) $scr_default))
                                $checked = ' checked="checked"';
                            if ( (isset($this->processing_unit_tag)) && ($this->processing_unit_tag == $_POST['wpdev_unit_tag']) && (
                                    $multiple && in_array($value, (array) $_POST[$name]) ||
                                    ! $multiple && $_POST[$name] == $value))
                                $checked = ' checked="checked"';
                            if (! isset($onclick)) $onclick = '';
                            if (preg_grep('%^label[_-]?first$%', $options)) { // put label first, input last
                                $item = '<span class="wpdev-list-item-label">' . $value . '</span>&nbsp;';
                                $item .= '<input '.$atts.'  onchange="javascript:if(typeof( showCostHintInsideBkForm ) == \'function\') {  showCostHintInsideBkForm('.$this->current_booking_type.');}"    type="' . $input_type . '" name="' . $name . ($multiple ? '[]' : '') . '" value="' . esc_attr($value) . '"' . $checked . $onclick . $defaultOn .  ' />';
                            } else {

                                /////////////////////////////////////////////////////////////////////////////////////
                                //AJOUT WTB : ajoute à checkbox un id et un event onClick si c'est une box anglais
                                /////////////////////////////////////////////////////////////////////////////////////
                                if(substr($name, 0, 7) == "anglais")
                                    $click = 'onClick = "anglais(this,  '.$this -> current_booking_type.', true)"';
                                else
                                    $click = "";

                                /////////////////////////////////////////////////////////////////////////////////////

                                $item = '<input '.$atts.' id="'.$name.'" onchange="javascript:if(typeof( showCostHintInsideBkForm ) == \'function\') {  showCostHintInsideBkForm('.$this->current_booking_type.');}"   type="' . $input_type . '" '.$click .' name="' . $name . ($multiple ? '[]' : '') . '" value="' . esc_attr($value) . '"' . $checked . $onclick . $defaultOn . ' />';
                                $item .= '&nbsp;<span class="wpdev-list-item-label">' . $value . '</span>';
                            }

                            $item = '<span class="wpdev-list-item">' . $item . '</span>';
                            $html .= $item;
                        }

                        $html = '<span' . $atts . '>' . $html . '</span>';
                                        $html = '<span class="wpdev-form-control-wrap ' . $name . '">' . $html . $validation_error . '</span>';
 //debuge($options, $values, $scr_default, $html);die;
                                        return $html;
                                        break;
                    case 'quiz':
                        if (count($raw_values) == 0 && count($values) == 0) { // default quiz
                            $raw_values[] = '1+1=?|2';
                            $values[] = '1+1=?';
                        }

                        $pipes = $this->get_pipes($raw_values);

                        if (count($values) == 0) {
                            break;
                        } elseif (count($values) == 1) {
                            $value = $values[0];
                        } else {
                            $value = $values[array_rand($values)];
                        }

                        $answer = $this->pipe($pipes, $value);
                        $answer = $this->canonicalize($answer);

                                        if (is_array($options)) {
                                                $size_maxlength_array = preg_grep('%^[0-9]*[/x][0-9]*$%', $options);
                                                if ($size_maxlength = array_shift($size_maxlength_array)) {
                                                        preg_match('%^([0-9]*)[/x]([0-9]*)$%', $size_maxlength, $sm_matches);
                                                        if ($size = (int) $sm_matches[1])
                                                                $atts .= ' size="' . $size . '"';
                                else
                                    $atts .= ' size="40"';
                                                        if ($maxlength = (int) $sm_matches[2])
                                                                $atts .= ' maxlength="' . $maxlength . '"';
                                                } else {
                                $atts .= ' size="40"';
                            }
                                        }

                        $html = '<span class="wpdev-quiz-label">' . $value . '</span>&nbsp;';
                        $html .= '<input type="text" name="' . $name . '"' . $atts . ' />';
                        $html .= '<input type="hidden" name="wpdev_quiz_answer_' . $name . '" value="' . wp_hash($answer, 'wpdev_quiz') . '" />';
                                        $html = '<span class="wpdev-form-control-wrap ' . $name . '">' . $html . $validation_error . '</span>';
                                        return $html;
                        break;
                    case 'acceptance':
                        $invert = (bool) preg_grep('%^invert$%', $options);
                        $default = (bool) preg_grep('%^default:on$%', $options);

                        $onclick = ' onclick="wpdevToggleSubmit(this.form);"';
                        $checked = $default ? ' checked="checked"' : '';
                        $html = '<input type="checkbox" name="' . $name . '" value="1"' . $atts . $onclick . $checked . ' />';
                        return $html;
                        break;
                                case 'captchac':
                        if (! class_exists('ReallySimpleCaptcha')) {
                            return '<em>' . 'To use CAPTCHA, you need <a href="http://wordpress.org/extend/plugins/really-simple-captcha/">Really Simple CAPTCHA</a> plugin installed.' . '</em>';
                            break;
                        }

                                        $op = array();
                                        // Default
                                        $op['img_size'] = array(72, 24);
                                        $op['base'] = array(6, 18);
                                        $op['font_size'] = 14;
                                        $op['font_char_width'] = 15;

                                        $op = array_merge($op, $this->captchac_options($options));

                                        if (! $filename = $this->generate_captcha($op)) {
                                                return '';
                                                break;
                                        }
                                        if (is_array($op['img_size']))
                                                $atts .= ' width="' . $op['img_size'][0] . '" height="' . $op['img_size'][1] . '"';
                                        $captcha_url = trailingslashit($this->captcha_tmp_url()) . $filename;
                                        $html = '<img alt="captcha" src="' . $captcha_url . '"' . $atts . ' />';
                                        $ref = substr($filename, 0, strrpos($filename, '.'));
                                        $html = '<input type="hidden" name="wpdev_captcha_challenge_' . $name . '" value="' . $ref . '" />' . $html;
                                        return $html;
                                        break;
                    case 'file':
                    case 'file*':
                        $html = '<input type="file" name="' . $name . '"' . $atts . ' value="1" />';
                        $html = '<span class="wpdev-form-control-wrap ' . $name . '">' . $html . $validation_error . '</span>';
                        return $html;
                        break;
                    case 'hidden':
                        $html = '<input type="hidden" name="'.$name.'" value="'.$value.'" id="'.$name.'" />';
                        return $html; 
                        }
                }

                function submit_replace_callback($matches) {
                    $atts = '';

                    $NumCmd = "CMD-" . date("YmdHis");
                    $html = '<input type="hidden" id="numcommande'.$this->current_booking_type.'" name="NumCommande'.$this->current_booking_type.'" value="'.$NumCmd.'" />';

                    $options = preg_split('/[\s]+/', trim($matches[1]));

                    $id_array = preg_grep('%^id:[-0-9a-zA-Z_]+$%', $options);
                    if ($id = array_shift($id_array)) {
                        preg_match('%^id:([-0-9a-zA-Z_]+)$%', $id, $id_matches);
                        if ($id = $id_matches[1])
                            $atts .= ' id="' . $id . '"';
                    }

                    $class_att = '';
                    $class_array = preg_grep('%^class:[-0-9a-zA-Z_]+$%', $options);
                    foreach ($class_array as $class) {
                        preg_match('%^class:([-0-9a-zA-Z_]+)$%', $class, $class_matches);
                        if ($class = $class_matches[1])
                            $class_att .= ' ' . $class;
                    }

                    if ($class_att)
                        $atts .= ' class="' . trim($class_att) . '"';

                    if ($matches[2])   $value = $this->strip_quote($matches[2]);
                    if (empty($value)) $value = __('Send', 'wpdev-booking');
                    $ajax_loader_image_url =   WPDEV_BK_PLUGIN_URL . '/img/ajax-loader.gif';

                    if (isset($_GET['booking_hash'])) {
                        $my_booking_id_type = apply_bk_filter('wpdev_booking_get_hash_to_id',false, $_GET['booking_hash'] );
                        if ($my_booking_id_type !== false) {
                            $my_edited_bk_id = $my_booking_id_type[0];  //$bk_type        = $my_booking_id_type[1];
                            if (isset($_GET['booking_cancel'])) {
                                $value = __('Cancel', 'wpdev-booking');

                                $html .= '<input type="button" value="' . $value . '"' . $atts . ' onclick="bookingCancelByVisitor(\''.$_GET['booking_hash'].'\','.$this->current_booking_type.' );" />';
                                $html .= '<img class="ajax-loader" style="visibility: hidden;" alt="ajax loader" src="' . $ajax_loader_image_url . '" />';

                                return $html;
                            }
                        }
                    }

                    $booking_type_id = $this->current_booking_type;
                    $capacite_per_hour = get_capacite_per_hour($booking_type_id);
                    
                    $html .= '<script>';

                    $html .= 'var bktype = '.$booking_type_id.';';
                    $html .= 'if(nb_pers_per_hour != undefined){';
                    $html .= 'nb_pers_per_hour[bktype] = new Array();';

                    $html .= '}else{var nb_pers_per_hour = new Array();';
                    $html .= 'nb_pers_per_hour[bktype] = new Array();}';

                    foreach($capacite_per_hour as $key => $valeur) {
                        $key = str_replace('-', '', $key);
                        $html .= 'nb_pers_per_hour[bktype]['.$key.'] = new Array();';
                    }

                    foreach ($capacite_per_hour as $key => $valeur) {
                        foreach ($capacite_per_hour[$key] as $key2 => $valeur2) {
                            $key = str_replace('-', '', $key);
                            $html .= 'nb_pers_per_hour[bktype]['.$key.']["'.$key2.'"] = '.$valeur2.';';
                        }
                    }

                    $html .= '</script>';

                    $horaires = getHoraire($this->current_booking_type);
                    $horairesAnglais = getHoraireAnglais($this->current_booking_type);
                    $html .= '<script type="text/javascript">
                        function listeHoraireAnglais(){
                            var horaires = "'.$horairesAnglais.'";
                            return horaires;
                        }
                        function listeToutHoraire(){
                            var horaires = "'.$horaires.'";
                            return horaires;
                        }
                    </script>';


                    //WINETOURBOOKING UPDATE ID PLACE - BOOKING
                    global $wp_query;

                    $pageActuelId = $wp_query->post->ID;

                    global $wpdb;

                    $requete = "UPDATE " . $wpdb->prefix . "bookingtypes SET id_place = '$pageActuelId' 
                                WHERE booking_type_id = '$booking_type_id'";

                    $wpdb->query($wpdb->prepare( $requete ) );

                    //FIN WINETOURBOOKING
                    
                    //$html .= '<input type="button" value="' . $value . '"' . $atts . ' onclick="mybooking_submit(this.form,'.$this->current_booking_type.', \''.getBookingLocale().'\' );" />';
                    $html .= '<img class="ajax-loader" style="visibility: hidden;" alt="ajax loader" src="' . $ajax_loader_image_url . '" />';
                    $html .= '<input type="submit" class="poplight" value="Ajouter au panier" />';
                    $html .= '<input type="hidden" name="type" value="addPanier" />';
                    $html .= '<input type="hidden" name="booking_type" value="'.$this->current_booking_type.'"/>';

                    return $html;
                }

                function form_element_parse($element) {
                        $type = trim($element[1]);
                        $name = trim($element[2]);
                        $options = preg_split('/[\s]+/', trim($element[3]));

                        preg_match_all('/"[^"]*"|\'[^\']*\'/', $element[4], $matches);
                        $raw_values = $this->strip_quote_deep($matches[0]);

                        if ( preg_match('/^(select[*]?|checkbox[*]?|radio)$/', $type) || 'quiz' == $type) {
                            $pipes = $this->get_pipes($raw_values);
                            $values = $this->get_pipe_ins($pipes);
                        } else {
                            $values =& $raw_values;
                        }

                        return compact('type', 'name', 'options', 'values', 'raw_values');
                }

                function strip_quote($text) {
                        $text = trim($text);
                        if (preg_match('/^"(.*)"$/', $text, $matches))
                                $text = $matches[1];
                        elseif (preg_match("/^'(.*)'$/", $text, $matches))
                                $text = $matches[1];
                        return $text;
                }

                function strip_quote_deep($arr) {
                        if (is_string($arr))
                                return $this->strip_quote($arr);
                        if (is_array($arr)) {
                                $result = array();
                                foreach ($arr as $key => $text) {
                                        $result[$key] = $this->strip_quote($text);
                                }
                                return $result;
                        }
                }

                function pipe($pipes, $value) {
                    if (is_array($value)) {
                        $results = array();
                        foreach ($value as $k => $v) {
                            $results[$k] = $this->pipe($pipes, $v);
                        }
                        return $results;
                    }

                    foreach ($pipes as $p) {
                        if ($p[0] == $value)
                            return $p[1];
                    }

                    return $value;
                }

                function get_pipe_ins($pipes) {
                    $ins = array();
                    foreach ($pipes as $pipe) {
                        $in = $pipe[0];
                        if (! in_array($in, $ins))
                            $ins[] = $in;
                    }
                    return $ins;
                }

                function get_pipes($values) {
                    $pipes = array();

                    foreach ($values as $value) {
                        $pipe_pos = strpos($value, '|');
                        if (false === $pipe_pos) {
                            $before = $after = $value;
                        } else {
                            $before = substr($value, 0, $pipe_pos);
                            $after = substr($value, $pipe_pos + 1);
                        }

                        $pipes[] = array($before, $after);
                    }

                    return $pipes;
                }

                function pipe_all_posted($contact_form) {
                    $all_pipes = array();

                    $fes = $this->form_elements($contact_form['form'], false);
                    foreach ($fes as $fe) {
                        $type = $fe['type'];
                        $name = $fe['name'];
                        $raw_values = $fe['raw_values'];

                        if (! preg_match('/^(select[*]?|checkbox[*]?|radio)$/', $type))
                            continue;

                        $pipes = $this->get_pipes($raw_values);

                        $all_pipes[$name] = array_merge($pipes, (array) $all_pipes[$name]);
                    }

                    foreach ($all_pipes as $name => $pipes) {
                        if (isset($this->posted_data[$name]))
                            $this->posted_data[$name] = $this->pipe($pipes, $this->posted_data[$name]);
                    }
                }
                ////////////////////////////////////////////////////////////////////////



 //  A d m i n   P a n e l  ->  B o o k i n g    p a g e           //////////////////////////////////////////////////////////////////////////////////////////////////
        function wpdev_updating_bk_resource_of_booking(){
                    $booking_id   = $_POST["booking_id"];
                    $resource_id = $_POST["resource_id"];
                    global $wpdb;

                   // 0.Get dates of specific booking
                    $sql = "SELECT *
                            FROM  ".$wpdb->prefix ."booking as bk
                            WHERE booking_id = $booking_id ";
                    $res = $wpdb->get_row($wpdb->prepare( $sql ));
                    $formdata = $res->form;
                    $bktype   = $res->booking_type;

                    // 1.Get dates of specific booking
                    $sql = "SELECT *
                            FROM  ".$wpdb->prefix ."bookingdates as dt
                            WHERE booking_id = $booking_id
                            ORDER BY booking_date ASC ";
                    $result_dates = $wpdb->get_results($wpdb->prepare( $sql ));

                    // Get dates in good format for SQL checking
                    $dates_string = '';
                    foreach ($result_dates as $k=>$v) {
                        $dates_string .= " DATE('" . $v->booking_date . "'), ";
                    }
                    $dates_string = substr($dates_string,0,-2);


                    //2. Get bookings of selected booking resource - checking if some dates there is booked or not
                    $sql = "SELECT *
                                FROM ".$wpdb->prefix ."booking as bk
                                INNER JOIN ".$wpdb->prefix ."bookingdates as dt
                                ON    bk.booking_id = dt.booking_id
                                WHERE     bk.booking_type = ". $resource_id ;
                    $sql .=       " AND DATE(dt.booking_date) IN ( $dates_string )";
                    $sql .= apply_bk_filter('get_sql_4_dates_from_other_types', ''  , $resource_id, '0,1' ); // Select bk ID from other TYPES, if they partly exist inside of DATES
                    $sql .= "   ORDER BY bk.booking_id DESC, dt.booking_date ASC ";

                    $result = $wpdb->get_results($wpdb->prepare( $sql ));

                    if (count($result) == 0 ) { // Possible to change

                        // Chnage the booking form:

                        // Fix the booking form ID of elements /////////////////////////////////////////////////////////////////
                        $updated_type_id = $resource_id;
                        $formdata_new = '';
                        $formdata_array = explode('~',$formdata);
                        $formdata_array_count = count($formdata_array);
                        for ( $i=0 ; $i < $formdata_array_count ; $i++) {
                            $elemnts = explode('^',$formdata_array[$i]);

                            $type = $elemnts[0];
                            $element_name = $elemnts[1];
                            $value = $elemnts[2];

                            $element_sufix = '';
                            if (substr($element_name, -2  )=='[]') {
                                //$element_sufix = '[]';
                                //$element_name = substr($element_name, 0,  (strlen($element_name) - 1) ) ;
                                $element_name = str_replace('[]', '', $element_name);
                            }

                            $element_name = substr($element_name, 0, -1 * strlen($bktype) ) . $updated_type_id    ;  // Change bk RES. ID in elemnts of FORM

                            if ($formdata_new!='') $formdata_new.= '~';
                            $formdata_new .= $type . '^' . $element_name . '^' . $value;
                        } ////////////////////////////////////////////////////////////////////////////////////////////////

                        // Update
                        $update_sql = "UPDATE ".$wpdb->prefix ."booking AS bk SET bk.form='$formdata_new', bk.booking_type=$updated_type_id WHERE bk.booking_id=$booking_id;";
                        if ( false === $wpdb->query($wpdb->prepare( $update_sql ) ) ){
                             ?> <script type="text/javascript">
                                jQuery('#ajax_message').removeClass('info_message');
                                jQuery('#ajax_message').addClass('error_message');
                                document.getElementById('ajax_message').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php  bk_error('Error during updating booking reource type in BD',__FILE__,__LINE__ ); ?></div>';
                                jQuery('#ajax_message').fadeOut(10000);
                            </script> <?php
                            die();
                       }


                        if ( class_exists('wpdev_bk_biz_l')) {
                            $update_sql = "UPDATE ".$wpdb->prefix ."bookingdates SET type_id=NULL WHERE booking_id=$booking_id;";
                            if ( false === $wpdb->query( $wpdb->prepare($update_sql ) ) ) {
                                ?> <script type="text/javascript">
                                    jQuery('#ajax_message').removeClass('info_message');
                                    jQuery('#ajax_message').addClass('error_message');
                                    document.getElementById('ajax_message').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php  bk_error('Error during updating dates type in BD',__FILE__,__LINE__ ); ?></div>';
                                    jQuery('#ajax_message').fadeOut(10000);
                                </script> <?php
                                die();
                            }
                        }
                        // Send modification email about this
                            $bktype = $resource_id;
                            $mail_sender    =  htmlspecialchars_decode( get_bk_option( 'booking_email_modification_adress') ) ;
                            $mail_subject   =  htmlspecialchars_decode( get_bk_option( 'booking_email_modification_subject') );
                            $mail_body      =  htmlspecialchars_decode( get_bk_option( 'booking_email_modification_content') );
                            $mail_subject =  apply_bk_filter('wpdev_check_for_active_language', $mail_subject );
                            $mail_body    =  apply_bk_filter('wpdev_check_for_active_language', $mail_body );

                            if (function_exists ('get_booking_title')) $bk_title = get_booking_title( $bktype );
                            else $bk_title = '';

                            $booking_form_show = get_form_content ($formdata_new, $bktype);

                            $mail_body_to_send = str_replace('[bookingtype]', $bk_title, $mail_body);
                            if (get_bk_option( 'booking_date_view_type') == 'short') $my_dates_4_send = get_dates_short_format( get_dates_str($booking_id) );
                            else                                                  $my_dates_4_send = change_date_format(get_dates_str($booking_id));
                            $mail_body_to_send = str_replace('[dates]',$my_dates_4_send , $mail_body_to_send);
                            //$mail_body_to_send = str_replace('[check_in_date]',$my_check_in_date , $mail_body_to_send);
                            //$mail_body_to_send = str_replace('[check_out_date]',$my_check_out_date , $mail_body_to_send);
                            $mail_body_to_send = str_replace('[id]', $booking_id , $mail_body_to_send);


                            $mail_body_to_send = str_replace('[content]', $booking_form_show['content'], $mail_body_to_send);
                            $mail_body_to_send = str_replace('[name]', $booking_form_show['name'], $mail_body_to_send);
                            $mail_body_to_send = str_replace('[cost]', '', $mail_body_to_send);
                            if ( isset($booking_form_show['secondname']) ) $mail_body_to_send = str_replace('[secondname]', $booking_form_show['secondname'], $mail_body_to_send);
                            $mail_body_to_send = str_replace('[siteurl]', htmlspecialchars_decode( '<a href="'.site_url().'">' . site_url() . '</a>'), $mail_body_to_send);
                            $mail_body_to_send = apply_bk_filter('wpdev_booking_set_booking_edit_link_at_email', $mail_body_to_send, $booking_id );

                            $mail_subject = str_replace('[name]', $booking_form_show['name'], $mail_subject);
                            if ( isset($booking_form_show['secondname']) ) $mail_subject = str_replace('[secondname]', $booking_form_show['secondname'], $mail_subject);

                            $mail_recipient =  $booking_form_show['email'];

                            $mail_headers = "From: $mail_sender\n";
                            $mail_headers .= "Content-Type: text/html\n";
                            $is_send_emeils = 1;
                            if (get_bk_option( 'booking_is_email_modification_adress'  ) != 'Off') {
                                // Send to the Visitor
                                if ( ( strpos($mail_recipient,'@blank.com') === false ) && ( strpos($mail_body_to_send,'admin@blank.com') === false ) )
                                    if ($is_send_emeils != 0 )
                                        @wp_mail($mail_recipient, $mail_subject, $mail_body_to_send, $mail_headers);

                                // Send to the Admin also
                                $mail_recipient =  htmlspecialchars_decode( get_bk_option( 'booking_email_reservation_adress') );
                                $is_email_modification_send_copy_to_admin = get_bk_option( 'booking_is_email_modification_send_copy_to_admin' );
                                if ( $is_email_modification_send_copy_to_admin == 'On')
                                    if ( ( strpos($mail_recipient,'@blank.com') === false ) && ( strpos($mail_body_to_send,'admin@blank.com') === false ) )
                                        if ($is_send_emeils != 0 )
                                            @wp_mail($mail_recipient, $mail_subject, $mail_body_to_send, $mail_headers);
                            }



                        ?> <script type="text/javascript">
                            document.getElementById('ajax_message').innerHTML = '<?php echo __('Updated successfully', 'wpdev-booking'); ?>';
                            jQuery('#ajax_message').fadeOut(5000);
                            set_booking_row_resource_name('<?php echo $booking_id; ?>', '<?php
                                                            $bk_booking_type_name = get_booking_title($resource_id);
                                                            if (strlen($bk_booking_type_name)>19) $bk_booking_type_name = substr($bk_booking_type_name, 0, 16) . '...';
                                                            echo $bk_booking_type_name;
                                                          ?>');
                        </script> <?php




                    } else {            // Already busy there, need to chnage to other resource

                        ?> <script type="text/javascript">
                            jQuery('#ajax_message').removeClass('info_message');
                            jQuery('#ajax_message').addClass('error_message');
                            document.getElementById('ajax_message').innerHTML = '<?php echo __('Warning! The resource was not chnaged. Actual dates are already booked there.', 'wpdev-booking'); ?>';
                            jQuery('#ajax_message').fadeOut(10000);
                        </script> <?php

                    }
                    die;

                    

                }


        //     R  E   M   A   R   K   S      /////////////////////////////////////////////////////////////////////////////////////////

            function wpdev_updating_remark(){
                $remark_id   = $_POST["remark_id"];
                $remark_text = $_POST["remark_text"];
                $remark_text = str_replace('%','&#37;',$remark_text);

                 $my_remark = str_replace('"','',$remark_text);
                 $my_remark = str_replace("'",'',$my_remark);
                 $my_remark =trim($my_remark);
                 



                global $wpdb;
                    $update_sql = "UPDATE ".$wpdb->prefix ."booking AS bk SET bk.remark='$remark_text' WHERE bk.booking_id=$remark_id;";
                    if ( false === $wpdb->query( $wpdb->prepare($update_sql ) ) ) {
                        ?> <script type="text/javascript">
                            jQuery('#ajax_message').removeClass('info_message');
                            jQuery('#ajax_message').addClass('error_message');
                            document.getElementById('ajax_message').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php  bk_error('Error during updating remarks in BD',__FILE__,__LINE__ ); ?></div>';
                            jQuery('#ajax_message').fadeOut(10000);
                        </script> <?php
                        die();
                    }

                    ?> <script type="text/javascript">
                        document.getElementById('ajax_message').innerHTML = '<?php echo __('Updated successfully', 'wpdev-booking'); ?>';
                        jQuery('#ajax_message').fadeOut(5000);
                        <?php  if (strlen($my_remark)>100) {$my_remark = esc_js(substr($my_remark,0,100)) . '...';}   ?>
                        set_booking_row_remark_in_hint(<?php echo $remark_id; ?>, '<?php echo $my_remark; ?>') ; 
                    </script> <?php
                    die();
            }

            function wpdev_make_update_of_remark($remark_id, $remark_text, $is_append = false ){

                 $my_remark = str_replace('"','',$remark_text);
                 $my_remark = str_replace("'",'',$my_remark);
                 $my_remark =trim(strip_tags($my_remark));
                 //$my_remark = substr($my_remark,0,75) . '...';

                global $wpdb;

                if ( $is_append ) {
                    $my_remark .= ' ' . $wpdb->get_var($wpdb->prepare( "SELECT remark FROM ".$wpdb->prefix ."booking  WHERE booking_id = " . $remark_id ));
                }

                $update_sql = "UPDATE ".$wpdb->prefix ."booking AS bk SET bk.remark='$my_remark' WHERE bk.booking_id=$remark_id;";
                if ( false === $wpdb->query( $wpdb->prepare($update_sql ) ) ) {
                       echo '<div class="error_message ajax_message textleft" style="font-size:12px;font-weight:bold;">';
                       bk_error('Error during updating remark of booking' ,__FILE__,__LINE__);
                       echo   '</div>';

                }

            }

        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


 //  S e t t i n g s     p a g e s           //////////////////////////////////////////////////////////////////////////////////////////////////

        function settings_menu_content(){
$is_can = apply_bk_filter('multiuser_is_user_can_be_here', true, 'not_low_level_user'); //Anxo customizarion
if (! $is_can) return; //Anxo customizarion
                switch ($_GET['tab']) {

                   case 'form':
                    $this->compouse_form();
                    return false;
                    break;

                   case 'email':
                    $this->compouse_email();
                    return false;
                    break;

                 default:
                    return true;
                    break;
                }

        }


        function compouse_email(){

             if ( isset( $_POST['email_reservation_adress'] ) ) {

                 $email_reservation_adress      = htmlspecialchars( str_replace('\"','"',$_POST['email_reservation_adress']));
                 $email_reservation_from_adress = htmlspecialchars( str_replace('\"','"',$_POST['email_reservation_from_adress']));
                 $email_reservation_subject     = htmlspecialchars( str_replace('\"','"',$_POST['email_reservation_subject']));
                 $email_reservation_content     = htmlspecialchars( str_replace('\"','"',$_POST['email_reservation_content']));

                 $email_reservation_adress      =  str_replace("\'","'",$email_reservation_adress);
                 $email_reservation_from_adress =  str_replace("\'","'",$email_reservation_from_adress);
                 $email_reservation_subject     =  str_replace("\'","'",$email_reservation_subject);
                 $email_reservation_content     =  str_replace("\'","'",$email_reservation_content);

                 
                 if (isset( $_POST['is_email_reservation_adress'] ))         $is_email_reservation_adress = 'On';
                 else                                                        $is_email_reservation_adress = 'Off';
                 update_bk_option( 'booking_is_email_reservation_adress' , $is_email_reservation_adress );

                 if ( get_bk_option( 'booking_email_reservation_adress' ) !== false  )      update_bk_option( 'booking_email_reservation_adress' , $email_reservation_adress );
                 else                                                                    add_bk_option( 'booking_email_reservation_adress' , $email_reservation_adress );
                 if ( get_bk_option( 'booking_email_reservation_from_adress' ) !== false  ) update_bk_option( 'booking_email_reservation_from_adress' , $email_reservation_from_adress );
                 else                                                                    add_bk_option( 'booking_email_reservation_from_adress' , $email_reservation_from_adress );
                 if ( get_bk_option( 'booking_email_reservation_subject' ) !== false  )     update_bk_option( 'booking_email_reservation_subject' , $email_reservation_subject );
                 else                                                                    add_bk_option( 'booking_email_reservation_subject' , $email_reservation_subject );
                 if ( get_bk_option( 'booking_email_reservation_content' ) !== false  )     update_bk_option( 'booking_email_reservation_content' , $email_reservation_content );
                 else                                                                    add_bk_option( 'booking_email_reservation_content' , $email_reservation_content );
                 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                 $email_approval_adress  = htmlspecialchars( str_replace('\"','"',$_POST['email_approval_adress']));
                 $email_approval_subject = htmlspecialchars( str_replace('\"','"',$_POST['email_approval_subject']));
                 $email_approval_content = htmlspecialchars( str_replace('\"','"',$_POST['email_approval_content']));

                 $email_approval_adress      =  str_replace("\'","'",$email_approval_adress);
                 $email_approval_subject     =  str_replace("\'","'",$email_approval_subject);
                 $email_approval_content     =  str_replace("\'","'",$email_approval_content);



                 if (isset( $_POST['is_email_approval_adress'] ))            $is_email_approval_adress = 'On';
                 else                                               $is_email_approval_adress = 'Off';
                 update_bk_option( 'booking_is_email_approval_adress' , $is_email_approval_adress );

                 if (isset( $_POST['is_email_approval_send_copy_to_admin'] ))            $is_email_approval_send_copy_to_admin = 'On';
                 else                                               $is_email_approval_send_copy_to_admin = 'Off';
                 update_bk_option( 'booking_is_email_approval_send_copy_to_admin' , $is_email_approval_send_copy_to_admin );



                 if ( get_bk_option( 'booking_email_approval_adress' ) !== false  )         update_bk_option( 'booking_email_approval_adress' , $email_approval_adress );
                 else                                                                    add_bk_option( 'booking_email_approval_adress' , $email_approval_adress );
                 if ( get_bk_option( 'booking_email_approval_subject' ) !== false  )        update_bk_option( 'booking_email_approval_subject' , $email_approval_subject );
                 else                                                                    add_bk_option( 'booking_email_approval_subject' , $email_approval_subject );
                 if ( get_bk_option( 'booking_email_approval_content' ) !== false  )        update_bk_option( 'booking_email_approval_content' , $email_approval_content );
                 else                                                                    add_bk_option( 'booking_email_approval_content' , $email_approval_content );
                 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                 $email_newbookingbyperson_adress  = htmlspecialchars( str_replace('\"','"',$_POST['email_newbookingbyperson_adress']));
                 $email_newbookingbyperson_subject = htmlspecialchars( str_replace('\"','"',$_POST['email_newbookingbyperson_subject']));
                 $email_newbookingbyperson_content = htmlspecialchars( str_replace('\"','"',$_POST['email_newbookingbyperson_content']));

                 $email_newbookingbyperson_adress      =  str_replace("\'","'",$email_newbookingbyperson_adress);
                 $email_newbookingbyperson_subject     =  str_replace("\'","'",$email_newbookingbyperson_subject);
                 $email_newbookingbyperson_content     =  str_replace("\'","'",$email_newbookingbyperson_content);



                 if (isset( $_POST['is_email_newbookingbyperson_adress'] ))            $is_email_newbookingbyperson_adress = 'On';
                 else                                               $is_email_newbookingbyperson_adress = 'Off';
                 update_bk_option( 'booking_is_email_newbookingbyperson_adress' , $is_email_newbookingbyperson_adress );

                 if ( get_bk_option( 'booking_email_newbookingbyperson_adress' ) !== false  )         update_bk_option( 'booking_email_newbookingbyperson_adress' , $email_newbookingbyperson_adress );
                 else                                                                    add_bk_option( 'booking_email_newbookingbyperson_adress' , $email_newbookingbyperson_adress );
                 if ( get_bk_option( 'booking_email_newbookingbyperson_subject' ) !== false  )        update_bk_option( 'booking_email_newbookingbyperson_subject' , $email_newbookingbyperson_subject );
                 else                                                                    add_bk_option( 'booking_email_newbookingbyperson_subject' , $email_newbookingbyperson_subject );
                 if ( get_bk_option( 'booking_email_newbookingbyperson_content' ) !== false  )        update_bk_option( 'booking_email_newbookingbyperson_content' , $email_newbookingbyperson_content );
                 else                                                                    add_bk_option( 'booking_email_newbookingbyperson_content' , $email_newbookingbyperson_content );
                 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                 $email_deny_adress  = htmlspecialchars( str_replace('\"','"',$_POST['email_deny_adress']));
                 $email_deny_subject = htmlspecialchars( str_replace('\"','"',$_POST['email_deny_subject']));
                 $email_deny_content = htmlspecialchars( str_replace('\"','"',$_POST['email_deny_content']));

                 $email_deny_adress      =  str_replace("\'","'",$email_deny_adress);
                 $email_deny_subject     =  str_replace("\'","'",$email_deny_subject);
                 $email_deny_content     =  str_replace("\'","'",$email_deny_content);



                 if (isset( $_POST['is_email_deny_adress'] ))         $is_email_deny_adress = 'On';
                 else                                        $is_email_deny_adress = 'Off';
                 update_bk_option( 'booking_is_email_deny_adress' , $is_email_deny_adress );


                 if (isset( $_POST['is_email_deny_send_copy_to_admin'] ))            $is_email_deny_send_copy_to_admin = 'On';
                 else                                               $is_email_deny_send_copy_to_admin = 'Off';
                 update_bk_option( 'booking_is_email_deny_send_copy_to_admin' , $is_email_deny_send_copy_to_admin );



                 if ( get_bk_option( 'booking_email_deny_adress' ) !== false  )             update_bk_option( 'booking_email_deny_adress' , $email_deny_adress );
                 else                                                                    add_bk_option( 'booking_email_deny_adress' , $email_deny_adress );
                 if ( get_bk_option( 'booking_email_deny_subject' ) !== false  )            update_bk_option( 'booking_email_deny_subject' , $email_deny_subject );
                 else                                                                    add_bk_option( 'booking_email_deny_subject' , $email_deny_subject );
                 if ( get_bk_option( 'booking_email_deny_content' ) !== false  )            update_bk_option( 'booking_email_deny_content' , $email_deny_content );
                 else                                                                    add_bk_option( 'booking_email_deny_content' , $email_deny_content );

                 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                 $email_modification_adress  = htmlspecialchars( str_replace('\"','"',$_POST['email_modification_adress']));
                 $email_modification_subject = htmlspecialchars( str_replace('\"','"',$_POST['email_modification_subject']));
                 $email_modification_content = htmlspecialchars( str_replace('\"','"',$_POST['email_modification_content']));

                 $email_modification_adress      =  str_replace("\'","'",$email_modification_adress);
                 $email_modification_subject     =  str_replace("\'","'",$email_modification_subject);
                 $email_modification_content     =  str_replace("\'","'",$email_modification_content);


                 if (isset( $_POST['is_email_modification_adress'] ))         $is_email_modification_adress = 'On';
                 else                                        $is_email_modification_adress = 'Off';
                 update_bk_option( 'booking_is_email_modification_adress' , $is_email_modification_adress );

                 if (isset( $_POST['is_email_modification_send_copy_to_admin'] ))            $is_email_modification_send_copy_to_admin = 'On';
                 else                                               $is_email_modification_send_copy_to_admin = 'Off';
                 update_bk_option( 'booking_is_email_modification_send_copy_to_admin' , $is_email_modification_send_copy_to_admin );


                 if ( get_bk_option( 'booking_email_modification_adress' ) !== false  )     update_bk_option( 'booking_email_modification_adress' , $email_modification_adress );
                 else                                                                    add_bk_option( 'booking_email_modification_adress' , $email_modification_adress );
                 if ( get_bk_option( 'booking_email_modification_subject' ) !== false  )    update_bk_option( 'booking_email_modification_subject' , $email_modification_subject );
                 else                                                                    add_bk_option( 'booking_email_modification_subject' , $email_modification_subject );
                 if ( get_bk_option( 'booking_email_modification_content' ) !== false  )    update_bk_option( 'booking_email_modification_content' , $email_modification_content );
                 else                                                                    add_bk_option( 'booking_email_modification_content' , $email_modification_content );

             } 

                 $email_reservation_adress      = get_bk_option( 'booking_email_reservation_adress') ;
                 $email_reservation_from_adress = get_bk_option( 'booking_email_reservation_from_adress');
                 $email_reservation_subject     = get_bk_option( 'booking_email_reservation_subject');
                 $email_reservation_content     = get_bk_option( 'booking_email_reservation_content');
                 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                 $email_newbookingbyperson_adress      = get_bk_option( 'booking_email_newbookingbyperson_adress');
                 $email_newbookingbyperson_subject     = get_bk_option( 'booking_email_newbookingbyperson_subject');
                 $email_newbookingbyperson_content     = get_bk_option( 'booking_email_newbookingbyperson_content');
                 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                 $email_approval_adress      = get_bk_option( 'booking_email_approval_adress');
                 $email_approval_subject     = get_bk_option( 'booking_email_approval_subject');
                 $email_approval_content     = get_bk_option( 'booking_email_approval_content');
                 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                 $email_deny_adress      = get_bk_option( 'booking_email_deny_adress');
                 $email_deny_subject     = get_bk_option( 'booking_email_deny_subject');
                 $email_deny_content     = get_bk_option( 'booking_email_deny_content');
                 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                 $email_modification_adress      = get_bk_option( 'booking_email_modification_adress');
                 $email_modification_subject     = get_bk_option( 'booking_email_modification_subject');
                 $email_modification_content     = get_bk_option( 'booking_email_modification_content');

                 $is_email_reservation_adress   = get_bk_option( 'booking_is_email_reservation_adress' );
                 $is_email_newbookingbyperson_adress      = get_bk_option( 'booking_is_email_newbookingbyperson_adress' );
                 $is_email_approval_adress      = get_bk_option( 'booking_is_email_approval_adress' );
                 $is_email_approval_send_copy_to_admin = get_bk_option( 'booking_is_email_approval_send_copy_to_admin'  );
                 $is_email_deny_adress          = get_bk_option( 'booking_is_email_deny_adress' );
                 $is_email_deny_send_copy_to_admin = get_bk_option( 'booking_is_email_deny_send_copy_to_admin'  );
                 $is_email_modification_adress          = get_bk_option( 'booking_is_email_modification_adress' );
                 $is_email_modification_send_copy_to_admin = get_bk_option( 'booking_is_email_modification_send_copy_to_admin'  );

            ?>
                    <div class="clear" style="height:0px;"></div>
                    <div id="ajax_working"></div>
                    <div id="poststuff" class="metabox-holder">

                        <form  name="post_settings_email_templates" action="" method="post" id="post_settings_email_templates" >

                            <div id="visibility_container_email_new_to_admin" class="visibility_container" style="display:block;">

                                <div class='meta-box'> <div <?php $my_close_open_win_id = 'bk_settings_emails_to_admin'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');" ><br></div>
                                      <h3 class='hndle'><span><?php _e('Email to "Admin" after booking at site', 'wpdev-booking'); ?></span></h3> <div class="inside">

                                    <table class="form-table email-table" >
                                        <tbody>
                                            <tr><td colspan="2" class="th-title">
                                                    <div style="float:left;"><h2><?php _e('Email to "Admin" after booking at site', 'wpdev-booking'); ?></h2></div>
                                                    <div style="float:right;font-weight: bold;"><label for="is_email_reservation_adress" ><?php _e('Active', 'wpdev-booking'); ?>: </label><input id="is_email_reservation_adress" type="checkbox" <?php if ($is_email_reservation_adress == 'On') echo "checked"; ?>  value="<?php echo $is_email_reservation_adress; ?>" name="is_email_reservation_adress"   onchange="document.getElementById('booking_is_email_reservation_adress_dublicated').checked=this.checked;"  /></div>
                                                </td></tr>

                                            <tr valign="top">
                                                <th scope="row"><label for="admin_cal_count" ><?php _e('To', 'wpdev-booking'); ?>:</label></th>
                                                <td><input id="email_reservation_adress"  name="email_reservation_adress" class="regular-text code" type="text" size="45" value="<?php echo $email_reservation_adress; ?>" />
                                                    <span class="description"><?php printf(__('Type default %sadmin email%s for checking bookings', 'wpdev-booking'),'<b>','</b>');?></span>
                                                </td>
                                            </tr>

                                            <tr valign="top">
                                                <th scope="row"><label for="admin_cal_count" ><?php _e('From', 'wpdev-booking'); ?>:</label></th>
                                                <td><input id="email_reservation_from_adress" name="email_reservation_from_adress" class="regular-text code" type="text" size="45" value="<?php echo $email_reservation_from_adress; ?>" />
                                                    <span class="description"><?php printf(__('Type default %sadmin email%s from where this email is sending', 'wpdev-booking'),'<b>','</b>');?></span>
                                                </td>
                                            </tr>

                                            <tr valign="top">
                                                    <th scope="row"><label for="admin_cal_count" ><?php _e('Subject', 'wpdev-booking'); ?>:</label></th>
                                                    <td><input id="email_reservation_subject" name="email_reservation_subject"  class="regular-text code" type="text" size="45" value="<?php echo $email_reservation_subject; ?>" />
                                                        <span class="description"><?php printf(__('Type your email subject for %schecking booking%s. You can use these %s shortcodes.', 'wpdev-booking'),'<b>','</b>', '<code>[name]</code>, <code>[secondname]</code>');?></span>
                                                    </td>
                                            </tr>

                                            <tr valign="top">
                                                <td colspan="2">
                                                    <span class="description"><?php printf(__('Type your %semail message for checking booking%s in. ', 'wpdev-booking'),'<b>','</b>');  ?></span>
                                                      <textarea id="email_reservation_content" name="email_reservation_content" style="width:100%;" rows="2"><?php echo ($email_reservation_content); ?></textarea>
                                                      <div class="shortcode_help_section" style="margin-top:10px;">
                                                      <span class="description"><?php printf(__('Use this shortcodes: ', 'wpdev-booking'));?></span>
                                                      <span class="description"><?php printf(__('%s - inserting ID of booking ', 'wpdev-booking'),'<code>[id]</code>');?>, </span>
                                                      <span class="description"><?php printf(__('%s - inserting name of person, who made booking (field %s requred at form for this bookmark), ', 'wpdev-booking'),'<code>[name]</code>','[text name]');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting dates of booking, ', 'wpdev-booking'),'<code>[dates]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting check in date (first day of booking), ', 'wpdev-booking'),'<code>[check_in_date]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting check out date (last day of booking), ', 'wpdev-booking'),'<code>[check_out_date]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting type of booking resource, ', 'wpdev-booking'),'<code>[bookingtype]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting detail person info, ', 'wpdev-booking'),'<code>[content]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting moderate link of new booking, ', 'wpdev-booking'),'<code>[moderatelink]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting link to booking editable by vistor from his account, ', 'wpdev-booking'),'<code>[visitorbookingediturl]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting link for booking cancellation by visitor at client side of site, ', 'wpdev-booking'),'<code>[visitorbookingcancelurl]</code>');?></span>
                                                      <?php if ($this->wpdev_bk_biz_s != false) { ?><span class="description"><?php printf(__('%s - inserting cost of this booking, ', 'wpdev-booking'),'<code>[cost]</code>');?></span><?php } ?>
                                                      <span class="description"><?php printf(__('%s - inserting new line', 'wpdev-booking'),'<code>&lt;br/&gt;</code>');?></span>
                                                      <br/><?php echo (sprintf(__('For example: "You need to approve a new booking %s on the following dates: %s Person detailed information:%s You can edit this booking at: %s Thank you, booking service."', 'wpdev-booking'),'[bookingtype]','[dates]&lt;br/&gt;&lt;br/&gt;','&lt;br/&gt; [content]&lt;br/&gt;&lt;br/&gt;', '[visitorbookingediturl] &lt;br/&gt;&lt;br/&gt; ')); ?>
                                                      <?php make_bk_action('show_additional_translation_shortcode_help'); ?>
                                                      </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div> </div> </div>

                            </div>


                            <div id="visibility_container_email_new_to_visitor" class="visibility_container" style="display:none;">

                                <div class='meta-box'> <div <?php $my_close_open_win_id = 'bk_settings_emails_to_person_after_new'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');" ><br></div>
                                      <h3 class='hndle'><span><?php _e('Email to "Person" after new booking is done by this person', 'wpdev-booking'); ?></span></h3> <div class="inside">


                                    <table class="form-table email-table" >
                                        <tbody>
                                            <tr><td colspan="2"  class="th-title">
                                                    <div style="float:left;"><h2><?php _e('Email to "Person" after new booking is done by this person', 'wpdev-booking'); ?></h2></div>
                                                    <div style="float:right;font-weight: bold;"><label for="is_email_newbookingbyperson_adress" ><?php _e('Active', 'wpdev-booking'); ?>: </label><input id="is_email_newbookingbyperson_adress" type="checkbox" <?php if ($is_email_newbookingbyperson_adress == 'On') echo "checked"; ?>  value="<?php echo $is_email_newbookingbyperson_adress; ?>" name="is_email_newbookingbyperson_adress"   onchange="document.getElementById('booking_is_email_newbookingbyperson_adress_dublicated').checked=this.checked;"  /></div>
                                                </td></tr>

                                            <tr valign="top">
                                                <th scope="row"><label for="admin_cal_count" ><?php _e('From', 'wpdev-booking'); ?>:</label></th>
                                                <td><input id="email_newbookingbyperson_adress"  name="email_newbookingbyperson_adress" class="regular-text code" type="text" size="45" value="<?php echo $email_newbookingbyperson_adress; ?>" />
                                                    <span class="description"><?php printf(__('Type default %sadmin email%s from where this email is sending', 'wpdev-booking'),'<b>','</b>');?></span>
                                                </td>
                                            </tr>

                                            <tr valign="top">
                                                    <th scope="row"><label for="admin_cal_count" ><?php _e('Subject', 'wpdev-booking'); ?>:</label></th>
                                                    <td><input id="email_newbookingbyperson_subject"  name="email_newbookingbyperson_subject" class="regular-text code" type="text" size="45" value="<?php echo $email_newbookingbyperson_subject; ?>" />
                                                        <span class="description"><?php printf(__('Type email subject for %svisitor after creation of a new booking%s. Use these %s shortcodes.', 'wpdev-booking'),'<b>','</b>', '<code>[name]</code>, <code>[secondname]</code>');?></span>
                                                    </td>
                                            </tr>

                                            <tr valign="top">
                                                <td colspan="2">
                                                      <span class="description"><?php printf(__('Type your %semail message for visitor after creation new booking%s at site', 'wpdev-booking'),'<b>','</b>');?></span>
                                                      <textarea id="email_newbookingbyperson_content" name="email_newbookingbyperson_content" style="width:100%;" rows="2"><?php echo ($email_newbookingbyperson_content); ?></textarea>
                                                      <div class="shortcode_help_section" style="margin-top:10px;">
                                                      <span class="description"><?php printf(__('Use this shortcodes: ', 'wpdev-booking'));?></span>
                                                      <span class="description"><?php printf(__('%s - inserting ID of booking ', 'wpdev-booking'),'<code>[id]</code>');?>, </span>
                                                      <span class="description"><?php printf(__('%s - inserting name of person, who made booking (field %s requred at form for this bookmark), ', 'wpdev-booking'),'<code>[name]</code>','[text name]');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting dates of booking, ', 'wpdev-booking'),'<code>[dates]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting check in date (first day of booking), ', 'wpdev-booking'),'<code>[check_in_date]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting check out date (last day of booking), ', 'wpdev-booking'),'<code>[check_out_date]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting type of booking resource, ', 'wpdev-booking'),'<code>[bookingtype]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting detail person info, ', 'wpdev-booking'),'<code>[content]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting link to booking editable by vistor from his account, ', 'wpdev-booking'),'<code>[visitorbookingediturl]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting link for booking cancellation by visitor at client side of site, ', 'wpdev-booking'),'<code>[visitorbookingcancelurl]</code>');?></span>
                                                      <?php if ($this->wpdev_bk_biz_s != false) { ?><span class="description"><?php printf(__('%s - inserting cost of this booking, ', 'wpdev-booking'),'<code>[cost]</code>');?></span><?php } ?>
                                                      <span class="description"><?php printf(__('%s - inserting new line', 'wpdev-booking'),'<code>&lt;br/&gt;</code>');?></span>
                                                      <br/><?php echo (sprintf(__('For example: "Your booking %s at dates: %s is processing now! Please, wait for the confirmation email. %s  You can edit this booking at this page: %s Thank you, booking service."', 'wpdev-booking'),'[bookingtype]', '[dates]','&lt;br/&gt;&lt;br/&gt;[content]&lt;br/&gt;&lt;br/&gt;', '[visitorbookingediturl] &lt;br/&gt;&lt;br/&gt; ')); ?>
                                                      <?php make_bk_action('show_additional_translation_shortcode_help'); ?>
                                                      </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div> </div> </div>

                            </div>


                            <div id="visibility_container_email_approved" class="visibility_container" style="display:none;">

                                <div class='meta-box'> <div <?php $my_close_open_win_id = 'bk_settings_emails_to_person_after_approval'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');" ><br></div>
                                      <h3 class='hndle'><span><?php _e('Email to "Person" after approval of booking', 'wpdev-booking'); ?></span></h3> <div class="inside">

                                    <table class="form-table email-table" >
                                        <tbody>
                                            <tr><td colspan="2"  class="th-title">
                                                    <div style="float:left;"><h2><?php _e('Email to "Person" after approval of booking', 'wpdev-booking'); ?></h2></div>
                                                    <div style="float:right;font-weight: bold;"><label for="is_email_approval_adress" ><?php _e('Active', 'wpdev-booking'); ?>: </label><input id="is_email_approval_adress" type="checkbox" <?php if ($is_email_approval_adress == 'On') echo "checked"; ?>  value="<?php echo $is_email_approval_adress; ?>" name="is_email_approval_adress"  onchange="document.getElementById('booking_is_email_approval_adress_dublicated').checked=this.checked;"  /></div>
                                                    <div style="float:right;font-weight: bold;margin:0px 50px;"><label for="is_email_approval_send_copy_to_admin" ><?php _e('Send copy of this email to Admin', 'wpdev-booking'); ?>: </label><input id="is_email_approval_send_copy_to_admin" type="checkbox" <?php if ($is_email_approval_send_copy_to_admin == 'On') echo "checked"; ?>  value="<?php echo $is_email_approval_send_copy_to_admin; ?>" name="is_email_approval_send_copy_to_admin"/></div>
                                                </td></tr>

                                            <tr valign="top">
                                                <th scope="row"><label for="admin_cal_count" ><?php _e('From', 'wpdev-booking'); ?>:</label></th>
                                                <td><input id="email_approval_adress"  name="email_approval_adress" class="regular-text code" type="text" size="45" value="<?php echo $email_approval_adress; ?>" />
                                                    <span class="description"><?php printf(__('Type default %sadmin email%s from where this email is sending', 'wpdev-booking'),'<b>','</b>');?></span>
                                                </td>
                                            </tr>

                                            <tr valign="top">
                                                    <th scope="row"><label for="admin_cal_count" ><?php _e('Subject', 'wpdev-booking'); ?>:</label></th>
                                                    <td><input id="email_approval_subject"  name="email_approval_subject" class="regular-text code" type="text" size="45" value="<?php echo $email_approval_subject; ?>" />
                                                        <span class="description"><?php printf(__('Type your email subject for %sapproval of booking%s. You can use these %s shortcodes.', 'wpdev-booking'),'<b>','</b>', '<code>[name]</code>, <code>[secondname]</code>');?></span>
                                                    </td>
                                            </tr>

                                            <tr valign="top">
                                                <td colspan="2">
                                                      <span class="description"><?php printf(__('Type your %semail message for approval booking%s at site', 'wpdev-booking'),'<b>','</b>');?></span>
                                                      <textarea id="email_approval_content" name="email_approval_content" style="width:100%;" rows="2"><?php echo ($email_approval_content); ?></textarea>
                                                      <div class="shortcode_help_section" style="margin-top:10px;">
                                                      <span class="description"><?php printf(__('Use this shortcodes: ', 'wpdev-booking'));?></span>
                                                      <span class="description"><?php printf(__('%s - inserting ID of booking ', 'wpdev-booking'),'<code>[id]</code>');?>, </span>
                                                      <span class="description"><?php printf(__('%s - inserting name of person, who made booking (field %s requred at form for this bookmark), ', 'wpdev-booking'),'<code>[name]</code>','[text name]');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting dates of booking, ', 'wpdev-booking'),'<code>[dates]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting check in date (first day of booking), ', 'wpdev-booking'),'<code>[check_in_date]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting check out date (last day of booking), ', 'wpdev-booking'),'<code>[check_out_date]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting type of booking resource, ', 'wpdev-booking'),'<code>[bookingtype]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting detail person info, ', 'wpdev-booking'),'<code>[content]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting link to booking editable by vistor from his account, ', 'wpdev-booking'),'<code>[visitorbookingediturl]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting link for booking cancellation by visitor at client side of site, ', 'wpdev-booking'),'<code>[visitorbookingcancelurl]</code>');?></span>
                                                      <?php if ($this->wpdev_bk_biz_s != false) { ?><span class="description"><?php printf(__('%s - inserting cost of this booking, ', 'wpdev-booking'),'<code>[cost]</code>');?></span><?php } ?>
                                                      <span class="description"><?php printf(__('%s - inserting new line', 'wpdev-booking'),'<code>&lt;br/&gt;</code>');?></span>
                                                      <br/><?php echo (sprintf(__('For example: "Your booking %s at dates: %s has been approved.%s  You can edit this booking at this page: %s Thank you, booking service."', 'wpdev-booking'),'[bookingtype]', '[dates]','&lt;br/&gt;&lt;br/&gt;[content]&lt;br/&gt;&lt;br/&gt;', '[visitorbookingediturl] &lt;br/&gt;&lt;br/&gt; ')); ?>
                                                      <?php make_bk_action('show_additional_translation_shortcode_help'); ?>
                                                      </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div> </div> </div>

                            </div>

                            <div id="visibility_container_email_declined" class="visibility_container" style="display:none;">

                                <div class='meta-box'> <div <?php $my_close_open_win_id = 'bk_settings_emails_to_person_after_deny'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');" ><br></div>
                                      <h3 class='hndle'><span><?php _e('Email to "Person" after deny of booking', 'wpdev-booking'); ?></span></h3> <div class="inside">


                                    <table class="form-table email-table" >
                                        <tbody>
                                            <tr><td colspan="2"  class="th-title">
                                                    <div style="float:left;"><h2><?php _e('Email to "Person" after deny of booking', 'wpdev-booking'); ?></h2></div>
                                                    <div style="float:right;font-weight: bold;"><label for="is_email_deny_adress" ><?php _e('Active', 'wpdev-booking'); ?>: </label><input id="is_email_deny_adress" type="checkbox" <?php if ($is_email_deny_adress == 'On') echo "checked"; ?>  value="<?php echo $is_email_deny_adress; ?>" name="is_email_deny_adress"  onchange="document.getElementById('booking_is_email_declined_adress_dublicated').checked=this.checked;"  /></div>
                                                    <div style="float:right;font-weight: bold;margin:0px 50px;"><label for="is_email_deny_send_copy_to_admin" ><?php _e('Send copy of this email to Admin', 'wpdev-booking'); ?>: </label><input id="is_email_deny_send_copy_to_admin" type="checkbox" <?php if ($is_email_deny_send_copy_to_admin == 'On') echo "checked"; ?>  value="<?php echo $is_email_deny_send_copy_to_admin; ?>" name="is_email_deny_send_copy_to_admin"/></div>
                                                </td></tr>

                                            <tr valign="top">
                                                <th scope="row"><label for="admin_cal_count" ><?php _e('From', 'wpdev-booking'); ?>:</label></th>
                                                <td><input id="email_deny_adress"  name="email_deny_adress" class="regular-text code" type="text" size="45" value="<?php echo $email_deny_adress; ?>" />
                                                    <span class="description"><?php printf(__('Type default %sadmin email%s from where this email is sending', 'wpdev-booking'),'<b>','</b>');?></span>
                                                </td>
                                            </tr>

                                            <tr valign="top">
                                                    <th scope="row"><label for="admin_cal_count" ><?php _e('Subject', 'wpdev-booking'); ?>:</label></th>
                                                    <td><input id="email_deny_subject"  name="email_deny_subject" class="regular-text code" type="text" size="45" value="<?php echo $email_deny_subject; ?>" />
                                                        <span class="description"><?php printf(__('Type your email subject for %sdeny of booking%s. You can use these %s shortcodes.', 'wpdev-booking'),'<b>','</b>', '<code>[name]</code>, <code>[secondname]</code>');?></span>
                                                    </td>
                                            </tr>

                                            <tr valign="top">
                                                <td colspan="2">
                                                      <span class="description"><?php printf(__('Type your %semail message for deny booking%s at site', 'wpdev-booking'),'<b>','</b>');?></span>
                                                      <textarea id="email_deny_content" name="email_deny_content" style="width:100%;" rows="2"><?php echo ($email_deny_content); ?></textarea>
                                                      <div class="shortcode_help_section" style="margin-top:10px;">
                                                      <span class="description"><?php printf(__('Use this shortcodes: ', 'wpdev-booking'));?></span>
                                                      <span class="description"><?php printf(__('%s - inserting ID of booking ', 'wpdev-booking'),'<code>[id]</code>');?>, </span>
                                                      <span class="description"><?php printf(__('%s - inserting name of person, who made booking (field %s requred at form for this bookmark), ', 'wpdev-booking'),'<code>[name]</code>','[text name]');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting dates of booking, ', 'wpdev-booking'),'<code>[dates]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting check in date (first day of booking), ', 'wpdev-booking'),'<code>[check_in_date]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting check out date (last day of booking), ', 'wpdev-booking'),'<code>[check_out_date]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting type of booking resource, ', 'wpdev-booking'),'<code>[bookingtype]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting detail person info', 'wpdev-booking'),'<code>[content]</code>');?></span>,
                                                      <span class="description"><?php printf(__('%s - inserting reason of cancel booking', 'wpdev-booking'),'<code>[denyreason]</code>');?></span>,
                                                      <?php if ($this->wpdev_bk_biz_s != false) { ?><span class="description"><?php printf(__('%s - inserting cost of this booking, ', 'wpdev-booking'),'<code>[cost]</code>');?></span><?php } ?>
                                                      <span class="description"><?php printf(__('%s - inserting new line', 'wpdev-booking'),'<code>&lt;br/&gt;</code>');?></span>
                                                      <br/><?php echo (   sprintf(__('For example: "Your booking %s at dates: %s has been  canceled. %s Thank you, booking service."', 'wpdev-booking'), '[bookingtype]' ,'[dates]' , '&lt;br/&gt;&lt;br/&gt;[denyreason]&lt;br/&gt;&lt;br/&gt;[content]&lt;br/&gt;&lt;br/&gt;')); ?>
                                                      <?php make_bk_action('show_additional_translation_shortcode_help'); ?>
                                                      </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div> </div> </div>

                            </div>


                            <div id="visibility_container_email_modification" class="visibility_container" style="display:none;">

                                <div class='meta-box'> <div <?php $my_close_open_win_id = 'bk_settings_emails_to_person_after_modification'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');" ><br></div>
                                      <h3 class='hndle'><span><?php _e('Email to "Person" after modification of booking', 'wpdev-booking'); ?></span></h3> <div class="inside">


                                    <table class="form-table email-table" >
                                        <tbody>
                                            <tr><td colspan="2"  class="th-title">
                                                    <div style="float:left;"><h2><?php _e('Email to "Person" after modification of booking', 'wpdev-booking'); ?></h2></div>
                                                    <div style="float:right;font-weight: bold;"><label for="is_email_modification_adress" ><?php _e('Active', 'wpdev-booking'); ?>: </label><input id="is_email_modification_adress" type="checkbox" <?php if ($is_email_modification_adress == 'On') echo "checked"; ?>  value="<?php echo $is_email_modification_adress; ?>" name="is_email_modification_adress" onchange="document.getElementById('booking_is_email_modification_adress_dublicated').checked=this.checked;"  /></div>
                                                    <div style="float:right;font-weight: bold;margin:0px 50px;"><label for="is_email_modification_send_copy_to_admin" ><?php _e('Send copy of this email to Admin', 'wpdev-booking'); ?>: </label><input id="is_email_modification_send_copy_to_admin" type="checkbox" <?php if ($is_email_modification_send_copy_to_admin == 'On') echo "checked"; ?>  value="<?php echo $is_email_modification_send_copy_to_admin; ?>" name="is_email_modification_send_copy_to_admin"/></div>
                                                </td></tr>

                                            <tr valign="top">
                                                <th scope="row"><label for="admin_cal_count" ><?php _e('From', 'wpdev-booking'); ?>:</label></th>
                                                <td><input id="email_modification_adress"  name="email_modification_adress" class="regular-text code" type="text" size="45" value="<?php echo $email_modification_adress; ?>" />
                                                    <span class="description"><?php printf(__('Type default %sadmin email%s from where this email is sending', 'wpdev-booking'),'<b>','</b>');?></span>
                                                </td>
                                            </tr>

                                            <tr valign="top">
                                                    <th scope="row"><label for="admin_cal_count" ><?php _e('Subject', 'wpdev-booking'); ?>:</label></th>
                                                    <td><input id="email_modification_subject"  name="email_modification_subject" class="regular-text code" type="text" size="45" value="<?php echo $email_modification_subject; ?>" />
                                                        <span class="description"><?php printf(__('Type your email subject for %smodification of booking%s. You can use these %s shortcodes.', 'wpdev-booking'),'<b>','</b>', '<code>[name]</code>, <code>[secondname]</code>');?></span>
                                                    </td>
                                            </tr>

                                            <tr valign="top">
                                                <td colspan="2">
                                                      <span class="description"><?php printf(__('Type your %semail message for modification booking%s at site', 'wpdev-booking'),'<b>','</b>');?></span>
                                                      <textarea id="email_modification_content" name="email_modification_content" style="width:100%;" rows="2"><?php echo ($email_modification_content); ?></textarea>
                                                      <div class="shortcode_help_section" style="margin-top:10px;">
                                                      <span class="description"><?php printf(__('Use this shortcodes: ', 'wpdev-booking'));?></span>
                                                      <span class="description"><?php printf(__('%s - inserting ID of booking ', 'wpdev-booking'),'<code>[id]</code>');?>, </span>
                                                      <span class="description"><?php printf(__('%s - inserting name of person, who made booking (field %s requred at form for this bookmark), ', 'wpdev-booking'),'<code>[name]</code>','[text name]');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting dates of booking, ', 'wpdev-booking'),'<code>[dates]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting check in date (first day of booking), ', 'wpdev-booking'),'<code>[check_in_date]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting check out date (last day of booking), ', 'wpdev-booking'),'<code>[check_out_date]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting type of booking resource, ', 'wpdev-booking'),'<code>[bookingtype]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting detail person info', 'wpdev-booking'),'<code>[content]</code>');?></span>,
                                                      <span class="description"><?php printf(__('%s - inserting link to booking editable by vistor from his account, ', 'wpdev-booking'),'<code>[visitorbookingediturl]</code>');?></span>
                                                      <span class="description"><?php printf(__('%s - inserting link for booking cancellation by visitor at client side of site, ', 'wpdev-booking'),'<code>[visitorbookingcancelurl]</code>');?></span>
                                                      <?php if ($this->wpdev_bk_biz_s != false) { ?><span class="description"><?php printf(__('%s - inserting cost of this booking, ', 'wpdev-booking'),'<code>[cost]</code>');?></span><?php } ?>
                                                      <span class="description"><?php printf(__('%s - inserting new line', 'wpdev-booking'),'<code>&lt;br/&gt;</code>');?></span>
                                                      <br/><?php echo (   sprintf(__('For example: "The booking %s at dates: %s has been  edited. %s  You can edit this booking at this page: %s  Thank you, booking service."', 'wpdev-booking'), '[bookingtype]' ,'[dates]' , '&lt;br/&gt;&lt;br/&gt;[content]&lt;br/&gt;&lt;br/&gt;', '[visitorbookingediturl] &lt;br/&gt;&lt;br/&gt; ')); ?>
                                                      <?php make_bk_action('show_additional_translation_shortcode_help'); ?>
                                                      </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>


                                </div> </div> </div>
                            
                            </div>

                        <?php make_bk_action('wpdev_booking_emails_settings'); ?>

                        
                        <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="Submit"/>
                        <div class="clear" style="height:10px;"></div>

                        </form>

                    </div>
            <?php
        }

        function compouse_form(){ 


             if ( isset( $_POST['booking_form'] ) ) {

                 if (( ( isset($_POST['booking_form_new_name'])  )  && (! empty($_POST['booking_form_new_name'])) || ( ( isset($_GET['booking_form'])  ) && ($_GET['booking_form'] !== 'standard')  ) )/* && ($_POST['select_booking_form'] !== 'standard') /**/  ) {
                     make_bk_action('update_booking_form_at_settings');
                 } else {
                     $booking_form =  ($_POST['booking_form']);
                     $booking_form = str_replace('\"','"',$booking_form);
                     $booking_form = str_replace("\'","'",$booking_form);
                     //$booking_form = htmlspecialchars_decode($booking_form);
                     ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                     if ( get_bk_option( 'booking_form' ) !== false  )   update_bk_option( 'booking_form' , $booking_form );
                     else                                             add_bk_option( 'booking_form' , $booking_form );
                }
             }


             if ( isset($_GET['booking_form']) ) {

             } else {
                $booking_form  = get_bk_option( 'booking_form' );
             }
             

             if ( isset( $_POST['booking_form_show'] ) ) {

                 $booking_form_show =  ($_POST['booking_form_show']);
                 $booking_form_show = str_replace('\"','"',$booking_form_show);
                 $booking_form_show = str_replace("\'","'",$booking_form_show);
                 ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                 if ( get_bk_option( 'booking_form_show' ) !== false  )   update_bk_option( 'booking_form_show' , $booking_form_show );
                 else                                                  add_bk_option( 'booking_form_show' , $booking_form_show );

             }else {

                $booking_form_show  = get_bk_option( 'booking_form_show' );

             }


            ?>
                    <div class="clear" style="height:0px;"></div>
                    <div id="ajax_working"></div>
                    <div id="poststuff" class="metabox-holder">
                    <script type="text/javascript">
                        function reset_to_def_from(type) {
                            if (type == 'payment')
                               document.getElementById('booking_form').value = '<?php echo $this->reset_to_default_form('payment'); ?>';
                            else
                               document.getElementById('booking_form').value = '<?php echo $this->get_default_form(); ?>';
                        }
                        function reset_to_def_from_show(type) {
                            if (type == 'payment')
                                document.getElementById('booking_form_show').value = '<?php echo $this->reset_to_default_form_show('payment'); ?>';
                            else
                                document.getElementById('booking_form_show').value = '<?php echo $this->get_default_form_show(); ?>';
                        }
                    </script>
                    <div class='meta-box'>
                        <div <?php $my_close_open_win_id = 'bk_settings_form_fields'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');"><br></div>
                            <h3 class='hndle'><span><?php _e('Form fields', 'wpdev-booking'); ?></span></h3><div class="inside">
                                
                                

                                <form  name="post_option" action="" method="post" id="post_option" >
                                    <?php 
                                    
                                  $is_can = true; //apply_bk_filter('multiuser_is_user_can_be_here', true, 'only_super_admin');
                                    if ($is_can) { // Extended booking forms, in MU mode reduce to superadmin only.
                                        $booking_form_content = apply_bk_filter('show_select_box_for_several_forms', '');
                                    }

                                    if (! empty($booking_form_content) )
                                        $booking_form = $booking_form_content;
                                    ?>
                                    <div style="float:left;margin:10px 0px;width:58%;">
                                        <textarea id="booking_form" name="booking_form" class="darker-border" style="width:100%;" rows="58"><?php echo htmlspecialchars($booking_form, ENT_NOQUOTES ); ?></textarea>
                                    </div>
                                    <div style="float:right;margin:10px 0px;width:40%;" class="code_description">
                                        <div  class="shortcode_help_section">
                                          <span class="description" style="padding:5px;"><?php printf(__('%sGeneral shortcode rule for fields insertion%s', 'wpdev-booking'),'<strong>','</strong>');?></span><br/><br/>
                                          <span class="description"><?php printf( '<code>[shortcode_type* field_name "value"]</code>');?></span><br/>
                                          <span class="description"><?php printf(__('Parameters: ', 'wpdev-booking'));?></span><br/>
                                          <span class="description"><?php printf(__('%s - this symbol means that this field is Required (can not be skipped)', 'wpdev-booking'),'<code>*</code>');?></span><br/>
                                          <span class="description"><?php printf(__('%s - field name, must be unique (can not be skipped)', 'wpdev-booking'),'<code>field_name</code>');?></span><br/>
                                          <span class="description"><?php printf(__('%s - default value of field (can be skipped)', 'wpdev-booking'),'<code>"value"</code>');?></span><br/><br/>
                                        </div>
                                        <div class="shortcode_help_section">
                                          <span class="description" style="padding:5px;"><?php printf(__('%sUse these shortcode types for inserting fields into form:%s', 'wpdev-booking'),'<strong>','</strong>');?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - calendar', 'wpdev-booking'),'<code>[calendar]</code>');?></span><br/>
                                          <span class="description"><?php printf(__('%s - CAPTCHA', 'wpdev-booking'),'<code>[captcha]</code>');?></span><br/>

                                          <span class="description"><?php printf(__('%s - text field. ', 'wpdev-booking'),'<code>[text]</code>');?></span>
                                          <span class="description example-code"><?php printf(__('Example: %sJohn%s', 'wpdev-booking'),'[text firt_name "', '"]');?></span><br/>
                                          <?php if ($this->wpdev_bk_biz_s !== false) { ?>
                                          <span class="description"><?php printf(__('%s - start time field. ', 'wpdev-booking'),'<code>[starttime]</code>');?></span>
                                          <span class="description example-code"><?php printf(__('Example: %s. If you have already predefined times, you can also use this shortcode: %s', 'wpdev-booking'),'[starttime]', '[select starttime "12:00" "14:00"]');?></span><br/>

                                          <span class="description"><?php printf(__('%s - end time field. ', 'wpdev-booking'),'<code>[endtime]</code>');?></span>
                                          <span class="description example-code"><?php printf(__('Example: %s. If you have already predefined times, you can also use this shortcode: %s', 'wpdev-booking'),'[endtime]', '[select endtime "16:00" "20:00"]');?></span><br/>

                                          <span class="description"><?php printf(__('%s - start and end time field in one dropdown list ', 'wpdev-booking'),'<code>[select rangetime]</code>');?></span>
                                          <span class="description example-code"><?php printf(__('If you have already predefined times (start and end time), use this code: %s ', 'wpdev-booking'), '[select rangetime "10:00 - 12:00" "12:00 - 14:00" "14:00 - 16:00" "16:00 - 18:00" "18:00 - 20:00" ]');?></span><br/>

                                          <span class="description"><?php printf(__('%s - duration time field. ', 'wpdev-booking'),'<code>[select durationtime]</code>');?></span>
                                          <span class="description example-code"><?php printf(__('If you set already start time, you can set duration of time using this shortcode: %s. You do not requre endtime.', 'wpdev-booking'), '[select durationtime "00:30" "01:00" "01:30" "02:00" "02:30" "03:00" ]');?></span><br/>

                                          <?php } ?>
                                          <span class="description"><?php printf(__('%s - additional time field (as an additional property). Do not apply to the dividing day into sections. ', 'wpdev-booking'),'<code>[time]</code>');?></span>
                                          <span class="description example-code"><?php printf(__('Example: %s ', 'wpdev-booking'),'[time my_tm]');?></span><br/>

                                          <span class="description"><?php printf(__('%s - email field, ', 'wpdev-booking'),'<code>[email]</code>');?></span>
                                          <span class="description example-code"><?php printf(__('Example: %s ', 'wpdev-booking'),'[email* my_email]');?></span><br/>

                                          <span class="description"><?php printf(__('%s - select field, ', 'wpdev-booking'),'<code>[select]</code>');?></span>
                                          <span class="description example-code"><?php printf(__('Example: %s ', 'wpdev-booking'),'[select my_slct "1" "2" "3"]');?></span><br/>

                                          <span class="description"><?php printf(__('%s - checkbox field, ', 'wpdev-booking'),'<code>[checkbox]</code>');?></span><br />
                                          <span class="description example-code"><?php printf(__('Example #1: %s ', 'wpdev-booking'),'[checkbox my_radio ""]');?></span><br />
                                          <span class="description example-code"><?php printf(__('Example #2: %s - checked by default', 'wpdev-booking'),'[checkbox my_radio default:on ""]');?></span><br />
                                          <span class="description example-code"><?php printf(__('Example #3: %s - several values', 'wpdev-booking'),'[checkbox my_radio "TV" "Player"]');?></span><br />

                                          <span class="description"><?php printf(__('%s - textarea field, ', 'wpdev-booking'),'<code>[textarea]</code>');?></span>
                                          <span class="description example-code"><?php printf(__('Example: %s ', 'wpdev-booking'),'[textarea my_details]');?></span><br/>

                                          <span class="description"><?php printf(__('%s - countries list field, ', 'wpdev-booking'),'<code>[country]</code>');?></span><br />
                                          <span class="description example-code"><?php printf(__('Example #1: %s - default usage', 'wpdev-booking'),'[country]');?></span><br/>
                                          <span class="description example-code"><?php printf(__('Example #2: %s - country selected by default', 'wpdev-booking'),'[country "US"]');?></span><br/>

                                          <span class="description"><?php printf(__('%s - submit button, ', 'wpdev-booking'),'<code>[submit]</code>');?></span>
                                          <span class="description example-code"><?php printf(__('Example: %sSend%s ', 'wpdev-booking'),'[submit "', '"]');?></span><br/>
                                          <?php make_bk_action('show_additional_shortcode_help_for_form'); ?>
                                          <span class="description"><?php printf(__('%s - inserting new line, ', 'wpdev-booking'),'<code>&lt;br/&gt;</code>');?></span><br/>
                                          <span class="description"><?php printf(__('use any other HTML tags (carefully).', 'wpdev-booking'),'<code>','</code>');?></span><br/><br/>
                                          <?php make_bk_action('show_additional_translation_shortcode_help'); ?>
                                        </div>
                                    </div>
                                    <div class="clear" style="height:1px;"></div>
                                    <input class="button-secondary" style="float:left;" type="button" value="<?php _e('Reset to default form', 'wpdev-booking'); ?>" onclick="javascript:reset_to_def_from();" name="reset_form"/>
                                    <?php  if ($this->wpdev_bk_biz_s !== false) { ?> 
                                    <input class="button-secondary" style="float:left; margin:0px 20px;" type="button" value="<?php _e('Reset to default Payment form', 'wpdev-booking'); ?>" onclick="javascript:reset_to_def_from('payment');" name="reset_form"/>
                                    <?php } ?>
                                    <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="Submit"/>
                                    <div class="clear" style="height:5px;"></div>

                                </form>
                     </div></div></div>


                    <div class='meta-box'>
                        <div <?php $my_close_open_win_id = 'bk_settings_form_fields_show'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');"><br></div>
                            <h3 class='hndle'><span><?php printf(__('Form content data showing in emails (%s-shortcode) and inside approval and booking tables in booking calendar page', 'wpdev-booking'),'[content]'); ?></span></h3><div class="inside">
                                <form  name="post_option" action="" method="post" id="post_option" >
                                    <div style="float:left;margin:10px 0px;width:58%;">
                                    <textarea id="booking_form_show" name="booking_form_show" class="darker-border" style="width:100%;" rows="12"><?php echo htmlspecialchars($booking_form_show, ENT_NOQUOTES ); ?></textarea>
                                    </div>
                                    <div style="float:right;margin:10px 0px;width:40%;" class="code_description">
                                        <div  class="shortcode_help_section">
                                          <span class="description"><?php printf(__('Use these shortcodes for customization: ', 'wpdev-booking'));?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - inserting data from fields of booking form, ', 'wpdev-booking'),'<code>[field_name]</code>');?></span><br/><br/>
                                          <span class="description"><?php printf(__('%s - inserting new line, ', 'wpdev-booking'),'<code>&lt;br/&gt;</code>');?></span><br/><br/>
                                          <span class="description"><?php printf(__('use any other HTML tags (carefully).', 'wpdev-booking'),'<code>','</code>');?></span><br/><br/>
                                          <?php make_bk_action('show_additional_translation_shortcode_help'); ?>
                                        </div>
                                    </div>
                                    <div class="clear" style="height:1px;"></div>
                                    <input class="button-secondary" style="float:left;" type="button" value="<?php _e('Reset to default form content', 'wpdev-booking'); ?>" onclick="javascript:reset_to_def_from_show();" name="reset_form"/>
                                    <?php  if ($this->wpdev_bk_biz_s !== false) { ?>
                                    <input class="button-secondary" style="float:left; margin:0px 20px;" type="button" value="<?php _e('Reset to default Payment form content', 'wpdev-booking'); ?>" onclick="javascript:reset_to_def_from_show('payment');" name="reset_form"/>
                                    <?php } ?>
                                    <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="Submit"/>
                                    <div class="clear" style="height:5px;"></div>

                                </form>
                     </div></div></div>

                    </div>
         <?php
        }

                // Get default booking form
                function get_default_form(){
                    if ($this->wpdev_bk_biz_s == false)
                       return '[calendar] \n\
        \n\
        <div style="text-align:left"> \n\
        <p>'. __('First Name (required)', 'wpdev-booking').':<br />  [text* name] </p> \n\
        \n\
        <p>'. __('Last Name (required)', 'wpdev-booking').':<br />  [text* secondname] </p> \n\
        \n\
        <p>'. __('Email (required)', 'wpdev-booking').':<br />  [email* email] </p> \n\
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

                     else
                       return  '[calendar] \n\
        \n\
        <div style="text-align:left"> \n\
        <p>'. __('Start time', 'wpdev-booking').': [starttime]  '. __('End time', 'wpdev-booking').': [endtime]</p> \n\
        \n\
        <p>'. __('First Name (required)', 'wpdev-booking').':<br />  [text* name] </p> \n\
        \n\
        <p>'. __('Last Name (required)', 'wpdev-booking').':<br />  [text* secondname] </p> \n\
        \n\
        <p>'. __('Email (required)', 'wpdev-booking').':<br />  [email* email] </p> \n\
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
        </div>';/**/
                }

                // Reset to Payment form
                function reset_to_default_form($form_type ){
                    if ($form_type == 'payment')
                       return '[calendar] \n\
        \n\
        <div style="text-align:left"> \n\
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

                // Get default content form text
                function get_default_form_show(){
                    if ($this->wpdev_bk_biz_s == false)
                       return '<div style="text-align:left"> \n\
        <strong>'. __('First Name', 'wpdev-booking').'</strong>:<span class="fieldvalue">[name]</span><br/>\n\
        <strong>'. __('Last Name', 'wpdev-booking').'</strong>:<span class="fieldvalue">[secondname]</span><br/>\n\
        <strong>'. __('Email', 'wpdev-booking').'</strong>:<span class="fieldvalue">[email]</span><br/>\n\
        <strong>'. __('Phone', 'wpdev-booking').'</strong>:<span class="fieldvalue">[phone]</span><br/>\n\
        <strong>'. __('Number of visitors', 'wpdev-booking').'</strong>:<span class="fieldvalue"> [visitors]</span><br/>\n\
        <strong>'. __('Children', 'wpdev-booking').'</strong>:<span class="fieldvalue"> [children]</span><br/>\n\
        <strong>'. __('Details', 'wpdev-booking').'</strong>:<br /><span class="fieldvalue"> [details]</span>\n\
        </div>';
                    else
                       return '<div style="text-align:left"> \n\
        <strong>'. __('Start time', 'wpdev-booking').'</strong>: <span class="fieldvalue">[starttime]</span> \n\
        <strong>'. __('End time', 'wpdev-booking').'</strong>: <span class="fieldvalue">[endtime]</span><br/>\n\
        <strong>'. __('First Name', 'wpdev-booking').'</strong>:<span class="fieldvalue">[name]</span><br/>\n\
        <strong>'. __('Last Name', 'wpdev-booking').'</strong>:<span class="fieldvalue">[secondname]</span><br/>\n\
        <strong>'. __('Email', 'wpdev-booking').'</strong>:<span class="fieldvalue">[email]</span><br/>\n\
        <strong>'. __('Phone', 'wpdev-booking').'</strong>:<span class="fieldvalue">[phone]</span><br/>\n\
        <strong>'. __('Number of visitors', 'wpdev-booking').'</strong>:<span class="fieldvalue"> [visitors]</span><br/>\n\
        <strong>'. __('Children', 'wpdev-booking').'</strong>:<span class="fieldvalue"> [children]</span><br/>\n\
        <strong>'. __('Details', 'wpdev-booking').'</strong>:<br /><span class="fieldvalue"> [details]</span>\n\
        </div>';
                }

                // Reset to default payment content show
                function reset_to_default_form_show($form_type ){
                    if ($form_type == 'payment')
                       return '<div style="text-align:left"> \n\
        <strong>'. __('Start time', 'wpdev-booking').'</strong>: <span class="fieldvalue">[starttime]</span> \n\
        <strong>'. __('End time', 'wpdev-booking').'</strong>: <span class="fieldvalue">[endtime]</span><br/>\n\
        <strong>'. __('First Name', 'wpdev-booking').'</strong>:<span class="fieldvalue">[name]</span><br/>\n\
        <strong>'. __('Last Name', 'wpdev-booking').'</strong>:<span class="fieldvalue">[secondname]</span><br/>\n\
        <strong>'. __('Email', 'wpdev-booking').'</strong>:<span class="fieldvalue">[email]</span><br/>\n\
        <strong>'. __('Address', 'wpdev-booking').'</strong>:<span class="fieldvalue">[address]</span><br/>\n\
        <strong>'. __('City', 'wpdev-booking').'</strong>:<span class="fieldvalue">[city]</span><br/>\n\
        <strong>'. __('Post code', 'wpdev-booking').'</strong>:<span class="fieldvalue">[postcode]</span><br/>\n\
        <strong>'. __('Country', 'wpdev-booking').'</strong>:<span class="fieldvalue">[country]</span><br/>\n\
        <strong>'. __('Phone', 'wpdev-booking').'</strong>:<span class="fieldvalue">[phone]</span><br/>\n\
        <strong>'. __('Number of visitors', 'wpdev-booking').'</strong>:<span class="fieldvalue"> [visitors]</span><br/>\n\
        <strong>'. __('Children', 'wpdev-booking').'</strong>:<span class="fieldvalue"> [children]</span><br/>\n\
        <strong>'. __('Details', 'wpdev-booking').'</strong>:<br /><span class="fieldvalue"> [details]</span>\n\
        </div>'; }


 //   A C T I V A T I O N   A N D   D E A C T I V A T I O N    O F   T H I S   P L U G I N  ///////////////////////////////////////////////////

        // Activate
        function pro_activate() {

               global $wpdb;

               if ($this->wpdev_bk_biz_s == false) {
                    add_bk_option( 'booking_form' , str_replace('\\n\\','',$this->get_default_form()));
                    add_bk_option( 'booking_form_show' ,str_replace('\\n\\','',$this->get_default_form_show()));
               } else {
                    add_bk_option( 'booking_form' , str_replace('\\n\\','', $this->reset_to_default_form('payment') ));
                    add_bk_option( 'booking_form_show' ,str_replace('\\n\\','',$this->reset_to_default_form_show('payment') ));
               }
               update_bk_option( 'booking_skin', WPDEV_BK_PLUGIN_URL . '/css/skins/traditional.css');
                if ( wpdev_bk_is_this_demo() ) {
                    update_bk_option( 'booking_is_use_captcha' , 'On' );
                    update_bk_option( 'booking_is_show_legend' , 'On' );
                }

                $charset_collate = '';
                $wp_queries = array();
                

                if ( ( ! $this->is_table_exists('bookingtypes')  )) { // Cehck if tables not exist yet
                        //if ( $wpdb->has_cap( 'collation' ) ) {
                            if ( ! empty($wpdb->charset) )
                                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
                            if ( ! empty($wpdb->collate) )
                                $charset_collate .= " COLLATE $wpdb->collate";
                        //}
                        /** Create WordPress database tables SQL */
                        $wp_queries[] = "CREATE TABLE ".$wpdb->prefix ."bookingtypes (
                             booking_type_id bigint(20) unsigned NOT NULL auto_increment,
                             title varchar(200) NOT NULL default '',
                             PRIMARY KEY  (booking_type_id)
                            ) $charset_collate;";

                        $wp_queries[] = "INSERT INTO ".$wpdb->prefix ."bookingtypes ( title ) VALUES ( '". __('Default', 'wpdev-booking') ."' );";
                        $wp_queries[] = "INSERT INTO ".$wpdb->prefix ."bookingtypes ( title ) VALUES ( '". __('Appartment #1', 'wpdev-booking') ."' );";
                        $wp_queries[] = "INSERT INTO ".$wpdb->prefix ."bookingtypes ( title ) VALUES ( '". __('Appartment #2', 'wpdev-booking') ."' );";
                        $wp_queries[] = "INSERT INTO ".$wpdb->prefix ."bookingtypes ( title ) VALUES ( '". __('Appartment #3', 'wpdev-booking') ."' );";

                        $wp_queries[] = "INSERT INTO ".$wpdb->prefix ."booking ( form, modification_date ) VALUES (
                         'text^starttime1^10:20~text^endtime1^15:40~text^name1^Victoria~text^secondname1^Smith~text^email1^victoria@wpdevelop.com~text^phone1^(044)458-77-88~select-one^visitors1^2~checkbox^children1[]^false~textarea^details1^Please, reserve an appartment with fresh flowers.', NOW() );";

                        foreach ($wp_queries as $wp_q)
                            $wpdb->query($wpdb->prepare($wp_q));

                        $temp_id = $wpdb->insert_id;
                        $wp_queries_sub = "INSERT INTO ".$wpdb->prefix ."bookingdates (
                             booking_id,
                             booking_date
                            ) VALUES
                            ( ". $temp_id .", CURDATE()+ INTERVAL 6 day ),
                            ( ". $temp_id .", CURDATE()+ INTERVAL 7 day ),
                            ( ". $temp_id .", CURDATE()+ INTERVAL 8 day );";
                        $wpdb->query($wpdb->prepare($wp_queries_sub));

                }

                if ( class_exists('wpdev_bk_multiuser'))
                    if  ($this->is_field_in_table_exists('bookingtypes','users') == 0){
                        $simple_sql = "ALTER TABLE ".$wpdb->prefix ."bookingtypes ADD users BIGINT(20) DEFAULT '1'";
                        $wpdb->query($wpdb->prepare($simple_sql));
                    }


                if  ($this->is_field_in_table_exists('booking','remark') == 0){ // Add remark field
                    $simple_sql = "ALTER TABLE ".$wpdb->prefix ."booking ADD remark TEXT";
                    $wpdb->query($wpdb->prepare($simple_sql));
                }

                if  ($this->is_field_in_table_exists('booking','hash') == 0) {  //HASH_EDIT
                    $simple_sql = "ALTER TABLE ".$wpdb->prefix ."booking ADD hash TEXT AFTER form";
                    $wpdb->query($wpdb->prepare($simple_sql));

                    $sql_check_table = "SELECT booking_id as id FROM ".$wpdb->prefix ."booking " ;
                    $res = $wpdb->get_results($wpdb->prepare($sql_check_table) );
                    foreach ($res as $l) {
                         $wpdb->query($wpdb->prepare( "UPDATE ".$wpdb->prefix ."booking SET hash = MD5('".time() . '_' .rand(1000,1000000)."') WHERE booking_id = " . $l->id));
                    }

                }

                if ( wpdev_bk_is_this_demo() ) {
                    $remark_text = 'Here can be some note about this booking...';
                    $update_sql = "UPDATE ".$wpdb->prefix ."booking AS bk SET bk.remark='$remark_text' WHERE bk.booking_id=1;";
                    $wpdb->query($wpdb->prepare($update_sql));
                }


            if ( wpdev_bk_is_this_demo() )
                    add_bk_option( 'booking_url_bookings_edit_by_visitors', site_url() .'/booking/edit/' );
            else
                    add_bk_option( 'booking_url_bookings_edit_by_visitors', site_url() );



            add_bk_option( 'booking_default_booking_resource', $this->get_default_booking_resource_id() );
            add_bk_option( 'booking_is_change_hash_after_approvement', 'Off');



            add_bk_option( 'booking_email_newbookingbyperson_adress',htmlspecialchars('"Booking system" <' .get_option('admin_email').'>'));
            add_bk_option( 'booking_email_newbookingbyperson_subject',__('New booking', 'wpdev-booking'));
            $blg_title = get_option('blogname'); $blg_title = str_replace('"', '', $blg_title);$blg_title = str_replace("'", '', $blg_title);
            add_bk_option( 'booking_email_newbookingbyperson_content',htmlspecialchars(sprintf(__('Your booking %s for: %s is processing now! Please, wait for the confirmation email.  %sYou can edit this booking at this page: %s  Thank you, %s', 'wpdev-booking'),'[bookingtype]','[dates]','<br/><br/>[content]<br/><br/>', '[visitorbookingediturl]<br/><br/>' , $blg_title.'<br/>[siteurl]')));
            add_bk_option( 'booking_is_email_newbookingbyperson_adress', 'Off' );

            add_bk_option( 'booking_email_modification_adress',htmlspecialchars('"Booking system" <' .get_option('admin_email').'>'));
            add_bk_option( 'booking_email_modification_subject',__('The booking has been modified', 'wpdev-booking'));
            $blg_title = get_option('blogname'); $blg_title = str_replace('"', '', $blg_title);$blg_title = str_replace("'", '', $blg_title);
            add_bk_option( 'booking_email_modification_content',htmlspecialchars(sprintf(__('The booking %s for: %s has been  edited. %sYou can edit this booking at this page: %s  Thank you, %s', 'wpdev-booking'),'[bookingtype]','[dates]','<br/><br/>[content]<br/><br/>', '[visitorbookingediturl]<br/><br/>' , $blg_title.'<br/>[siteurl]')));
            add_bk_option( 'booking_is_email_modification_adress', 'On' );


            add_bk_option( 'booking_is_email_approval_send_copy_to_admin' , 'Off' );
            add_bk_option( 'booking_is_email_deny_send_copy_to_admin' , 'Off' );
            add_bk_option( 'booking_is_email_modification_send_copy_to_admin' , 'Off'  );
            add_bk_option( 'booking_resourses_num_per_page' , '10'  );


        }

        //Decativate
        function pro_deactivate(){
            global $wpdb;

            delete_bk_option( 'booking_form');
            delete_bk_option( 'booking_form_show');

            delete_bk_option( 'booking_default_booking_resource',1);

            delete_bk_option( 'booking_email_modification_adress' );
            delete_bk_option( 'booking_email_modification_subject');
            delete_bk_option( 'booking_email_modification_content');
            delete_bk_option( 'booking_is_email_modification_adress');

            delete_bk_option( 'booking_email_newbookingbyperson_adress' );
            delete_bk_option( 'booking_email_newbookingbyperson_subject');
            delete_bk_option( 'booking_email_newbookingbyperson_content');
            delete_bk_option( 'booking_is_email_newbookingbyperson_adress');



            delete_bk_option( 'booking_is_email_approval_send_copy_to_admin'  );
            delete_bk_option( 'booking_is_email_deny_send_copy_to_admin'  );
            delete_bk_option( 'booking_is_email_modification_send_copy_to_admin'  );


            delete_bk_option( 'booking_is_change_hash_after_approvement');
            delete_bk_option( 'booking_url_bookings_edit_by_visitors');
            delete_bk_option( 'booking_resourses_num_per_page'   );

            $wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'bookingtypes'));

        }





        // Check if user can be at some admin panel, which belong to specific booking resource
        function recheck_version($blank){ 
//delete_bk_option( 'bk_version_data' );
            $ver = get_bk_option('bk_version_data');
            if ( $ver === false ) {
            ?>
                    <div id="recheck_version">
                        <div class="clear" style="height:10px;"></div>
                            <script type="text/javascript">
                            function sendRecheck(order_num){


                                document.getElementById('ajax_working').innerHTML =
                                '<div class="info_message ajax_message" id="ajax_message">\n\
                                    <div style="float:left;">'+'<?php _e('Sending request...','wpdev-booking') ?>'+'</div> \n\
                                    <div  style="float:left;width:80px;margin-top:-3px;">\n\
                                           <img src="'+wpdev_bk_plugin_url+'/img/ajax-loader.gif">\n\
                                    </div>\n\
                                </div>';

                                jQuery.ajax({                                           // Start Ajax Sending
                                    url: '<?php echo WPDEV_BK_PLUGIN_URL , '/' ,  WPDEV_BK_PLUGIN_FILENAME ; ?>' ,
                                    type:'POST',
                                    success: function (data, textStatus){if( textStatus == 'success')   jQuery('#ajax_respond').html( data );},
                                    error:function (XMLHttpRequest, textStatus, errorThrown){window.status = 'Ajax sending Error status:'+ textStatus;alert(XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText);if (XMLHttpRequest.status == 500) {alert('Please check at this page according this error:' + ' http://wpbookingcalendar.com/faq/#ajax-sending-error');}},
                                    // beforeSend: someFunction,
                                    data:{
                                        ajax_action : 'CHECK_BK_VERSION',
                                        order_num:order_num
                                    }
                                });
                            }
                            </script>

                        <div style="margin:15px auto;width:700px;" class="code_description">
                            <div class="shortcode_help_section">

                            <div style="width:auto;text-align: center;padding:10px;">
                                <span style="font-weight:bold;font-size:12px;line-height:24px;margin-right:10px;" ><?php _e('Order number', 'wpdev-booking'); ?>:</span>

                                <input type="text" maxlength="12" value="" style="width:110px;" id="bk_order_number" name="bk_order_number" />
                                <input class="button" style="" type="button" value="<?php _e('Register', 'wpdev-booking'); ?>" name="submit_advanced_resources_settings" onclick="javascript:sendRecheck(document.getElementById('bk_order_number').value);" />
                                <div class="clear" style="height:10px;"></div>
                                <span style="font-style: italic;font-size:11px;color#ccc;text-shadow:0 1px 0 #fff;"><?php _e('Please, enter your order number of purchasing this version, which you are received in email.', 'wpdev-booking');?></span>


                                <div class="clear" style="height:40px;"></div>
                              <span class="description" style="font-style: italic;font-size:11px;"><?php printf(__('If you will get any difficulties or have a questions, please contact by email %s', 'wpdev-booking'),'<code><a href="mailto:activate@wpbookingcalendar.com">activate@wpbookingcalendar.com</a></code>');?></span><br/>

                              </div>
                            </div>
                        </div>

                    </div>

                </div>
            <?php return false;
            }
            return true;
        }

    }
}

?>