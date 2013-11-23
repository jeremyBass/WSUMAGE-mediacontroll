<?php

class Wsu_Mediacontroll_Model_Mysql4_Imgclean extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the mediacontroll_id refers to the key field in your database table.
        $this->_init('mediacontroll/imgclean', 'mediacontroll_id');
    }
	
}