<?php
class Wsu_Mediacontroll_Block_Assignment extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_blockGroup = 'mediacontroll';
		$this->_controller = 'assignment';
		$this->_headerText = Mage::helper('mediacontroll')->__('Image Assessment');
		$this->_addButtonLabel = Mage::helper('mediacontroll')->__('Refresh');
		parent::__construct();
	}

}

