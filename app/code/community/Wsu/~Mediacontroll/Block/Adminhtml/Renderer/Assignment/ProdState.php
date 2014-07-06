<?php

class Wsu_Mediacontroll_Block_Adminhtml_Renderer_Assignment_ProdState extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    public function _getValue(Varien_Object $row) {

		$Imgprofile = $row->getData("imgprofile");
		$profile = (array)json_decode($Imgprofile);

		$prodImgProf = (array)$profile["productImageProfile"];
		//var_dump($prodImgProf);
		
		$missingAssigned = $prodImgProf['missingAssigned']?"true":"false";
		$hasAssigned = $prodImgProf['hasAssigned']?"true":"false";

		$location = Mage::getStoreConfig('web/secure/base_url');
		
		$html = "<ul>
			<li>Missing Assigned: ${missingAssigned}</li>
			<li>Has Assigned: ${hasAssigned}</li>
		</ul>";

		return $html;
	
	}
}