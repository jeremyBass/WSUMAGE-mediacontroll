<?php

class Wsu_Mediacontroll_Block_Adminhtml_Renderer_ProdImgState extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    /**
     * Format variables pattern
     *
     * @var string
     */
    protected $_variablePattern = '/\\$([a-z0-9_]+)/i';

    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return mixed
     */
    public function _getValue(Varien_Object $row) {
		
		$prodImgProf = $row->getData("productImageProfile");
		$location = Mage::getStoreConfig('web/secure/base_url');
		//var_dump($prodImgProf);
		$html = "<ul>";
		foreach($prodImgProf["imgs"] as $img){
			$imgfile = $img['file'];
			$imgposition = $img['position'];
			$html .= "<li>
					<a style='width:50px; height:75px; display:inline-block;'>
						<img src='${location}media/catalog/product{$imgfile}' tile='img_a.jgp' style='width:100%;'/>
					</a>
					<ul style='display:inline-block;'>
						<li>Sort: ${imgposition}</li>";
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