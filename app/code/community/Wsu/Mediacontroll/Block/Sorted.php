<?php
class Wsu_Mediacontroll_Block_Sorted extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_blockGroup = 'mediacontroll';
		$this->_controller = 'sorted';
		$this->_headerText = Mage::helper('mediacontroll')->__('Image Sorting assessment');
		$this->_addButtonLabel = Mage::helper('mediacontroll')->__('Refresh');
		parent::__construct();
	}
/*	
	public function _prepareLayout() {
		return parent::_prepareLayout();
    }
    */
     public function getImgclean() { 
        if (!$this->hasData('imgclean')) {
            $this->setData('imgclean', Mage::registry('imgclean'));
        }
        return $this->getData('imgclean');
    }
}

