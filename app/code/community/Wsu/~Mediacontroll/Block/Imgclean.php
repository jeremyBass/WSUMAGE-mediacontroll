<?php
class Wsu_Mediacontroll_Block_Imgclean extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_blockGroup = 'mediacontroll';
		$this->_controller = 'imgclean';
		$this->_headerText = Mage::helper('mediacontroll')->__('Items Manager. These files are not in database.');
		parent::__construct();
		$this->_addButton('add', array(
			'label' => Mage::helper('mediacontroll')->__('Refresh'),
			'onclick' => "setLocation('" . $this->getUrl('*/*/search', array('page_key' => 'collection')) . "')",
			'class' => 'add'
		));
	}
     public function getImgclean() { 
        if (!$this->hasData('imgclean')) {
            $this->setData('imgclean', Mage::registry('imgclean'));
        }
        return $this->getData('imgclean');
    }
}

