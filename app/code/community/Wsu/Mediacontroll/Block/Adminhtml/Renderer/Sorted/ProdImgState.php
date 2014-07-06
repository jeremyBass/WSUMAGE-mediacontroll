<?php

class Wsu_Mediacontroll_Block_Adminhtml_Renderer_Sorted_ProdImgState extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    public function _getValue(Varien_Object $row) {
		$Imgprofile = $row->getData("imgprofile");
		$profile = (array)json_decode($Imgprofile);
		$prodImgProf = (array)$profile["productImageProfile"];
		$location = Mage::getStoreConfig('web/unsecure/base_url');
		//var_dump($prodImgProf);
		$html = "<ul>";
		$prodImgProf_array=(array)$prodImgProf["imgs"];
		foreach($prodImgProf_array as $imgItem){
			$img=(array)$imgItem;
			$imgfile = $img['file'];
			$imgposition = $img['position'];
			$disabled = $img['disabled']?"Excluded":"available"; 
			$html .= "<li>
					<a style='width:25px; height:25px; display:inline-block;'>
						<b style='font-size:15px' data-img='${location}media/catalog/product{$imgfile}'>[=]</b>
					</a>
					<ul style='display:inline-block;'>
						<li>Sort: ${imgposition}</li>";
						
					$html .= "<li>${disabled}</li>";
						
			if(count($img['typed_as'])>0){
				$html .= "<li>Assigned as:
								<ul>";
				foreach($img["typed_as"] as $type){		
					$html .= "<li>${type}</li>";
				}
				$html .= "
							</ul>
						</li>";
			}else{
				$html .= "<li>Is not assigned</li>";
			}				
			$html .= "	
						
					</ul>
				</li>";
		}			
		$html .= "</ul>";

		return $html;
	}
}