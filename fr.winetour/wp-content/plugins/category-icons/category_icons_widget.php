<?php
/**
 * Category Icons Widget
 * Since 2.8.0
 *
*/
add_action('widgets_init', create_function('', 'return register_widget("WP_Widget_Caticons");'));

class WP_Widget_Caticons extends WP_Widget {

	function WP_Widget_Caticons() {
		load_plugin_textdomain('category_icons','wp-content/plugins/category-icons/languages/');
		$description = __( 'Easily assign icons to your categories','category_icons' );
		$widget_ops = array( 'classname' => 'widget_caticons', 'description' => $description );
		$this->WP_Widget('caticons', __('Category Icons','category_icons'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$c = $instance['count'] ? '1' : '0';
		$h = $instance['hierarchical'] ? '1' : '0';
		if (function_exists('mycategoryorder')) $o = $instance['order'] ? 'term_order' : 'name';
		$exclude = $instance['exclude'];
		$include = $instance['include'];
		if (empty($exclude)) $exclude = '0';
		if (empty($include)) $include = '0';
		$title = empty($instance['title']) ? __('Categories') : $instance['title'];
		echo $before_widget . $before_title . $title . $after_title;
		// my category order compatibility : $cat_args = "orderby=order&
		$cat_args = "orderby=";
		$cat_args .= function_exists('mycategoryorder') ?  $o : "name";
		$cat_args .= "&show_count={$c}&hierarchical={$h}&exclude={$exclude}&include={$include}";
		$putcaticons_parameters = $instance['putcaticons_parameters'];
		?>
			<ul>
			<?php
				if (!empty($putcaticons_parameters)) 
					put_cat_icons(wp_list_categories($cat_args . '&echo=0&title_li='), $putcaticons_parameters); 
				else
					put_cat_icons(wp_list_categories($cat_args . '&echo=0&title_li=')); 
			?>
			</ul>
		<?php
		
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['exclude'] = strip_tags($new_instance['exclude']);
		$instance['include'] = strip_tags($new_instance['include']);
		if (empty($exclude)) $exclude = '0';
		if (empty($include)) $include = '0';
		$instance['count'] = $new_instance['count'] ? 1 : 0;
		$instance['hierarchical'] = $new_instance['hierarchical'] ? 1 : 0;
		if (function_exists('mycategoryorder')) 
			$instance['order'] = strip_tags($new_instance['order']);
		$instance['putcaticons_parameters'] = strip_tags($new_instance['putcaticons_parameters']);
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = esc_attr( $instance['title'] );
		$count = (bool) $instance['count'];
		$hierarchical = (bool) $instance['hierarchical'];
		if (function_exists('mycategoryorder')) 	$order = esc_attr( $instance['order'] );
		$exclude = esc_attr( $instance['exclude'] );
		$include = esc_attr( $instance['include'] );
		$putcaticons_parameters = esc_attr( $instance['putcaticons_parameters'] );
		
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Show post counts' ); ?></label><br />
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked( $hierarchical ); ?> />
		<label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e( 'Show hierarchy' ); ?></label>
				<?php if (function_exists('mycategoryorder')) { ?>
                <br />
                    <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>"<?php checked( $order); ?> />
                    <label for="<?php echo $this->get_field_id('order'); ?>"><?php _e( 'Order : use My Category Order plugin','category_icons' ); ?>
                </label>
                <?php 
				} // End if ?>
		<p>
                <label for="caticons-putcationsargs">
                    <?php _e('put_cat_icons() parameters:','category_icons'); ?><br />
                    <input class="widefat" id="<?php echo $this->get_field_id('putcaticons_parameters'); ?>" name="<?php echo $this->get_field_name('putcaticons_parameters'); ?>" type="text" value="<?php echo $putcaticons_parameters; ?>" />
                </label>
            </p>
            <p>
                <label for="caticons-exclude">
                    <?php _e('Exclude:'); ?>
                    <input class="widefat" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo $exclude; ?>" /><br/>
                    <small><?php _e('Category IDs, separated by commas.','category_icons'); ?></small>
            	</label>
            </p>
            <p>
                <label for="caticons-include">
                    <?php _e('Include:'); ?>
                    <input class="widefat" id="<?php echo $this->get_field_id('include'); ?>" name="<?php echo $this->get_field_name('include'); ?>" type="text" value="<?php echo $include; ?>" /><br/>
                    <small><?php _e('Category IDs, separated by commas.','category_icons'); ?></small>
            	</label>
            </p>
		</p>
<?php
	}
}