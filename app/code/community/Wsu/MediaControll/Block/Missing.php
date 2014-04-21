<?php
class Wsu_Mediacontroll_Block_Sorted extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_blockGroup = 'mediacontroll';
		$this->_controller = 'missing';
		$this->_headerText = Mage::helper('mediacontroll')->__('Products Missing Images');
		$this->_addButtonLabel = Mage::helper('mediacontroll')->__('Refresh');
		parent::__construct();
	}

}

