jQuery(document).ready(function($) {


	function Admin() {
		var self = this;
		var popup,popupCallBack,popupCallBackArgs;
		var preview;
		var options = pe_options.options;
		var pluginUrl = pe_options.pluginUrl;
		var ajaxUrl = pe_options.url;
		var ajaxNonce = pe_options.nonce;
		var slug = pe_options.slug;
		var editForm = $("#pe_edit_form").hide();
		var globalForm = $("#pe_global_options");
		var slideForm;
		var slides;
		var shortcode;
		var selected;
		var main = $("#pe_main");
		var barTop = true;
		var lastEditedID = "";
		var lastEditedValues;
		var thumbQueue = [];
		var thumbQueueRunning = false;
		var thumbCurrent;
		var dirty = false;
		var locked = false;
		var clearMessage = 0;
		
		var toolTipConf = {
			content: {
				attr: 'data-help'
			},
			position: {
				at: 'top center', 
				my: 'bottom center'
			},
			style: {
				classes: 'ui-tooltip-rounded ui-tooltip-shadow ui-tooltip-tipsy'
			}
		};
		
		
		function buildSliderRow(slider) {
			var shortcodeHelp = 'Get shortcode to display in post/page';
			var editHelp = 'Edit';
			var deleteHelp = 'Delete';
			var cloneHelp = 'Duplicate';
			var previewHelp = 'Preview';
			return ''+
'<tr id="row?">'.format(slider.id)+
	'<td>?</td>'.format(slider.id)+
	'<td class="ui-widget">'+
		'<a rel="shortcode" data-help="'+shortcodeHelp+'" class="ui-state-default ui-corner-all" id="'+slider.id+'"><span class="ui-icon ui-icon-link"></span></a>'+
		'<a rel="preview" data-help="'+previewHelp+'" class="ui-state-default ui-corner-all" id="'+slider.id+'"><span class="ui-icon ui-icon-zoomin"></span></a>'+
		'<a rel="edit"   data-help="'+editHelp+'" class="ui-state-default ui-corner-all" id="'+slider.id+'"><span class="ui-icon ui-icon-pencil"></span></a>'+
		'<a rel="clone" data-help="'+cloneHelp+'" class="ui-state-default ui-corner-all" id="'+slider.id+'"><span class="ui-icon ui-icon-plus"></span></a>'+
		'<a rel="delete" data-help="'+deleteHelp+'" class="ui-state-default ui-corner-all" id="'+slider.id+'"><span class="ui-icon ui-icon-trash"></span></a>'+
	'</td>'+
	'<td><span class="pe_inline_edit">'+slider.title+'</span></td>'+
	'<td>'+slider.slides.length+'</td>'+
'</tr>';
		}
		
		function slidersList() {
			// clean the list
			sliders.empty();
			
			$.each(options.sliders,function(id, slider) { 
				sliders.append(buildSliderRow(slider));
			});
			
			if (lastEditedID) {
				sliders.find("tr").removeClass("pe_selected").end().find("tr#row"+lastEditedID).addClass("pe_selected").end();
			}
			
			if (options.global.tooltip != "disabled") {	
				sliders.find('a[data-help]').qtip(toolTipConf);
			}
		}
		
		function getSliderIndexFromID(id) {
			var sl = options.sliders;
			var i = sl.length;
			
			while (i--) {
				if (sl[i].id == id) { 
					break; 
				}
			}
			return i;
		}
		
		function getSliderFromID(id) {
			return options.sliders[getSliderIndexFromID(id)];
		}
		
		function actionEditSlider(id) {
			if (lastEditedID) {
				if (id != lastEditedID) {
					if (JSON.stringify(getSliderOptions()) != lastEditedValues) {
						confirmAction(getSliderFromID(lastEditedID).title+" changes were not saved<br>Discard them ?",actionDiscardChanges,id);
						return;						
					}
				}
			}
			
			
			if (id != lastEditedID) {
				var slider = getSliderFromID(id);
				
				if (!slider) {
					disableEdit();
					return;
				}
				lastEditedID = id;
				$.cookie(slug+"edit",id);
				
				editForm.populate(slider).show().find(".pe_collapse_section").show();
				
				sliders.find("tr").removeClass("pe_selected").end().find("tr#row"+id).addClass("pe_selected").end();
				
				
				main.tabs("enable","pe_main_edit").tabs("select","pe_main_edit");
				slides.empty();
				$.each(slider.slides,loadSlide);
				editForm.find("select")
					.trigger("change")
				.end();
				slider.uniqID = slider.slides.length;		
				lastEditedValues = JSON.stringify(getSliderOptions());
			} else {
				main.tabs("enable","pe_main_edit").tabs("select","pe_main_edit");
			}
			$("#pe_main_preview").show();
		}
		
		function actionCloneSlider(id) {
			var newSlider = $.extend(true, {},getSliderFromID(id)); 
			newSlider.id = ++options.uniqID;
			newSlider.title += " (copy)";
			actionCreateSlider(newSlider);
		}
		
		function actionCreateSlider(slide) {
			var newSlider = slide || {id:++options.uniqID,title:"Slider "+options.uniqID,"slides":[]};
			options.sliders.push(newSlider);
			unsaved(true);
			sliders.append(buildSliderRow(newSlider));
			if (options.global.tooltip != "disabled") {
				sliders.find('#row'+newSlider.id+" a[data-help]").qtip(toolTipConf);
			}
			actionEditSlider(newSlider.id);
		}
		
		function disableEdit() {
			editForm.hide();
			lastEditedID = undefined;
			$("#pe_main_preview").hide();
			main.tabs("disable","pe_main_edit");
		}
		
		function actionDeleteSlider(id) {
			options.sliders.splice(getSliderIndexFromID(id),1);
			unsaved(true);
			if (lastEditedID == id ) {
				disableEdit();
			}
			sliders.find("tr#row"+id).remove();
		} 
		
		function actionPreviewSlider(id) {
			
			var gO = form2object('pe_global_options');
			var slider = (id !== undefined) ? getSliderFromID(id) : getSliderOptions();
			
			var w = parseInt(slider.width || gO.width || 920,10);
			var h = parseInt(slider.height || gO.height || 400,10);
			
			var blank = pluginUrl+"resources/img/blank.png";
			var captionTag = gO.captionTag || "h1";
			
			var sliderHtml = '<div class="peKenBurns ?" data-autopause="?" data-controls="?" data-logo="?" data-logo-target="?" data-logo-link="?" data-shadow="?" data-thumb="?" data-mode="?" style="width: ?px; height: ?px; margin-left: ?px">'.format(
					slider.skin || gO.skin,
					slider.autopause,
					slider.controls,
					slider.logo,
					slider.logo_target,
					slider.logo_link,
					slider.shadow,
					slider.thumb,
					slider.type,
					w,
					h,
					slider.logo == "disabled" ? 0 : 30
				);
			
			$.each(slider.slides,function(id, slide) { 
				// filter imageless slides
				if (!slide.img) {
					return;
				}
				
				sliderHtml += '<div data-align="?" data-delay="?" data-thumb="?" data-duration="?" data-pan="?" data-transition="?" data-zoom="?">'.format(
					slide.align,
					slide.delay !== undefined ? parseInt(slide.delay,10) : 5,
					slide.img.replace(/\.(\w+)$/,"-240x160.$1"),
					slide.duration !== undefined ? parseInt(slide.duration,10) : 15,
					slide.pan,
					slide.transition,
					slide.zoom
				);
				if (slide.link_url) {
					if (slide.link_type == "video") {
						var vClasses = ["video"];
						// if (slide.video_autoplay) { vClasses.push("autoplay"); }
						// autoplay always enabled
						vClasses.push("autoplay");
						if (slide.video_autostart) { vClasses.push("autostart"); }
						if (slide.video_hd) { vClasses.push("hd"); }
						if (slide.video_loop) { vClasses.push("loop"); }
						if (slide.video_skiptonext) { vClasses.push("skiptonext"); }
						sliderHtml += '<a class="?" href="?">'.format(
							vClasses.join(" "),
							slide.link_url
						);
					} else {
						sliderHtml += '<a ? ? href="?" >'.format(
							slide.link_type == "_blank" ? 'target="_blank"' : '',
							//slide.link_type == "lightbox" ? 'data-rel="lightbox" rel="lightbox"' : '',
							'',
							slide.link_url
						);
					
					}
				}
				if (gO.lazyLoading) {
					sliderHtml += '<img src="?" data-src="?" />'.format(blank,slide.img);
				} else {
					sliderHtml += '<img src="?" />'.format(slide.img);
				}
				if (slide.link_url) {
					sliderHtml += "</a>";
				}
				if (slide.caption) {
					sliderHtml += '<?>?</?>'.format(captionTag,slide.caption,captionTag);
				}
				sliderHtml += '</div>';
			});
			
			sliderHtml += '</div>';
			
			preview
				.dialog("option","width",w+30+ (slider.logo == "disabled" ? 0 : 30))
				.dialog("option","height",h+100)
				.dialog("option","title",slider.title)
				.html(sliderHtml)
				.dialog("open")
				.find(".peKenBurns")
					.peKenburnsSlider()
				.end();

		}
		
		function actionDiscardChanges(id) {
			lastEditedValues = undefined;
			lastEditedID = false;
			actionEditSlider(id);
		}
		
		function showShortCode(parent,value) {
			hideShortCode();
			shortcode.val(value);
			shortcode.width(parent.width());
			parent.find("a").hide().end().prepend(shortcode);
			shortcode.focus().select();
			
		}
		
		function hideShortCode() {
			var parent = shortcode.parent();
			shortcode.detach();
			parent.find("a").show().end();
		}
		
		function actionSave() {
			$("#pe_main_save").button('disable');
			message("Saving configuration ....","",true,true);
			hideShortCode();
			save();
		}
		
		function message(msg,cls,spinner,persist) {
			$("#pe_message_text").removeClass("ok warn").addClass(cls).html(msg);
			$("#pe_loading_spinner")[spinner ? "show" : "hide"]();
			clearTimeout(clearMessage);
			if (!persist) {
				clearMessage = setTimeout(defaultMessage,3000);
			}
		}
		
		function defaultMessage() {
			if (!unsaved()) {
				message("","",false,true);
			}
		}
		
		function getSliderOptions() {
			if (!lastEditedID) {
				return undefined;
			}
			var slider = form2object(editForm[0]);
			slider.slides = $("#pe_slides form").map(function () {
				return form2object(this);
			}).get() ;
			slider.uniqID = getSliderFromID(lastEditedID).uniqID;
			return slider;
		}
		
		function getAllOptions() {
			var opt = $.extend(true, {},options);
			opt.global = form2object('pe_global_options');
			
			if (lastEditedID) {
				opt.sliders[getSliderIndexFromID(lastEditedID)] = getSliderOptions();
			}
			
			return opt;
			
		}
		
		function save() {
			options = getAllOptions();
						
			jQuery.post(
				ajaxUrl,
				{
					action : 'peOptionsSave',
					options : JSON.stringify(options),
					pe_admin_nonce : ajaxNonce
				},
				saved
			);
			
		}
		
		function saved(response) {
			if (response && response.ok) {
				message("Configuration saved","ok");
				dirty = false;
				lastEditedValues = JSON.stringify(getSliderOptions());
			} else {
				alert(response);
				message("Error while saving data","warn");
			}
			$("#pe_main_save").button('enable');
			slidersList();
		}
		
		
		
		function slidersHandler(e) {
			if (locked) {
				return false;
			}
			var id = e.currentTarget.id;
			var action = e.currentTarget.rel;
			switch (action) {
				case "advanced":
					$(e.currentTarget).closest(".pe_advanced").find(".pe_advanced_section").toggle(); 
				break;
				case "collapse":
					$(e.currentTarget).closest(".pe_collapse").find(".pe_collapse_section").toggle(); 
				break;
				case "create":
					actionCreateSlider();
				break;
				case "delete":
					confirmAction("Delete "+getSliderFromID(id).title,actionDeleteSlider,id);
				break;
				case "shortcode":
					showShortCode($(e.currentTarget).parent(),'[pe_estro_slider id="?"]'.format(id));
					main.one("click",function () {
						hideShortCode();
					});
				break;
				case "save":
					actionSave();
				break;
				case "edit":
					actionEditSlider(id);
				break;
				case "clone":
					actionCloneSlider(id);
				break;
				case "addSlide":
					addSlide();
				break;
				case "previewCurrent":
					actionPreviewSlider();
				break;
				case "preview":
					actionPreviewSlider(id);
				break;
			}
			return false;
		}
		
		function loadSlide(id,data) {
			addSlide(data,id,true);
		}
		
		function addSlide(data,id,noAnimation,after) {
			if (id == null) {
				id = getSliderFromID(lastEditedID).uniqID++;
			}
			var slide = slideForm.clone();
			if (data != null) {
				slide.populate(data);
			} else {
				slide.find("input[name=img]").val("");
			}
			slide
				.find(".pe_radio")
					.find("input")
						.each(function () {
							$(this).attr("id",$(this).attr("id")+id);
						})
					.end()
					.find("label")
						.each(function () {
							$(this).attr("for",$(this).attr("for")+id);
						})
					.end()
					.buttonset()
				.end()
				.find("a[rel=pos]")
					.text(++id)
				.end();
			if (after) {
				after.after(slide);
			} else {
				slides.append(slide);
			}
			slide
				.find("input:checked, select, input[name=img]")
					.trigger("change")
				.end();
			if (options.global.tooltip != "disabled") {
				slide.find('a[data-help],label[data-help]')
					.qtip(toolTipConf)
				.end();
			}
			if (!noAnimation) {
				slide.find("table").css("background-color","#FFF4B8").animate({ backgroundColor: "white" }, 500);
				$.scrollTo(slide,300);

			}
		}
		
		function actionDeleteSlide(el) {
			unsaved(true);
			el.slideUp(350,"peAdmin",function() { 
				el.remove(); 
			});
		} 
		
		function showActiveMultiSection(el) {
			var jqEl = $(el);
			var value = jqEl.val();
			var parent = jqEl.closest("table");
			parent
				.find(".pe_multi_visible")
					.removeClass("pe_multi_visible")
				.end()
				.find(".pe_multi_"+value)
					.addClass("pe_multi_visible")
				.end();
				
				
		}
		
		function changeTypeClass(options,el) {
			var prefix = "pe_related_type_";
			options = $(options);
			var selected = prefix+options.val();
			var classes = options.find("option").map(function() {
				return this.value;
			}).get();
			
			$.each(classes,function(idx,value) {
				slides.removeClass(prefix+value);
			});
			
			slides.addClass(selected);
		}
		
		function now() {
			return (new Date()).getTime();
		}
		
		function changeImage(field,force) {
			
			field = $(field).removeClass("pe_error");
			
			var url = field.val();
			if (!url) {
				return;
			}
			
			
			var img = $("<img>");
			var spinner = $("");
			var thumb;
			var original = false;
			
			if (field.data("error")) {
				thumb = url;
				field.data("error",false);
				original = true;
			
			} else {
				thumb = url.replace(/\.(\w+)$/,"-240x160.$1");
			}
			
			if (field.data("cached")) {
				thumb += "?t="+now(); 
				field.data("cached",false);
			}
			
			//if (thumb ==  field.data("lastValue") && (now() - field.data("lastChange") < 500) ) {
			if (thumb == field.data("lastValue") && !force) {
				return;
			} 
			
			field.data("lastChange",now());
			field.data("lastValue",thumb);
			
			var replace = field.closest("table").find("a[rel=img]").addClass("loading").find("img");
			
			img.one("load",{replace:replace},imgresize);
			img.one("error",{"url":url,"field":field},original ? imageError : queueThumb);	
			img.attr("src",thumb);
			
			if (img.get(0).ready) {
				img.trigger("load");
			}
		}
		
		function imageError(e) {
			var thumbCurrent = e.data;
			var name = thumbCurrent.url.replace(/.*\//,"");
			message("Error loading image : "+name,"warn");
			thumbCurrent.field.addClass("pe_error");
			thumbCurrent.field.closest("table").find("a[rel=img]").removeClass("loading").find("img").hide();
			$(e.target).remove();
		}
		
		function thumbReady(response) {
			thumbQueueRunning = false;
			var name = thumbCurrent.url.replace(/.*\//,"");
			if (response.ok) {
				message("Thumb created : "+name,"ok");
				thumbCurrent.field.data("cached",true).trigger("change");
			} else {
				message("Error creating thumb : "+name,"warn");
				thumbCurrent.field.data("error",true).trigger("change");
			}
			thumbCurrent = null;
			//setTimeout(createQueuedThumb,1000)
			createQueuedThumb();
		}
		
		function queueThumb(e) {
			$(e.target).remove();
			thumbQueue.push({url:e.data.url,field:e.data.field});
			if (!thumbQueueRunning) {
				createQueuedThumb();
			}
		}
		
		function createQueuedThumb(e) {
			if (thumbQueue.length === 0) {
				return;
			}
			thumbQueueRunning = true;
			thumbCurrent = thumbQueue.shift();
			
			message("Creating thumb : "+thumbCurrent.url.replace(/.*\//,""),"",true,true);
			
			$.ajax({
			  type: 'POST',
			  url: ajaxUrl,
			  data: {
					'action': 'peThumbGet',
					'img' : JSON.stringify(thumbCurrent.url),
					'pe_admin_nonce' : ajaxNonce
				},
			  success: thumbReady,
			  error: function(jqXHR, textStatus, errorThrown) { alert(textStatus); alert(errorThrown); }
			});
			
		}
		
		function imgresize(e) {
			var img = $(e.currentTarget);
			
			e.data.replace.replaceWith(img);
			e.data.replace = null;
			
			var w = img.width();
			var h = img.height();
			var p = img.parent();
			
			var scaler = $.pixelentity.Geom.getScaler("fillmax","center","center",p.width(),p.height(),w,h);
			img.width(w*scaler.ratio);
			img.height(h*scaler.ratio);
			img.css("margin-left",scaler.offset.w);
			img.css("margin-top",scaler.offset.h);
			img.fadeTo(500,1);
			
			img.closest("table").find("a[rel=img]").removeClass("loading");
			
		}
		
		function changeVideoOptions(option) {
			option = $(option);
			option.closest("table").find(".pe_related_type_video")[option.val() == "video" ? "removeClass" : "addClass"]("pe_multi_disabled");
		}
		
		function slidesHandler(e,force) {
			if (locked && e.type != "change") {
				return true;
			}
			var id = e.currentTarget.id;
			var retValue = false;
			switch (e.type) {
				case "change":
					switch (e.target.name) {
						case "pe_multi_chooser":
							showActiveMultiSection(e.target);
						break;	
						case "type":
							changeTypeClass(e.target,slides);
						break;
						case "link_type":
							changeVideoOptions(e.target);
						break;
						case "img":
							changeImage(e.target);
						break;
					}
					retValue=true;
				break;
				case "click":
					var action = e.currentTarget.rel;
					var table;
					switch (action) {
						case "add":
							var current = $(e.currentTarget).closest("form");
							addSlide(form2object(current.get(0)),null,false,current);
							unsaved(true);
						break;
						case "pos":
							table = $(e.currentTarget).closest("table");
							if (table.hasClass("pe_selected")) {
								table.removeClass("pe_selected");
								selected = false;
							} else {
								if (selected) {
									
									var oldPos = slides.find("form").index(selected.parent());
									var newPos = slides.find("form").index(table.parent());
									selected.removeClass("pe_selected").css("background-color","#FFF4B8").animate({ backgroundColor: "white" }, 500);
									selected.parent()[newPos > oldPos ? "insertAfter" : "insertBefore"](table.parent());
									selected = false;
									unsaved(true);
								} else {
									selected = table.addClass("pe_selected");
								}
							}
						break;
						case "delete":
							var el = $(e.currentTarget).closest("form");
							table = el.find("> table").addClass("pe_selected");
							popup.one("abort", {selected:table} ,function(e) {
								e.data.selected.removeClass("pe_selected");
								selected = false;
							});
							confirmAction("Delete slide ",actionDeleteSlide,el);
						break;
						case "img":
							var field = $(e.currentTarget).closest("table").find("input[name=img]");
							tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
							// prevent "unload" event from firing when tb_window is closed 
							$("#TB_window,#TB_overlay,#TB_HideSelect").one("unload",killTheDamnUnloadEvent);
							window.send_to_editor = function(html) {
								tb_remove();
								field.val(jQuery('img',html).attr('src')).trigger("change");
							};
							
						break;
					}
				break;
			}
			
			return retValue;
		}
		
		function killTheDamnUnloadEvent(e) {
			e.stopPropagation();
			e.stopImmediatePropagation();
			return false;
		}
		
		function hoverHandler(e) {
			$(e.currentTarget)[e.type == "mouseenter" ? "addClass" : "removeClass"]('ui-state-hover');
		}
		
		function scrollHandler(e) {
			var wTop = $(window).scrollTop();
			var wBottom = wTop + $(window).height();
			var nav = main.find("> ul");
			
			var eTop = nav.offset().top;
			var eBottom = eTop + nav.height();
			
			var visible = ((eBottom >= wTop) && (wTop <= wBottom));
			
			var bar = $("#pe_main_buttons_bar");
				
			if (!visible) {
				if (barTop) {
					bar.parent().append(bar);
					barTop = false;
				}
			} else {
				if (!barTop) {
					bar.parent().prepend(bar);
					barTop = true;
				}
			}

		}
		
		
		function tabHandler(e,ui) {
			var id = ui.panel.id;
			var bar = $("#pe_main_buttons_bar");
			bar
				.find("a")
					.show()
					.filter("a[data-hide~="+id+"]")
						.hide()
					.end()
					.filter("a[data-show]")
						.hide()
					.end()
					.filter("a[data-show~="+id+"]")
						.show()
					.end()
				.end();
			$("#"+id).prepend(bar);
			
			if (options.global.tooltip != "disabled") {
				$(".ui-tooltip").hide();
			}
		}
		
		function globalHandler(e) {
			var skin = globalForm.find("input[name=skin]:checked").val();
			if (!$("#pe_estro_skin_"+skin).hasClass("pe_active")) {
				$("#pe_global_skin_preview img").removeClass("pe_last_active");
				$("#pe_global_skin_preview img.pe_active").addClass("pe_last_active").removeClass("pe_active");
				$("#pe_estro_skin_"+skin).stop().fadeTo(0,0).addClass("pe_active").fadeTo(300,1);
			}
			
			actionShowAdvanced(globalForm.find("input[name=advanced]:checked").val() == "enabled");
			
			return false;
		}
		
		function confirmAction(message,callback) {
			popup.html('<p>'+message+'</p>');
			popup.dialog("open");
			popupCallBack = callback;
			popupCallBackArgs = Array.prototype.slice.call(arguments);
			popupCallBackArgs.shift();
			popupCallBackArgs.shift();
			
		}
		
		function actionShowAdvanced(show) {
			main.find(".pe_advanced_section")[show ? "show" : "hide"]();
		}
		
		function doConfirmedAction() {
			popup.data("confirmed",true);
			dialogClose();
			if (popupCallBack) {
				popupCallBack.apply(this,popupCallBackArgs);
				popupCallBack = undefined;
				popupCallBackArgs = undefined;
			}
		} 
		
		function dialogAbort() {
			if (!popup.data("confirmed")) {
				popup.trigger("abort");
				popup.data("confirmed",false);
			}
		}
		
		function dialogClose() {
			popup.dialog("close");
		}
		
		function dialogAbortAndClose() {
			dialogAbort();
			dialogClose();
		}
		
		function unsaved(isDirty) {
			if (isDirty) {
				dirty = true;
			}
			var unsavedData = (dirty || JSON.stringify(getAllOptions()) != JSON.stringify(options));
			message("You have unsaved changes","warn",false,true);
			return unsavedData;
		}
		
		function beforeUnload(e) {
			if (unsaved()) {
				if (confirm("You have unsaved changes, do you want to save them ?")) {
					$.ajaxSetup({async:false});
					$.cookie(slug+"reload",true);	
					save();
				}
			}
			return true;
		}
		
		function load() {
			message("Loading configuration ....","",true,true);
			locked = true;
			jQuery.post(
				ajaxUrl,
				{
					action : 'peOptionsLoad',
					pe_admin_nonce : ajaxNonce
				},
				loaded
			);
		}
		
		function loaded(result) {
			message("Configuration loaded","ok");
			options = result.options;
			$.cookie(slug+"reload",null);
			addMissingConfValues();
			
			globalForm.populate(options.global);
			globalHandler();
			
			refreshSliderList();
			locked = false;
			
		}
		
		function addMissingConfValues() {
			
			if (!options) {
				options = {};
			}
			
			if (options.uniqID === undefined) {
				options.uniqID = 0;
			} 
			
			if (options.global === undefined) {
				options.global = {};
			}
			
			if (options.sliders === undefined) {
				options.sliders = [];
			}
		}
		
		
		function refreshSliderList() {
			
			slidersList();
			dirty = false;
				
			if (main.find("ul:first li a").eq(main.tabs("option","selected")).attr("href") == "#pe_main_edit") {
				var id = $.cookie(slug+"edit");
				if (id) {
					actionEditSlider(id);
				} else {
					disableEdit();
				}
			} else {
				disableEdit();
			}
		}
		
		function keyHandler(e) {
			if ((e.which ? e.which : e.keyCode) == 13) {
				setTimeout(function () { $(e.target).trigger("change",[true]); },100);
			}
		}
		
		function disableSubmit(e) {
			return false;
		}
		
		function closePreview() {
			preview.find(".peKenBurns").data("peKenburnsSlider").destroy();
			preview.empty();
		}
		
		return {
			init: function() {
			
				if ($.browser.msie && $.browser.version <= 7 ) {
					main.addClass("ie7");
				}
				
				slides = $("#pe_slides");
				
				slideForm = $("#pe_slides form").detach();
				
				
				slides
					.find("a")
						.live("click",slidesHandler)
						.live("hover",hoverHandler,hoverHandler)
					.end()
					.bind("change",slidesHandler)
					.bind("submit",disableSubmit)
					.bind("keypress",keyHandler);
					
				$(".pe_admin")
					.find(".pe_tabs")
						.show()
						.tabs({show:tabHandler,cookie: { name:slug+"tab",expires: 30 }})
					.end()
					.find(".pe_button")
						.button()
					.end()
					.find(".pe_radio")
						.buttonset()
					.end()
					.find(".pe_buttons_bar a")
						.bind("click",slidersHandler)
					.end()
					.find("#pe_edit_form a")
						.bind("click",slidersHandler)
						.bind("mouseenter",hoverHandler)
						.bind("mouseleave",hoverHandler)
					.end();
			
			
				globalForm.bind("submit",globalHandler).bind("change",globalHandler);
				editForm.bind("change",slidesHandler).bind("submit",disableSubmit);
				
				sliders = $("#pe_sliders");
				sliders.find("a").live("click",slidersHandler).live("hover",hoverHandler,hoverHandler);
				sliders = sliders.find("tbody");
				
				shortcode = $('<input type="text" class="pe_clipboard" id="pe_shortcode">');
				
				popup = $("#pe_dialog").dialog({
					autoOpen: false,
					modal: true,
					resizable: false,
					buttons: {
						"yes": doConfirmedAction,
						"no": dialogAbortAndClose
					},
					close : dialogAbort
				});
				
				preview = $("#pe_preview").dialog({
					autoOpen: false,
					modal: true,
					resizable: false,
					draggable: false,
					close : closePreview,
					buttons: {
						"close": function () {
							preview.dialog("close");
						}
					}
				});
				
				$('.pe_sortable').sortable({
					forcePlaceholderSize: true, 
					handle: '.pe_img_drag',
					axis:"y",
					tolerance: "pointer",
					cursor: 'move',
					distance: 5,
					containment: 'parent',
					dropOnEmtpy:false
				});
				
				$(window).scroll(scrollHandler).bind("unload",beforeUnload);
				//$(window).scroll(scrollHandler);
				
				if ($.cookie(slug+"reload")) {
					load();
				} else {
					addMissingConfValues();
					globalForm.populate(options.global);
					globalHandler();
					refreshSliderList();
				}
				if (options.global.tooltip != "disabled") {
					$('a[data-help],label[data-help]',main).qtip(toolTipConf);
				}
			},
			redraw: function() {
				slidersList();
			}
		};
	}

	jQuery.extend(jQuery.easing, {
		peAdmin:  function (x, t, b, c, d) {
			return c*((t=t/d-1)*t*t + 1) + b;
		}
	});
	
	// Replace ? tokens with variables passed as arguments in a string 
	String.prototype.format = function() {
		if (!arguments.length) { throw "String.format() failed, no arguments passed, this = "+this; }
		var tokens = this.split("?");
		if (arguments.length != (tokens.length - 1)) { throw "String.format() failed, tokens != arguments, this = "+this; }
		var s = tokens[0];
		for (var i = 0; i < arguments.length; ++i) {
			s = s + (arguments[i] + tokens[i + 1]);
		}
		return s;
	};

	var admin = new Admin();
	admin.init();
	
	$("#pe_documentation table tr td:first-child").addClass("parameter");
	$("#pe_documentation table tr:even").addClass("even");

});
