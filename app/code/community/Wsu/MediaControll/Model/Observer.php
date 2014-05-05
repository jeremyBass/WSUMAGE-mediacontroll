<?php
class Wsu_Mediacontroll_Model_Observer {

	public function checkProductChange($observer) {
        /**
         * @var $product Mage_Catalog_Model_Product
         * @var $user    Mage_Admin_Model_User
         */
        $product = $observer->getEvent()->getProduct();
		if ($product->hasDataChanges()) {

        }
	}



}