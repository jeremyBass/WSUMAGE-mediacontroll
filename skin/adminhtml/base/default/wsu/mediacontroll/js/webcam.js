// JavaScript Document
(function($){
	
    var camAccess = {
		extern		: null, // external select token to support jQuery dialogs
		append		: true, // append object instead of overwriting
		mode		: "stream", // callback | snap | save | stream
		
		width		: 320,
		height		: 240,
		swffile		: "jscam.swf",
		quality		: 85,
		append		: false,
	
		debug		: function(){},
		onCapture	: function(){},
		onTick		: function(){},
		onSave		: function(){},
		onLoad		: function(){},
		
		video		: true,
		audio		: false,
		success		: function(){},
		fail		: function(){},
		onStop		: function(){},
		prePlay		: function(){},
		console		: false
    };

    window.camAccess = camAccess;
	
    $.fn.camAccess = function(options) {
        $.extend(window.camAccess, options);
		var audioSource	= null;
		var videoSource	= null;
		var ctx 		= null;
		var localstream = null;	
		var action		= function(){};

		//html objs
		var wraper		= null;
		var video 		= null;
		var videostream	= null;
		var canvas 		= null;
		var preview 	= null;
		
        // Try to play the media stream
        function int(video) {
			//set data
            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
            window.URL = window.URL || window.webkitURL || window.mozURL || window.msURL;
            // Call the getUserMedia method with our callback functions
            if (navigator.getUserMedia) {
				buildPlayer();
            } else {
                sendError(video,'Native web camera streaming (getUserMedia) not supported in this browser.');
            }

        }
		
		function iniFallback(){
			var source = '<object id="XwebcamXobjectX" type="application/x-shockwave-flash" data="'+camAccess.swffile+'" width="'+camAccess.width+'" height="'+camAccess.height+'"><param name="movie" value="'+camAccess.swffile+'" /><param name="FlashVars" value="mode='+camAccess.mode+'&amp;quality='+camAccess.quality+'" /><param name="allowScriptAccess" value="always" /></object>';
		
			if (null !== camAccess.extern) {
				$(camAccess.extern)[camAccess.append ? "append" : "html"](source);
			} else {
				this[camAccess.append ? "append" : "html"](source);
			}
		
			(_register = function(run) {
				var cam = document.getElementById('XwebcamXobjectX');
				if (cam.capture !== undefined) {
					/* Simple callback methods are not allowed :-/ */
					camAccess.capture = function(x) {
						try {
							return cam.capture(x);
						} catch(e) {}
					}
					camAccess.save = function(x) {
						try {
							return cam.save(x);
						} catch(e) {}
					}
					camAccess.setCamera = function(x) {
						try {
							return cam.setCamera(x);
						} catch(e) {}
					}
					camAccess.getCameraList = function() {
						try {
							return cam.getCameraList();
						} catch(e) {}
					}
					camAccess.onLoad();
				} else if (0 == run) {
					camAccess.debug("error", "Flash movie not yet registered!");
				} else {
					/* Flash interface not ready yet */
					window.setTimeout(_register, 1000 * (4 - run), run - 1);
				}
			})(3);	
			
		}
		
		
		function buildPlayer(){
				//do wrapers
				if(video.closest('.cam_warpper').length<=0){
					 video.wrap('<div class="cam_warpper" />');
				}
				wraper		= video.closest('.cam_warpper');
				if(wraper.find('.cam_snap_canvas').length<=0){
					wraper.append('<canvas class="cam_snap_canvas" />');
				}
				canvas		= wraper.find('.cam_snap_canvas');
				if(wraper.find('.preview').length<=0){
					wraper.append('<img class="preview" />');
				}
				preview		= wraper.find('.preview');
				if(wraper.find('.controlls').length<=0){
					 wraper.append('<span class="">'+
							'<button class="capture_btn">Capture video</button>'+
							'<button class="play_btn">Stop</button>'+
						'</span>'); 
				}
				controlls	= wraper.find('.controlls');//maybe lose this
				cap_btn		= wraper.find('.capture_btn');
				play_btn	= wraper.find('.play_btn');			
	
				//present choices
				if(hasSourceChoice()){
					if(wraper.find('.choice').length<=0){
						 wraper.append('<div class="choice" style="display:none;"/>');
					}
					sourceSelection	= wraper.closest('.choice');
				}

				play_btn.on("click",function(e){
					e.preventDefault();
					//if (!!window.stream) { stop(); }
					camOps = $.extend({video:camAccess.video},{audio:camAccess.audio},{});
					navigator.getUserMedia({video: true}, onStream, function(error) { 
						iniFallback();
						//sendError(video,'Unable to get webcam stream.');
					});
				});	
			
		}
		function hasSourceChoice(){
			if (typeof MediaStreamTrack === 'undefined'){
				return false;
			} else {
				return true;
			}
		}

		function gotSources(sourceInfos) {
			for (var i = 0; i != sourceInfos.length; ++i) {
				var sourceInfo = sourceInfos[i];
				var option = document.createElement("option");
				option.value = sourceInfo.id;
				if (sourceInfo.kind === 'audio') {
					option.text = sourceInfo.label || 'microphone ' + (audioSelect.length + 1);
					audioSelect.appendChild(option);
				} else if (sourceInfo.kind === 'video') {
					option.text = sourceInfo.label || 'camera ' + (videoSelect.length + 1);
					videoSelect.appendChild(option);
				} else {
					console.log('Some other kind of source: ', sourceInfo);
				}
			}
			$('.vidop').on('change',function(){
				//optional: [{sourceId: audioSource}]
				});
			$('.audioop').on('change',function(){
				//optional: [{sourceId: audioSource}]
			});
		}


		function presentSourceSelection(){
			MediaStreamTrack.getSources(gotSources);
		}

        // Define our error message
        function sendError(video,message) {
            var e = new Error();
			if(camAccess.console) { console.error(message); }
			if ( $.isFunction( camAccess.fail ) ) {
				camAccess.fail.call( video, message );	
			}
        }

		function onStream(stream) {
			if ( $.isFunction( camAccess.prePlay ) ) { //if default is a function then don't check
				camAccess.prePlay.call(video);	
			}
			playStream(stream);
		}
		
		function stopStream(){
			video.attr('src',null);
			window.stream.stop();	
			if ( $.isFunction( camAccess.onStop ) ) {
				camAccess.onStop.call(video);	
			}
		}

		function playStream(stream){
			localstream = null;

			if(camAccess.mode=="snap"){
				action = function () {
					canvas 		= video.next('canvas');
					preview 	= video.next('.cam_snap_canvas');
					ctx 		= canvas.get(0).getContext('2d');
					ctx.drawImage(video.get(0), 0, 0);
					// "image/webp" works in Chrome.
					// Other browsers will fall back to image/png.
					preview.attr('src',canvas.toDataURL('image/webp'));
				};
			}

			//set events
			cap_btn.on("click",function(e){
				e.preventDefault();
				action();
			});

			// Set the source of the video element with the stream from the camera
			if (video.attr("mozSrcObject") !== undefined) {
				video.attr("mozSrcObject",stream);
			}

			window.stream = stream; // make stream available to console
			video.attr('src', window.URL.createObjectURL(stream) );
			video.get(0).play();
			video.on('loadedmetadata', function() {
				if ( $.isFunction( settings.success ) ) {
					settings.success.call($(video),video);	
				}
		 	});
		}


		return this.each( function() { 
			video=$(this);
			int(video);
		});
    };

})(jQuery);