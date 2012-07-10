<?php  
	/* 
	Plugin Name: Pixelentity - Estro Slider 
	Plugin URI: http://codecanyon.net 
	Description: This jQuery plugin uses unobstrusive javascript to transform a block of simple HTML markup into a georgous elegant slider. 
	Ken Burns & swipe jQuery slider 
	Author: pixelentity 
	Version: 1.0 
	Author URI: http://pixelentity.com
	*/ 
	
	if (!class_exists("PeEstroPlugin")) {
		require_once dirname( __FILE__ ) . '/classes/PeEstroPlugin.php';
		
		$peEstroPlugin =& new PeEstroPlugin(__FILE__ );
		$peEstroPlugin =& $peEstroPlugin->frontend;
	}

	
?>