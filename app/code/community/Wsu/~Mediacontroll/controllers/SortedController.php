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

	public function searchAction(){
		Mage::helper('mediacontroll')->indexUnsorted();
		$this->_redirect('*/*/');
	}



//look to http://stackoverflow.com/a/4353686/746758
//http://stackoverflow.com/a/19159776/746758
	public function resortAction($id=0) {
		$requestId = $this->getRequest()->getParam('id');
		$unsorted_id = ($id > 0) ? $id : $requestId;
		$affected = array();
		$starting = 1;
		if( $prod_id > 0 ) {
				$model = Mage::getModel('wsu_mediacontroll/unsorted');
				$model->load($unsorted_id);			
				$product = mage::getModel('catalog/product')->load($model['prod_id']);

				$attributes = $product->getTypeInstance(true)->getSetAttributes($product);
				$gallery = $attributes['media_gallery'];
				$images = $product->getMediaGalleryImages();

				$i=$starting;
				foreach ($images as $image) {
					if($image->getDisabled()!='1'){
						$backend = $gallery->getBackend();
						$backend->updateImage(
							$product,
							$image->getFile(),
							array('position' => $i)
						);
						Mage::getSingleton('adminhtml/session')->addSuccess(
							Mage::helper('mediacontroll')->__('Setting sort to '.$i.' for '.$image->getFile())
						);
						$i++;
					}
				}
			try {
				if($i>1){
					$product->getResource()->saveAttribute($product, 'media_gallery');
					$product->save();
					$model->setId($unsorted_id)->delete();
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