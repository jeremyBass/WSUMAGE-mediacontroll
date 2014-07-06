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

	public function searchAction(){
		Mage::helper('mediacontroll')->indexMissassignment();
		$this->_redirect('*/*/');
	}

    public function generateAction() {
        $storeId = $this->getRequest()->getParam('store_id');
        $store = Mage::app()->getStore($storeId);
        $session = Mage::getSingleton('adminhtml/session');

        /*if (!$storeId || !$store->getId() || !$store->getIsActive()) {
            $session->addError('Please specify a valid store for list generation');
        }*/
		//would be setting this as option
		Mage::register('set_params', array(
			'json'=>true,
			'type'=>"missassignments",
			'limit'=>5,
			'offset'=>0,
			'store'=>$store
		));
        $this->loadLayout();
        $this->renderLayout();
    }

    public function generateListItemAction() {
		
		$defaults = Mage::registry('set_params');
        $params = array(
			'json'=>$this->getRequest()->getParam('json',$defaults['json']),
        	'type'=>$this->getRequest()->getParam('type',$defaults['type']),
        	'limit'=>$this->getRequest()->getParam('limit',$defaults['limit']),
        	'offset'=>$this->getRequest()->getParam('offset',$defaults['offset']),
        	'store'=>$this->getRequest()->getParam('store',$defaults['store'])
		);

        $result = Mage::helper('mediacontroll')->get_ProductImages($params);

        return $this->getResponse()
            ->setHeader('Content-type', 'application/json')
            ->setBody(Mage::helper('core')->jsonEncode($result));
    }

//look to http://stackoverflow.com/a/4353686/746758
//http://stackoverflow.com/a/19159776/746758
	public function assignmentAction($id=0) {
		$requestId = $this->getRequest()->getParam('id');
		$missassignments_id = ($id > 0) ? $id : $requestId;
		$affected = array();
		$starting = 1;
		if( $missassignments_id > 0 ) {
				$model = Mage::getModel('wsu_mediacontroll/missassignments');
				$model->load($missassignments_id);			
				$product = mage::getModel('catalog/product')->load($model['prod_id']);
				//$images = $product->getMediaGalleryImages();
				
				$profile = (array)json_decode($model->getImgprofile());
				$profile = (array)$profile['productImageProfile'];
				$images = (array)$profile['imgs'];
			try {
				if(count($images)>0){
						//this needs to be looped over on the type.. not hard coded
						$image = (array)$images[0];
						
						//if(!$product->hasImage()){
							$product->setImage($image['file']);
							$product->getResource()->saveAttribute($product, 'image');
						//}
						//if(!$product->hasSmallImage()){
							$product->setSmallImage($image['file']);
							$product->getResource()->saveAttribute($product, 'small_image');
						//}
						//if(!$product->hasThumbnail()){
							$product->setThumbnail($image['file']);
							$product->getResource()->saveAttribute($product, 'thumbnail');
						//}
						//
						
						$product->save();
						$model->setId($missassignments_id)->delete();

						if($requestId>0){
							Mage::getSingleton('adminhtml/session')->addSuccess(
								Mage::helper('mediacontroll')->__('Product Images were successfully assigned to '.$path)
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
				//Mage::helper('mediacontroll')->halt_indexing();
                foreach ($mediacontrollIds as $mediacontrollId) {
					$this->assignmentAction($mediacontrollId);
                }
				
				//Mage::helper('mediacontroll')->run_indexer();
				//Mage::helper('mediacontroll')->restore_indexing();
				
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