<?php
class Wsu_Mediacontroll_Model_Resource_Imgless_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    protected function _construct() {
        $this->_init('wsu_mediacontroll/imgless');
    }
	
	protected $total;

	public function getCache(){
		$collection = Mage::getModel('wsu_mediacontroll/imgless')->getCollection();
		$array=array();
		foreach	($collection as $itemObj){
			$array[] = $itemObj->getFile();
		}
		return $array;
	}
}
