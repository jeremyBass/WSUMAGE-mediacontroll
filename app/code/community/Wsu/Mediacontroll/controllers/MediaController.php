<?php
class Wsu_Mediacontroll_MediaController extends Mage_Adminhtml_Controller_Action {

    public function indexAction(){
        $this->_title($this->__('Media Control'));
        $this->loadLayout();
        $this->_setActiveMenu('system/mediacontrol');
        $this->renderLayout();
    }


    public function orphanedAction(){
        $this->_getSession()->setActiveSection('orphaned');
        $this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('mediacontrol/media_orphaned'));
			
        $this->renderLayout();
    }

    public function deleteActionAction(){

        return $this->_redirect('*/*/');
    }





    public function generateListAction() {
        $storeId = $this->getRequest()->getParam('store_id');
        $store = Mage::app()->getStore($storeId);
        $session = Mage::getSingleton('adminhtml/session');

        if (!$storeId || !$store->getId() || !$store->getIsActive()) {
            $session->addError('Please specify a valid store for list generation');
        }
		//would be setting this as option
		Mage::register('set_params', array(
			'json'=>true,
			'limit'=>5,
			'offset'=>0,
			'store'=>$store
		));
        $this->loadLayout();
        $this->renderLayout();
    }

    public function generateListItemAction() {
        $url = $this->getRequest()->getParam('last_set_item');
        $store = $this->getRequest()->getParam('store');
		$set_params = Mage::registry('set_params');

        $result = Mage::helper('mediacontroll')->get_ProductImages($set_params);

        return $this->getResponse()
            ->setHeader('Content-type', 'application/json')
            ->setBody(Mage::helper('core')->jsonEncode($result));
    }








	
	
}