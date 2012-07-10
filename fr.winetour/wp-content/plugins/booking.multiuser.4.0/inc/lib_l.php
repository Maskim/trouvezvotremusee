<?php
/*
This is COMMERCIAL SCRIPT
We are do not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  S Q L   Modifications  for  Booking Listing  ///////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Resources
        function get_l_bklist_sql_resources($blank, $wh_booking_type, $wh_approved, $wh_booking_date, $wh_booking_date2 ){

                global $wpdb;
                $sql_where = '';

                // BL                                                               // Childs in dif sub resources
                $sql_where.=   "        OR ( bk.booking_id IN (
                                                         SELECT DISTINCT booking_id
                                                         FROM ".$wpdb->prefix ."bookingdates as dtt
                                                         WHERE  " ;
                if ($wh_approved !== '')
                                        $sql_where.=                  " dtt.approved = $wh_approved  AND " ;
                $sql_where.= set_dates_filter_for_sql($wh_booking_date, $wh_booking_date2, 'dtt.') ;

                $sql_where.=                                          "   (
                                                                        dtt.type_id IN ( ". $wh_booking_type ." )
                                                                        OR  dtt.type_id IN (
                                                                                             SELECT booking_type_id
                                                                                             FROM ".$wpdb->prefix ."bookingtypes as bt
                                                                                             WHERE  bt.parent IN ( ". $wh_booking_type ." )
                                                                                            )
                                                                     )
                                                         )
                                      ) " ;
                // BL                                                               // Just Childs sub resources
                $sql_where.=   "         OR ( bk.booking_type IN (
                                                             SELECT booking_type_id
                                                             FROM ".$wpdb->prefix ."bookingtypes as bt
                                                             WHERE  bt.parent IN ( ". $wh_booking_type ." )
                                                            )
                                      )" ;

            return $sql_where;
        }
        add_bk_filter('get_l_bklist_sql_resources', 'get_l_bklist_sql_resources');


?>
