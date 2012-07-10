<?php 
/**
 * I use this class to order the categories the way I want
 */
class Walker_Caticons extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');
	/**
	 * Start the element output
	 */
	function start_el(&$output, $category, $depth, $args) {
		extract($args);
		$output .= $category->term_id.',';
	}
}
?>