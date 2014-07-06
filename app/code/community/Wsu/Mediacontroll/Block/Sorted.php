<?php
class Wsu_Mediacontroll_Block_Sorted extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_blockGroup = 'mediacontroll';
		$this->_controller = 'sorted';
		$this->_headerText = Mage::helper('mediacontroll')->__('Image Sorting assessment');
		parent::__construct();
		$this->_addButton('add', array(
			'label' => Mage::helper('mediacontroll')->__('Refresh'),
			'onclick' => "setLocation('" . $this->getUrl('*/*/search', array('page_key' => 'collection')) . "')",
			'class' => 'add'
		));
	}

}

