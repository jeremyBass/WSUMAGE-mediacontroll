<?php
class Wsu_MediaControll_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config {
    public function testCatalogProductImageRewriteConfigurationDefined() {
        $this->assertModelAlias('catalog/product_image', 'Wsu_MediaControll_Model_Product_Image');
    }
    public function testWatermarkModelConfigurationDefined() {
        $this->assertModelAlias('wsu_mediacontroll/source_image_adapter', 'Wsu_MediaControll_Model_Source_Image_Adapter');
    }
    public function testWatermarkHelperConfigurationDefined() {
        $this->assertHelperAlias('wsu_mediacontroll', 'Wsu_MediaControll_Helper_Data');
    }
}
