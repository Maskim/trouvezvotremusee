<?php
/*
This is COMMERCIAL SCRIPT
We are do not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  S u p p o r t    f u n c t i o n s       ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // List of possible Payments statuses from the different payment systems.
        function get_payment_status_ok(){
           $payment_status = array(
                       'OK',
                       'Completed',
                       'PayPal:OK',
                       'Sage:OK',
                       'ipay88:OK',
                       'success',
                       'Paid OK'
                      );
            return  $payment_status;
        }

        function get_payment_status_pending(){
           $payment_status = array(
                                   'Not_Completed',
                                   'Not Completed',
                                   'Pending',
                                   'Processed',
                                   'In-Progress',
                                   'partially',
                                   'Partially paid'
                                   );
            return  $payment_status;
        }

        function get_payment_status_unknown(){
           $payment_status = array(
                                   '1',
                                   'Canceled_Reversal',
                                   'Voided',
                                   'Created'
                                   );
            return  $payment_status;
        }

        function get_payment_status_error(){
           $payment_status = array(
                                   'PayPal:Failed',
                                   'Sage:Failed',
                                   'Sage:REJECTED',
                                   'ipay88:Failed',
                                   'Denied',
                                   'Expired',
                                   'Failed',
                                   'Reversed',
                                   'Partially_Refunded',
                                   'Refunded',
                                   'not-authed',
                                   'malformed',
                                   'invalid',
                                   'abort',
                                   'rejected',
                                   'fraud',
                                   'Cancelled',
                                   'error'
                                   );
            return  $payment_status;
        }

        // Get type of the payment status 
        function wpdev_bk_get_type_of_payment_status($payment_status){

           $payment_type = 'unknown';               // Default payment status type

           $payment_success = get_payment_status_ok();
           $payment_pending = get_payment_status_pending();
           $payment_unknown = get_payment_status_unknown();
           $payment_error   = get_payment_status_error();

           // Check  LOWERCASE for the any payemtn status
           if (in_array( strtolower($payment_status), wpdev_bk_arraytolower($payment_success) ) !== false) $payment_type = 'success';
           if (in_array( strtolower($payment_status), wpdev_bk_arraytolower($payment_pending) ) !== false) $payment_type = 'pending';
           if (in_array( strtolower($payment_status), wpdev_bk_arraytolower($payment_unknown) ) !== false) $payment_type = 'unknown';
           if (in_array( strtolower($payment_status), wpdev_bk_arraytolower($payment_error) ) !== false) $payment_type = 'error';

           return  $payment_type;
           
           }

        // Check  if Payment Status - SUCCESS
        function is_payment_status_ok($payment_status){

            if (wpdev_bk_get_type_of_payment_status($payment_status) == 'success')  return  true;
            else                                                                    return false;
        }

        // Check  if Payment Status - PENDING
        function is_payment_status_pending($payment_status){

            if (wpdev_bk_get_type_of_payment_status($payment_status) == 'pending')  return  true;
            else                                                                    return false;
        }

        // Check  if Payment Status - UNKNOWN
        function is_payment_status_unknown($payment_status){

            if (wpdev_bk_get_type_of_payment_status($payment_status) == 'unknown')  return  true;
            else                                                                    return false;
        }

        // Check  if Payment Status - ERROR
        function is_payment_status_error($payment_status){

            if (wpdev_bk_get_type_of_payment_status($payment_status) == 'error')  return  true;
            else                                                                    return false;
        }


        // P a y m e n t     f u n c t i o n s
        // Its called, when returned from Paymant system
        function wpdev_bk_update_pay_status(){

        //debuge($_GET['merchant_return_link']);

            if (  isset( $_GET['merchant_return_link']))  {
                wpdev_redirect( get_bk_option( 'booking_paypal_return_url' ) )   ;
                die;
            }

            global $wpdb;
            $status = '';  $booking_id = '';  $pay_system = ''; $wp_nonce = '';

            if (isset($_GET['payed_booking']))  $booking_id = $_GET['payed_booking'];
            if (isset($_GET['stats']))          $status = $_GET['stats'];
            if (isset($_GET['pay_sys']))        $pay_system = $_GET['pay_sys'];
            if (isset($_GET['wp_nonce']))       $wp_nonce   = $_GET['wp_nonce'];

            $slct_sql = "SELECT pay_status FROM ".$wpdb->prefix ."booking WHERE booking_id IN ($booking_id) LIMIT 0,1";
            $slct_sql_results  = $wpdb->get_results($wpdb->prepare( $slct_sql ));

            $is_go_on = false;
            if ( count($slct_sql_results) > 0 )
                if ($slct_sql_results[0]->pay_status == $wp_nonce)  $is_go_on = true; // Evrything GOOD

            if ($is_go_on == false) { // Some Unautorize request, die
                wpdev_redirect( site_url()  );
            }

            if ($pay_system == 'sage') {
                $strCrypt=$_REQUEST["crypt"];
                $strEncryptionPassword =  get_bk_option( 'booking_sage_encryption_password' );
                $strDecoded=wpdev_simpleXor(wpdev_Base64Decode($strCrypt),$strEncryptionPassword);
                $values = wpdev_getToken($strDecoded);
                $status = 'Sage:' . $values['Status'];
               // debuge($values, $booking_id, $status, $pay_system, get_bk_option( 'booking_sage_order_Successful' ), get_bk_option( 'booking_sage_order_Failed' ));
            }

            if ($pay_system == 'paypal') { $status = 'PayPal:' . $status; }

            if ($pay_system == 'ipay88') {
                if ($_REQUEST['Status'] == 1) {

                    $MerchantCode = $_REQUEST['MerchantCode'];
                    $RefNo = $_REQUEST['RefNo'];
                    $Amount = $_REQUEST['Amount'];

                    $status = '';

                    // Check the REFERER site
                    if ($status == '')
                        if(isset($_SERVER['HTTP_REFERER'])) {
                            $pos1 = strpos($_SERVER['HTTP_REFERER'], 'https://www.mobile88.com');
                            $pos2 = strpos($_SERVER['HTTP_REFERER'], 'http://www.mobile88.com/');

                            if (( $pos1 === false) && ($pos2 === false)) {
                                debuge( 'Respond not from correct payment site !' );
                                $status = 'ipay88:Failed';
                            }
                        }
                    // Requery
                    if ($status == '') {
                        $result = iPay88_Requery($MerchantCode, $RefNo, $Amount);
                        if ( $result == '00') {
                            $iPayStatusMessage = __('Successful payment', 'wpdev-booking');
                        } else {
                            if ( $result == 'Invalid parameters') $iPayStatusMessage = __(' Parameters pass in incorrect,', 'wpdev-booking');
                            else if ( $result == 'Record not found') $iPayStatusMessage = __('Cannot found the record', 'wpdev-booking');
                            else if ( $result == 'Incorrect amount') $iPayStatusMessage = __('Amount different', 'wpdev-booking');
                            else if ( $result == 'Payment fail') $iPayStatusMessage = __('Payment fail', 'wpdev-booking');
                            else if ( $result == 'M88Admin') $iPayStatusMessage = __('Payment status updated by Mobile88 Admin(Fail)', 'wpdev-booking');
                            else if ( $result == 'Connection Error') $iPayStatusMessage = __('Requery connection Error', 'wpdev-booking');

                            $status = 'ipay88:Failed';
                            debuge($_REQUEST['ErrDesc'], $iPayStatusMessage );
                        }
                    }

                    if(0){ //Disabled check
                        // Check payment ammount
                        if ($status == '')
                            if ($slct_sql_results[0]->cost != $Amount ) {
                                debuge( 'Payment amount is different from original !' );
                                $status = 'ipay88:Failed';
                            }
                    }
                    // Check signature
                    if ($status == '') {

                        $summ_sing = str_replace('.', '', $Amount /*$slct_sql_results[0]->cost*/);
                        $summ_sing = str_replace(',', '', $summ_sing );
                        $ipay88_merchant_code = get_bk_option( 'booking_ipay88_merchant_code' );
                        $ipay88_merchant_key = get_bk_option( 'booking_ipay88_merchant_key' );
                        // $signature = $ipay88_merchant_key . $ipay88_merchant_code . $_REQUEST['RefNo'] . $summ_sing .  $_REQUEST['Currency'] ;
                        $signature = $ipay88_merchant_key . $ipay88_merchant_code . $_REQUEST['PaymentId']. $_REQUEST['RefNo'] . $summ_sing .  $_REQUEST['Currency'] . $_REQUEST['Status'];

                        $signature = iPay88_signature($signature);

                        if ($_REQUEST["Signature"] != $signature ) {
                            debuge( 'Signature is different from original !' );
                            $status = 'ipay88:Failed';
                        }
                    }

                    if ($status == '') $status = 'ipay88:OK';

                } else {
                    $status = 'ipay88:Failed';
                    debuge($_REQUEST['ErrDesc']);
                    //debuge($booking_id, $status);die;
                    /* // Parameters in Respond
                    [payed_booking] => 44
                    [wp_nonce] => 30068
                    [pay_sys] => ipay88
                    [stats] => OK
                    [MerchantCode] => 1111111
                    [PaymentId] => 0
                    [RefNo] => A044
                    [Amount] => 240
                    [Currency] => PHP
                    [Remark] =>
                    [TransId] => T0203282500
                    [AuthCode] =>
                    [Status] => 0
                    [ErrDesc] => Invalid parameters(Currency Not Supported By Merchant Account)
                    [Signature] =>
                    /**/
                }
            }

        //debuge ($_REQUEST, $iPayStatusMessage); die;


            if ( ($booking_id =='') || ($status =='') || ($pay_system =='') || ($wp_nonce =='') ) wpdev_redirect( site_url()  )   ;

            $update_sql = "UPDATE ".$wpdb->prefix ."booking AS bk SET bk.pay_status='$status' WHERE bk.booking_id=$booking_id;";
            if ( false === $wpdb->query($wpdb->prepare( $update_sql ) ) ){
                if ($pay_system == 'sage')        { $payment_status =  'Sage:Failed' ;  }
                else if ($pay_system == 'paypal') { $payment_status =  'PayPal:Failed'; }
                else wpdev_redirect( site_url()  )   ;
            }

            if ($pay_system == 'sage') {
                if (($status == 'OK') || ($status == 'Sage:OK') ) $payment_status =  'Sage:OK' ;
                else                                              $payment_status =  'Sage:Failed' ;
            }
            if ($pay_system == 'ipay88') {
                if (($status == 'OK') || ($status == 'ipay88:OK') ) $payment_status =  'ipay88:OK' ;
                else                                                $payment_status =  'ipay88:Failed' ;
            }

            if ($pay_system == 'paypal') {
                if ($status == 'PayPal:OK')  $payment_status =  'PayPal:OK' ;
                else                  $payment_status =  'PayPal:Failed' ;
            }


            switch ($payment_status) {

                case 'Sage:OK':
                        check_auto_approve_or_cancell($booking_id, true, 'sage');
                        wpdev_redirect( get_bk_option( 'booking_sage_order_Successful' ) )   ;
                        break;

                case 'Sage:Failed':
                        check_auto_approve_or_cancell($booking_id, false, 'sage');
                        wpdev_redirect( get_bk_option( 'booking_sage_order_Failed' ) )   ;
                        break;

                case 'PayPal:OK':
                        check_auto_approve_or_cancell($booking_id, true, 'paypal');
                        wpdev_redirect( get_bk_option( 'booking_paypal_return_url' ) )   ;
                        break;

                case 'PayPal:Failed':
                        check_auto_approve_or_cancell($booking_id, false, 'paypal');
                        wpdev_redirect( get_bk_option( 'booking_paypal_cancel_return_url' ) )   ;
                        break;

                case 'ipay88:OK':
                        check_auto_approve_or_cancell($booking_id, true, 'ipay88');
                        wpdev_redirect( get_bk_option( 'booking_ipay88_return_url' ) )   ;
                        break;

                case 'ipay88:Failed':
                        check_auto_approve_or_cancell($booking_id, false, 'ipay88');
                        wpdev_redirect( get_bk_option( 'booking_ipay88_cancel_return_url' ) )   ;
                        break;


                default:
                        wpdev_redirect( site_url()  )   ;
                        break;
            }




        }

        function check_auto_approve_or_cancell($booking_id, $is_approve, $pay_system) {

            global $wpdb;
            if ($pay_system == 'paypal')
                $auto_approve = get_bk_option( 'booking_paypal_is_auto_approve_cancell_booking'  );

            if ($pay_system == 'sage')
                $auto_approve = get_bk_option( 'booking_sage_is_auto_approve_cancell_booking'  );

            if ($pay_system == 'ipay88')
                $auto_approve = get_bk_option( 'booking_ipay88_is_auto_approve_cancell_booking'  );



            if ($auto_approve == 'On') {

                if ($is_approve === true ) { // Auto Approve it

                    sendApproveEmails($booking_id,1);

                    $update_sql = "UPDATE ".$wpdb->prefix ."bookingdates SET approved = '1' WHERE booking_id IN ($booking_id);";
                    if ( false === $wpdb->query($wpdb->prepare( $update_sql ) ) ){ wpdev_redirect( site_url()  )   ; }
                }

                if ($is_approve === false ) { // Auto Cancell it


                    // Send decline emails
                    $auto_cancel_pending_unpaid_bk_is_send_email =  get_bk_option( 'booking_auto_cancel_pending_unpaid_bk_is_send_email' );
                    if ($auto_cancel_pending_unpaid_bk_is_send_email == 'On') {
                        $auto_cancel_pending_unpaid_bk_email_reason  =  get_bk_option( 'booking_auto_cancel_pending_unpaid_bk_email_reason' );
                        sendDeclineEmails($booking_id,1, $auto_cancel_pending_unpaid_bk_email_reason );
                    }

                    if ( false === $wpdb->query($wpdb->prepare( "DELETE FROM ".$wpdb->prefix ."bookingdates WHERE booking_id IN ($booking_id)") ) ){ wpdev_redirect( site_url()  )   ; }
                    if ( false === $wpdb->query($wpdb->prepare( "DELETE FROM ".$wpdb->prefix ."booking WHERE booking_id IN ($booking_id)") ) ){ wpdev_redirect( site_url()  )   ; }
                }
            }

        }

        //Function to redirect browser to a specific page
        function wpdev_redirect($url) {
           //if (!headers_sent()) header('Location: '.$url . '/');
           //else
               {
               echo '<script type="text/javascript">';
               echo 'window.location.href="'.$url.'";';
               echo '</script>';
               echo '<noscript>';
               echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
               echo '</noscript>';
           }
        }


        //  S A G E    F u n c t i o n s
        function wpdev_getToken($thisString) {

        // List the possible tokens
        $Tokens = array(
        "Status",
        "StatusDetail",
        "VendorTxCode",
        "VPSTxId",
        "TxAuthNo",
        "Amount",
        "AVSCV2",
        "AddressResult",
        "PostCodeResult",
        "CV2Result",
        "GiftAid",
        "3DSecureStatus",
        "CAVV",
            "AddressStatus",
            "CardType",
            "Last4Digits",
            "PayerStatus","CardType");



        // Initialise arrays
        $output = array();
        $resultArray = array();

        // Get the next token in the sequence
        for ($i = count($Tokens)-1; $i >= 0 ; $i--){
        // Find the position in the string
        $start = strpos($thisString, $Tokens[$i]);
            // If it's present
        if ($start !== false){
          // Record position and token name
          $resultArray[$i]->start = $start;
          $resultArray[$i]->token = $Tokens[$i];
        }
        }

        // Sort in order of position
        sort($resultArray);
            // Go through the result array, getting the token values
        for ($i = 0; $i<count($resultArray); $i++){
        // Get the start point of the value
        $valueStart = $resultArray[$i]->start + strlen($resultArray[$i]->token) + 1;
            // Get the length of the value
        if ($i==(count($resultArray)-1)) {
          $output[$resultArray[$i]->token] = substr($thisString, $valueStart);
        } else {
          $valueLength = $resultArray[$i+1]->start - $resultArray[$i]->start - strlen($resultArray[$i]->token) - 2;
              $output[$resultArray[$i]->token] = substr($thisString, $valueStart, $valueLength);
        }

        }

        // Return the ouput array
        return $output;
        }

        function wpdev_base64Decode($scrambled) {
          // Initialise output variable
          $output = "";

          // Fix plus to space conversion issue
          $scrambled = str_replace(" ","+",$scrambled);

          // Do encoding
          $output = base64_decode($scrambled);

          // Return the result
          return $output;
        }

        //  The SimpleXor encryption algorithm **  NOTE: This is a placeholder really.  Future releases of Form will use AES or TwoFish.  Proper encryption **  This simple function and the Base64 will deter script kiddies and prevent the "View Source" type tampering **  It won't stop a half decent hacker though, but the most they could do is change the amount field to something **  else, so provided the vendor checks the reports and compares amounts, there is no harm done.  It's still **  more secure than the other PSPs who don't both encrypting their forms at all
        function wpdev_simpleXor($InString, $Key) {
          // Initialise key array
          $KeyList = array();
          // Initialise out variable
          $output = "";

          // Convert $Key into array of ASCII values
          for($i = 0; $i < strlen($Key); $i++){
            $KeyList[$i] = ord(substr($Key, $i, 1));
          }

          // Step through string a character at a time
          for($i = 0; $i < strlen($InString); $i++) {
            // Get ASCII code from string, get ASCII code from key (loop through with MOD), XOR the two, get the character from the result
            // % is MOD (modulus), ^ is XOR
            $output.= chr(ord(substr($InString, $i, 1)) ^ ($KeyList[$i % strlen($Key)]));
          }

          // Return the result
          return $output;
        }

        // iPay88
        function iPay88_signature($source) {
                return base64_encode(iPay88_hex2bin(sha1($source)));
        }

        function iPay88_hex2bin($hexSource) {
            $bin='';
             for ($i=0;$i<strlen($hexSource);$i=$i+2) {
              $bin .= chr(hexdec(substr($hexSource,$i,2)));
             }
             return $bin;
        }

        function iPay88_Requery($MerchantCode, $RefNo, $Amount){

            $query = "http://www.mobile88.com/epayment/enquiry.asp?MerchantCode=" . $MerchantCode . "&RefNo=" . $RefNo . "&Amount=" . $Amount;

            $url = parse_url($query);
            $host = $url["host"];
            $path = $url["path"] . "?" . $url["query"];
            $timeout = 1;
            $fp = fsockopen ($host, 80, $errno, $errstr, $timeout);
            if ($fp) {
              fputs ($fp, "GET $path HTTP/1.0\nHost: " . $host . "\n\n");
              while (!feof($fp)) {
                $buf .= fgets($fp, 128);
              }
              $lines = split("\n", $buf);
              $Result = $lines[count($lines)-1];
              fclose($fp);
            } else {
              # enter error handing code here
              $Result = 'Connection Error';
            }
            return $Result;

        }



        

        function get_payment_status_titles(){
            $payment_status_titles = array(
                                    __('Completed', 'wpdev-booking')  =>'Completed',

                                    __('In-Progress', 'wpdev-booking')  =>'In-Progress',

                                    __('Unknown', 'wpdev-booking')   =>'1',

                                    __('Partially paid', 'wpdev-booking')  =>'partially',
                                    __('Cancelled', 'wpdev-booking')  =>'canceled',
                                    __('Failed', 'wpdev-booking')  =>'Failed',
                                    __('Refunded', 'wpdev-booking')  =>'Refunded',

                                    __('Fraud', 'wpdev-booking')  =>'fraud'
                                   );

            return $payment_status_titles;


            $payment_status_titles = array(
                                    __('!Paid OK', 'wpdev-booking')          =>'OK',
                                    __('Unknown status', 'wpdev-booking')   =>'1',
                                    __('Not Completed', 'wpdev-booking')   =>'Not_Completed',

                                    // PayPal statuses
                                    __('Completed', 'wpdev-booking')  =>'Completed',

                                    __('Pending', 'wpdev-booking')  =>'Pending',
                                    __('Processed', 'wpdev-booking')  =>'Processed',
                                    __('In-Progress', 'wpdev-booking')  =>'In-Progress',

                                    __('Canceled_Reversal', 'wpdev-booking')  =>'Canceled_Reversal',

                                    __('Denied', 'wpdev-booking')  =>'Denied',
                                    __('Expired', 'wpdev-booking')  =>'Expired',
                                    __('Failed', 'wpdev-booking')  =>'Failed',

                                    __('Partially_Refunded', 'wpdev-booking')  =>'Partially_Refunded',
                                    __('Refunded', 'wpdev-booking')  =>'Refunded',
                                    __('Reversed', 'wpdev-booking')  =>'Reversed',
                                    __('Voided', 'wpdev-booking')  =>'Voided',
                                    __('Created', 'wpdev-booking')  =>'Created',

                                    // Sage Statuses
                                    __('Not authed', 'wpdev-booking')  =>'not-authed',
                                    __('Malformed', 'wpdev-booking')  =>'malformed',
                                    __('Invalid', 'wpdev-booking')  =>'invalid',
                                    __('Abort', 'wpdev-booking')  =>'abort',
                                    __('Rejected', 'wpdev-booking')  =>'rejected',
                                    __('Error', 'wpdev-booking')  =>'error' ,

                                    __('Partially paid', 'wpdev-booking')  =>'partially',
                                    __('Cancelled', 'wpdev-booking')  =>'canceled',
                                    __('Fraud', 'wpdev-booking')  =>'fraud',
                                    __('Suspended', 'wpdev-booking')  =>'suspended'
                                   );
            return $payment_status_titles;
        }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  Filters interface     Controll elements  ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Payment status
        function wpdebk_filter_field_bk_paystatus(){
            // Pay status
            $wpdevbk_id =              'wh_pay_status';
            //$wpdevbk_control_label =   __('OK', 'wpdev-booking');
            //$wpdevbk_help_block =      __('Payment status', 'wpdev-booking');
            //wpdevbk_text_filter($wpdevbk_id, $wpdevbk_control_label, $wpdevbk_help_block)

            $wpdevbk_selectors = array( __('All', 'wpdev-booking')       =>'all',
                                'divider0'=>'divider',
                                       __('Paid OK', 'wpdev-booking') =>'group_ok',
                                       __('Unknown status', 'wpdev-booking')    =>'group_unknown',
                                       __('Not Completed', 'wpdev-booking')     =>'group_pending',
                                       __('Failed', 'wpdev-booking')            =>'group_failed'
                /*
                                            // PayPal statuses
                                            __('Completed', 'wpdev-booking')  =>'Completed',

                                            __('Pending', 'wpdev-booking')  =>'Pending',
                                            __('Processed', 'wpdev-booking')  =>'Processed',
                                            __('In-Progress', 'wpdev-booking')  =>'In-Progress',

                                            __('Canceled_Reversal', 'wpdev-booking')  =>'Canceled_Reversal',

                                            __('Denied', 'wpdev-booking')  =>'Denied',
                                            __('Expired', 'wpdev-booking')  =>'Expired',
                                            __('Failed', 'wpdev-booking')  =>'Failed',

                                            __('Partially_Refunded', 'wpdev-booking')  =>'Partially_Refunded',
                                            __('Refunded', 'wpdev-booking')  =>'Refunded',
                                            __('Reversed', 'wpdev-booking')  =>'Reversed',
                                            __('Voided', 'wpdev-booking')  =>'Voided',
                                            __('Created', 'wpdev-booking')  =>'Created',
                                'divider2'=>'divider',
                                            // Sage Statuses
                                            __('Not authed', 'wpdev-booking')  =>'not-authed',
                                            __('Malformed', 'wpdev-booking')  =>'malformed',
                                            __('Invalid', 'wpdev-booking')  =>'invalid',
                                            __('Abort', 'wpdev-booking')  =>'abort',
                                            __('Rejected', 'wpdev-booking')  =>'rejected',
                                            __('Error', 'wpdev-booking')  =>'error' ,
                                'divider3'=>'divider',
                                       __('Partially paid', 'wpdev-booking')  =>'partially',
                                       __('Cancelled', 'wpdev-booking')  =>'canceled',
                                       __('Fraud', 'wpdev-booking')  =>'fraud',
                                       __('Suspended', 'wpdev-booking')  =>'suspended'
/**/
                                       );




            $wpdevbk_control_label =   '';
            $wpdevbk_help_block =      __('Payment', 'wpdev-booking');

            wpdevbk_selection_and_custom_text_for_filter($wpdevbk_id, $wpdevbk_selectors, $wpdevbk_control_label, $wpdevbk_help_block, 'all');

        }


        // Cost filter page
        function wpdebk_filter_field_bk_costs(){
            // Costs
            $wpdevbk_id =              'wh_cost';
            $wpdevbk_control_label =   '';
            $wpdevbk_placeholder =     '0';
            $wpdevbk_help_block =      __('Min. cost', 'wpdev-booking');

            $wpdevbk_id2 =              'wh_cost2';
            $wpdevbk_control_label2 =   __('-', 'wpdev-booking');
            $wpdevbk_placeholder2 =     '100000';
            $wpdevbk_help_block2 =      __('Max. cost', 'wpdev-booking');

            $wpdevbk_width = 'span1';

            wpdevbk_text_from_to_filter($wpdevbk_id, $wpdevbk_control_label, $wpdevbk_placeholder, $wpdevbk_help_block, $wpdevbk_id2, $wpdevbk_control_label2, $wpdevbk_placeholder2, $wpdevbk_help_block2, $wpdevbk_width);
        }


        // Get the sort options for the filter at the booking listing page
        function get_s_bk_filter_sort_options($wpdevbk_selectors_def){
              $wpdevbk_selectors = array(__('ID', 'wpdev-booking').'&nbsp;<i class="icon-arrow-up "></i>' =>'',
                               __('Resource', 'wpdev-booking').'&nbsp;<i class="icon-arrow-up "></i>' =>'booking_type',
                               __('Cost', 'wpdev-booking').'&nbsp;<i class="icon-arrow-up "></i>' =>'cost',
                               'divider0'=>'divider',
                               __('ID', 'wpdev-booking').'&nbsp;<i class="icon-arrow-down "></i>' =>'booking_id_asc',
                               __('Resource', 'wpdev-booking').'&nbsp;<i class="icon-arrow-down "></i>' =>'booking_type_asc',
                               __('Cost', 'wpdev-booking').'&nbsp;<i class="icon-arrow-down "></i>' =>'cost_asc'
                              );
              return $wpdevbk_selectors;
        }
        add_bk_filter('bk_filter_sort_options', 'get_s_bk_filter_sort_options');



    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  S Q L   Modifications  for  Booking Listing  ///////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Pay status
        function get_s_bklist_sql_paystatus($blank, $wh_pay_status ){
            $sql_where = '';

            if ( (isset($_REQUEST['wh_pay_status']) ) && ( $_REQUEST['wh_pay_status'] != 'all') ) {

                $sql_where .= " AND ( ";

                // Check  firstly if we are selected some goup of payment status
                if ($_REQUEST['wh_pay_status'] == 'group_ok' ) {                // SUCCESS

                   $payment_status = get_payment_status_ok();

                   foreach ($payment_status as $label) {
                       $sql_where .= " ( bk.pay_status = '". $label ."' ) OR";
                   }
                   $sql_where = substr($sql_where, 0, -2);

                } else if ( ($_REQUEST['wh_pay_status'] == 'group_unknown' ) || (is_numeric($wh_pay_status)) || ($wh_pay_status == '') ) {     // UNKNOWN

                   $payment_status = get_payment_status_unknown();
                   foreach ($payment_status as $label) {
                       $sql_where .= " ( bk.pay_status = '". $label ."' ) OR";
                   }
                   //$sql_where = substr($sql_where, 0, -2);
                   $sql_where .= " ( bk.pay_status = '' ) OR ( bk.pay_status regexp '^[0-9]') ";

                } else if ($_REQUEST['wh_pay_status'] == 'group_pending' ){     // Pending

                   $payment_status = get_payment_status_pending();
                   foreach ($payment_status as $label) {
                       $sql_where .= " ( bk.pay_status = '". $label ."' ) OR";
                   }
                   $sql_where = substr($sql_where, 0, -2);

                } else if ($_REQUEST['wh_pay_status'] == 'group_failed' ) {     // Failed

                   $payment_status   = get_payment_status_error();
                   foreach ($payment_status as $label) {
                       $sql_where .= " ( bk.pay_status = '". $label ."' ) OR";
                   }
                   $sql_where = substr($sql_where, 0, -2);

                } else {                                                        // CUSTOM Payment Status
                    $sql_where .= " bk.pay_status = '" . $wh_pay_status . "' ";
                }

                $sql_where .= " ) ";
            }

            return $sql_where;
        }
        add_bk_filter('get_bklist_sql_paystatus', 'get_s_bklist_sql_paystatus');

        // Cost
        function get_s_bklist_sql_cost($blank, $wh_cost, $wh_cost2  ){
            $sql_where = '';

            if ( $wh_cost   !== '' )    $sql_where.=   " AND (  bk.cost >= " . $wh_cost . " ) ";
            if ( $wh_cost2  !== '' )    $sql_where.=   " AND (  bk.cost <= " . $wh_cost2 . " ) ";

            return $sql_where;
        }
        add_bk_filter('get_bklist_sql_cost', 'get_s_bklist_sql_cost');



    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  H T M L   &  E l e m e n t s   in   Booking   L i s t i n g  Table  ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        function wpdev_bk_listing_show_cost_btn( $booking_id, $bk_cost ){
          $currency = apply_bk_filter( 'get_currency_info' );
          ?>
          <div class="cost-fields-group">

                  <div class="field-currency"><?php echo $currency; ?></div>
                  <input type="text" id="booking_cost<?php echo $booking_id; ?>" name="booking_cost<?php echo $booking_id; ?>"
                         value="<?php echo $bk_cost; ?>" class="field-booking-cost"
                         onkeydown="javascript:document.getElementById('booking_save_cost<?php echo $booking_id; ?>').style.display='block';" />
                  <?php /** ?>
                  <a href="javascript:;" data-original-title="<?php _e('Send Email to visitor','wpdev-booking'); ?>"  rel="tooltip" class="tooltip_bottom"
                     onclick="javascript:payment_request_id = 5; document.getElementById(&quot;payment_request_reason&quot;).value = &quot;&quot;; openModalWindow(&quot;modal_content_payment_request&quot;);">
                        <img src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/ForwardEmails.png" style="margin:-4px 0px;">
                  </a> <?php /**/ ?>
                  <a href="javascript:;" data-original-title="<?php _e('Send payment request to visitor','wpdev-booking'); ?>"  rel="tooltip" class="tooltip_bottom"
                     id="send_payment_request<?php echo $booking_id; ?>"
                     onclick='javascript:payment_request_id = <?php echo $booking_id; ?>;
                         document.getElementById("payment_request_reason").value = "";
                         jQuery("#sendPaymentRequestModal").modal("show");'  >
                        <img src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/credit_card_24x24.png" style="margin:-4px 0px;">
                  </a>
          </div>
          <?php
        }
        add_bk_action( 'wpdev_bk_listing_show_cost_btn', 'wpdev_bk_listing_show_cost_btn');

        function wpdev_bk_listing_show_payment_status_btn($booking_id){
            ?>
            <a href="javascript:;" data-original-title="<?php _e('Payment status','wpdev-booking'); ?>"  rel="tooltip" class="tooltip_bottom payment_status_bk_link"
               onclick='javascript:
               if (document.getElementById(&quot;payment_status_row<?php echo $booking_id;?>&quot;).style.display==&quot;block&quot;)
                   document.getElementById(&quot;payment_status_row<?php echo $booking_id;?>&quot;).style.display=&quot;none&quot;;
               else
                   document.getElementById(&quot;payment_status_row<?php echo $booking_id;?>&quot;).style.display=&quot;block&quot;; '
               >
                <img src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/payment-status.png" style="width:16px; height:16px;margin:-2px 0px;"></a>
            <?php
        }
        add_bk_action( 'wpdev_bk_listing_show_payment_status_btn', 'wpdev_bk_listing_show_payment_status_btn');


        function wpdev_bk_listing_show_payment_status_cost_fields($booking_id, $bk_pay_status){
            $payment_status_titles = get_payment_status_titles();
            ?>
           <?php //BS : Save cost  ?>
           <div class="booking_row_modification_element" id="booking_save_cost<?php echo $booking_id; ?>" >
                      <input class="btn btn-primary btn-save-cost" value="<?php _e('Save cost'); ?>" type="button"
                             name="btn_booking_save_cost<?php echo $booking_id; ?>" id="btn_booking_save_cost<?php echo $booking_id; ?>"
                             onclick="javascript:
                                    document.getElementById('booking_save_cost<?php echo $booking_id; ?>').style.display='none';
                                    save_this_booking_cost(<?php echo $booking_id; ?>, document.getElementById('booking_cost<?php echo $booking_id; ?>').value );"
                       />
           </div>

           <?php  //BS : Payment status  ?>
           <div class="booking_row_modification_element_payment_status booking_row_modification_element " id="payment_status_row<?php echo $booking_id; ?>" >
                      <input class="btn btn-primary btn-save-cost" value="<?php _e('Change status'); ?>" type="button"
                             name="btn_booking_chnage_status<?php echo $booking_id; ?>" id="btn_booking_chnage_status<?php echo $booking_id; ?>"
                             onclick="javascript:
                                    document.getElementById('payment_status_row<?php echo $booking_id; ?>').style.display='none';
                                    chnage_booking_payment_status(<?php echo $booking_id; ?>,
                                    document.getElementById('select_payment_status_row<?php echo $booking_id; ?>').value,
                                    document.getElementById('select_payment_status_row<?php echo $booking_id; ?>').options[document.getElementById('select_payment_status_row<?php echo $booking_id; ?>').selectedIndex].text
                                );"
                       />
                <select id="select_payment_status_row<?php echo $booking_id; ?>" name="select_payment_status_row<?php echo $booking_id; ?>"
                        class="bkalignright" style="margin:0px 5px;">
                    <?php
                    $wpdevbk_selectors = $payment_status_titles ;
                    foreach ($wpdevbk_selectors as $kk=>$vv) { ?>
                    <option <?php if ( ( $bk_pay_status == $vv ) || ( (is_numeric($bk_pay_status)) && ($vv == '1') ) ) echo "selected='SELECTED'"; ?> value="<?php echo $vv; ?>"
                        ><?php echo $kk ; ?></option>
                    <?php } ?>
                </select>
           </div>
           <?php
        }
        add_bk_action( 'wpdev_bk_listing_show_payment_status_cost_fields', 'wpdev_bk_listing_show_payment_status_cost_fields');


        function wpdev_bk_listing_show_payment_label(  $is_paid, $pay_print_status , $real_payment_status_label){

            $css_payment_label = 'payment-label-' . wpdev_bk_get_type_of_payment_status($real_payment_status_label);
            if ($is_paid) { ?><span class="label label-payment-status label-success <?php echo $css_payment_label; ?> "><?php echo '<span style="font-size:07px;">'.__('Payment','wpdev-booking') .'</span> '.$pay_print_status ; ?></span><?php     }
            else          {               
                ?><span class="label label-payment-status <?php echo $css_payment_label; ?> "><?php  echo '<span style="font-size:07px;">'.__('Payment','wpdev-booking') .'</span> '. $pay_print_status; ; ?></span><?php
           }
        }
        add_bk_action( 'wpdev_bk_listing_show_payment_label', 'wpdev_bk_listing_show_payment_label');
?>
