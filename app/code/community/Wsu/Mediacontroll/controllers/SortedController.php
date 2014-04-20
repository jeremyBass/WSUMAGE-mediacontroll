<?php

class Wsu_Mediacontroll_SortedController extends Mage_Adminhtml_Controller_action {


	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('system/tools')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'))
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Tools'), Mage::helper('adminhtml')->__('Tools'))
			->_addBreadcrumb(Mage::helper('mediacontroll')->__('Image cleaner'), Mage::helper('mediacontroll')->__('Sort assignment'));
		
		return $this;
	}   


    public function indexAction() {
		
		
        $this->_initAction()->_addContent($this->getLayout()->createBlock('mediacontroll/sorted'));
		
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




	public function resortAction($id=0) {
		$requestId = $this->getRequest()->getParam('id');
		$prod_id = ($id > 0) ? $id : $requestId;
		$affected = array();
		if( $prod_id > 0 ) {
			try {

				$product = mage::getModel('catalog/product')->load($prod_id);
				$attributes = $product->getTypeInstance(true)->getSetAttributes($product);
				$gallery = $attributes['media_gallery'];
				$images = $product->getMediaGalleryImages();
				$i=0;
				foreach ($images as $image) {
					$backend = $gallery->getBackend();
					$backend->updateImage(
						$product,
						$image->getFile(),
						array('position' => $i)
					);
					$i++;
				}
				$product->getResource()->saveAttribute($product, 'media_gallery');
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mediacontroll')->__('Product Images were successfully re Sorted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				if($requestId>0)$this->_redirect( '*/*/edit', array('id' => $imgcleanId) );
			}
		}else{
			Mage::getSingleton('adminhtml/session')->addError("failed to get key");
			$this->_redirect('*/*/');
		}
	}
    public function massResortAction() {
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
                        'Total of %d record(s) were successfully deleted', count($mediacontrollIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	

	
	public function deleteAction($id=0) {
		$requestId = $this->getRequest()->getParam('id');
		$imgcleanId = ($id > 0) ? $id : $requestId;
		$affected = array();
		if( $imgcleanId > 0 ) {
			try {
				$model = Mage::getModel('wsu_mediacontroll/imgclean');
				$model->load($imgcleanId);
				$file = 'media/catalog/product'. $model->getFilename();
				unlink($file);
				$model->setId($imgcleanId)->delete();
				Mage::log('Deleted media file: '.$file, Zend_Log::WARN);
				if($requestId>0){
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mediacontroll')->__('Image was successfully deleted'));
					$this->_redirect('*/*/');
				}

			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				if($requestId>0)$this->_redirect( '*/*/edit', array('id' => $imgcleanId) );
			}
		}
		if($requestId>0){
			$this->_redirect('*/*/');
		}
	}

    public function massDeleteAction() {
        $mediacontrollIds = $this->getRequest()->getParam('mediacontroll');
        if(!is_array($mediacontrollIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($mediacontrollIds as $mediacontrollId) {
					$this->deleteAction($mediacontrollId);
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