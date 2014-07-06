<?php

class Wsu_Mediacontroll_Model_Source_EditorTools {

	public function toOptionArray($isMultiselect = false) {
		$options = array();
		$services = array(	'enhance', 'effects', 'frames','stickers', 'orientation', 
							'focus', 'resize', 'crop', 'warmth', 'brightness',
							'contrast','saturation','sharpness','colorsplash',
							'draw','text','redeye','whiten','blemish'
						);
		foreach ($services as $service) {
			$options[] = array(
				'value'=> $service,
				'label' => Mage::helper('mediacontroll')->__($service)
			);
		}
		return $options;
	}

}
/*
##enhance: Autocorrect your photo with one of four basic enhancements.
##effects: Choose from a variety of effects and filters for your photo.
##frames: Choose from a variety of frames to apply around your photo.
##stickers: Choose from a variety of stickers you can resize and place on your photo.
##orientation: Rotate and flip your photo in one tool.
##focus: Adds a selective linear or radial focus to your photo.
##resize: Resize the image using width and height number fields. 
##crop: Crop a portion of your photo. Add presets via API. Fixed-pixel cropPresets perform a resize when applied.
##warmth: Adjust the overall image color temperature.
##brightness: Adjust the overall image brightness.
##contrast: Adjust the overall image contrast.
##saturation: Adjust the overall image saturation.
##sharpness: Blur or sharpen the overall image in one tool.
##colorsplash: Use a smart brush to colorize parts of your photo which becomes grayscale otherwise.
##draw: Add doodle overlays with a brush.
##text: Add custom, resizable text.
##redeye: Remove redeye from your photo with a brush.
##whiten: Whiten teeth with a brush. (Not supported in IE8)
##blemish: Remove skin blemishes with a brush.

*/