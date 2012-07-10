<div class="wrap pe_admin">
	<div class="icon32" id="icon-options-general"></div>
	
	<div id="pe_dialog" title="Info"></div>
	<div id="pe_preview" title="Info"></div>
	
	<h2><?php echo $this->plugin->title; ?></h2>
	
	
	
		<div class="pe_tabs" id="pe_main" >
			<div class="pe_buttons_bar" id="pe_main_buttons_bar">
				<div class="pe_messages"> 
					<img id="pe_loading_spinner" src="<?php echo $this->plugin->url; ?>/resources/img/spinner.gif" />
					<span id="pe_message_text"></span>
				</div>
				<a class="pe_button" data-hide="pe_main_docs" rel="save" id="pe_main_save" data-help="Save current configuration">SAVE</a>
				<a class="pe_button" data-hide="pe_main_edit pe_main_docs" data-help="Create a new empty slider" rel="create">NEW SLIDER</a>
				<a class="pe_button" data-hide="pe_main_docs" data-show="pe_main_edit" data-help="Add a new black slide to current slider" rel="addSlide">ADD SLIDE</a>
				<a class="pe_button" data-hide="pe_main_docs" rel="previewCurrent" id="pe_main_preview" data-help="Show a preview of current slider">PREVIEW</a>
				<br>
			</div>
			<ul>
				<li><a href="#pe_main_general" >General</a></li>
				<li><a href="#pe_main_list" >Sliders</a></li>
				<li><a href="#pe_main_edit" >Edit Slider</a></li>
				<li><a href="#pe_main_docs">Documentation</a></li>
			</ul>
		
			<div class="clear"></div>
				<div id="pe_main_docs">
					<?php include($this->plugin->path."/docs/docs.html"); ?>
				</div>
				<div id="pe_main_general">
					<form id="pe_global_options" >
						
<table class="">
	<tbody>
		<tr>
			<td rowspan="10" class="pe_field_skin">
				<label for="pe_global_skin" id="pe_global_skin_label" data-help="Select Estro skin">Select Skin</label>
				<div class="pe_radio">
					<input type="radio" name="skin" id="pe_global_skin0" value="default" checked>
					<label for="pe_global_skin0">Pixelentity</label>
					<input type="radio" name="skin" id="pe_global_skin1" value="neutral" >
					<label for="pe_global_skin1">Neutral Dark</label>
					<input type="radio" name="skin" id="pe_global_skin2" value="neutral_light" >
					<label for="pe_global_skin2">Neutral Light</label>
					<input type="radio" name="skin" id="pe_global_skin3" value="organic" >
					<label for="pe_global_skin3">Organinc</label>
				</div>
				<div id="pe_global_skin_preview">
					<img id="pe_estro_skin_default" src="<?php echo $this->plugin->url; ?>/resources/shots/default.jpg" />
					<img id="pe_estro_skin_neutral" src="<?php echo $this->plugin->url; ?>/resources/shots/neutral_dark.jpg" />
					<img id="pe_estro_skin_neutral_light" src="<?php echo $this->plugin->url; ?>/resources/shots/neutral.jpg" />
					<img id="pe_estro_skin_organic" src="<?php echo $this->plugin->url; ?>/resources/shots/organic.jpg" />
				</div>

			</td>
			<td colspan="2" class="pe_field_skin_filler">&nbsp;</td>
		</tr>
		<tr>
			<td><label for="pe_global_width" data-help="Default slider size">Size</label></td>
			<td><input size="3" type="text" id="pe_global_width" name="width" placeholder="W" value="" />x<input type="text" size="3" id="pe_global_height" name="height" placeholder="H" value="" /></td>
		</tr>
		<tr>
			<td><label data-help="Show/hide advanced configuration options">Advanced options</label></td>
			<td>
				<div class="pe_radio">
				<input type="radio" name="advanced" id="pe_global_advanced0" value="enabled">
				<label for="pe_global_advanced0" >Show</label>
				<input type="radio" name="advanced" id="pe_global_advanced1" value="disabled" checked>
				<label for="pe_global_advanced1">Hide</label>
				</div>
			</td>
		</tr>
		<tr class="pe_advanced_section">
			<td><label for="pe_global_lazy" data-help="When lazy loading is active, each image will only be loaded when the relevant slide is about to be shown">Lazy loading</label></td>
			<td>
				<div class="pe_radio">
				<input type="radio" name="lazyLoading" id="pe_global_lazy0" value="enabled" >
				<label for="pe_global_lazy0">Enabled</label>
				<input type="radio" name="lazyLoading" id="pe_global_lazy1" value="disabled" checked>
				<label for="pe_global_lazy1">Disabled</label>
				</div>
			</td>
		</tr>
		<tr class="pe_advanced_section">
			<td><label for="pe_global_tooltip" data-help="Enable/Disable tooltips in admin area (requires page reload after change)">Tooltips</label></td>
			<td>
				<div class="pe_radio">
				<input type="radio" name="tooltip" id="pe_global_tooltip0" value="enabled" checked>
				<label for="pe_global_tooltip0">Enabled</label>
				<input type="radio" name="tooltip" id="pe_global_tooltip1" value="disabled">
				<label for="pe_global_tooltip1">Disabled</label>
				</div>
			</td>
		</tr>
		<tr class="pe_advanced_section">
			<td><label data-help="Select the tag to use for captions (SEO setting)">Caption Tag</label></td>
			<td>
				<select name="captionTag" >
					<option value="h1" selected>H1</option>
					<option value="h2">H2</option>
					<option value="h3">H3</option>
					<option value="h4">H4</option>
					<option value="p">P</option>
				</select>
			</td>
		</tr>
		

		<!--
		<tr class="pe_advanced_section">
			<td><label for="pe_global_shadow">Shadow</label></td>
			<td>
				<div class="pe_radio">
				<input type="radio" name="shadow" id="pe_global_shadow0" value="enabled" checked>
				<label for="pe_global_shadow0" >Enabled</label>
				<input type="radio" name="shadow" id="pe_global_shadow1" value="disabled" >
				<label for="pe_global_shadow1">Disabled</label>
				</div>
			</td>
		</tr>
		<tr class="pe_advanced_section">
			<td><label for="pe_global_logo">Logo tab</label></td>
			<td>
				<div class="pe_radio">
				<input type="radio" name="logo" id="pe_global_logo0" value="enabled" >
				<label for="pe_global_logo0">Enabled</label>
				<input type="radio" name="logo" id="pe_global_logo1" value="disabled" checked>
				<label for="pe_global_logo1">Disabled</label>
				</div>
			</td>
		</tr>
		-->
	</tbody>
</table>	
	
						
					</form>
				</div>
				
				<div id="pe_main_list">
				
<table id="pe_sliders" class="widefat">
	<thead>
		<tr>
			<th class="pe_field_id">ID</th>
			<th class="pe_field_icons"></th>
			<th class="pe_field_title">Title</th>
			<th class="pe_field_number">Slides</th>
		</tr>
	</thead>
	
	<tbody>
	</tbody>
</table>
				</div>
				<div id="pe_main_edit" >
			
<form id="pe_edit_form" >

<table class="widefat pe_options pe_advanced pe_collapse">

	<thead>
		<tr>
			<th class="pe_field_id">Slider option</th>
			<th colspan="5" class="ui-widget">
				<a rel="collapse" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-triangle-1-n"></span></a>
			</th>
		</tr>
	</thead>
 
<tbody class="pe_collapse_section">
<tr>
	<td><label for="pe_edit_id" data-help="Used to identify a slider to be displayed via shortcodes">ID</label></td>
	<td class="pe_field_multi"><input size="5" type="text" id="pe_edit_id" name="id" placeholder="ID" value="" /></td>
	<td class="pe_field_column_3"><label for="pe_edit_title" data-help="Slider title, used only in admin area">Title</label></td>
	<td class="pe_field_column_4"><input type="text" id="pe_edit_title" name="title" placeholder="TITLE" value="" /></td>
	<td><label for="pe_edit_type" data-help="Set slider working mode">Type</label></td>
	<td>
		<select id="pe_edit_type" name="type" >
			<option value="kb" selected>Ken Burns</option>
			<option value="swipe">Swipe</option>
			<!-- for future updates -->
			<!--
			<option value="grid">Grid</option>
			-->
		</select>
	</td>	
</tr>
<tr class="pe_advanced_section">
	<td><label for="pe_edit_controls" data-help="Controls behaviour and visibility of slider controls panel">Controls</label></td>
	<td>
		<select id="pe_edit_controls" name="controls" >
			<option value="always" selected>Always visible</option>
			<option value="inner">Inside frame</option>
			<option value="disabled">None</option>
			<option value="over">On mouse over</option>
			<option value="hideOnFirst">Hide on first slide</option>
		</select>
	</td>
	<td><label for="pe_edit_autopause" data-help="Pause/resume timer when mouse is over slider">Auto pause</label></td>
	<td>
		<select id="pe_edit_autopause" name="autopause" >
			<option value="image" selected>When mouse over image</option>
			<option value="controls">When mouse over controls</option>
			<option value="image,controls">When mouse over image or controls</option>
			<option value="none">Never</option>
		</select>
	</td>
	<td><label for="pe_edit_thumb" data-help="Controls slider button rollover thumbnail preview">Thumbnails</label></td>
	<td>
		<select id="pe_edit_thumb" name="thumb" >
			<option value="enabled" selected>With Keen Burns effect</option>
			<option value="fixed">No effect</option>
			<option value="disabled">Hidden</option>
		</select>
	</td>
	
</tr>
<tr class="pe_advanced_section">
	<td><label for="pe_edit_width" data-help="Slider size (leave empty to use global value)">Size</label></td>
	<td><input size="3" type="text" id="pe_edit_width" name="width" placeholder="W" value="" />x<input type="text" size="3" id="pe_edit_height" name="height" placeholder="H" value="" /></td>
	<td><label for="pe_edit_shadow" data-help="Controls shadow visibility">Shadow</label></td>
	<td>
		<div class="pe_radio">
		<input type="radio" name="shadow" id="pe_edit_shadow0" value="enabled" checked>
		<label for="pe_edit_shadow0" >Enabled</label>
		<input type="radio" name="shadow" id="pe_edit_shadow1" value="disabled" >
		<label for="pe_edit_shadow1">Disabled</label>
		</div>
	</td>
	<td><label for="pe_edit_skin" data-help="Choose a skin for this slider only">Skin</label></td>
	<td>
		<select id="pe_edit_skin" name="skin" >		
			<option value="" selected>Global</option>
			<option value="default">Pixelentity</option>
			<option value="neutral">Neutral Dark</option>
			<option value="neutral_light">Neutral Light</option>
			<option value="organic">Organic</option>
		</select>
	</td>
</tr>
<tr class="pe_advanced_section" class="pe_edit_logo_enabled">
	<td><label for="pe_edit_logo" data-help="Controls logo tab visibility">Logo tab</label></td>
	<td>
		<div class="pe_radio">
		<input type="radio" name="logo" id="pe_edit_logo0" value="enabled" >
		<label for="pe_edit_logo0">Enabled</label>
		<input type="radio" name="logo" id="pe_edit_logo1" value="disabled" checked>
		<label for="pe_edit_logo1">Disabled</label>
		</div>
	</td>
	<td><label for="pe_edit_logo_link" data-help="Controls the link attached to the logo tab">Logo link</label></td>
	<td><input type="text" id="pe_edit_logo_link" name="logo_link" placeholder="URL" value="" /></td>
	<td><label for="pe_edit_logo_target" data-help="Controls whether the logo link opens in a new tab/window or in the current tab/window">Logo target</label></td>
	<td >
		<div class="pe_radio">
		<input type="radio" name="logo_target" id="pe_edit_logo_target0" value="_self" checked>
		<label for="pe_edit_logo_target0">self</label>
		<input type="radio" name="logo_target" id="pe_edit_logo_target1" value="_blank" >
		<label for="pe_edit_logo_target1">blank</label>
		</div>
	</td>
</tr>
</tbody>

</table>
</form>

<div id="pe_slides" class="pe_sortable pe_related_type_kb">
<form>
<table class="widefat pe_options">
<tbody class="pe_collapse_section">
<tr >

	<td rowspan="5" class="pe_field_skin">
		<div class="pe_img_drag">
			<a rel="pos" href="#" data-help="Click to select the slide<br/>Click on other slide to swap position">1</a>
		</div>
		<div class="pe_img_preview">
			<a rel="img" href="#" data-help="Click op select an image using media uploader" >
			<img src="<?php echo $this->plugin->url; ?>/resources/img/blank.png" />
			</a>
		</div>
		<div class="pe_img_drag">
			<div class="pe_sortable_handler"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></div>
		</div>
	</td>
</tr>
<tr >
	<td class="pe_field_column_3">
		<a rel="add" class="ui-state-default ui-corner-all" data-help="Clone"><span rel="add" class="ui-icon ui-icon-plus"></span></a>
		<a rel="delete" class="ui-state-default ui-corner-all" data-help="Delete"><span rel="delete" class="ui-icon ui-icon-trash"></span></a>
	</td>
	<td colspan="3">
		<div class="pe_radio">
			<input type="radio" name="pe_multi_chooser" id="pe_multi_chooser_basic" value="basic" checked>
			<label for="pe_multi_chooser_basic" data-help="Basic slide options">BASIC</label>
			<input type="radio" name="pe_multi_chooser" id="pe_multi_chooser_advanced" value="advanced" >
			<label for="pe_multi_chooser_advanced" data-help="Advanced slide options">ADVANCED</label>
		</div>
	</td>
</tr>

<tr class="pe_multi_basic pe_multi_visible">
	<td><label data-help="Type the full url or click on the left box to select an image">Image</label></td>
	<td colspan="3"><input size="35" type="text" name="img" placeholder="URL" value="" class="widefat"/></td>
</tr>
<tr class="pe_multi_basic pe_multi_visible">
	<td><label data-help="Caption text">Caption</label></td>
	<td class="pe_field_column_4"><input size="35" type="text" name="caption" placeholder="TEXT" value="" /></td>
	<td><label data-help="Time (in seconds) before next slide is loaded">Delay</label></td>
	<td><input size="5" type="text" name="delay" placeholder="5" value="" /></td>
</tr>
<tr class="pe_multi_basic pe_multi_visible">
	<td><label data-help="Set slide link">Link</label></td>
	<td><input size="35" type="text" name="link_url" placeholder="URL" value="" /></td>
	<td><label data-help="Set slide link type">Link type</label></td>
	<td>
		<select name="link_type" >
			<option value="_self" selected>Page (same tab)</option>
			<option value="_blank">Page (new tab)</option>
			<option value="video">Video</option>
			<option value="lightbox">Lightbox</option>
		</select>
	</td>
</tr>
<tr class="pe_multi_advanced pe_related_type_kb pe_related_type_swipe pe_related_type_video">
	<td><label data-help="Set video playback options">Video</label></td>
	<td colspan="3">
		<div class="pe_radio">
			<input type="checkbox" name="video_hd" value="ok" id="pe_slide_video_hd"/><label data-help="Play the High Resolution version (if available)" for="pe_slide_video_hd">hd</label>
			<input type="checkbox" name="video_autostart" value="ok" id="pe_slide_video_autostart" /><label data-help="Start video playback as soon as slide is displayed" for="pe_slide_video_autostart">autostart</label>
			<input type="checkbox" name="video_loop" value="ok" id="pe_slide_video_loop" /><label data-help="Restart video once playback is finished" for="pe_slide_video_loop">loop</label>
			<input type="checkbox" name="video_skiptonext" value="ok" id="pe_slide_video_skiptonext"/><label data-help="When playback ends, jump to next slide (experimental)" for="pe_slide_video_skiptonext">next</label>			
		</div>
	</td>
</tr>
<tr class="pe_related_type_kb pe_multi_advanced">
	<td><label data-help="Ken burns zoom mode">Zoom</label></td>
	<td>
		<select name="zoom" >
			<option value="random" selected>Random</option>
			<option value="in">In</option>
			<option value="out">Out</option>
		</select>
	</td>
	<td><label data-help="Time required to complete the Keen Burns transition, in seconds (high value = slow transition)">Duration</label></td>
	<td><input size="5" type="text" name="duration" placeholder="15" value="" /></td>
</tr>
<tr class="pe_related_type_kb pe_multi_advanced">
	<td><label data-help="Image start position">Start position</label></td>
	<td>
		<select name="align" >
			<option value="random" selected>Random</option>
			<option value="top,left">top,left</option>
			<option value="top,center">top,center</option>
			<option value="top,right">top,right</option>
			<option value="center,left">center,left</option>
			<option value="center,center">center,center</option>
			<option value="center,right">center,right</option>
			<option value="bottom,left">bottom,left</option>
			<option value="bottom,center">bottom,center</option>
			<option value="bottom,right">bottom,right</option>
		</select>
	</td>
	<td><label data-help="Image end position">End position</label></td>
	<td>
		<select name="pan" >
			<option value="random" selected>Random</option>
			<option value="top,left">top,left</option>
			<option value="top,center">top,center</option>
			<option value="top,right">top,right</option>
			<option value="center,left">center,left</option>
			<option value="center,center">center,center</option>
			<option value="center,right">center,right</option>
			<option value="bottom,left">bottom,left</option>
			<option value="bottom,center">bottom,center</option>
			<option value="bottom,right">bottom,right</option>
		</select>
	</td>
</tr>
<tr class="pe_related_type_swipe pe_multi_advanced">
	<td><label data-help="Set transition type (in swipe mode)">Transition</label></td>
	<td>
		<select name="transition" >
			<option value="swipe" selected>Swipe</option>
			<option value="fade">Fade</option>
			<option value="flyBy">flyBy</option>
		</select>
	</td>
</tr>

</tbody>

</table>
</form>

</div>
				
				
				</div>
				
				
			</div>
		</div>
		
<div id="pe_preview_container">
	<div id="pe_preview_content">
	</div>
</div>