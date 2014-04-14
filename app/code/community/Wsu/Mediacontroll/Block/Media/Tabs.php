<?php
class Wsu_Mediacontroll_Block_Medial_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
    public function __construct(){
        parent::__construct();
        $this->setId('mediacontroll_tabs');
        $this->setTitle(Mage::helper('mediacontroll')->__('media controll'));
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
        $this->addTab('orphaned_section', array(
            'label'     => Mage::helper('mediacontroll')->__('Orphaned Images'),
            'title'     => Mage::helper('mediacontroll')->__('Orphaned Images'),
            'url'       => $this->getUrl('*/*/orphaned', array('_current' => true)),
            'class'     => 'ajax',
            'active'    => $activeSection === 'orphaned',
        ));


        return parent::_beforeToHtml();
    }
	
    protected function _getActiveSection($default = 'orphaned'){
        $activeSection = Mage::getSingleton('adminhtml/session')->getActiveSection();
        if (!$activeSection) {
            $activeSection = $default;
        }

        return $activeSection;
    }
}