<?php
class Wsu_Mediacontroll_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config {
    public function testCatalogProductImageRewriteConfigurationDefined() {
        $this->assertModelAlias('catalog/product_image', 'Wsu_Mediacontroll_Model_Product_Image');
    }
    public function testWatermarkModelConfigurationDefined() {
        $this->assertModelAlias('wsu_mediacontroll/source_image_adapter', 'Wsu_Mediacontroll_Model_Source_Image_Adapter');
    }
    public function testWatermarkHelperConfigurationDefined() {
        $this->assertHelperAlias('wsu_mediacontroll', 'Wsu_Mediacontroll_Helper_Data');
    }
}
