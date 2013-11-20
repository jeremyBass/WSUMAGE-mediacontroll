// JavaScript Document
(function($){
	
	
    $.fn.camAccess = function(options) {
		
        var settings = $.extend({
			video		 : true,
			audio		 : false,
			mode		 : null,
            success      : function(){},
            fail         : function(video,error){},
			onStop       : function(video){},
			prePlay		 : function(video){},
            console      : false
        }, options);
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
					//if (!!window.stream) {
						//stop();
					//}
					
					camOps = $.extend({video:settings.video},{audio:settings.audio},{});
					navigator.getUserMedia({video: true}, onStream, function(error) { 
						sendError(video,'Unable to get webcam stream.');
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
			if(settings.console) { console.error(message); }
			if ( $.isFunction( settings.fail ) ) {
				settings.fail.call( video, message );	
			}
        }

		function onStream(stream) {
			if ( $.isFunction( settings.prePlay ) ) {
				settings.prePlay.call(video);	
			}
			playStream(stream);
		}
		
		function stopStream(){
			video.attr('src',null);
			window.stream.stop();	
			if ( $.isFunction( settings.onStop ) ) {
				settings.onStop.call(video);	
			}
		}

		function playStream(stream){
			localstream = null;

			if(settings.mode=="snap"){
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