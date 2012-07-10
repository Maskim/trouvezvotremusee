<?php
/*
This is COMMERCIAL SCRIPT
We are do not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  S e t t i n g s    f u n c t i o n s       ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    function wpdev_bk_payment_show_tab_in_top_settings_ipay88(){
        ?>
            <a href="#"
               onclick="javascript:
                       jQuery('.visibility_container').css('display','none');
                       jQuery('#visibility_container_ipay88').css('display','block');
                       jQuery('.nav-tab').removeClass('booking-submenu-tab-selected');
                       jQuery(this).addClass('booking-submenu-tab-selected');"
               rel="tooltip" class="tooltip_bottom nav-tab  booking-submenu-tab <?php
                    if ( get_bk_option( 'booking_ipay88_is_active' ) != 'On' ) echo ' booking-submenu-tab-disabled '; ?>"
               original-title="<?php _e('Integration of iPay88 payment system', 'wpdev-booking');?>" >
             <?php _e('iPay88', 'wpdev-booking');?>
             <input type="checkbox" <?php if ( get_bk_option( 'booking_ipay88_is_active' ) == 'On' ) echo ' checked="CHECKED" '; ?>
                    name="ipay88_is_active_dublicated" id="ipay88_is_active_dublicated"
                       onchange="document.getElementById('ipay88_is_active').checked=this.checked;" >
            </a>
        <script type="text/javascript">
            jQuery(document).ready( function(){
                recheck_active_itmes_in_top_menu('ipay88_is_active', 'ipay88_is_active_dublicated');
            });
        </script>           
        <?php
    }
    add_bk_action( 'wpdev_bk_payment_show_tab_in_top_settings', 'wpdev_bk_payment_show_tab_in_top_settings_ipay88');


    // Settings for iPay88
    function wpdev_bk_payment_show_settings_content_ipay88(){


        if( isset($_POST['ipay88_curency']) ) {
                 ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                 if (isset( $_POST['ipay88_is_active'] ))     $ipay88_is_active = 'On';
                 else                                         $ipay88_is_active = 'Off';
                 update_bk_option( 'booking_ipay88_is_active' , $ipay88_is_active );

                 update_bk_option( 'booking_ipay88_merchant_code' , $_POST['ipay88_merchant_code'] );
                 update_bk_option( 'booking_ipay88_merchant_key' , $_POST['ipay88_merchant_key'] );

                 if (isset( $_POST['ipay88_is_auto_approve_cancell_booking'] ))     $ipay88_is_auto_approve_cancell_booking = 'On';
                 else                                                               $ipay88_is_auto_approve_cancell_booking = 'Off';
                 update_bk_option( 'booking_ipay88_is_auto_approve_cancell_booking' , $ipay88_is_auto_approve_cancell_booking );
                 update_bk_option( 'booking_ipay88_return_url' , $_POST['ipay88_return_url'] );
                 update_bk_option( 'booking_ipay88_cancel_return_url' , $_POST['ipay88_cancel_return_url'] );
                 update_bk_option( 'booking_ipay88_curency'  , $_POST['ipay88_curency'] );
                 update_bk_option( 'booking_ipay88_subject' , $_POST['ipay88_subject'] );

                 if (isset( $_POST['ipay88_is_description_show'] ))     $ipay88_is_description_show = 'On';
                 else                                                   $ipay88_is_description_show = 'Off';
                 update_bk_option( 'booking_ipay88_is_description_show' , $ipay88_is_description_show );

        }

        $ipay88_is_active     = get_bk_option( 'booking_ipay88_is_active' );
        $ipay88_merchant_code = get_bk_option( 'booking_ipay88_merchant_code' );
        $ipay88_merchant_key = get_bk_option( 'booking_ipay88_merchant_key' );
        $ipay88_curency = get_bk_option( 'booking_ipay88_curency' );
        $ipay88_subject = get_bk_option( 'booking_ipay88_subject' );
        $ipay88_is_description_show = get_bk_option( 'booking_ipay88_is_description_show' );


        $ipay88_is_auto_approve_cancell_booking =  get_bk_option( 'booking_ipay88_is_auto_approve_cancell_booking' );
        $ipay88_return_url                      =  get_bk_option( 'booking_ipay88_return_url' );
        $ipay88_cancel_return_url               =  get_bk_option( 'booking_ipay88_cancel_return_url' );

            /*
            Merchant Code
            Payment Method
            Merchant Reference Number

            Signature (refer to 1.1.11)

            Currency

            Payment Amount
            Response URL
            Product Description
            Merchant Remark


            Customer Name
            Customer Email
            Customer Contact
            /**/



        ?>
            <div id="visibility_container_ipay88" class="visibility_container" style="display:none;">

        <div class='meta-box'>
          <div <?php $my_close_open_win_id = 'bk_settings_costs_ipay88_payment'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');"><br></div>
                <h3 class='hndle'><span><?php _e('iPay88 customization', 'wpdev-booking'); ?></span></h3> <div class="inside">

            <!--form  name="post_option" action="" method="post" id="post_option" -->

                <table class="form-table settings-table">
                    <tbody>

                        <tr valign="top">
                            <th scope="row">
                                <label for="ipay88_is_active" ><?php _e('iPay88 active', 'wpdev-booking'); ?>:</label>
                            </th>
                            <td>
                                <input <?php if ($ipay88_is_active == 'On') echo "checked";/**/ ?>  value="<?php echo $ipay88_is_active; ?>" name="ipay88_is_active" id="ipay88_is_active" type="checkbox"
                                                   onchange="document.getElementById('ipay88_is_active_dublicated').checked=this.checked;"
                                                                                                          />
                                <span class="description"><?php _e(' Check this checkbox to use iPay88.', 'wpdev-booking');?></span>
                            </td>
                        </tr>

                        <tr valign="top">
                          <th scope="row">
                            <label for="ipay88_merchant_code" ><?php _e('Merchant Code', 'wpdev-booking'); ?>:</label>
                          </th>
                          <td>
                              <input value="<?php echo $ipay88_merchant_code; ?>"
                                     name="ipay88_merchant_code"
                                     id="ipay88_merchant_code" class="regular-text code" type="text" size="45" />
                              <span class="description"><?php printf(__('Enter your iPay88 Merchant Code.', 'wpdev-booking'),'<b>','</b>');?></span>
                          </td>
                        </tr>

                        <tr valign="top">
                          <th scope="row">
                            <label for="ipay88_merchant_code" ><?php _e('Merchant Key', 'wpdev-booking'); ?>:</label>
                          </th>
                          <td>
                              <input value="<?php echo $ipay88_merchant_key; ?>"
                                     name="ipay88_merchant_key"
                                     id="ipay88_merchant_key" class="regular-text code" type="text" size="45" />
                              <span class="description"><?php printf(__('Enter your iPay88 Merchant Key.', 'wpdev-booking'),'<b>','</b>');?></span>
                          </td>
                        </tr>
<?php /*
Australian Dollar  AUD
Canadian Dollar CAD
Euro  EUR
Hong Kong Dollar HKD
Indian Rupee  INR
Indonesian Rupiah IDR
Malaysian Ringgit  MYR
Philippines Peso PHP
Pound Sterling  GBP
Singapore Dollar SGD
Thai Baht  THB
United States Dollar USD
New Taiwan dollar  TWD



United States Dollar USD
/**/
?>
                        <tr valign="top">
                          <th scope="row">
                            <label for="ipay88_curency" ><?php _e('Choose Payment Currency', 'wpdev-booking'); ?>:</label>
                          </th>
                          <td>
                             <select id="ipay88_curency" name="ipay88_curency">
                                <option <?php if($ipay88_curency == 'MYR') echo "selected"; ?> value="MYR"><?php _e('Malaysian Ringgit', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'USD') echo "selected"; ?> value="USD"><?php _e('U.S. Dollars', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'PHP') echo "selected"; ?> value="PHP"><?php _e('Philippines Peso', 'wpdev-booking'); ?></option>



                                <!--option <?php if($ipay88_curency == 'EUR') echo "selected"; ?> value="EUR"><?php _e('Euros', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'GBP') echo "selected"; ?> value="GBP"><?php _e('Pounds Sterling', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'JPY') echo "selected"; ?> value="JPY"><?php _e('Yen', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'AUD') echo "selected"; ?> value="AUD"><?php _e('Australian Dollars', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'CAD') echo "selected"; ?> value="CAD"><?php _e('Canadian Dollars', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'NZD') echo "selected"; ?> value="NZD"><?php _e('New Zealand Dollar', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'CHF') echo "selected"; ?> value="CHF"><?php _e('Swiss Franc', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'HKD') echo "selected"; ?> value="HKD"><?php _e('Hong Kong Dollar', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'SGD') echo "selected"; ?> value="SGD"><?php _e('Singapore Dollar', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'SEK') echo "selected"; ?> value="SEK"><?php _e('Swedish Krona', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'DKK') echo "selected"; ?> value="DKK"><?php _e('Danish Krone', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'PLN') echo "selected"; ?> value="PLN"><?php _e('Polish Zloty', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'NOK') echo "selected"; ?> value="NOK"><?php _e('Norwegian Krone', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'HUF') echo "selected"; ?> value="HUF"><?php _e('Hungarian Forint', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'CZK') echo "selected"; ?> value="CZK"><?php _e('Czech Koruna', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'ILS') echo "selected"; ?> value="ILS"><?php _e('Israeli Shekel', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'MXN') echo "selected"; ?> value="MXN"><?php _e('Mexican Peso', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'BRL') echo "selected"; ?> value="BRL"><?php _e('Brazilian Real (only for Brazilian users)', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'MYR') echo "selected"; ?> value="MYR"><?php _e('Malaysian Ringgits (only for Malaysian users)', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'PHP') echo "selected"; ?> value="PHP"><?php _e('Philippine Pesos', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'TWD') echo "selected"; ?> value="TWD"><?php _e('Taiwan New Dollars', 'wpdev-booking'); ?></option>
                                <option <?php if($ipay88_curency == 'THB') echo "selected"; ?> value="THB"><?php _e('Thai Baht', 'wpdev-booking'); ?></option-->
                             </select>
                             <span class="description"><?php printf(__('This is the currency for your visitors to make Payments', 'wpdev-booking'),'<b>','</b>');?></span>
                          </td>
                        </tr>





                        <tr valign="top">
                          <th scope="row" >
                            <label for="ipay88_subject" ><?php _e('Payment description', 'wpdev-booking'); ?>:</label><br/><br/>
                            <span class="description"><?php printf(__('Enter the service name or the reason for the payment here.', 'wpdev-booking'),'<br/>','</b>');?></span>
                          </th>
                          <td>


                    <div style="float:left;margin:10px 0px;width:100%;">
                    <input id="ipay88_subject" name="ipay88_subject" class="darker-border"  type="text" maxlength="70" size="59" value="<?php echo $ipay88_subject; ?>" />

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
                                <label for="ipay88_is_description_show" ><?php _e('Show payment description', 'wpdev-booking'); ?>:</label><br/>
                                <span class="description"><?php printf(__('on booking form near pay button', 'wpdev-booking'),'<br/>','</b>');?></span>
                            </th>
                            <td>
                                <input <?php if ($ipay88_is_description_show == 'On') echo "checked";/**/ ?>  value="<?php echo $ipay88_is_description_show; ?>" name="ipay88_is_description_show" id="ipay88_is_description_show" type="checkbox" />
                                <span class="description"><?php _e(' Check this checkbox if you want to show payment description near pay button.', 'wpdev-booking');?></span>
                            </td>
                        </tr>




                        <tr valign="top">
                          <th scope="row">
                            <label for="ipay88_return_url" ><?php _e('Return URL after Successful order', 'wpdev-booking'); ?>:</label>
                          </th>
                          <td>
                              <input value="<?php echo $ipay88_return_url; ?>" name="ipay88_return_url" id="ipay88_return_url" class="regular-text code" type="text" size="45" />
                              <span class="description"><?php printf(__('Enter a return relative Successful URL.', 'wpdev-booking'),'<b>','</b>');?><br/>
                               <?php printf(__('Please test this URL. Its have to be valid.', 'wpdev-booking'),'<b>','</b>');?> <a href="<?php echo  $ipay88_return_url; ?>" target="_blank"><?php echo  $ipay88_return_url; ?></a></span>
                          </td>
                        </tr>

                        <tr valign="top">
                          <th scope="row">
                            <label for="ipay88_cancel_return_url" ><?php _e('Return URL after Failed order', 'wpdev-booking'); ?>:</label>
                          </th>
                          <td>
                              <input value="<?php echo $ipay88_cancel_return_url; ?>" name="ipay88_cancel_return_url" id="ipay88_cancel_return_url" class="regular-text code" type="text" size="45" />
                              <span class="description"><?php printf(__('Enter a return relative Failed URL.', 'wpdev-booking'),'<b>','</b>');?><br/>
                               <?php printf(__('Please test this URL. Its have to be valid.', 'wpdev-booking'),'<b>','</b>');?> <a href="<?php echo   $ipay88_cancel_return_url; ?>" target="_blank"><?php  echo $ipay88_cancel_return_url; ?></a></span>
                          </td>
                        </tr>
                       <?php /**/ ?>
                        <tr valign="top">
                            <th scope="row">
                                <label for="ipay88_is_auto_approve_cancell_booking" ><?php _e('Auto approve/cancel of booking', 'wpdev-booking'); ?>:</label>
                            </th>
                            <td>
                                <input <?php if ($ipay88_is_auto_approve_cancell_booking == 'On') echo "checked";/**/ ?>  value="<?php echo $ipay88_is_auto_approve_cancell_booking; ?>" name="ipay88_is_auto_approve_cancell_booking" id="ipay88_is_auto_approve_cancell_booking" type="checkbox" />
                                <span class="description"><?php _e(' Check this checkbox and booking will be automaticaly approve or cancel, when visitor make successfully payment or when visitor make a payment cancellation. Warning this wont work if visitor leave the payment page', 'wpdev-booking');?></span>
                            </td>
                        </tr>

                        <tr valign="top">
                          <th scope="row" colspan="2">
                              <span style="font-size:11px;color:#f00;" class="description"><?php printf(__('Please %s configure %s fields inside the billing form%s, this is necessary for %s  Payment System.', 'wpdev-booking'),'<b>', __('Customer Email', 'wpdev-booking') . ', ' . __('First Name(s)', 'wpdev-booking') . ', ' . __('Last name', 'wpdev-booking') . ', ' . __('Phone', 'wpdev-booking')    ,'</b>', 'iPay88');?></span>
                          </th>
                        </tr>


                    </tbody>
                </table>

                <div class="clear" style="height:10px;"></div>
                <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="ipay88submit"/>
                <div class="clear" style="height:10px;"></div>

            <!--/form-->

       </div> </div> </div>
            </div>
       <?php
    }
    add_bk_action( 'wpdev_bk_payment_show_settings_content', 'wpdev_bk_payment_show_settings_content_ipay88');



    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //   P a y m e n t    f o r m    d e f i n i t i o n      //////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function wpdev_bk_define_payment_form_ipay88($blank, $booking_id, $summ,$bk_title, $booking_days_count, $booking_type, $bkform, $wp_nonce, $is_deposit ){
        $output = '';
        if( (get_bk_option( 'booking_ipay88_is_active' ) == 'On')   ) {
            $ipay88_is_active     = get_bk_option( 'booking_ipay88_is_active' );
            if ($ipay88_is_active == 'Off') return '';

            $ipay88_merchant_code   = get_bk_option( 'booking_ipay88_merchant_code' );
            $ipay88_merchant_key    = get_bk_option( 'booking_ipay88_merchant_key' );
            $ipay88_curency         = get_bk_option( 'booking_ipay88_curency' );
            $ipay88_subject         = get_bk_option( 'booking_ipay88_subject' );
            $ipay88_subject           =  apply_bk_filter('wpdev_check_for_active_language', $ipay88_subject );

            $ipay88_order_Successful  =  WPDEV_BK_PLUGIN_URL .'/'. WPDEV_BK_PLUGIN_FILENAME . '?payed_booking=' . $booking_id .'&wp_nonce=' . $wp_nonce . '&pay_sys=ipay88&stats=OK' ;
            $ipay88_order_Failed      =  WPDEV_BK_PLUGIN_URL .'/'. WPDEV_BK_PLUGIN_FILENAME . '?payed_booking=' . $booking_id .'&wp_nonce=' . $wp_nonce . '&pay_sys=ipay88&stats=FAILED' ;   //get_bk_option( 'booking_sage_order_Failed' );


            $cost_currency = apply_bk_filter('get_currency_info', 'ipay88');
            // iPay Description of this payment /////////////////////////////////////////////////////////
            $ipay88_subject = str_replace('[bookingname]',$bk_title[0]->title,$ipay88_subject);

            $booking_days_new_string = '';
            if (! empty($booking_days_count)) {
                $booking_days_new = explode(',',$booking_days_count);
                foreach ($booking_days_new as $new_day) {
                    $new_day=trim($new_day);
                    if (strpos($new_day, '.')!==false) $new_day = explode('.',$new_day);
                    else                               $new_day = explode('-',$new_day);
                    $booking_days_new_string .= $new_day[2] .'-' . $new_day[1] .'-' . $new_day[0] . ' 00:00:00,';
                }
                $booking_days_new_string = substr($booking_days_new_string ,0,-1);
            }
            $my_short_dates = get_dates_short_format($booking_days_new_string );


            $ipay88_subject = str_replace('[dates]',$my_short_dates,$ipay88_subject);

            $my_d_c = explode(',', $booking_days_count);
            $my_d_c = count($my_d_c);
            $ipay88_subject = str_replace('[datescount]',$my_d_c,$ipay88_subject);
            $ipay88_subject = str_replace('"','',$ipay88_subject);
            //////////////////////////////////////////////////////////////////////////////////////////////


            // Prepopulate some fields : ////////////////////////////////////////////////////////////////
            $form_fields = get_form_content ($bkform, $booking_type);
//debuge($form_fields, $bkform, $booking_type);
            $form_fields = $form_fields['_all_'];
//debuge($form_fields);
            $email = '';
            if (  get_bk_option( 'booking_billing_customer_email' )  !== false ) {
              $billing_customer_email  = (string) trim( get_bk_option( 'booking_billing_customer_email' ) . $booking_type );
              if ( isset($form_fields[$billing_customer_email]) !== false ){
                  $email      = substr($form_fields[$billing_customer_email], 0, 100);
              }
            }
            $email =  substr($email, 0, 100);
            $first_name = '';
            if ( get_bk_option( 'booking_billing_firstnames' )  !== false ) {
              $billing_firstnames      = (string) trim( get_bk_option( 'booking_billing_firstnames' ) . $booking_type );
              if ( isset($form_fields[$billing_firstnames]) !== false ){
                  $first_name = substr($form_fields[$billing_firstnames], 0, 32);
              }
            }
            $last_name = '';
            if ( get_bk_option( 'booking_billing_surname' )  !== false ) {
              $billing_surname         = (string) trim( get_bk_option( 'booking_billing_surname' ) . $booking_type );
              if ( isset($form_fields[$billing_surname]) !== false ){
                  $last_name  = substr($form_fields[$billing_surname], 0, 64);
              }
            }
            $firstlast_name =  substr($first_name . ' ' . $last_name, 0, 100);

            $phone = ' ';
            if ( get_bk_option( 'booking_billing_phone' )  !== false ) {
              $billing_phone         = (string) trim( get_bk_option( 'booking_billing_phone' ) . $booking_type );
              if ( isset($form_fields[$billing_phone]) !== false ){
                  $phone  = substr($form_fields[$billing_phone], 0, 20);
              }
            }

            //////////////////////////////////////////////////////////////////////////////////////////////

            $summ = number_format ( $summ , 2 , '.' , '' );
            $summ = str_replace(',', '.', $summ);
            $ref_no = substr('A0' . $booking_id, 0 , 20);




            $summ_sing = str_replace('.', '', $summ);
            $summ_sing = str_replace(',', '', $summ_sing);
            $signature = $ipay88_merchant_key . $ipay88_merchant_code . $ref_no . $summ_sing . $ipay88_curency;
            $signature = iPay88_signature($signature);



//debuge($firstlast_name, $email, $phone, $ipay88_merchant_code);
            if ( (! empty($firstlast_name)) && (! empty($email)) && (! empty($phone)) && (! empty($ipay88_merchant_code)) ) {

            // Show payment cost and some description //////////////////////////


                $is_show_it = get_bk_option( 'booking_ipay88_is_description_show' );
                if ($is_show_it == 'On') $output .= $ipay88_subject . '<br />';

                $ipay88_subject =  substr($ipay88_subject, 0, 100);
                $summ_show = wpdev_bk_cost_number_format ( $summ  );
                if ($is_deposit) $cost__title = __('Deposit', 'wpdev-booking')." : ";
                else             $cost__title = __('Cost', 'wpdev-booking')." : ";
                if ($cost_currency == $ipay88_curency) $cost_summ_with_title = "<strong>".$cost__title. $summ_show ." " . $cost_currency ."</strong><br />";
                else                                   $cost_summ_with_title = "<strong>".$cost__title. $cost_currency ." " . $summ_show ."</strong><br />";

                $output .= $cost_summ_with_title;
                /*
                if ($is_deposit) {
                    $today_day = date('m.d.Y')  ;
                    $cost_summ_with_title .= ' ('  . $today_day .')';
                    make_bk_action('wpdev_make_update_of_remark' , $booking_id , $cost_summ_with_title , true );
                }/**/


            // Generate iPay88 form /////////////////////////////////////////////////////////////////////////////////////////
                $output .= "<FORM method=\"post\" name=\"ePayment\" action=\"https://www.mobile88.com/ePayment/entry.asp\">";

                $output .= "<INPUT type=\"hidden\" name=\"MerchantCode\"  value=\"".$ipay88_merchant_code."\">";
                // $output .= "<INPUT type=\"hidden\" name=\"PaymentId\" value=\"2\">";  // Payment ID is getted reffear to the Appendix 1 from ipay88 API
                $output .= "<INPUT type=\"hidden\" name=\"RefNo\"  value=\"".$ref_no."\">";
                $output .= "<INPUT type=\"hidden\" name=\"Amount\"  value=\"".$summ."\">";
                $output .= "<INPUT type=\"hidden\" name=\"Currency\"  value=\"".$ipay88_curency."\">";
                $output .= "<INPUT type=\"hidden\" name=\"ProdDesc\"  value=\"".$ipay88_subject."\">";
                $output .= "<INPUT type=\"hidden\" name=\"UserName\"  value=\"".$firstlast_name."\">";
                $output .= "<INPUT type=\"hidden\" name=\"UserEmail\"  value=\"".$email."\">";
                $output .= "<INPUT type=\"hidden\" name=\"UserContact\"  value=\"".$phone."\">";
                $output .= "<INPUT type=\"hidden\" name=\"Remark\"  value=\"\">";
                $output .= "<INPUT type=\"hidden\" name=\"Lang\"   value=\"UTF-8\">";
                $output .= "<INPUT type=\"hidden\" name=\"Signature\"  value=\"".$signature."\">";
            $output .= "<INPUT type=\"hidden\" name=\"ResponseURL\" value=\"". $ipay88_order_Successful ."\">";
                $output .= "<INPUT type=\"submit\" value=\"".__('Proceed with iPay88 Payment','wpdev-booking')."\" name=\"Submit\">";

                $output .= "</FORM>";
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            }

        }
        return $output ;

    }
    add_bk_filter('wpdev_bk_define_payment_form_ipay88', 'wpdev_bk_define_payment_form_ipay88');


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //   A C T I V A T I O N   A N D   D E A C T I V A T I O N    O F   T H I S   P L U G I N    ///////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Activate
    function wpdev_bk_payment_activate_system_ipay88() {
        global $wpdb;

        add_bk_option( 'booking_ipay88_is_active' , 'Off');
        add_bk_option( 'booking_ipay88_merchant_code', '' );
        add_bk_option( 'booking_ipay88_merchant_key', '' );
        add_bk_option( 'booking_ipay88_curency'  , 'MYR' );
        add_bk_option( 'booking_ipay88_subject', sprintf(__('Payment for booking of the %s for days: %s' , 'wpdev-booking'),'[bookingname]','[dates]'));
        add_bk_option( 'booking_ipay88_is_description_show', 'Off' );
        add_bk_option( 'booking_ipay88_is_auto_approve_cancell_booking', 'Off' );
        add_bk_option( 'booking_ipay88_return_url' ,'');
        add_bk_option( 'booking_ipay88_cancel_return_url','' );

    }
    add_bk_action( 'wpdev_bk_payment_activate_system', 'wpdev_bk_payment_activate_system_ipay88');


    // Activate
    function wpdev_bk_payment_deactivate_system_ipay88() {
        global $wpdb;

        delete_bk_option( 'booking_ipay88_is_active' );
        delete_bk_option( 'booking_ipay88_merchant_code' );
        delete_bk_option( 'booking_ipay88_merchant_key' );
        delete_bk_option( 'booking_ipay88_curency'   );
        delete_bk_option( 'booking_ipay88_subject' );
        delete_bk_option( 'booking_ipay88_is_description_show' );
        delete_bk_option( 'booking_ipay88_is_auto_approve_cancell_booking' );
        delete_bk_option( 'booking_ipay88_return_url' );
        delete_bk_option( 'booking_ipay88_cancel_return_url' );
    }
    add_bk_action( 'wpdev_bk_payment_deactivate_system', 'wpdev_bk_payment_deactivate_system_ipay88');


?>
