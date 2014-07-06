<?php
class Wsu_Mediacontroll_Model_Source_Image_Adapter {
    /**
     * get Authtypes as Option Array
     *
     * @return array
     */
    public function toOptionArray() {
		$options = array(
            array(
                'value' => Varien_Image_Adapter::ADAPTER_GD2,
                'label' => Mage::helper('mediacontroll')->__('GD2 Adapter')
            )
        );
		if(extension_loaded('imagick')){
			$options[] = array(
					'value' => Varien_Image_Adapter::ADAPTER_IM,
					'label' => Mage::helper('mediacontroll')->__('Imagemagick Adapter')
				);
		}
        return $options;
    }
}
