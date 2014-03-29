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
     * Get a collection of products that have images that are unassigned
     *	//@return array
     *	$tmp = array(
     *		$prodId => array(
     *			"hasAssigned"=>True/False, //were there any images for this product assigned
     *			"missingAssigned"=>True/False, //where there any images for this product unassigned
     *			"hasSorted"=>True/False, //where there any images for this product sorted
     *			"missingSorted"=>True/False, //where there any images for this product unsorted
     *			"images"=> array(
     *				$imgID => array(
     *					"isSorted" => True/False, //does this image have a sort value of > 1
     *					"isAssgined" => True/False //does this image have an assignment like "Base Image"
	 * 					"object" => Mage_Catalog_Model_Product_Image
     *				)
     *			)
     *		)
     *	);
     */
	public function get_ProductImages(){
		$collection = Mage::getModel('catalog/product')
						->getCollection()
						->addAttributeToSelect('image')
						->addAttributeToSelect('media_gallery');
		$totalProducts = 1000;

		$productBasedImgCollection = Mage::getResourceModel('catalog/product_collection')
			//->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')
			//->addAttributeToFilter('category_id', array('in' => $cats))
			->addAttributeToFilter('small_image', array('neq' => ''))
			->addAttributeToFilter('small_image', array('neq' => 'no_selection'))
			->addAttributeToSelect('image')
			->addAttributeToSelect('media_gallery'); 
		//$productBasedImgCollection->getSelect()->order(new Zend_Db_Expr('RAND()'));
		$productBasedImgCollection->setPage(1,$totalProducts);	
		
		
		
		foreach($productBasedImgCollection as $product){
			$imgObj = array();
			$_prod = Mage::getModel('catalog/product')->load($product->getId());
			$types=array();
			foreach ($_prod->getMediaAttributes() as $attribute) {
				$types[] = $attribute->getAttributeCode();
			}
			
			echo "PROD", PHP_EOL;
			echo  "id=>",$product->getId(), PHP_EOL;
			echo  "imageTypes=>",$product->getId(), PHP_EOL;

			//var_dump($imageTypes);
			$attrImgs=array();
			foreach ($types as $type){
				$imgHelper = Mage::helper('catalog/image');
				$filename = "";
				try{
					$filename = Mage::helper('catalog/image')->init($_prod, $type);
				}catch(Exception $e){}
				echo " ==>",$type, PHP_EOL;
				if ($filename=="") {
					echo " 	==>","NULL", PHP_EOL;
				} else {
					$attrImgs[$type] = $filename."";
					echo " 	==>",$filename, PHP_EOL;
				}	
			}
							
			$_images = $_prod->getMediaGalleryImages();
			$_assignCount = 0;
			$_sortedCount = 0;

			if(count($_images)){
				echo "  IMG_GAL=>",PHP_EOL;
				foreach ($_images as $_image){

					echo "    {",PHP_EOL;
					echo "       ID==>",$_image->getId(), PHP_EOL;
					
					$filenameTest = basename($_image->getFile(), ".jpg").'/';
					foreach ($attrImgs as $code=>$setFile){	
						if(strpos($setFile,$filenameTest)>-1){
							echo "       ${code}==>","ture", PHP_EOL;
							$_assignCount++;
						}
					}
					echo "       File==>",$_image->getFile(), PHP_EOL;
					echo "       getGalleryUrl==>",$_image->getGalleryUrl(), PHP_EOL;
					echo "       LABLE==>",$_image->getImageLabel(), PHP_EOL;
					echo "       POSITION==>",$_image->getPosition(), PHP_EOL;
					
					if($_image->getPosition()>-1){
						$_sortedCount++;
					}

					//var_dump($_image);
					echo "    }",PHP_EOL;
				}
			}
			$imgObj['missingSorted'] = $_sortedCount>0 && $_sortedCount!=count($_images);
			$imgObj['hasSorted'] = $_sortedCount>0;
			$imgObj['missingAssigned'] = $_assignCount>0 && $_sortedCount!=count($attrImgs);
			$imgObj['hasAssigned'] = $_assignCount>0;	
			var_dump($imgObj);//die();
						
			//$img = Mage::helper('catalog/image')->init($product, 'image');
			//var_dump($img);die();
			
			//echo "IMG=>",$img->getLable(), PHP_EOL;
			
			
		}die();
		return $productBasedImgCollection;
	}
	/*
	* @return array of products that have images that are unassigned
	*/	
	public function get_ProductUnassignedImages(){
		
		$data = $this->get_ProductImages();
		
		var_dump($data);die();
		
		
		$_array = array_filter($data, function ($item) use ($data) {
			$prodImgs = $item["images"];
			$_prop_array = array_filter($prodImgs, function ($prop) use ($prodImgs) {
				return strlen($prop["object"]->getLable())>1;
			});
			return !empty($_prop_array);
		});
		return $_array;
	}
	/*
	* @return array of products that have images that are unsorted
	*/	
	public function get_ProductUnsortedImages(){
		$data = $this->get_ProductImages();
		$_array = array_filter($data, function ($item) use ($data) {
			$prodImgs = $item["images"];
			$_prop_array = array_filter($prodImgs, function ($prop) use ($prodImgs) {
				return strlen($prop["object"]->getLable())>1;
			});
			return !empty($_prop_array);
		});
		return $_array;
	}
	/*
	* @return array of products that have images that are missing a lable
	*/
	public function get_ProductUnlabledImages(){
		$data = $this->get_ProductImages();
		$_array = array_filter($data, function ($item) use ($data) {
			$prodImgs = $item["images"];
			$_prop_array = array_filter($prodImgs, function ($prop) use ($prodImgs) {
				return strlen($prop["object"]->getLable())>1;
			});
			return !empty($_prop_array);
		});
		return $_array;
	}
	
	
	
	
}