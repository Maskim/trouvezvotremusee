<?php
	// Get the How To feed from www.category-icons.com
	require_once("../../../wp-config.php"); 
	require_once (ABSPATH . WPINC . '/class-feed.php');
	
	$msg = stripslashes($_POST['error']);
	$url = "http://www.category-icons.com/category/howto/feed/";

	$feed = new SimplePie();
	$feed->set_feed_url($url);
	$feed->set_cache_class('WP_Feed_Cache');
	$feed->set_file_class('WP_SimplePie_File');
	$feed->set_cache_duration(apply_filters('wp_feed_cache_transient_lifetime', 43200));
	$feed->init();
	$feed->handle_content_type();

	if ( !$feed->error() ) {
		$msg = '';
		foreach ($feed->get_items() as $item) {
			$msg .= sprintf('<p><a href="%s">%s</a><br/>',$item->get_permalink(), $item->get_title());
			$msg .= sprintf('<small>%s</small></p>',$item->get_date('j F Y | g:i a'));
		}
	}
	
	die ($msg);
?>