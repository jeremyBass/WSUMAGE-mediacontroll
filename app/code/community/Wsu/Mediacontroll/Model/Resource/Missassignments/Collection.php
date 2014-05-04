<?php
class Wsu_Mediacontroll_Model_Resource_Missassignments_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    protected function _construct() {
        $this->_init('wsu_mediacontroll/missassignments');
    }
	
	protected $total;

	public function getCache(){
		try {
			$this->setConnection($this->getResource()->getReadConnection());
			$this->getSelect();

			$array=array();
			foreach	($this->getData() as $item){
				$prod_id=$item['prod_id'];
				$array[$prod_id] = json_decode($item['imgprofile']);
			}
		}catch(Exception $e){
			Mage::log($e->getMessage());
		}
		return $array;
	}
}
