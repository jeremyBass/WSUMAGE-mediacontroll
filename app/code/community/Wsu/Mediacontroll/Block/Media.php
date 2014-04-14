<?php
class Wsu_Mediacontroll_Block_Media extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface {
	 
	public function __construct() {
		$this->_blockGroup = 'mediacontroll';
		$this->_controller = 'media';
		$this->_headerText = Mage::helper('mediacontroll')->__('media.');
		//$this->_addButtonLabel = Mage::helper('mediacontroll')->__('Refresh');
		parent::__construct();
	}
	 

}

