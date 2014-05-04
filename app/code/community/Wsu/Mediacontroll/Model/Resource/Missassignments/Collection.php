<?php
class Wsu_Mediacontroll_Model_Resource_Missassignments_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    protected function _construct() {
        $this->_init('wsu_mediacontroll/missassignments');
    }
	
	protected $total;
	public function getImages(){
		try {
			$this->setConnection($this->getResource()->getReadConnection());
			$this->getSelect()
				->from(array('main_table'=>$this->getTable('catalog/product_attribute_media_gallery')),'*')
				->group(array('value_id'));

			$array=array();
			foreach	($this->getData() as $item){
				$array[]	= $item['value'];
			}
		}catch(Exception $e){
			Mage::log($e->getMessage());
		}
		return $array;
	}
	
}
