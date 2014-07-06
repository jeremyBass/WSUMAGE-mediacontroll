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





  







	
	
}