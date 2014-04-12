<?php
class Wsu_Mediacontroll_Adminhtml_MediacontrollController extends Mage_Adminhtml_Controller_Action {

    public function indexAction(){
        $this->_title($this->__('Mediacontroll'));
        $this->loadLayout();
        $this->_setActiveMenu('system/mediacontroll');
        $this->renderLayout();
    }








	
	
}