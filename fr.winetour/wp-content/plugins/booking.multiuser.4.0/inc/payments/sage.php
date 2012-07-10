<?php
/*
This is COMMERCIAL SCRIPT
We are do not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  S e t t i n g s    f u n c t i o n s       ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    function wpdev_bk_payment_show_tab_in_top_settings_sage(){
        ?><a href="#"
           onclick="javascript:
                   jQuery('.visibility_container').css('display','none');
                   jQuery('#visibility_container_sage').css('display','block');
                   jQuery('.nav-tab').removeClass('booking-submenu-tab-selected');
                   jQuery(this).addClass('booking-submenu-tab-selected');"
               rel="tooltip" class="tooltip_bottom nav-tab  booking-submenu-tab <?php
                                     if ( get_bk_option( 'booking_sage_is_active' ) != 'On' ) echo ' booking-submenu-tab-disabled '; ?>"
                                     original-title="<?php _e('Integration of Sage payment system', 'wpdev-booking');?>">
            <?php _e('Sage', 'wpdev-booking');?>
            <input type="checkbox"  <?php if ( get_bk_option( 'booking_sage_is_active' ) == 'On' ) echo ' checked="CHECKED" '; ?>
                   name="sage_is_active_dublicated" id="sage_is_active_dublicated"
                   onchange="document.getElementById('sage_is_active').checked=this.checked;" >
        </a>
        <script type="text/javascript">
            jQuery(document).ready( function(){
                recheck_active_itmes_in_top_menu('sage_is_active',   'sage_is_active_dublicated');
            });
        </script>
        <?php
    }
    add_bk_action( 'wpdev_bk_payment_show_tab_in_top_settings', 'wpdev_bk_payment_show_tab_in_top_settings_sage');


    // Settings page for   S a g e
    function wpdev_bk_payment_show_settings_content_sage(){
            if ( isset( $_POST['sage_curency'] ) ) {
                  if (isset( $_POST['sage_is_active'] ))     $sage_is_active = 'On';
                  else                                       $sage_is_active = 'Off';
                  update_bk_option( 'booking_sage_is_active', $sage_is_active );
                  update_bk_option( 'booking_sage_subject', $_POST['sage_subject'] );
                  update_bk_option( 'booking_sage_test', $_POST['sage_test'] );
                  update_bk_option( 'booking_sage_order_Successful', $_POST['sage_order_Successful'] );
                  update_bk_option( 'booking_sage_order_Failed', $_POST['sage_order_Failed'] );
                  update_bk_option( 'booking_sage_vendor_name', $_POST['sage_vendor_name'] );
                  update_bk_option( 'booking_sage_encryption_password', $_POST['sage_encryption_password'] );
                  update_bk_option( 'booking_sage_curency', $_POST['sage_curency'] );
                  update_bk_option( 'booking_sage_transaction_type', $_POST['sage_transaction_type'] );
                 ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                 if (isset( $_POST['sage_is_description_show'] ))     $sage_is_description_show = 'On';
                 else                                                   $sage_is_description_show = 'Off';
                 update_bk_option( 'booking_sage_is_description_show' , $sage_is_description_show );

                 if (isset( $_POST['sage_is_auto_approve_cancell_booking'] ))     $sage_is_auto_approve_cancell_booking = 'On';
                 else                                                   $sage_is_auto_approve_cancell_booking = 'Off';
                 update_bk_option( 'booking_sage_is_auto_approve_cancell_booking' , $sage_is_auto_approve_cancell_booking );

            }

            $sage_is_active         =  get_bk_option( 'booking_sage_is_active' );
            $sage_subject           =  get_bk_option( 'booking_sage_subject' );
            $sage_test              =  get_bk_option( 'booking_sage_test' );
            $sage_order_Successful  =  get_bk_option( 'booking_sage_order_Successful' );
            $sage_order_Failed      =  get_bk_option( 'booking_sage_order_Failed' );
            $sage_vendor_name       =  get_bk_option( 'booking_sage_vendor_name' );
            $sage_encryption_password =  get_bk_option( 'booking_sage_encryption_password' );
            $sage_curency           =  get_bk_option( 'booking_sage_curency' );
            $sage_transaction_type  =  get_bk_option( 'booking_sage_transaction_type' );
            $sage_is_description_show = get_bk_option( 'booking_sage_is_description_show' );
            $sage_is_auto_approve_cancell_booking  = get_bk_option( 'booking_sage_is_auto_approve_cancell_booking' );
            ?>
            <div id="visibility_container_sage" class="visibility_container" style="display:none;">
                    <div class='meta-box'>
                      <div <?php $my_close_open_win_id = 'bk_settings_costs_sage_payment'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');"><br></div>
                            <h3 class='hndle'><span><?php _e('Sage payment customization', 'wpdev-booking'); ?></span></h3> <div class="inside">
                        <!--form  name="post_option_sage" action="" method="post" id="post_option_sage" -->
                            <center><?php printf(__('If you have no account for this system, please visit %s to create one. Simulator account emulates the Sage Pay account as well as a  Test and Live account.','wpdev-booking'), '<a href="https://support.sagepay.com/apply/RequestSimAccount.aspx"  target="_blank">sagepay.com</a>');?></center>
                            <table class="form-table settings-table">
                                <tbody>
                                    <tr valign="top">
                                        <th scope="row">
                                            <label for="sage_is_active" ><?php _e('Sage payment active', 'wpdev-booking'); ?>:</label>
                                        </th>
                                        <td>
                                            <input <?php if ($sage_is_active == 'On') echo "checked"; ?>  value="<?php echo $sage_is_active; ?>" name="sage_is_active" id="sage_is_active" type="checkbox"
                                                   onchange="document.getElementById('sage_is_active_dublicated').checked=this.checked;"
                                                                                                          />
                                            <span class="description"><?php _e(' Check this checkbox for using Sage payment.', 'wpdev-booking');?></span>
                                        </td>
                                    </tr>

                                    <tr valign="top">
                                      <th scope="row" >
                                        <label for="sage_subject" ><?php _e('Payment description', 'wpdev-booking'); ?>:</label><br/><br/>
                                        <span class="description"><?php printf(__('Enter the service name or the reason for the payment here.', 'wpdev-booking'),'<br/>','</b>');?></span>
                                      </th>
                                      <td>

                                            <div style="float:left;margin:10px 0px;width:100%;">
                                            <input id="sage_subject" name="sage_subject" class="darker-border"  type="text" maxlength="150" size="59" value="<?php echo $sage_subject; ?>" />

                                            </div>
                                            <div style="float:left;margin:10px 0px;width:375px;" class="code_description">
                                                <div  class="shortcode_help_section">
                                                  <span class="description">&nbsp;<?php printf(__(' Use these shortcodes for customization: ', 'wpdev-booking'));?></span><br/><br/>
                                                  <span class="description"><?php printf(__('%s[bookingname]%s - inserting name of booking resource, ', 'wpdev-booking'),'<code>','</code>');?></span><br/>
                                                  <span class="description"><?php printf(__('%s[dates]%s - inserting list of reserved dates ', 'wpdev-booking'),'<code>','</code>');?></span><br/>
                                                  <span class="description"><?php printf(__('%s[datescount]%s - inserting number of reserved dates ', 'wpdev-booking'),'<code>','</code>');?></span><br/>
                                                  <?php make_bk_action('show_additional_translation_shortcode_help'); ?>
                                                </div>
                                            </div>
                                          <div class="clear"></div>


                                      </td>
                                    </tr>

                                    <tr valign="top">
                                        <th scope="row">
                                            <label for="sage_is_description_show" ><?php _e('Show payment description', 'wpdev-booking'); ?>:</label><br/>
                                            <span class="description"><?php printf(__('on booking form near pay button', 'wpdev-booking'),'<br/>','</b>');?></span>
                                        </th>
                                        <td>
                                            <input <?php if ($sage_is_description_show == 'On') echo "checked";/**/ ?>  value="<?php echo $sage_is_description_show; ?>" name="sage_is_description_show" id="sage_is_description_show" type="checkbox" />
                                            <span class="description"><?php _e(' Check this checkbox if you want to show payment description near pay button.', 'wpdev-booking');?></span>
                                        </td>
                                    </tr>


                                    <tr valign="top">
                                      <th scope="row">
                                        <label for="sage_test" ><?php _e('Choose live or test environment', 'wpdev-booking'); ?>:</label>
                                      </th>
                                      <td>
                                         <select id="sage_test" name="sage_test">
                                            <option <?php if($sage_test == 'SIMULATOR') echo "selected"; ?> value="SIMULATOR"><?php _e('SIMULATOR', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_test == 'TEST') echo "selected"; ?> value="TEST"><?php _e('TEST', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_test == 'LIVE') echo "selected"; ?> value="LIVE"><?php _e('LIVE', 'wpdev-booking'); ?></option>
                                         </select>
                                         <span class="description"><?php printf(__('Select SIMULATOR for the Simulator expert system, TEST for the Test Server and LIVE in the live environment', 'wpdev-booking'),'<b>','</b>');?></span>
                                      </td>
                                    </tr>

                                    <tr valign="top">
                                      <th scope="row">
                                        <label for="sage_order_Successful" ><?php _e('Return URL after Successful order', 'wpdev-booking'); ?>:</label>
                                      </th>
                                      <td>
                                          <input value="<?php echo $sage_order_Successful; ?>" name="sage_order_Successful" id="sage_order_Successful" class="regular-text code" type="text" size="45" />
                                          <span class="description"><?php printf(__('Enter a return relative Successful URL. Sage will redirect visitors to this page after Successful Payment', 'wpdev-booking'),'<b>','</b>');?><br/>
                                           <?php printf(__('Please test this URL. Its have to be valid', 'wpdev-booking'),'<b>','</b>');?> <a href="<?php echo  $sage_order_Successful; ?>" target="_blank"><?php echo  $sage_order_Successful; ?></a></span>
                                      </td>
                                    </tr>

                                    <tr valign="top">
                                      <th scope="row">
                                        <label for="sage_order_Failed" ><?php _e('Return URL after Failed order', 'wpdev-booking'); ?>:</label>
                                      </th>
                                      <td>
                                          <input value="<?php echo $sage_order_Failed; ?>" name="sage_order_Failed" id="sage_order_Failed" class="regular-text code" type="text" size="45" />
                                          <span class="description"><?php printf(__('Enter a return relative Failed URL. Sage will redirect visitors to this page after Failed Payment', 'wpdev-booking'),'<b>','</b>');?><br/>
                                           <?php printf(__('Please test this URL. Its have to be valid', 'wpdev-booking'),'<b>','</b>');?> <a href="<?php echo   $sage_order_Failed; ?>" target="_blank"><?php  echo $sage_order_Failed; ?></a></span>
                                      </td>
                                    </tr>


                                    <tr valign="top">
                                        <th scope="row">
                                            <label for="sage_is_auto_approve_cancell_booking" ><?php _e('Auto approve/cancel of booking', 'wpdev-booking'); ?>:</label>
                                        </th>
                                        <td>
                                            <input <?php if ($sage_is_auto_approve_cancell_booking == 'On') echo "checked";/**/ ?>  value="<?php echo $sage_is_auto_approve_cancell_booking; ?>" name="sage_is_auto_approve_cancell_booking" id="sage_is_auto_approve_cancell_booking" type="checkbox" />
                                            <span class="description"><?php _e(' Check this checkbox and booking will be automaticaly approve or cancel, when visitor make successfully payment or when visitor make a payment cancellation. Warning this wont work if visitor leave the payment page', 'wpdev-booking');?></span>
                                        </td>
                                    </tr>




                                    <tr valign="top">
                                      <th scope="row">
                                        <label for="sage_vendor_name" ><?php _e('Vendor Name', 'wpdev-booking'); ?>:</label>
                                      </th>
                                      <td>
                                          <input value="<?php echo $sage_vendor_name; ?>" name="sage_vendor_name" id="sage_vendor_name" class="regular-text code" type="text" size="45" />
                                          <span class="description"><?php printf(__('Set this value to the Vendor Name assigned to you by Sage Pay or chosen when you applied.', 'wpdev-booking'),'<b>','</b>');?></span>
                                      </td>
                                    </tr>

                                    <tr valign="top">
                                      <th scope="row">
                                        <label for="sage_encryption_password" ><?php _e('XOR Encryption password', 'wpdev-booking'); ?>:</label>
                                      </th>
                                      <td>
                                          <input value="<?php echo $sage_encryption_password; ?>" name="sage_encryption_password" id="sage_encryption_password" class="regular-text code" type="text" size="45" />
                                          <span class="description"><?php printf(__('Set this value to the XOR Encryption password assigned to you by Sage Pay', 'wpdev-booking'),'<b>','</b>');?></span>
                                      </td>
                                    </tr>

                                    <tr valign="top">
                                      <th scope="row">
                                        <label for="sage_curency" ><?php _e('Choose Payment Currency', 'wpdev-booking'); ?>:</label>
                                      </th>
                                      <td>
                                         <select id="sage_curency" name="sage_curency">
                                            <option <?php if($sage_curency == 'USD') echo "selected"; ?> value="USD"><?php _e('U.S. Dollars', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'EUR') echo "selected"; ?> value="EUR"><?php _e('Euros', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'GBP') echo "selected"; ?> value="GBP"><?php _e('Pounds Sterling', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'JPY') echo "selected"; ?> value="JPY"><?php _e('Yen', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'AUD') echo "selected"; ?> value="AUD"><?php _e('Australian Dollars', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'CAD') echo "selected"; ?> value="CAD"><?php _e('Canadian Dollars', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'NZD') echo "selected"; ?> value="NZD"><?php _e('New Zealand Dollar', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'CHF') echo "selected"; ?> value="CHF"><?php _e('Swiss Franc', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'HKD') echo "selected"; ?> value="HKD"><?php _e('Hong Kong Dollar', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'SGD') echo "selected"; ?> value="SGD"><?php _e('Singapore Dollar', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'SEK') echo "selected"; ?> value="SEK"><?php _e('Swedish Krona', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'DKK') echo "selected"; ?> value="DKK"><?php _e('Danish Krone', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'PLN') echo "selected"; ?> value="PLN"><?php _e('Polish Zloty', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'NOK') echo "selected"; ?> value="NOK"><?php _e('Norwegian Krone', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'HUF') echo "selected"; ?> value="HUF"><?php _e('Hungarian Forint', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'CZK') echo "selected"; ?> value="CZK"><?php _e('Czech Koruna', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'ILS') echo "selected"; ?> value="ILS"><?php _e('Israeli Shekel', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'MXN') echo "selected"; ?> value="MXN"><?php _e('Mexican Peso', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'BRL') echo "selected"; ?> value="BRL"><?php _e('Brazilian Real (only for Brazilian users)', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'MYR') echo "selected"; ?> value="MYR"><?php _e('Malaysian Ringgits (only for Malaysian users)', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'PHP') echo "selected"; ?> value="PHP"><?php _e('Philippine Pesos', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'TWD') echo "selected"; ?> value="TWD"><?php _e('Taiwan New Dollars', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_curency == 'THB') echo "selected"; ?> value="THB"><?php _e('Thai Baht', 'wpdev-booking'); ?></option>
                                         </select>
                                         <span class="description"><?php printf(__('This is the currency for your visitors to make Payments', 'wpdev-booking'),'<b>','</b>');?></span>
                                      </td>
                                    </tr>

                                    <tr valign="top">
                                      <th scope="row">
                                        <label for="sage_transaction_type" ><?php _e('Transaction type', 'wpdev-booking'); ?>:</label>
                                      </th>
                                      <td>
                                         <select id="sage_transaction_type" name="sage_transaction_type">
                                            <option <?php if($sage_transaction_type == 'PAYMENT') echo "selected"; ?> value="PAYMENT"><?php _e('PAYMENT', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_transaction_type == 'DEFERRED') echo "selected"; ?> value="DEFERRED"><?php _e('DEFERRED', 'wpdev-booking'); ?></option>
                                            <option <?php if($sage_transaction_type == 'AUTHENTICATE') echo "selected"; ?> value="AUTHENTICATE"><?php _e('AUTHENTICATE', 'wpdev-booking'); ?></option>
                                         </select>
                                         <span class="description"><?php printf(__('This can be DEFERRED or AUTHENTICATE if your Sage Pay account supports those payment types', 'wpdev-booking'),'<b>','</b>');?></span>
                                      </td>
                                    </tr>

                                    <tr valign="top">
                                      <th scope="row" colspan="2">
                                          <span style="font-size:11px;color:#f00;" class="description"><?php printf(__('Please %s configure %s fields inside the billing form%s, this is necessary for %s  Payment System.', 'wpdev-booking'),'<b>',__('all','wpdev-booking'),'</b>', 'Sage');?></span>
                                      </th>
                                    </tr>
<?php
$strCustomerEMail      = "";

$strBillingFirstnames  = "John";
$strBillingSurname     = "Smith";
$strBillingAddress1    = "Street";
$strBillingAddress2    = "";
$strBillingCity        = "London";
$strBillingPostCode    = "32432";
$strBillingCountry     = "UK";
$strBillingState       = "";
$strBillingPhone       = "";

$strConnectTo="SIMULATOR";                                  //Set to SIMULATOR for the Simulator expert system, TEST for the Test Server and LIVE in the live environment
$orderSuccessful = 'good.php';
$orderFailed = 'bad.php';
$strYourSiteFQDN= site_url() . "/";              //"http://wp/";  // IMPORTANT.  Set the strYourSiteFQDN value to the Fully Qualified Domain Name of your server. **** This should start http:// or https:// and should be the name by which our servers can call back to yours **** i.e. it MUST be resolvable externally, and have access granted to the Sage Pay servers **** examples would be https://www.mysite.com or http://212.111.32.22/ **** NOTE: You should leave the final / in place.
//TODO: Define from settings page
$strVendorName="";                                 // Set this value to the Vendor Name assigned to you by Sage Pay or chosen when you applied **/
$strEncryptionPassword="";                  // Set this value to the XOR Encryption password assigned to you by Sage Pay **/
$strCurrency="USD";                                         // Set this to indicate the currency in which you wish to trade. You will need a merchant number in this currency **/
$strTransactionType="PAYMENT";                              // This can be DEFERRED or AUTHENTICATE if your Sage Pay account supports those payment types **/
$strPartnerID="";                                           // Optional setting. If you are a Sage Pay Partner and wish to flag the transactions with your unique partner id set it here. **/
$bSendEMail=0;                                              // Optional setting. ** 0 = Do not send either customer or vendor e-mails, ** 1 = Send customer and vendor e-mails if address(es) are provided(DEFAULT). ** 2 = Send Vendor Email but not Customer Email. If you do not supply this field, 1 is assumed and e-mails are sent if addresses are provided.
$strVendorEMail="";                                         // Optional setting. Set this to the mail address which will receive order confirmations and failures
$strProtocol="2.23";

?>

                                </tbody>
                            </table>

                            <div class="clear" style="height:10px;"></div>
                            <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="sagesubmit"/>
                            <div class="clear" style="height:10px;"></div>

                        <!--/form-->
                   </div> </div> </div>
            </div>
              <?php
    }
    add_bk_action( 'wpdev_bk_payment_show_settings_content', 'wpdev_bk_payment_show_settings_content_sage');



    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //   P a y m e n t    f o r m    d e f i n i t i o n      //////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function wpdev_bk_define_payment_form_sage($blank, $booking_id, $summ,$bk_title, $booking_days_count, $booking_type, $bkform, $wp_nonce, $is_deposit ){
        $output = '';
        if( (get_bk_option( 'booking_sage_is_active' ) == 'On')   ) {
                // Need to set status of bookings
                // Pending, Aproved, Payed

                $form_fields = get_form_content ($bkform, $booking_type);
                $form_fields = $form_fields['_all_'];

                $sage_is_active         =  get_bk_option( 'booking_sage_is_active' );
                if ($sage_is_active != 'On')  return '';

                $sage_subject           =  get_bk_option( 'booking_sage_subject' );
                $sage_subject           =  apply_bk_filter('wpdev_check_for_active_language', $sage_subject );
                $sage_subject = str_replace('[bookingname]',$bk_title[0]->title,$sage_subject);
                $sage_subject = str_replace('[dates]',$booking_days_count,$sage_subject); //$paypal_subject .= ' Booking type: ' . $bk_title[0]->title . '. For period: ' . $booking_days_count;
                    $my_d_c = explode(',', $booking_days_count);
                    $my_d_c = count($my_d_c);
                    $sage_subject = str_replace('[datescount]',$my_d_c,$sage_subject);

                $subject_payment = $sage_subject;



                $sage_test              =  get_bk_option( 'booking_sage_test' );
                $sage_order_Successful  =  WPDEV_BK_PLUGIN_URL .'/'. WPDEV_BK_PLUGIN_FILENAME . '?payed_booking=' . $booking_id .'&wp_nonce=' . $wp_nonce . '&pay_sys=sage&stats=OK' ;   //get_bk_option( 'booking_sage_order_Successful' );
                $sage_order_Failed      =  WPDEV_BK_PLUGIN_URL .'/'. WPDEV_BK_PLUGIN_FILENAME . '?payed_booking=' . $booking_id .'&wp_nonce=' . $wp_nonce . '&pay_sys=sage&stats=FAILED' ;   //get_bk_option( 'booking_sage_order_Failed' );
                $sage_vendor_name       =  get_bk_option( 'booking_sage_vendor_name' );
                $sage_encryption_password =  get_bk_option( 'booking_sage_encryption_password' );
                $sage_curency           =  get_bk_option( 'booking_sage_curency' );
                $sage_transaction_type  =  get_bk_option( 'booking_sage_transaction_type' );

                if ( empty( $sage_test ) ) return '';
                if ( empty( $sage_order_Successful ) ) return '';
                if ( empty( $sage_order_Failed ) ) return '';
                if ( empty( $sage_vendor_name ) ) return '';
                if ( empty( $sage_encryption_password ) ) return '';
                if ( empty( $sage_curency ) ) return '';
                if ( empty( $sage_transaction_type ) ) return '';

                // Get all fields for biling info
                $sage_billing_customer_email  = (string) trim(get_bk_option( 'booking_billing_customer_email' ) . $booking_type );
                $sage_billing_firstnames      = (string) trim( get_bk_option( 'booking_billing_firstnames' ) . $booking_type );
                $sage_billing_surname         = (string) trim( get_bk_option( 'booking_billing_surname' ) . $booking_type );
                $sage_billing_address1        = (string) trim( get_bk_option( 'booking_billing_address1' ) . $booking_type) ;
                $sage_billing_city            = (string) trim( get_bk_option( 'booking_billing_city' ) . $booking_type );
                $sage_billing_country         = (string) trim( get_bk_option( 'booking_billing_country' ) . $booking_type );
                $sage_billing_post_code       = (string) trim( get_bk_option( 'booking_billing_post_code' ) . $booking_type );

                // Check if all fields set, if no so then return empty
                if ( isset($form_fields[$sage_billing_customer_email]) === false ) return '';
                if ( isset($form_fields[$sage_billing_firstnames]) === false ) return '';
                if ( isset($form_fields[$sage_billing_surname]) === false ) return '';
                if ( isset($form_fields[$sage_billing_address1]) === false ) return '';
                if ( isset($form_fields[$sage_billing_city]) === false ) return '';
                if ( isset($form_fields[$sage_billing_country]) === false ) return '';
                if ( isset($form_fields[$sage_billing_post_code]) === false ) return '';


                    $strConnectTo=$sage_test;                                   //Set to SIMULATOR for the Simulator expert system, TEST for the Test Server and LIVE in the live environment
                    $orderSuccessful = $sage_order_Successful;
                    $orderFailed     = $sage_order_Failed ;
                    $strYourSiteFQDN= site_url() . "/";              //"http://wp/";  // IMPORTANT.  Set the strYourSiteFQDN value to the Fully Qualified Domain Name of your server. **** This should start http:// or https:// and should be the name by which our servers can call back to yours **** i.e. it MUST be resolvable externally, and have access granted to the Sage Pay servers **** examples would be https://www.mysite.com or http://212.111.32.22/ **** NOTE: You should leave the final / in place.


                    $strVendorName=$sage_vendor_name;                           // Set this value to the Vendor Name assigned to you by Sage Pay or chosen when you applied **/
                    $strEncryptionPassword=$sage_encryption_password;           // Set this value to the XOR Encryption password assigned to you by Sage Pay **/
                    $strCurrency=$sage_curency;                                 // Set this to indicate the currency in which you wish to trade. You will need a merchant number in this currency **/
                    $strTransactionType=$sage_transaction_type;                 // This can be DEFERRED or AUTHENTICATE if your Sage Pay account supports those payment types **/
                    $strPartnerID="";                                           // Optional setting. If you are a Sage Pay Partner and wish to flag the transactions with your unique partner id set it here. **/
                    $bSendEMail=0;                                              // Optional setting. ** 0 = Do not send either customer or vendor e-mails, ** 1 = Send customer and vendor e-mails if address(es) are provided(DEFAULT). ** 2 = Send Vendor Email but not Customer Email. If you do not supply this field, 1 is assumed and e-mails are sent if addresses are provided.
                    $strVendorEMail="";                                         // Optional setting. Set this to the mail address which will receive order confirmations and failures
                    $strProtocol="2.23";

                    if ($strConnectTo=="LIVE")      $strPurchaseURL="https://live.sagepay.com/gateway/service/vspform-register.vsp";
                    elseif ($strConnectTo=="TEST")  $strPurchaseURL="https://test.sagepay.com/gateway/service/vspform-register.vsp";
                    else                            $strPurchaseURL="https://test.sagepay.com/simulator/vspformgateway.asp";


//TODO: get from booking form (or from other form ?



$strCustomerEMail      = $form_fields[$sage_billing_customer_email] ;

$strBillingFirstnames  = $form_fields[$sage_billing_firstnames];
$strBillingSurname     = $form_fields[$sage_billing_surname];
$strBillingAddress1    = $form_fields[$sage_billing_address1];
$strBillingAddress2    = "";
$strBillingCity        = $form_fields[$sage_billing_city];
$strBillingPostCode    = $form_fields[$sage_billing_post_code];
$strBillingCountry     = $form_fields[$sage_billing_country];
$strBillingState       = "";
$strBillingPhone       = "";

                        $bIsDeliverySame       = true;//$_SESSION["bIsDeliverySame"];
                        if ($bIsDeliverySame == true) {
                            $strDeliveryFirstnames = $strBillingFirstnames;
                            $strDeliverySurname    = $strBillingSurname;
                            $strDeliveryAddress1   = $strBillingAddress1;
                            $strDeliveryAddress2   = $strBillingAddress2;
                            $strDeliveryCity       = $strBillingCity;
                            $strDeliveryPostCode   = $strBillingPostCode;
                            $strDeliveryCountry    = $strBillingCountry;
                            $strDeliveryState      = $strBillingState;
                            $strDeliveryPhone      = $strBillingPhone;
                        } else {
                            $strDeliveryFirstnames = "";//$_SESSION["strDeliveryFirstnames"];
                            $strDeliverySurname    = "";//$_SESSION["strDeliverySurname"];
                            $strDeliveryAddress1   = "";//$_SESSION["strDeliveryAddress1"];
                            $strDeliveryAddress2   = "";//$_SESSION["strDeliveryAddress2"];
                            $strDeliveryCity       = "";//$_SESSION["strDeliveryCity"];
                            $strDeliveryPostCode   = "";//$_SESSION["strDeliveryPostCode"];
                            $strDeliveryCountry    = "";//$_SESSION["strDeliveryCountry"];
                            $strDeliveryState      = "";//$_SESSION["strDeliveryState"];
                            $strDeliveryPhone      = "";//$_SESSION["strDeliveryPhone"];
                        }
                        $intRandNum = rand(0,32000)*rand(0,32000);                  // Okay, build the crypt field for Form using the information in our session ** First we need to generate a unique VendorTxCode for this transaction **  We're using VendorName, time stamp and a random element.  You can use different methods if you wish *  but the VendorTxCode MUST be unique for each transaction you send to Server
                        $strVendorTxCode=$strVendorName . $intRandNum;

                        $subject_payment = str_replace(':','.',$subject_payment);
                        $summ = str_replace(',','.',$summ);
                        $strBasket = '1:'.$subject_payment.':::::'.$summ;

                        $strPost="VendorTxCode=" . $strVendorTxCode;                                    // Now to build the Form crypt field.  For more details see the Form Protocol 2.23 As generated above

                        if (strlen($strPartnerID) > 0) $strPost=$strPost . "&ReferrerID=" . $strPartnerID;      // Optional: If you are a Sage Pay Partner and wish to flag the transactions with your unique partner id, it should be passed here
                        $strPost=$strPost . "&Amount=" . number_format($summ,2); // Formatted to 2 decimal places with leading digit
                        $strPost=$strPost . "&Currency=" . $strCurrency;
                        $strPost=$strPost . "&Description=" . substr($subject_payment,0,100);                         // Up to 100 chars of free format description
                        $strPost=$strPost . "&SuccessURL=" . /*$strYourSiteFQDN .*/ $orderSuccessful  ;    // The SuccessURL is the page to which Form returns the customer if the transaction is successful. You can change this for each transaction, perhaps passing a session ID or state flag if you wish
                        $strPost=$strPost . "&FailureURL=" . /*$strYourSiteFQDN .*/ $orderFailed      ;    // The FailureURL is the page to which Form returns the customer if the transaction is unsuccessful You can change this for each transaction, perhaps passing a session ID or state flag if you wish
                        $strPost=$strPost . "&CustomerName=" . $strBillingFirstnames . " " . $strBillingSurname;        // This is an Optional setting. Here we are just using the Billing names given.
                        $strPost=$strPost . "&SendEMail=0";
                        /* Email settings:
                        ** Flag 'SendEMail' is an Optional setting.
                        ** 0 = Do not send either customer or vendor e-mails,
                        ** 1 = Send customer and vendor e-mails if address(es) are provided(DEFAULT).
                        ** 2 = Send Vendor Email but not Customer Email. If you do not supply this field, 1 is assumed and e-mails are sent if addresses are provided. **

                        */

$strPost=$strPost . "&BillingFirstnames=" . $strBillingFirstnames;              // Billing Details:
$strPost=$strPost . "&BillingSurname=" . $strBillingSurname;
$strPost=$strPost . "&BillingAddress1=" . $strBillingAddress1;
if (strlen($strBillingAddress2) > 0) $strPost=$strPost . "&BillingAddress2=" . $strBillingAddress2;
$strPost=$strPost . "&BillingCity=" . $strBillingCity;
$strPost=$strPost . "&BillingPostCode=" . $strBillingPostCode;
$strPost=$strPost . "&BillingCountry=" . $strBillingCountry;
if (strlen($strBillingState) > 0) $strPost=$strPost . "&BillingState=" . $strBillingState;
if (strlen($strBillingPhone) > 0) $strPost=$strPost . "&BillingPhone=" . $strBillingPhone;


$strPost=$strPost . "&DeliveryFirstnames=" . $strDeliveryFirstnames;            // Delivery Details:
$strPost=$strPost . "&DeliverySurname=" . $strDeliverySurname;
$strPost=$strPost . "&DeliveryAddress1=" . $strDeliveryAddress1;
if (strlen($strDeliveryAddress2) > 0) $strPost=$strPost . "&DeliveryAddress2=" . $strDeliveryAddress2;
$strPost=$strPost . "&DeliveryCity=" . $strDeliveryCity;
$strPost=$strPost . "&DeliveryPostCode=" . $strDeliveryPostCode;
$strPost=$strPost . "&DeliveryCountry=" . $strDeliveryCountry;
if (strlen($strDeliveryState) > 0) $strPost=$strPost . "&DeliveryState=" . $strDeliveryState;
if (strlen($strDeliveryPhone) > 0) $strPost=$strPost . "&DeliveryPhone=" . $strDeliveryPhone;


                        $strPost=$strPost . "&Basket=" . $strBasket; // As created above
                        $strPost=$strPost . "&AllowGiftAid=0";                                          // For charities registered for Gift Aid, set to 1 to display the Gift Aid check box on the payment pages
                        if ($strTransactionType!=="AUTHENTICATE") $strPost=$strPost . "&ApplyAVSCV2=0"; // Allow fine control over AVS/CV2 checks and rules by changing this value. 0 is Default. It can be changed dynamically, per transaction, if you wish.  See the Server Protocol document
                        $strPost=$strPost . "&Apply3DSecure=0";                                         // Allow fine control over 3D-Secure checks and rules by changing this value. 0 is Default. It can be changed dynamically, per transaction, if you wish.  See the Form Protocol document

                        $strCrypt = wpdev_bk_sage_base64Encode(wpdev_sage_simpleXor($strPost,$strEncryptionPassword));           // Encrypt the plaintext string for inclusion in the hidden field


                        $output = '<div style="width:100%;clear:both;margin-top:20px;"></div><div class="sage_div" style="text-align:left;clear:both;">';   // This form is all that is required to submit the payment information to the system -->
                        $output .= '<form action=\"'.$strPurchaseURL.'\" method=\"POST\" id=\"SagePayForm\" name=\"SagePayForm\" style=\"text-align:left;\" class=\"booking_SagePayForm\">';
                        $output .= '<input type=\"hidden\" name=\"navigate\" value=\"\" />';
                        $output .= '<input type=\"hidden\" name=\"VPSProtocol\" value=\"'.$strProtocol.'\">';
                        $output .= '<input type=\"hidden\" name=\"TxType\" value=\"'.$strTransactionType.'\">';
                        $output .= '<input type=\"hidden\" name=\"Vendor\" value=\"'.$strVendorName.'\">';
                        $output .= '<input type=\"hidden\" name=\"Crypt\" value=\"'.$strCrypt.'\">';
                        $is_show_it = get_bk_option( 'booking_sage_is_description_show' );
                        if ($is_show_it == 'On')
                            $output .= $sage_subject . '<br />';

                        
                        $cost_currency = apply_bk_filter('get_currency_info', 'sage');
                        $summ_show = wpdev_bk_cost_number_format ( $summ );
                        if ($is_deposit) $cost__title = __('Deposit', 'wpdev-booking')." : ";
                        else             $cost__title = __('Cost', 'wpdev-booking')." : ";

                        if ($cost_currency == $sage_curency) $cost_summ_with_title = "<strong>".$cost__title. $summ_show ." " . $sage_curency ."</strong><br/>";
                        else                                 $cost_summ_with_title = "<strong>".$cost__title. $cost_currency ." " . $summ_show ."</strong><br />";

                        $output .= $cost_summ_with_title;
                        /*
                        if ($is_deposit) {
                            $today_day = date('m.d.Y')  ;
                            $cost_summ_with_title .= ' ('  . $today_day .')';
                            make_bk_action('wpdev_make_update_of_remark' , $booking_id , $cost_summ_with_title , true );
                        }/**/


                        $output .= '<input type=\"submit\" name=\"submitsagebutton\" value=\"'.__('Pay now','wpdev-booking').'\" class=\"button\">';
                        $output .= "<br/><span style=\"font-size:11px;\">".sprintf(__('Pay using %s payment service', 'wpdev-booking'), '<a href="http://www.sagepay.com/" target="_blank">Sage Pay</a>').".</span>";
                        //$output .= '<a href=\"javascript:SagePayForm.submit();\" title=\"Proceed to Form registration\"><img src=\"images/proceed.gif\" alt=\"Proceed to Form registration\" border=\"0\"></a>';
                        $output .= '</form></div>';


        }
        return $output ;

    }
    add_bk_filter('wpdev_bk_define_payment_form_sage', 'wpdev_bk_define_payment_form_sage');


                // Base 64 Encoding function ** PHP does it natively but just for consistency and ease of maintenance, let's declare our own function
                function wpdev_bk_sage_base64Encode($plain) {
                  // Initialise output variable
                  $output = "";

                  // Do encoding
                  $output = base64_encode($plain);

                  // Return the result
                  return $output;
                }

                //  The SimpleXor encryption algorithm **  NOTE: This is a placeholder really.  Future releases of Form will use AES or TwoFish.  Proper encryption **  This simple function and the Base64 will deter script kiddies and prevent the "View Source" type tampering **  It won't stop a half decent hacker though, but the most they could do is change the amount field to something **  else, so provided the vendor checks the reports and compares amounts, there is no harm done.  It's still **  more secure than the other PSPs who don't both encrypting their forms at all
                function wpdev_sage_simpleXor($InString, $Key) {
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



    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //   A C T I V A T I O N   A N D   D E A C T I V A T I O N    O F   T H I S   P L U G I N    ///////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Activate
    function wpdev_bk_payment_activate_system_sage() {
        global $wpdb;

        // Sage Account /////////////////////////////////////////////////////////////////////////////////////////////
        add_bk_option( 'booking_sage_is_active', 'Off' );
        add_bk_option( 'booking_sage_subject', sprintf(__('Payment for booking of the %s for days: %s' , 'wpdev-booking'),'[bookingname]','[dates]'));
        add_bk_option( 'booking_sage_test', 'SIMULATOR' );
        add_bk_option( 'booking_sage_order_Successful', site_url() );
        add_bk_option( 'booking_sage_order_Failed', site_url() );
        if ( wpdev_bk_is_this_demo() ) {
            add_bk_option( 'booking_sage_vendor_name', 'wpdevelop' );
            add_bk_option( 'booking_sage_encryption_password', 'FfCDQjLiM524VtE7' );
            add_bk_option( 'booking_sage_curency', 'USD' );
            add_bk_option( 'booking_sage_transaction_type', 'PAYMENT' );
        } else {
            add_bk_option( 'booking_sage_vendor_name', '' );
            add_bk_option( 'booking_sage_encryption_password', '' );
            add_bk_option( 'booking_sage_curency', '' );
            add_bk_option( 'booking_sage_transaction_type', '' );
        }
        add_bk_option( 'booking_sage_is_description_show', 'Off' );
        add_bk_option( 'booking_sage_is_auto_approve_cancell_booking' , 'Off' );

    }
    add_bk_action( 'wpdev_bk_payment_activate_system', 'wpdev_bk_payment_activate_system_sage');


    // Activate
    function wpdev_bk_payment_deactivate_system_sage() {
        global $wpdb;
        // Sage account
        delete_bk_option( 'booking_sage_is_active' );
        delete_bk_option( 'booking_sage_subject' );
        delete_bk_option( 'booking_sage_test' );
        delete_bk_option( 'booking_sage_order_Successful' );
        delete_bk_option( 'booking_sage_order_Failed' );
        delete_bk_option( 'booking_sage_vendor_name' );
        delete_bk_option( 'booking_sage_encryption_password' );
        delete_bk_option( 'booking_sage_curency' );
        delete_bk_option( 'booking_sage_transaction_type' );
        delete_bk_option( 'booking_sage_is_description_show' );
        delete_bk_option( 'booking_sage_is_auto_approve_cancell_booking' );

    }
    add_bk_action( 'wpdev_bk_payment_deactivate_system', 'wpdev_bk_payment_deactivate_system_sage');


?>
