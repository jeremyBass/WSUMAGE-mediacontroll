<?php
class Wsu_MediaControll_Block_FilePicker extends Mage_Adminhtml_Block_Media_Uploader {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->setTemplate('media/filepicker.phtml');
	}

	/**
	 * Get filepicker.io API key
	 *
	 * @return string
	 */
	public function getApiKey() {
		return Mage::getStoreConfig('wsu_mediacontroll/filepicker/apikey');
	}

	/**
	 * Get active filepicker.io services
	 *
	 * @return array
	 */
	public function getServices() {
		return Mage::getStoreConfig('wsu_mediacontroll/filepicker/services');
	}

}