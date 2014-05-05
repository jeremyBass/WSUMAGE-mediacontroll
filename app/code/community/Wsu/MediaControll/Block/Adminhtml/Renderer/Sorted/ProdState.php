<?php

class Wsu_Mediacontroll_Block_Adminhtml_Renderer_Sorted_ProdState extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function _getValue(Varien_Object $row) {

		$Imgprofile = $row->getData("imgprofile");
		$profile = (array)json_decode($Imgprofile);

		$prodImgProf = (array)$profile["productImageProfile"];
		//var_dump($prodImgProf);
		
		$missingSorted = $prodImgProf['missingSorted']?"true":"false";
		$hasSorted = $prodImgProf['hasSorted']?"true":"false";
		$hasSortIndexStart = $prodImgProf['hasSortIndexStart']?"true":"false";

		$location = Mage::getStoreConfig('web/secure/base_url');
		
		$html = "<ul>
			<li>Missing Sorted: ${missingSorted}</li>
			<li>Has Sorted: ${hasSorted}</li>
			<li>Sort Index Start @: ${hasSortIndexStart}</li>
		</ul>";

		return $html;
	
	}
}