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
	protected $prodBasedImgCollection = null;

	public function halt_indexing(){
		$this->processes = Mage::getSingleton('index/indexer')->getProcessesCollection(); 
		$this->processes->walk('setMode', array(Mage_Index_Model_Process::MODE_MANUAL)); 
		$this->processes->walk('save'); 
	}
	public function run_indexer(){
		exec("php shell/indexer.php -reindexall");
	}	
	public function restore_indexing(){
		$this->run_indexer();
		$this->processes->walk('setMode', array(Mage_Index_Model_Process::MODE_REAL_TIME)); 
		$this->processes->walk('save'); 
	}
    /**
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest(){
        return Mage::app()->getRequest();
    }



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
	
	public function indexMissassignment() {
		
		try{
			$this->get_ProductImages(array('type'=>'missassignments'));
		} catch(Zend_Db_Exception $e){
		} catch(Exception $e){
			Mage::log($e->getMessage());
		}
	}

	public function indexUnsorted() {
		
		try{
			$this->get_ProductImages(array('type'=>'unsorted'));
		} catch(Zend_Db_Exception $e){
		} catch(Exception $e){
			Mage::log($e->getMessage());
		}
	}

	public function indexImgless() {
		
		try{
			$this->get_ProductImages(array('type'=>'imgless'));
		} catch(Zend_Db_Exception $e){
		} catch(Exception $e){
			Mage::log($e->getMessage());
		}
	}
	/**
	 * Creating new varien collection  
	 * for given array or object 
	 * 
	 * @param array|object $items   Any array or object 
	 * @return Varien_Data_Collection $collection 
	 */ 
	
	public function getVarienDataCollection($items) { 
		$collection = new Varien_Data_Collection();              
		foreach ($items as $item) { 
			$varienObject = new Varien_Object(); 
			$varienObject->setData($item); 
			$collection->addItem($varienObject); 
		} 
		return $collection; 
	} 

	
	
	
	
	
	
	
	
	
    /**
     * Get a collection of products that have images that are unassigned
     *	//@return array
     */
	public function get_ProductImages($params=array()){
		$time_start = microtime(true);
		if(empty($params)){
			$params = array(
				'json'=>false,
				'type'=>"",
				'limit'=>5,
				'offset'=>0,
				'id'=>0,
				'store'=>0
			);	
		}
		$json=isset($params['json'])?$params['json']:false;
		$type=isset($params['type'])?$params['type']:"";
		$limit=isset($params['limit'])?$params['limit']:5;
		$offset=isset($params['offset'])?$params['offset']:0;
		$store=isset($params['store'])?$params['store']:0;
		$id=isset($params['id'])?$params['id']:0;
		$status = "nothing done";
		$status_code=0;
		$obj ="";
		$prodcollection = Mage::getResourceModel('catalog/product_collection');
		if($id>0){
			
			
			
			$prodcollection->addAttributeToFilter('entity_id', array('eq' => $id));
			
			
			$typecollection = Mage::getModel('wsu_mediacontroll/'.$type)->getCollection()->addFieldToFilter('prod_id', array('eq' => $id));
			if($typecollection->getSize()>0){
				
				$items_profiles="";
				foreach	($typecollection->getData() as $itemObj){
					$item=(array)$itemObj;
					$items_profiles.=json_encode($item['imgprofile']);
				}
				$status_code=2;
				$status = "Item is already logged";
				$obj =$items_profiles;
				if($json){
					$time_end = microtime(true);
					return (object)array(
						"error"=>"",
						"status_code"=>$status_code,
						"status"=>$status,
						"object"=>$obj,
						"total_time"=>$time_end - $time_start
					);
				}
				
			}
			
			
		}else{
			if($store>0){
				Mage::app()->setCurrentStore($store); 
			}

			$model = Mage::getModel('wsu_mediacontroll/'.$type);	
			$collection = Mage::getModel('wsu_mediacontroll/'.$type)->getCollection();
			$val=array();
			$i=0;
			$tracked_products=array();
			foreach	($collection->getData() as $itemObj){
				$item=(array)$itemObj;
				$prod_id= $item['prod_id'];
				if(isset($item['imgprofile'])){
					$tracked_products[] = $prod_id;
				}
			}
			$prodcollection->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
			
			if(!empty($tracked_products)){
				$prodcollection->addAttributeToFilter('entity_id', array('nin' => $tracked_products));
			}

			if($limit>0 && $offset>0){
				$prodcollection->limit($limit, $offset);
			}
			$prodcollection->addStoreFilter();
		}
		
		$prodcollection->getSelect()->order('entity_id','ASC');
		//print( $prodcollection->getSelect() );//die();
		//var_dump(count($prodcollection));print('<br/>');
		
		if($type!='orphaned'){
		
			$productImgCollection=array();
			//$prodcollection = $this->prodBasedImgCollection->getSelect();
			//var_dump('here');var_dump($collection);
			if(!empty($prodcollection)){
				$status = "";

				foreach($prodcollection as $product){
					$prodID=(int)$product->getId();
	
					$productArray = $this->checkProductImgs($prodID);				
					$missingAssigned = $productArray['productImageProfile']['missingAssigned'];
					$missingSorted = $productArray['productImageProfile']['missingSorted'];
					$_prodImgObj = $productArray['productImageProfile']['imgs'];
					//var_dump(count($_prodImgObj)); print('<br/>');
					if( 
						   $type == 'imgless' && count($_prodImgObj)==0
						|| $type == 'missassignments' && $missingAssigned===true && count($_prodImgObj)>0
						|| $type == 'unsorted' && $missingSorted && count($_prodImgObj)>0
					){
						$newModel = Mage::getModel('wsu_mediacontroll/'.$type);	
						$newModel->setData(array('prod_id'=>$prodID,'imgprofile'=>json_encode($productArray)))->setId(null);
						$newModel->save();
						$status .= "logged as having issues";
						$status_code=0;
					}else{
						$status .= "found with no issues";
						$status_code=2;
					}
					$status .= " for ${type}";
					$obj .= json_encode($productArray);
					
				}
				
			}else{
				$status_code=1;
				$status .= " Cound not find found items";	
			}
		}
		if($type=='orphaned'){
			$model = Mage::getModel('wsu_mediacontroll/orphaned');	
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
		}//die('before final end');
		

		if($json){
			$time_end = microtime(true);
			return (object)array(
				"error"=>"",
				"status"=>$status,
				"status_code"=>$status_code,
				"object"=>$obj,
				"total_time"=>$time_end - $time_start
			);
		}
		
		
	}
	public function checkProductImgs($prodID){
		$sortIndex=0;
		$unsorted=false;
		//var_dump($prodID);
		$productArray=array();
		$_prod = Mage::getModel('catalog/product')->load($prodID);
		$_images = $_prod->getMediaGallery('images');
		
		
		$productArray['prod_id']= $prodID;
		$productArray['name']= $_prod->getName();

		$types=array();
		foreach ($_prod->getMediaAttributes() as $attribute) {
			$types[] = $attribute->getAttributeCode();
		}
		$productArray['avialible_types']=$types;
		$faillist=array();
		$attrImgs=array();
		foreach ($types as $typeof){
			$imgHelper = Mage::helper('catalog/image');
			$filename = "";
			try{
				$filename = Mage::helper('catalog/image')->init($_prod, $typeof);
				if(@file_exists($filename)===FALSE){
					$faillist[]=$filename;
					$filename = "";
				}
			}catch(Exception $e){}

			if ($filename!="") {
				$attrImgs[$typeof] = $filename."";
			}	
		}
		$productArray['nonexists']=$faillist;
		$productArray['types']=$attrImgs;

		$_assignCount = 0;
		$_sortedCount = 0;
		$_excluded = 0;

		$_prodImgObj = array();
		$_sortedArray=array();
		if(count($_images)){
			foreach ($_images as $_image){
				$_imgObj=array();
				$IMGID=$_image['value_id'];
				
				$_imgObj['id']=(int)$IMGID;

				$typed_as=array();
				$filenameTest = basename($_image['file'], ".jpg").'/';
				foreach ($attrImgs as $code=>$setFile){	
					if(strpos($setFile,$filenameTest)>-1){
						$typed_as[]=$code;
						$_assignCount++;
					}
				}
				
				$position=$_image['position'];
				$disabled=$_image['disabled'];
					$_imgObj['disabled']=$disabled;
					$_imgObj['position']=$position;
					$_imgObj['lable']=$_image['label'];
					$_imgObj['file']=$_image['file'];
					$_imgObj['typed_as']=$typed_as;
				if($disabled>0){
					$_excluded++;
				}
				if($position>-1){
					$_sortedArray[$IMGID]=$position;
					$_sortedCount++;
				}elseif(empty($position)||$position==""){
					$unsorted=true;	
				}
				$_prodImgObj[]=$_imgObj;
			}
		}
		
		$_sortIndexes=array();
		$_sortConflict=array();
		foreach($_sortedArray as $k=>$v){
			if(isset($_sortIndexes[$v])){
				unset($_sortedArray[$k]);
				$_sortConflict[$k]=$v;
			}else{
				$_sortIndexes[$v]=$k;	
			}
		}

		$missingSort = $_sortedCount>0 
						&& ( $_excluded>0 && $_excluded != count($_images) && $_excluded != $_assignCount )
						&& ( 
								count($_sortConflict) > 0 
							||	$_sortedCount != count($_images)
							||	count($_sortedArray) != count($_images) 
							||	!(
									count($_sortedArray) == count($_images) 
									&& $_sortedCount == count($_images)
									&& count($_sortedArray) == $_sortedCount
								)
							);
		$missingAssigned=true;

		if( ( count($productArray['types']) == count($productArray['avialible_types']) ) || ( $_assignCount>0 && $_assignCount==count($types) ) ){
			$missingAssigned=false;
		}


		$imgObj = array();
		$imgObj['missingSorted'] = $unsorted?true:$missingSort;
		$imgObj['hasSorted'] = $_sortedCount>0;
		$imgObj['hasSortIndexStart'] = isset($_sortIndexes[$sortIndex]);
		$imgObj['missingAssigned'] = $missingAssigned;
		$imgObj['hasAssigned'] = count($productArray['types'])>0 || $_assignCount>0;
		$imgObj['imgs'] =$_prodImgObj;
		
		$productArray['productImageProfile'] = $imgObj;
		return $productArray;
	}
}