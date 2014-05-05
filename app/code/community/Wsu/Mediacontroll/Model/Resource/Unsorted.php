<?php
class Wsu_Mediacontroll_Model_Resource_Unsorted extends Mage_Core_Model_Resource_Db_Abstract {
    protected function _construct() {    
        $this->_init('wsu_mediacontroll/unsorted', 'unsorted_id');
    }
}