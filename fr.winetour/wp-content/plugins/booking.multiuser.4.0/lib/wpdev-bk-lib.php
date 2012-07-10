<?php if (  (! isset( $_GET['merchant_return_link'] ) ) && (! isset( $_GET['payed_booking'] ) ) && (!function_exists ('get_option')  )  ) { die('You do not have permission to direct access to this file !!!'); }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  S u p p o r t    f u n c t i o n s       ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    // Change date format
    function wpdevbk_get_date_in_correct_format( $dt ) {

        $date_format = get_bk_option( 'booking_date_format');
        $time_format = get_bk_option( 'booking_time_format');
        if (empty($date_format)) $date_format = "m / d / Y, D";
        if (empty($time_format)) $time_format = 'h:i a';
        $my_time = date('H:i:s' , mysql2date('U',$dt) );
        if ($my_time == '00:00:00')     $time_format='';
        $bk_date = date_i18n($date_format  , mysql2date('U',$dt));
        $bk_time = date_i18n(' ' . $time_format  , mysql2date('U',$dt));
        if ($bk_time == ' ') $bk_time = '';

        return array($bk_date, $bk_time);
    }

    // Check if nowday is tommorow from previosday
    function wpdevbk_is_next_day($nowday, $previosday) {

        if ( empty($previosday) ) return false;

        $nowday_d = (date('m.d.Y',  mysql2date('U', $nowday ))  );
        $prior_day = (date('m.d.Y',  mysql2date('U', $previosday ))  );
        if ($prior_day == $nowday_d)    return true;                // if its the same date


        $previos_array = (date('m.d.Y',  mysql2date('U', $previosday ))  );
        $previos_array = explode('.',$previos_array);
        $prior_day =  date('m.d.Y' , mktime(0, 0, 0, $previos_array[0], ($previos_array[1]+1), $previos_array[2] ));


        if ($prior_day == $nowday_d)    return true;                // zavtra
        else                            return false;               // net
    }

    // Transform the REQESTS parameters (GET and POST) into URL
    function get_params_in_url( $exclude_prms = array(), $only_these_parameters = false ){

        $url_start = 'admin.php?';                          //$url_start = 'admin.php?page='. WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME. 'wpdev-booking';

        foreach ($_REQUEST as $prm_key => $prm_value) {
            if ( ! in_array($prm_key, $exclude_prms ) )
                    if ( ($only_these_parameters === false) || ( in_array($prm_key, $only_these_parameters ) ) )
                $url_start .= $prm_key .'=' . $prm_value . '&' ;
        }
        $url_start = substr($url_start, 0, -1);
        return $url_start ;
    }

    // Load default filter parameters only for the initial loading of page.     // ShiftP
    function wpdevbk_get_default_bk_listing_filter_set_to_params( $filter_name ) {

        $wpdevbk_saved_filter  = get_user_option( 'booking_listing_filter_' . $filter_name ) ;

        if ($wpdevbk_saved_filter !== false) {

            $wpdevbk_saved_filter = str_replace('admin.php?', '', $wpdevbk_saved_filter);

            $wpdevbk_saved_filter = explode('&',$wpdevbk_saved_filter);
            $wpdevbk_filter_params = array();
            foreach ($wpdevbk_saved_filter as $bkfilter) {
                $bkfilter_key_value = explode('=',$bkfilter);
                $wpdevbk_filter_params[ $bkfilter_key_value[0] ] = trim($bkfilter_key_value[1]);
            }
            // Get here default selected tab
            $booking_default_toolbar_tab = get_bk_option( 'booking_default_toolbar_tab');
            if ( $booking_default_toolbar_tab !== false)
                $wpdevbk_filter_params[ 'tab' ] = $booking_default_toolbar_tab;  // 'filter' / 'actions' ;

            
            if (! isset($_REQUEST['wh_approved'])) {                            // We are do not have approved or pending value, so its mean that user open the page as default, without clicking on Filter apply.
                foreach ($wpdevbk_filter_params as $filter_key => $filter_value) {
                    $_REQUEST[$filter_key] = $filter_value ;                    // Set to REQUEST
                }
            }

        }
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  Control elements      ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function wpdevbk_selectbox_normal_filter($wpdevbk_id, $wpdevbk_selectors, $wpdevbk_control_label, $wpdevbk_help_block){

        if (isset($_REQUEST[$wpdevbk_id]))    $wpdevbk_value = $_REQUEST[$wpdevbk_id];
        else                                  $wpdevbk_value = '';
        $wpdevbk_selector_default = array_search($wpdevbk_value, $wpdevbk_selectors);
        if ($wpdevbk_selector_default === false) $wpdevbk_selector_default = current($wpdevbk_selectors);
          ?>
          <div class="control-group" style="float:left;">
            <label for="<?php echo $wpdevbk_id; ?>" class="control-label"><?php echo $wpdevbk_control_label; ?></label>
            <div class="inline controls">
                <div class="btn-group">
                    <select class="span8 chzn-select" id="<?php echo $wpdevbk_id; ?>" name="<?php echo $wpdevbk_id; ?>" data-placeholder="<?php echo $wpdevbk_help_block; ?>"                       
                            >
                      <?php
                      foreach ($wpdevbk_selectors as $key=>$value) {
                        if ($value != 'divider') {
                            ?><option <?php if ($wpdevbk_value == $value ) echo ' selected="SELECTED" '; ?> 
                                <?php if (strpos($key , '&nbsp;') === false) echo ' style="font-weight:bold;" '; ?>
                                value="<?php echo $value; ?>"><?php echo $key; ?></option><?php
                        } else {
                            ?><?php
                        }
                      } ?>
                  </select>
                </div>                
                <p class="help-block" style="margin-top: 0px;"><?php echo $wpdevbk_help_block; ?></p>
            </div>
          </div>
            <script type="text/javascript">
              jQuery(document).ready( function(){
                jQuery("#<?php echo $wpdevbk_id; ?>").chosen({no_results_text: "No results matched"});
              });
            </script>
            <style type="text/css">
                .bookingpage .wpdevbk a.chzn-single {
                    height: 25px;
                    margin-top: 1px;
                }
            </style>
        <?php
    }


    function wpdevbk_selectbox_filter($wpdevbk_id, $wpdevbk_selectors, $wpdevbk_control_label, $wpdevbk_help_block, $wpdevbk_default_value = ''){

            if (isset($_REQUEST[$wpdevbk_id]))    $wpdevbk_value = $_REQUEST[$wpdevbk_id];
            else                                  $wpdevbk_value = $wpdevbk_default_value;
            $wpdevbk_selector_default = array_search($wpdevbk_value, $wpdevbk_selectors);
            if ($wpdevbk_selector_default === false) {
                $wpdevbk_selector_default = key($wpdevbk_selectors);
                $wpdevbk_selector_default_value = current($wpdevbk_selectors);
            } else $wpdevbk_selector_default_value = $wpdevbk_value;
          ?>
          <div class="control-group" style="float:left;">
            <label for="<?php echo $wpdevbk_id; ?>" class="control-label"><?php echo $wpdevbk_control_label; ?></label>
            <div class="inline controls">
                <div class="btn-group">
                  <a href="#" data-toggle="dropdown" id="<?php echo $wpdevbk_id;?>_selector" class="btn dropdown-toggle"><?php echo $wpdevbk_selector_default; ?> &nbsp; <span class="caret"></span></a>
                  <ul class="dropdown-menu">
                      <?php
                      foreach ($wpdevbk_selectors as $key=>$value) {
                        if ($value != 'divider') {
                          ?><li><a href="#" onclick="javascript:jQuery('#<?php echo $wpdevbk_id;?>_selector').html(jQuery(this).html() + ' &nbsp; <span class=&quot;caret&quot;></span>');jQuery('#<?php echo $wpdevbk_id; ?>').val('<?php echo $value; ?>');" ><?php echo $key; ?></a></li><?php
                        } else { ?><li class="divider"></li><?php }
                      } ?>
                  </ul>
                  <input type="hidden" value="<?php echo $wpdevbk_selector_default_value; ?>" id="<?php echo $wpdevbk_id; ?>" name="<?php echo $wpdevbk_id; ?>" />
                </div>
              <p class="help-block"><?php echo $wpdevbk_help_block; ?></p>
            </div>
          </div>
        <?php
    }


    function wpdevbk_checkboxbutton_filter($wpdevbk_id, $wpdevbk_selectors, $wpdevbk_control_label, $wpdevbk_help_block){

            if (isset($_REQUEST[$wpdevbk_id]))    $wpdevbk_value = $_REQUEST[$wpdevbk_id];
            else                                  $wpdevbk_value = '';
            $wpdevbk_selector_default = array_search($wpdevbk_value, $wpdevbk_selectors);
            if ($wpdevbk_selector_default === false) $wpdevbk_selector_default = current($wpdevbk_selectors);
          ?>
          <div class="control-group" style="float:left;">
           <!--label for="<?php echo $wpdevbk_id; ?>" class="control-label"><?php echo $wpdevbk_control_label; ?>:</label-->
           <div class="inline controls">
            
            <a href="#" class="btn" data-toggle="button" name="checkboxbutton_<?php echo $wpdevbk_id; ?>" id="checkboxbutton_<?php echo $wpdevbk_id; ?>"
               onclick="javascript:if (jQuery(this).attr('class').indexOf('active')>0) { jQuery('#<?php echo $wpdevbk_id; ?>').val('<?php echo $wpdevbk_selectors[0]; ?>'); } else { jQuery('#<?php echo $wpdevbk_id; ?>').val('<?php echo $wpdevbk_selectors[1]; ?>'); }; "

               ><?php echo $wpdevbk_control_label; ?></a>
            
            <script type="text/javascript">
                jQuery('#checkboxbutton_<?php echo $wpdevbk_id; ?>').button();
                <?php if ($wpdevbk_value == '1') {  // Press the button ?>
                    jQuery('#checkboxbutton_<?php echo $wpdevbk_id; ?>').button('toggle');
                <?php } ?>
            </script>

            <input type="hidden" value="<?php echo $wpdevbk_value; ?>" id="<?php echo $wpdevbk_id; ?>" name="<?php echo $wpdevbk_id; ?>" />

            <p class="help-block"><?php echo $wpdevbk_help_block; ?></p>
           </div>
          </div>
        <?php
    }


    function wpdevbk_text_filter($wpdevbk_id, $wpdevbk_control_label, $wpdevbk_help_block) {

            if (isset($_REQUEST[$wpdevbk_id]))    $wpdevbk_value = $_REQUEST[$wpdevbk_id];
            else                                  $wpdevbk_value = '';
        ?>
          <div class="control-group" style="float:left;">
           <!--label for="<?php echo $wpdevbk_id; ?>" class="control-label"><?php echo $wpdevbk_control_label; ?>:</label-->
           <div class="inline controls">
            <input type="text" class="span2"  placeholder="<?php echo $wpdevbk_control_label; ?>" value="<?php echo $wpdevbk_value; ?>" id="<?php echo $wpdevbk_id; ?>" name="<?php echo $wpdevbk_id; ?>" />
            <p class="help-block"><?php echo $wpdevbk_help_block; ?></p>
           </div>
          </div>
        <?php
    }


    function wpdevbk_text_from_to_filter($wpdevbk_id, $wpdevbk_control_label, $wpdevbk_placeholder, $wpdevbk_help_block, $wpdevbk_id2, $wpdevbk_control_label2, $wpdevbk_placeholder2, $wpdevbk_help_block2, $wpdevbk_width, $input_append = '') {

            if (isset($_REQUEST[$wpdevbk_id]))    $wpdevbk_value = $_REQUEST[$wpdevbk_id];
            else                                  $wpdevbk_value = '';
            if (isset($_REQUEST[$wpdevbk_id2]))   $wpdevbk_value2 = $_REQUEST[$wpdevbk_id2];
            else                                  $wpdevbk_value2 = '';
        ?>
          <div class="control-group" style="float:left;">
           <label for="<?php echo $wpdevbk_id; ?>" class="control-label"><?php echo $wpdevbk_control_label; ?></label>
           <div class="inline controls">
            <?php if ( $input_append !== '' ) { ?><div class="input-append"><?php } ?>
            <input type="text" class="<?php echo $wpdevbk_width; ?>"  placeholder="<?php echo $wpdevbk_placeholder; ?>" value="<?php echo $wpdevbk_value; ?>" id="<?php echo $wpdevbk_id; ?>" name="<?php echo $wpdevbk_id; ?>" />
            <?php if ( $input_append !== '' ) { ?><span class="add-on"><?php echo $input_append ?></span></div><?php } ?>
            <p class="help-block"><?php echo $wpdevbk_help_block; ?></p>
           </div>
          </div>
           <div class="control-group" style="float:left;">
            <label for="<?php echo $wpdevbk_id2; ?>" class="control-label" style="margin-left: -5px; text-align: left; width: 10px;"><?php echo $wpdevbk_control_label2; ?></label>
            <div class="inline controls">
            <?php if ( $input_append !== '' ) { ?><div class="input-append"><?php } ?>
                <input type="text" class="<?php echo $wpdevbk_width; ?>"  placeholder="<?php echo $wpdevbk_placeholder2; ?>" value="<?php echo $wpdevbk_value2; ?>" id="<?php echo $wpdevbk_id2; ?>" name="<?php echo $wpdevbk_id2; ?>" />
            <?php if ( $input_append !== '' ) { ?><span class="add-on"><?php echo $input_append ?></span></div><?php } ?>
            <p class="help-block"><?php echo $wpdevbk_help_block2; ?></p>
           </div>
          </div>
        <?php
    }


    function wpdevbk_dates_selection_for_filter($wpdevbk_id,  $wpdevbk_id2,
                                                $wpdevbk_control_label,    $wpdevbk_help_block,
                                                $wpdevbk_width, $input_append = '',
                                                $exclude_items = array() , $default_item = 0) {
        
            if (isset($_REQUEST[$wpdevbk_id]))    $wpdevbk_value = $_REQUEST[$wpdevbk_id];
            else  {                               $wpdevbk_value = $default_item; }
            if (isset($_REQUEST[$wpdevbk_id2]))   $wpdevbk_value2 = $_REQUEST[$wpdevbk_id2];
            else                                  $wpdevbk_value2 = '';

            $dates_interval = array(  1 => '1' . ' ' . __('day', 'wpdev-booking') ,
                                      2 => '2' . ' ' . __('days', 'wpdev-booking') ,
                                      3 => '3' . ' ' . __('days', 'wpdev-booking') ,
                                      4 => '4' . ' ' . __('days', 'wpdev-booking') ,
                                      5 => '5' . ' ' . __('days', 'wpdev-booking') ,
                                      6 => '6' . ' ' . __('days', 'wpdev-booking') ,
                                      7 => '1' . ' ' . __('week', 'wpdev-booking') ,
                                      14 => '2' . ' ' . __('weeks', 'wpdev-booking') ,
                                      30 => '1' . ' ' . __('month', 'wpdev-booking') ,
                                      60 => '2' . ' ' . __('months', 'wpdev-booking') ,
                                      90 => '3' . ' ' . __('months', 'wpdev-booking') ,
                                      183 => '6' . ' ' . __('months', 'wpdev-booking') ,
                                      365 => '1' . ' ' . __('Year', 'wpdev-booking')  );

            $filter_labels = array(
                                __('Actual dates', 'wpdev-booking'),
                                __('Today', 'wpdev-booking'),
                                __('Previous dates', 'wpdev-booking'),
                                __('All dates', 'wpdev-booking'),
                                __('Some Next days', 'wpdev-booking'),
                                __('Some Prior days', 'wpdev-booking'),
                                __('Fixed dates interval', 'wpdev-booking'),
                               );
        ?>
            <script type="text/javascript">

                function wpdevbk_days_selection_in_filter( primary_field, secondary_field, primary_value, secondary_value ) {

                    if (primary_value == '0') {         // Actual  = '', ''
                        jQuery('#' + primary_field   ).val('0');
                        jQuery('#' + secondary_field ).val('');
                        jQuery('#'+primary_field+'_selector').html( '<?php echo esc_js($filter_labels[0]); ?>' + ' &nbsp; <span class="caret"></span>');
                    } else if (primary_value == '1') {  // Today
                        jQuery('#' + primary_field   ).val('1');
                        jQuery('#' + secondary_field ).val('');
                        jQuery('#'+primary_field+'_selector').html( '<?php echo esc_js($filter_labels[1]); ?>' + ' &nbsp; <span class="caret"></span>');
                    } else if (primary_value == '2') {  // Previous
                        jQuery('#' + primary_field   ).val('2');
                        jQuery('#' + secondary_field ).val('');
                        jQuery('#'+primary_field+'_selector').html( '<?php echo esc_js($filter_labels[2]); ?>' + ' &nbsp; <span class="caret"></span>');
                    } else if (primary_value == '3') { // All
                        jQuery('#' + primary_field   ).val('3');
                        jQuery('#' + secondary_field ).val('');
                        jQuery('#'+primary_field+'_selector').html( '<?php echo esc_js($filter_labels[3]); ?>' + ' &nbsp; <span class="caret"></span>');
                    } else if (primary_value == '4') { // Next
                        jQuery('#' + primary_field   ).val('4');
                        jQuery('#' + secondary_field ).val(secondary_value);
                        jQuery('#'+primary_field+'_selector').html( '<?php echo esc_js($filter_labels[4]) ; ?>' + ' &nbsp; <span class="caret"></span>');
                    } else if (primary_value == '5') { // Prior
                        jQuery('#' + primary_field   ).val('5');
                        jQuery('#' + secondary_field ).val(secondary_value);
                        jQuery('#'+primary_field+'_selector').html( '<?php echo esc_js($filter_labels[5]) ; ?>' + ' &nbsp; <span class="caret"></span>');
                    } else if (primary_value == '6') { // Fixed
                        jQuery('#' + primary_field   ).val(secondary_value[0]);
                        jQuery('#' + secondary_field ).val(secondary_value[1]);
                        jQuery('#'+primary_field+'_selector').html( '<?php echo esc_js($filter_labels[6]) ; ?>' + ' &nbsp; <span class="caret"></span>');
                    }
                    jQuery('#' + primary_field+ '_container').hide();
                }

            </script>

          <div class="control-group" style="float:left;">
            <label for="<?php echo $wpdevbk_id; ?>" class="control-label"><?php echo $wpdevbk_control_label; ?></label>
            <div class="inline controls">
                <input type="hidden" value="<?php echo $wpdevbk_value; ?>"  id="<?php echo $wpdevbk_id; ?>"  name="<?php echo $wpdevbk_id; ?>" />
                <input type="hidden" value="<?php echo $wpdevbk_value2; ?>" id="<?php echo $wpdevbk_id2; ?>" name="<?php echo $wpdevbk_id2; ?>" />
                <div class="btn-group">
                    <a onclick="javascript:jQuery('#<?php echo $wpdevbk_id; ?>_container').show();" id="<?php echo $wpdevbk_id; ?>_selector" data-toggle="dropdown"  class="btn dropdown-toggle" href="#"><?php
                    if ( isset($_REQUEST[ $wpdevbk_id ]) ) {
                        if ( $_REQUEST[ $wpdevbk_id ] == '0' ) echo $filter_labels[0];
                        else if ( $_REQUEST[ $wpdevbk_id ] == '1' ) echo $filter_labels[1];
                        else if ( $_REQUEST[ $wpdevbk_id ] == '2' ) echo $filter_labels[2];
                        else if ( $_REQUEST[ $wpdevbk_id ] == '3' ) echo $filter_labels[3];
                        else if ( $_REQUEST[ $wpdevbk_id ] == '4' ) echo $filter_labels[4];
                        else if ( $_REQUEST[ $wpdevbk_id ] == '5' ) echo $filter_labels[5];
                        else echo $filter_labels[6];
                    } else {
                        echo $filter_labels[ $default_item ];
                    }
                    ?> &nbsp; <span class="caret"></span></a>
                    <ul class="dropdown-menu" style="display:none;" id="<?php echo $wpdevbk_id; ?>_container" >
                        <?php   if ( ! in_array(0, $exclude_items ) ) { ?>
                        <li><a onclick="javascript:wpdevbk_days_selection_in_filter( '<?php echo $wpdevbk_id; ?>', '<?php echo $wpdevbk_id2; ?>', '0' , '' );" href="#"><?php echo $filter_labels[0]; ?></a></li>
                        <?php } if ( ! in_array(1, $exclude_items ) ) { ?>
                        <li><a onclick="javascript:wpdevbk_days_selection_in_filter( '<?php echo $wpdevbk_id; ?>', '<?php echo $wpdevbk_id2; ?>', '1' , '' );" href="#"><?php echo $filter_labels[1]; ?></a></li>
                        <?php } if ( ! in_array(2, $exclude_items ) ) { ?>
                        <li><a onclick="javascript:wpdevbk_days_selection_in_filter( '<?php echo $wpdevbk_id; ?>', '<?php echo $wpdevbk_id2; ?>', '2' , '' );" href="#"><?php echo $filter_labels[2]; ?></a></li>
                        <?php } if ( ! in_array(3, $exclude_items ) ) { ?>
                        <li><a onclick="javascript:wpdevbk_days_selection_in_filter( '<?php echo $wpdevbk_id; ?>', '<?php echo $wpdevbk_id2; ?>', '3' , '' );" href="#"><?php echo $filter_labels[3]; ?></a></li>
                        <?php } ?>
                        <li class="divider"></li>
                        <?php   if ( ! in_array(4, $exclude_items ) ) { ?>
                        <li><div style="margin-left:15px;"> 
                                <input <?php if ( isset($_REQUEST[ $wpdevbk_id . 'days_interval_Radios']) ) if ( $_REQUEST[ $wpdevbk_id . 'days_interval_Radios'] == 'next' ) echo ' checked="CHECKED" ';  ?>
                                    type="radio" value="next" id="<?php echo $wpdevbk_id; ?>days_interval1" name="<?php echo $wpdevbk_id; ?>days_interval_Radios" style="margin:-2px 5px 0px -5px;">
                                <span><?php _e('Next', 'wpdev-booking'); ?>: </span>
                                <select class="span1" style="width:85px;" id="<?php echo $wpdevbk_id; ?>next" name="<?php echo $wpdevbk_id; ?>next" >
                                  <?php
                                  foreach ($dates_interval as $key=>$value) {
                                    if ($value != 'divider') {
                                        ?><option <?php if ( isset($_REQUEST[ $wpdevbk_id . 'next']) ) if ( $_REQUEST[ $wpdevbk_id . 'next'] == $key ) echo ' selected="SELECTED" '; ?>
                                            value="<?php echo $key; ?>"><?php echo $value; ?></option><?php
                                    }
                                  }
                                  ?>
                                </select>
                            </div></li>
                        <?php } if ( ! in_array(5, $exclude_items ) ) { ?>
                        <li><div style="margin-left:15px;">
                               <input  <?php if ( isset($_REQUEST[ $wpdevbk_id . 'days_interval_Radios']) ) if ( $_REQUEST[ $wpdevbk_id . 'days_interval_Radios'] == 'prior' ) echo ' checked="CHECKED" ';  ?>
                                    type="radio" value="prior" id="<?php echo $wpdevbk_id; ?>days_interval2" name="<?php echo $wpdevbk_id; ?>days_interval_Radios" style="margin:-2px 5px 0px -5px;">
                                <span><?php _e('Prior', 'wpdev-booking'); ?>: </span>
                                <select class="span1" style="width:85px;" id="<?php echo $wpdevbk_id; ?>prior" name="<?php echo $wpdevbk_id; ?>prior" >
                                  <?php
                                  foreach ($dates_interval as $key=>$value) {
                                    if ($value != 'divider') {
                                        ?><option <?php if ( isset($_REQUEST[ $wpdevbk_id . 'prior']) ) if ( $_REQUEST[ $wpdevbk_id . 'prior'] == '-'.$key ) echo ' selected="SELECTED" '; ?>
                                            value="-<?php echo $key; ?>"><?php echo $value; ?></option><?php
                                    }
                                  }
                                  ?>
                                </select>
                            </div></li>
                        <?php } if ( ! in_array(6, $exclude_items ) ) { ?>
                        <li>    
                            <input  <?php if ( isset($_REQUEST[ $wpdevbk_id . 'days_interval_Radios']) ) if ( $_REQUEST[ $wpdevbk_id . 'days_interval_Radios'] == 'fixed' ) echo ' checked="CHECKED" ';  ?>
                                    type="radio"  value="fixed" id="<?php echo $wpdevbk_id; ?>days_interval3" name="<?php echo $wpdevbk_id; ?>days_interval_Radios" style="margin:0 0 0 10px;">
                            <div style="margin-left:30px;margin-top:-17px;">
                                <div><?php _e('Check in', 'wpdev-booking'); ?>: : </div>
                                <div class="input-append">
                                    <input style="width:100px;" type="text" class="span2<?php echo $wpdevbk_width; ?> wpdevbk-filters-section-calendar"  placeholder="<?php echo '2012-02-25'; ?>"
                                           value="<?php if ( isset($_REQUEST[ $wpdevbk_id . 'fixeddates']) )  echo $_REQUEST[ $wpdevbk_id . 'fixeddates']; ?>"  id="<?php echo $wpdevbk_id; ?>fixeddates"  name="<?php echo $wpdevbk_id; ?>fixeddates" />
                                    <span class="add-on"><?php echo $input_append ?></span>
                                </div>
                                <div style="margin-top: 10px;"><?php _e('Check out', 'wpdev-booking'); ?>: : </div>
                                <div class="input-append">
                                    <input style="width:100px;" type="text" class="span2<?php echo $wpdevbk_width; ?> wpdevbk-filters-section-calendar"  placeholder="<?php echo '2012-02-25'; ?>"
                                           value="<?php if ( isset($_REQUEST[ $wpdevbk_id2 . 'fixeddates']) )  echo $_REQUEST[ $wpdevbk_id2 . 'fixeddates']; ?>"  id="<?php echo $wpdevbk_id2; ?>fixeddates"  name="<?php echo $wpdevbk_id2; ?>fixeddates" />
                                    <span class="add-on"><?php echo $input_append ?></span>
                                </div>
                            </div>
                        </li>
                        <?php }  ?>
                        <li class="divider"></li>
                        <li style="margin: 0;padding: 0 5px;text-align: right;">
                            <div class="btn-toolbar" style="margin:0px;">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary"
                                    onclick="javascript:
                                    var rad_val = jQuery('input:radio[name=<?php echo $wpdevbk_id; ?>days_interval_Radios]:checked').val();
                                    if (rad_val == 'next') wpdevbk_days_selection_in_filter( '<?php echo $wpdevbk_id; ?>', '<?php echo $wpdevbk_id2; ?>', '4' , jQuery('#<?php echo $wpdevbk_id; ?>next').val() );
                                    if (rad_val == 'prior') wpdevbk_days_selection_in_filter( '<?php echo $wpdevbk_id; ?>', '<?php echo $wpdevbk_id2; ?>', '5' , jQuery('#<?php echo $wpdevbk_id; ?>prior').val() );
                                    if (rad_val == 'fixed') wpdevbk_days_selection_in_filter( '<?php echo $wpdevbk_id; ?>', '<?php echo $wpdevbk_id2; ?>', '6' , [ jQuery('#<?php echo $wpdevbk_id; ?>fixeddates').val(), jQuery('#<?php echo $wpdevbk_id2; ?>fixeddates').val()  ]  );
                                "    ><?php _e('Apply', 'wpdev-booking'); ?></button>
                            </div><div class="btn-group">
                                <button type="button" class="btn"
                                    onclick="javascript: jQuery('#<?php echo $wpdevbk_id; ?>_container').hide();"
                                ><?php _e('Close', 'wpdev-booking'); ?></button>
                              </div>
                            </div>
                        </li>
                    </ul>
 
                </div>
              <p class="help-block"><?php echo $wpdevbk_help_block; ?></p>
            </div>
          </div>
        <?php
    }



    function wpdevbk_selection_and_custom_text_for_filter($wpdevbk_id, $wpdevbk_selectors, $wpdevbk_control_label, $wpdevbk_help_block, $wpdevbk_default_value = '') {

            if (isset($_REQUEST[$wpdevbk_id]))    $wpdevbk_value = $_REQUEST[$wpdevbk_id];
            else                                  $wpdevbk_value = $wpdevbk_default_value;
            $wpdevbk_selector_default = array_search($wpdevbk_value, $wpdevbk_selectors);
            if ($wpdevbk_selector_default === false) {
                    $wpdevbk_selector_default = $wpdevbk_value;//key($wpdevbk_selectors);
                    $wpdevbk_selector_default_value = $wpdevbk_value;//current($wpdevbk_selectors);
            } else $wpdevbk_selector_default_value = $wpdevbk_value;
        ?>
          <div class="control-group" style="float:left;">
            <label for="<?php echo $wpdevbk_id; ?>" class="control-label"><?php echo $wpdevbk_control_label; ?></label>
            <div class="inline controls">
                <div class="btn-group">
                  <a onclick="javascript:jQuery('#<?php echo $wpdevbk_id; ?>_container').show();" id="<?php echo $wpdevbk_id;?>_selector" class="btn dropdown-toggle"  href="#" data-toggle="dropdown"  ><?php echo $wpdevbk_selector_default; ?> &nbsp; <span class="caret"></span></a>
                  <ul class="dropdown-menu"  id="<?php echo $wpdevbk_id; ?>_container"  style="display:none;"  >
                      <?php
                      foreach ($wpdevbk_selectors as $key=>$value) {
                        if ($value != 'divider') {
                          ?><li><a href="#" onclick="javascript:jQuery('#<?php echo $wpdevbk_id;?>_selector').html(jQuery(this).html() + ' &nbsp; <span class=&quot;caret&quot;></span>');jQuery('#<?php echo $wpdevbk_id; ?>').val('<?php echo $value; ?>');jQuery('#<?php echo $wpdevbk_id; ?>_container').hide();" ><?php echo $key; ?></a></li><?php
                        } else { ?><li class="divider"></li><?php }
                      } ?>


                        <li class="divider"></li>
                        <li style="margin: 0;padding: 0 5px 0 15px;">
                            <div><?php _e('Custom', 'wpdev-booking'); ?>: </div>
                            <input style="width:150px;" type="text"  placeholder=""
                                       value="<?php $pos = strpos($wpdevbk_value, 'group_'); if (( $pos === false ) && ($wpdevbk_value !== 'all'))  echo $wpdevbk_value; ?>"
                                       id="<?php echo $wpdevbk_id; ?>custom"  name="<?php echo $wpdevbk_id; ?>custom" />
                        </li>

                        <li class="divider"></li>
                        <li style="margin: 0;padding: 0 5px;text-align: right;">
                            <div class="btn-toolbar" style="margin:0px;">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary"
                                    onclick="javascript:
                                    var custom_val = jQuery('#<?php echo $wpdevbk_id; ?>custom').val();
                                    if (custom_val != '') {
                                        jQuery('#<?php echo $wpdevbk_id; ?>').val( custom_val );
                                        jQuery('#<?php echo $wpdevbk_id;?>_selector').html( custom_val + ' &nbsp; <span class=&quot;caret&quot;></span>');
                                    }
                                    jQuery('#<?php echo $wpdevbk_id; ?>_container').hide();
                                "    ><?php _e('Apply', 'wpdev-booking'); ?></button>
                            </div><div class="btn-group">
                                <button type="button" class="btn"
                                    onclick="javascript: jQuery('#<?php echo $wpdevbk_id; ?>_container').hide();"
                                ><?php _e('Close', 'wpdev-booking'); ?></button>
                              </div>
                            </div>
                        </li>

                  </ul>
                  <input type="hidden" value="<?php echo $wpdevbk_selector_default_value; ?>" id="<?php echo $wpdevbk_id; ?>" name="<?php echo $wpdevbk_id; ?>" />
                </div>
              <p class="help-block"><?php echo $wpdevbk_help_block; ?></p>
            </div>
          </div>
        <?php
    }










    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  T O O L B A R       ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Show     T A B s    in      t o o l b a r
    function wpdevbk_booking_listings_tabs_in_top_menu_line() {

        $is_only_icons = ! true;
        if ($is_only_icons) echo '<style type="text/css"> #menu-wpdevplugin .nav-tab { padding:4px 2px 6px 32px !important; } </style>';
        $selected_icon = 'Season-64x64.png';

        if (! isset($_REQUEST['tab'])) $_REQUEST['tab'] = 'filter';
        $selected_title = $_REQUEST['tab'];

        ?>
         <div style="height:1px;clear:both;margin-top:30px;"></div>
         <div id="menu-wpdevplugin">
            <div class="nav-tabs-wrapper">
                <div class="nav-tabs">

                    <?php $title = __('Filter', 'wpdev-booking'); $my_icon = 'Season-64x64.png'; $my_tab = 'filter';  $my_additinal_class= ''; ?>
                    <?php if ($_REQUEST['tab'] == 'filter') {  $slct_a = 'selected'; $selected_title = $title; $selected_icon = $my_icon; } else {  $slct_a = ''; } ?><a class="nav-tab <?php if ($slct_a == 'selected') { echo ' nav-tab-active '; } echo $my_additinal_class; ?>" title="<?php //echo __('Customization of booking form fields','wpdev-booking');  ?>"  href="#" onclick="javascript:jQuery('.visibility_container').hide(); jQuery('#<?php echo $my_tab; ?>').show();jQuery('.nav-tab').removeClass('nav-tab-active');jQuery(this).addClass('nav-tab-active');"><img class="menuicons" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?></a>

                    <?php $title = __('Actions', 'wpdev-booking'); $my_icon = 'actionservices24x24.png'; $my_tab = 'actions';  $my_additinal_class= ''; ?>
                    <?php if ($_REQUEST['tab'] == 'actions') {  $slct_a = 'selected'; $selected_title = $title; $selected_icon = $my_icon; } else {  $slct_a = ''; } ?><a class="nav-tab <?php if ($slct_a == 'selected') { echo ' nav-tab-active '; } echo $my_additinal_class;  ?>" title="<?php //echo __('Customization of booking form fields','wpdev-booking');  ?>"  href="#" onclick="javascript:jQuery('.visibility_container').hide(); jQuery('#<?php echo $my_tab; ?>').show();jQuery('.nav-tab').removeClass('nav-tab-active');jQuery(this).addClass('nav-tab-active');"><img class="menuicons" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>"><?php  if ($is_only_icons) echo '&nbsp;'; else echo $title; ?></a>


                    <?php $title = __('Help', 'wpdev-booking'); $my_icon = 'system-help22x22.png'; $my_tab = 'help'; $my_additinal_class= ' nav-tab-right '; ?>


                    <?php
                    $version = 'free';
                    $version = get_bk_version();
                    if ( wpdev_bk_is_this_demo() ) $version = 'free';
                    if( ( strpos( strtolower(WPDEV_BK_VERSION) , 'multisite') !== false  ) || ($version == 'free' ) )  $multiv = '-multi';
                    else                                                                                               $multiv = '';
                    //$version = 'free';
                    $upgrade_lnk = '';
                    if ( ($version == 'personal') )  $upgrade_lnk = "http://wpbookingcalendar.com/upgrade-p" .$multiv;
                    if ( ($version == 'biz_s') )     $upgrade_lnk = "http://wpbookingcalendar.com/upgrade-s" .$multiv;
                    if ( ($version == 'biz_m') )     $upgrade_lnk = "http://wpbookingcalendar.com/upgrade-m" .$multiv;
                    ?>
                    <span class="dropdown pull-right">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle nav-tab ">
                            <img class="menuicons" src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/<?php echo $my_icon; ?>">
                            <?php echo $title; ?> <b class="caret" style="border-top-color: #333333 !important;"></b></a>
                      <ul class="dropdown-menu" id="menu1" style="right:0px; left:auto;">
                        <li><a href="http://wpbookingcalendar.com/help/" target="_blank"><?php _e('Help', 'wpdev-booking'); ?></a></li>
                        <li><a href="http://wpbookingcalendar.com/faq/" target="_blank"><?php _e('FAQ', 'wpdev-booking'); ?></a></li>
                        <li><a href="http://wpbookingcalendar.com/support/" target="_blank"><?php _e('Technical Support', 'wpdev-booking'); ?></a></li>
                        <?php if ($version == 'free') { ?>
                        <li class="divider"></li>
                        <li><a href="http://wpbookingcalendar.com/buy/" target="_blank"><?php _e('Purchase', 'wpdev-booking'); ?></a></li>
                        <?php } else if ($version != 'biz_l') { ?>
                        <li class="divider"></li>
                        <li><a href="<?php echo $upgrade_lnk; ?>" target="_blank"><?php _e('Upgrade', 'wpdev-booking'); ?></a></li>
                        <?php }  ?>
                      </ul>
                    </span>
                    

                </div>
            </div>
        </div>
        <?php
        
    }


    // Show    T O O L B A R   at top of page
    function wpdevbk_booking_listings_interface_header() {

            wpdevbk_booking_listings_tabs_in_top_menu_line();

            if (! isset($_REQUEST['tab'])) $_REQUEST['tab'] = 'filter';
            $selected_title = $_REQUEST['tab'];

        ?>
            <div class="booking-submenu-tab-container" style="">
                <div class="nav-tabs booking-submenu-tab-insidecontainer">

                    <div class="visibility_container active" id="filter" style="<?php if ($selected_title == 'filter') { echo 'display:block;'; } else { echo 'display:none;'; }  ?>">
                        <?php wpdevbk_show_booking_filters(); ?>

                        <span id="show_link_advanced_booking_filter" class="tab-bottom tooltip_right" data-original-title="<?php _e('Expand Advanced Filter','wpdev-booking'); ?>"  rel="tooltip"><a href="#" onclick="javascript:jQuery('.advanced_booking_filter').show();jQuery('#show_link_advanced_booking_filter').hide();jQuery('#hide_link_advanced_booking_filter').show();"><i class="icon-chevron-down"></i></a></span>
                        <span id="hide_link_advanced_booking_filter" style="display:none;" class="tab-bottom tooltip_right" data-original-title="<?php _e('Collapse Advanced Filter','wpdev-booking'); ?>" rel="tooltip" ><a href="#"  onclick="javascript:jQuery('.advanced_booking_filter').hide(); jQuery('#hide_link_advanced_booking_filter').hide(); jQuery('#show_link_advanced_booking_filter').show();"><i class="icon-chevron-up"></i></a></span>
                    </div>

                    <div class="visibility_container" id="actions"  style="<?php if ($selected_title == 'actions') { echo 'display:block;'; } else { echo 'display:none;'; }  ?>">
                        <?php wpdev_show_booking_actions(); ?>
                    </div>

                    <div class="visibility_container" id="help"     style="<?php if ($selected_title == 'help') { echo 'display:block;'; } else { echo 'display:none;'; }  ?>">
                    </div>

                </div>
            </div>

            <div class="btn-group" style="position:absolute;right:20px;">
                <input style="vertical-align:bottom;height: 27px;margin-bottom: 13px;" type="checkbox" checked="CHECKED" id="is_send_email_for_pending"
                     data-original-title="<?php _e('Send email notification to customer after approvement, unapprovement, deletion of bookings'); ?>"  rel="tooltip" class="tooltip_top"
                       />
                <span style="color: #777777;line-height: 36px;text-shadow: 0 1px 0 #FFFFFF;vertical-align: top;" ><?php _e('Emails sending','wpdev-booking') ?></span>
            </div>


            <div style="height:1px;clear:both;margin-top:40px;"></div>

        <?php
        
    }



    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  Filters interface      ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
    function wpdevbk_show_booking_filters(){
        ?>  <div style="clear:both;height:1px;"></div>
            <div class="wpdevbk-filters-section ">

                    <div style="  float: right; margin-top: -90px;">
                    <form  name="booking_filters_formID" action="" method="post" id="booking_filters_formID" class=" form-search">
                        <?php if (isset($_REQUEST['wh_booking_id']))  $wh_booking_id = $_REQUEST['wh_booking_id'];                  //  {'1', '2', .... }
                              else                                    $wh_booking_id      = '';                    ?>
                        <input class="input-small" type="text" placeholder="<?php _e('Booking ID', 'wpdev-booking'); ?>" name="wh_booking_id" id="wh_booking_id" value="<?php echo $wh_booking_id; ?>" >
                        <button class="btn small" type="submit"><?php _e('Go', 'wpdev-booking'); ?></button>
                    </form>
                    </div>

                    <form  name="booking_filters_form" action="" method="post" id="booking_filters_form"  class="form-inline">
                        <input type="hidden" name="page_num" id ="page_num" value="1" />
                        <a class="btn btn-primary" style="float: left; margin-right: 15px; margin-top: 1px;"
                            onclick="javascript:booking_filters_form.submit();"
                            ><?php _e('Apply', 'wpdev-booking'); ?> <i class="icon-refresh icon-white"></i></a>

                        <?php if (function_exists('wpdebk_filter_field_bk_resources')) {
                                  wpdebk_filter_field_bk_resources();
                        } ?>
<?php /** ?>
          <?php

          $wpdevbk_id= '';
          $wpdevbk_selectors='';
          $wpdevbk_control_label='';
          $wpdevbk_help_block='Booking Status';
          $wpdevbk_default_value = '';
          $wpdevbk_selector_default_value='';
          ?>
          <div class="control-group" style="float:left;">
            <label for="<?php echo $wpdevbk_id; ?>" class="control-label"><?php echo $wpdevbk_control_label; ?></label>
            <div class="inline controls">
                <!--div class="btn-group" data-toggle="buttons-radio" id="radiobutton_<?php echo $wpdevbk_id; ?>"-->
                <div class="btn-group" data-toggle="buttons-checkbox" id="radiobutton_<?php echo $wpdevbk_id; ?>">
                    <a  href="#" class="btn">Approved</a>
                    <a  href="#" class="btn">Pending</a>
                </div>
                <input type="hidden" value="<?php echo $wpdevbk_selector_default_value; ?>" id="<?php echo $wpdevbk_id; ?>" name="<?php echo $wpdevbk_id; ?>" />
              <p class="help-block"><?php echo $wpdevbk_help_block; ?></p>
            </div>
          </div>
            <script type="text/javascript">
                jQuery('#radiobutton_<?php echo $wpdevbk_id; ?> .btn').button();
                <?php if (1) {  // Press the button ?>
                    jQuery('#radiobutton_<?php echo $wpdevbk_id; ?> .btn:first').button('toggle');
                <?php } ?>
            </script>
<?php /**/ ?>
                        <?php // Approved / Pending
                        $wpdevbk_id =              'wh_approved';                           //  {'', '0', '1' }
                        $wpdevbk_selectors = array(__('Pending', 'wpdev-booking')   =>'0',
                                                   __('Approved', 'wpdev-booking')  =>'1',
                                                   'divider0'=>'divider',
                                                   __('All', 'wpdev-booking')       =>'');
                        $wpdevbk_control_label =   '';
                        $wpdevbk_help_block =      __('Booking Status', 'wpdev-booking');
                        // Pending, Active, Suspended, Terminated, Cancelled, Fraud
                        wpdevbk_selectbox_filter($wpdevbk_id, $wpdevbk_selectors, $wpdevbk_control_label, $wpdevbk_help_block);
                        ?>


                        <?php  // Booking Dates
                        $wpdevbk_id =              'wh_booking_date';
                        $wpdevbk_id2 =             'wh_booking_date2';
                        $wpdevbk_control_label =   '';
                        $wpdevbk_help_block =      __('Booking dates', 'wpdev-booking');
                        $wpdevbk_width =           'span2 wpdevbk-filters-section-calendar';
                        $wpdevbk_icon =            '<i class="icon-calendar"></i>' ;
                        wpdevbk_dates_selection_for_filter($wpdevbk_id, $wpdevbk_id2, $wpdevbk_control_label,  $wpdevbk_help_block,  $wpdevbk_width, $wpdevbk_icon );
                        ?>

                        <span style="display:none;" class="advanced_booking_filter">

                        <?php  // Read / Unread
                        $wpdevbk_id =              'wh_is_new';                           //  {'',  '1' }
                        $wpdevbk_selectors =        array('','1');
                        $wpdevbk_control_label =   __('Unread', 'wpdev-booking');
                        $wpdevbk_help_block =      __('Only New', 'wpdev-booking');

                        wpdevbk_checkboxbutton_filter($wpdevbk_id, $wpdevbk_selectors, $wpdevbk_control_label, $wpdevbk_help_block);
                        ?>

                        
                        <?php  // Creation Dates
                        $wpdevbk_id =              'wh_modification_date';
                        $wpdevbk_id2 =             'wh_modification_date2';
                        $wpdevbk_control_label =   '';
                        $wpdevbk_help_block =      __('Creation date(s)', 'wpdev-booking');
                        $wpdevbk_width =           'span2 wpdevbk-filters-section-calendar';
                        $wpdevbk_icon =            '<i class="icon-calendar"></i>' ;
                        $exclude_items = array(0, 2, 4);
                        $default_item = 3 ;
                        wpdevbk_dates_selection_for_filter($wpdevbk_id, $wpdevbk_id2, $wpdevbk_control_label,  $wpdevbk_help_block,  $wpdevbk_width, $wpdevbk_icon, $exclude_items, $default_item );
                        ?>

                        <?php if (function_exists('wpdebk_filter_field_bk_keyword')) {
                                  wpdebk_filter_field_bk_keyword();
                        } ?>

                        <?php if (function_exists('wpdebk_filter_field_bk_paystatus')) {
                                  wpdebk_filter_field_bk_paystatus();
                        } ?>

                        <?php if (function_exists('wpdebk_filter_field_bk_costs')) {
                                  wpdebk_filter_field_bk_costs();
                        } ?>

                        </span>

                        <?php // Sort
                        $wpdevbk_id =              'or_sort';                           //  {'', '0', '1' }
                        $wpdevbk_selectors = array(__('ID', 'wpdev-booking').'&nbsp;<i class="icon-arrow-up "></i>' =>'',
                                                   'divider0'=>'divider',
                                                   __('ID', 'wpdev-booking').'&nbsp;<i class="icon-arrow-down "></i>' =>'booking_id_asc'
                                                  );

                        $wpdevbk_selectors = apply_bk_filter('bk_filter_sort_options', $wpdevbk_selectors);
       
                        $wpdevbk_control_label =   '';
                        $wpdevbk_help_block =      __('Sort', 'wpdev-booking');
                        
                        $wpdevbk_default_value = get_bk_option( 'booking_sort_order');
                        wpdevbk_selectbox_filter($wpdevbk_id, $wpdevbk_selectors, $wpdevbk_control_label, $wpdevbk_help_block, $wpdevbk_default_value);
                        ?>


                        <span style="display:none;" class="advanced_booking_filter">

                            <div class="clear"></div>
                            <a data-original-title="<?php _e('Save filter settings as default template (Please, click Apply filter button, before saving!)','wpdev-booking'); ?>"  rel="tooltip"
                               class="tooltip_top btn" style="margin-bottom:10px;"
                                onclick="javascript:save_bk_listing_filter( '<?php echo get_bk_current_user_id(); ?>',  'default' , '<?php echo get_params_in_url( array('page_num') ); ?>' );"
                                ><?php _e('Save as Default', 'wpdev-booking'); ?> <i class="icon-upload"></i></a>

                        </span>


                      <div class="clear"></div>
                    </form>

                    

            <!--div id="tooltipsinit" class="tooltip-demo well">
                <p style="margin-bottom: 0;" class="muted">Tight pants next level keffiyeh
                    <a rel="tooltip" href="#" data-original-title="first tooltip">you probably</a>

                    haven't heard of them. Photo booth beard raw denim letterpress vegan messenger bag stumptown. Farm-to-table seitan, mcsweeney's fixie sustainable quinoa 8-bit american apparel

                    <a rel="tooltip" href="#" data-original-title="Another tooltip">have a</a>

                    terry richardson vinyl chambray. Beard stumptown, cardigans banh mi lomo thundercats. Tofu biodiesel williamsburg marfa, four loko mcsweeney's cleanse vegan chambray. A

                    <a title="Another one here too" rel="tooltip" href="#">really ironic</a>

                    artisan whatever keytar, scenester farm-to-table banksy Austin

                    <a rel="tooltip" href="#" data-original-title="The last tip!">twitter handle</a>

                    freegan cred raw denim single-origin coffee viral.
                </p>
            </div>

            <script type="text/javascript">
                jQuery('#tooltipsinit a').tooltip( {
                    animation: true
                  , delay: { show: 500, hide: 100 }
                  , selector: false
                  , placement: 'top'
                  , trigger: 'hover'
                  , title: ''
                  , template: '<div class="wpdevbk tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
                });
            </script>


            <div id="popover" class="well">
                <a data-content="And here's some amazing content. It's very engaging. right?" rel="popover" class="btn btn-danger" href="#" data-original-title="A Title">hover for popover</a>
            </div>


            <script type="text/javascript">
                jQuery('#popover a').popover( {
                    placement: 'bottom'
                  , delay: { show: 100, hide: 100 }
                  , content: ''
                  , template: '<div class="wpdevbk popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
                });
            </script-->
            </div>
            <div style="clear:both;height:1px;"></div>
        <?php
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  Actions interface      ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function wpdev_show_booking_actions(){
            $user = wp_get_current_user(); $user_bk_id = $user->ID;
        ?>
            <div class="btn-toolbar" style="margin:0px;">
                <div class="btn-group" style="margin-top: 2px; vertical-align: top;">
                    <a     data-original-title="<?php _e('Approve selected bookings'); ?>"  rel="tooltip" class="tooltip_top btn btn-primary"
                           onclick="javascript: 
                                                approve_unapprove_booking( get_selected_bookings_id_in_booking_listing() ,
                                                      1, <?php echo $user_bk_id; ?>, '<?php echo getBookingLocale(); ?>' , 1);
                           " /><?php _e('Approve', 'wpdev-booking'); ?> <i class="icon-ok icon-white"></i></a>
                    <a     data-original-title="<?php _e('Set selected bookings as pening'); ?>"  rel="tooltip" class="tooltip_top btn"
                           onclick="javascript: 
                                        if ( bk_are_you_sure('<?php echo esc_js(__('Are you really want to set booking as pending ?', 'wpdev-booking')); ?>') )
                                                approve_unapprove_booking( get_selected_bookings_id_in_booking_listing() ,
                                                      0, <?php echo $user_bk_id; ?>, '<?php echo getBookingLocale(); ?>' , 1);
                           " /><?php _e('Unapprove', 'wpdev-booking'); ?> <i class="icon-ban-circle"></i></a>
                </div>
                <div class="btn-group" style="margin-top: 2px; vertical-align: top;">
                    <a  data-original-title="<?php _e('Delete selected bookings'); ?>"  rel="tooltip" class="tooltip_top btn btn-danger"
                        onclick="javascript: 
                                if ( bk_are_you_sure('<?php echo esc_js(__('Are you really want to delete selected booking(s) ?', 'wpdev-booking')); ?>') )
                                    delete_booking( get_selected_bookings_id_in_booking_listing() ,
                                                    <?php echo $user_bk_id; ?>, '<?php echo getBookingLocale(); ?>' , 1  );
                                " >
                        <?php _e('Delete', 'wpdev-booking'); ?> <i class="icon-trash icon-white"></i></a>
                    <input  style="border-bottom-left-radius: 0; border-top-left-radius: 0; height: 28px; "
                            type="text" placeholder="<?php echo __('Reason of cancellation here', 'wpdev-booking'); ?>"
                            class="span3" value="" id="denyreason" name="denyreason" />
                </div>

                <div class="btn-group" style="margin-top: 2px; vertical-align: top;">
                    <a     data-original-title="<?php _e('Mark as read selected bookings'); ?>"  rel="tooltip" class="tooltip_top btn btn"
                           onclick="javascript:
                                                mark_read_booking( get_selected_bookings_id_in_booking_listing() ,
                                                      0, <?php echo $user_bk_id; ?>, '<?php echo getBookingLocale(); ?>' );
                           " /><?php _e('Read', 'wpdev-booking'); ?> <i class="icon-eye-close"></i></a>
                    <a     data-original-title="<?php _e('Mark as Unread selected bookings'); ?>"  rel="tooltip" class="tooltip_top btn"
                           onclick="javascript:                                       
                                                mark_read_booking( get_selected_bookings_id_in_booking_listing() ,
                                                      1, <?php echo $user_bk_id; ?>, '<?php echo getBookingLocale(); ?>' );
                           " /><?php _e('Unread', 'wpdev-booking'); ?> <i class="icon-eye-open"></i></a>
                </div>


                <?php if (function_exists('wpdebk_action_field_export_print')) {
                          wpdebk_action_field_export_print();
                } ?>

          </div>
          <div class="clear" style="height:1px;"></div>
          <div id="admin_bk_messages" style="margin:0px;"> </div>
          <div class="clear" style="height:1px;"></div>
        <?php
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  SQL for the dates filtering      ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    // SQL - WHERE -  D a t e s  (BK)
    function set_dates_filter_for_sql($wh_booking_date, $wh_booking_date2, $pref = 'dt.') {
            $sql_where= '';
            if ($pref == 'dt.')  { $and_pre = ' AND '; $and_suf = ''; }
            else                 { $and_pre = ''; $and_suf = ' AND '; }

                                                                                // Actual
            if (  ( ( $wh_booking_date  === '' ) && ( $wh_booking_date2  === '' ) ) || ($wh_booking_date  === '0') ) {
                $sql_where =               $and_pre."( ".$pref."booking_date >= ( CURDATE() - INTERVAL 1 DAY ) ) ".$and_suf ;

            } else  if ($wh_booking_date  === '1') {                            // Today
                $sql_where  =               $and_pre."( ".$pref."booking_date <= ( CURDATE() + INTERVAL 1 DAY ) ) ".$and_suf ;
                $sql_where .=               $and_pre."( ".$pref."booking_date >= ( CURDATE() - INTERVAL 1 DAY ) ) ".$and_suf ;


            } else if ($wh_booking_date  === '2') {                             // Previous
                $sql_where =               $and_pre."( ".$pref."booking_date <= ( CURDATE() + INTERVAL 1 DAY ) ) ".$and_suf ;

            } else if ($wh_booking_date  === '3') {                             // All
                $sql_where =  '';

            } else if ($wh_booking_date  === '4') {                             // Next
                $sql_where  =               $and_pre."( ".$pref."booking_date <= ( CURDATE() + INTERVAL ". $wh_booking_date2 . " DAY ) ) ".$and_suf ;
                $sql_where .=               $and_pre."( ".$pref."booking_date >= ( CURDATE() - INTERVAL 1 DAY ) ) ".$and_suf ;

            } else if ($wh_booking_date  === '5') {                             // Prior
                $wh_booking_date2 = str_replace('-', '', $wh_booking_date2);
                $sql_where  =               $and_pre."( ".$pref."booking_date >= ( CURDATE() - INTERVAL ". $wh_booking_date2 . " DAY ) ) ".$and_suf ;
                $sql_where .=               $and_pre."( ".$pref."booking_date <= ( CURDATE() + INTERVAL 1 DAY ) ) ".$and_suf ;

            } else {                                                            // Fixed

                if ( $wh_booking_date  !== '' )
                    $sql_where.=               $and_pre."( ".$pref."booking_date >= '" . $wh_booking_date . "' ) ".$and_suf;

                if ( $wh_booking_date2  !== '' )
                    $sql_where.=               $and_pre."( ".$pref."booking_date <= '" . $wh_booking_date2 . "' ) ".$and_suf;
            }
            return $sql_where;
    }

    // SQL - WHERE -  D a t e s  (Modification)
    function set_creation_dates_filter_for_sql($wh_modification_date, $wh_modification_date2, $pref = 'bk.') {
            $sql_where= '';
            if ($pref == 'bk.')  { $and_pre = ' AND '; $and_suf = ''; }
            else                 { $and_pre = ''; $and_suf = ' AND '; }

            if ($wh_modification_date  === '1') {                               // Today
                $sql_where  =               $and_pre."( ".$pref."modification_date <= ( CURDATE() + INTERVAL 1 DAY ) ) ".$and_suf ;
                $sql_where .=               $and_pre."( ".$pref."modification_date >= ( CURDATE() - INTERVAL 1 DAY ) ) ".$and_suf ;

            } else if ($wh_modification_date  === '3') {                        // All
                $sql_where =  '';

            } else if ($wh_modification_date  === '5') {                        // Prior
                $wh_modification_date2 = str_replace('-', '', $wh_modification_date2);
                $sql_where  =               $and_pre."( ".$pref."modification_date >= ( CURDATE() - INTERVAL ". $wh_modification_date2 . " DAY ) ) ".$and_suf ;
                $sql_where .=               $and_pre."( ".$pref."modification_date <= ( CURDATE() + INTERVAL 1 DAY ) ) ".$and_suf ;

            } else {                                                            // Fixed

                if ( $wh_modification_date  !== '' )
                    $sql_where.=               $and_pre."( ".$pref."modification_date >= '" . $wh_modification_date . "' ) ".$and_suf;

                if ( $wh_modification_date2  !== '' )
                    $sql_where.=               $and_pre."( ".$pref."modification_date <= '" . $wh_modification_date2 . "' ) ".$and_suf;
            }
            return $sql_where;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  Bookings listing    E N G I N E        ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Get Default params or from Request
    function wpdev_get_args_from_request_in_bk_listing(){

        $args = array(
		'wh_booking_type' =>    (isset($_REQUEST['wh_booking_type']))?$_REQUEST['wh_booking_type']:'',
                'wh_approved' =>        (isset($_REQUEST['wh_approved']))?$_REQUEST['wh_approved']:'',
		'wh_booking_id' =>      (isset($_REQUEST['wh_booking_id']))?$_REQUEST['wh_booking_id']:'',
                'wh_is_new' =>          (isset($_REQUEST['wh_is_new']))?$_REQUEST['wh_is_new']:'',
		'wh_pay_status' =>      (isset($_REQUEST['wh_pay_status']))?$_REQUEST['wh_pay_status']:'',
                'wh_keyword' =>         (isset($_REQUEST['wh_keyword']))?$_REQUEST['wh_keyword']:'',
		'wh_booking_date' =>    (isset($_REQUEST['wh_booking_date']))?$_REQUEST['wh_booking_date']:'',
                'wh_booking_date2' =>   (isset($_REQUEST['wh_booking_date2']))?$_REQUEST['wh_booking_date2']:'',
		'wh_modification_date' =>  (isset($_REQUEST['wh_modification_date']))?$_REQUEST['wh_modification_date']:'',
                'wh_modification_date2' => (isset($_REQUEST['wh_modification_date2']))?$_REQUEST['wh_modification_date2']:'',
		'wh_cost' =>            (isset($_REQUEST['wh_cost']))?$_REQUEST['wh_cost']:'',
                'wh_cost2' =>           (isset($_REQUEST['wh_cost2']))?$_REQUEST['wh_cost2']:'',
		'or_sort' =>            (isset($_REQUEST['or_sort']))?$_REQUEST['or_sort']:get_bk_option( 'booking_sort_order'),
		'page_num' =>           (isset($_REQUEST['page_num']))?$_REQUEST['page_num']:'1',
                'page_items_count' =>   (isset($_REQUEST['page_items_count']))?$_REQUEST['page_items_count']:get_bk_option( 'bookings_num_per_page'),
	);

        return $args;
    }


    // S Q L    B o o k i n g    L i s t i n g
    function wpdev_sql_get_booking_lising( $args ){
	global $wpdb;

        ////////////////////////////////////////////////////////////////////////
        // CONSTANTS
        ////////////////////////////////////////////////////////////////////////
	$defaults = array(
		'wh_booking_type' => '',    'wh_approved' => '',
		'wh_booking_id' => '',      'wh_is_new' => '',
		'wh_pay_status' => '',      'wh_keyword' => '',
		'wh_booking_date' => '',        'wh_booking_date2' => '',
		'wh_modification_date' => '',   'wh_modification_date2' => '',
		'wh_cost' => '',            'wh_cost2' => '',
		'or_sort' => get_bk_option( 'booking_sort_order'),
		'page_num' => '1',
                'page_items_count' => get_bk_option( 'bookings_num_per_page')
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

        $page_start = ( $page_num - 1 ) * $page_items_count ;

        if ($or_sort == '') $or_sort = 'booking_id';

        ////////////////////////////////////////////////////////////////////////
        // S Q L
        ////////////////////////////////////////////////////////////////////////
        // GET ONLY ROWS OF THE     B o o k i n g s    - So we can limit the requests
        $sql_start_select = " SELECT * " ;
        $sql_start_count  = " SELECT COUNT(*) as count" ;
        $sql = " FROM ".$wpdb->prefix ."booking as bk" ;
        $sql_where = " WHERE " .                                                      // Date (single) connection (Its required for the correct Pages in SQL: LIMIT Keyword)
               "       EXISTS (
                                SELECT *
                                FROM ".$wpdb->prefix ."bookingdates as dt
                                WHERE  bk.booking_id = dt.booking_id " ;
                if ($wh_approved !== '')
                    $sql_where.=           " AND approved = $wh_approved  " ;            // Approved or Pending

            $sql_where.= set_dates_filter_for_sql($wh_booking_date, $wh_booking_date2) ;

            $sql_where.=   "   ) " ;

        if ( $wh_is_new !== '' )    $sql_where .= " AND  bk.is_new = " . $wh_is_new . " ";

            // P
            $sql_where .= apply_bk_filter('get_bklist_sql_keyword', ''  , $wh_keyword );

        $sql_where.= set_creation_dates_filter_for_sql($wh_modification_date, $wh_modification_date2 ) ;

            // BS
            $sql_where .= apply_bk_filter('get_bklist_sql_paystatus', ''  , $wh_pay_status );
            $sql_where .= apply_bk_filter('get_bklist_sql_cost', ''  , $wh_cost, $wh_cost2 );

            // P  || BL
            $sql_where .= apply_bk_filter('get_bklist_sql_resources', ''  , $wh_booking_type, $wh_approved, $wh_booking_date, $wh_booking_date2 );

        if (! empty ($wh_booking_id) ) $sql_where = " WHERE bk.booking_id = " . $wh_booking_id . " ";

        if (strpos($or_sort, '_asc') !== false) {                               // Order
               $or_sort = str_replace('_asc', '', $or_sort);
               $sql_order = " ORDER BY " .$or_sort ." ASC ";                                          
        } else $sql_order = " ORDER BY " .$or_sort ." DESC ";                                          // Order

        $sql_limit = " LIMIT $page_start, $page_items_count ";                        // Page s
        
        return array( $sql_start_count, $sql_start_select , $sql , $sql_where , $sql_order , $sql_limit );        
    }



    // E n g i n e     B o o k i n g    L i s t i n g
    function wpdev_get_bk_listing_structure_engine( $args ){
        global $wpdb;
        
        $sql_boking_listing = wpdev_sql_get_booking_lising( $args );

        $sql_start_count    = $sql_boking_listing[0];
        $sql_start_select   = $sql_boking_listing[1];
        $sql       = $sql_boking_listing[2];
        $sql_where = $sql_boking_listing[3];
        $sql_order = $sql_boking_listing[4];
        $sql_limit = $sql_boking_listing[5];


	$defaults = array(
		'wh_booking_type' => '',    'wh_approved' => '',
		'wh_booking_id' => '',      'wh_is_new' => '',
		'wh_pay_status' => '',      'wh_keyword' => '',
		'wh_booking_date' => '',        'wh_booking_date2' => '',
		'wh_modification_date' => '',   'wh_modification_date2' => '',
		'wh_cost' => '',            'wh_cost2' => '',
		'or_sort' => get_bk_option( 'booking_sort_order'),
		'page_num' => '1',
                'page_items_count' => get_bk_option( 'bookings_num_per_page')
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

        $page_start = ( $page_num - 1 ) * $page_items_count ;

        // Get Bookings Array
        $bookings_res = $wpdb->get_results($wpdb->prepare( $sql_start_select . $sql . $sql_where . $sql_order . $sql_limit ));

        // Get Number of booking for the pages
        $bookings_count = $wpdb->get_results($wpdb->prepare( $sql_start_count . $sql . $sql_where   ));

        // Get NUMBER of Bookings
        if (count($bookings_count)>0)   $bookings_count = $bookings_count[0]->count ;
        else                            $bookings_count = 0;



        // Bookings array init                 - Get the ID list of ALL bookings
        $booking_id_list = array();

        $bookings = array();
        $short_days = array();
        $short_days_type_id = array();
        if ( count($bookings_res)>0 )
        foreach ($bookings_res as $booking ) {
            if ( ! in_array($booking->booking_id, $booking_id_list) ) $booking_id_list[] = $booking->booking_id;

            $bookings[$booking->booking_id] = $booking;
            $bookings[$booking->booking_id]->dates=array();
            $bookings[$booking->booking_id]->dates_short=array();

            $bk_list_type = (isset($booking->booking_type))?$booking->booking_type:'1';
            $cont = get_form_content($booking->form, $bk_list_type);
            $search = array ("'(<br[ ]?[/]?>)+'si","'(<p[ ]?[/]?>)+'si","'(<div[ ]?[/]?>)+'si");
            $replace = array ("&nbsp;&nbsp;"," &nbsp; "," &nbsp; ");
            $cont['content'] = preg_replace($search, $replace, $cont['content']);
            $bookings[$booking->booking_id]->form_show = $cont['content'];
            unset($cont['content']);
            $bookings[$booking->booking_id]->form_data = $cont;
        }
        $booking_id_list = implode(",",$booking_id_list);

        if (! empty($booking_id_list)) {
            // Get Dates  for all our Bookings
            $sql = " SELECT *
            FROM ".$wpdb->prefix ."bookingdates as dt
            WHERE dt.booking_id in ( " .$booking_id_list . ") ";

            if (class_exists('wpdev_bk_biz_l'))
                $sql .= " ORDER BY booking_id, type_id, booking_date   ";
            else
                $sql .= " ORDER BY booking_id, booking_date   ";

            $booking_dates = $wpdb->get_results($wpdb->prepare( $sql ));
        } else
            $booking_dates = array();


        $last_booking_id = '';
        // Add Dates to Bookings array
        foreach ($booking_dates as $date) {
            $bookings[$date->booking_id]->dates[] = $date;

                if ($date->booking_id != $last_booking_id) {
                    if (! empty($last_booking_id)) {
                        if($last_show_day != $dte) { $short_days[]= $dte; $short_days_type_id[] = $last_day_id;}

                        $bookings[ $last_booking_id ]->dates_short = $short_days;
                        $bookings[ $last_booking_id ]->dates_short_id = $short_days_type_id;
                    }
                    $last_day = '';
                    $last_day_id = '';
                    $last_show_day = '';
                    $short_days = array();
                    $short_days_type_id = array();
                }

                $last_booking_id = $date->booking_id;
                $dte = $date->booking_date;

                if (empty($last_day)) { // First date
                    $short_days[]= $dte; $short_days_type_id[] = (isset($date->type_id))?$date->type_id:'';
                    $last_show_day = $dte;
                } else {                // All other days
                    if ( wpdevbk_is_next_day( $dte ,$last_day) ) {
                        if ($last_show_day != '-') { $short_days[]= '-'; $short_days_type_id[] = ''; }
                        $last_show_day = '-';
                    } else {
                        if ($last_show_day !=$last_day) { $short_days[]= $last_day; $short_days_type_id[] = $last_day_id; }
                        $short_days[]= ','; $short_days_type_id[] = '';
                        $short_days[]= $dte; $short_days_type_id[] = (isset($date->type_id))?$date->type_id:'';
                        $last_show_day = $dte;
                    }
                }
                $last_day = $dte;
                $last_day_id = (isset($date->type_id))?$date->type_id:'';
        }

        if (isset($dte))
            if($last_show_day != $dte) { $short_days[]= $dte; $short_days_type_id[] = (isset($date->type_id))?$date->type_id:'';}
        if (isset($bookings[ $last_booking_id ]) )  {
            $bookings[ $last_booking_id ]->dates_short = $short_days;
            $bookings[ $last_booking_id ]->dates_short_id = $short_days_type_id;
        }

        $booking_types = apply_bk_filter('wpdebk_get_keyed_all_bk_resources', array() );
        

        return array($bookings , $booking_types, $bookings_count, $page_num,  $page_items_count);
    }


    // B o o k i n g    L i s t i n g    P A G E
    function wpdevbk_show_booking_listings() {

        wpdevbk_get_default_bk_listing_filter_set_to_params('default');         // Get saved filters set

        wpdevbk_booking_listings_interface_header();                            // Show Filters and Action tabs

        // If the booking resources is not set, and current user  is not superadmin, so then get only the booking resources of the current user
        make_bk_action('check_for_resources_of_notsuperadmin_in_booking_listing' );

	$args = wpdev_get_args_from_request_in_bk_listing();                    // Get safy PARAMS from REQUEST
        ?><textarea id="bk_request_params" style="display:none;"><?php echo  serialize($args) ; ?></textarea><?php

        $bk_listing = wpdev_get_bk_listing_structure_engine( $args );           // Get Bookings structure
        $bookings       = $bk_listing[0];
        $booking_types  = $bk_listing[1];
        $bookings_count = $bk_listing[2];
        $page_num       = $bk_listing[3];
        $page_items_count= $bk_listing[4];
        
        booking_listing_table($bookings , $booking_types);                      // Show the bookings listing table

        wpdevbk_show_pagination($bookings_count, $page_num, $page_items_count); // Show Pagination
        
        wpdevbk_booking_listing_write_js();                                     // Wtite inline  JS
        wpdevbk_booking_listing_write_css();                                    // Write inline  CSS
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  Bookings listing    T A B L E      ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //   S H O W      B o o k i n g    L i s t i n g    T a b l e
    function booking_listing_table($bookings , $booking_types) {

        $user = wp_get_current_user(); $user_bk_id = $user->ID;
        
        $bk_url_listing     = 'admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME . 'wpdev-booking' ;
        $bk_url_add         = $bk_url_listing . '-reservation' ;
        $bk_url_resources   = $bk_url_listing . '-resources' ;
        $bk_url_settings    = $bk_url_listing . '-option' ;

        $booking_date_view_type = get_bk_option( 'booking_date_view_type');
        if ($booking_date_view_type == 'short') { $wide_days_class = ' hidden_items '; $short_days_class = ''; }
        else {                                    $wide_days_class = ''; $short_days_class = ' hidden_items '; }

        ?>
         <div id="listing_visible_bookings">
          <?php if (count($bookings)>0) { ?>
          <div class="row-fluid booking-listing-header">
              <div class="booking-listing-collumn span1">
                  <input type="checkbox" onclick="javascript:setCheckBoxInTable(this.checked, 'booking_list_item_checkbox');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <?php _e('ID', 'wpdev-booking'); ?>
              </div>
              <div class="booking-listing-collumn span2"><?php _e('Labels', 'wpdev-booking'); ?></div>
              <div class="booking-listing-collumn span4"><?php _e('Booking Data', 'wpdev-booking'); ?></div>
              <div class="booking-listing-collumn span3"><?php _e('Booking Dates', 'wpdev-booking'); ?>&nbsp;&nbsp;&nbsp;
                  <a href="javascript:;" id="booking_dates_full" onclick="javascript:
                            jQuery('#booking_dates_full').hide();
                            jQuery('#booking_dates_small').show();
                            jQuery('.booking_dates_small').hide();
                            jQuery('.booking_dates_full').show();" data-original-title="<?php _e('Show ALL dates of booking','wpdev-booking'); ?>"  rel="tooltip" class="tooltip_top <?php echo $short_days_class; ?> "><i class="icon-resize-full"></i></a>
                  <a href="javascript:;" id="booking_dates_small" onclick="javascript:
                            jQuery('#booking_dates_small').hide();
                            jQuery('#booking_dates_full').show();
                            jQuery('.booking_dates_small').show();
                            jQuery('.booking_dates_full').hide();" data-original-title="<?php _e('Show only check in/out dates','wpdev-booking'); ?>"  rel="tooltip" class="tooltip_top <?php echo $wide_days_class; ?> " ><i class="icon-resize-small"></i></a>
              </div>
              <div class="booking-listing-collumn span2"><?php _e('Actions', 'wpdev-booking'); ?></div>
          </div>
          <?php } else {
                        echo '<center><h3>'.__('Nothing found!', 'wpdev-booking') .'</h3></center>';
                } ?>
        <?php

        // P
        $print_data = apply_bk_filter('get_bklist_print_header', array(array())  );

        $is_alternative_color = true;
        $id_of_new_bookings = array();

        foreach ($bookings as $bk) {
            $is_selected_color = 0;//rand(0,1);
            $is_alternative_color = ! $is_alternative_color;

            $booking_id             = $bk->booking_id;          // 100
            $is_new                 = (isset($bk->is_new))?$bk->is_new:'0';                           // 1
            $bk_modification_date   = (isset($bk->modification_date))?$bk->modification_date:'';    // 2012-02-29 16:01:58
            $bk_form                = $bk->form;                // select-one^rangetime5^10:00 - 12:00~text^name5^Jonny~text^secondname5^Smith~email^ ....
            $bk_form_show           = $bk->form_show;           // First Name:Jonny   Last Name:Smith   Email:email@server.com  Country:GB  ....
            $bk_form_data           = $bk->form_data;           // Array ([name] => Jonny... [_all_] => Array ( [rangetime5] => 10:00 - 12:00 [name5] => Jonny ... ) .... )
            $bk_dates               = $bk->dates;               // Array ( [0] => stdClass Object ( [booking_id] => 8 [booking_date] => 2012-04-16 10:00:01 [approved] => 0 [type_id] => )
            $bk_dates_short         = $bk->dates_short;         // Array ( [0] => 2012-04-16 10:00:01 [1] => - [2] => 2012-04-20 12:00:02 [3] => , [4] => 2012-04-16 10:00:01 ....

            //P
            $bk_booking_type        = (isset($bk->booking_type))?$bk->booking_type:'1';        // 3
            if (!class_exists('wpdev_bk_personal')) {
                $bk_booking_type_name = '<span class="label_resource_not_exist">'.__('Default', 'wpdev-booking').'</span>';
            } else if (isset($booking_types[$bk_booking_type]))   {
                $bk_booking_type_name   = $booking_types[$bk_booking_type]->title;        // Default
                if (strlen($bk_booking_type_name)>19) $bk_booking_type_name = substr($bk_booking_type_name, 0,  13) . ' ... ' . substr($bk_booking_type_name, -3 );
            } else  {
                $bk_booking_type_name = '<span class="label_resource_not_exist">'.__('Resource not exist', 'wpdev-booking').'</span>';
            }

            $bk_hash                = (isset($bk->hash))?$bk->hash:'';                // 99c9c2bd4fd0207e4376bdbf5ee473bc
            $bk_remark              = (isset($bk->remark))?$bk->remark:'';            //
            //BS
            $bk_cost                = (isset($bk->cost))?$bk->cost:'';                // 150.00
            $bk_pay_status          = (isset($bk->pay_status))?$bk->pay_status:'';    // 30800
            $bk_pay_request         = (isset($bk->pay_request))?$bk->pay_request:'';  // 0
            $bk_status              = (isset($bk->status))?$bk->status:'';
            //BL
            $bk_dates_short_id = array(); if (count($bk->dates) > 0 ) $bk_dates_short_id      = (isset($bk->dates_short_id))?$bk->dates_short_id:array();      // Array ([0] => [1] => .... [4] => 6... [11] => [12] => 8 )

            $is_approved = 0;   if (count($bk->dates) > 0 )     $is_approved = $bk->dates[0]->approved ;
            //BS
            $is_paid = 0;
            $payment_status_titles_current = '';
            if (class_exists('wpdev_bk_biz_s')) {

                if ( is_payment_status_ok( trim($bk_pay_status) ) ) $is_paid = 1 ;
                
                $payment_status_titles = get_payment_status_titles();
                $payment_status_titles_current = array_search($bk_pay_status, $payment_status_titles);
                if ($payment_status_titles_current === FALSE ) $payment_status_titles_current = $bk_pay_status ;
            }

            if ( $is_new == 1) $id_of_new_bookings[] = $booking_id;


            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // Get SHORT Dates showing data ////////////////////////////////////////////////////////////////////////////////////////////////////
            $short_dates_content = '';
            $dcnt = 0;
            foreach ($bk_dates_short as $dt) {
                if ($dt == '-') {       $short_dates_content .= '<span class="date_tire"> - </span>';
                } elseif ($dt == ',') { $short_dates_content .= '<span class="date_tire">, </span>';
                } else {
                    $short_dates_content .= '<a href="javascript:;" class="field-booking-date ';
                    if ($is_approved) $short_dates_content .= ' approved';
                    $short_dates_content .= '">';

                    $bk_date = wpdevbk_get_date_in_correct_format($dt);
                    $short_dates_content .= $bk_date[0];
                    $short_dates_content .= '<sup class="field-booking-time">'. $bk_date[1] .'</sup>';

                     // BL
                     if (class_exists('wpdev_bk_biz_l')) {
                         if (! empty($bk_dates_short_id[$dcnt]) ) {
                             $bk_booking_type_name_date   = $booking_types[$bk_dates_short_id[$dcnt]]->title;        // Default
                             if (strlen($bk_booking_type_name_date)>19) $bk_booking_type_name_date = substr($bk_booking_type_name_date, 0,  13) . '...' . substr($bk_booking_type_name_date, -3 );

                             $short_dates_content .= '<sup class="field-booking-time date_from_dif_type"> '.$bk_booking_type_name_date.'</sup>';
                         }
                     }
                    $short_dates_content .= '</a>';
                }
                $dcnt++;
            }


            // Get WIDE Dates showing data /////////////////////////////////////////////////////////////////////////////////////////////////////
            $wide_dates_content = '';
            $dates_count = count($bk_dates); $dcnt = 0;
            foreach ($bk_dates as $dt) { $dcnt++;
                $wide_dates_content .= '<a href="javascript:;" class="field-booking-date ';
                if ($is_approved) $wide_dates_content .= ' approved';
                $wide_dates_content .= ' ">';

                $bk_date = wpdevbk_get_date_in_correct_format($dt->booking_date);
                $wide_dates_content .=  $bk_date[0];
                $wide_dates_content .= '<sup class="field-booking-time">' . $bk_date[1]. '</sup>';
                 // BL
                if (class_exists('wpdev_bk_biz_l')) {
                 if ($dt->type_id != '') {
                     $bk_booking_type_name_date   = $booking_types[$dt->type_id]->title;        // Default
                     if (strlen($bk_booking_type_name_date)>19) $bk_booking_type_name_date = substr($bk_booking_type_name_date, 0, 13) . '...' . substr($bk_booking_type_name_date, -3 );
                     $wide_dates_content .= '<sup class="field-booking-time date_from_dif_type"> '.$bk_booking_type_name_date.'</sup>';
                 }
                }
                 $wide_dates_content .= '</a>';
                 if ($dcnt<$dates_count) { $wide_dates_content .= '<span class="date_tire">, </span>'; }
            }
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


            // BS
            $pay_print_status = '';
            if (class_exists('wpdev_bk_biz_s')) {
                if ($is_paid) {
                    $pay_print_status = __('Paid OK', 'wpdev-booking');
                    if ($payment_status_titles_current == 'Completed') $pay_print_status = $payment_status_titles_current;
                } else if ( (is_numeric($bk_pay_status)) || ($bk_pay_status == '') )        {
                    $pay_print_status = __('Unknown', 'wpdev-booking');
                } else  {
                    $pay_print_status = $payment_status_titles_current;
                }
            }
            ///// Print data  //////////////////////////////////////////////////////////////////////////////
            $print_data[] = apply_bk_filter('get_bklist_print_row', array() ,
                                             $booking_id,
                                             $is_approved ,
                                             $bk_form_show,
                                             $bk_booking_type_name,
                                             $is_paid ,
                                             $pay_print_status,
                                             ($booking_date_view_type == 'short')?'<div class="booking_dates_small">' . $short_dates_content . '</div>':'<div class="booking_dates_full">' .$wide_dates_content . '</div>' ,
                                             $bk_cost
                    );

            //////////////////////////////////////////////////////////////////////////////////////////////
            ?>
          <div id="booking_mark_<?php echo $booking_id; ?>"  class="<?php if ( $is_new!= '1') echo ' hidden_items '; ?> new-label clearfix-height"><img src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/label_new_blue.png" style="width:24px; height:24px;"></div>
          <div id="booking_row_<?php echo $booking_id; ?>"  class="row-fluid booking-listing-row clearfix-height<?php
          if ($is_alternative_color) echo ' row_alternative_color ';
          if ($is_selected_color) echo ' row_selected_color ';
          //if ($is_new) echo ' row_unread_color ';
          
            //$date_format = get_bk_option( 'booking_date_format');
            //$time_format = get_bk_option( 'booking_time_format');
            if (empty($date_format)) $date_format = "m / d / Y, D";
            if (empty($time_format)) $time_format = 'h:i a';
            $cr_date = date_i18n($date_format  , mysql2date('U',$bk_modification_date));
            $cr_time = date_i18n($time_format  , mysql2date('U',$bk_modification_date));
          ?>" >

              <div class="booking-listing-collumn span1 bktextcenter">
                  <input type="checkbox" class="booking_list_item_checkbox" 
                         onclick="javascript: if (jQuery(this).attr('checked') !== undefined ) { jQuery(this).parent().parent().addClass('row_selected_color'); } else {jQuery(this).parent().parent().removeClass('row_selected_color');}"
                         <?php if ($is_selected_color) echo ' checked="CHECKED" '; ?>
                         id="booking_id_selected_<?php  echo $booking_id;  ?>"  name="booking_appr_<?php  $booking_id;  ?>"
                         />&nbsp;&nbsp;&nbsp;
                  <span class="field-id"><?php echo $booking_id; ?></span>
                  <div class="field-date"> <?php echo $cr_date; ?></div>
                  <span class="field-time"> <?php echo $cr_time; ?></span>
              </div>

              <div class="booking-listing-collumn span2 bktextleft booking-labels">
                  <?php make_bk_action('wpdev_bk_listing_show_resource_label', $bk_booking_type_name );  ?>
                  <?php make_bk_action('wpdev_bk_listing_show_payment_label', $is_paid,  $pay_print_status, $payment_status_titles_current);  ?>
                  <span class="label label-pending <?php if ($is_approved) echo ' hidden_items '; ?> "><?php _e('Pending', 'wpdev-booking'); ?></span>
                  <span class="label label-approved <?php if (! $is_approved) echo ' hidden_items '; ?>"><?php _e('Approved', 'wpdev-booking'); ?></span>
              </div>

              <div class="booking-listing-collumn span4 bktextjustify">
                    <div style="text-align:left"><?php echo $bk_form_show; ?></div>
              </div>

              <div class="booking-listing-collumn span3 bktextleft booking-dates">

                <div class="booking_dates_small <?php echo $short_days_class; ?>"><?php echo $short_dates_content; ?></div>
                <div class="booking_dates_full  <?php echo $wide_days_class; ?>" ><?php echo $wide_dates_content;  ?></div>

              </div>

              <?php // P
                    $edit_booking_url = $bk_url_add . '&booking_type='.$bk_booking_type.'&booking_hash='.$bk_hash.'&parent_res=1' ; ?>

              <div class="booking-listing-collumn span2 bktextcenter  booking-actions">

                  <?php make_bk_action('wpdev_bk_listing_show_cost_btn', $booking_id, $bk_cost );  ?>
                  
                  <div class="actions-fields-group">

                    <?php make_bk_action('wpdev_bk_listing_show_edit_btn', $booking_id , $edit_booking_url, $bk_remark, $bk_booking_type );  ?>

                    <a href="javascript:;"  class="tooltip_bottom approve_bk_link  <?php if ($is_approved) echo ' hidden_items '; ?> "
                       onclick="javascript:approve_unapprove_booking(<?php echo $booking_id; ?>,1, <?php echo $user_bk_id; ?>, '<?php echo getBookingLocale(); ?>' , 1  );"
                       data-original-title="<?php _e('Approve','wpdev-booking'); ?>"  rel="tooltip" >
                        <img src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/accept-24x24.gif" style="width:14px; height:14px;"></a>
                    
                    <a href="javascript:;"  class="tooltip_bottom pending_bk_link  <?php if (! $is_approved) echo ' hidden_items '; ?> "
                       onclick="javascript:if ( bk_are_you_sure('<?php echo esc_js(__('Are you really want to set booking as pending ?', 'wpdev-booking')); ?>') ) approve_unapprove_booking(<?php echo $booking_id; ?>,0, <?php echo $user_bk_id; ?>, '<?php echo getBookingLocale(); ?>' , 1  );"
                       data-original-title="<?php _e('Unapprove','wpdev-booking'); ?>"  rel="tooltip" >
                        <img src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/remove-16x16.png" style="width:15px; height:15px;"></a>
                    
                    <a href="javascript:;" 
                       onclick="javascript:if ( bk_are_you_sure('<?php echo esc_js(__('Are you really want to delete this booking ?', 'wpdev-booking')); ?>') ) delete_booking(<?php echo $booking_id; ?>, <?php echo $user_bk_id; ?>, '<?php echo getBookingLocale(); ?>' , 1   );"
                       data-original-title="<?php _e('Delete','wpdev-booking'); ?>"  rel="tooltip" class="tooltip_bottom">
                        <img src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/delete_type.png" style="width:13px; height:13px;"></a>

                    <?php make_bk_action('wpdev_bk_listing_show_payment_status_btn', $booking_id );  ?>
                      
                  </div>
              </div>

              <?php make_bk_action('wpdev_bk_listing_show_edit_fields', $booking_id , $bk_remark );  ?>

              <?php make_bk_action('wpdev_bk_listing_show_payment_status_cost_fields', $booking_id  , $bk_pay_status);  ?>
              
          </div>
        <?php } ?>
        </div>

        <?php  //if  ( is_field_in_table_exists('booking','is_new') != 0 )  renew_NumOfNewBookings($id_of_new_bookings); // Update num status if supported  ?>

        <?php make_bk_action('wpdev_bk_listing_show_change_booking_resources', $booking_types);  ?>

        <?php if ( function_exists('wpdevbk_generate_print_loyout')) wpdevbk_generate_print_loyout( $print_data );
    }


    //  P a g i n a t i o n     of    Booking Listing
    function wpdevbk_show_pagination($summ_number_of_items, $active_page_num, $num_items_per_page , $only_these_parameters = false ) {

        $pages_number = ceil ( $summ_number_of_items / $num_items_per_page );
        if ( $pages_number < 2 ) return;
        
        $bk_admin_url = get_params_in_url( array('page_num') , $only_these_parameters );
        
        ?>
        <div class="pagination pagination-centered" style="height:auto;">
          <ul>
              
            <?php if ($pages_number>1) { ?>
                <li <?php if ($active_page_num == 1) echo ' class="disabled" '; ?> >
                    <a href="<?php echo $bk_admin_url; ?>&page_num=<?php if ($active_page_num == 1) { echo $active_page_num; } else { echo ($active_page_num-1); } ?>">
                        <?php _e('Prev', 'wpdev-booking'); ?>
                    </a>
                </li>
            <?php } ?>
            
            <?php for ($pg_num = 1; $pg_num <= $pages_number; $pg_num++) { ?>

              <li <?php if ($pg_num == $active_page_num ) echo ' class="active" '; ?> >
                  <a href="<?php echo $bk_admin_url; ?>&page_num=<?php echo $pg_num; ?>">
                    <?php echo $pg_num; ?>
                  </a>
              </li>

            <?php } ?>

            <?php if ($pages_number>1) { ?>
                <li <?php if ($active_page_num == $pages_number) echo ' class="disabled" '; ?> >
                    <a href="<?php echo $bk_admin_url; ?>&page_num=<?php  if ($active_page_num == $pages_number) { echo $active_page_num; } else { echo ($active_page_num+1); } ?>">
                        <?php _e('Next', 'wpdev-booking'); ?>
                    </a>
                </li>
            <?php } ?>

          </ul>
        </div>
        <?php
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //   J S   and   C S S   for the Booking Listing page   ///////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function wpdevbk_booking_listing_write_js(){
        ?>
            <script type="text/javascript">
              jQuery(document).ready( function(){
                jQuery('input.wpdevbk-filters-section-calendar').datepick(
                    {   //onSelect: selectCheckInDay,
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

                jQuery('# a.popover_here').popover( {
                    placement: 'bottom'
                  , delay: { show: 100, hide: 100 }
                  , content: ''
                  , template: '<div class="wpdevbk popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
                });
            <?php
            $is_use_hints = get_bk_option( 'booking_is_use_hints_at_admin_panel'  );
            if ($is_use_hints == 'On')
                if (  ( ( strpos($_SERVER['REQUEST_URI'],'wpdev-booking.php')) !== false) &&
                      (   ( strpos($_SERVER['REQUEST_URI'],'wpdev-booking.phpwpdev-booking-reservation'))  === false)
                   ) { ?>

                jQuery('.tooltip_right').tooltip( {
                    animation: true
                  , delay: { show: 500, hide: 100 }
                  , selector: false
                  , placement: 'right'
                  , trigger: 'hover'
                  , title: ''
                  , template: '<div class="wpdevbk tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
                });

                jQuery('.tooltip_left').tooltip( {
                    animation: true
                  , delay: { show: 500, hide: 100 }
                  , selector: false
                  , placement: 'left'
                  , trigger: 'hover'
                  , title: ''
                  , template: '<div class="wpdevbk tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
                });

                jQuery('.tooltip_top').tooltip( {
                    animation: true
                  , delay: { show: 500, hide: 100 }
                  , selector: false
                  , placement: 'top'
                  , trigger: 'hover'
                  , title: ''
                  , template: '<div class="wpdevbk tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
                });

                jQuery('.tooltip_bottom').tooltip( {
                    animation: true
                  , delay: { show: 500, hide: 100 }
                  , selector: false
                  , placement: 'bottom'
                  , trigger: 'hover'
                  , title: ''
                  , template: '<div class="wpdevbk tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
                });
                <?php } ?>
                //jQuery('.dropdown-toggle').dropdown();

               });
              </script>
        <?php
    }

    function wpdevbk_booking_listing_write_css(){
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
                #datepick-div .datepick-one-month {
                    height: 215px;
                }
            </style>
        <?php
    }
?>