(function ($) {
	/*jslint undef: false, browser: true, devel: false, eqeqeq: false, bitwise: false, white: false, plusplus: false, regexp: false, nomen: false */ 
	/*global jQuery,setTimeout,projekktor,location,setInterval,YT,clearInterval */

	$.pixelentity = $.pixelentity || {version: '1.0.0'};
	
	$.pixelentity.video = {	
		conf: { 
		},
		getType: function(src) {
			return 'video/'+src.match(/(\w+)$/)[1].replace("ogv","ogg");
		}
	};
	
	var iDev = navigator.userAgent.toLowerCase().match(/(iphone|ipod|ipad)/);
	var instances = 0;
	
	function PeVideo(t, conf) {
		var jthis = $(this);
		var target = t;
		var player;
		var checkTimer;
		var ready = false;
		
		function start() {
			switch (conf.type) {
				case "youtube":
					$.pixelentity.youtube(youtubePlayerReady);
				break;
				case "vimeo":
					$.pixelentity.vimeo(vimeoPlayerReady);
				break;
				case "vidly":
					localVideo([
						"http://vid.ly/"+conf.videoId+"?content=video&format=mp4",
						"http://vid.ly/"+conf.videoId+"?content=video&format=webm",
						"http://vid.ly/"+conf.videoId+"?content=video&format=ogv"
					],conf.poster);
				break;
				case "local":
					localVideo(conf.videoId,conf.poster);
				break;
			}
		} 
		
		function localVideo(srcs,poster) {
			
			instances++;
			var id = 'pe_local_player_'+(instances);
			var vid = $('<div id="'+(id)+'"/>').css({
				"background-color": "black",
				"width": conf.width,
				"height": conf.height
			});
			target.html(vid[0]);
			
			var video = {};
			
			for (var i=0;i<srcs.length;i++) {
				video[i] = {
					src: srcs[0],
					type: $.pixelentity.video.getType(srcs[0])
				};
			}
			
			player = new projekktor("#"+id, {
				controls: true,
				volume: 0.9,
				_width: conf.width,
				_height: conf.height,
				_autoplay: true,
				enableFullscreen: false,
				imageScaling: "fill",
				videoScaling: "aspectratio",
				_plugins: ['Display', 'Controlbar'],
				poster: poster,
				playerFlashMP4: 'js/template/video/jarisplayer.swf',
				playerFlashMP3: 'js/template/video/jarisplayer.swf',
				playlist: [video]
			});
			
			player.addListener(iDev ? 'ready' : 'buffer',localVideoBuffer);		
		}
		
		function localVideoBuffer(value) {
			if (iDev) {
				setTimeout(fireReadyEvent,100);
				player.removeListener('ready',localVideoBuffer);
			} else if (value == "FULL") {
				player.removeListener('buffer',localVideoBuffer);
				fireReadyEvent();
			}
		}
		
		function youtubePlayerReady(ytplayer) {
			var div=$("<div/>");
			target.append(div);
			player = new ytplayer(div[0], {
				height: conf.height,
				width: conf.width,
				videoId: conf.videoId,
				playerVars: {
					theme: "dark",
					wmode: "opaque",
					autohide: 0,
					enablejsapi: 1,
					origin: location.href.match(/:\/\/(.[^\/]+)/)[1],
					loop: conf.loop ? 1 : 0,
					hd: conf.hd ? 1 : 0,
					autoplay: conf.autoPlay ? 1 : 0,
					showinfo:0,
					iv_load_policy:3,
					modestbranding:1,
					showsearch:0,
					fs:0
				},
				events: {
				  'onStateChange': ytStateChange,
				  'onReady': fireReadyEvent
				}
			});
			checkTimer = setInterval(ytStateChange,250);
			if ($.browser.msie && $.browser.version < 8) {
				setTimeout(fireReadyEvent,1000);
			}
		}
		
		function fireReadyEvent() {
			if (!ready) {
				jthis.trigger("video_ready.pixelentity");
				ready = true;
			}
		}
		
		function vimeoPlayerReady(vimeoplayer) {
			player = new vimeoplayer(target[0], {
				height: conf.height,
				width: conf.width,
				videoId: conf.videoId,
				playerVars: {
					autohide: 0,
					origin: location.href.match(/:\/\/(.[^\/]+)/)[1],
					loop: conf.loop ? 1 : 0,
					autoplay: conf.autoPlay ? 1 : 0
				}
			});
			$(player)
				.one("video_ready.pixelentity",fireReadyEvent)
				.one("video_ended.pixelentity",vimeoVideoEnded);
		}
		
		function vimeoVideoEnded() {
			jthis.trigger("video_ended.pixelentity");
		}
		
		function ytStateChange() {
			if (!player) {return;}
			if (typeof player.getPlayerState === "function") {
				switch (player.getPlayerState()) {
					case YT.PlayerState.ENDED:
						jthis.trigger("video_ended.pixelentity");
					break;
					case YT.PlayerState.PLAYING:
						if ((player.getDuration()-player.getCurrentTime()) < 0.4) {
							jthis.trigger("video_ended.pixelentity");
						}
					break;
					
				}
			}
		}
		
		$.extend(this, {
			bind: function(ev,handler) {
				jthis.bind(ev,handler);
			},
			one: function(ev,handler) {
				jthis.one(ev,handler);
			},
			destroy: function() {
				clearInterval(checkTimer);
				if (jthis) {
					jthis.remove();
				}
				jthis = undefined;
				if (player) {
					$(player).unbind("video_ended.pixelentity");
					
					switch (conf.type) {
						case "vidly":
						case "local":
							player.removeListener(iDev ? 'ready' : 'buffer',localVideoBuffer);
							if (player.selfDestruct) {
								player.selfDestruct();
							}
						break;
						default:
							if (player.destroy) {
								player.destroy();
							}
					}
				}
				player = undefined;
				target.data("peVideo", null);
				target = undefined;
				
			}
		});
		
		start();
		
		
	}
	
	// jQuery plugin implementation
	$.fn.peVideo = function(conf) {
		// return existing instance
		
		var api = this.data("peVideo");
		
		if (api) { 
			return api; 
		}

		conf = $.extend(true, {}, $.pixelentity.video.conf, conf);
		
		// install kb for each entry in jQuery object
		this.each(function() {
			api = new PeVideo($(this), conf);
			$(this).data("peVideo", api); 
		});
		
		return conf.api ? api: this;		 
	};
	
		
}(jQuery));
