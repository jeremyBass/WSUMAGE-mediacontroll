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
	public function get_ProductImages(){
		$collection = Mage::getModel('catalog/product')
						->getCollection()
						->addAttributeToSelect('image')
						->addAttributeToSelect('media_gallery');
		$totalProducts = 1000;
		$sortIndex=0;
		

		$productBasedImgCollection = Mage::getResourceModel('catalog/product_collection')
			//->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')
			//->addAttributeToFilter('category_id', array('in' => $cats))
			->addAttributeToFilter('small_image', array('neq' => ''))
			->addAttributeToFilter('small_image', array('neq' => 'no_selection'))
			->addAttributeToSelect('image')
			->addAttributeToSelect('media_gallery'); 
		//$productBasedImgCollection->getSelect()->order(new Zend_Db_Expr('RAND()'));
		$productBasedImgCollection->setPage(1,$totalProducts);	
		
		
		$productImgCollection=array();
		foreach($productBasedImgCollection as $product){
			
			$productArray=array();
			$_prod = Mage::getModel('catalog/product')->load($product->getId());
			$productArray['prod_id']= (int)$product->getId();
			$productArray['name']= $_prod->getName();
			
			$types=array();
			foreach ($_prod->getMediaAttributes() as $attribute) {
				$types[] = $attribute->getAttributeCode();
			}

			$attrImgs=array();
			foreach ($types as $type){
				$imgHelper = Mage::helper('catalog/image');
				$filename = "";
				try{
					$filename = Mage::helper('catalog/image')->init($_prod, $type);
				}catch(Exception $e){}

				if ($filename=="") {

				} else {
					$attrImgs[$type] = $filename."";
				}	
			}
			$productArray['types']=$attrImgs;
				
							
			$_images = $_prod->getMediaGallery('images');
			$_assignCount = 0;
			$_sortedCount = 0;

			$_prodImgObj = array();
			$_sortedArray=array();
			if(count($_images)){
				//echo "  IMG_GAL=>",PHP_EOL;
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
					
						$_imgObj['position']=$position;
						$_imgObj['lable']=$_image['label'];
						$_imgObj['file']=$_image['file'];
						$_imgObj['typed_as']=$typed_as;
					
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
			
			$missingSort = $_sortedCount>0 && ( count($_sortConflict)>0 || count($_sortedArray)!=count($_images) || $_sortedCount!=count($_images) );

			$imgObj = array();
			$imgObj['missingSorted'] = $missingSort;
			$imgObj['hasSorted'] = $_sortedCount>0;
			$imgObj['hasSortIndexStart'] = isset($_sortIndexes[$sortIndex]);
			$imgObj['missingAssigned'] = $_assignCount>0 && $_assignCount!=count($attrImgs);
			$imgObj['hasAssigned'] = $_assignCount>0;
			$imgObj['imgs'] =$_prodImgObj;
			
			$productArray['productImageProfile'] = $imgObj;

			$productImgCollection[]=$productArray;
			
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
		
		$data = $this->get_ProductImages();
		echo 'total products with images',' ',count($data), PHP_EOL;
		$_array = array_filter($data, function($val){
						return $val['productImageProfile']['missingAssigned'];
					});
		echo 'total products missing assigned',' ',count($_array), PHP_EOL;			
		var_dump($_array);die();
					
		return $_array;
	}
	
	/*
	* @return array of products that have images that are unsorted
	*/	
	public function get_ProductUnsortedImages(){
		$data = $this->get_ProductImages();
		echo 'total products with images',' ',count($data), PHP_EOL;
		$_array = array_filter($data, function($val){
						return $val['productImageProfile']['missingSorted'];
					});
		echo 'total products missing sorted',' ',count($_array), PHP_EOL;			
		var_dump($_array);die();
		
		return $_array;
	}

	
}