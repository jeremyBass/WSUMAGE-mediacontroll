<?php
class Wsu_Mediacontroll_Model_Resource_Orphaned_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    protected function _construct() {
        $this->_init('wsu_mediacontroll/orphaned');
    }
	
	protected $total;

	public function getCache(){
		$collection = Mage::getModel('wsu_mediacontroll/orphaned')->getCollection();
		$array=array();
		foreach	($collection as $itemObj){
			$array[] = $itemObj->getFilename();
		}
		return $array;
	}
}
