<?php
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  S e t t i n g s    f u n c t i o n s       ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    function wpdev_bk_payment_show_tab_in_top_settings_atos(){
        ?><a href="#"
           onclick="javascript:
                   jQuery('.visibility_container').css('display','none');
                   jQuery('#visibility_container_atos').css('display','block');
                   jQuery('.nav-tab').removeClass('booking-submenu-tab-selected');
                   jQuery(this).addClass('booking-submenu-tab-selected');"
               rel="tooltip" class="tooltip_bottom nav-tab  booking-submenu-tab <?php
                                     if ( get_bk_option( 'booking_atos_is_active' ) != 'On' ) echo ' booking-submenu-tab-disabled '; ?>"
                                     original-title="<?php _e('Integration of atos payment system', 'wpdev-booking');?>">
            <?php _e('Atos', 'wpdev-booking');?>
            <input type="checkbox"  <?php if ( get_bk_option( 'booking_atos_is_active' ) == 'On' ) echo ' checked="CHECKED" '; ?>
                   name="atos_is_active_dublicated" id="atos_is_active_dublicated"
                   onchange="document.getElementById('atos_is_active').checked=this.checked;" >
        </a>
        <script type="text/javascript">
            jQuery(document).ready( function(){
                recheck_active_itmes_in_top_menu('atos_is_active',   'atos_is_active_dublicated');
            });
        </script>
        <?php
    }
    add_bk_action( 'wpdev_bk_payment_show_tab_in_top_settings', 'wpdev_bk_payment_show_tab_in_top_settings_atos');

    // Settings page for    Atos
    function wpdev_bk_payment_show_settings_content_atos(){
          if (isset($_POST) && !empty($_POST)) {
            if (isset( $_POST['atos_is_active'] ))    $atos_is_active = 'On';
            else                                      $atos_is_active = 'Off';

            update_bk_option( 'booking_atos_is_active' , $atos_is_active );
          }
          
        
        $atos_is_active         =  get_bk_option( 'booking_atos_is_active' );
        echo $atos_is_active;
        ?>
        
        <div id="visibility_container_atos" class="visibility_container" style="display:none;">
          <div class='meta-box'>
            <div <?php $my_close_open_win_id = 'bk_settings_costs_atos_payment'; ?>  id="<?php echo $my_close_open_win_id; ?>" class="postbox <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>" > <div title="<?php _e('Click to toggle','wpdev-booking'); ?>" class="handlediv"  onclick="javascript:verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_win_id; ?>');"><br></div>
              <h3 class='hndle'><span><?php _e('atos payment customization', 'wpdev-booking'); ?></span></h3>
              <div class="inside">
                <table class="form-table settings-table">
                  <tbody>
                      <tr valign="top">
                          <th scope="row">
                              <label for="atos_is_active" ><?php _e('atos payment active', 'wpdev-booking'); ?>:</label>
                          </th>
                          <td>
                              <input <?php if ($atos_is_active == 'On') echo "checked"; ?>  value="<?php echo $atos_is_active; ?>" name="atos_is_active" id="atos_is_active" type="checkbox"
                                     onchange="document.getElementById('atos_is_active_dublicated').checked=this.checked;"
                                                                                            />
                              <span class="description"><?php _e(' Check this checkbox for using Atos payment.', 'wpdev-booking');?></span>
                          </td>
                      </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <?php
        echo $atos_is_active;
    }
    add_bk_action( 'wpdev_bk_payment_show_settings_content', 'wpdev_bk_payment_show_settings_content_atos');

    function wpdev_bk_define_payment_form_atos($blank, $booking_id, $summ,$bk_title, $booking_days_count, $booking_type, $bkform, $wp_nonce, $is_deposit ){
      $form_fields = get_form_content($bkform, $booking_type);
      $form_fields = $form_fields['_all_'];

      $output = '<div id="atos_div" style="text-align:center;clear:both;">';
      $output .= '<h3>Système de paiement Sécurisé</h3>';
      $output .= "<p>Vous devez : ".$summ.",00€</p>";
      $output .= "<form action=\"http://fr.winetourbooking.com/paiement\" method=\"POST\">";

      if (  get_bk_option( 'booking_billing_customer_email' )  !== false ) {
        $billing_customer_email  = (string) trim( get_bk_option( 'booking_billing_customer_email' ) . $booking_type );
        if ( isset($form_fields[$billing_customer_email]) !== false ){
            $email = substr($form_fields[$billing_customer_email], 0, 127);
            $output .= "<input type=\"hidden\" name=\"email\" value=\"$email\" />";
        }
      }

      if ( get_bk_option( 'booking_billing_firstnames' )  !== false ) {
        $billing_firstnames      = (string) trim( get_bk_option( 'booking_billing_firstnames' ) . $booking_type );
        if ( isset($form_fields[$billing_firstnames]) !== false ){
            $first_name = substr($form_fields[$billing_firstnames], 0, 32);
            $output .= "<input type=\"hidden\" name=\"first_name\" value=\"". $first_name ."\"/>";
        }
      }
      if ( get_bk_option( 'booking_billing_surname' )  !== false ) {
        $billing_surname         = (string) trim( get_bk_option( 'booking_billing_surname' ) . $booking_type );
        if ( isset($form_fields[$billing_surname]) !== false ){
            $last_name  = substr($form_fields[$billing_surname], 0, 64);
            $output .= "<input type=\"hidden\" name=\"last_name\" value=\"". $last_name ."\"/>";
        }
      }
      if ( get_bk_option( 'booking_billing_address1' )  !== false ) {
        $billing_address1        = (string) trim( get_bk_option( 'booking_billing_address1' ) . $booking_type) ;
        if ( isset($form_fields[$billing_address1]) !== false ){
            $address1   = substr($form_fields[$billing_address1], 0, 100);
            $output .= "<input type=\"hidden\" name=\"address1\" value=\"". $address1 ."\"/>";
        }
      }
      if ( get_bk_option( 'booking_billing_city' )  !== false ) {
        $billing_city            = (string) trim( get_bk_option( 'booking_billing_city' ) . $booking_type );
        if ( isset($form_fields[$billing_city]) !== false ){
            $city       = substr($form_fields[$billing_city], 0, 40);
            $output .= "<input type=\"hidden\" name=\"city\" value=\"". $city ."\"/>";
        }
      }
      if ( get_bk_option( 'booking_billing_country' )  !== false ) {
        $billing_country         = (string) trim( get_bk_option( 'booking_billing_country' ) . $booking_type );
        if ( isset($form_fields[$billing_country]) !== false ){
            $country    = substr($form_fields[$billing_country], 0, 2);
            $output .= "<input type=\"hidden\" name=\"country\" value=\"". $country ."\"/>";
        }
      }
      if ( get_bk_option( 'booking_billing_post_code' )  !== false ) {
            $billing_post_code       = (string) trim( get_bk_option( 'booking_billing_post_code' ) . $booking_type );
            if ( isset($form_fields[$billing_post_code]) !== false ){
                $zip        = substr($form_fields[$billing_post_code], 0, 32);
                $output .= "<input type=\"hidden\" name=\"zip\" value=\"". $zip ."\"/>";
            }
      }

      if ( isset($form_fields["phone".$booking_type]) !== false ){
        $phone        = $form_fields["phone".$booking_type];
        $output .= "<input type=\"hidden\" name=\"phone\" value=\"". $phone ."\"/>";
      }

      if ( isset($form_fields["visitors".$booking_type]) !== false ){
        $visitors        = $form_fields["visitors".$booking_type];
        $output .= "<input type=\"hidden\" name=\"nb_pers\" value=\"". $visitors ."\"/>";
      }

      if ( isset($form_fields["starttime".$booking_type]) !== false ){
        $starttime        = $form_fields["starttime".$booking_type];
        $output .= "<input type=\"hidden\" name=\"heure_visite\" value=\"". $starttime ."\"/>";
      }

      if ( isset($form_fields["NumCommande".$booking_type]) !== false ){
        $NumCommande        = $form_fields["NumCommande".$booking_type];
        $output .= "<input type=\"hidden\" name=\"NumCommande\" value=\"". $NumCommande ."\"/>";
      }

      $title = getNomPropriete($booking_type);
      foreach ($title as $key) {
          $propriete = $key->title;
      }
      $output .= "<input type=\"hidden\" name=\"propriete\" value=\"". $propriete ."\"/>";

      $paypal_order_Successful  =  WPDEV_BK_PLUGIN_URL .'/'. WPDEV_BK_PLUGIN_FILENAME . '?payed_booking=' . $booking_id .'&wp_nonce=' . $wp_nonce . '&pay_sys=paypal&stats=OK' ;
      $output .= '<input type=\"hidden\" name=\"return\" value=\"'.$paypal_order_Successful.'\" />';

      $paypal_order_Failed      =  WPDEV_BK_PLUGIN_URL .'/'. WPDEV_BK_PLUGIN_FILENAME . '?payed_booking=' . $booking_id .'&wp_nonce=' . $wp_nonce . '&pay_sys=paypal&stats=FAILED' ;   //get_bk_option( 'booking_sage_order_Failed' );

      $array_find = array(" ","â", "é", "è");
      $array_replace = array("-", "a", "e", "e");
      $title = str_replace($array_find, $array_replace, $propriete);
      $output .= '<input type=\"hidden\" name=\"cancel_return\" value=\"http://bordeaux.winetourbooking.com/'.$title.'\" />';

      $output .= '<input type="hidden" name="booking_type" value="'.$booking_type.'" />';

      $output .= '<input type="hidden" name="summ" value="'.$summ.'" />';

      $output .= '<input type="hidden" name="ip_client" value="'.$REMOTE_ADDR.'" />';

      //$output .= '<input type="submit" name="validez" value="Validez Vos informations" />';
      $output .= '<input src=\"http://bordeaux.winetourbooking.com/wp-content/plugins/booking.multiuser.3.0/img/payer.png" type="image" Value="submit"> ';

      $output .= "<p>Vous devez, afin de valider vos réservations, payer chacune d'elle séparément. Afin d'améliorer notre service, vous pourrez très prochainement régler en une fois l'ensemble de vos réservations ... <br />L'équipe de WineTourBooking</p>";

      $output .= "</form>";

      $output .= "</div>";
      return $output;
    }
    add_bk_filter('wpdev_bk_define_payment_form_atos', 'wpdev_bk_define_payment_form_atos');

    function wpdev_bk_payment_activate_system_atos() {
      add_bk_option( 'booking_atos_is_active','On' );
    }
    add_bk_action( 'wpdev_bk_payment_activate_system', 'wpdev_bk_payment_activate_system_atos');

    function wpdev_bk_payment_deactivate_system_atos() {
      delete_bk_option( 'booking_atos_is_active' );
    }
    add_bk_action( 'wpdev_bk_payment_deactivate_system', 'wpdev_bk_payment_deactivate_system_atos');
?>