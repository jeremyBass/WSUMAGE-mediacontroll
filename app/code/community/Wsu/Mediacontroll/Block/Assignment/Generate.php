<?php
class Wsu_Mediacontroll_Block_Assignment_Generate extends Mage_Adminhtml_Block_Template {
    public function getStore() {
        return Mage::registry('store');
    }

    public function getGenerateUrl() {
        return $this->getUrl('*/*/generateListItem');
    }


    public function getProducts() {
		$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('entity_id');
        return (array)$collection;
    }



}