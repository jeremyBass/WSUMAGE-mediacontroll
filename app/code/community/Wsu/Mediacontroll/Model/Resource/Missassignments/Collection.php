<?php
class Wsu_Mediacontroll_Model_Resource_Missassignments_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    protected function _construct() {
        $this->_init('wsu_mediacontroll/missassignments');
    }
	
	protected $total;

	public function getCache(){
		$collection = Mage::getModel('wsu_mediacontroll/missassignments')->getCollection();
		$array=array();
		foreach	($collection as $itemObj){
			$item=(array)$itemObj;
			$prod_id=$item['prod_id'];
			$array[$prod_id] = json_decode($item['imgprofile']);
		}

		return $array;
	}
}
