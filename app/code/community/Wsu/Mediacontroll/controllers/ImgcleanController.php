<?php

class Wsu_Mediacontroll_ImgcleanController extends Mage_Adminhtml_Controller_action {

    public function indexAction() {
        $this->_initAction()->_addContent($this->getLayout()->createBlock('mediacontroll/imgclean'))->renderLayout();
    }

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('mediacontroll/imgclean')
			->_addBreadcrumb(Mage::helper('wsu_mediacontroll')->__('Items Manager'), Mage::helper('wsu_mediacontroll')->__('Item Manager'));
		
		return $this;
	}   

/*	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	*/
	public function newAction(){
		Mage::helper('wsu_mediacontroll')->compareList();
		$this->_redirect('*/*/');
	}
	
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('mediacontroll/imgclean');
				$model->load($this->getRequest()->getParam('id'));
				unlink('media/catalog/product'. $model->getFilename());
				$model->setId($this->getRequest()->getParam('id'))->delete();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('wsu_mediacontroll')->__('Image was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $mediacontrollIds = $this->getRequest()->getParam('mediacontroll');
        if(!is_array($mediacontrollIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
				$model = Mage::getModel('mediacontroll/imgclean');
                foreach ($mediacontrollIds as $mediacontrollId) {
					$model->load($mediacontrollId);
					unlink('media/catalog/product'. $model->getFilename());
					$model->setId($mediacontrollId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($mediacontrollIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
}