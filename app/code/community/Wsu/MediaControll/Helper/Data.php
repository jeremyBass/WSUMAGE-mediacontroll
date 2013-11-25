<?php
class Wsu_Mediacontroll_Helper_Data extends Mage_Core_Helper_Abstract {
	/**
     * @param $value
     * @return Varien_Image_Adapter_Gd|Varien_Image_Adapter_Gd2|Varien_Image_Adapter_Imagemagic|Varien_Image_Adapter_ImagemagicExternal
     */
    public function getImageAdapter($value){
        return Varien_Image_Adapter::factory($value);
    }

	protected $result = array();
	protected $_mainTable;
	public $valdir = array();

	public function listDirectories($path){
		if(is_dir($path)){
			if ($dir = opendir($path)) {
				while (($entry = readdir($dir)) !== false) {
					if(preg_match('/^\./',$entry) != 1){
						if (is_dir($path . DS . $entry) && !in_array($entry,array('cache','watermark')) ){
							$this->listDirectories($path . DS . $entry);
						} elseif(!in_array($entry,array('cache','watermark')) && (strpos($entry,'.') != 0)) {
							$this->result[] = substr($path . DS . $entry, 21);
						}
					}
				}
				closedir($dir);
			}
		}
		return $this->result;
	}
	
	
	public function compareList() {
		$model = Mage::getModel('wsu_mediacontroll/imgclean');	
		$val	= $model->getCollection()->getImages();
		$prodImg = 'media' . DS . 'catalog' . DS . 'product';
		$imgList = $this->listDirectories($prodImg);
		foreach ($imgList as $item){
			try{
				$item	= strtr($item,'\\','/');
				if(!in_array($item, $val)){
					$valdir[]['filename'] = $item;
					$model->setData(array('filename'=>$item))->setId(null);
					$model->save();
				}
			} catch(Zend_Db_Exception $e){
			} catch(Exception $e){
				Mage::log($e->getMessage());
			}
		}
	}
}