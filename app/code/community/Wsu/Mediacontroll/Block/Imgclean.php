<?php
class Wsu_Mediacontroll_Block_Imgclean extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getImgclean()     
     { 
        if (!$this->hasData('imgclean')) {
            $this->setData('imgclean', Mage::registry('imgclean'));
        }
        return $this->getData('imgclean');
        
    }
}