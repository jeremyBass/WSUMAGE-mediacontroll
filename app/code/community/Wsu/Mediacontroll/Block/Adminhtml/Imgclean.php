<?php
class Wsu_Mediacontroll_Block_Adminhtml_Imgclean extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_imgclean';
    $this->_blockGroup = 'mediacontroll';
    $this->_headerText = Mage::helper('wsu_mediacontroll')->__('Items Manager. These files are not in database.');
   $this->_addButtonLabel = Mage::helper('wsu_mediacontroll')->__('Refresh');
    parent::__construct();
	
  }
}