<?php
/*
This is COMMERCIAL SCRIPT
We are do not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

    if (file_exists(WPDEV_BK_PLUGIN_DIR. '/inc/payments/paypal.php')) { require_once(WPDEV_BK_PLUGIN_DIR. '/inc/payments/paypal.php' ); }
    if (file_exists(WPDEV_BK_PLUGIN_DIR. '/inc/payments/sage.php')) { require_once(WPDEV_BK_PLUGIN_DIR. '/inc/payments/sage.php' ); }
    if (file_exists(WPDEV_BK_PLUGIN_DIR. '/inc/payments/ipay88.php')) { require_once(WPDEV_BK_PLUGIN_DIR. '/inc/payments/ipay88.php' ); }
    if (file_exists(WPDEV_BK_PLUGIN_DIR. '/inc/payments/atos.php')) { require_once(WPDEV_BK_PLUGIN_DIR. '/inc/payments/atos.php' ); }


    function wpdev_bk_define_payment_forms($blank, $booking_id, $summ,$bk_title, $booking_days_count, $booking_type, $bkform, $wp_nonce, $is_deposit=false ){
        $output = '';
        $output .= apply_bk_filter('wpdev_bk_define_payment_form_paypal', '', $booking_id, $summ,$bk_title, $booking_days_count, $booking_type, $bkform, $wp_nonce, $is_deposit );
        $output .= apply_bk_filter('wpdev_bk_define_payment_form_sage', '', $booking_id, $summ,$bk_title, $booking_days_count, $booking_type, $bkform, $wp_nonce, $is_deposit );
        $output .= apply_bk_filter('wpdev_bk_define_payment_form_ipay88', '', $booking_id, $summ,$bk_title, $booking_days_count, $booking_type, $bkform, $wp_nonce, $is_deposit );
        $output .= apply_bk_filter('wpdev_bk_define_payment_form_atos', '', $booking_id, $summ,$bk_title, $booking_days_count, $booking_type, $bkform, $wp_nonce, $is_deposit );


        $output = str_replace("'",'"',$output);
        $output = str_replace('\"','"',$output);
        return $output;
    }
    add_bk_filter('wpdev_bk_define_payment_forms', 'wpdev_bk_define_payment_forms');

    function wpdev_bk_is_payment_forms_off($blank){

        $is_active =  get_bk_option( 'booking_paypal_is_active' );
        if ($is_active == 'On') return  false;

        $is_active =  get_bk_option( 'booking_sage_is_active' );
        if ($is_active == 'On') return  false;

        $is_active =  get_bk_option( 'booking_ipay88_is_active' ) ;
        if ($is_active == 'On') return  false;

        $is_active = get_bk_option('booking_atos_is_active');
        if ($is_active == 'On') return  false;

        return true;
    }
    add_bk_filter('is_payment_forms_off', 'wpdev_bk_is_payment_forms_off');
    
?>