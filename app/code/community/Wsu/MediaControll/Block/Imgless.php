<?php
class Wsu_Mediacontroll_Block_Imgless extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_blockGroup = 'mediacontroll';
		$this->_controller = 'imgless';
		$this->_headerText = Mage::helper('mediacontroll')->__('Products with no image assessment');
		parent::__construct();
		$this->_addButton('add', array(
			'label' => Mage::helper('mediacontroll')->__('Refresh'),
			'onclick' => "setLocation('" . $this->getUrl('*/*/search', array('page_key' => 'collection')) . "')",
			'class' => 'add'
		));
	}

}

