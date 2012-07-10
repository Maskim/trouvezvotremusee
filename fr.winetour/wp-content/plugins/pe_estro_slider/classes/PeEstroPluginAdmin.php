<?php

	// plugin admin class 
	class PeEstroPluginAdmin {
		
		var $plugin;
		var $slug;
		var $options;
		
		function PeEstroPluginAdmin(&$plugin) {
			$this->plugin =& $plugin;
			$this->init();
			$this->actions();
		}
		
		function init() {
			$this->slug = $this->plugin->name."-options";
			$this->options = get_option($this->slug);
			if (!isset($this->options) || $this->options == null) {
				$this->createDefaultOptions();
			}
		}
		
		function actions() {
		
			register_activation_hook($this->plugin->main,array(&$this, 'activate'));
			register_deactivation_hook($this->plugin->main,array(&$this, 'deactivate'));

			/*
			if (!$this->options) {
				add_action('admin_notices', array(&$this, 'notice') );
			}
			*/
			
			add_action('admin_init', array(&$this, 'adminInit'));
			add_action('admin_menu', array(&$this, 'createMenu'));
			add_action('wp_ajax_peOptionsSave', array(&$this, 'adminAjax') );
			add_action('wp_ajax_peThumbGet', array(&$this, 'adminAjax') );
			add_action('wp_ajax_peOptionsLoad', array(&$this, 'adminAjax') );
					
			add_filter("plugin_action_links",array(&$this, 'plugin_action_links'), 10, 2 );

		}
		
		function plugin_action_links($links, $file) {
			if (dirname($file) == $this->plugin->name) {
				 $links[] = '<a href="admin.php?page='.$this->slug.'">Settings</a>';
			}
			return $links;
		}
		
		function activate() {
		}
		
		function deactivate() {
		}
		
		function createDefaultOptions() {
			$default = new stdClass();
			$default->uniqID = 0;
			$default->sliders = Array();
			$default->global = new stdClass();
			$default->global->skin = "default";
			$default->global->advanced = "disabled";
			$default->global->lazyLoading = "disabled";
			$default->global->tooltip = "disabled";	
			
			$this->options =& $default;
			
			add_option($this->slug, $default, null, "no");
		}
		
		/*
		function notice() {
		    echo '<div class="updated" id="'.$this->slug.'_notice"><p></p></div>';
		}
		*/
		
		// add page to menu
		function createMenu() {
			$page = add_menu_page($this->plugin->title, $this->plugin->title, "manage_options", $this->slug, array(&$this, 'adminPage'));
			add_action("admin_print_scripts-$page", array(&$this, 'loadScripts'));
			add_action("admin_print_styles-$page", array(&$this, 'loadStyles'));
		}
		

		
		function loadScripts() {
			
			
			wp_enqueue_script(array("jquery", "jquery-ui-core", "interface", "jquery-ui-sortable", "wp-lists", "jquery-ui-sortable",'jquery-ui-tabs', 'jquery-ui-button','jquery-ui-dialog','json2','media-upload','thickbox'));
			
			wp_enqueue_script($this->slug."cookie", $this->plugin->url.'resources/js/jquery.c.min.js',array("jquery"),"1.0");
			wp_enqueue_script($this->slug."form", $this->plugin->url.'resources/js/form2object.min.js',array("jquery"),"1.0");
			wp_enqueue_script($this->slug."pupulate", $this->plugin->url.'resources/js/jquery.populate.min.js',array("jquery"),"1.0");
			//wp_enqueue_script($this->slug."storage", $this->plugin->url.'resources/js/jstorage.min.js',array("jquery"),"1.0");
			wp_enqueue_script($this->slug."scroll", $this->plugin->url.'resources/js/jquery.scrollTo-1.4.2-min.js',array("jquery"),"1.0");
			wp_enqueue_script($this->slug."qtip", $this->plugin->url.'resources/js/qtip/jquery.qtip.min.js',array("jquery"),"1.0");
			wp_enqueue_script($this->slug."utils", $this->plugin->url.'resources/js/jquery.pixelentity.utils.geom.min.js',array("jquery"),"1.0");
			
			wp_enqueue_script($this->slug."options", $this->plugin->url.'resources/js/admin.js',array("jquery-ui-sortable", "jquery-ui-sortable",'jquery-ui-tabs', 'jquery-ui-button','jquery-ui-dialog','json2','media-upload','thickbox'),"1.0");
					
			$this->plugin->loadScripts();
			
			$adminOptions["options"] =& $this->options;
    		$adminOptions["slug"] = $this->slug;
    		$adminOptions["nonce"] = wp_create_nonce( 'pe_admin_nonce');
    		$adminOptions["url"] = admin_url('admin-ajax.php');
    		$adminOptions["pluginUrl"] = $this->plugin->url;
    		
			echo str_replace("%value%", json_encode($adminOptions), '<script type="text/javascript">var pe_options = %value%; </script>');
			
		}
		
		function loadStyles() {
			wp_enqueue_style('thickbox');
			wp_enqueue_style($this->slug."qtip", $this->plugin->url.'resources/js/qtip/jquery.qtip.min.css',array(),"1.0");
			wp_enqueue_style($this->slug."options", $this->plugin->url.'resources/css/admin.css',array(),"1.0");
			
			$this->plugin->loadStyles();
		}

		function adminInit() {
		}

		function adminPage() {
			include ("{$this->plugin->path}/templates/options.tpl");
		}
		
		function adminAjax() {
			
			if (!current_user_can("manage_options") || !wp_verify_nonce( $_POST['pe_admin_nonce'], 'pe_admin_nonce' )) die ('NOT ALLOWED');
		 
		 	$_POST = array_map( 'stripslashes_deep', $_POST );
			
		 	$result = "";
			switch ($_POST["action"]) {
				case "peOptionsSave":
					$this->options = json_decode($_POST['options']);
					$result = update_option($this->slug, $this->options);
					$result = json_encode( array("options" => $this->options,"ok"=>true));
				break;
				case "peOptionsLoad":
					$result = json_encode( array("options" => $this->options,"ok"=>true));
				break;
				case "peThumbGet":
					$img = json_decode($_POST['img']);
					
					require ("{$this->plugin->path}/classes/PeUtilsImage.php");
					$utils = new PeUtilsImage();
					$thumb = $utils->getThumb($img,240,160,true);
					$result = json_encode( array("thumb" => $thumb,"ok"=>is_string($thumb)));
				break;
			}
			
			header( "Content-Type: application/json" );
			echo $result;
		
			exit;
		}

		
	}
?>
