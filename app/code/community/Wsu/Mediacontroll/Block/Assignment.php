<?php
class Wsu_Mediacontroll_Block_Assignment extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_blockGroup = 'mediacontroll';
		$this->_controller = 'assignment';
		$this->_headerText = Mage::helper('mediacontroll')->__('Image Assignment Assessment');
		parent::__construct();
		$this->_addButton('add', array(
			'label' => Mage::helper('mediacontroll')->__('Refresh'),
			'onclick' => "setLocation('" . $this->getUrl('*/*/generate', array('page_key' => 'collection')) . "')",
			'class' => 'add'
		));
	}

}

