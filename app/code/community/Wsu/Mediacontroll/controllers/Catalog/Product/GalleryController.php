<?php

require_once('Mage/Adminhtml/controllers/Catalog/Product/GalleryController.php');

class Wsu_Mediacontroll_Catalog_Product_GalleryController extends Mage_Adminhtml_Catalog_Product_GalleryController {


	public function uploadAction() {
        try {

			$fpResponse = Zend_Json::decode($this->getRequest()->getParam('fp_response'));

			$resultCollection = array();

			foreach ($fpResponse as $key => $fpImage) {

				$uploader = Mage::getModel('wsu_mediacontroll/fileUploader', array(
					'fileId' => 'image',
					'fpResponse' => $fpImage
				));
				$allowedImageTypes = array('jpg','jpeg','gif','png');
				$adapter =  Mage::getStoreConfig('design/watermark/image_adapter');
				if(strpos(strtolower($adapter),'imagemagic')>-1){
					$expandedTypes = array('tiff', 'tif', 'svg');
					$allowedImageTypes = array_merge($allowedImageTypes, $expandedTypes);
				}
				
				$uploader->setAllowedExtensions($allowedImageTypes);
				$uploader->addValidateCallback('catalog_product_image', Mage::helper('catalog/image'), 'validateUploadFile');
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(true);
				$result = $uploader->save(
					Mage::getSingleton('catalog/product_media_config')->getBaseTmpMediaPath()
				);

				Mage::dispatchEvent('catalog_product_gallery_upload_image_after', array(
					'result' => $result,
					'action' => $this
				));

				/**
				 * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
				 */
				$result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
				$result['path'] = str_replace(DS, "/", $result['path']);

				$result['url'] = Mage::getSingleton('catalog/product_media_config')->getTmpMediaUrl($result['file']);
				$result['file'] = $result['file'] . '.tmp';
				$result['cookie'] = array(
					'name'     => session_name(),
					'value'    => $this->_getSession()->getSessionId(),
					'lifetime' => $this->_getSession()->getCookieLifetime(),
					'path'     => $this->_getSession()->getCookiePath(),
					'domain'   => $this->_getSession()->getCookieDomain()
				);

				$resultCollection[] = $result;
			}
        } catch (Exception $e) {
            $result = array(
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode());
			$resultCollection[] = $result;
        }
		Mage::log($resultCollection);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($resultCollection));
    }

}
