<?php

class Wsu_Mediacontroll_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
    public function __construct(){
        parent::__construct();
        $this->setId('mc_img_tabs');
        $this->setTitle(Mage::helper('mediacontroll')->__('Image controls'));
    }
	
    protected function _beforeToHtml(){
        $activeSection = $this->_getActiveSection();
		/*
		echo 'total products with images',' ',count($productImgCollection), PHP_EOL;
		
		echo 'total products missing starting sort idexes',' ',count(array_filter($productImgCollection, function($val){
						return $val['productImageProfile']['hasSortIndexStart'];
					})
				), PHP_EOL;
		echo 'total products missing sorted',' ',count(array_filter($productImgCollection, function($val){
						return $val['productImageProfile']['missingSorted'];
					})
				), PHP_EOL;
		echo 'total products missing assigned',' ',count(array_filter($productImgCollection, function($val){
						return $val['productImageProfile']['missingAssigned'];
					})
				), PHP_EOL;
		*/
        $this->addTab('missingSorted_section', array(
            'label'     => Mage::helper('mediacontroll')->__('Missing Sorted'),
            'title'     => Mage::helper('mediacontroll')->__('Missing Sorted'),
            'url'       => $this->getUrl('*/*/missingSorted', array('_current' => true)),
            'class'     => 'ajax',
            'active'    => $activeSection === 'missingSorted',
        ));

        $this->addTab('missingAssigned_section', array(
            'label'     => Mage::helper('mediacontroll')->__('Missing Assigned'),
            'title'     => Mage::helper('mediacontroll')->__('Missing Assigned'),
            'url'       => $this->getUrl('*/*/missingAssigned', array('_current' => true)),
            'class'     => 'ajax',
            'active'    => $activeSection === 'missingAssigned',
        ));

        $this->addTab('missSorted_section', array(
            'label'     => Mage::helper('mediacontroll')->__('Miss Sorted'),
            'title'     => Mage::helper('mediacontroll')->__('Miss Sorted'),
            'url'       => $this->getUrl('*/*/missSorted', array('_current' => true)),
            'class'     => 'ajax',
            'active'    => $activeSection === 'missSorted',
        ));

        return parent::_beforeToHtml();
    }
	
	
	
}


