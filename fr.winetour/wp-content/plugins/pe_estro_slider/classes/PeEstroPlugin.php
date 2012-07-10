<?php

	// main plugin class 
	class PeEstroPlugin {
		
		var $main;
		var $title = "Estro Slider";
		var $name = "";
		var $url = "";
		var $path = "";
		var $frontend;
		
		function PeEstroPlugin($main) {
			$this->main = $main;
			$this->init();
		}
		
		function init() {
			$this->path = dirname(dirname(__FILE__));
			$this->name = basename($this->path);
			$this->url = plugins_url("/{$this->name}/");
			
			if (!load_plugin_textdomain($this->name,'/wp-content/languages/')) {
				load_plugin_textdomain($this->name, null, "{$this->path}/lang");
			}
			
			$pClass = basename(__FILE__,".php"); 
			
			if ( is_admin() ) {
				$pAdminClass = "{$pClass}Admin";
				include "{$this->path}/classes/{$pAdminClass}.php";	
				
				$admin = new $pAdminClass($this);
				
				
			} else {
				$pFrontendClass = "{$pClass}Frontend";
				include "{$this->path}/classes/{$pFrontendClass}.php";	
				
				$this->frontend = new $pFrontendClass($this);
			}
		}
		
		function loadScripts() {
			wp_enqueue_script($this->name."_script", $this->url.'resources/pe.kenburns/jquery.pixelentity.kenburnsSlider.min.js',array("jquery"),"1.0",false);
		}
		
		function loadStyles() {
			wp_enqueue_style($this->name."_style", $this->url.'resources/pe.kenburns/themes/allskins.min.css',array(),"1.0");
		}
		
	}
?>