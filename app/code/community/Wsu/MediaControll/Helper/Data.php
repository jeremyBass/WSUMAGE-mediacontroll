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
			$this->get_ProductImages('unassigned');
		} catch(Zend_Db_Exception $e){
		} catch(Exception $e){
			Mage::log($e->getMessage());
		}
	}

	public function indexUnsorted() {
		try{
			$this->get_ProductImages('unsorted');
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
	public function get_ProductImages($type=""){
		if($type=='unassigned'){
			$productBasedImgCollection = Mage::getResourceModel('catalog/product_collection')
			->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
			$productBasedImgCollection->getSelect()->order('updated_at','ASC');
			//print( $productBasedImgCollection->getSelect() );die();
		}else{
			$productBasedImgCollection = Mage::getResourceModel('catalog/product_collection')
				->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
				//->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')
				//->addAttributeToFilter('category_id', array('in' => $cats))
				//->addAttributeToFilter('small_image', array('neq' => ''))
				//->addAttributeToFilter('small_image', array('neq' => 'no_selection'))
				//->addAttributeToSelect('image')
				//->addAttributeToSelect('media_gallery'); 
			//$productBasedImgCollection->getSelect()->order(new Zend_Db_Expr('RAND()'));
			$productBasedImgCollection->getSelect()->order('updated_at','DESC');
			//$productBasedImgCollection->getSelect()->limit($totalProducts,$page);
		}

		if($type=='unassigned'){
			$model = Mage::getModel('wsu_mediacontroll/missassignments');	
			$collection = Mage::getModel('wsu_mediacontroll/missassignments')->getCollection();
			$val=array();
			foreach	($collection->getData() as $itemObj){
				$item=(array)$itemObj;
				$prod_id=$item['prod_id'];
				$val[$prod_id] = json_decode($item['imgprofile']);
			}
			$tracked_products = array_keys($val);
		}
		//var_dump(count($productBasedImgCollection));print('<br/>');
		$productImgCollection=array();
		foreach($productBasedImgCollection as $product){
			$prodID=(int)$product->getId();
			var_dump($prodID);print('<br/>');
			//var_dump($tracked_products);
			//print('--------------------');
			if(empty($tracked_products) || !array_key_exists($prodID,$tracked_products)){
				
				$productArray=array();
				$_prod = Mage::getModel('catalog/product')->load($prodID);
				$_images = $_prod->getMediaGallery('images');
				
				
				$productArray['prod_id']= (int)$product->getId();
				$productArray['name']= $_prod->getName();
				//print($productArray['name']);print('<br/>');
				//print('--------------------');print('<br/>');
				$types=array();
				foreach ($_prod->getMediaAttributes() as $attribute) {
					$types[] = $attribute->getAttributeCode();
				}
				$productArray['avialible_types']=$types;
				$attrImgs=array();
				foreach ($types as $typeof){
					$imgHelper = Mage::helper('catalog/image');
					$filename = "";
					try{
						$filename = Mage::helper('catalog/image')->init($_prod, $typeof);
					}catch(Exception $e){}
	
					if ($filename!="") {
						$attrImgs[$typeof] = $filename."";
					}	
				}
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
				if($_assignCount>0){
					if($_assignCount==count($types))$missingAssigned=false;
				}


				$imgObj = array();
				$imgObj['missingSorted'] = $missingSort;
				$imgObj['hasSorted'] = $_sortedCount>0;
				$imgObj['hasSortIndexStart'] = isset($_sortIndexes[$sortIndex]);
				$imgObj['missingAssigned'] = $missingAssigned;
				$imgObj['hasAssigned'] = $_assignCount>0;
				$imgObj['imgs'] =$_prodImgObj;
				
				$productArray['productImageProfile'] = $imgObj;

				if($type=='unassigned'){
					if( $missingAssigned && count($_prodImgObj)>0 ){
						$newModel = Mage::getModel('wsu_mediacontroll/missassignments');	
						$newModel->setData(array('prod_id'=>$prodID,'imgprofile'=>json_encode($productArray)))->setId(null);
						$newModel->save();
					}
				}
				if($type=='unsorted'){
					if( $missingSort && count($_prodImgObj)>0 ){
						$newModel = Mage::getModel('wsu_mediacontroll/missassignments');	
						$newModel->setData(array('prod_id'=>$prodID,'imgprofile'=>json_encode($productArray)))->setId(null);
						$newModel->save();
					}
				}
				
				
				
				
			}//die('hit');
		}
		/*
		echo 'total products with images',' ',count($productImgCollection), PHP_EOL;
		
		echo 'total products missing starting sort idexes',' ',count(array_filter($productImgCollection, function($val){
						return $val['productImageProfile']['hasSortIndexStart'];
					})
				), PHP_EOL;
		echo 'total products missing sorted',' ',count(array_filter($productImgCollection, function($val){
						return $val['productImageProfile']['missingSorted'];
					})
				), PHP_EOL;
		echo 'total products missing assigned',' ',count(array_filter($productImgCollection, function($val){
						return $val['productImageProfile']['missingAssigned'];
					})
				), PHP_EOL;
		*/
		return $productImgCollection;
	}
	
	/*
	* @return array of products that have images that are unassigned
	*/	
	public function get_ProductUnassignedImages(){
		
		$data = $this->get_ProductImages('unassigned');

		$_array = array_filter($data, function($val){
						return $val['productImageProfile']['missingAssigned'] && count($val['productImageProfile']['imgs'])>0;
					});
		return $_array;
	}
	
	/*
	* @return array of products that have images that are unsorted
	*/	
	public function get_ProductUnsortedImages(){
		$data = $this->get_ProductImages();

		$_array = array_filter($data, function($val){
						return $val['productImageProfile']['missingSorted'] && count($val['productImageProfile']['imgs'])>0;
					});

		return $_array;
	}


	/*
	* @return array of products that have images that are unsorted
	*/	
	public function get_ProductNoImages(){
		$data = $this->get_ProductImages();

		$_array = array_filter($data, function($val){
						return count($val['productImageProfile']['imgs'])<=0;
					});
		return $_array;
	}


	
}