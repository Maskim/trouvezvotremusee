<?php
/*
This is COMMERCIAL SCRIPT
We are do not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  S e t t i n g s    f u n c t i o n s       ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    function wpdev_bk_payment_show_tab_in_top_settings_paypal(){
        ?><a href="#" onclick="javascript:
                jQuery('.visibility_container').css('display','none');
                jQuery('#visibility_container_paypal').css('display','block');
                jQuery('.nav-tab').removeClass('booking-submenu-tab-selected');
                jQuery(this).addClass('booking-submenu-tab-selected');"
           rel="tooltip"
           class="tooltip_bottom nav-tab  booking-submenu-tab booking-submenu-tab-selected <?php
                   if ( get_bk_option( 'booking_paypal_is_active' ) != 'On' ) echo ' booking-submenu-tab-disabled '; ?>"
           original-title="<?php _e('Integration of Paypal payment system', 'wpdev-booking');?>" >
           <?php _e('PayPal', 'wpdev-booking');?>
           <input type="checkbox" <?php if ( get_bk_option( 'booking_paypal_is_active' ) == 'On' ) echo ' checked="CHECKED" '; ?>
                   name="paypal_is_active_dublicated" id="paypal_is_active_dublicated"
                   onchange="document.getElementById('paypal_is_active').checked=this.checked;" >
        </a>
        <script type="text/javascript">
            jQuery(document).ready( function(){
                recheck_active_itmes_in_top_menu('paypal_is_active', 'paypal_is_active_dublicated');
            });
        </script>
        <?php
    }
    add_bk_action( 'wpdev_bk_payment_show_tab_in_top_settings', 'wpdev_bk_payment_show_tab_in_top_settings_paypal');



    // Settings page for    P a y P a l
    function wpdev_bk_payment_show_settings_content_paypal(){
        if ( ( isset( $_POST['paypal_curency'] ) )  ) {
                 if ( wpdev_bk_is_this_demo() ) $_POST['paypal_emeil'] = 'booking@wpdevelop.com';

                 ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                 update_bk_option( 'booking_paypal_is_sandbox' ,    $_POST['paypal_is_sandbox'] );
                 if (isset($_POST['paypal_emeil']))
                    update_bk_option( 'booking_paypal_emeil' ,         $_POST['paypal_emeil'] );
                 if (isset($_POST['paypal_secure_merchant_id']))
                     update_bk_option( 'booking_paypal_secure_merchant_id' , $_POST['paypal_secure_merchant_id'] );
                 update_bk_option( 'booking_paypal_curency' ,       $_POST['paypal_curency'] );
                 update_bk_option( 'booking_paypal_subject' ,       $_POST['paypal_subject'] );
                 update_bk_option( 'booking_paypal_return_url' ,    $_POST['paypal_return_url'] );
                 update_bk_option( 'booking_paypal_button_type' ,   $_POST['paypal_button_type'] );
                 update_bk_option( 'booking_paypal_paymentaction' ,   $_POST['paypal_paymentaction'] );


                 if (isset($_POST['paypal_reference_title_box']))
                     update_bk_option( 'booking_paypal_reference_title_box' , $_POST['paypal_reference_title_box'] );

                 if (isset( $_POST['paypal_is_active'] ))     $paypal_is_active = 'On';
                 else                                         $paypal_is_active = 'Off';
                 update_bk_option( 'booking_paypal_is_active' , $paypal_is_active );

                 if (  $_POST['paypal_pro_hosted_solution'] == 'On' )     $paypal_pro_hosted_solution = 'On';
                 else                                                   $paypal_pro_hosted_solution = 'Off';
                 update_bk_option( 'booking_paypal_pro_hosted_solution' , $paypal_pro_hosted_solution );

                 if (isset( $_POST['paypal_is_reference_box'] ))     $paypal_is_reference_box = 'On';
                 else                                                $paypal_is_reference_box = 'Off';
                 update_bk_option( 'booking_paypal_is_reference_box' , $paypal_is_reference_box );

                 if (isset( $_POST['paypal_is_description_show'] ))     $paypal_is_description_show = 'On';
                 else                                                   $paypal_is_description_show = 'Off';
                 update_bk_option( 'booking_paypal_is_description_show' , $paypal_is_description_show );

                 if (isset( $_POST['paypal_is_auto_approve_cancell_booking'] ))     $paypal_is_auto_approve_cancell_booking = 'On';
                 else                                                               $paypal_is_auto_approve_cancell_booking = 'Off';
                 update_bk_option( 'booking_paypal_is_auto_approve_cancell_booking',$paypal_is_auto_approve_cancell_booking );

                 if (isset( $_POST['paypal_ipn_is_active'] ))     $paypal_ipn_is_active = 'On';
                 else                                             $paypal_ipn_is_active = 'Off';
                 update_bk_option( 'booking_paypal_ipn_is_active' , $paypal_ipn_is_active );

                 if (isset( $_POST['paypal_ipn_is_send_verified_email'] ))      $paypal_ipn_is_send_verified_email = 'On';
                 else                                                           $paypal_ipn_is_send_verified_email = 'Off';
                 update_bk_option( 'booking_paypal_ipn_is_send_verified_email' , $paypal_ipn_is_send_verified_email );
                 if (isset( $_POST['paypal_ipn_verified_email'] )) update_bk_option( 'booking_paypal_ipn_verified_email' , $_POST['paypal_ipn_verified_email']  );

                 if (isset( $_POST['paypal_ipn_is_send_invalid_email'] ))      $paypal_ipn_is_send_invalid_email = 'On';
                 else                                                           $paypal_ipn_is_send_invalid_email = 'Off';
                 update_bk_option( 'booking_paypal_ipn_is_send_invalid_email' , $paypal_ipn_is_send_invalid_email );
                 if (isset( $_POST['paypal_ipn_invalid_email'] )) update_bk_option( 'booking_paypal_ipn_invalid_email' , $_POST['paypal_ipn_invalid_email']  );

                 if (isset( $_POST['paypal_ipn_is_send_error_email'] ))      $paypal_ipn_is_send_error_email = 'On';
                 else                                                           $paypal_ipn_is_send_error_email = 'Off';
                 update_bk_option( 'booking_paypal_ipn_is_send_error_email' , $paypal_ipn_is_send_error_email );
                 if (isset( $_POST['paypal_ipn_error_email'] )) update_bk_option( 'booking_paypal_ipn_error_email' , $_POST['paypal_ipn_error_email']  );

                 if (isset( $_POST['paypal_ipn_use_ssl'] ))                     $paypal_ipn_use_ssl = 'On';
                 else                                                           $paypal_ipn_use_ssl = 'Off';
                 update_bk_option( 'booking_paypal_ipn_use_ssl' , $paypal_ipn_use_ssl );
                 if (isset( $_POST['paypal_ipn_use_curl'] ))                    $paypal_ipn_use_curl = 'On';
                 else                                                           $paypal_ipn_use_curl = 'Off';
                 update_bk_option( 'booking_paypal_ipn_use_curl' , $paypal_ipn_use_curl );
        }
        $paypal_emeil               =  get_bk_option( 'booking_paypal_emeil' );
        $paypal_secure_merchant_id  =  get_bk_option( 'booking_paypal_secure_merchant_id'  );
        $paypal_pro_hosted_solution =  get_bk_option( 'booking_paypal_pro_hosted_solution' );
        $paypal_curency             =  get_bk_option( 'booking_paypal_curency' );
        $paypal_subject             =  get_bk_option( 'booking_paypal_subject' );
        $paypal_is_active           =  get_bk_option( 'booking_paypal_is_active' );
        $paypal_pro_hosted_solution =  get_bk_option( 'booking_paypal_pro_hosted_solution' );
        $paypal_is_reference_box    =  get_bk_option( 'booking_paypal_is_reference_box' );           // checkbox
        $paypal_reference_title_box =  get_bk_option( 'booking_paypal_reference_title_box' );
        $paypal_paymentaction       =  get_bk_option( 'booking_paypal_paymentaction' );
        $paypal_return_url          =  get_bk_option( 'booking_paypal_return_url' );
        $paypal_cancel_return_url   =  get_bk_option( 'booking_paypal_cancel_return_url' );
        $paypal_button_type         =  get_bk_option( 'booking_paypal_button_type' );  // radio
        $paypal_is_sandbox          =  get_bk_option( 'booking_paypal_is_sandbox' );  // radio
        $paypal_is_description_show =  get_bk_option( 'booking_paypal_is_description_show' );  // radio
        $paypal_is_auto_approve_cancell_booking =  get_bk_option( 'booking_paypal_is_auto_approve_cancell_booking' );  // radio

        $paypal_ipn_is_active =  get_bk_option( 'booking_paypal_ipn_is_active' );

        $paypal_ipn_is_send_verified_email  =  get_bk_option( 'booking_paypal_ipn_is_send_verified_email' );
        $paypal_ipn_verified_email          =  get_bk_option( 'booking_paypal_ipn_verified_email' );
        $paypal_ipn_is_send_invalid_email  =  get_bk_option( 'booking_paypal_ipn_is_send_invalid_email' );
        $paypal_ipn_invalid_email          =  get_bk_option( 'booking_paypal_ipn_invalid_email' );
        $paypal_ipn_is_send_error_email  =  get_bk_option( 'booking_paypal_ipn_is_send_error_email' );
        $paypal_ipn_error_email          =  get_bk_option( 'booking_paypal_ipn_error_email' );
        $paypal_ipn_use_ssl     =  get_bk_option( 'booking_paypal_ipn_use_ssl' );
        $paypal_ipn_use_curl    =  get_bk_option( 'booking_paypal_ipn_use_curl' );
        ?>
        <div id="visibility_container_paypal" class="visibility_container" style="display:block;">
         <div class='meta-box'>
          <div <?php $my_close_open_win_id = 'bk_settings_costs_paypal_payment'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <!--div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');"><br></div-->
           <h3 class='hndle'><span><?php _e('PayPal customization', 'wpdev-booking'); ?></span></h3>
           <div class="inside">

<a data-original-title="Configuration of PayPal Standard payment form" class="tooltip_bottom nav-tab  booking-submenu-tab top-to-bottom  <?php if ($paypal_pro_hosted_solution == 'Off') echo ' booking-submenu-tab-selected '; ?> " rel="tooltip" onclick="javascript:
           jQuery('.visibility_sub_container_paypal').addClass('hidden_items');
           jQuery('.visibility_sub_container_paypal_standard').removeClass('hidden_items');
           jQuery('.visibility_paypal_account_settings').removeClass('hidden_items');
           jQuery('.visibility_paypal_ipn_settings').addClass('hidden_items');           
           jQuery('.nav-tab.top-to-bottom').removeClass('booking-submenu-tab-selected');
           jQuery(this).addClass('booking-submenu-tab-selected');" href="#" >
    <?php _e('Paypal Standard', 'wpdev-booking');?>
    <input <?php if ($paypal_pro_hosted_solution == 'Off') echo ' checked="checked" '; ?> type="radio" name="paypal_pro_hosted_solution" value="Off" style=""
     onMouseDown="javascript: document.getElementById('paypal_secure_merchant_id').disabled=! this.checked; document.getElementById('paypal_emeil').disabled= this.checked; "
     data-original-title="Set Active of PayPal Standard account"  rel="tooltip" class="tooltip_right"
                                                                                          />
</a>

<a data-original-title="Configuration of PayPal Pro Hosted Solution payment form" class="tooltip_bottom nav-tab  booking-submenu-tab top-to-bottom  <?php if ($paypal_pro_hosted_solution == 'On') echo ' booking-submenu-tab-selected '; ?> " rel="tooltip" onclick="javascript:
           jQuery('.visibility_sub_container_paypal').addClass('hidden_items');
           jQuery('.visibility_sub_container_paypal_pro').removeClass('hidden_items');
           jQuery('.visibility_paypal_account_settings').removeClass('hidden_items');
           jQuery('.visibility_paypal_ipn_settings').addClass('hidden_items');
           jQuery('.nav-tab.top-to-bottom').removeClass('booking-submenu-tab-selected');
           jQuery(this).addClass('booking-submenu-tab-selected');" href="#" >
    <?php _e('Paypal Pro Hosted Solution', 'wpdev-booking');?>
        <input <?php if ($paypal_pro_hosted_solution == 'On') echo ' checked="checked" '; ?> type="radio" name="paypal_pro_hosted_solution" value="On" style=""
        onMouseDown="javascript: document.getElementById('paypal_secure_merchant_id').disabled=this.checked; document.getElementById('paypal_emeil').disabled=! this.checked; "
        data-original-title="Set Active of PayPal Pro Hosted Solution account"  rel="tooltip" class="tooltip_right"
                                                                                             />
</a>

<a data-original-title="Instant Payment Notification (IPN) is a message service that notifies you of events related to PayPal transactions" class="tooltip_bottom nav-tab  booking-submenu-tab top-to-bottom" rel="tooltip" onclick="javascript:
           jQuery('.visibility_paypal_account_settings').addClass('hidden_items');
           jQuery('.visibility_paypal_ipn_settings').removeClass('hidden_items');
           jQuery('.nav-tab.top-to-bottom').removeClass('booking-submenu-tab-selected');
           jQuery(this).addClass('booking-submenu-tab-selected');" href="#" >
        <?php _e('IPN', 'wpdev-booking');?>
<?php /** ?>
        <input type="checkbox" onchange="document.getElementById('paypal_ipn_is_active').checked=this.checked;" id="paypal_ipn_is_active_dublicated" name="paypal_ipn_is_active_dublicated" <?php if ($paypal_ipn_is_active == 'On') echo ' checked="CHECKED" '; ?>
               data-original-title="Set Active of PayPal IPN feature"  rel="tooltip" class="tooltip_right"
               >
<?php /**/ ?>
</a>
            <table class="visibility_paypal_ipn_settings form-table settings-table wpdevbk hidden_items">
                <tbody>
<?php /*
                    <tr valign="top">
                        <th scope="row">
                            <label for="paypal_ipn_is_active" ><?php _e('PayPal IPN service active', 'wpdev-booking'); ?>:</label>
                        </th>
                        <td>
                            <input <?php if ($paypal_ipn_is_active == 'On') echo "checked"; ?>
                                value="<?php echo $paypal_ipn_is_active; ?>" name="paypal_ipn_is_active" id="paypal_ipn_is_active" type="checkbox"
                                onchange="document.getElementById('paypal_ipn_is_active_dublicated').checked=this.checked;"
                                                                                          />
                            <span class="description"><?php _e(' Check this checkbox to use Instant Payment Notification  service that notifies you of events related to PayPal transactions.', 'wpdev-booking');?></span>
                        </td>
                    </tr>
<?php /**/ ?>


                    <tr valign="top">
                      <th scope="row">
                        <label for="paypal_ipn_verified_email" ><?php _e('Sending email for verified transaction', 'wpdev-booking'); ?>:</label>
                      </th>
                      <td>

                            <input <?php if ($paypal_ipn_is_send_verified_email == 'On') echo "checked";/**/ ?>
                                value="<?php echo $paypal_ipn_is_send_verified_email; ?>" name="paypal_ipn_is_send_verified_email" id="paypal_ipn_is_send_verified_email" type="checkbox"
                                onMouseDown="javascript: document.getElementById('paypal_ipn_verified_email').disabled=this.checked; "/>
                            <span class="description"><?php _e('Active / Inactive', 'wpdev-booking'); ?></span>&nbsp;&nbsp;&nbsp;&nbsp;

                          <input <?php if ($paypal_ipn_is_send_verified_email !== 'On') echo " disabled "; ?>
                              value="<?php echo $paypal_ipn_verified_email; ?>" name="paypal_ipn_verified_email" id="paypal_ipn_verified_email"
                              class="regular-text code" type="text" size="145" />
                          <span class="description"><?php printf(__('Email for getting report for %sverified%s transactions.', 'wpdev-booking'),'<b>','</b>');?></span>
                      </td>
                    </tr>

                    <tr valign="top">
                      <th scope="row">
                        <label for="paypal_ipn_invalid_email" ><?php _e('Sending email for invalid transaction', 'wpdev-booking'); ?>:</label>
                      </th>
                      <td>

                            <input <?php if ($paypal_ipn_is_send_invalid_email == 'On') echo "checked";/**/ ?>
                                value="<?php echo $paypal_ipn_is_send_invalid_email; ?>" name="paypal_ipn_is_send_invalid_email" id="paypal_ipn_is_send_invalid_email" type="checkbox"
                                onMouseDown="javascript: document.getElementById('paypal_ipn_invalid_email').disabled=this.checked; "/>
                            <span class="description"><?php _e('Active / Inactive', 'wpdev-booking'); ?></span>&nbsp;&nbsp;&nbsp;&nbsp;

                          <input <?php if ($paypal_ipn_is_send_invalid_email !== 'On') echo " disabled "; ?>
                              value="<?php echo $paypal_ipn_invalid_email; ?>" name="paypal_ipn_invalid_email" id="paypal_ipn_invalid_email"
                              class="regular-text code" type="text" size="145" />
                          <span class="description"><?php printf(__('Email for getting report for %sinvalid%s transactions.', 'wpdev-booking'),'<b>','</b>');?></span>
                      </td>
                    </tr>


                    <tr valign="top">
                      <th scope="row">
                        <label for="paypal_ipn_error_email" ><?php _e('Sending email if error occuer during veryfication', 'wpdev-booking'); ?>:</label>
                      </th>
                      <td>

                            <input <?php if ($paypal_ipn_is_send_error_email == 'On') echo "checked";/**/ ?>
                                value="<?php echo $paypal_ipn_is_send_error_email; ?>" name="paypal_ipn_is_send_error_email" id="paypal_ipn_is_send_error_email" type="checkbox"
                                onMouseDown="javascript: document.getElementById('paypal_ipn_error_email').disabled=this.checked; "/>
                            <span class="description"><?php _e('Active / Inactive', 'wpdev-booking'); ?></span>&nbsp;&nbsp;&nbsp;&nbsp;

                          <input <?php if ($paypal_ipn_is_send_error_email !== 'On') echo " disabled "; ?>
                              value="<?php echo $paypal_ipn_error_email; ?>" name="paypal_ipn_error_email" id="paypal_ipn_error_email"
                              class="regular-text code" type="text" size="145" />
                          <span class="description"><?php printf(__('Email for getting report for %ssome errors in  veryfication process%s.', 'wpdev-booking'),'<b>','</b>');?></span>
                      </td>
                    </tr>


                    <tr valign="top">
                      <th scope="row">
                        <label for="paypal_ipn_use_ssl" ><?php _e('Use SSL connection', 'wpdev-booking'); ?>:</label>
                      </th>
                      <td>
                            <input <?php if ($paypal_ipn_use_ssl == 'On') echo "checked"; ?> value="<?php echo $paypal_ipn_use_ssl; ?>"
                                name="paypal_ipn_use_ssl" id="paypal_ipn_use_ssl" type="checkbox" />
                            <span class="description"><?php _e('Use the SSL connection for posting data, instead of standard HTTP connection', 'wpdev-booking'); ?></span>
                      </td>
                    </tr>

                    <tr valign="top">
                      <th scope="row">
                        <label for="paypal_ipn_use_curl" ><?php _e('Use cURL posting', 'wpdev-booking'); ?>:</label>
                      </th>
                      <td>
                            <input <?php if ($paypal_ipn_use_curl == 'On') echo "checked"; ?> value="<?php echo $paypal_ipn_use_curl; ?>"
                                name="paypal_ipn_use_curl" id="paypal_ipn_use_curl" type="checkbox" />
                            <span class="description"><?php _e('Use the cURL for posting data, instead of fsockopen() function', 'wpdev-booking'); ?></span>
                      </td>
                    </tr>

                    <tr>
                        <td colspan="2" >
                            <div style="width:599px;margin:0px auto;" class="code_description">
                                <div  class="shortcode_help_section">
                                  <span class="description">
                                    <strong><?php _e(' Follow these instructions to set up your listener at your PayPal account:', 'wpdev-booking');?></strong>
                                    <ol style="line-height: 4px; white-space: pre;">
                                    <li><?php _e('Click Profile on the My Account tab.', 'wpdev-booking');?></li>
                                    <li><?php _e('Click Instant Payment Notification Preferences in the Selling Preferences column.', 'wpdev-booking');?></li>
                                    <li><?php _e('Click Choose IPN Settings to specify your listeners URL and activate the listener.', 'wpdev-booking');?></li>
                                    <li><?php _e('Specify the URL for your listener in the Notification URL field as:', 'wpdev-booking');?><br /><code><?php echo WPDEV_BK_PLUGIN_URL .'/inc/payments/ipn.php';?></code></li>
                                    <li><?php _e('Click Receive IPN messages (Enabled) to enable your listener.', 'wpdev-booking');?></li>
                                    <li><?php _e('Click Save.', 'wpdev-booking');?></li>
                                    <li><?php _e('Click Back to Profile Summary to return to the Profile after activating your listener.', 'wpdev-booking');?></li>
                                    </ol>
                                  </span>
                                </div>
                            </div>
                        </td>
                    </tr>
            </table>

            <table class="visibility_paypal_account_settings form-table settings-table wpdevbk">
                <tbody>

                    <tr valign="top">
                        <th scope="row">
                            <label for="paypal_is_active" ><?php _e('PayPal active', 'wpdev-booking'); ?>:</label>
                        </th>
                        <td>
                            <input <?php if ($paypal_is_active == 'On') echo "checked"; ?>
                                value="<?php echo $paypal_is_active; ?>" name="paypal_is_active" id="paypal_is_active" type="checkbox"
                                onchange="document.getElementById('paypal_is_active_dublicated').checked=this.checked;"
                                                                                          />
                            <span class="description"><?php _e(' Check this checkbox to use PayPal.', 'wpdev-booking');?></span>
                        </td>
                    </tr>
                    <!--tr valign="top">
                        <th scope="row">
                            <label for="paypal_pro_hosted_solution" ><?php _e('Payment Pro Hosted Solution active', 'wpdev-booking'); ?>:</label>
                        </th>
                        <td>
                            <input <?php if ($paypal_pro_hosted_solution == 'On') echo "checked";/**/ ?>
                                value="<?php echo $paypal_pro_hosted_solution; ?>" name="paypal_pro_hosted_solution" id="paypal_pro_hosted_solution" type="checkbox"
                                onMouseDown="javascript: document.getElementById('paypal_secure_merchant_id').disabled=this.checked; document.getElementById('paypal_emeil').disabled=! this.checked; "
                                                                                                />
                            <span class="description"><?php _e(' Check this checkbox to activate Payment Pro Hosted Solution.', 'wpdev-booking');?></span>
                        </td>
                    </tr-->

                    <tr valign="top" class="well visibility_sub_container_paypal visibility_sub_container_paypal_pro   <?php if ($paypal_pro_hosted_solution == 'Off') echo ' hidden_items '; ?> " >
                      <th>
                        <label class="control-label" for="paypal_secure_merchant_id" ><?php _e('Secure Merchant ID', 'wpdev-booking'); ?>:</label>
                      </th>
                      <td>
                          <input  <?php if ($paypal_pro_hosted_solution !== 'On') echo " disabled "; ?>
                              value="<?php echo $paypal_secure_merchant_id; ?>" name="paypal_secure_merchant_id" id="paypal_secure_merchant_id"
                              class="regular-text code" type="text" size="45" />
                          <span class="help-block"><?php printf(__('This is the Secure Merchant ID, which can be found on the profile page', 'wpdev-booking'),'<b>','</b>');?></span>
                          <?php  if ( wpdev_bk_is_this_demo() ) { ?> <span class="description">You do not allow to change email because right now you test DEMO</span> <?php } ?>
                      </td>
                    </tr>

                    <tr valign="top" class="well visibility_sub_container_paypal visibility_sub_container_paypal_standard <?php if ($paypal_pro_hosted_solution == 'On') echo ' hidden_items '; ?> " >
                      <th scope="row">
                        <label class="control-label" for="paypal_emeil" ><?php _e('Paypal Email address to receive payments', 'wpdev-booking'); ?>:</label>
                      </th>
                      <td>
                          <input  <?php if ($paypal_pro_hosted_solution == 'On') echo " disabled "; ?>
                              value="<?php echo $paypal_emeil; ?>" name="paypal_emeil" id="paypal_emeil" class="regular-text code" type="text" size="45" />
                          <span class="help-block"><?php printf(__('This is the Paypal Email address where the payments will go', 'wpdev-booking'),'<b>','</b>');?></span>
                          <?php  if ( wpdev_bk_is_this_demo() ) { ?> <span class="description">You do not allow to change email because right now you test DEMO</span> <?php } ?>
                      </td>
                    </tr>

                    <tr valign="top">
                      <th scope="row">
                        <label for="paypal_is_sandbox" ><?php _e('Chose payment mode', 'wpdev-booking'); ?>:</label>
                      </th>
                      <td>
                         <select id="paypal_is_sandbox" name="paypal_is_sandbox">
                            <option <?php if($paypal_is_sandbox == 'Off') echo "selected"; ?> value="Off"><?php _e('Live', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_is_sandbox == 'On')  echo "selected"; ?> value="On"><?php _e('Sandbox', 'wpdev-booking'); ?></option>
                         </select>
                         <span class="description"><?php _e(' Select using test (Sandbox Test Environment) or live PayPal payment.', 'wpdev-booking');?></span>
                      </td>
                    </tr>

                    <tr valign="top">
                      <th scope="row">
                        <label for="paypal_paymentaction" ><?php _e('Payment action', 'wpdev-booking'); ?>:</label>
                      </th>
                      <td>
                         <select id="paypal_paymentaction" name="paypal_paymentaction">
                            <option <?php if($paypal_paymentaction == 'sale') echo "selected"; ?> value="sale"><?php _e('Sale', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_paymentaction == 'authorization')  echo "selected"; ?> value="authorization"><?php _e('Authorization', 'wpdev-booking'); ?></option>
                         </select>
                         <span class="description"><?php _e(' Indicates whether the transaction is payment on a final sale or an authorization for a final sale, to be captured later. ', 'wpdev-booking');?></span>
                      </td>
                    </tr>

                    <tr valign="top">
                      <th scope="row">
                        <label for="paypal_curency" ><?php _e('Choose Payment Currency', 'wpdev-booking'); ?>:</label>
                      </th>
                      <td>
                         <select id="paypal_curency" name="paypal_curency">
                            <option <?php if($paypal_curency == 'USD') echo "selected"; ?> value="USD"><?php _e('U.S. Dollars', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'EUR') echo "selected"; ?> value="EUR"><?php _e('Euros', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'GBP') echo "selected"; ?> value="GBP"><?php _e('British Pound', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'JPY') echo "selected"; ?> value="JPY"><?php _e('Japanese Yen', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'AUD') echo "selected"; ?> value="AUD"><?php _e('Australian Dollars', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'CAD') echo "selected"; ?> value="CAD"><?php _e('Canadian Dollars', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'NZD') echo "selected"; ?> value="NZD"><?php _e('New Zealand Dollar', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'CHF') echo "selected"; ?> value="CHF"><?php _e('Swiss Franc', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'HKD') echo "selected"; ?> value="HKD"><?php _e('Hong Kong Dollar', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'SGD') echo "selected"; ?> value="SGD"><?php _e('Singapore Dollar', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'SEK') echo "selected"; ?> value="SEK"><?php _e('Swedish Krona', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'DKK') echo "selected"; ?> value="DKK"><?php _e('Danish Krone', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'PLN') echo "selected"; ?> value="PLN"><?php _e('Polish Zloty', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'NOK') echo "selected"; ?> value="NOK"><?php _e('Norwegian Krone', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'HUF') echo "selected"; ?> value="HUF"><?php _e('Hungarian Forint', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'CZK') echo "selected"; ?> value="CZK"><?php _e('Czech Koruna', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'ILS') echo "selected"; ?> value="ILS"><?php _e('Israeli New Shekel', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'MXN') echo "selected"; ?> value="MXN"><?php _e('Mexican Peso', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'BRL') echo "selected"; ?> value="BRL"><?php _e('Brazilian Real (only for Brazilian users)', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'MYR') echo "selected"; ?> value="MYR"><?php _e('Malaysian Ringgits (only for Malaysian users)', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'PHP') echo "selected"; ?> value="PHP"><?php _e('Philippine Pesos', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'TWD') echo "selected"; ?> value="TWD"><?php _e('Taiwan New Dollars', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'THB') echo "selected"; ?> value="THB"><?php _e('Thai Baht', 'wpdev-booking'); ?></option>
                            <option <?php if($paypal_curency == 'TRY') echo "selected"; ?> value="TRY"><?php _e('Turkish Lira (only for Turkish members)', 'wpdev-booking'); ?></option>
                         </select>
                         <span class="description"><?php printf(__('This is the currency for your visitors to make Payments', 'wpdev-booking'),'<b>','</b>');?></span>
                      </td>
                    </tr>

                    <tr valign="top">
                      <th scope="row" >
                        <label for="paypal_subject" ><?php _e('Payment description', 'wpdev-booking'); ?>:</label><br/><br/>
                        <span class="description"><?php printf(__('Enter the service name or the reason for the payment here.', 'wpdev-booking'),'<br/>','</b>');?></span>
                      </th>
                      <td>
                        <div style="float:left;margin:10px 0px;width:100%;">
                            <input id="paypal_subject" name="paypal_subject" class="darker-border"  type="text" maxlength="70" size="59" value="<?php echo $paypal_subject; ?>" style="width:400px;" />
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input <?php if ($paypal_is_description_show == 'On') echo "checked";/**/ ?>  value="<?php echo $paypal_is_description_show; ?>" name="paypal_is_description_show" id="paypal_is_description_show" type="checkbox" />
                            <span class="description"><?php _e('Show payment description in payment form.', 'wpdev-booking');?></span>

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
                        <label for="paypal_reference_title_box" ><?php _e('Reference Text Box Title', 'wpdev-booking'); ?>:</label>
                      </th>
                      <td>
                          
                            <input <?php if ($paypal_is_reference_box == 'On') echo "checked";/**/ ?>
                                value="<?php echo $paypal_is_reference_box; ?>" name="paypal_is_reference_box" id="paypal_is_reference_box" type="checkbox"
                                onMouseDown="javascript: document.getElementById('paypal_reference_title_box').disabled=this.checked; "/>
                            <span class="description"><?php _e('Active / Inactive', 'wpdev-booking'); ?></span>&nbsp;&nbsp;&nbsp;&nbsp;

                          <input <?php if ($paypal_is_reference_box !== 'On') echo " disabled "; ?>
                              value="<?php echo $paypal_reference_title_box; ?>" name="paypal_reference_title_box" id="paypal_reference_title_box"
                              class="regular-text code" type="text" size="45" />
                          <span class="description"><?php printf(__('Enter a title for the Reference text box (i.e. Your emeil). The visitors will see this text', 'wpdev-booking'),'<b>','</b>');?></span>
                      </td>
                    </tr>

                    <tr valign="top">
                      <th scope="row">
                        <label for="paypal_return_url" ><?php _e('Return URL from PayPal', 'wpdev-booking'); ?>:</label>
                      </th>
                      <td>
                          <input value="<?php echo $paypal_return_url; ?>" name="paypal_return_url" id="paypal_return_url" class="regular-text code" type="text" size="45" />
                          <span class="description"><?php printf(__('The URL to which the payers browser is redirected after completing the payment; for example, a URL on your site that displays a {Thank you for your payment page}.', 'wpdev-booking'),'<b>','</b>');?></span>
                          <br/><span class="description"><?php printf(__('For using this feature you %smust have activated auto return link%s at your Paypal account.
Follow these steps to configure it:%s
1. Log in to your PayPal account.%s
2. Click the Profile subtab.%s
3. Click Website Payment Preferences in the Seller Preferences column.%s
4. Under Auto Return for Website Payments, click the On radio button.%s
5. For the Return URL, enter the YOUR URL for your site for successfull payment. ', 'wpdev-booking'),'<b>','</b>','<br/>','<br/>','<br/>','<br/>','<br/>','<br/>');?></span>
                      </td>
                    </tr>

                    <tr valign="top">
                      <th scope="row">
                        <label for="paypal_cancel_return_url" ><?php _e('Cancel Return URL from PayPal', 'wpdev-booking'); ?>:</label>
                      </th>
                      <td>
                          <input value="<?php echo $paypal_cancel_return_url; ?>" name="paypal_cancel_return_url" id="paypal_cancel_return_url" class="regular-text code" type="text" size="45" />
                          <span class="description"><?php printf(__('A URL to which the payers browser is redirected if payment is cancelled, for example, a URL on your website that displays a {Payment Canceled} page.', 'wpdev-booking'),'<b>','</b>');?></span>
                      </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">
                            <label for="paypal_is_auto_approve_cancell_booking" ><?php _e('Auto approve/cancel of booking', 'wpdev-booking'); ?>:</label>
                        </th>
                        <td>
                            <input <?php if ($paypal_is_auto_approve_cancell_booking == 'On') echo "checked";/**/ ?>  value="<?php echo $paypal_is_auto_approve_cancell_booking; ?>" name="paypal_is_auto_approve_cancell_booking" id="paypal_is_auto_approve_cancell_booking" type="checkbox" />
                            <span class="description"><?php _e(' Check this checkbox and booking will be automaticaly approve or cancel, when visitor make successfully payment or when visitor make a payment cancellation. Warning this wont work if visitor leave the payment page', 'wpdev-booking');?></span>
                        </td>
                    </tr>

                    <tr valign="top">
                      <th scope="row">
                        <label for="paypal_button_type" ><?php _e('Button types', 'wpdev-booking'); ?>:</label>
                      </th>
                      <td>
                            <div style="width:150px;margin:auto;float:left;margin:5px;text-align:center;">
                                    <img src="https://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif" /><br/>
                                    <input <?php if ($paypal_button_type == 'https://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif') echo ' checked="checked" '; ?> type="radio" name="paypal_button_type" value="https://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif" style="margin:10px;" />
                            </div>
                            <div style="width:150px;margin:auto;float:left;margin:5px;text-align:center;">
                                    <img src="https://www.paypal.com/en_US/i/btn/btn_paynow_LG.gif" /><br/>
                                    <input <?php if ($paypal_button_type == 'https://www.paypal.com/en_US/i/btn/btn_paynow_LG.gif') echo ' checked="checked" '; ?>  type="radio" name="paypal_button_type" value="https://www.paypal.com/en_US/i/btn/btn_paynow_LG.gif" style="margin:10px;" />
                            </div>
                            <div style="width:150px;margin:auto;float:left;margin:5px;text-align:center;">
                                    <img src="https://www.paypal.com/en_US/i/btn/btn_paynow_SM.gif" /><br/>
                                    <input <?php if ($paypal_button_type == 'https://www.paypal.com/en_US/i/btn/btn_paynow_SM.gif') echo ' checked="checked" '; ?>  type="radio" name="paypal_button_type" value="https://www.paypal.com/en_US/i/btn/btn_paynow_SM.gif" style="margin:10px;" />
                            </div>
                           <span class="description"><?php printf(__('Select submit button type', 'wpdev-booking'),'<b>','</b>');?></span>
                      </td>
                    </tr>

                </tbody>
            </table>

            <div class="clear" style="height:10px;"></div>
            <input class="button-primary" style="float:right;" type="submit" value="<?php _e('Save', 'wpdev-booking'); ?>" name="Submit"/>
            <div class="clear" style="height:10px;"></div>

           </div>
          </div>
         </div>
        </div>
        <?php
    }
    add_bk_action( 'wpdev_bk_payment_show_settings_content', 'wpdev_bk_payment_show_settings_content_paypal');




    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //   P a y m e n t    f o r m    d e f i n i t i o n      //////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function wpdev_bk_define_payment_form_paypal($blank, $booking_id, $summ,$bk_title, $booking_days_count, $booking_type, $bkform, $wp_nonce, $is_deposit ){
        $output = '';
        if(
            (get_bk_option( 'booking_paypal_is_active' ) == 'On') ||
            ( (isset($_GET['booking_pay']))  &&
              (get_bk_option( 'booking_sage_is_active' ) != 'On') &&
              (get_bk_option( 'booking_ipay88_is_active' ) != 'On') )
          ) {



                   // $is_sand_box = true;
                    $is_sand_box = get_bk_option( 'booking_paypal_is_sandbox');

                    if ($is_sand_box == 'On') $is_sand_box = true;
                    else                      $is_sand_box = false;

                    $paypal_emeil               =  get_bk_option( 'booking_paypal_emeil' );
                    $paypal_curency             =  get_bk_option( 'booking_paypal_curency' );
                    $paypal_subject             =  get_bk_option( 'booking_paypal_subject' );
                    $paypal_subject           =  apply_bk_filter('wpdev_check_for_active_language', $paypal_subject );
                    $paypal_is_reference_box    =  get_bk_option( 'booking_paypal_is_reference_box' );           // checkbox
                    $paypal_reference_title_box =  get_bk_option( 'booking_paypal_reference_title_box' );
                    $paypal_return_url          =  get_bk_option( 'booking_paypal_return_url' );
                    $paypal_cancel_return_url   =  get_bk_option( 'booking_paypal_cancel_return_url' );
                    $paypal_button_type         =  get_bk_option( 'booking_paypal_button_type' );  // radio
                    $paypal_secure_merchant_id  =  get_bk_option( 'booking_paypal_secure_merchant_id'  );
                    $paypal_pro_hosted_solution =  get_bk_option( 'booking_paypal_pro_hosted_solution' );
                    $paypal_paymentaction       =  get_bk_option( 'booking_paypal_paymentaction' );
                    $paypal_subject = str_replace('[bookingname]',$bk_title[0]->title,$paypal_subject);

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




                    $paypal_subject = str_replace('[dates]',$my_short_dates,$paypal_subject); //$paypal_subject .= ' Booking type: ' . $bk_title[0]->title . '. For period: ' . $booking_days_count;

                    $my_d_c = explode(',', $booking_days_count);
                    $my_d_c = count($my_d_c);
                    $paypal_subject = str_replace('[datescount]',$my_d_c,$paypal_subject);

                    $output = '<div  class="paypal_div" style="text-align:left;clear:both;">';
                    if ($paypal_pro_hosted_solution != 'On') {
                        if (! $is_sand_box ) { // Live
                            $output .= '<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" style="text-align:left;">';
                            $output .= '<input type=\"hidden\" name=\"rm\" value=\"2\">';

                        } else {               // Sandbox
                            $output .= '<form action=\"https://www.sandbox.paypal.com/cgi-bin/webscr\" method=\"post\" style="text-align:left;">';                            
                            $output .= '<input type=\"hidden\" name=\"rm\" value=\"1\">';
                                // Text which show at return merchant button and url
                                // $output .= ' <input type=\"hidden\" name=\"cbt\" value=\"'.'?payed_booking=' . $booking_id .'&wp_nonce=' . $wp_nonce . '&pay_sys=paypal&stats=OK'.'\">';
                        }
                        $output .= '<input type=\"hidden\" name=\"cmd\" value=\"_xclick\" /> ';
                        $output .= '<input type=\"hidden\" name=\"amount\" size=\"10\" title=\"Cost\" value=\"'. $summ .'\" />';
                        $output .= '<input type=\"hidden\" name=\"business\" value=\"'.$paypal_emeil.'\" />';
                        $output .= '<input type=\"hidden\" name=\"no_shipping\" value=\"2\" /> <input type=\"hidden\" name=\"no_note\" value=\"1\" />  ';
                        
                    } else {    // Pro hosted sollution

                        if (! $is_sand_box ) { // Live
                            $output .= '<form action=\"https://securepayments.paypal.com/acquiringweb?cmd=_hosted-payment\" method=\"post\">';
                        } else {               // Sandbox
                            $output .= '<form action=\"https://securepayments.sandbox.paypal.com/acquiringweb?cmd=_hosted-payment\" method=\"post\">';
                        }
                        $output .='<input type=\"hidden\" name=\"cmd\" value=\"_hosted-payment\">';
                        $output .='<input type=\"hidden\" name=\"subtotal\" value=\"'. $summ .'\">';
                        $output .= '<input type=\"hidden\" name=\"business\" value=\"'.$paypal_secure_merchant_id.'\" />';
                        /*
                        //if (! $is_sand_box ) {                                // Previous configuration .....
                        //    $output .= '<form action=\"https://www.paypal.com/cgi-bin/webscr?" method=\"post\">';
                        //} else {               
                        //    $output .= '<form action=\"https://securepayments.sandbox.paypal.com/acquiringweb?cmd=_hosted-payment\" method=\"post\">';
                        //}
                        //$output .='<input type=\"hidden\" name=\"cmd\" value=\"_xclick">';
                        /**/
                    }
                    $output .= '<input type=\"hidden\" name=\"paymentaction\" value=\"'.$paypal_paymentaction.'\" />';


                    if ( strlen( WPDEV_BK_PLUGIN_URL .'/inc/payments/ipn.php' ) < 255 ) // Check for the PayPal 255 symbol restriction
                        $output .= '<input type=\"hidden\" name=\"notify_url\" value=\"'. WPDEV_BK_PLUGIN_URL .'/inc/payments/ipn.php' . '\" /> ';

                    $my_booking_id_type = apply_bk_filter('wpdev_booking_get_hash_using_booking_id',false, $booking_id );
                    if ($my_booking_id_type !== false) {
                        $my_edited_bk_hash    = $my_booking_id_type[0];
                        $my_boook_type        = $my_booking_id_type[1];
                        $output .= '<input type=\"hidden\" name=\"custom\" value=\"'. $my_edited_bk_hash . '\" /> ';
                    }

                    if ($paypal_pro_hosted_solution != 'On')
                        $output .= '<input type=\"hidden\" name=\"item_number\" value=\"'. $booking_id . '\" /> ';

                    $cost_currency = apply_bk_filter('get_currency_info', 'paypal');

                    //$output .= "<strong>". get_booking_title($booking_type) .': ' . $my_d_c .' ' . ( ($my_d_c==1)?__('day', 'wpdev-booking'):  __('days', 'wpdev-booking')) ."</strong> ". '<br />';
                    $is_show_it = get_bk_option( 'booking_paypal_is_description_show' );
                    if ($is_show_it == 'On') $output .= $paypal_subject . '<br />';

                    $summ_show = wpdev_bk_cost_number_format ( $summ  );
                    if ($is_deposit) $cost__title = __('Deposit', 'wpdev-booking')." : ";
                    else             $cost__title = __('Cost', 'wpdev-booking')." : ";
                    if ($cost_currency == $paypal_curency) $cost_summ_with_title = "<strong>".$cost__title . $summ_show ." " . $cost_currency ."</strong><br />";
                    else                                   $cost_summ_with_title = "<strong>".$cost__title . $cost_currency ." " . $summ_show ."</strong><br />";
                    $output .= $cost_summ_with_title;
                    /*
                    if ($is_deposit) {
                        $today_day = date('m.d.Y')  ;
                        $cost_summ_with_title .= ' ('  . $today_day .')';
                        make_bk_action('wpdev_make_update_of_remark' , $booking_id , $cost_summ_with_title , true );
                    }/**/
                    // Get all fields for biling info
                    $form_fields = get_form_content ($bkform, $booking_type);
                    $form_fields = $form_fields['_all_'];

                    if ($paypal_pro_hosted_solution != 'On')
                        if (  get_bk_option( 'booking_billing_customer_email' )  !== false ) {
                          $billing_customer_email  = (string) trim( get_bk_option( 'booking_billing_customer_email' ) . $booking_type );
                          if ( isset($form_fields[$billing_customer_email]) !== false ){
                              $email      = substr($form_fields[$billing_customer_email], 0, 127);
                              $output .= "<input type=\"hidden\" name=\"email\" value=\"$email\" />";
                          }
                        }

                    if ($paypal_pro_hosted_solution != 'On') $billing_prefix = '';
                    else                                     $billing_prefix = 'billing_';
                    if ( get_bk_option( 'booking_billing_firstnames' )  !== false ) {
                      $billing_firstnames      = (string) trim( get_bk_option( 'booking_billing_firstnames' ) . $booking_type );
                      if ( isset($form_fields[$billing_firstnames]) !== false ){
                          $first_name = substr($form_fields[$billing_firstnames], 0, 32);
                          $output .= "<input type=\"hidden\" name=\"".$billing_prefix."first_name\" value=\"$first_name\" />";
                      }
                    }
                    if ( get_bk_option( 'booking_billing_surname' )  !== false ) {
                      $billing_surname         = (string) trim( get_bk_option( 'booking_billing_surname' ) . $booking_type );
                      if ( isset($form_fields[$billing_surname]) !== false ){
                          $last_name  = substr($form_fields[$billing_surname], 0, 64);
                          $output .= "<input type=\"hidden\" name=\"".$billing_prefix."last_name\" value=\"$last_name\" />";
                      }
                    }
                    if ( get_bk_option( 'booking_billing_address1' )  !== false ) {
                      $billing_address1        = (string) trim( get_bk_option( 'booking_billing_address1' ) . $booking_type) ;
                      if ( isset($form_fields[$billing_address1]) !== false ){
                          $address1   = substr($form_fields[$billing_address1], 0, 100);
                          $output .= "<input type=\"hidden\" name=\"".$billing_prefix."address1\" value=\"$address1\" />";
                      }
                    }
                    if ( get_bk_option( 'booking_billing_city' )  !== false ) {
                      $billing_city            = (string) trim( get_bk_option( 'booking_billing_city' ) . $booking_type );
                      if ( isset($form_fields[$billing_city]) !== false ){
                          $city       = substr($form_fields[$billing_city], 0, 40);
                          $output .= "<input type=\"hidden\" name=\"".$billing_prefix."city\" value=\"$city\" />";
                      }
                    }
                    if ( get_bk_option( 'booking_billing_country' )  !== false ) {
                      $billing_country         = (string) trim( get_bk_option( 'booking_billing_country' ) . $booking_type );
                      if ( isset($form_fields[$billing_country]) !== false ){
                          $country    = substr($form_fields[$billing_country], 0, 2);
                          $output .= "<input type=\"hidden\" name=\"".$billing_prefix."country\" value=\"$country\" />";
                      }
                    }
                    if ( get_bk_option( 'booking_billing_post_code' )  !== false ) {
                          $billing_post_code       = (string) trim( get_bk_option( 'booking_billing_post_code' ) . $booking_type );
                          if ( isset($form_fields[$billing_post_code]) !== false ){
                              $zip        = substr($form_fields[$billing_post_code], 0, 32);
                              $output .= "<input type=\"hidden\" name=\"".$billing_prefix."zip\" value=\"$zip\" />";
                          }
                    }
                                // P a y P a l      f o r m  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                    $output .= "<input type=\"hidden\" name=\"item_name\" value=\"".substr($paypal_subject,0,127)."\" />";
                    $output .= "<input type=\"hidden\" name=\"currency_code\" value=\"$paypal_curency\" />";
                    //$output .= "<span style=\"font-size:10.0pt\"><strong> $paypal_subject</strong></span><br /><br />";


                    // Show the reference text box
                    if ($paypal_is_reference_box == 'On') {
                        $output .= "<br/><strong> $paypal_reference_title_box :</strong>";
                        $output .= '<input type=\"hidden\" name=\"on0\" value=\"Reference\" />';
                        $output .= '<input type=\"text\" name=\"os0\" maxlength=\"60\" /><br/><br/>';
                    }


                    $paypal_order_Successful  =  WPDEV_BK_PLUGIN_URL .'/'. WPDEV_BK_PLUGIN_FILENAME . '?payed_booking=' . $booking_id .'&wp_nonce=' . $wp_nonce . '&pay_sys=paypal&stats=OK' ;
                    $output .= '<input type=\"hidden\" name=\"return\" value=\"'.$paypal_order_Successful.'\" />';

                    $paypal_order_Failed      =  WPDEV_BK_PLUGIN_URL .'/'. WPDEV_BK_PLUGIN_FILENAME . '?payed_booking=' . $booking_id .'&wp_nonce=' . $wp_nonce . '&pay_sys=paypal&stats=FAILED' ;   //get_bk_option( 'booking_sage_order_Failed' );
                    $output .= '<input type=\"hidden\" name=\"cancel_return\" value=\"'.$paypal_order_Failed.'\" />';

                    $output .= "<input type=\"image\" src=\"$paypal_button_type\" name=\"submit\" style=\"border:none;\" alt=\"".__('Make payments with payPal - its fast, free and secure!', 'wpdev-booking')."\" />";

                    $output .= '</form></div>';
                    // P a y P a l      f o r m  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        }
        return $output ;

    }
    add_bk_filter('wpdev_bk_define_payment_form_paypal', 'wpdev_bk_define_payment_form_paypal');



    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //   A C T I V A T I O N   A N D   D E A C T I V A T I O N    O F   T H I S   P L U G I N    ///////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Activate
    function wpdev_bk_payment_activate_system_paypal() {
        global $wpdb;

        add_bk_option( 'booking_paypal_emeil', get_option('admin_email') );
        add_bk_option( 'booking_paypal_secure_merchant_id' , '' );
        add_bk_option( 'booking_paypal_curency', 'USD' );
        add_bk_option( 'booking_paypal_subject', sprintf(__('Payment for booking of the %s for days: %s' , 'wpdev-booking'),'[bookingname]','[dates]'));
        add_bk_option( 'booking_is_time_apply_to_cost','Off' );

        add_bk_option( 'booking_paypal_is_active','On' );
        add_bk_option( 'booking_paypal_pro_hosted_solution','Off' );
        add_bk_option( 'booking_paypal_is_reference_box', 'Off' );           // checkbox
        add_bk_option( 'booking_paypal_reference_title_box', __('Enter your phone' , 'wpdev-booking'));
        add_bk_option( 'booking_paypal_paymentaction', 'sale');
        add_bk_option( 'booking_paypal_return_url', site_url() );
        add_bk_option( 'booking_paypal_cancel_return_url', site_url() );
        add_bk_option( 'booking_paypal_button_type', 'https://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif' );  // radio
        add_bk_option( 'booking_paypal_price_period' , 'day' );
        add_bk_option( 'booking_paypal_is_sandbox','Off');
        add_bk_option( 'booking_paypal_is_description_show', 'Off' );
        add_bk_option( 'booking_paypal_is_auto_approve_cancell_booking', 'Off' );


        add_bk_option( 'booking_paypal_ipn_is_active' , 'Off' );
        add_bk_option( 'booking_paypal_ipn_is_send_verified_email' , 'On');
        add_bk_option( 'booking_paypal_ipn_verified_email' ,get_option('admin_email'));
        add_bk_option( 'booking_paypal_ipn_is_send_invalid_email' , 'On');
        add_bk_option( 'booking_paypal_ipn_invalid_email' , get_option('admin_email') );
        add_bk_option( 'booking_paypal_ipn_is_send_error_email' , 'Off');
        add_bk_option( 'booking_paypal_ipn_error_email' , get_option('admin_email') );

        add_bk_option( 'booking_paypal_ipn_use_ssl' , 'On');
        add_bk_option( 'booking_paypal_ipn_use_curl' , 'Off');
    }
    add_bk_action( 'wpdev_bk_payment_activate_system', 'wpdev_bk_payment_activate_system_paypal');


    // Activate
    function wpdev_bk_payment_deactivate_system_paypal() {
        global $wpdb;
        
        delete_bk_option( 'booking_paypal_emeil' );
        delete_bk_option( 'booking_paypal_secure_merchant_id'  );
        delete_bk_option( 'booking_paypal_curency' );
        delete_bk_option( 'booking_paypal_subject' );
        delete_bk_option( 'booking_is_time_apply_to_cost' );
        delete_bk_option( 'booking_paypal_is_active' );
        
        delete_bk_option( 'booking_paypal_pro_hosted_solution' );
        delete_bk_option( 'booking_paypal_is_reference_box' );           // checkbox
        delete_bk_option( 'booking_paypal_reference_title_box' );
        delete_bk_option( 'booking_paypal_paymentaction' );
        delete_bk_option( 'booking_paypal_return_url' );
        delete_bk_option( 'booking_paypal_cancel_return_url' );
        delete_bk_option( 'booking_paypal_button_type' );  // radio
        delete_bk_option( 'booking_paypal_price_period' );
        delete_bk_option( 'booking_paypal_is_sandbox');
        delete_bk_option( 'booking_paypal_is_description_show' );
        delete_bk_option( 'booking_paypal_is_auto_approve_cancell_booking'  );

        delete_bk_option( 'booking_paypal_ipn_is_active' );
        delete_bk_option( 'booking_paypal_ipn_is_send_verified_email' );
        delete_bk_option( 'booking_paypal_ipn_verified_email' );
        delete_bk_option( 'booking_paypal_ipn_is_send_invalid_email' );
        delete_bk_option( 'booking_paypal_ipn_invalid_email' );
        delete_bk_option( 'booking_paypal_ipn_is_send_error_email' );
        delete_bk_option( 'booking_paypal_ipn_error_email' );
        delete_bk_option( 'booking_paypal_ipn_use_ssl' );
        delete_bk_option( 'booking_paypal_ipn_use_curl' );

    }
    add_bk_action( 'wpdev_bk_payment_deactivate_system', 'wpdev_bk_payment_deactivate_system_paypal');

?>