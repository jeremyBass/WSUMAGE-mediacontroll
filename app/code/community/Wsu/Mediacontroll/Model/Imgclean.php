<?php

class Wsu_Mediacontroll_Model_Imgclean extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mediacontroll/imgclean');
    }
}