<?php
class Wsu_Mediacontroll_Block_Assignment_Generate_Auto extends Mage_Adminhtml_Block_Template {

    public function getGenerateUrl() {
        return $this->getUrl('*/assignment/generateUrl');
    }

    public function getUrls() {
        $urls = Mage::getSingleton('core/session')->getAutoGenerateUrls();
        if ($urls) {
            $urls = array_values((array) $urls);
        }

        return $urls;
    }
}