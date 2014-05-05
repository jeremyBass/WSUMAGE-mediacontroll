<?php

class Wsu_Mediacontroll_ImglessController extends Mage_Adminhtml_Controller_action {


	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('system/tools')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'))
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Tools'), Mage::helper('adminhtml')->__('Tools'))
			->_addBreadcrumb(Mage::helper('mediacontroll')->__('Image cleaner'), Mage::helper('mediacontroll')->__('Sort assignment'));
		
		return $this;
	}   


    public function indexAction() {
        $this->_initAction()->_addContent($this->getLayout()->createBlock('mediacontroll/imgless'));
		$this->renderLayout();
    }

	public function searchAction(){
		Mage::helper('mediacontroll')->indexImgless();
		$this->_redirect('*/*/');
	}



//look to http://stackoverflow.com/a/4353686/746758
//http://stackoverflow.com/a/19159776/746758
	public function resortAction($id=0) {

	}
    public function massResortAction() {

    }

	
}