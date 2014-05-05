<?php
class Wsu_Mediacontroll_Model_Resource_Orphaned extends Mage_Core_Model_Resource_Db_Abstract {
    protected function _construct() {    
        $this->_init('wsu_mediacontroll/orphaned', 'orphaned_id');
    }
}