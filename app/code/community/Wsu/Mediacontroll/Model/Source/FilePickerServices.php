<?php

class Wsu_Mediacontroll_Model_Source_FilePickerServices {

	public function toOptionArray($isMultiselect = false) {
		$options = array();
		$services = array('BOX', 'COMPUTER', 'DROPBOX','FACEBOOK', 'GITHUB', 'GMAIL', 'GOOGLE_DRIVE', 'IMAGE_SEARCH', 'URL', 'WEBCAM');
		foreach ($services as $service) {
			$options[] = array(
				'value'=> 'filepicker.SERVICES.'.$service,
				'label' => Mage::helper('mediacontroll')->__($service)
			);
		}
		return $options;
	}

}