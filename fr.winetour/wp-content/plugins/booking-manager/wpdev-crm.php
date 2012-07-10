<?php
/*
Plugin Name: Booking Manager
Plugin URI: http://ordersplugin.onlinebookingcalendar.com/
Description: Booking Manager - show all old bookings (and cutomers) from <a href="http://onlinebookingcalendar.com">Booking Calendar</a> plugin. Profesional verion can export bookings to CSV, print bookings, have advanced filter.
Version: 1.1
Author: wpdevelop
Author URI: http://www.wpdevelop.com
*/

/* T O D O   P L A N   of  W O R K  :
 *
 * 2. Notes for customers and orders - Pro
 * 3. Actions: delete - Pro
 * 1. Email sending         - Pro
 * 2. Notification emails   - Pro
 *
 */

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (!function_exists ('debuge')) {  function debuge() {  $numargs = func_num_args();   $var = func_get_args(); $makeexit = is_bool($var[count($var)-1])?$var[count($var)-1]:false; echo "<div style='text-align:left;background:#ffffff;border: 1px dashed #ff9933;font-size:11px;line-height:15px;font-family:'Lucida Grande',Verdana,Arial,'Bitstream Vera Sans',sans-serif;'><pre>"; print_r ( $var ); echo "</pre></div>"; if ($makeexit) { echo '<div style="font-size:18px;float:right;">' . get_num_queries(). '/'  . timer_stop(0, 3) . 'qps</div>'; exit;} } }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Internal plugin action system
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (true) {
        global $wpdev_crm_action, $wpdev_crm_filter;

        function add_crm_filter($filter_type, $filter) {
            global $wpdev_crm_filter;

            $args = array();
            if ( is_array($filter) && 1 == count($filter) && is_object($filter[0]) ) // array(&$this)
                $args[] =& $filter[0];
            else
                $args[] = $filter;
            for ( $a = 2; $a < func_num_args(); $a++ )
                $args[] = func_get_arg($a);

            if ( is_array($wpdev_crm_filter) )
                if ( is_array($wpdev_crm_filter[$filter_type]) )
                    $wpdev_crm_filter[$filter_type][]= $args;
                else
                    $wpdev_crm_filter[$filter_type]= array($args);
            else
                $wpdev_crm_filter = array( $filter_type => array( $args ) ) ;
        }

        function remove_crm_filter($filter_type, $filter) {
            global $wpdev_crm_filter;

            if ( isset($wpdev_crm_filter[$filter_type]) ) {
                for ($i = 0; $i < count($wpdev_crm_filter[$filter_type]); $i++) {
                    if ( $wpdev_crm_filter[$filter_type][$i][0] == $filter ) {
                        $wpdev_crm_filter[$filter_type][$i] = null;
                        return;
                    }
                }
            }
        }

        function apply_crm_filter($filter_type) {
            global $wpdev_crm_filter;


            $args = array();
            for ( $a = 1; $a < func_num_args(); $a++ )
                $args[] = func_get_arg($a);

            $value = $args[0];

            if ( is_array($wpdev_crm_filter) )
                if ( isset($wpdev_crm_filter[$filter_type]) )
                    foreach ($wpdev_crm_filter[$filter_type] as $filter) {
                        $filter_func = array_shift($filter);
                        $parameter = $args;
                        $value =  call_user_func_array($filter_func,$parameter );
                    }
            return $value;
        }

        function make_crm_action($action_type) {
            global $wpdev_crm_action;


            $args = array();
            for ( $a = 1; $a < func_num_args(); $a++ )
                $args[] = func_get_arg($a);

            //$value = $args[0];

            if ( is_array($wpdev_crm_action) )
                if ( isset($wpdev_crm_action[$action_type]) )
                    foreach ($wpdev_crm_action[$action_type] as $action) {
                        $action_func = array_shift($action);
                        $parameter = $action;
                        call_user_func_array($action_func,$args );
                    }
        }

        function add_crm_action($action_type, $action) {
            global $wpdev_crm_action;

            $args = array();
            if ( is_array($action) && 1 == count($action) && is_object($action[0]) ) // array(&$this)
                $args[] =& $action[0];
            else
                $args[] = $action;
            for ( $a = 2; $a < func_num_args(); $a++ )
                $args[] = func_get_arg($a);

            if ( is_array($wpdev_crm_action) )
                if ( is_array($wpdev_crm_action[$action_type]) )
                    $wpdev_crm_action[$action_type][]= $args;
                else
                    $wpdev_crm_action[$action_type]= array($args);
            else
                $wpdev_crm_action = array( $action_type => array( $args ) ) ;
        }

        function remove_crm_action($action_type, $action) {
            global $wpdev_crm_action;

            if ( isset($wpdev_crm_action[$action_type]) ) {
                for ($i = 0; $i < count($wpdev_crm_action[$action_type]); $i++) {
                    if ( $wpdev_crm_action[$action_type][$i][0] == $action ) {
                        $wpdev_crm_action[$action_type][$i] = null;
                        return;
                    }
                }
            }
        }
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Get header info from this file, just for compatibility with WordPress 2.8 and older versions.
function get_file_data_crm_wpdev( $file, $default_headers, $context = '' ) {
	// We don't need to write to the file, so just open for reading.
	$fp = fopen( $file, 'r' );

	// Pull only the first 8kiB of the file in.
	$file_data = fread( $fp, 8192 );

	// PHP will close file handle, but we are good citizens.
	fclose( $fp );

	if( $context != '' ) {
		$extra_headers = array();//apply_filters( "extra_$context".'_headers', array() );

		$extra_headers = array_flip( $extra_headers );
		foreach( $extra_headers as $key=>$value ) {
			$extra_headers[$key] = $key;
		}
		$all_headers = array_merge($extra_headers, $default_headers);
	} else {
		$all_headers = $default_headers;
	}


	foreach ( $all_headers as $field => $regex ) {
		preg_match( '/' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, ${$field});
		if ( !empty( ${$field} ) )
			${$field} =  trim(preg_replace("/\s*(?:\*\/|\?>).*/", '',  ${$field}[1] ));
		else
			${$field} = '';
	}

	$file_data = compact( array_keys( $all_headers ) );

	return $file_data;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Define all our CONSTANTS and other...

$wpdev_booking_module_for_orders = false;

function wpdev_crm_define_static() {


    if (!function_exists ('get_option')) { die('You do not have permission to direct access to this file !!!'); }

    $default_headers = array(
            'Name' => 'Plugin Name',
            'PluginURI' => 'Plugin URI',
            'Version' => 'Version',
            'Description' => 'Description',
            'Author' => 'Author',
            'AuthorURI' => 'Author URI',
            'TextDomain' => 'Text Domain',
            'DomainPath' => 'Domain Path'
    );
    $plugin_data = get_file_data_crm_wpdev(  __FILE__, $default_headers, 'plugin' );

    if (!defined('WPDEV_CRM_VERSION'))    define('WPDEV_CRM_VERSION',  $plugin_data['Version'] );                             // 0.1
    if (!defined('WP_CONTENT_DIR'))   define('WP_CONTENT_DIR', ABSPATH . 'wp-content');                   // Z:\home\test.wpdevelop.com\www/wp-content
    if (!defined('WP_CONTENT_URL'))   define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');    // http://test.wpdevelop.com/wp-content
    if (!defined('WP_PLUGIN_DIR'))       define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');               // Z:\home\test.wpdevelop.com\www/wp-content/plugins
    if (!defined('WP_PLUGIN_URL'))       define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');               // http://test.wpdevelop.com/wp-content/plugins
    if (!defined('WPDEV_CRM_PLUGIN_FILENAME'))  define('WPDEV_CRM_PLUGIN_FILENAME',  basename( __FILE__ ) );              // menu-compouser.php
    if (!defined('WPDEV_CRM_PLUGIN_DIRNAME'))   define('WPDEV_CRM_PLUGIN_DIRNAME',  plugin_basename(dirname(__FILE__)) ); // menu-compouser
    if (!defined('WPDEV_CRM_PLUGIN_DIR')) define('WPDEV_CRM_PLUGIN_DIR', WP_PLUGIN_DIR.'/'.WPDEV_CRM_PLUGIN_DIRNAME ); // Z:\home\test.wpdevelop.com\www/wp-content/plugins/menu-compouser
    if (!defined('WPDEV_CRM_PLUGIN_URL')) define('WPDEV_CRM_PLUGIN_URL', WP_PLUGIN_URL.'/'.WPDEV_CRM_PLUGIN_DIRNAME ); // http://test.wpdevelop.com/wp-content/plugins/menu-compouser


    // Load modules, if they exist
    if (file_exists(WPDEV_CRM_PLUGIN_DIR. '/modules/booking-module.php')) {
        require_once(WPDEV_CRM_PLUGIN_DIR. '/modules/booking-module.php' );
        global $wpdev_booking_module_for_orders;
        if ( class_exists('wpdev_booking_module_for_orders')) {  $wpdev_booking_module_for_orders = new wpdev_booking_module_for_orders(); }
    }


    if (file_exists(WPDEV_CRM_PLUGIN_DIR. '/include/wpdev-pro.php')) { require_once(WPDEV_CRM_PLUGIN_DIR. '/include/wpdev-pro.php' ); }

    if ( ! loadLocale_crm() ) { loadLocale_crm('en_US');  }    //loadLocale_crm('ru_RU');                                      // Localization

}


// Load locale
function loadLocale_crm($locale = '') { // Load locale, if not so the  load en_EN default locale from folder "languages" files like "this_file_file_name-ru_RU.po" and "this_file_file_name-ru_RU.mo"
    if ( empty( $locale ) ) $locale = get_locale();
    if ( !empty( $locale ) ) {
        //Filenames like this  "microstock-photo-ru_RU.po",   "microstock-photo-de_DE.po" at folder "languages"
        $mofile = WPDEV_BK_PLUGIN_DIR  .'/languages/'.str_replace('.php','',WPDEV_CRM_PLUGIN_FILENAME).'-'.$locale.'.mo';
        if (file_exists($mofile))   return load_textdomain(str_replace('.php','',WPDEV_CRM_PLUGIN_FILENAME), $mofile);
        else                        return false;
    } return
    false;
}



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Getting Ajax requests
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ( isset( $_POST['ajax_action_crm'] ) ) {
    define('DOING_AJAX', true);
    require_once( dirname(__FILE__) . '/../../../wp-load.php' );
    @header('Content-Type: text/html; charset=' . get_option('blog_charset'));
    wpdev_crm_define_static();
    if ( class_exists('wpdev_crm_pro')) { $wpdev_crm_pro_in_ajax = new wpdev_crm_pro(); }
    wpdev_crm_ajax_responder();
}



wpdev_crm_define_static();

if (!class_exists('wpdev_crm')) {
    class wpdev_crm  {
            var $wpdev_crm_pro;
            var $wpdev_booking_module_for_orders;
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Constructor ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
         function wpdev_crm() {

             

            if ( class_exists('wpdev_crm_pro')) {  $this->wpdev_crm_pro = new wpdev_crm_pro(); }
            else {                                $this->wpdev_crm_pro = false;              }

            // Add settings top line before all other menu items.
            add_crm_action('wpdev_crm_settings_show_top_line', array($this, 'show_settings_menu_top_line'));
            add_crm_action('wpdev_crm_settings_show_content', array(&$this, 'show_settings_menu_content'));

            add_crm_action('wpdev_crm_orders_show_content', array(&$this, 'show_orders_content'));
            add_crm_action('wpdev_crm_customers_show_content', array(&$this, 'show_customers_content'));


            // Create admin menu
            add_action('admin_menu', array(&$this, 'add_new_admin_menu'));


            // Add settings link at the plugin page
            add_filter('plugin_action_links', array(&$this, 'plugin_links'), 10, 2 );


            // Install / Uninstall
            register_activation_hook( __FILE__, array(&$this,'wpdev_crm_activate' ));
            register_deactivation_hook( __FILE__, array(&$this,'wpdev_crm_deactivate' ));

            wp_enqueue_script( 'jquery-ui-dialog' );
            wp_enqueue_style(  'wpdev-bk-jquery-ui', WPDEV_CRM_PLUGIN_URL. '/css/jquery-ui.css', array(), 'wpdev-bk', 'screen' );

         }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ADMIN MENU SECTIONS  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
       function add_new_admin_menu(){
            $users_roles = array(get_option( 'wpdev_crm_user_role_orders' ), get_option( 'wpdev_crm_user_role_customers' ), get_option( 'wpdev_crm_user_role_settings' ) );
            //$users_roles = array('administrator','administrator','administrator');

            for ($i = 0 ; $i < count($users_roles) ; $i++) {
                if ( $users_roles[$i] == 'administrator' )  $users_roles[$i] = 10;
                if ( $users_roles[$i] == 'editor' )         $users_roles[$i] = 7;
                if ( $users_roles[$i] == 'author' )         $users_roles[$i] = 2;
                if ( $users_roles[$i] == 'contributor' )    $users_roles[$i] = 1;
                if ( $users_roles[$i] == 'subscriber')      $users_roles[$i] = 0;
            }

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // M A I N     B O O K I N G
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $pagehook1 = add_menu_page( __('Orders', 'wpdev-crm'), __('Orders', 'wpdev-crm'), $users_roles[0],
                   __FILE__ . 'wpdev-crm', array(&$this, 'on_show_crm_page_orders'),  WPDEV_CRM_PLUGIN_URL . '/img/crm-16x16.png'  );
            add_action("admin_print_scripts-" . $pagehook1 , array( &$this, 'on_add_admin_js_files'));
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // A D D     R E S E R V A T I O N
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $pagehook2 = add_submenu_page(__FILE__ . 'wpdev-crm',__('Customers', 'wpdev-crm'), __('Customers', 'wpdev-crm'), $users_roles[1],
                    __FILE__ .'wpdev-crm-customers', array(&$this, 'on_show_crm_page_customers')  );
            add_action("admin_print_scripts-" . $pagehook2 , array( &$this, 'on_add_admin_js_files'));
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // S E T T I N G S
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $pagehook3 = add_submenu_page(__FILE__ . 'wpdev-crm',__('Settings', 'wpdev-crm'), __('Settings', 'wpdev-crm'), $users_roles[2],
                    __FILE__ .'wpdev-crm-settings', array(&$this, 'on_show_crm_page_settings')  );
            add_action("admin_print_scripts-" . $pagehook3 , array( &$this, 'on_add_admin_js_files'));
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            global $submenu, $menu;               // Change Title of the Main menu inside of submenu
           // this is make error in roleswhen we have first autor and then admin and admin roles
            $submenu[plugin_basename( __FILE__ ) . 'wpdev-crm'][0][0] = __('Orders', 'wpdev-crm');

       }
           //Orders
           function on_show_crm_page_orders(){
               $this->on_show_page_adminmenu('wpdev-crm','/img/crm-48x48.png', __('Orders', 'wpdev-crm'),1);
           }
           //Customers
           function on_show_crm_page_customers(){
                $this->on_show_page_adminmenu('wpdev-crm-customers','/img/crm-48x48.png', __('Customers', 'wpdev-crm'),2);
           }
           //Settings
           function on_show_crm_page_settings(){
                $this->on_show_page_adminmenu('wpdev-crm-settings','/img/crm-48x48.png', __('Settings', 'wpdev-crm'),3);
           }
       //Show content
       function on_show_page_adminmenu($html_id, $icon, $title, $content_type) {
           ?>
            <div id="<?php echo $html_id; ?>-general" class="wrap crmpage">
            <?php
              if ($content_type == 3 ) echo '<div class="icon32" style="margin:5px 40px 10px 10px;"><img src="'. WPDEV_CRM_PLUGIN_URL . $icon .'"><br /></div>' ;
              else                     echo '<div class="icon32" style="margin:10px 25px 10px 10px;"><img src="'. WPDEV_CRM_PLUGIN_URL . $icon .'"><br /></div>' ; ?>

            <h2><?php echo $title; ?></h2>
                <?php
                switch ($content_type) {
                    case 1: $this->content_of_crm_orders(); break;
                    case 2: $this->content_of_crm_customers(); break;
                    case 3: $this->content_of_crm_settings(); break;
                    default: break;
                } ?>
            </div>
          <?php
       }
       ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // A D M I N    C O N T E N T    P A G E S
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
       // CONTENT OF THE ADMIN    O R D E R S   PAGE   /////////////////////////////////////////////////////////////////////////////////////////////
       function content_of_crm_orders(){ ?>
            <div style="height:1px;clear:both;border-bottom:1px solid #cccccc;"></div>
            <div id="ajax_working"></div>
            <div id="ajax_respond"></div>
            <div style="display:none;" id="wpdev-crm-dialog-container"><div id="wpdev-crm-dialog" ><div id="wpdev-crm-dialog-content" >
                        <!--div id="modal_content1" style="display:none;" class="modal_content_text" >
                            <iframe src="http://wpdevelop.com/booking-calendar-professional/#content" style="border:1px solid red; width:1000px;height:500px;padding:0px;margin:0px;"></iframe>
                        </div-->
                        <div id="modal_content1" style="display:none;" class="modal_content_text" style="" >
                        <p style="line-height:35px;text-align:left;font-size:17px;padding:0px;margin: 0px;text-align:center;"><?php printf(__('This functionality available in %sCustomer &amp; Orders Support Professional%s','wpdev-crm'),'<b style="font-size:15px;"> ',' </b>'); ?>
                        <?php _e('Please, check feature list for this version','wpdev-crm'); ?> <a href="http://wpdevelop.com/booking-manager-professional/" target="_blank"><?php _e('here','wpdev-crm'); ?></a></p>
                        <p style="line-height:25px;text-align:center;padding-top:15px;"><a href="http://wpdevelop.com/booking-manager-professional/" target="_blank" class="buttonlinktext">Buy now</a></p>
                        </div>
            </div></div></div>

            <div style="height:10px;clear:both;"></div>
            <?php if (!class_exists('wpdev_crm_pro')) { ?>
                <div style="float:right;margin:0px 10px 0 0;"> <input type="button" class="button" style="font-weight:bold;" value="<?php _e('Export (Pro)','wpdev-crm'); ?>" onclick="javascript:openModalWindowCRM(&quot;modal_content1&quot;);" /> </div>
                <div style="float:right;margin:0px 10px 0 0;"> <input type="button" class="button" style="font-weight:bold;" value="<?php _e('Print (Pro)','wpdev-crm'); ?>" onclick="javascript:openModalWindowCRM(&quot;modal_content1&quot;);" /> </div>
            <?php } else { ?>
                <div style="float:right;margin:0px 10px 0 0;"> <input type="button" class="button" style="font-weight:bold;" value="<?php _e('Export All','wpdev-crm'); ?>" onclick="javascript:make_export_all_csv();" /> </div>
                <div style="float:right;margin:0px 10px 0 0;"> <input type="button" class="button" style="font-weight:bold;" value="<?php _e('Export','wpdev-crm'); ?>" onclick="javascript:make_export_csv();" /> </div>
                <div style="float:right;margin:0px 10px 0 0;"> <input type="button" class="button" style="font-weight:bold;" value="<?php _e('Print','wpdev-crm'); ?>" onclick="javascript:make_print('modal_content_print');" /> </div>
            <?php } ?>
            <div style="float:left;margin:0px 0px;"> <input type="button" class="button" style="font-weight:bold;" value="<?php _e('Import new bookings','wpdev-crm'); ?>" onclick="javscript:importBookings();" /> </div>

            <?php if (!class_exists('wpdev_crm_pro')) { ?>
                <div style="float:left;margin:0px 0px;"> <input type="button" class="button" style="font-weight:bold;" value="<?php _e('Show filters (Pro)','wpdev-crm'); ?>" onclick="javascript:openModalWindowCRM(&quot;modal_content1&quot;);" /> </div>
            <?php } else { ?>
            <?php make_crm_action('wpdev_crm_show_orders_filter'); ?>
            <?php } ?>

            <div style="height:10px;clear:both;"></div>
            <?php make_crm_action('wpdev_crm_orders_show_content'); ?>
            <div style="height:10px;clear:both;"></div>
            <div style="float:left;margin:0px 0px;"> 
                <?php
                    if (isset($_GET['show_all_records'])) {
                        if ($_GET['show_all_records']=='1') { ?>
                            <input type="button" class="button" style="font-weight:bold;" value="<?php _e('Show orders on several pages','wpdev-crm'); ?>" onclick="javscript:showNormalRecordsAtOnePage();" /> </div>
                        <?php } else {  ?>
                            <input type="button" class="button" style="font-weight:bold;" value="<?php _e('Show records at one page','wpdev-crm'); ?>" onclick="javscript:showAllRecordsAtOnePage();" /> </div>
                        <?php }
                    } else { ?>
                            <input type="button" class="button" style="font-weight:bold;" value="<?php _e('Show records at one page','wpdev-crm'); ?>" onclick="javscript:showAllRecordsAtOnePage();" /> </div>
                    <?php } ?>

            <div style="height:10px;clear:both;"></div>
            <?php
       }

            // Show content of orders
           function show_orders_content(){ global $wpdb;
                                           global $wpdev_booking_module_for_orders;
                //    D E F I N E      I N I T I A L      P A R A M E T E R S    //////////////////////////////////////////////////////////////
                $start_just_page = '0';
                if (isset($_GET['wpdev_page_num']))
                    if ( ($_GET['wpdev_page_num'] +0 ) > 0)
                        $start_just_page =  $_GET['wpdev_page_num'];
                    
                $cont_on_page = get_option( 'wpdev_crm_rows_per_page_orders' );
                if (isset($_GET['show_all_records']))
                    if ($_GET['show_all_records']=='1')
                        $cont_on_page = 'all';
                if ($cont_on_page == 'all') {$cont_on_page = '10000'; $start_just_page = '0'; }
                
                $start_page = ((int) $start_just_page) * ((int) $cont_on_page);
                $sort_array_tags = array('date','id','customer','order_date','type','cost','email','internal_filter');

                $table_sort_tags = array('','ordr.order_id','ordr.customer_id','ordr.order_date','ordr.type','ordr.cost','custmr.email','ordr.internal_filters');
                $sort_tag_for_table ='';

                 // Set TYPE of SORTING :           dates, id, customer, order_date, type, cost
                 $sorted_structure = array();
                 $sort_type = 'id'; $sort_order = 'DESC'; $ii=0;
                 if (isset($_GET['wpdev_sort_order'])) {    // CHECK   S O R T   T A Gs
                     foreach ($sort_array_tags as $sort_tag) {
                         if ($_GET['wpdev_sort_order'] == $sort_tag)              { $sort_type = $sort_tag; $sort_order = 'ASC'; $sort_tag_for_table = $table_sort_tags[$ii] ; }
                         if ($_GET['wpdev_sort_order'] == ($sort_tag . '_desc') ) { $sort_type = $sort_tag; $sort_order = 'DESC';$sort_tag_for_table = $table_sort_tags[$ii] ; }
                         $ii++;
                     }
                 }

                 if ($sort_tag_for_table !=='') $sort_tag_for_table .= ' ' . $sort_order . ', ';

                $booking_date_view_type = get_option( 'booking_date_view_type');    // Get data from Booking Plugin according hided dates
                if ($booking_date_view_type == 'short'){ $wide_days_class = ' hide_dates_view '; $short_days_class = ''; }
                else                                   { $wide_days_class = ''; $short_days_class = ' hide_dates_view '; }

                $sort_url = '<a style="color:#fff;" href="admin.php?page='. WPDEV_CRM_PLUGIN_DIRNAME . '/'. WPDEV_CRM_PLUGIN_FILENAME. 'wpdev-crm';
                $sort_url = '<a style="color:#fff;" href="javascript:;" onclick="javascript:reloadpage_with_param(\'wpdev_sort_order\',\'';

                //if (isset( $_GET['wpdev_filter_customer'] )) { $sort_url .= '&wpdev_filter_customer=' . $_GET['wpdev_filter_customer']; }
                //$sort_url .= '&wpdev_sort_order=';


                $arr_asc  = '<img src="'.WPDEV_CRM_PLUGIN_URL.'/img/arrow-down.png" width="12" height="12" style="margin:0 0px -3px 0;">';
                $arr_desc = '<img src="'.WPDEV_CRM_PLUGIN_URL.'/img/arrow-up.png" width="12" height="12" style="margin:0 0px -3px 0;">';

                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $where = '';

                if (isset($_GET['wpdev_filter_customer'])) {
                    if ( $_GET['wpdev_filter_customer'] != '0') {
                        if ($where !== '') { $where .= ' AND '; }
                        $where .= '  custmr.customer_id = '. $_GET['wpdev_filter_customer'] . ' ';
                    }
                }


// TODO: add here filter and set it to pro
               $where = apply_crm_filter('wpdev_where_filter', $where);
                /*// Pro functionality //////////////////////////////////////////////////////////////////////////////
                if (isset($_GET['wpdev_filter_order_type'])) {
                    if ( ( $_GET['wpdev_filter_order_type'] !== '' ) && ( $_GET['wpdev_filter_order_type'] != '0' ) ) {
                        if ($where !== '') { $where .= ' AND '; }
                        $where .= '  ordr.internal_filters1 = '. $_GET['wpdev_filter_order_type'] . ' ';
                    }
                }
                if (isset($_GET['wpdev_filter_bk_dates_start'])) {  
                    if ( ( $_GET['wpdev_filter_bk_dates_start'] !== '' ) && ( $_GET['wpdev_filter_bk_dates_start'] != '0' ) ) {
                        if ($where !== '') { $where .= ' AND '; }
                        $my_date = explode('-',$_GET['wpdev_filter_bk_dates_start']);
                        $my_date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], '00', '00', '00' );
                        $where .= '  ordr.internal_filters2 > \''. $my_date . '\' ';
                    }
                }
                if (isset($_GET['wpdev_filter_bk_dates_fin'])) {  
                    if ( ( $_GET['wpdev_filter_bk_dates_fin'] !== '' ) && ( $_GET['wpdev_filter_bk_dates_fin'] != '0' ) ) {
                        if ($where !== '') { $where .= ' AND '; }
                        $my_date = explode('-',$_GET['wpdev_filter_bk_dates_fin']);
                        $my_date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], '23', '59', '59' );
                        $where .= '  ordr.internal_filters3 < \''. $my_date . '\' ';
                    }
                } 
                if (isset($_GET['wpdev_filter_cost_start'])) {
                    if ( ($_GET['wpdev_filter_cost_start'] +0) > 0  ) {
                        if ($where !== '') { $where .= ' AND '; }
                        $where .= '  ordr.cost > '. $_GET['wpdev_filter_cost_start'] . ' ';
                    }
                }
                if (isset($_GET['wpdev_filter_cost_fin'])) {
                    if ( ($_GET['wpdev_filter_cost_fin'] +0) > 0  ) {
                        if ($where !== '') { $where .= ' AND '; }
                        $where .= '  ordr.cost < '. $_GET['wpdev_filter_cost_fin'] . ' ';
                    }
                }/**/
                ///////////////////////////////////////////////////////////////////////////////////////////////////
//debuge($where);

                if ($where !== '') $where = ' WHERE ' . $where;

                //Set here order by some field
                $sql = "SELECT count(ordr.order_id) as cnt
                            FROM ".$wpdb->prefix ."wpdev_crm_orders as ordr
                            INNER JOIN ".$wpdb->prefix ."wpdev_crm_customers as custmr
                            ON    ordr.customer_id = custmr.customer_id
                            " . $where ."
                            ORDER BY ".$sort_tag_for_table." ordr.order_id DESC";

                $result = $wpdb->get_results( $sql );
                $togather_pages = ceil($result[0]->cnt / ((int) $cont_on_page));

                $sql = "SELECT *
                            FROM ".$wpdb->prefix ."wpdev_crm_orders as ordr
                            INNER JOIN ".$wpdb->prefix ."wpdev_crm_customers as custmr
                            ON    ordr.customer_id = custmr.customer_id " .
                            $where ."
                            ORDER BY ".$sort_tag_for_table." ordr.order_id DESC LIMIT " .$start_page . "," . $cont_on_page;

// debuge($sql);
                $result = $wpdb->get_results( $sql );
// debuge($result);
                 ?>
                <table class='booking_table' cellspacing="0" id="wpdev_order_table">
                    <tr>
                        <th style="width:28px;">
                        <?php echo $sort_url; ?><?php
                            if (isset($_GET['wpdev_sort_order'])) {  if ($_GET['wpdev_sort_order']=='id')  echo 'id_desc';    else echo 'id';  }
                            else echo 'id'; ?>');"><?php _e('ID', 'wpdev-crm'); ?></a><?php
                            if ( ($_GET['wpdev_sort_order']=='id')  )      echo $arr_asc;
                            if ( ($_GET['wpdev_sort_order']=='id_desc') || (! isset( $_GET['wpdev_sort_order']) )  ) echo $arr_desc;
                         ?>
                        </th>
                        <th style="width:auto;"><?php _e('Info', 'wpdev-crm'); ?>

                            <?php if ( class_exists('wpdev_booking')) { ?>
                            
                            <span style="font-size: 10px; font-weight: normal;">(<?php // printf(__('Sort results by: ','wpdev-crm'),'<strong>','</strong>'); ?>
                             <?php $current_sort_type = 'internal_filter'; $current_sort_type_title = __('Dates', 'wpdev-crm');
                             echo $sort_url; if (isset($_GET['wpdev_sort_order'])) {  if ($_GET['wpdev_sort_order']== $current_sort_type ) echo $current_sort_type . '_desc'; else echo $current_sort_type ; } else echo $current_sort_type . '_desc'; ?>');"><?php echo $current_sort_type_title ; ?></a><?php if ($_GET['wpdev_sort_order']== $current_sort_type          ){ echo $arr_asc; } if ($_GET['wpdev_sort_order']== $current_sort_type . '_desc'){ echo $arr_desc; } ?>
                             <?php /* ?>, <?php $current_sort_type = 'email'; $current_sort_type_title = __('Email', 'wpdev-crm');
                             echo $sort_url; if (isset($_GET['wpdev_sort_order'])) {  if ($_GET['wpdev_sort_order']== $current_sort_type ) echo $current_sort_type . '_desc'; else echo $current_sort_type ; } else echo $current_sort_type . '_desc'; ?>');"><?php echo $current_sort_type_title ; ?></a><?php if ($_GET['wpdev_sort_order']== $current_sort_type          ){ echo $arr_asc; } if ($_GET['wpdev_sort_order']== $current_sort_type . '_desc'){ echo $arr_desc; } ?>
                            <?php /**/ ?>
                                )</span>

                            
                            <div style="float:right;">
                            <span  class="showwidedates<?php echo $short_days_class; ?>" style="cursor: pointer;text-decoration: none;font-size:10px;" onclick="javascript:
                               jWPDev('.short_dates_view').addClass('hide_dates_view');
                               jWPDev('.short_dates_view').removeClass('show_dates_view');
                               jWPDev('.wide_dates_view').addClass('show_dates_view');
                               jWPDev('.wide_dates_view').removeClass('hide_dates_view');
                               jWPDev('#showwidedates').addClass('hide_dates_view');

                               jWPDev('.showwidedates').addClass('hide_dates_view');
                               jWPDev('.showshortdates').addClass('show_dates_view');
                               jWPDev('.showshortdates').removeClass('hide_dates_view');
                               jWPDev('.showwidedates').removeClass('show_dates_view');

                            "><img src="<?php echo WPDEV_CRM_PLUGIN_URL; ?>/img/arrow-left.png" width="16" height="16" style="margin:0 5px -4px 0;"><?php _e('Wide Days view', 'wpdev-crm'); ?><img src="<?php echo WPDEV_CRM_PLUGIN_URL; ?>/img/arrow-right.png" width="16" height="16"  style="margin:0 0px -4px 5px;"></span>
                            <span class="showshortdates<?php echo $wide_days_class; ?>" style="cursor: pointer;text-decoration: none;font-size:10px;"  onclick="javascript:
                               jWPDev('.wide_dates_view').addClass('hide_dates_view');
                               jWPDev('.wide_dates_view').removeClass('show_dates_view');
                               jWPDev('.short_dates_view').addClass('show_dates_view');
                               jWPDev('.short_dates_view').removeClass('hide_dates_view');

                               jWPDev('.showshortdates').addClass('hide_dates_view');
                               jWPDev('.showwidedates').addClass('show_dates_view');
                               jWPDev('.showwidedates').removeClass('hide_dates_view');
                               jWPDev('.showshortdates').removeClass('show_dates_view');
                               "><img src="<?php echo WPDEV_CRM_PLUGIN_URL; ?>/img/arrow-right.png" width="16" height="16" style="margin:0 5px -4px 0;"><?php _e('Short Days view', 'wpdev-crm'); ?><img src="<?php echo WPDEV_CRM_PLUGIN_URL; ?>/img/arrow-left.png" width="16" height="16"  style="margin:0 0px -4px 5px;"></span>
                            </div>
                            <?php } ?>
                        </th>
                        <th style="width:75px;"><?php $current_sort_type = 'customer'; $current_sort_type_title = __('Customer', 'wpdev-crm'); echo $sort_url; if (isset($_GET['wpdev_sort_order'])) {  if ($_GET['wpdev_sort_order']== $current_sort_type ) echo $current_sort_type . '_desc'; else echo $current_sort_type ; } else echo $current_sort_type . '_desc'; ?>');"><?php echo $current_sort_type_title ; ?></a><?php if ($_GET['wpdev_sort_order']== $current_sort_type          ){ echo $arr_asc; } if ($_GET['wpdev_sort_order']== $current_sort_type . '_desc'){ echo $arr_desc; } ?></th>
                        <th style="width:45px;"><?php $current_sort_type = 'type'; $current_sort_type_title = __('Type', 'wpdev-crm'); echo $sort_url; if (isset($_GET['wpdev_sort_order'])) {  if ($_GET['wpdev_sort_order']== $current_sort_type ) echo $current_sort_type . '_desc'; else echo $current_sort_type ; } else echo $current_sort_type . '_desc'; ?>');"><?php echo $current_sort_type_title ; ?></a><?php if ($_GET['wpdev_sort_order']== $current_sort_type          ){ echo $arr_asc; } if ($_GET['wpdev_sort_order']== $current_sort_type . '_desc'){ echo $arr_desc; } ?></th>
                        <th style="width:95px;"><?php $current_sort_type = 'order_date'; $current_sort_type_title = __('Date of order', 'wpdev-crm'); echo $sort_url; if (isset($_GET['wpdev_sort_order'])) {  if ($_GET['wpdev_sort_order']== $current_sort_type ) echo $current_sort_type . '_desc'; else echo $current_sort_type ; } else echo $current_sort_type . '_desc'; ?>');"><?php echo $current_sort_type_title ; ?></a><?php if ($_GET['wpdev_sort_order']== $current_sort_type          ){ echo $arr_asc; } if ($_GET['wpdev_sort_order']== $current_sort_type . '_desc'){ echo $arr_desc; } ?></th>
                        <?php if ( class_exists('wpdev_bk_premium')) { 
                          ?>    <th style="width:45px;border-bottom: 1px solid #aaa;"><?php $current_sort_type = 'cost'; $current_sort_type_title = __('Cost', 'wpdev-crm'); echo $sort_url; if (isset($_GET['wpdev_sort_order'])) {  if ($_GET['wpdev_sort_order']== $current_sort_type ) echo $current_sort_type . '_desc'; else echo $current_sort_type ; } else echo $current_sort_type . '_desc'; ?>');"><?php echo $current_sort_type_title ; ?></a><?php if ($_GET['wpdev_sort_order']== $current_sort_type          ){ echo $arr_asc; } if ($_GET['wpdev_sort_order']== $current_sort_type . '_desc'){ echo $arr_desc; } ?></th><?php
                        } ?>
                        <!--th style="width:50px;"><?php _e('Actions', 'wpdev-crm'); ?></th>
                        <th style="width:50px;"><?php _e('Labels', 'wpdev-crm'); ?></th-->
                    </tr>
                <?php                    
                 $result_structure = array();
                 $ns = -1;
                 $last_date = $last_show_date = '';

                 // Get   S T R U C T U R E   for   O R D E R
                 foreach ($result as $res) {
                        $ns++;
                        $content = false;
                        $my_type = unserialize($res->type);
                        $order_type = 0;
                        // C O N T E N T
                        if (isset($my_type['booking'])) {                                                   // C O N T E N T
                            if ( function_exists ('get_form_content')) {                                    // Booking
                                $content =  get_form_content($res->order_info, $my_type['booking']);
                                $order_type = $my_type['booking'];
                                $result_structure[$ns]['content'] = str_replace('<br/>', '', $content['content']);
                                $result_structure[$ns]['content'] = str_replace('<br />', '', $result_structure[$ns]['content']);
                            } else {
                                $result_structure[$ns]['content'] = __( 'Booking Calendar plugin is not installed. This plugin is requred Booking Calendar for this type of records. ', 'wpdev-crm');
                            }
                        } else  $result_structure[$ns]['content'] = $res->order_info;                       // SOME OTHER ORDERS


                         if ($content)                                                                      // D A T E S
                            if (isset($content['_all_'])) {
                                if (isset($content['_all_']['booking_dates'])) {
                                    $my_dates = explode(',',$content['_all_']['booking_dates']);
                                    sort($my_dates);
                                    $result_structure[$ns]['dates'] = $my_dates;
                                    if ($wpdev_booking_module_for_orders!== false)
                                        $result_structure[$ns]['short_dates'] = $wpdev_booking_module_for_orders->get_booking_short_days($my_dates);
                                }
                                if (isset($content['_all_']['email' . $my_type['booking'] ])) {
                                    $result_structure[$ns]['email'] = $content['_all_']['email' . $my_type['booking'] ];
                                }
                                $result_structure[$ns]['all']         = $content['_all_'];                  // A l l   b o o k i n g     d a t a
                            }

                         $result_structure[$ns]['id']           = $res->order_id;                           // I D
                         $result_structure[$ns]['customer']     = $res->second_name . ' ' .$res->name;      // C u s t o m e r
                         $result_structure[$ns]['customer_id']     = $res->customer_id ;                    // C u s t o m e r ID
                         $result_structure[$ns]['order_date']   = $res->order_date;                         // O r d e r    D a t e
                         $result_structure[$ns]['type']         = $order_type;                              // O r d e r    T y p e
                         if ( class_exists('wpdev_bk_premium')) $result_structure[$ns]['cost']= $res->cost; // O r d e r    C o s t
                 }
//debuge($result_structure, $sort_type);
                 // S O R T process
                 foreach ($result_structure as $key=>$value) {
                    switch ($sort_type) {
                        case 'date':
                            if ( ! empty($sorted_structure[$value['dates'][0]])) $sorted_structure[$value['dates'][0] . (time() * rand()) ] = $value;
                            else                                                 $sorted_structure[$value['dates'][0]] = $value; break;
                        case 'email':
                            if ( ! empty($sorted_structure[$value['email']])) $sorted_structure[$value['email'] . (time() * rand()) ] = $value;
                            else                                              $sorted_structure[$value['email']] = $value; break;
                        case 'customer':    $my_index = strtolower( str_replace(' ','',$value[ $sort_type ]) );  if ( ! empty($sorted_structure[$my_index])) $sorted_structure[$my_index . (time() * rand()) ] = $value; else $sorted_structure[strtolower($my_index)] = $value;  break;
                        case 'order_date':  $my_index = strtolower( str_replace(' ','',$value[ $sort_type ]) );  if ( ! empty($sorted_structure[$my_index])) $sorted_structure[$my_index . (time() * rand()) ] = $value; else $sorted_structure[strtolower($my_index)] = $value;  break;
                        case 'id':          $my_index = strtolower( str_replace(' ','',$value[ $sort_type ]) );  if ( ! empty($sorted_structure[$my_index])) $sorted_structure[$my_index . (time() * rand()) ] = $value; else $sorted_structure[strtolower($my_index)] = $value;  break;
                        case 'type':        $my_index = strtolower( str_replace(' ','',$value[ $sort_type ]) );  if ( ! empty($sorted_structure[$my_index])) $sorted_structure[$my_index . (time() * rand()) ] = $value; else $sorted_structure[strtolower($my_index)] = $value;  break;
                        case 'cost':        $my_index = strtolower( str_replace(' ','',$value[ $sort_type ]) );  if ( ! empty($sorted_structure[$my_index])) $sorted_structure[$my_index . (time() * rand()) ] = $value; else $sorted_structure[strtolower($my_index)] = $value;  break;
                        // Check booking here
                        //case 'email':     if ( ! empty($sorted_structure[$value['all']['email' . $value['type'] ]])) $sorted_structure[$value['all']['email' . $value['type'] ] . (time() * rand()) ] = $value; else $sorted_structure[strtolower($value['all']['email' . $value['type'] ])] = $value;  break;
                        default: $sorted_structure[$value['id']] = $value; break;
                    }
                    //debuge($sorted_structure);
                 }
                // if ($sort_order == 'DESC') krsort($sorted_structure );
                // else                       ksort($sorted_structure );

                 $alternative_color = '';
                 $summ_togather = 0;

                 
                 //  L o o p    A l l   R O W S
                 foreach ($sorted_structure as $sort_element)  {

                    if ( $alternative_color == '') {$alternative_color = ' class="alternative_color" ';} else { $alternative_color = '';} ?>

                    <tr>
                        <td<?php echo $alternative_color ?> style="border-right:1px solid #ccc;text-align:center;"><?php echo $sort_element['id']; ?></td>
                        <td<?php echo $alternative_color ?> style="border-right:1px solid #ccc;font-size:11px;"><?php echo $sort_element['content']; ?>
                           <?php
                                if (isset($sort_element['dates'])) $my_dates = $sort_element['dates'];
                                $outColorClass = 0; 
                                ?>
                                <div style="height: 0px; clear: both;margin:3px;" class="clear topmenuitemseparatorv0"></div>
                                <div style="float:left;height:auto;line-height: 21px; "><strong><?php _e('Booking dates','wpdev-crm') ?></strong>:&nbsp;</div>

                                    <div id="wide_dates_view<?php echo $sort_element['id'];?>" class="wide_dates_view<?php echo $wide_days_class; ?>"  >
                                    <?php foreach ($my_dates as $date_key => $bkdate) {
                                          $date_class = ( date('m',  mysql2date('U',$bkdate)) + 0 ) . '-' . ( date('d',  mysql2date('U',$bkdate)) + 0 ) . '-' . ( date('Y',  mysql2date('U',$bkdate)) + 0 );  ?>
                                          <span class="<?php echo "adate-" . $date_class; ?>"><a href="#" class="booking_overmause<?php echo $outColorClass; ?>" ><?php
                                            echo apply_filters('wpdev_bk_get_showing_date_format',  mysql2date('U', $bkdate )); ?>
                                          </a></span><?php
                                            //make_bk_action('show_subtype_num',$bk['booking_type'], $bk['id'], $bkdate);
                                         if ($date_key != (count($my_dates)-1)) echo ", ";
                                    }
                                    echo '</div><div id="short_dates_view'.$sort_element['id'].'"  class="short_dates_view'. $short_days_class. '"  >';

                                    $short_days = $sort_element['short_dates']; //if ($wpdev_booking_module_for_orders!== false) $wpdev_booking_module_for_orders->get_booking_short_days($my_dates);
                                    if ( isset($short_days))
                                    // SHORT DAYS ECHO
                                    foreach ($short_days[0] as $bkdate) { //echo $bkdate;   continue;
                                        if ((trim($bkdate) != ',') && (trim($bkdate) != '-')) { //eco date
                                            $date_class = ( date('m',  mysql2date('U',$bkdate)) + 0 ) . '-' . ( date('d',  mysql2date('U',$bkdate)) + 0 ) . '-' . ( date('Y',  mysql2date('U',$bkdate)) + 0 );

                                            echo '<span class="adate-'.$date_class.'"><a href="#" class="booking_overmause'. $outColorClass.'"'.
                                                ' >' . apply_filters('wpdev_bk_get_showing_date_format',  mysql2date('U', $bkdate )) .
                                                    '</a></span>';
                                            //echo apply_bk_filter('filter_subtype_num','',$bk['booking_type'], $bk['id'], $bkdate);
                                            echo ' ';
                                        } else { // echo separator
                                            echo '<span class="date_tire">'.$bkdate.' </span>' ;
                                        }
                                    }
                                    ?>
                                    </div>
                        </td>
                        <td<?php echo $alternative_color ?> style="border-right:1px solid #ccc;"><?php
//debuge($sort_element);die;
                            echo '<a  class="bktypetitle" style="line-height: 25px; background-color:#888;text-shadow:0 -1px 0 #CCCCCC;color:#fff; padding: 2px 5px; font-size: 10px; white-space: nowrap;" href="admin.php?page=' . WPDEV_CRM_PLUGIN_DIRNAME . '/'. WPDEV_CRM_PLUGIN_FILENAME . 'wpdev-crm-customers'.'&wpdev_selected_customer='.$sort_element['customer_id'].'">';
                            echo $sort_element['customer']; $current_customer = $sort_element['customer']; ?></a></td>
                        <td<?php echo $alternative_color ?> style="border-right:1px solid #ccc;"><?php
                                if (function_exists('get_booking_title')) {
                                    $title = get_booking_title( $sort_element['type'] ) ;
                                    echo '<a class="bktypetitle" style="line-height: 25px; background-color:#fff; padding: 2px 5px; font-size: 10px; white-space: nowrap;"  href="admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME . 'wpdev-crm'.'&booking_type='.$sort_element['type'].'" >' , $title , '</a>';
                                }
                        ?></td>
                        <td<?php echo $alternative_color ?> style="border-right:1px solid #ccc;padding:0px 5px 0px 5px;"><?php
                              $order_year = ( date('Y',  mysql2date('U',$sort_element['order_date'])) + 0 );
//debuge($sort_element['order_date']);
                              if ( $order_year != '1970') {
                                  $outColorClass = 1;
                                  $date_class = ( date('m',  mysql2date('U',$sort_element['order_date'])) + 0 ) . '-' . ( date('d',  mysql2date('U',$sort_element['order_date'])) + 0 ) . '-' . ( date('Y',  mysql2date('U',$sort_element['order_date'])) + 0 );  ?>
                                  <span class="<?php echo "adate-" . $date_class; ?>"><a href="#" class="booking_overmause<?php echo $outColorClass; ?>" ><?php
                                    echo apply_filters('wpdev_bk_get_showing_date_format',  mysql2date('U', $sort_element['order_date'] ));
                                  ?></a></span>
                              <?php } ?>
                        </td>
                        <?php if ( class_exists('wpdev_bk_premium')) { ?>
                        <th<?php echo $alternative_color ?>><?php
                            $cost_currency = '';
                            $cost_currency = get_option( 'booking_paypal_curency' );
                            if ($cost_currency == 'USD' ) $cost_currency = '$';
                            elseif ($cost_currency == 'EUR' ) $cost_currency = '&euro;';
                            elseif ($cost_currency == 'GBP' ) $cost_currency = '&#163;';
                            elseif ($cost_currency == 'JPY' ) $cost_currency = '&#165;';

                            echo $cost_currency  ,$sort_element['cost'];
                            $summ_togather += (float) $sort_element['cost'];
                        ?></th>
                        <?php } ?>
                        <!--td<?php echo $alternative_color ?>><?php _e('Actions', 'wpdev-crm'); ?></td>
                        <td<?php echo $alternative_color ?>><?php _e('Labels', 'wpdev-crm'); ?></td-->
                    </tr>
                    <?php
                 }

                 //  T a bl e   f o o t e r
                 if ( count($sorted_structure) > 0 ) {
                 ?>
                    <tr>
                        <th style="border-top:1px solid #777;" align="left" colspan="2">
                            <?php
                             $page_url = 'admin.php?page='. WPDEV_CRM_PLUGIN_DIRNAME . '/'. WPDEV_CRM_PLUGIN_FILENAME. 'wpdev-crm';
                             if (isset( $_GET['wpdev_filter_customer'] )) { $page_url .= '&wpdev_filter_customer=' . $_GET['wpdev_filter_customer']; }
                             if (isset( $_GET['wpdev_sort_order'] )) { $page_url .= '&wpdev_sort_order=' . $_GET['wpdev_sort_order']; }
                             for ($ii  = 0; $ii < $togather_pages; $ii++) {
                                if ($start_just_page == $ii) $selected_page = ' selected ';
                                else $selected_page = '';
                                ?><a class="<?php echo $selected_page; ?>" 
                                   href="javascript:;"  onclick="javascript:reloadpage_with_param('wpdev_page_num',<?php echo $ii ?>);"
                                   style="color:#fff;"><?php echo ($ii+1) ?></a> &nbsp;<?php
                             } ?>
                        </th>
                        <th style="border-top:1px solid #777;"></th>
                        <th style="border-top:1px solid #777;"></th>
                        <th style="border-top:1px solid #777;font-size: 15px;" align="right"><?php if ( class_exists('wpdev_bk_premium')) { _e('Summ','wpdev-crm'); echo ' :'; } ?></th>
                        <?php if ( class_exists('wpdev_bk_premium')) { ?>
                            <th<?php echo $alternative_color ?> style="border-top:1px solid #777;font-size: 15px;"><?php
                                $cost_currency = '';
                                $cost_currency = get_option( 'booking_paypal_curency' );
                                if ($cost_currency == 'USD' ) { $cost_currency = '$'; }
                                elseif ($cost_currency == 'EUR' ) { $cost_currency = '&euro;'; }
                                elseif ($cost_currency == 'GBP' ) $cost_currency = '&#163;';
                                elseif ($cost_currency == 'JPY' ) $cost_currency = '&#165;';

                                echo $cost_currency   ,$summ_togather;
                            ?></th>
                        <?php }?>
                    </tr>
                <?php } else { ?>
                    <tr><td colspan="6" style="text-align: center; " class="this_td_empty">
                    <strong><?php _e('There are no orders. ','wpdev-crm') ?></strong><br/><br/>
                    <?php printf(__('Right now current plugin is support orders from %s or newer.','wpdev-crm'), '<strong>Booking Calendar v.2.1</strong>'); ?><br/><br/>
                    <?php printf(__('You can  install %s%s%s now for free. ','wpdev-crm'), '<a href="'.get_option('siteurl')  . '/wp-admin/plugin-install.php?tab=search&type=tag&s=reservation-service">', '<strong>Booking Calendar Standard</strong>','</a>'); ?><br/>
                    </td></tr>
                <?php } ?>
                </table>
                <?php

                    make_crm_action('wpdev_crm_make_generate_sorted_structure', $sorted_structure);
/*

    $wpdev_csv = new adv_csv();
    $csv = $wpdev_csv->adv_html_to_csv($out1);

    $dir_relative      =  '';       //$b['host'] . dirname ( $b['path'] );
    $dir      = $_SERVER['DOCUMENT_ROOT'] .$dir_relative ;       //$b['host'] . dirname ( $b['path'] );

    $fp =    fopen( $dir . '/' . 'file.csv' , 'w' );
    $search = array ("'<script[^>]*?>.*?</script>'si","'<[\/\!]*?[^<>]*?>'si","'([\r\n])[\s]+'","'&(quot|#34);'i","'&(amp|#38);'i","'&(lt|#60);'i","'&(gt|#62);'i","'&(nbsp|#160);'i","'&(iexcl|#161);'i","'&(cent|#162);'i","'&(pound|#163);'i","'&(copy|#169);'i","'&#(\d+);'e");
						$replace = array ("","","\\1","\"","&","<",">"," ",chr(161),chr(162),chr(163),chr(169),"chr(\\1)");
						$csv = preg_replace($search, $replace, $csv);

    fwrite($fp, $csv);
    //foreach ($csv as $line) {    fputcsv($fp, split(',', $line)); }
    fclose($fp);
//debuge($csv);/**/
?>

                 <script type="text/javascript">
                     <?php if ( isset($_GET['wpdev_filter_customer']) ){
                            if ( $_GET['wpdev_filter_customer'] != '0') {
                         ?>
                                //var val1 = '<img src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $selected_icon; ?>"><br />';
                                //jQuery('div.wrap div.icon32').html(val1);
                                //jQuery('div.bookingpage h2').after('<?php echo $support_links; ?>');
                                jQuery('div.crmpage h2').html(  jQuery('div.crmpage h2').html() + '<?php echo ' '; _e('from','wpdev-crm'); echo ' ', $current_customer ; ?>');
                     <?php } }  ?>
                </script>
                <?php
            }



       // CONTENT OF THE ADMIN PAGE   ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
       function content_of_crm_customers(){?>
            <div style="height:1px;clear:both;border-bottom:1px solid #cccccc;"></div>
            <div id="ajax_working"></div>

            <div style="display:none;" id="wpdev-crm-dialog-container"><div id="wpdev-crm-dialog" ><div id="wpdev-crm-dialog-content" >
                        <!--div id="modal_content1" style="display:none;" class="modal_content_text" >
                            <iframe src="http://wpdevelop.com/booking-manager-professional/#content" style="border:1px solid red; width:1000px;height:500px;padding:0px;margin:0px;"></iframe>
                        </div-->
                        <div id="modal_content1" style="display:none;" class="modal_content_text" style="" >
                        <p style="line-height:25px;text-align:left;"><?php printf(__('This functionality exist at %sProfessional%s version.','wpdev-crm'),'<a href="http://wpdevelop.com/booking-manager-professional/" target="_blank">','</a>','<a href="http://wpdevelop.com/booking-manager-professional/" target="_blank">','</a>','<a href="http://wpdevelop.com/booking-manager-professional/" target="_blank">','</a>'); ?>
                        Please, check feature list for this version <a href="http://wpdevelop.com/booking-manager-professional/" target="_blank">here</a></p>
                        <p style="line-height:25px;text-align:center;padding-top:15px;"><a href="http://wpdevelop.com/booking-manager-professional/" target="_blank" class="buttonlinktext">Buy now</a></p>
                        </div>
            </div></div></div>

            <?php make_crm_action('wpdev_crm_customers_show_content');
       }
            // Show content of orders
           function show_customers_content(){  global $wpdb;

                //    D E F I N E      I N I T I A L      P A R A M E T E R S    //////////////////////////////////////////////////////////////
                $start_just_page = '0';
                if (isset($_GET['wpdev_page_num']))
                    if ( ($_GET['wpdev_page_num'] +0 ) > 0)
                        $start_just_page =  $_GET['wpdev_page_num'];

                $cont_on_page = get_option( 'wpdev_crm_rows_per_page_customers' );
                $start_page = ((int) $start_just_page) * ((int) $cont_on_page);

                $sort_array_tags = array( 'id','name','email','phone','adress','city','country','orders_num');

                $table_sort_tags = array( 'custmr.customer_id','custmr.second_name','custmr.email','custmr.phone','custmr.adress','custmr.city','custmr.country','cnt');
                $sort_tag_for_table ='';


                 // Set TYPE of SORTING :           dates, id, customer, order_date, type, cost
                 $sorted_structure = array();
                 $sort_type = 'id'; $sort_order = 'DESC'; $ii=0;
                 if (isset($_GET['wpdev_sort_order'])) {    // CHECK   S O R T   T A Gs
                     foreach ($sort_array_tags as $sort_tag) {
                         if ($_GET['wpdev_sort_order'] == $sort_tag)              { $sort_type = $sort_tag; $sort_order = 'ASC'; $sort_tag_for_table = $table_sort_tags[$ii] ; }
                         if ($_GET['wpdev_sort_order'] == ($sort_tag . '_desc') ) { $sort_type = $sort_tag; $sort_order = 'DESC';$sort_tag_for_table = $table_sort_tags[$ii] ; }
                         $ii++;
                     }
                 }
                 if ($sort_tag_for_table !=='') $sort_tag_for_table .= ' ' . $sort_order . ', ';
                $sort_url = '<a style="color:#fff;" href="admin.php?page='. WPDEV_CRM_PLUGIN_DIRNAME . '/'. WPDEV_CRM_PLUGIN_FILENAME. 'wpdev-crm-customers&wpdev_sort_order=';
                $arr_asc  = '<img src="'.WPDEV_CRM_PLUGIN_URL.'/img/arrow-down.png" width="12" height="12" style="margin:0 0px -3px 0;">';
                $arr_desc = '<img src="'.WPDEV_CRM_PLUGIN_URL.'/img/arrow-up.png" width="12" height="12" style="margin:0 0px -3px 0;">';

                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                //Set here order by some field
                $sql = "SELECT count(custmr.customer_id) as cnt
                            FROM ".$wpdb->prefix ."wpdev_crm_customers as custmr
                            ORDER BY ".$sort_tag_for_table." custmr.customer_id DESC";

                $result = $wpdb->get_results( $sql );
                $togather_pages = ceil($result[0]->cnt / ((int) $cont_on_page));


               ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
               //     S Q L
               $sql = "SELECT custmr.*, count(ordr.order_id) as cnt
                            FROM  ".$wpdb->prefix ."wpdev_crm_customers as custmr
                            INNER JOIN ".$wpdb->prefix ."wpdev_crm_orders as ordr
                            ON    ordr.customer_id = custmr.customer_id
                            GROUP by customer_id 
                            ORDER BY ".$sort_tag_for_table." custmr.customer_id DESC LIMIT " .$start_page . "," . $cont_on_page ;
                $result = $wpdb->get_results( $sql );

                $alternative_color = '';
            ?>
            <br/><br/><table class='booking_table' cellspacing="0"  >
                    <tr>
                        <th style="width:28px;">                        <?php echo $sort_url; ?><?php
                            if (isset($_GET['wpdev_sort_order'])) {  if ($_GET['wpdev_sort_order']=='id')  echo 'id_desc';    else echo 'id';  }
                            else echo 'id'; ?>"><?php _e('ID', 'wpdev-crm'); ?></a><?php
                            if ( ($_GET['wpdev_sort_order']=='id')  )      echo $arr_asc;
                            if ( ($_GET['wpdev_sort_order']=='id_desc') || (! isset( $_GET['wpdev_sort_order']) )  ) echo $arr_desc;
                         ?>
                        </th>                        
                        <th style="width:70px;"><?php $current_sort_type = 'name'; $current_sort_type_title = __('Name', 'wpdev-crm'); echo $sort_url; if (isset($_GET['wpdev_sort_order'])) {  if ($_GET['wpdev_sort_order']== $current_sort_type ) echo $current_sort_type . '_desc'; else echo $current_sort_type ; } else echo $current_sort_type . '_desc'; ?>"><?php echo $current_sort_type_title ; ?></a><?php if ($_GET['wpdev_sort_order']== $current_sort_type          ){ echo $arr_asc; } if ($_GET['wpdev_sort_order']== $current_sort_type . '_desc'){ echo $arr_desc; } ?></th>
                        <th style="width:170px;"><?php $current_sort_type = 'email'; $current_sort_type_title = __('Email', 'wpdev-crm'); echo $sort_url; if (isset($_GET['wpdev_sort_order'])) {  if ($_GET['wpdev_sort_order']== $current_sort_type ) echo $current_sort_type . '_desc'; else echo $current_sort_type ; } else echo $current_sort_type . '_desc'; ?>"><?php echo $current_sort_type_title ; ?></a><?php if ($_GET['wpdev_sort_order']== $current_sort_type          ){ echo $arr_asc; } if ($_GET['wpdev_sort_order']== $current_sort_type . '_desc'){ echo $arr_desc; } ?></th>
                        <th style="width:90px;"><?php $current_sort_type = 'phone'; $current_sort_type_title = __('Phone', 'wpdev-crm'); echo $sort_url; if (isset($_GET['wpdev_sort_order'])) {  if ($_GET['wpdev_sort_order']== $current_sort_type ) echo $current_sort_type . '_desc'; else echo $current_sort_type ; } else echo $current_sort_type . '_desc'; ?>"><?php echo $current_sort_type_title ; ?></a><?php if ($_GET['wpdev_sort_order']== $current_sort_type          ){ echo $arr_asc; } if ($_GET['wpdev_sort_order']== $current_sort_type . '_desc'){ echo $arr_desc; } ?></th>
                        <th style="width:auto;"><?php $current_sort_type = 'adress'; $current_sort_type_title = __('Adress', 'wpdev-crm'); echo $sort_url; if (isset($_GET['wpdev_sort_order'])) {  if ($_GET['wpdev_sort_order']== $current_sort_type ) echo $current_sort_type . '_desc'; else echo $current_sort_type ; } else echo $current_sort_type . '_desc'; ?>"><?php echo $current_sort_type_title ; ?></a><?php if ($_GET['wpdev_sort_order']== $current_sort_type          ){ echo $arr_asc; } if ($_GET['wpdev_sort_order']== $current_sort_type . '_desc'){ echo $arr_desc; } ?></th>
                        <th style="width:75px;"><?php $current_sort_type = 'city'; $current_sort_type_title = __('City', 'wpdev-crm'); echo $sort_url; if (isset($_GET['wpdev_sort_order'])) {  if ($_GET['wpdev_sort_order']== $current_sort_type ) echo $current_sort_type . '_desc'; else echo $current_sort_type ; } else echo $current_sort_type . '_desc'; ?>"><?php echo $current_sort_type_title ; ?></a><?php if ($_GET['wpdev_sort_order']== $current_sort_type          ){ echo $arr_asc; } if ($_GET['wpdev_sort_order']== $current_sort_type . '_desc'){ echo $arr_desc; } ?></th>
                        <th style="width:65px;"><?php $current_sort_type = 'country'; $current_sort_type_title = __('Country', 'wpdev-crm'); echo $sort_url; if (isset($_GET['wpdev_sort_order'])) {  if ($_GET['wpdev_sort_order']== $current_sort_type ) echo $current_sort_type . '_desc'; else echo $current_sort_type ; } else echo $current_sort_type . '_desc'; ?>"><?php echo $current_sort_type_title ; ?></a><?php if ($_GET['wpdev_sort_order']== $current_sort_type          ){ echo $arr_asc; } if ($_GET['wpdev_sort_order']== $current_sort_type . '_desc'){ echo $arr_desc; } ?></th>
                        <th style="width:117px;"><?php $current_sort_type = 'orders_num'; $current_sort_type_title = __('Count of Orders', 'wpdev-crm'); echo $sort_url; if (isset($_GET['wpdev_sort_order'])) {  if ($_GET['wpdev_sort_order']== $current_sort_type ) echo $current_sort_type . '_desc'; else echo $current_sort_type ; } else echo $current_sort_type . '_desc'; ?>"><?php echo $current_sort_type_title ; ?></a><?php if ($_GET['wpdev_sort_order']== $current_sort_type          ){ echo $arr_asc; } if ($_GET['wpdev_sort_order']== $current_sort_type . '_desc'){ echo $arr_desc; } ?></th>
                        <!--th style="width:50px;"><?php _e('Actions', 'wpdev-crm'); ?></th-->
                    </tr>
            <?php
            $page_url = 'admin.php?page='. WPDEV_CRM_PLUGIN_DIRNAME . '/'. WPDEV_CRM_PLUGIN_FILENAME. 'wpdev-crm';

            for ($i = 0; $i < count($result); $i++) {
                 if ( $alternative_color == '') {$alternative_color = ' class="alternative_color" ';} else { $alternative_color = '';}
                
                 if (isset($_GET['wpdev_selected_customer']))
                     if ($_GET['wpdev_selected_customer'] == $result[$i]->customer_id ) {
                             $alternative_color = ' class="selected_row_color" ';
                     }

                 ?>
                    <tr>
                        <td style="text-align:center;border-right:1px solid #ccc;" <?php echo $alternative_color ?>><?php echo $result[$i]->customer_id; ?></td>
                        <td style="border-right:1px solid #ccc;" <?php echo $alternative_color ?>>
                            <a href="<?php echo $page_url . '&wpdev_filter_customer=' . $result[$i]->customer_id; ?>" style="line-height: 25px; background-color: rgb(136, 136, 136); text-shadow: 0pt -1px 0pt rgb(204, 204, 204); color: rgb(255, 255, 255); padding: 2px 5px; font-size: 10px; white-space: nowrap;" class="bktypetitle">
                        <?php echo $result[$i]->second_name . ' ' . $result[$i]->name; ?></a>
                        </td>
                        <td style="border-right:1px solid #ccc;" <?php echo $alternative_color ?>><?php echo $result[$i]->email; ?></td>
                        <td style="border-right:1px solid #ccc;" <?php echo $alternative_color ?>><?php echo $result[$i]->phone; ?></td>
                        <td style="border-right:1px solid #ccc;" <?php echo $alternative_color ?>><?php echo $result[$i]->adress; ?></td>
                        <td style="border-right:1px solid #ccc;text-align:center;" <?php echo $alternative_color ?>><?php echo $result[$i]->city; ?></td>
                        <td style="border-right:1px solid #ccc;text-align:center;" <?php echo $alternative_color ?>><?php echo $result[$i]->country; ?></td>
                        <td style="border-right:1px solid #ccc;text-align:center;" <?php echo $alternative_color ?>>
                            <a href="<?php echo $page_url . '&wpdev_filter_customer=' . $result[$i]->customer_id; ?>" style="line-height: 25px; background-color: rgb(136, 136, 136); text-shadow: 0pt -1px 0pt rgb(204, 204, 204); color: rgb(255, 255, 255); padding: 2px 5px; font-size: 10px; white-space: nowrap;" class="bktypetitle">
                                <?php  echo $result[$i]->cnt; ?></a></td>
                        <?php /* ?>
                        <td style="text-align:center;" <?php echo $alternative_color ?>>
                                    <?php
                                    if ($bk_type==0)  if (isset($type_items[ $bk['booking_type'] ]))
                                        echo '<a href="admin.php?page=' . WPDEV_CRM_PLUGIN_DIRNAME . '/'. WPDEV_CRM_PLUGIN_FILENAME . 'wpdev-crm'.'&booking_type='.$bk['booking_type'].'" class="bktypetitle" style="line-height:25px;background-color:#fff;padding:2px 5px;font-size:10px;white-space:nowrap;">' . $type_items[ $bk['booking_type'] ]  . '</a>';

?>
  <a href="javascript:;" onclick='javascript:openModalWindowCRM(&quot;modal_content1&quot;);' >
    <img  src="<?php  echo  WPDEV_CRM_PLUGIN_URL . '/img/delete_type.png'; ?>" class="tipcy" title='<?php echo __('Edit reservation','wpdev-crm'); ?>'
          /></a>
<?php ?>
   <a href="javascript:;" onclick='javascript:openModalWindowCRM(&quot;modal_content1&quot;);' >
        <img  src="<?php  echo  WPDEV_CRM_PLUGIN_URL . '/img/notes.png'; ?>" class="tipcy" title='<?php echo __('Edit Notes','wpdev-crm'); ?>' /></a>
                                    <?php 
                                        //echo "<input type='button' class='button' value='".__('Remark','wpdev-crm')."' onclick='javascript:openModalWindow(&quot;modal_content1&quot;);'>";

                                       make_CRM_action('show_remark_editing_field',$bk['id'],$bk,"height:1px;padding:0px;margin:0px;border:none;");
                                    ?>
                        </td>
                        <?php /**/ ?>
                    </tr>
                    <?php
            }


                 //  T a bl e   f o o t e r
                 if ( count($result) > 0 ) {
                 ?>
                    <tr>
                        <th style="border-top:1px solid #777;" align="left" colspan="9">
                            <?php
                             $page_url = 'admin.php?page='. WPDEV_CRM_PLUGIN_DIRNAME . '/'. WPDEV_CRM_PLUGIN_FILENAME. 'wpdev-crm-customers';
                             if (isset( $_GET['wpdev_sort_order'] )) { $page_url .= '&wpdev_sort_order=' . $_GET['wpdev_sort_order']; }
                             for ($ii  = 0; $ii < $togather_pages; $ii++) {
                                if ($start_just_page == $ii) $selected_page = ' selected ';
                                else $selected_page = '';
                                ?><a class="<?php echo $selected_page; ?>" href="<?php echo $page_url; ?>&wpdev_page_num=<?php echo $ii ?>" style="color:#fff;"><?php echo ($ii+1) ?></a> &nbsp;<?php
                             } ?>
                        </th>
                    </tr>
                <?php } else { ?>
                    <tr><td colspan="9" style="text-align: center; ">
                    <strong><?php _e('There are no customers. ','wpdev-crm') ?></strong><br/><br/>
                    <?php printf(__('Right now current plugin is support orders from %s or newer.','wpdev-crm'), '<strong>Booking Calendar v.2.1</strong>'); ?><br/><br/>
                    <?php printf(__('You can  install %s%s%s now for free. ','wpdev-crm'), '<a href="'.get_option('siteurl')  . '/wp-admin/plugin-install.php?tab=search&type=tag&s=reservation-service">', '<strong>Booking Calendar Standard</strong>','</a>'); ?><br/>
                    </td></tr>
                <?php } 

            ?> </table> <?php

           }



       // CONTENT OF THE ADMIN    S E T T I N G S    PAGE   //////////////////////////////////////////////////////////////////////////////////////////
       function content_of_crm_settings(){ 
            make_crm_action('wpdev_crm_settings_show_top_line'); ?>
            <div style="height:1px;clear:both;border-top:1px solid #cccccc;"></div>
            <div id="ajax_working"></div>
           <?php make_crm_action('wpdev_crm_settings_show_content');
       }

           // Show     T O P   L I N E   M E N U
           function show_settings_menu_top_line(){
                $version = 'free';
                if (class_exists('wpdev_crm_pro'))     $version = 'pro';

               ?>
                
                 <div style="height:1px;clear:both;margin-top:20px;"></div>
                 <div id="menu-wpdevplugin">
                    <div class="nav-tabs-wrapper">
                        <div class="nav-tabs">
                                <?php $title = __('General', 'wpdev-crm');  $my_icon = 'General-setting-64x64.png'; $my_tab = 'main';  ?>
                                <?php if ( ($_GET['tab'] == $my_tab) || (! isset($_GET['tab'])) ) { $slct_a = 'selected'; } else { $slct_a = ''; } ?>
                                <?php if ($slct_a == 'selected') { $selected_title = $title; $selected_icon = $my_icon;  ?><span class="nav-tab nav-tab-active"><?php } else { ?><a class="nav-tab" href="admin.php?page=<?php echo WPDEV_CRM_PLUGIN_DIRNAME . '/'. WPDEV_CRM_PLUGIN_FILENAME ; ?>wpdev-crm-settings&tab=<?php echo $my_tab; ?>"><?php } ?><img class="menuicons" src="<?php echo WPDEV_CRM_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?><?php if ($slct_a == 'selected') { ?></span><?php } else { ?></a><?php } ?>
<?php /*
                                <?php $title = __('Advanced settings', 'wpdev-crm');  $my_icon = 'General-setting-64x64.png'; $my_tab = 'pro';  ?>
                                <?php if ($_GET['tab'] == $my_tab) { $slct_a = 'selected'; } else { $slct_a = ''; } ?>
                                <?php if ($slct_a == 'selected') { $selected_title = $title; $selected_icon = $my_icon;  ?><span class="nav-tab nav-tab-active"><?php } else { ?><a class="nav-tab" href="admin.php?page=<?php echo WPDEV_CRM_PLUGIN_DIRNAME . '/'. WPDEV_CRM_PLUGIN_FILENAME ; ?>wpdev-crm-settings&tab=<?php echo $my_tab; ?>"><?php } ?><img class="menuicons" src="<?php echo WPDEV_CRM_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?><?php if ($slct_a == 'selected') { ?></span><?php } else { ?></a><?php } ?>
 */ ?>
                                <?php if ( ($version == 'free')  ) { ?>
                                    <?php $title = __('Buy now', 'wpdev-crm');  $my_icon = 'shopping_trolley.png'; $my_tab = 'buy';  ?>
                                    <?php if ( ($_GET['tab'] == $my_tab)  ) { $slct_a = 'selected'; } else { $slct_a = ''; } ?>
                                    <?php if ($slct_a == 'selected') { $selected_title = $title; $selected_icon = $my_icon;  ?><span class="nav-tab nav-tab-active"><?php } else { ?><a class="nav-tab" href="admin.php?page=<?php echo WPDEV_CRM_PLUGIN_DIRNAME . '/'. WPDEV_CRM_PLUGIN_FILENAME ; ?>wpdev-crm-settings&tab=<?php echo $my_tab; ?>"><?php } ?><img class="menuicons" src="<?php echo WPDEV_CRM_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?><?php if ($slct_a == 'selected') { ?></span><?php } else { ?></a><?php } ?>
                                <?php } ?>
                        </div>
                    </div>
                 </div>
                <script type="text/javascript">
                                var val1 = '<img src="<?php echo WPDEV_CRM_PLUGIN_URL; ?>/img/<?php echo $selected_icon; ?>"><br />';
                                jQuery('div.wrap div.icon32').html(val1);
                                jQuery('div.crmpage h2').html( '<?php echo $selected_title . ' ' . __('settings'); ?>');
                </script><?php

           }

           // Switch between tabs content at settings page
           function show_settings_menu_content(){
                ?> <div id="poststuff" class="metabox-holder"> <?php
                    $version = 'free';
                    if (class_exists('wpdev_crm_pro'))  $version = 'pro';

                if   ( ! isset($_GET['tab']) )   $this->settings_general_content();


                switch ($_GET['tab']) {
                    case 'main':   $this->settings_general_content();  break;
                    case 'pro':    $this->settings_advanced_content(); break;
                    case 'buy':
                        if ( ($version == 'free')   ) {
                        ?>
                        <div class="clear" style="height:20px;"></div>
                        <div id="ajax_working"></div>
                        <div id="poststuff" class="metabox-holder">
                            <?php $this->showBuyWidow(); ?>
                        </div>
                        <?php } break;

                }
                ?> </div> <?php
           }

               // Show General content of settings
               function settings_general_content(){
                    $version = 'free';
                    if (class_exists('wpdev_crm_pro'))  $version = 'pro';

                    if ( isset( $_POST['Submit_general'] ) ) {

                            $is_delete_if_deactive =  $_POST['is_delete_if_deactive']; // check
                            $wpdev_copyright  = $_POST['wpdev_copyright'];             // check
                            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            if (isset( $is_delete_if_deactive ))            $is_delete_if_deactive = 'On';
                            else                                            $is_delete_if_deactive = 'Off';
                            update_option('wpdev_crm_is_delete_if_deactive' , $is_delete_if_deactive );

                            if (isset( $wpdev_copyright ))                  $wpdev_copyright = 'On';
                            else                                            $wpdev_copyright = 'Off';
                            update_option( 'wpdev_crm_copyright' , $wpdev_copyright );
                            ////////////////////////////////////////////////////////////////////////////////////////////////////////////

                            $user_role_orders      = $_POST['user_role_orders'];
                            $user_role_customers   = $_POST['user_role_customers'];
                            $user_role_settings    = $_POST['user_role_settings'];
                            if ( strpos($_SERVER['HTTP_HOST'],'onlinebookingcalendar.com') !== FALSE ) {
                                $user_role_orders      = 'subscriber';
                                $user_role_customers   = 'subscriber';
                                $user_role_settings     = 'subscriber';
                            }
                            update_option( 'wpdev_crm_user_role_orders', $user_role_orders );
                            update_option( 'wpdev_crm_user_role_customers', $user_role_customers );
                            update_option( 'wpdev_crm_user_role_settings', $user_role_settings );


                            update_option( 'wpdev_crm_rows_per_page_customers', $_POST['rows_per_page_customers'] );
                            update_option( 'wpdev_crm_rows_per_page_orders',    $_POST['rows_per_page_orders'] );

                    }
                    $is_delete_if_deactive  = get_option( 'wpdev_crm_is_delete_if_deactive' );
                    $wpdev_copyright        = get_option( 'wpdev_crm_copyright' );

                    $user_role_orders      = get_option( 'wpdev_crm_user_role_orders' );
                    $user_role_customers   = get_option( 'wpdev_crm_user_role_customers' );
                    $user_role_settings     = get_option( 'wpdev_crm_user_role_settings' );

                    $rows_per_page_customers = get_option( 'wpdev_crm_rows_per_page_customers' );
                    $rows_per_page_orders    = get_option( 'wpdev_crm_rows_per_page_orders' );

                     ?>
                    <div class="clear" style="height:20px;"></div>

                    <div  style="width:64%; float:left;margin-right:1%;">

                        <div class='meta-box'>
                            <div  class="postbox" > <h3 class='hndle'><span><?php _e('General Settings', 'wpdev-crm'); ?></span></h3>
                                <div class="inside">


                    <form  name="post_option" action="" method="post" id="post_option" >
                        <table class="form-table"><tbody>


                                <!--tr valign="top">
                                    <th scope="row"><label for="client_cal_count" ><?php _e('Some setttingsd', 'wpdev-crm'); ?>:</label><br><?php printf(__('at %sclient%s side view', 'wpdev-crm'),'<span style="color:#888;font-weight:bold;">','</span>'); ?></th>
                                    <td>
                                        <input id="client_cal_count" class="regular-text code" type="text" size="45" value="<?php echo $client_cal_count; ?>" name="client_cal_count"/>
                                        <span class="description"><?php printf(__('Type your %sdefault count of calendars%s for inserting into post/page', 'wpdev-crm'),'<b>','</b>');?></span>
                                    </td>
                                </tr-->

                                <tr valign="top">
                                    <th scope="row"><label for="rows_per_page_orders" ><?php _e('Rows per page', 'wpdev-crm'); ?>:</label><br><?php printf(__('at %sorders%s page', 'wpdev-crm'),'<span style="color:#888;font-weight:bold;">','</span>'); ?></th>
                                    <td>
                                        <select id="rows_per_page_orders" name="rows_per_page_orders">
                                            <?php $rows_per_page = array('10','25','50','75','100','all');
                                             foreach ($rows_per_page as $row_num) { ?>
                                                <option <?php if($rows_per_page_orders == $row_num) echo "selected"; ?> value="<?php echo $row_num; ?>" ><?php if ($row_num =='all') _e('All records at one page','wpdev-crm'); else  echo $row_num; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="description"><?php _e('Select number orders per page', 'wpdev-crm');?></span>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <th scope="row"><label for="rows_per_page_customers" ><?php _e('Rows per page', 'wpdev-crm'); ?>:</label><br><?php printf(__('at %scustomers%s page', 'wpdev-crm'),'<span style="color:#888;font-weight:bold;">','</span>'); ?></th>
                                    <td>
                                        <select id="rows_per_page_customers" name="rows_per_page_customers">
                                            <?php $rows_per_page = array('10','25','50','75','100');
                                             foreach ($rows_per_page as $row_num) { ?>
                                                <option <?php if($rows_per_page_customers == $row_num) echo "selected"; ?> value="<?php echo $row_num; ?>" ><?php echo $row_num; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="description"><?php _e('Select number customers per page', 'wpdev-crm');?></span>
                                    </td>
                                </tr>

 
                                <tr valign="top">
                                    <td colspan="2"><div style="width:100%;">
                                        <span style="color:#21759B;cursor: pointer;font-weight: bold;"
                                           onclick="javascript: jQuery('#togle_settings_useraccess').slideToggle('normal');"
                                           style="text-decoration: none;font-weight: bold;font-size: 11px;">
                                           + <span style="border-bottom:1px dashed #21759B;"><?php _e('Show settings of user access level to admin menu', 'wpdev-crm'); ?></span>
                                        </span>
                                    </div>

                                    <table id="togle_settings_useraccess" style="display:none;" class="hided_settings_table">
                                        <tr valign="top">

                                            <th scope="row"><label for="start_day_weeek" ><?php _e('Orders', 'wpdev-crm'); ?>:</label><br><?php _e('access level', 'wpdev-crm'); ?></th>
                                            <td>
                                                <select id="user_role_orders" name="user_role_orders">
                                                    <option <?php if($user_role_orders == 'subscriber') echo "selected"; ?> value="subscriber" ><?php echo translate_user_role('Subscriber'); ?></option>
                                                    <option <?php if($user_role_orders == 'administrator') echo "selected"; ?> value="administrator" ><?php echo translate_user_role('Administrator'); ?></option>
                                                    <option <?php if($user_role_orders == 'editor') echo "selected"; ?> value="editor" ><?php echo translate_user_role('Editor'); ?></option>
                                                    <option <?php if($user_role_orders == 'author') echo "selected"; ?> value="author" ><?php echo translate_user_role('Author'); ?></option>
                                                    <option <?php if($user_role_orders == 'contributor') echo "selected"; ?> value="contributor" ><?php echo translate_user_role('Contributor'); ?></option>
                                                </select>
                                                <span class="description"><?php _e('Select user access level for this administration page', 'wpdev-crm');?></span>
                                            </td>
                                        </tr>


                                        <tr valign="top">
                                            <th scope="row"><label for="start_day_weeek" ><?php _e('Customers', 'wpdev-crm'); ?>:</label><br><?php _e('access level', 'wpdev-crm'); ?></th>
                                            <td>
                                                <select id="user_role_customers" name="user_role_customers">
                                                    <option <?php if($user_role_customers == 'subscriber') echo "selected"; ?> value="subscriber" ><?php echo translate_user_role('Subscriber'); ?></option>
                                                    <option <?php if($user_role_customers == 'administrator') echo "selected"; ?> value="administrator" ><?php echo translate_user_role('Administrator'); ?></option>
                                                    <option <?php if($user_role_customers == 'editor') echo "selected"; ?> value="editor" ><?php echo translate_user_role('Editor'); ?></option>
                                                    <option <?php if($user_role_customers == 'author') echo "selected"; ?> value="author" ><?php echo translate_user_role('Author'); ?></option>
                                                    <option <?php if($user_role_customers == 'contributor') echo "selected"; ?> value="contributor" ><?php echo translate_user_role('Contributor'); ?></option>
                                                </select>
                                                <span class="description"><?php _e('Select user access level for this administration page', 'wpdev-crm');?></span>
                                            </td>
                                        </tr>


                                        <tr valign="top">
                                            <th scope="row"><label for="start_day_weeek" ><?php _e('Settings', 'wpdev-crm'); ?>:</label><br><?php _e('access level', 'wpdev-crm'); ?></th>
                                            <td>
                                                <select id="user_role_settings" name="user_role_settings">
                                                    <option <?php if($user_role_settings == 'subscriber') echo "selected"; ?> value="subscriber" ><?php echo translate_user_role('Subscriber'); ?></option>
                                                    <option <?php if($user_role_settings == 'administrator') echo "selected"; ?> value="administrator" ><?php echo translate_user_role('Administrator'); ?></option>
                                                    <option <?php if($user_role_settings == 'editor') echo "selected"; ?> value="editor" ><?php echo translate_user_role('Editor'); ?></option>
                                                    <option <?php if($user_role_settings == 'author') echo "selected"; ?> value="author" ><?php echo translate_user_role('Author'); ?></option>
                                                    <option <?php if($user_role_settings == 'contributor') echo "selected"; ?> value="contributor" ><?php echo translate_user_role('Contributor'); ?></option>
                                                </select>
                                                <span class="description"><?php _e('Select user access level for this administration page', 'wpdev-crm');?></span>
                                                <?php if ( strpos($_SERVER['HTTP_HOST'],'onlinebookingcalendar.com') !== FALSE ) { ?> <br/><span class="description" style="font-weight: bold;">You do not allow to change this items because right now you test DEMO</span> <?php } ?>
                                            </td>
                                        </tr>
                                    </table>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td colspan="2"><div style="border-bottom:1px solid #cccccc;"></div></td>
                                </tr>

                                <tr valign="top">
                                    <th scope="row"><label for="is_delete_if_deactive" ><?php _e('Delete data', 'wpdev-crm'); ?>:</label><br><?php _e('when plugin deactivated', 'wpdev-crm'); ?></th>
                                    <td><input id="is_delete_if_deactive" type="checkbox" <?php if ($is_delete_if_deactive == 'On') echo "checked"; ?>  value="<?php echo $is_delete_if_deactive; ?>" name="is_delete_if_deactive"/>
                                        <span class="description"><?php _e(' Check, if you want delete booking data during uninstalling plugin.', 'wpdev-crm');?></span>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <th scope="row"><label for="wpdev_copyright" ><?php _e('Copyright notice', 'wpdev-crm'); ?>:</label></th>
                                    <td><input id="wpdev_copyright" type="checkbox" <?php if ($wpdev_copyright == 'On') echo "checked"; ?>  value="<?php echo $wpdev_copyright; ?>" name="wpdev_copyright"/>
                                        <span class="description"><?php printf(__(' Turn On/Off copyright %s notice at footer of site view.', 'wpdev-crm'),'wpdevelop.com');?></span>
                                    </td>
                                </tr>

                            </tbody></table>

                        <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save Changes', 'wpdev-crm'); ?>" name="Submit_general"/>
                        <div class="clear" style="height:10px;"></div>
                    </form>

                                    <div style="clear:both;"></div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div style="width:35%; float:left;">

                        <?php if( $version == 'free' ) { ?>
                            <?php   $this->showBuyWidow();  ?>

                        <?php } ?>

                        <?php if ( false ) { ?>

                            <div class='meta-box'>
                                <div  class="postbox gdrgrid" > <h3 class='hndle'><span><?php _e('Category & Page Icons Professional version'); ?></span></h3>
                                    <div class="inside">
                                        <h2 style="margin:10px;"><?php _e('Category & Page Icons Pro'); ?> </h2>
                                        <p style="margin:0px;">
                                    <?php printf(__('If you want %sdonate or have more functionality%s you can %sBuy Professional version%s, which are include posibility to set icons at the top, bottom, right or left side according to titles of pages or categories. At this version is also possible to set spaces between titles and icons.'),'<strong>','</strong>','<strong>','</strong>'); ?> <br/>
                                        </p>
                                        <p style="text-align:center;padding:10px 0px;">
                                            <a href="http://wpdevelop.com/category-page-icons-purchase" class="button-primary" target="_blank">Donate</a>
                                            <a href="http://wpdevelop.com/category-page-icons-purchase" class="button-primary" target="_blank">Buy PRO version</a>
                                        </p>

                                    </div>
                                </div>
                            </div>

                            <div class='meta-box'>
                                <div  class="postbox gdrgrid" > <h3 class='hndle'><span><?php _e('Recomended WordPress Plugins'); ?></span></h3>
                                    <div class="inside">
                                        <h2 style="margin:10px;"><?php _e('Booking Calendar - online booking system'); ?> </h2>
                                        <img src="<?php echo WPDEV_C_PLUGIN_URL . '/img/calendar-48x48.png'; ?>" style="float:left; padding:0px 10px 10px 0px;">

                                        <p style="margin:0px;">
                                    <?php printf(__('This wordpress plugin is  %sadd booking service to your site%s. Your site visitors can make booking for one or several days of one or several properties (appartments, hotel rooms, cars and so on).  Its can be  interesting %sfor hotel reservation service, rental service or any other service%s, where is needed making reservation at specific dates.'),'<strong>','</strong>','<strong>','</strong>'); ?> <br/>
                                        </p>
                                        <p style="text-align:center;padding:10px 0px;">
                                            <a href="http://wordpress.org/extend/plugins/booking" class="button-primary" target="_blank">Download from wordpress</a>
                                            <a href="http://onlinebookingcalendar.com" class="button-primary" target="_blank">Demo site</a>
                                        </p>

                                    </div>
                                </div>
                            </div>

                        <?php } ?>

                            <div class='meta-box'>
                                <div  class="postbox gdrgrid" > <h3 class='hndle'><span><?php _e('Information'); ?></span></h3>
                                    <div class="inside">
                                        <p class="sub"><?php _e("Info"); ?></p>
                                        <div class="table">
                                            <table><tbody>
                                                    <tr class="first">
                                                        <td class="first b" style="width: 133px;"><?php _e("Version"); ?></td>
                                                        <td class="t"><?php _e("release date"); ?>: <?php echo date ("d.m.Y", filemtime(__FILE__)); ?></td>
                                                        <td class="b options" style="color: red; font-weight: bold;"><?php echo WPDEV_CRM_VERSION; ?></td>
                                                    </tr>
                                                </tbody></table>
                                        </div>
                                        <p class="sub"><?php _e("Links"); ?></p>
                                        <div class="table">
                                            <table><tbody>
                                                    <tr class="first">
                                                        <td class="first b">Plugin page</td>
                                                        <td class="t"><?php _e("official plugin page"); ?></td>
                                                        <td class="t options"><a href="http://wpdevelop.com/wp-plugins/" target="_blank"><?php _e("visit"); ?></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="first b">WordPress Extend</td>
                                                        <td class="t"><?php _e("wordpress plugin page"); ?></td>
                                                        <td class="t options"><a href="http://wordpress.org/extend/plugins" target="_blank"><?php _e("visit"); ?></a></td>
                                                    </tr>
                                                </tbody></table>
                                        </div>
                                        <p class="sub"><?php _e("Author"); ?></p>
                                        <div class="table">
                                            <table><tbody>
                                                    <tr class="first">
                                                        <td class="first b"><span><?php _e("Premium Support"); ?></span></td>
                                                        <td class="t"><?php _e("special plugin customizations"); ?></td>
                                                        <td class="t options"><a href="mailto:info@wpdevelop.com" target="_blank"><?php _e("contact"); ?></a></td>
                                                    </tr>
                                                </tbody></table>
                                        </div>

                                    </div>
                                </div>
                            </div>

                    </div>

                <?php
               }

               // Show Advanced settings content
               function settings_advanced_content(){
                     ?>
                    <div class="clear" style="height:20px;"></div>

                    <div  style="width:99%; ">

                        <div class='meta-box'>
                            <div  class="postbox" > <h3 class='hndle'><span><?php _e('Advanced Settings', 'wpdev-crm'); ?></span></h3>
                                <div class="inside">
                                <?php //$this->show_help(true); ?>
                                    <div style="clear:both;"></div>
                                    <div style="margin:0px 5px auto;text-align:center;">
                                    <!--img style="margin:0px auto;" src="<?php echo WPDEV_CRM_PLUGIN_URL; ?>/img/help/range_selection_settings.png" -->
                                        <img style="margin:0px 0px 20px auto;" src="<?php echo WPDEV_CRM_PLUGIN_URL; ?>/img/help/range_selection_wide.png" >
                                    </div>
                                    <div style="clear:both;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php

               }


                // Show window for purchase
                function showBuyWidow(){
                    ?>
                        <div class='meta-box'>
                            <div  class="postbox gdrgrid" > <h3 class='hndle'><span><?php _e('Professional version', 'wpdev-crm'); ?></span></h3>
                                <div class="inside">
                                    <p class="sub"><?php _e("Main difference features", 'wpdev-crm'); ?></p>
                                    <div class="table">
                                        <table><tbody>
                                                <tr class="first">
                                                    <td class="first b" style="width: 53px;"><b><?php _e("Features", 'wpdev-crm'); ?></b></td>
                                                    <td class="t"></td>
                                                    <td class="b options comparision" style="color:#11cc11;"><b><?php _e("Free", 'wpdev-crm'); ?></b></td>
                                                    <td class="b options comparision" style="color:#f71;"><b><?php _e("Paid", 'wpdev-crm'); ?></b></td>
                                                </tr>
                                                <tr class="">
                                                    <td class="first b" ><?php _e("Functionality", 'wpdev-crm'); ?></td>
                                                    <td class="t i">(<?php _e("all existing functionality", 'wpdev-crm'); ?>)</td>
                                                    <td class="b options comparision" style="text-align:center;color: green; font-weight: bold;">&bull;</td>
                                                    <td class="b options comparision" style="text-align:center;color: green; font-weight: bold;">&bull;</td>
                                                </tr>
                                                <tr class="">
                                                    <td class="first b" ><?php _e("Export", 'wpdev-crm'); ?></td>
                                                    <td class="t i">(<?php _e("export to CSV", 'wpdev-crm'); ?>)</td>
                                                    <td class="b options comparision" style="text-align:center;color: green; font-weight: bold;"></td>
                                                    <td class="b options comparision" style="text-align:center;color: green; font-weight: bold;">&bull;</td>
                                                </tr>
                                                <tr class="">
                                                    <td class="first b"><?php _e("Print", 'wpdev-crm'); ?></td>
                                                    <td class="t i">(<?php _e("Print loyout", 'wpdev-crm'); ?>)</td>
                                                    <td class="b options comparision" style="text-align:center;color: green; font-weight: bold;"></td>
                                                    <td class="b options comparision" style="text-align:center;color: green; font-weight: bold;">&bull;</td>
                                                </tr>
                                                <tr class="">
                                                    <td class="first b"><?php _e("Filter", 'wpdev-crm'); ?></td>
                                                    <td class="t i">(<?php _e("advanced filter for orders", 'wpdev-crm'); ?>)</td>
                                                    <td class="b options comparision" style="text-align:center;color: green; font-weight: bold;"></td>
                                                    <td class="b options comparision" style="text-align:center;color: green; font-weight: bold;">&bull;</td>
                                                </tr>
                                                <tr>
                                                    <td class="first b" ><?php _e("Cost", 'wpdev-crm'); ?></td>
                                                    <td class="t"><?php  ?></td>
                                                    <td class="t options comparision" style="border-right:1px solid #ddd;"><?php _e("free", 'wpdev-crm'); ?></td>
                                                    <td class="t options comparision"><?php //_e("start", 'wpdev-crm'); ?>$119</td>
                                                </tr>
                                                <tr class="last">
                                                    <td class="first b" ><?php _e("Live demo", 'wpdev-crm'); ?></td>
                                                    <td class="t" colspan="3"> <a href="http://ordersplugin.onlinebookingcalendar.com/wp-admin/" target="_blank"> Professional </a>
                                                    </td>
                                                </tr>
                                                <tr class="last">
                                                    <!--td class="first b"><span><?php _e("Purchase", 'wpdev-crm'); ?></span></td-->
                                                    <td colspan="4" style="text-align:right;font-size: 18px;font-weight:normal;" class="t comparision"> <a class="buttonlinktext"  href="http://wpdevelop.com/booking-manager-professional/" target="_blank"><?php _e("Buy now", 'wpdev-crm'); ?></a> </td>
                                                </tr>

                                            </tbody></table>
                                    </div>
                                </div>
                            </div>
                        </div>


                    <?php
                }



    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // S U P P O R T    F U N C T I O N S
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Check if table exist
        function is_table_exists( $tablename ) {
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

        // Check if table exist
        function is_field_in_table_exists( $tablename , $fieldname) {
            global $wpdb;
            if (strpos($tablename, $wpdb->prefix) ===false) $tablename = $wpdb->prefix . $tablename ;
            $sql_check_table = "SHOW COLUMNS FROM " . $tablename ;

            $res = $wpdb->get_results($sql_check_table);

            foreach ($res as $fld) {
                if ($fld->Field == $fieldname) return 1;
            }

            return 0;

        }

        // Adds Settings link to plugins settings
        function plugin_links($links, $file) {

            $this_plugin = plugin_basename(__FILE__);

            if ($file == $this_plugin) {
                $settings_link = '<a href="admin.php?page=' . WPDEV_CRM_PLUGIN_DIRNAME . '/'. WPDEV_CRM_PLUGIN_FILENAME . 'wpdev-crm-settings">'.__("Settings", 'wpdev-crm').'</a>';
                $settings_link2 = '<a href="http://wpdevelop.com/booking-manager-professional/">'.__("Buy", 'wpdev-crm').' Pro</a>';
                array_unshift($links, $settings_link2);
                array_unshift($links, $settings_link);
            }
            return $links;
        }

 
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // J a v a S c r i p t
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
       // Files and CSS - print only at the admin pages of this plugin
       function on_add_admin_js_files(){
            add_action('admin_head', array(&$this, 'print_JS_CSS' ), 1);
       }

       // Direct Write JS and CSS  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
       function print_JS_CSS(){
           ?> <!-- CRM JS & CSS files - Start -->  <?php
           ?> <link href="<?php echo WPDEV_CRM_PLUGIN_URL; ?>/css/admin.css" rel="stylesheet" type="text/css" />  <?php
           ?> <!-- CRM JS & CSS files - End -->  <?php
           ?><script type="text/javascript">
             if(jWPDev == undefined)
                var jWPDev = jQuery.noConflict();
              var wpdev_crm_plugin_url = '<?php echo WPDEV_CRM_PLUGIN_URL; ?>';
              var wpdev_crm_plugin_filename = '<?php echo WPDEV_CRM_PLUGIN_FILENAME; ?>';
           </script><?php
           do_action('wpdev_crm_js_define_variables');
           ?> <script type="text/javascript" src="<?php echo WPDEV_CRM_PLUGIN_URL; ?>/js/wpdev.stnd.js"></script>  <?php
           do_action('wpdev_crm_js_write_files');

       }





    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///   A C T I V A T I O N   A N D   D E A C T I V A T I O N    O F   T H I S   P L U G I N  ///////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
         function setDefaultInitialValues($evry_one = 1) { global $wpdb;
                    $names = array(  'Jacob', 'Michael', 'Daniel', 'Anthony', 'William', 'Emma', 'Sophia', 'Kamila', 'Isabella', 'Jack', 'Daniel', 'Matthew',
                                     'Olivia', 'Emily', 'Grace', 'Jessica', 'Joshua', 'Harry', 'Thomas', 'Oliver', 'Jack' );
                    $second_names = array(  'Smith', 'Johnson', 'Widams', 'Brown', 'Jones', 'Miller', 'Davis', 'Garcia', 'Rodriguez', 'Wilyson', 'Gonzalez', 'Gomez',
                                            'Taylor', 'Bron', 'Wilson', 'Davies', 'Robinson', 'Evans', 'Walker', 'Jackson', 'Clarke' );
                    $city =    array(       'New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'San Antonio', 'San Diego', 'San Jose', 'Detroit',
                                            'San Francisco', 'Jacksonville', 'Austin',
                                            'London', 'Birmingham', 'Leeds', 'Glasgow', 'Sheffield', 'Bradford', 'Edinburgh', 'Liverpool', 'Manchester' );
                    $adress =   array(      '30 Mortensen Avenue', '144 Hitchcock Rd', '222 Lincoln Ave', '200 Lincoln Ave', '65 West Alisal St',
                                            '426 Work St', '65 West Alisal Street', '159 Main St', '305 Jonoton Avenue', '423 Caiptown Rd', '34 Linoro Ave',
                                            '50 Voro Ave', '15 East St', '226 Middle St', '35 West Town Street', '59 Other St', '50 Merci Ave', '15 Dolof St',
                                            '226 Gordon St', '35 Sero Street', '59 Exit St' );
                    $country = array( 'US' ,'US' ,'US' ,'US' ,'US' ,'US' ,'US' ,'US' ,'US' ,'US' ,'US' ,'US' ,'UK','UK','UK','UK','UK','UK','UK','UK','UK' );
                    $info = array(    '  ' ,'  ' ,'  ' ,'  ' ,'  ' ,'  ' ,'  ' ,'  ' ,'  ' ,'  ' ,'  ' ,'  ' ,'  ','  ','  ','  ','  ','  ','  ','  ','  ' );

                    for ($i = 0; $i < count($names); $i++) {
                            if ( ($i % $evry_one) !==0 ) { continue; }
                            $bk_type = rand(1,4);
                            $form = 'text^starttime'.$bk_type.'^0'.rand(0,9).':'.rand(10,59).'~'.
                             'text^endtime'.$bk_type.'^'.rand(13,23).':'.rand(10,59).'~'.
                             'text^name'.$bk_type.'^'.$names[$i].'~'.
                             'text^secondname'.$bk_type.'^'.$second_names[$i].'~'.
                             'text^email'.$bk_type.'^'.$second_names[$i].'.example@wpdevelop.com~'.
                             'text^address'.$bk_type.'^'.$adress[$i].'~'.
                             'text^city'.$bk_type.'^'.$city[$i].'~'.
                             'text^postcode'.$bk_type.'^'.rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).'~'.
                             'text^country'.$bk_type.'^'.$country[$i].'~'.
                             'text^phone'.$bk_type.'^'.rand(0,9).rand(0,9).rand(0,9).'-'.rand(0,9).rand(0,9).'-'.rand(0,9).rand(0,9).'~'.
                             'select-one^visitors'.$bk_type.'^'.rand(0,9).'~'.
                             'checkbox^children'.$bk_type.'[]^false~'.
                             'textarea^details'.$bk_type.'^'.$info[$i];

                             $wp_bk_querie = "INSERT INTO ".$wpdb->prefix ."booking ( form, booking_type, cost ) VALUES
                                                   ( '".$form."', ".$bk_type .", ".rand(0,1000)." ) ;";
                             $wpdb->query($wp_bk_querie);

                             $temp_id = $wpdb->insert_id;
                             $wp_queries_sub = "INSERT INTO ".$wpdb->prefix ."bookingdates (
                                     booking_id,
                                     booking_date
                                    ) VALUES
                                    ( ". $temp_id .", CURDATE()+ INTERVAL ".(2*$i*$evry_one+2)." day ),
                                    ( ". $temp_id .", CURDATE()+ INTERVAL ".(2*$i*$evry_one+3)." day ),
                                    ( ". $temp_id .", CURDATE()+ INTERVAL ".(2*$i*$evry_one+4)." day );";
                             $wpdb->query($wp_queries_sub);
                    }
         }



         // Activate
         function wpdev_crm_activate(){


            global $wpdb;
            $charset_collate = '';
            if ( $wpdb->has_cap( 'collation' ) ) {
                if ( ! empty($wpdb->charset) ) $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
                if ( ! empty($wpdb->collate) ) $charset_collate .= " COLLATE $wpdb->collate";
            }

            $wp_queries = array();
            if ( ! $this->is_table_exists('wpdev_crm_orders') ) { // Cehck if tables not exist yet
                $wp_queries[] = "CREATE TABLE ".$wpdb->prefix ."wpdev_crm_orders (
                         order_id bigint(20) unsigned NOT NULL auto_increment,
                         order_info text NOT NULL default '',
                         customer_id bigint(10) NOT NULL default 1,
                         type text NOT NULL default '',
                         cost FLOAT(7,2) NOT NULL DEFAULT 0.00,
                         tags bigint(10) NOT NULL default 1,
                         order_date datetime NOT NULL default '0000-00-00 00:00:00',
                         remark TEXT NOT NULL DEFAULT '',
                         internal_id bigint(10) NOT NULL default 0,
                         internal_filters text NOT NULL default '',
                         internal_filters1 text NOT NULL default '',
                         internal_filters2 datetime NOT NULL default '0000-00-00 00:00:00',
                         internal_filters3 datetime NOT NULL default '0000-00-00 00:00:00',
                         PRIMARY KEY  (order_id)
                        ) $charset_collate;";
            }
            
            if ( ! $this->is_table_exists('wpdev_crm_customers') ) { // Cehck if tables not exist yet
                $wp_queries[] = "CREATE TABLE ".$wpdb->prefix ."wpdev_crm_customers (
                         customer_id bigint(20) unsigned NOT NULL auto_increment,
                         name varchar(255) NOT NULL default '',
                         second_name varchar(255) NOT NULL default '',
                         email varchar(255) NOT NULL default '',
                         phone varchar(255) NOT NULL default '',
                         adress text NOT NULL default '',
                         city varchar(255) NOT NULL default '',
                         country varchar(255) NOT NULL default '',
                         info text NOT NULL default '',
                         comments text NOT NULL default '',
                         tags bigint(10) NOT NULL default 1,
                         customer_date datetime NOT NULL default '0000-00-00 00:00:00',
                         remark TEXT NOT NULL DEFAULT '',
                         PRIMARY KEY  (customer_id)
                        ) $charset_collate;";
            }

            if (count($wp_queries)>0)   // Execute all SQL
                foreach ($wp_queries as $wp_q) $wpdb->query($wp_q);




            if ( strpos($_SERVER['SCRIPT_FILENAME'],'ordersplugin.onlinebookingcalendar.com') !== FALSE ) {
                    $this->setDefaultInitialValues();
                    $this->setDefaultInitialValues(3);
                    $this->setDefaultInitialValues(9);
                    wpdev_import_booking_crm_ajax(true);
            }


            add_option( 'wpdev_crm_is_delete_if_deactive' , 'Off' );

            add_option( 'wpdev_crm_copyright' , 'Off' );

            add_option( 'wpdev_crm_user_role_orders', 'subscriber' );
            add_option( 'wpdev_crm_user_role_customers', 'subscriber' );
            add_option( 'wpdev_crm_user_role_settings', 'subscriber' );

            add_option( 'wpdev_crm_rows_per_page_customers', '25' );
            add_option( 'wpdev_crm_rows_per_page_orders', '25' );

            make_crm_action('wpdev_crm_activation');
         }

         // Deactivate
         function wpdev_crm_deactivate(){
            $is_delete_if_deactive =  get_option( 'wpdev_crm_is_delete_if_deactive' ); // check

            if ($is_delete_if_deactive == 'On') {

                delete_option( 'wpdev_crm_is_delete_if_deactive');
                delete_option( 'wpdev_crm_copyright' );

                delete_option( 'wpdev_crm_user_role_orders' );
                delete_option( 'wpdev_crm_user_role_customers' );
                delete_option( 'wpdev_crm_user_role_settings' );

                delete_option( 'wpdev_crm_rows_per_page_customers' );
                delete_option( 'wpdev_crm_rows_per_page_orders' );


                global $wpdb;
                $wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'wpdev_crm_orders');
                $wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'wpdev_crm_customers');
                
                make_crm_action('wpdev_crm_deactivation');
            }

         }

    } 
}

$wpdev_crm = new wpdev_crm();



// Ajax response ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function wpdev_crm_ajax_responder() {  global $wpdb;
    if (!function_exists ('adebug')) { function adebug() { $var = func_get_args(); echo "<div style='text-align:left;background:#ffffff;border: 1px dashed #ff9933;font-size:11px;line-height:15px;font-family:'Lucida Grande',Verdana,Arial,'Bitstream Vera Sans',sans-serif;'><pre>"; print_r ( $var ); echo "</pre></div>"; } }

    $action = $_POST['ajax_action_crm'];

    switch ( $action ) :

        case  'IMPORT_BOOKINGS':
            if (function_exists ('wpdev_import_booking_crm_ajax')) wpdev_import_booking_crm_ajax();
            die();
        case 'EXPORT_BOOKINGS'    :
            if (function_exists ('wpdev_export_booking_crm_ajax')) wpdev_export_booking_crm_ajax();
            die();
        case 'EXPORT_ALL_BOOKINGS'    :
            if (function_exists ('wpdev_export_booking_all_crm_ajax')) wpdev_export_booking_all_crm_ajax();
            die();
        default:
            if (function_exists ('wpdev_pro_crm_ajax')) wpdev_pro_crm_ajax();
            die();

    endswitch;
}


?>
