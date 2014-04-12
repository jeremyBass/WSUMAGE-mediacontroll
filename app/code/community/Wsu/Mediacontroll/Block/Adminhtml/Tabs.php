<?php

class Wsu_Mediacontroll_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
    public function __construct(){
        parent::__construct();
        $this->setId('mc_img_tabs');
        $this->setTitle(Mage::helper('mediacontroll')->__('Image controls'));
    }
	
	
	
	
	
}


