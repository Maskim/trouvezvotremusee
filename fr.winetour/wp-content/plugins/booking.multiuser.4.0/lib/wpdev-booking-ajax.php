<?php

if (  (! isset( $_GET['merchant_return_link'] ) ) && (! isset( $_GET['payed_booking'] ) ) && (!function_exists ('get_option')  )  ) { die('You do not have permission to direct access to this file !!!'); }


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Getting Ajax requests
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ( isset( $_POST['ajax_action'] ) ) {
    define('DOING_AJAX', true);


    if ( class_exists('wpdev_bk_personal')) { $wpdev_bk_personal_in_ajax = new wpdev_bk_personal(); }

    wpdev_bk_ajax_responder();
}


if ( ( isset( $_GET['payed_booking'] ) )  || (  isset( $_GET['merchant_return_link'])) ) {

    if ( class_exists('wpdev_bk_personal'))                 { $wpdev_bk_personal_in_ajax = new wpdev_bk_personal(); }

    if (function_exists ('wpdev_bk_update_pay_status')) wpdev_bk_update_pay_status();
    die;
} /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//    A J A X     R e s p o n d e r     Real Ajax with jQuery sender     ///////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function wpdev_bk_ajax_responder() {

    global $wpdb;
    $action = $_POST['ajax_action'];

    if  (isset($_POST['wpdev_active_locale'])) {    // Reload locale according request parameter
            global  $l10n;
            if (isset($l10n['wpdev-booking'])) unset($l10n['wpdev-booking']);

            if(! defined('WPDEV_BK_LOCALE_RELOAD') ) define('WPDEV_BK_LOCALE_RELOAD', $_POST['wpdev_active_locale']);
          
            loadLocale(WPDEV_BK_LOCALE_RELOAD);
    }

    switch ( $action ) :

        case  'INSERT_INTO_TABLE':
            wpdev_bk_insert_new_booking();
            die();
            break;

        case 'UPDATE_READ_UNREAD':

            make_bk_action('check_multiuser_params_for_client_side_by_user_id', $_POST['user_id'] );

            $is_read_or_unread = $_POST[ "is_read_or_unread" ];
            if ($is_read_or_unread == 1)   $is_new = '1';
            else                           $is_new = '0';

            $id_of_new_bookings       = $_POST[ "booking_id" ];
            $arrayof_bookings_id    = explode('|',$id_of_new_bookings);

            renew_NumOfNewBookings(  $arrayof_bookings_id, $is_new  );


            ?>  <script type="text/javascript">
                    <?php foreach ($arrayof_bookings_id as $bk_id) {
                            if ($is_new == '1') { ?>
                                set_booking_row_read(<?php echo $bk_id ?>);
                            <?php } else { ?>
                                set_booking_row_unread(<?php echo $bk_id ?>);
                            <?php }?>
                    <?php } ?>
                    <?php if ($is_new == '1') { ?>
                    //    var my_num = parseInt(jQuery('.bk-update-count').text()) + parseInt(1<?php echo '*' . count($arrayof_bookings_id); ?>);
                    <?php } else { ?>
                    //    var my_num = parseInt(jQuery('.bk-update-count').text()) - parseInt(1<?php echo '*' . count($arrayof_bookings_id); ?>);
                    <?php } ?>
                    //jQuery('.bk-update-count').html( my_num );
                    document.getElementById('ajax_message').innerHTML = '<?php if ($is_new == '1') { echo __('Set as Read', 'wpdev-booking'); } else { echo __('Set as Unread', 'wpdev-booking'); } ?>';
                    jQuery('#ajax_message').fadeOut(1000);
                </script> <?php
            die();

            break;

        case 'UPDATE_APPROVE' :

            make_bk_action('check_multiuser_params_for_client_side_by_user_id', $_POST['user_id'] );

            // Approve or Unapprove
            $is_approve_or_pending = $_POST[ "is_approve_or_pending" ];
            if ($is_approve_or_pending == 1)   $is_approve_or_pending = '1';
            else                        $is_approve_or_pending = '0';
            // Booking ID
            $booking_id       = $_POST[ "booking_id" ];
            $approved_id    = explode('|',$booking_id);
            $denyreason     = $_POST["denyreason"];
            $is_send_emeils = $_POST["is_send_emeils"];
            

            if ( (count($approved_id)>0) && ($approved_id !==false)) {

                $approved_id_str = join( ',', $approved_id);

                if ( false === $wpdb->query( $wpdb->prepare("UPDATE ".$wpdb->prefix ."bookingdates SET approved = '".$is_approve_or_pending."' WHERE booking_id IN ($approved_id_str)") ) ){
                    ?> <script type="text/javascript"> document.getElementById('ajax_message').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during updating to DB' ,__FILE__,__LINE__); ?></div>'; </script> <?php
                    die();
                }

                if ($is_approve_or_pending == '1')
                    sendApproveEmails($approved_id_str, $is_send_emeils);
                else
                    sendDeclineEmails($approved_id_str, $is_send_emeils,$denyreason);

                ?>  <script type="text/javascript">
                        <?php foreach ($approved_id as $bk_id) {
                                if ($is_approve_or_pending == '1') { ?>
                                    set_booking_row_approved(<?php echo $bk_id ?>);
                                <?php } else { ?>
                                    set_booking_row_pending(<?php echo $bk_id ?>);
                                <?php }?>
                        <?php } ?>
                        document.getElementById('ajax_message').innerHTML = '<?php if ($is_approve_or_pending == '1') { echo __('Set as Approved', 'wpdev-booking'); } else { echo __('Set as Pending', 'wpdev-booking'); } ?>';
                        jQuery('#ajax_message').fadeOut(1000);
                    </script> <?php
                die();
            }
            break;

        case 'DELETE_APPROVE' :
            make_bk_action('check_multiuser_params_for_client_side_by_user_id', $_POST['user_id'] );

            $booking_id       = $_POST[ "booking_id" ];         // Booking ID
            $denyreason     = $_POST["denyreason"];
            if ( ( $denyreason == __('Reason of cancellation here', 'wpdev-booking')) || ( $denyreason == 'Reason of cancel here') )  $denyreason = '';
            $is_send_emeils = $_POST["is_send_emeils"];
            $approved_id    = explode('|',$booking_id);

            if ( (count($approved_id)>0) && ($approved_id !=false) && ($approved_id !='')) {

                $approved_id_str = join( ',', $approved_id);

                sendDeclineEmails($approved_id_str, $is_send_emeils,$denyreason);


                if ( false === $wpdb->query( $wpdb->prepare("DELETE FROM ".$wpdb->prefix ."bookingdates WHERE booking_id IN ($approved_id_str)") ) ){
                    ?> <script type="text/javascript"> document.getElementById('ajax_message').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during deleting dates at DB' ,__FILE__,__LINE__); ?></div>'; </script> <?php
                    die();
                }

                if ( false === $wpdb->query($wpdb->prepare( "DELETE FROM ".$wpdb->prefix ."booking WHERE booking_id IN ($approved_id_str)") ) ){
                    ?> <script type="text/javascript"> document.getElementById('ajax_message').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during deleting reservation at DB',__FILE__,__LINE__ ); ?></div>'; </script> <?php
                    die();
                }

                ?>
                    <script type="text/javascript">
                        <?php foreach ($approved_id as $bk_id) { ?>
                                    set_booking_row_deleted(<?php echo $bk_id ?>);
                        <?php } ?>
                        document.getElementById('ajax_message').innerHTML = '<?php echo __('Deleted', 'wpdev-booking'); ?>';
                        jQuery('#ajax_message').fadeOut(1000);
                    </script>
                <?php
                die();
            }
            break;

        case 'DELETE_BY_VISITOR':
            make_bk_action('wpdev_delete_booking_by_visitor');
            break;

        case 'SAVE_BK_COST':
            make_bk_action('wpdev_save_bk_cost');
            break;

        case 'SEND_PAYMENT_REQUEST':
            make_bk_action('wpdev_send_payment_request');
            break;


        case 'CHANGE_PAYMENT_STATUS':
            make_bk_action('wpdev_change_payment_status');
            break;

        case 'UPDATE_BK_RESOURCE_4_BOOKING':
            make_bk_action('wpdev_updating_bk_resource_of_booking');
            break;


        case 'UPDATE_REMARK':
            make_bk_action('wpdev_updating_remark');
            break;

        case 'DELETE_BK_FORM':
            make_bk_action('wpdev_delete_booking_form');
            break;

        case 'USER_SAVE_OPTION':

            if ($_POST['option'] == 'ADMIN_CALENDAR_COUNT') {
                update_user_option($_POST['user_id'],'booking_admin_calendar_count',$_POST['count']);
            }
            ?> <script type="text/javascript">
                    document.getElementById('ajax_message').innerHTML = '<?php echo __('Done', 'wpdev-booking'); ?>';
                    jQuery('#ajax_message').fadeOut(1000);
                    <?php if ( $_POST['is_reload'] == 1 ) { ?> location.reload(true); <?php  } ?>
                </script> <?php
            die();
            break;

        case 'USER_SAVE_WINDOW_STATE':
            update_user_option($_POST['user_id'],'booking_win_' . $_POST['window'] ,$_POST['is_closed']);
            die();
            break;

        case 'CALCULATE_THE_COST':
            make_bk_action('wpdev_ajax_show_cost');
            die();
            break;

        case 'BOOKING_SEARCH':
            make_bk_action('wpdev_ajax_booking_search');
            die();
            break;

        case 'CHECK_BK_NEWS':
            wpdev_ajax_check_bk_news();
            die();
            break;
        case 'CHECK_BK_VERSION':
            wpdev_ajax_check_bk_version();
            die();
            break;

        case 'SAVE_BK_LISTING_FILTER':
            make_bk_action('wpdev_ajax_save_bk_listing_filter');
            die();
            break;
        case 'EXPORT_BOOKINGS_TO_CSV'    :
            make_bk_action('wpdev_ajax_export_bookings_to_csv');
            die();

        default:
            if (function_exists ('wpdev_pro_bk_ajax')) wpdev_pro_bk_ajax();
            die();

        endswitch;
}





function wpdev_bk_insert_new_booking(){


            make_bk_action('check_multiuser_params_for_client_side', $_POST[  "bktype" ] );

            global $wpdb;
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // Define init variables
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $dates          = $_POST[ "dates" ];

            $bktype         = $_POST[ "bktype" ];
            $formdata       = $_POST[ "form" ];

            if(isset($_POST['numcommande']))
                $numCommande = $_POST['numcommande'];
            else
                $numCommande = "";

            if(isset($_POST['visitor']))
                $visitor = $_POST['visitor'];
            else
                $visitor = "";

            $formdata = escape_any_xss($formdata);
            $is_send_emeils = 1;        if (isset($_POST["is_send_emeils"])) $is_send_emeils = $_POST["is_send_emeils"];

            $my_booking_id  = 0;
            $my_booking_hash= '';

            if (isset($_POST['my_booking_hash'])) {
                $my_booking_hash = $_POST['my_booking_hash'];
                if ($my_booking_hash!='') {
                    $my_booking_id_type = false;
                    $my_booking_id_type = apply_bk_filter('wpdev_booking_get_hash_to_id',false, $my_booking_hash);
                    if ($my_booking_id_type !== false) {
                        $my_booking_id = $my_booking_id_type[0];
                        $bktype        = $my_booking_id_type[1];
                    }
                }
            }



            if (strpos($dates,' - ')!== FALSE) {
                $dates =explode(' - ', $dates );
                $dates = createDateRangeArray($dates[0],$dates[1]);
            }

            ///  CAPTCHA CHECKING   //////////////////////////////////////////////////////////////////////////////////////
            $the_answer_from_respondent = $_POST['captcha_user_input'];
            $prefix = $_POST['captcha_chalange'];
            if (! ( ($the_answer_from_respondent == '') && ($prefix == '') )) {
                $captcha_instance = new wpdevReallySimpleCaptcha();
                $correct = $captcha_instance->check($prefix, $the_answer_from_respondent);

                if (! $correct) {
                    $word = $captcha_instance->generate_random_word();
                    $prefix = mt_rand();
                    $captcha_instance->generate_image($prefix, $word);

                    $filename = $prefix . '.png';
                    $captcha_url = WPDEV_BK_PLUGIN_URL . '/js/captcha/tmp/' .$filename;
                    $ref = substr($filename, 0, strrpos($filename, '.'));
                    ?> <script type="text/javascript">
                        document.getElementById('captcha_input<?php echo $bktype; ?>').value = '';
                        // chnage img
                        document.getElementById('captcha_img<?php echo $bktype; ?>').src = '<?php echo $captcha_url; ?>';
                        document.getElementById('wpdev_captcha_challenge_<?php echo $bktype; ?>').value = '<?php echo $ref; ?>';
                        document.getElementById('captcha_msg<?php echo $bktype; ?>').innerHTML =
                            '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php echo __('Your entered code is incorrect', 'wpdev-booking'); ?></div>';
                        document.getElementById('submiting<?php echo $bktype; ?>').innerHTML ='';
                        jQuery('#captcha_input<?php echo $bktype; ?>')
                        .fadeOut( 350 ).fadeIn( 300 )
                        .fadeOut( 350 ).fadeIn( 400 )
                        .animate( {opacity: 1}, 4000 )
                        ;  // mark red border
                        jQuery(".wpdev-help-message div")
                        .css( {'color' : 'red'} )
                        .animate( {opacity: 1}, 10000 )
                        .fadeOut( 2000 );   // hide message
                        document.getElementById('captcha_input<?php echo $bktype; ?>').focus();    // make focus to elemnt

                    </script> <?php
                    die();
                }
            }//////////////////////////////////////////////////////////////////////////////////////////////////////////

            $booking_form_show = get_form_content ($formdata, $bktype);
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




            $my_modification_date = "'" . date_i18n( 'Y-m-d H:i:s'  ) ."'" ;    // Localize booking modification date
            $my_modification_date = 'NOW()';                                    // Server value modification date

            if ($my_booking_id>0) {                  // Edit exist booking

                if ( strpos($_SERVER['HTTP_REFERER'],'wp-admin/admin.php?') !==false ) {
                    ?> <script type="text/javascript">
                        document.getElementById('ajax_working').innerHTML =
                            '<div class="info_message ajax_message" id="ajax_message">\n\
                                            <div style="float:left;"><?php echo __('Updating...', 'wpdev-booking'); ?></div> \n\
                                            <div  style="float:left;width:80px;margin-top:-3px;">\n\
                                                   <img src="'+wpdev_bk_plugin_url+'/img/ajax-loader.gif">\n\
                                            </div>\n\
                                        </div>';
                    </script> <?php
                }
                $update_sql = "UPDATE ".$wpdb->prefix ."booking AS bk SET bk.form='$formdata', bk.booking_type=$bktype , bk.modification_date=".$my_modification_date." WHERE bk.booking_id=$my_booking_id;";
                if ( false === $wpdb->query($wpdb->prepare( $update_sql ) ) ){
                    ?> <script type="text/javascript"> document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during updating exist booking in BD',__FILE__,__LINE__); ?></div>'; </script> <?php
                    die();
                }

                // Check if dates already aproved or no
                $slct_sql = "SELECT approved FROM ".$wpdb->prefix ."bookingdates WHERE booking_id IN ($my_booking_id) LIMIT 0,1";
                $slct_sql_results  = $wpdb->get_results( $wpdb->prepare($slct_sql) );
                if ( count($slct_sql_results) > 0 ) {
                    $is_approved_dates = $slct_sql_results[0]->approved;
                }

                $delete_sql = "DELETE FROM ".$wpdb->prefix ."bookingdates WHERE booking_id IN ($my_booking_id)";
                if ( false === $wpdb->query($wpdb->prepare( $delete_sql ) ) ){
                    ?> <script type="text/javascript"> document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during updating exist booking for deleting dates in BD' ,__FILE__,__LINE__); ?></div>'; </script> <?php
                    die();
                }
                $booking_id = (int) $my_booking_id;       //Get ID  of reservation

            } else {                                // Add new booking

                $coupon_exist = false;
                $id_coupon = 0;

                if(isset($_POST['coupon']) && !empty($_POST['coupon']) && $_POST['coupon'] != ''){
                    $coupon = $_POST['coupon'];

                    if($coupon != "NULL" && $coupon != ""){
                        $sql_select_coupon = "SELECT coupon_id FROM ". $wpdb->prefix . "booking_coupons WHERE coupon_code = '".$coupon."'";
                        $coupon_results = $wpdb->get_results( $wpdb->prepare($sql_select_coupon) );
                        if(count($coupon_results) != 0){
                            $id_coupon = $coupon_results[0]->coupon_id;
                            if(isset($id_coupon) && !empty($id_coupon) && $id_coupon != 0)
                                $coupon_exist = true;
                            else
                                $coupon_exist = false;
                        }
                    }else{
                        $coupon_exist = false;
                    }
                }

                $sql_insertion = "INSERT INTO ".$wpdb->prefix ."booking (form, booking_type, modification_date, NumCommande, idCoupon, nombreDeVisiteur) VALUES ('$formdata',  $bktype, ".$my_modification_date.", '$numCommande', '$id_coupon', '$visitor')" ;
//debuge($formdata);
                if ( false === $wpdb->query($wpdb->prepare( $sql_insertion ) ) ){
                    ?> <script type="text/javascript"> document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during inserting into BD',__FILE__,__LINE__); ?></div>'; </script> <?php
                    die();
                }
                // Make insertion into BOOKINGDATES
                $booking_id = (int) $wpdb->insert_id;       //Get ID  of reservation

                if($coupon_exist == true){
                    $date           = $dates;

                    $customerEmail  = $booking_form_show['email'];
                    $nom            = $booking_form_show['name'];
                    $prenom         = $booking_form_show['secondname'];
                    $addr           = $booking_form_show['_all_']['address'. $bktype];
                    $ville          = $booking_form_show['_all_']['city'. $bktype];
                    $cdePost        = $booking_form_show['_all_']['postcode'. $bktype];
                    $pays           = $booking_form_show['_all_']['country'. $bktype];
                    $phone          = $booking_form_show['_all_']['phone'. $bktype];

                    $attente        = $booking_form_show['_all_']['amateur'. $bktype];
                    if($attente == "")
                        $attente    = $booking_form_show['_all_']['ludique'. $bktype];
                    if($attente == "")
                        $attente    = $booking_form_show['_all_']['decouverte'. $bktype];

                    $connais        = $booking_form_show['_all_']['connais'. $bktype];
                    if($connais == "")
                        $connais    = $booking_form_show['adore'. $bktype];
                    if($connais == "")
                        $connais    = $booking_form_show['_all_']['curieux'. $bktype];

                    $isAnglais       = $booking_form_show['_all_']['anglais'. $bktype];
                    if($isAgnlais == 'checkbox')  $isAnglais = true;
                    else                    $isAnglais = false;

                    sendMailProprietaire($bktype, $booking_id, "", "", "", "", "", "", $numCommande, $date, $visitor, 
                                        $isAnglais, 
                                        $connais, $attente, $customerEmail, $nom, $prenom, 
                                        $addr, $ville, $cdePost, $pays, $phone);
                }

                $is_approved_dates = '0';
                $auto_approve_new_bookings_is_active       =  get_bk_option( 'booking_auto_approve_new_bookings_is_active' );
                if ( trim($auto_approve_new_bookings_is_active) == 'On')
                    $is_approved_dates = '1';

                if($coupon_exist == true){
                    $is_approved_dates = '1';
                }
            }




            $sdform = $_POST['form'];
            $my_dates = explode(",",$dates);
            $i=0; foreach ($my_dates as $md) {$my_dates[$i] = trim($my_dates[$i]) ; $i++; }

            $start_end_time = get_times_from_bk_form($sdform, $my_dates, $bktype);
            $start_time = $start_end_time[0];
            $end_time = $start_end_time[1];
            $my_dates = $start_end_time[2];

            make_bk_action('wpdev_booking_post_inserted', $booking_id, $bktype, str_replace('|',',',$dates),  array($start_time, $end_time ) );
            $my_cost = apply_bk_filter('get_booking_cost_from_db', '', $booking_id);


            $i=0;
            foreach ($my_dates as $md) { // Set in dates in such format: yyyy.mm.dd
                if ($md != '') {
                    $md = explode('.',$md);
                    $my_dates[$i] = $md[2] . '.' . $md[1] . '.' . $md[0] ;
                } else { unset($my_dates[$i]) ; } // If some dates is empty so remove it   // This situation can be if using several bk calendars and some calendars is not checked
                $i++;

            }
            sort($my_dates); // Sort dates

            $my_dates4emeil = '';
            $i=0;
            $insert='';
            $my_date_previos = '';
            foreach ($my_dates as $my_date) {
                $i++;          // Loop through all dates
                if (strpos($my_date,'.')!==false) {

                    if ( get_bk_option( 'booking_recurrent_time' ) !== 'On') {
                            $my_date = explode('.',$my_date);
                            if ($i == 1) {
                                $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], $start_time[0], $start_time[1], $start_time[2] );
                            }elseif ($i == count($my_dates)) {
                                $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], $end_time[0], $end_time[1], $end_time[2] );
                            }else {
                                $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], '00', '00', '00' );
                            }
                            $my_dates4emeil .= $date . ',';
                            if ( !empty($insert) ) $insert .= ', ';
                            $insert .= "('$booking_id', '$date', '$is_approved_dates' )";
                    } else {
                            if ($my_date_previos  == $my_date) continue; // escape for single day selections.

                            $my_date_previos  = $my_date;
                            $my_date = explode('.',$my_date);
                            $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], $start_time[0], $start_time[1], $start_time[2] );
                            $my_dates4emeil .= $date . ',';
                            if ( !empty($insert) ) $insert .= ', ';
                            $insert .= "('$booking_id', '$date', '$is_approved_dates' )";

                            $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], $end_time[0], $end_time[1], $end_time[2] );
                            $my_dates4emeil .= $date . ',';
                            if ( !empty($insert) ) $insert .= ', ';
                            $insert .= "('$booking_id', '$date', '$is_approved_dates' )";

                    }

                }
            }
            $my_dates4emeil = substr($my_dates4emeil,0,-1);

            $my_dates4emeil_check_in_out = explode(',',$my_dates4emeil);
            $my_check_in_date = change_date_format($my_dates4emeil_check_in_out[0] );
            $my_check_out_date = change_date_format($my_dates4emeil_check_in_out[ count($my_dates4emeil_check_in_out)-1 ] );
            /* // This add 1 more day for check out day
            $my_check_out_date = $my_dates4emeil_check_in_out[ count($my_dates4emeil_check_in_out)-1 ];
            $dt = trim($my_check_out_date);
            $dta = explode(' ',$dt);
            $dta = $dta[0];
            $dta = explode('-',$dta);
            $my_check_out_date = date('Y-m-d H:i:s' , mktime(0, 0, 0, $dta[1], ($dta[2]+1), $dta[0] ));
            $my_check_out_date = change_date_format($my_check_out_date);
            /**/

            if ( !empty($insert) )
                if ( false === $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix ."bookingdates (booking_id, booking_date, approved) VALUES " . $insert) ) ){
                    ?> <script type="text/javascript"> document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during inserting into BD - Dates',__FILE__,__LINE__); ?></div>'; </script> <?php
                    die();
                }

            if (function_exists ('get_booking_title')) $bk_title = get_booking_title( $bktype );
            else $bk_title = '';

            if ($my_booking_id>0) { // For editing exist booking


                $mail_sender    =  htmlspecialchars_decode( get_bk_option( 'booking_email_modification_adress') ) ;
                $mail_subject   =  htmlspecialchars_decode( get_bk_option( 'booking_email_modification_subject') );
                $mail_body      =  htmlspecialchars_decode( get_bk_option( 'booking_email_modification_content') );
                $mail_subject =  apply_bk_filter('wpdev_check_for_active_language', $mail_subject );
                $mail_body    =  apply_bk_filter('wpdev_check_for_active_language', $mail_body );

                if (function_exists ('get_booking_title')) $bk_title = get_booking_title( $bktype );
                else $bk_title = '';

                $booking_form_show = get_form_content ($formdata, $bktype);

                $mail_body_to_send = str_replace('[bookingtype]', $bk_title, $mail_body);
                if (get_bk_option( 'booking_date_view_type') == 'short') $my_dates_4_send = get_dates_short_format( get_dates_str($booking_id) );
                else                                                  $my_dates_4_send = change_date_format(get_dates_str($booking_id));
                $mail_body_to_send = str_replace('[dates]',$my_dates_4_send , $mail_body_to_send);
                $mail_body_to_send = str_replace('[check_in_date]',$my_check_in_date , $mail_body_to_send);
                $mail_body_to_send = str_replace('[check_out_date]',$my_check_out_date , $mail_body_to_send);
                $mail_body_to_send = str_replace('[id]', $booking_id , $mail_body_to_send);


                $mail_body_to_send = str_replace('[content]', $booking_form_show['content'], $mail_body_to_send);
                if (! isset($denyreason)) $denyreason = '';
                $mail_body_to_send = str_replace('[denyreason]', $denyreason, $mail_body_to_send);
                $mail_body_to_send = str_replace('[name]', $booking_form_show['name'], $mail_body_to_send);
                $mail_body_to_send = str_replace('[cost]', $my_cost, $mail_body_to_send);
                if ( isset($booking_form_show['secondname']) ) $mail_body_to_send = str_replace('[secondname]', $booking_form_show['secondname'], $mail_body_to_send);
                $mail_body_to_send = str_replace('[siteurl]', htmlspecialchars_decode( '<a href="'.site_url().'">' . site_url() . '</a>'), $mail_body_to_send);
                $mail_body_to_send = apply_bk_filter('wpdev_booking_set_booking_edit_link_at_email', $mail_body_to_send, $booking_id );

                $mail_subject = str_replace('[name]', $booking_form_show['name'], $mail_subject);
                if ( isset($booking_form_show['secondname']) ) $mail_subject = str_replace('[secondname]', $booking_form_show['secondname'], $mail_subject);

                $mail_recipient =  $booking_form_show['email'];

                $mail_headers = "From: $mail_sender\n";
                $mail_headers .= "Content-Type: text/html\n";

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
//debuge($_SERVER);

                if ( strpos($_SERVER['HTTP_REFERER'],'wp-admin/admin.php?') ===false ) {
                    do_action('wpdev_new_booking',$booking_id, $bktype, str_replace('|',',',$dates), array($start_time, $end_time ) ,$sdform );
                }

                ?> <script type="text/javascript">
                <?php
                if ( strpos($_SERVER['HTTP_REFERER'],'wp-admin/admin.php?') ===false ) { ?>
                            //document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div class=\"submiting_content\" ><?php echo get_bk_option( 'booking_title_after_reservation'); ?></div>';
                            //jQuery('.submiting_content').fadeOut(<?php echo get_bk_option( 'booking_title_after_reservation_time'); ?>);
                            setReservedSelectedDates('<?php echo $bktype; ?>');
                    <?php }  else { ?>
                            document.getElementById('ajax_message').innerHTML = '<?php echo __('Updated successfully', 'wpdev-booking'); ?>';
                            jQuery('#ajax_message').fadeOut(1000);
                            document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php echo __('Updated successfully', 'wpdev-booking'); ?></div>';
                            location.href='admin.php?page=<?php echo WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME ;?>wpdev-booking&booking_type=<?php echo $bktype; ?>&booking_id_selection=<?php echo  $my_booking_id;?>';
                    <?php } ?>
                    </script> <?php


            } else {
                    if ( count($my_dates) > 0 ) {
                        //// For inserting NEW booking
                        // Sending mail ///////////////////////////////////////////////////////
                        $mail_sender    =  htmlspecialchars_decode( get_bk_option( 'booking_email_reservation_from_adress') ) ; //'"'. 'Booking sender' . '" <' . $booking_form_show['email'].'>';
                        $mail_recipient =  htmlspecialchars_decode( get_bk_option( 'booking_email_reservation_adress') );//'"Booking receipent" <' .get_option('admin_email').'>';
                        $mail_subject   =  htmlspecialchars_decode( get_bk_option( 'booking_email_reservation_subject') );
                        $mail_body      =  htmlspecialchars_decode( get_bk_option( 'booking_email_reservation_content') );
                        $mail_subject =  apply_bk_filter('wpdev_check_for_active_language', $mail_subject );
                        $mail_body    =  apply_bk_filter('wpdev_check_for_active_language', $mail_body );

                        $mail_body = str_replace('[bookingtype]', $bk_title, $mail_body);
                        if (get_bk_option( 'booking_date_view_type') == 'short') $my_dates_4_send = get_dates_short_format( $my_dates4emeil );
                        else                                                  $my_dates_4_send = change_date_format($my_dates4emeil);
                        $mail_body = str_replace('[dates]',  $my_dates_4_send , $mail_body);
                        $mail_body = str_replace('[check_in_date]',$my_check_in_date , $mail_body);
                        $mail_body = str_replace('[check_out_date]',$my_check_out_date , $mail_body);
                        $mail_body = str_replace('[id]',$booking_id , $mail_body);

                        $mail_body = str_replace('[content]', $booking_form_show['content'], $mail_body);
                        $mail_body = str_replace('[name]', $booking_form_show['name'], $mail_body);
                        $mail_body = str_replace('[cost]', $my_cost, $mail_body);
                        $mail_body = str_replace('[siteurl]', htmlspecialchars_decode( '<a href="'.site_url().'">' . site_url() . '</a>'), $mail_body);
                        $mail_body = str_replace('[moderatelink]', htmlspecialchars_decode(
                                '<a href="'.site_url()  . '/wp-admin/admin.php?page='. WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME .'wpdev-booking&booking_type='.$bktype.'&moderate_id='. $booking_id .'">'
                                . __('here','wpdev-booking') . '</a>'), $mail_body);


                        $mail_body = apply_bk_filter('wpdev_booking_set_booking_edit_link_at_email', $mail_body, $booking_id );



                        if ( isset($booking_form_show['secondname']) ) $mail_body = str_replace('[secondname]', $booking_form_show['secondname'], $mail_body);

                        $mail_subject = str_replace('[name]', $booking_form_show['name'], $mail_subject);
                        if ( isset($booking_form_show['secondname']) ) $mail_subject = str_replace('[secondname]', $booking_form_show['secondname'], $mail_subject);

                        $mail_headers = "From: $mail_sender\n";
                        $mail_headers .= "Content-Type: text/html\n";

                        if ( strpos($mail_recipient,'[visitoremeil]') !== false ) {
                            $mail_recipient = str_replace('[visitoremeil]',$booking_form_show['email'],$mail_recipient);
                        }

                        if (get_bk_option( 'booking_is_email_reservation_adress'  ) != 'Off')
                            if ( ( strpos($mail_recipient,'@blank.com') === false ) && ( strpos($mail_body,'admin@blank.com') === false ) )
                                if ($is_send_emeils != 0 )
                                    @wp_mail($mail_recipient, $mail_subject, $mail_body, $mail_headers);
                        /////////////////////////////////////////////////////////////////////////

                        if (get_bk_option( 'booking_is_email_newbookingbyperson_adress'  ) == 'On') {

                            $mail_sender    =  htmlspecialchars_decode( get_bk_option( 'booking_email_newbookingbyperson_adress') ) ; //'"'. 'Booking sender' . '" <' . $booking_form_show['email'].'>';
                            $mail_recipient =  $booking_form_show['email'];
                            $mail_subject   =  htmlspecialchars_decode( get_bk_option( 'booking_email_newbookingbyperson_subject') );
                            $mail_body      =  htmlspecialchars_decode( get_bk_option( 'booking_email_newbookingbyperson_content') );
                            $mail_subject =  apply_bk_filter('wpdev_check_for_active_language', $mail_subject );
                            $mail_body    =  apply_bk_filter('wpdev_check_for_active_language', $mail_body );

                            
                            $mail_body = str_replace('[bookingtype]', $bk_title, $mail_body);
                            if (get_bk_option( 'booking_date_view_type') == 'short') $my_dates_4_send = get_dates_short_format( $my_dates4emeil );
                            else                                                  $my_dates_4_send = change_date_format($my_dates4emeil);
                            $mail_body = str_replace('[dates]',  $my_dates_4_send , $mail_body);
                            $mail_body = str_replace('[check_in_date]',$my_check_in_date , $mail_body);
                            $mail_body = str_replace('[check_out_date]',$my_check_out_date , $mail_body);
                            $mail_body = str_replace('[id]',$booking_id , $mail_body);


                            $mail_body = str_replace('[content]', $booking_form_show['content'], $mail_body);
                            $mail_body = str_replace('[name]', $booking_form_show['name'], $mail_body);
                            $mail_body = str_replace('[cost]', $my_cost, $mail_body);
                            $mail_body = str_replace('[siteurl]', htmlspecialchars_decode( '<a href="'.site_url().'">' . site_url() . '</a>'), $mail_body);
                            $mail_body = apply_bk_filter('wpdev_booking_set_booking_edit_link_at_email', $mail_body, $booking_id );

                            if ( isset($booking_form_show['secondname']) ) $mail_body = str_replace('[secondname]', $booking_form_show['secondname'], $mail_body);

                            $mail_subject = str_replace('[name]', $booking_form_show['name'], $mail_subject);
                            if ( isset($booking_form_show['secondname']) ) $mail_subject = str_replace('[secondname]', $booking_form_show['secondname'], $mail_subject);

                            $mail_headers = "From: $mail_sender\n";
                            $mail_headers .= "Content-Type: text/html\n";

                            if ( strpos($mail_recipient,'[visitoremeil]') !== false ) {
                                $mail_recipient = str_replace('[visitoremeil]',$booking_form_show['email'],$mail_recipient);
                            }
                            if ( strpos($mail_recipient,'@blank.com') === false )
                                if ( ( strpos($mail_recipient,'@blank.com') === false ) && ( strpos($mail_body,'admin@blank.com') === false ) )
                                    if ($is_send_emeils != 0 )
                                        @wp_mail($mail_recipient, $mail_subject, $mail_body, $mail_headers);

                        }
                    }
                do_action('wpdev_new_booking',$booking_id, $bktype, str_replace('|',',',$dates), array($start_time, $end_time ) ,$sdform );

                ?> <script type="text/javascript"> setReservedSelectedDates('<?php echo $bktype; ?>'); </script>  <?php

            }

            // ReUpdate booking resource TYPE if its needed here
            if (! empty($dates) ) // check to have dates not empty
                make_bk_action('wpdev_booking_reupdate_bk_type_to_childs', $booking_id, $bktype, str_replace('|',',',$dates),  array($start_time, $end_time ) , $sdform );

}


?>