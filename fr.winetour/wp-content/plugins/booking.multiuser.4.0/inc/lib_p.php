<?php
/*
This is COMMERCIAL SCRIPT
We are do not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  S u p p o r t    f u n c t i o n s       ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Get COUNT of booking resources.
        function get_booking_resources_count(){
            global $wpdb;
            $sql_count  = " SELECT COUNT(*) as count FROM ".$wpdb->prefix ."bookingtypes as bt" ;

            $where = '';
            $where = apply_bk_filter('multiuser_modify_SQL_for_current_user', $where);
            if ($where != '') $where = ' WHERE ' . $where;
            if ( class_exists('wpdev_bk_biz_l')) {
                if ($where != '')   $where .= ' AND bt.parent = 0 ';
                else                $where .= ' WHERE bt.parent = 0 ';
            }
            if (isset($_REQUEST['wh_resource_id'])) {
                 if ($where == '') $where .= " WHERE " ;
                 else $where .= " AND ";
                 $where .= " ( (bt.booking_type_id = '" . $_REQUEST['wh_resource_id'] . "') OR (bt.title like '%%".$_REQUEST['wh_resource_id']."%%') )  ";
            }

            $booking_resources_count = $wpdb->get_results( $wpdb->prepare( $sql_count . $where ) );
            return $booking_resources_count[0]->count;
        }

        function get_booking_types_all_parents_and_single(){ global $wpdb;

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

            if (strpos($or_sort, '_asc') !== false) {                            // Order
                   $or_sort = str_replace('_asc', '', $or_sort);
                   $sql_order = " ORDER BY " .$or_sort ." ASC ";
            } else $sql_order = " ORDER BY " .$or_sort ." DESC ";

            $types_list = $wpdb->get_results( $wpdb->prepare( $sql .  $where. $sql_order ) );

            return  $types_list;
        }

        // Get booking types from DB
        function get_bk_types($is_use_filter = false  ) { global $wpdb;

            ////////////////////////////////////////////////////////////////////////
            // CONSTANTS
            ////////////////////////////////////////////////////////////////////////
            /*update_bk_option( 'booking_resourses_num_per_page',3);
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

            $types_list = $wpdb->get_results( $wpdb->prepare( $sql .  $where. $sql_order . $sql_limit ) );



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

        function get__default_type(){
                    global $wpdb;
                    $mysql = "SELECT booking_type_id as id FROM  ".$wpdb->prefix ."bookingtypes ORDER BY id ASC LIMIT 1";
                    $types_list = $wpdb->get_results( $wpdb->prepare($mysql) );
                    if (count($types_list) > 0 ) $types_list = $types_list[0]->id;
                    else $types_list =1;
                    return $types_list;

        }

        function get_booking_title( $type_id = 1){
            global $wpdb;
            $types_list = $wpdb->get_results( $wpdb->prepare("SELECT title FROM ".$wpdb->prefix ."bookingtypes  WHERE booking_type_id =" . $type_id) );
            if ($types_list)
                return $types_list[0]->title;
            else
                return '';
        }




        function get_bk_types____OLD(){
                        global $wpdb;
                    if ( class_exists('wpdev_bk_biz_s'))
                        $mysql = "SELECT booking_type_id as id, title, cost FROM ".$wpdb->prefix ."bookingtypes  ORDER BY title";
                    else
                        $mysql = "SELECT booking_type_id as id, title FROM ".$wpdb->prefix ."bookingtypes  ORDER BY title";

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

        }

        function wpdebk_get_keyed_all_bk_resources($blank){
            // Get All Booking types in array with Keys using bk res ID
            $booking_types = array();
            $booking_types_res = get_bk_types();  // All types
            foreach ($booking_types_res as $value) {
                $booking_types[$value->id] = $value;
            }
            return $booking_types;
        }
        add_bk_filter('wpdebk_get_keyed_all_bk_resources', 'wpdebk_get_keyed_all_bk_resources');


        // A J A X     R e s p o n d e r   Real Ajax with jQuery sender     //////////////////////////////////////////////////////////////////////////////////
        function wpdev_pro_bk_ajax(){

            global $wpdb;

            $action = $_POST['ajax_action'];

            switch ($action) {
                case 'ADD_BK_TYPE':
                    $title = $_POST[ "title" ];
                    $wp_querie  = "INSERT INTO ".$wpdb->prefix ."bookingtypes (
                     title
                    ) VALUES (
                     '".$title."'
                    );";

                    if ( false === $wpdb->query($wpdb->prepare( $wp_querie ) ) ){
                        ?> <script type="text/javascript">document.getElementById('ajax_message').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during inserting into BD',__FILE__,__LINE__ ); ?></div>'; jQuery('#ajax_message').fadeOut(5000); </script> <?php
                        die();
                    } else {
                        $newid = (int) $wpdb->insert_id;
                        make_bk_action('added_new_booking_resource',$newid);
                        ?> <script type="text/javascript">
                            document.getElementById('ajax_message').innerHTML = '<?php echo __('Saved', 'wpdev-booking'); ?>';
                            document.getElementById('last_book_type').innerHTML = '<?php
                            //echo '<a href="admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME . 'wpdev-booking&booking_type='.$newid.'"  class="bktypetitle">' . $title . '</a>  <a href="#" title="'. __('Delete', 'wpdev-booking') .'" style="text-decoration:none;" onclick="javascript:delete_bk_type('.$newid.');"><img src="'.WPDEV_BK_PLUGIN_URL.'/img/delete_type.png" width="8" height="8" /></a>' ;
                            echo '<a href="admin.php?page=' . WPDEV_BK_PLUGIN_DIRNAME . '/'. WPDEV_BK_PLUGIN_FILENAME . 'wpdev-booking&booking_type='.$newid.'"  class="bktypetitlenew">' .  $title  . '</a>';
                                    ?>';
                            jQuery('#last_book_type').attr("id",'bktype<?php echo $newid; ?>');
                            //jQuery('#last_book_type_separator').attr("id",'bktype_separator<?php echo $newid; ?>');
                            jQuery('#ajax_message').fadeOut(1000);
                        </script> <?php
                        die();
                    }

                    break;

                case 'EDIT_BK_TYPE':
                    $title = $_POST[ "title" ];
                    $type_id = $_POST[ "type_id" ];

                    $wp_querie  = "UPDATE ".$wpdb->prefix ."bookingtypes SET title='".$title."'  WHERE booking_type_id = $type_id";

                    if ( false === $wpdb->query($wpdb->prepare( $wp_querie ) ) ){
                        ?> <script type="text/javascript">document.getElementById('ajax_message').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during editing BD',__FILE__,__LINE__ ); ?></div>'; jQuery('#ajax_message').fadeOut(5000); </script> <?php
                        die();
                    } else {
                        ?> <script type="text/javascript">
                            document.getElementById('ajax_message').innerHTML = '<?php echo __('Saved', 'wpdev-booking'); ?>';
                            jQuery('#ajax_message').fadeOut(1000);
                        </script> <?php
                        die();
                    }
                    break;

                case 'DELETE_BK_TYPE':
                    $type_id = $_POST['type_id'];

                   $wp_querie = "DELETE FROM ".$wpdb->prefix ."bookingtypes WHERE booking_type_id = $type_id";

                    if ( false === $wpdb->query($wpdb->prepare( $wp_querie ) ) ){
                        ?> <script type="text/javascript">document.getElementById('ajax_message').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php bk_error('Error during deleting from BD' ,__FILE__,__LINE__); ?></div>'; jQuery('#ajax_message').fadeOut(5000); </script> <?php
                    } else {
                        ?> <script type="text/javascript">
                            document.getElementById('ajax_message').innerHTML = '<?php echo __('Deleted', 'wpdev-booking'); ?>';
                            jQuery('#ajax_message').fadeOut(1000);
                            jQuery('#bktype<?php echo $type_id; ?>' ).fadeOut(1000);
                            jQuery('#bktype_separator<?php echo $type_id; ?>' ).fadeOut(1000);
                        </script> <?php
                    }
                    die();

                    break;


                case 'ACTIVATE':

                    //debuge($_POST);

                    $base = 'http://activate.wpdevelop.com/decode.php';

                    $params = array(
                        'order_num'   => $_POST['num'] ,
                        'server'  =>  $_POST['site']
                    );

                    $query_string = "";
                    foreach ($params as $key => $value) {
                        $query_string .= "$key=" . urlencode($value) . "&";
                    }

                    $url = "$base?$query_string";

                    // Authorization.
                    $context = stream_context_create(array(
                        'http' => array(
                            'method' => 'GET'
                            //'header'  => "Authorization: Basic " . base64_encode("$username:$password")
                        )
                    ));

                    $output = file_get_contents($url, false, $context);
                    if (! empty($output)) {
                        if (is_serialized($output)) $output = unserialize($output);
                        //debuge($output);

                        if ($output['OK']) {
                            ?> <script type="text/javascript"> document.getElementById('a_o').value='<?php echo $output['HASH']; ?>'; </script> <?php
                        }
                    }
                default:
                    break;
            }

        }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  Filters interface     Controll elements  ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Booking     R e s o u r c e s Filter field
        function wpdebk_filter_field_bk_resources(){

            $types_list = get_bk_types();
            $wpdevbk_id =              'wh_booking_type';                           //  {'', '1', '4,7,5', .... }
            $wpdevbk_selectors = array();
            $wpdevbk_selectors['<strong>'.__('All resources','wpdev-booking').'</strong>']='';
            //$wpdevbk_selectors['divider0']='divider';

            //for ($i = 0; $i < 1000; $i++)
            foreach ($types_list as $bkr) {
                $bkr_title = $bkr->title;
                    if ($bkr->parent == 0)
                    $bkr_title = $bkr_title;
                else
                    $bkr_title = '&nbsp;&nbsp;&nbsp;' . $bkr_title ;
                $wpdevbk_selectors[$bkr_title  ] = $bkr->id;
            }

            $wpdevbk_control_label =   '';
            $wpdevbk_help_block =      __('Booking resources', 'wpdev-booking');

            wpdevbk_selectbox_normal_filter($wpdevbk_id, $wpdevbk_selectors, $wpdevbk_control_label, $wpdevbk_help_block);


        }

        // Keyword Filter field
        function wpdebk_filter_field_bk_keyword(){

            $wpdevbk_id =              'wh_keyword';                           //  {'',  '1' }
            $wpdevbk_control_label =   __('Keyword', 'wpdev-booking');
            $wpdevbk_help_block =      __('Filter by Keyword', 'wpdev-booking');

            wpdevbk_text_filter($wpdevbk_id, $wpdevbk_control_label, $wpdevbk_help_block);
        }

        // Get the sort options for the filter at the booking listing page
        function get_p_bk_filter_sort_options($wpdevbk_selectors_def){

              $wpdevbk_selectors = array(__('ID', 'wpdev-booking').'&nbsp;<i class="icon-arrow-up "></i>' =>'',
                               __('Resource', 'wpdev-booking').'&nbsp;<i class="icon-arrow-up "></i>' =>'booking_type',
                               'divider0'=>'divider',
                               __('ID', 'wpdev-booking').'&nbsp;<i class="icon-arrow-down "></i>' =>'booking_id_asc',
                               __('Resource', 'wpdev-booking').'&nbsp;<i class="icon-arrow-down "></i>' =>'booking_type_asc'
                              );
              return $wpdevbk_selectors;
        }
        add_bk_filter('bk_filter_sort_options', 'get_p_bk_filter_sort_options');


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  Actions interface     Controll elements  ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Keyword Filter field
        function wpdebk_action_field_export_print(){
            ?>
            <div class="btn-group" style="margin-top: 2px; vertical-align: top;">
                <a  data-original-title="<?php _e('Print bookings listing'); ?>"  rel="tooltip"
                    class="tooltip_top btn" onclick='javascript:print_booking_listing();'
                   /><?php _e('Print', 'wpdev-booking'); ?> <i class="icon-print"></i></a>
                <a data-original-title="<?php _e('Export only current page of bookings to CSV format'); ?>"  rel="tooltip" class="tooltip_top btn" onclick='javascript:export_booking_listing("page");'
                   /><?php _e('Export', 'wpdev-booking'); ?> <i class="icon-list"></i></a>
                <a data-original-title="<?php _e('Export All bookings to CSV format'); ?>"  rel="tooltip"
                   class="tooltip_top btn" onclick='javascript:export_booking_listing("all");'
                   /><?php _e('Export All', 'wpdev-booking'); ?> <i class="icon-list"></i></a>
            </div>
            <?php
        }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  S Q L   Modifications  for  Booking Listing  ///////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Keyword
        function get_p_bklist_sql_keyword($blank, $wh_keyword ){
            $sql_where = '';

            if ( $wh_keyword !== '' )
                $sql_where .= " AND  bk.form LIKE '%%" . $wh_keyword . "%%' ";

            return $sql_where;
        }
        add_bk_filter('get_bklist_sql_keyword', 'get_p_bklist_sql_keyword');


        // Resources
        function get_p_bklist_sql_resources($blank, $wh_booking_type, $wh_approved, $wh_booking_date, $wh_booking_date2 ){
            global $wpdb;
            $sql_where = '';

            if ( ! empty($wh_booking_type) )  {
                // P
                $sql_where.=   " AND (  " ;
                $sql_where.=   "       ( bk.booking_type IN  ( ". $wh_booking_type ." ) ) " ;     // BK Resource conections

                //  BL
                $sql_where .= apply_bk_filter('get_l_bklist_sql_resources', ''  , $wh_booking_type, $wh_approved, $wh_booking_date, $wh_booking_date2 );

                // P
                $sql_where.=   "     )  " ;
            }

            return $sql_where;
        }
        add_bk_filter('get_bklist_sql_resources', 'get_p_bklist_sql_resources');


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  P r i n t    L o y o u t     ///////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        function wpdevbk_generate_print_loyout( $print_data ) {
            ?>
              <div style="display:none;">
                  <div id="booking_print_loyout">
                      <table style="width:100%;" >
                          <thead>
                              <tr class="booking-listing-header">
                                  <th style="width:10%"><?php echo $print_data[0][0]; ?></th>
                                  <th style="width:10%"><?php echo $print_data[0][1]; ?></th>
                                  <th ><?php echo $print_data[0][2]; ?></th>
                                  <th style="width:20%"><?php echo $print_data[0][3]; ?></th>
                                  <th style="width:10%"><?php echo $print_data[0][4]; ?></th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php
                              $is_alternative_color = true;
                              for ($i = 1; $i < count($print_data); $i++) {
                                      $print_item = $print_data[$i] ;
                                      $is_alternative_color = ! $is_alternative_color;
                              ?>
                              <tr class="booking-listing-row <?php if ($is_alternative_color) echo ' row_alternative_color ';?>" >
                                  <td class=" bktextcenter"><?php echo $print_item[0]; ?></td>
                                  <td class=" bktextcenter"><?php echo '<span class="label">'.$print_item[1][0] . '</span>, <span class="label">' . $print_item[1][1] . '</span>, <span class="label">' . $print_item[1][2] .'</span>'; ?></td>
                                  <td class=" bktextcenter"><?php echo $print_item[2]; ?></td>
                                  <td class=" bktextcenter"><?php echo strip_tags($print_item[3]); ?></td>
                                  <td class=" bktextcenter"><span  class="label"><?php echo $print_item[4][0] . ' ' . $print_item[4][1]; ?></span></td>
                              </tr>
                              <?php } ?>
                          </tbody>
                      </table>
                  </div>
              </div>
            <?php
        }

        function get_bklist_print_header($blank){
            return array(
                             array(
                                    __('ID', 'wpdev-booking'),
                                    __('Labels', 'wpdev-booking'),
                                    __('Data', 'wpdev-booking'),
                                    __('Dates', 'wpdev-booking'),
                                    __('Cost', 'wpdev-booking'),
                                   )
                            );
        }
        add_bk_filter('get_bklist_print_header', 'get_bklist_print_header');

        function get_bklist_print_row($blank, $booking_id,
                                             $is_approved ,
                                             $bk_form_show,
                                             $bk_booking_type_name,
                                             $is_paid ,
                                             $pay_print_status ,
                                             $print_dates,
                                             $bk_cost ){

            if ($is_approved) $bk_print_status =  __('Approved', 'wpdev-booking');
            else              $bk_print_status =  __('Pending', 'wpdev-booking');

            //BS
            $currency = apply_bk_filter('get_currency_info', 'paypal');

            return array(  $booking_id,
                                    array($bk_print_status, $bk_booking_type_name, $pay_print_status),
                                    $bk_form_show,
                                    $print_dates,
                                    array($currency, $bk_cost)                                  //BS
                                  );
        }
        add_bk_filter('get_bklist_print_row', 'get_bklist_print_row');



    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  H T M L   &  E l e m e n t s   in   Booking   L i s t i n g  Table  ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        function wpdev_bk_listing_show_edit_btn( $booking_id , $edit_booking_url, $bk_remark, $bk_booking_type ){

          ?>
            <a href="<?php echo $edit_booking_url; ?>" onclick="" data-original-title="<?php _e('Edit Booking','wpdev-booking'); ?>"  rel="tooltip" class="tooltip_bottom">
                <img src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/edit_type.png" style="width:12px; height:13px;"></a>

            <a href="javascript:;"
               data-original-title="<?php if ($bk_remark=='') { _e('Edit Note','wpdev-booking'); } else {echo esc_js(substr($bk_remark,0,100)); if (strlen($bk_remark)>100) {echo '...';}  } ?>"
               rel="tooltip" class="remark_bk_link <?php if ($bk_remark=='') { echo 'tooltip_bottom';} else { echo 'tooltip_top';} ?>"
               onclick='javascript: if (document.getElementById(&quot;remark_row<?php echo $booking_id;?>&quot;).style.display==&quot;block&quot;) document.getElementById(&quot;remark_row<?php echo $booking_id;?>&quot;).style.display=&quot;none&quot;; else document.getElementById(&quot;remark_row<?php echo $booking_id;?>&quot;).style.display=&quot;block&quot;; ' >
                <img src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/notes<?php if ($bk_remark!='') echo '_rd' ?>.png" style="width:16px; height:16px;"></a>

            <a href="javascript:;"  data-original-title="<?php _e('Change Resource','wpdev-booking'); ?>"  rel="tooltip" class="tooltip_bottom"
               onclick='javascript:
                     document.getElementById("new_booking_resource_booking_id").value = "<?php echo $booking_id; ?>";
                     setSelectBoxByValue("new_booking_resource", <?php echo $bk_booking_type; ?> );
                     var cbr;
                     cbr = jQuery("#change_booking_resource_controll_elements").detach();
                     cbr.appendTo(jQuery("#changing_bk_res_in_booking<?php echo $booking_id; ?>"));
                     cbr = null;
                     jQuery(".booking_row_modification_element_changing_resource").hide();
                     jQuery("#changing_bk_res_in_booking<?php echo $booking_id; ?>").show();
               ' >
                <img src="<?php echo WPDEV_BK_PLUGIN_URL; ?>/img/exchange.png" style="width:16px; height:16px;"></a>
          <?php
        }
        add_bk_action( 'wpdev_bk_listing_show_edit_btn', 'wpdev_bk_listing_show_edit_btn');



        function wpdev_bk_listing_show_edit_fields( $booking_id , $bk_remark ){
          ?>
              <?php //P : Edit Note  ?>
              <div class="booking_row_modification_element" id="remark_row<?php echo $booking_id; ?>" >
                    <textarea id="remark_text<?php echo $booking_id; ?>"  name="remark_text<?php echo $booking_id; ?>" cols="2" rows="2" style="width:99%;margin:5px;"><?php echo $bk_remark; ?></textarea>
                    <input type="button" value="<?php _e('Cancel','wpdev-booking'); ?>" class="btn bkalignright" style="margin:0px 8px;"
                           onclick='javascript:document.getElementById("remark_row<?php echo $booking_id; ?>").style.display="none";' />
                    <input type="button" value="<?php _e('Save','wpdev-booking'); ?>" class="btn btn-primary bkalignright"
                           onclick='javascript:wpdev_add_remark(<?php echo $booking_id; ?>, document.getElementById("remark_text<?php echo $booking_id; ?>").value);' />
               </div>

               <?php //P : Chnage Resources  ?>
               <div id="changing_bk_res_in_booking<?php echo $booking_id; ?>" class="booking_row_modification_element_changing_resource booking_row_modification_element" ></div>
          <?php
        }
        add_bk_action( 'wpdev_bk_listing_show_edit_fields', 'wpdev_bk_listing_show_edit_fields');




        function wpdev_bk_listing_show_change_booking_resources(  $booking_types ){
          ?>
          <div id="hided_boking_modifications_elements">
            <div id="change_booking_resource_controll_elements">
                <input type="hidden" value="" id="new_booking_resource_booking_id" />
                <input type="button" value="<?php _e('Cancel','wpdev-booking'); ?>" class="btn bkalignright" style="margin:3px 7px 7px 2px;"
                       onclick='javascript:
                         var cbrce;
                         cbrce = jQuery("#change_booking_resource_controll_elements").detach();
                         cbrce.appendTo(jQuery("#hided_boking_modifications_elements"));
                         cbrce = null;
                         jQuery(".booking_row_modification_element_changing_resource").hide();
                     ' />
                <input type="button" value="<?php _e('Change','wpdev-booking'); ?>" class="btn btn-primary bkalignright"   style="margin:3px 7px 7px 5px;"
                       onclick='javascript:wpdev_change_bk_resource(document.getElementById("new_booking_resource_booking_id").value, document.getElementById("new_booking_resource").value);
                         var cbrce;
                         cbrce = jQuery("#change_booking_resource_controll_elements").detach();
                         cbrce.appendTo(jQuery("#hided_boking_modifications_elements"));
                         cbrce = null;
                         jQuery(".booking_row_modification_element_changing_resource").hide();
                       ' />
                <select id="new_booking_resource" name="new_booking_resource" class="bkalignright" style="margin:2px 5px;">
                    <?php
                    foreach ($booking_types as $mm) { ?>
                    <option value="<?php echo $mm->id; ?>"
                          style="<?php if  (isset($mm->parent)) if ($mm->parent == 0 ) { echo 'font-weight:bold;'; } else { echo 'font-size:11px;padding-left:20px;'; } ?>"
                        ><?php echo $mm->title; ?></option>
                    <?php } ?>
                </select>
            </div>
          </div>
          <?php
        }
        add_bk_action( 'wpdev_bk_listing_show_change_booking_resources', 'wpdev_bk_listing_show_change_booking_resources');



        function wpdev_bk_listing_show_resource_label(  $bk_booking_type_name ){
           ?>
             <span class="label label-resource label-info"><?php echo $bk_booking_type_name; ?></span>
           <?php
        }
        add_bk_action( 'wpdev_bk_listing_show_resource_label', 'wpdev_bk_listing_show_resource_label');
?>
