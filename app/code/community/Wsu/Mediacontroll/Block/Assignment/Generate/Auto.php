<?php
class Wsu_Mediacontroll_Block_Adminhtml_Generate_Auto extends Mage_Adminhtml_Block_Template {

    public function getGenerateUrl() {
        return $this->getUrl('*/mediacontroll/generateUrl');
    }

    public function getUrls() {
        $urls = Mage::getSingleton('core/session')->getAutoGenerateUrls();
        if ($urls) {
            $urls = array_values((array) $urls);
        }

        return $urls;
    }
}