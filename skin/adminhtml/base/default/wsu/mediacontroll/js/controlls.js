// JavaScript Document

(function($){
	$(function(){
		var vgaConstraints = {
			mandatory: {
			  maxWidth: 640,
			  maxHeight: 360
			}
		};		
		$('#stream').camAccess({
			video	:vgaConstraints,
			success	:function(ele,video){
						alert('Hey, it works! stream Dimensions: ' + ele.width() + ' x ' + ele.height());
					},
			fail	:function(video,message){
						alert('Oops: stream :: ' + message);
					},
			console	:true
		}).css("border","red solid 5px");

		var hdConstraints = {
			mandatory: {
			  maxWidth: 1280,
			  maxHeight: 720
			}
		};	
		$('#snap').camAccess({
			video	:hdConstraints,
			mode	: "snap",
			success	:function(ele,video){
						alert('Hey, it works! stream Dimensions: ' + ele.width() + ' x ' + ele.height());
					},
			fail	:function(error,message){
						alert('Oops: stream :: ' + message);
					},
			console	:true
		}).css("border","blue solid 5px");


	});
})(jQuery);