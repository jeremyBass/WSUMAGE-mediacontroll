<?php

class Wsu_Mediacontroll_Block_Adminhtml_Renderer_Sorted_Name extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function _getValue(Varien_Object $row) {
		$Imgprofile = $row->getData("imgprofile");
		$profile = (array)json_decode($Imgprofile);
		$html= $profile['name'];
		return $html;
	
	}
}