<?php

	// plugin frontend class 
	class PeEstroPluginFrontend {
		
		var $plugin;
		var $slug;
		var $options;
		
		function PeEstroPluginFrontend(&$plugin) {
			$this->plugin =& $plugin;
			$this->init();
		}
		
		function init() {
			$this->slug = $this->plugin->name;
			$this->options = get_option($this->slug."-options");
			$this->shortcodes();
			add_action('template_redirect', array(&$this, 'loadResources'));
		}
		
		function loadResources() {
			$this->plugin->loadScripts();
			$this->plugin->loadStyles();
		}
		
		function shortcodes() {
			add_shortcode($this->slug, array(&$this, "shortcode_slider"));
		}
		
		
		function filter_markup($value) {
			return preg_replace;
		}
		
		function shortcode_slider($atts) {
			extract( shortcode_atts( array(
				'id' => '0'
			), $atts ) );
		
			return $this->getSlider($id);
		}
		
		function getSliderID($id) {
			if (!$id || !$this->options || !$this->options->sliders ) return false;
			
			$n = count($this->options->sliders);
			
			for ($i=0;$i<$n;$i++) {
				if ($this->options->sliders[$i]->id == $id) return $this->options->sliders[$i];
			}
			return false;
		}
		
		function the_slider($id) {
			echo $this->getSlider($id);
		}
		
		function getSlider($id) {
			
			if (!($slider =& $this->getSliderID($id))) return "";
			
			$gO =& $this->options->global;
			
			$w = intval($slider->width);
			$h = intval($slider->height); 
			$w = $w > 0 ? $w : intval($gO->width);
			$h = $h > 0 ? $h : intval($gO->height);
			$w = $w > 0 ? $w : 940;
			$h = $h > 0 ? $h : 400;
			
			$captionTag = $gO->captionTag ? $gO->captionTag : "h1";
			
			$blank = $this->plugin->url."resources/img/blank.png";
			
			
			$sliderHtml = sprintf('<div id="%s_%s" class="peKenBurns %s" data-autopause="%s" data-controls="%s" data-logo="%s" data-logo-target="%s" data-logo-link="%s" data-shadow="%s" data-thumb="%s" data-mode="%s" style="width: %spx; height: %spx;">',
					$this->slug,
					$id,
					isset($slider->skin) ? $slider->skin : $gO->skin ,
					$slider->autopause,
					$slider->controls,
					$slider->logo,
					$slider->logo_target,
					$slider->logo_link,
					$slider->shadow,
					$slider->thumb,
					$slider->type,
					$w,
					$h
				);
			
			foreach ($slider->slides as $slide) {
				if (!$slide->img) continue;
				
				$sliderHtml .= sprintf('<div data-align="%s" data-delay="%s" data-thumb="%s" data-duration="%s" data-pan="%s" data-transition="%s" data-zoom="%s">',
					$slide->align,
					isset($slide->delay) ? intval($slide->delay) : 5,
					preg_replace("/\.(\w+)$/","-240x160.$1",$slide->img),
					isset($slide->duration) ? intval($slide->duration) : 15,
					$slide->pan,
					$slide->transition,
					$slide->zoom
				);
				
				if ($slide->link_url) {
					if ($slide->link_type == "video") {
						$vClasses = Array();
						//if ($slide->video_autoplay) $vClasses[] = "autoplay";
						// autoplay always enabled
						$vClasses[] = "autoplay";
						if ($slide->video_autostart) $vClasses[] = "autostart";
						if ($slide->video_hd) $vClasses[] = "hd";
						if ($slide->video_loop) $vClasses[] = "loop";
						if ($slide->video_skiptonext) $vClasses[] = "skiptonext";
						$sliderHtml .= sprintf('<a class="video %s" href="%s">',
							join(" ",$vClasses),
							$slide->link_url
						);
					} else {
						$sliderHtml .= sprintf('<a %s %s href="%s" >',
							$slide->link_type == "_blank" ? 'target="_blank"' : '',
							$slide->link_type == "lightbox" ? 'data-rel="lightbox-slide" ' : '',
							$slide->link_url
						);
					
					}
				}
				if ($gO->lazyLoading) {
					$sliderHtml .= sprintf('<img src="%s" data-src="%s" />',$blank,$slide->img);
				} else {
					$sliderHtml .= sprintf('<img src="%s" />',$slide->img);
				}
				if ($slide->link_url) {
					$sliderHtml .= "</a>";
				}
				if ($slide->caption) {
					$sliderHtml .= sprintf('<%s>%s</%s>',$captionTag,$slide->caption,$captionTag);
				}
				$sliderHtml .= '</div>';
				
			}
			
			$sliderHtml .= '</div>';
			
			$sliderHtml = apply_filters($this->slug."_markup",$sliderHtml);
			
			$id = $this->slug."_".$id;
			$sliderHtml .= sprintf('<script>%s
				jQuery("#%s").peKenburnsSlider();
			</script>',
				has_filter($this->slug."_customJS") ? apply_filters($this->slug."_customJS","#$id") : "",
				$id
			);
			return $sliderHtml;
		}
		
	}
?>