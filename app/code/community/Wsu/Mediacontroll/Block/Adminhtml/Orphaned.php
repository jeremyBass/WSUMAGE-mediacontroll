<?php

class Wsu_Mediacontroll_Block_Adminhtml_Orphaned extends Mage_Adminhtml_Block_Widget_Grid_Container{
    public function __construct(){
        $this->_controller = 'adminhtml_orphaned';
        $this->_blockGroup = 'wsu_mediacontroll';
        $this->_headerText = Mage::helper('mediacontroll')->__('Find Orphaned Images');
        parent::__construct();
        $this->_removeButton('add');
        $this->setTemplate('wsu/mediacontroll/widget/grid/container.phtml');
    }

    public function getCreateUrl() {
        return '';
    }

    public function getHeaderCssClass() {
        return '';
    }
}