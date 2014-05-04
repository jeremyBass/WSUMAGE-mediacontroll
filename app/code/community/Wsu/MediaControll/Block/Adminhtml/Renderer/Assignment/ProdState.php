<?php

class Wsu_Mediacontroll_Block_Adminhtml_Renderer_Assignment_ProdState extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
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

		$Imgprofile = $row->getData("imgprofile");
		$profile = (array)json_decode($Imgprofile);

		$prodImgProf = (array)$profile["productImageProfile"];
		//var_dump($prodImgProf);
		
		$missingAssigned = $prodImgProf['missingAssigned']?"true":"false";
		$hasAssigned = $prodImgProf['hasAssigned']?"true":"false";

		
		/*

		
		$format = ( $this->getColumn()->getFormat() ) ? $this->getColumn()->getFormat() : null;
        $defaultValue = $this->getColumn()->getDefault();
        if (is_null($format)) {
            // If no format and it column not filtered specified return data as is.
            $data = parent::_getValue($row);
            $string = is_null($data) ? $defaultValue : $data;
            $url	= htmlspecialchars($string);
        }
        elseif (preg_match_all($this->_variablePattern, $format, $matches)) {
            // Parsing of format string
            $formatedString = $format;
            foreach ($matches[0] as $matchIndex=>$match) {
                $value = $row->getData($matches[1][$matchIndex]);
                $formatedString = str_replace($match, $value, $formatedString);
            }
            $url	= $formatedString;
        } else {
            $url	= htmlspecialchars($format);
        }
		*/
		$location = Mage::getStoreConfig('web/secure/base_url');
		
		$html = "<ul>
			<li>Missing Assigned: ${missingAssigned}</li>
			<li>Has Assigned: ${hasAssigned}</li>
		</ul>";

		return $html;
	
	}
}