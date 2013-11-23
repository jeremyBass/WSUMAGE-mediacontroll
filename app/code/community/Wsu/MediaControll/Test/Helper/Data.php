<?php
class Wsu_Mediacontroll_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case_Config {
    /**
     * @var Wsu_Mediacontroll_Helper_Data
     */
    protected $_helper;
    public function setUp() {
        $this->_helper = Mage::helper('wsu_mediacontroll');
    }
    public function testGetImageAdapter() {
        $this->assertInstanceOf('Varien_Image_Adapter_Gd2', $this->_helper->getImageAdapter(Varien_Image_Adapter::ADAPTER_GD2));
        $this->assertInstanceOf('Varien_Image_Adapter_Imagemagic', $this->_helper->getImageAdapter(Varien_Image_Adapter::ADAPTER_IM));
    }
}
