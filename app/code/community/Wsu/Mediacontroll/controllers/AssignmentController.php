<?php

class Wsu_Mediacontroll_AssignmentController extends Mage_Adminhtml_Controller_action {


	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('system/tools')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'))
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Tools'), Mage::helper('adminhtml')->__('Tools'))
			->_addBreadcrumb(Mage::helper('mediacontroll')->__('Image cleaner'), Mage::helper('mediacontroll')->__('Image Assignment'));
		
		return $this;
	}   


    public function indexAction() {
		
		
        $this->_initAction()->_addContent($this->getLayout()->createBlock('mediacontroll/assignment'));
		
		$this->renderLayout();
    }
/*	
	public function idUnsortedAction() {
		Mage::helper('mediacontroll')->get_ProductUnsortedImages();
		
		$this->_initAction()->_addContent($this->getLayout()->createBlock('mediacontroll/imgclean'));
		
		$this->renderLayout();
	}






public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	*/
	public function newAction(){
		Mage::helper('mediacontroll')->compareList();
		$this->_redirect('*/*/');
	}



//look to http://stackoverflow.com/a/4353686/746758
//http://stackoverflow.com/a/19159776/746758
	public function assignmentAction($id=0) {
		$requestId = $this->getRequest()->getParam('id');
		$prod_id = ($id > 0) ? $id : $requestId;
		$affected = array();
		$starting = 1;
		if( $prod_id > 0 ) {
			

				$product = mage::getModel('catalog/product')->load($prod_id);
				$images = $product->getMediaGalleryImages();

			try {
				if(count($images)>0){
						
						$image = $images->getFirstItem();
						if (!$product->hasImage()) $product->setSmallImage($image->getFile());
						if (!$product->hasSmallImage()) $product->setSmallImage($image->getFile());
						if (!$product->hasThumbnail()) $product->setThumbnail($image->getFile());
	
						$product->save();
						if($requestId>0){
							Mage::getSingleton('adminhtml/session')->addSuccess(
								Mage::helper('mediacontroll')->__('Product Images were successfully reSorted from 0-'.$i)
								);
						}
				}else{
					if($requestId>0){
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mediacontroll')->__('Product Image wasn\'t able to be done.'));
					}
				}
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/');
			}
		}else{
			Mage::getSingleton('adminhtml/session')->addError("failed to get key");
			$this->_redirect('*/*/');
		}
	}
    public function massAssignmentAction() {
        $mediacontrollIds = $this->getRequest()->getParam('mediacontroll');
        if(!is_array($mediacontrollIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($mediacontrollIds as $mediacontrollId) {
					$this->resortAction($mediacontrollId);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully resorted', count($mediacontrollIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

}