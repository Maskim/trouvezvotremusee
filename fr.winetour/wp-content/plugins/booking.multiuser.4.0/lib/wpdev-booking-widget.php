<?php

// BookingWidget Class
class BookingWidget extends WP_Widget {
    /** constructor */
    function BookingWidget() {
        parent::WP_Widget(false, $name = 'Booking Calendar');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        $booking_widget_title = apply_filters('widget_title', $instance['booking_widget_title']);
        $booking_widget_show = $instance['booking_widget_show'];
        $booking_widget_type = $instance['booking_widget_type'];
        if (empty($booking_widget_type)) $booking_widget_type=1;
        $booking_widget_calendar_count = $instance['booking_widget_calendar_count'];
        $booking_widget_last_field = $instance['booking_widget_last_field'];


        echo $before_widget;
        if (isset($_GET['booking_hash'])) {
            _e('You need to use special shortcode [bookingedit] for booking editing.','wpdev-booking');
            echo $after_widget;
            return;
        }

        if ($booking_widget_title != '') echo $before_title . htmlspecialchars_decode($booking_widget_title) . $after_title;

        echo "<div class='widget_wpdev_booking' style='float:left;margin:10px 0px;' >";
        if ($booking_widget_show == 'booking_form') {
            do_action('wpdev_bk_add_form', $booking_widget_type , $booking_widget_calendar_count);
        } else {
            do_action('wpdev_bk_add_calendar', $booking_widget_type , $booking_widget_calendar_count);
        }

        if ($booking_widget_last_field !== '') echo '<br/>' . htmlspecialchars_decode($booking_widget_last_field);
        echo "</div>";

        echo $after_widget;


    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
	$instance = $old_instance;

	$instance['booking_widget_title']           = strip_tags($new_instance['booking_widget_title']);
	$instance['booking_widget_show']            = strip_tags($new_instance['booking_widget_show']);
	$instance['booking_widget_type']            = strip_tags($new_instance['booking_widget_type']);
	$instance['booking_widget_calendar_count']  = strip_tags($new_instance['booking_widget_calendar_count']);
	$instance['booking_widget_last_field']      = $new_instance['booking_widget_last_field'];
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        if ( isset($instance['booking_widget_title']) ) $booking_widget_title           = esc_attr($instance['booking_widget_title']);
        else $booking_widget_title = '';
        if ( isset($instance['booking_widget_show']) ) $booking_widget_show            = esc_attr($instance['booking_widget_show']);
        else $booking_widget_show = '';
        if ( ( class_exists('wpdev_bk_personal') ) && ( isset($instance['booking_widget_type']) ) ) {
            $booking_widget_type            = esc_attr($instance['booking_widget_type']);
        } else $booking_widget_type=1;
        if ( isset($instance['booking_widget_calendar_count']) ) $booking_widget_calendar_count  = esc_attr($instance['booking_widget_calendar_count']);
        else $booking_widget_calendar_count = 1;
        if ( isset($instance['booking_widget_last_field']) ) $booking_widget_last_field      = esc_attr($instance['booking_widget_last_field']);
        else $booking_widget_last_field = '';
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('booking_widget_title'); ?>"><?php _e('Title', 'wpdev-booking'); ?>:</label><br/>
            <input value="<?php echo $booking_widget_title; ?>"
                   name="<?php echo $this->get_field_name('booking_widget_title'); ?>"
                   id="<?php echo $this->get_field_id('booking_widget_title'); ?>"
                   type="text" class="widefat" style="width:100%;" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('booking_widget_show'); ?>"><?php _e('Show', 'wpdev-booking'); ?>:</label><br/>
            <select
                   name="<?php echo $this->get_field_name('booking_widget_show'); ?>"
                   id="<?php echo $this->get_field_id('booking_widget_show'); ?>" style="width:100%;">
                <option <?php if($booking_widget_show == 'booking_form') echo "selected"; ?> value="booking_form"><?php _e('Booking form', 'wpdev-booking'); ?></option>
                <option <?php if($booking_widget_show == 'booking_calendar') echo "selected"; ?> value="booking_calendar"><?php _e('Booking calendar', 'wpdev-booking'); ?></option>
            </select>
        </p>


        <?php
        if ( class_exists('wpdev_bk_personal')) {
            $types_list = get_bk_types(); ?>
            <p>
                <label for="<?php echo $this->get_field_id('booking_widget_type'); ?>"><?php _e('Booking resource', 'wpdev-booking'); ?>:</label><br/>
                <!--input id="calendar_type"  name="calendar_type" class="input" type="text" -->
                <select
                       name="<?php echo $this->get_field_name('booking_widget_type'); ?>"
                       id="<?php echo $this->get_field_id('booking_widget_type'); ?>"
                       style="width:100%;">
                            <?php foreach ($types_list as $tl) { ?>
                    <option  <?php if($booking_widget_type == $tl->id ) echo "selected"; ?>
                        style="<?php if  (isset($tl->parent)) if ($tl->parent == 0 ) { echo 'font-weight:bold;'; } else { echo 'font-size:11px;padding-left:20px;'; } ?>"
                        value="<?php echo $tl->id; ?>"><?php echo $tl->title; ?></option>
                                <?php } ?>
                </select>

            </p>
        <?php } ?>

        <p>
            <label for="<?php echo $this->get_field_id('booking_widget_calendar_count'); ?>"><?php _e('Visible months', 'wpdev-booking'); ?>:</label><br/>
            <!--input value="<?php echo $booking_widget_calendar_count; ?>"
                   name="<?php echo $this->get_field_name('booking_widget_calendar_count'); ?>"
                   id="<?php echo $this->get_field_id('booking_widget_calendar_count'); ?>"
                   type="text" style="width:100%;" /-->

            <select
                   name="<?php echo $this->get_field_name('booking_widget_calendar_count'); ?>"
                   id="<?php echo $this->get_field_id('booking_widget_calendar_count'); ?>"
                   style="width:100%;">
                        <?php foreach (array(1,2,3,4,5,6,7,8,9,10,11,12) as $tl) { ?>
                <option  <?php if($booking_widget_calendar_count == $tl ) echo "selected"; ?>
                    style="font-weight:bold;"
                    value="<?php echo $tl; ?>"><?php echo $tl; ?></option>
                            <?php } ?>
            </select>

        </p>

        <p>
            <label for="<?php echo $this->get_field_id('booking_widget_last_field'); ?>"><?php _e('Footer', 'wpdev-booking'); ?>:</label><br/>
            <input value="<?php echo $booking_widget_last_field; ?>"
                   name="<?php echo $this->get_field_name('booking_widget_last_field'); ?>"
                   id="<?php echo $this->get_field_id('booking_widget_last_field'); ?>"
                   type="text" style="width:100%;" /><br/>
            <label><?php printf(__("Example: %sMake booking here%s", 'wpdev-booking'),"<code>&lt;a href='LINK'&gt;",'&lt;/a&gt;</code>'); ?></label>
        </p>

        <p style="font-size:10px;">
                    <?php printf(__("%sImportant!!!%s Please note, if you show booking calendar (inserted into post/page) with widget at the same page, then the last will not be visible.", 'wpdev-booking'),'<strong>','</strong>'); ?>

                    <?php if (!class_exists('wpdev_bk_personal')) { ?>

                            <em><?php printf(__("%sSeveral widgets are supported at %spaid versions%s.", 'wpdev-booking'),'<span style="">','<a href="http://wpbookingcalendar.com/" target="_blank" style="text-decoration:none;color:#3A5670;">','</a>','</span>'); ?></em>

                    <?php
                    }
?></p><?php
    }

} // class BookingWidget

?>
