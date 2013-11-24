<?php
class Wsu_Mediacontroll_Model_Resource_Imgclean extends Mage_Core_Model_Resource_Db_Abstract {
    protected function _construct() {    
        $this->_init('wsu_mediacontroll/imgclean', 'mediacontroll_id');
    }
}