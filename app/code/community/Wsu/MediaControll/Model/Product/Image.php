<?php
class Wsu_Mediacontroll_Model_Product_Image extends Mage_Catalog_Model_Product_Image {
    private $_disableMemoryCheck = true;
    /**
     * @return bool
     */
    public function getDisableMemoryCheck() {
        return $this->_disableMemoryCheck;
    }
    /**
     * @param $disableMemoryCheck
     */
    public function setDisableMemoryCheck($disableMemoryCheck) {
        $this->_disableMemoryCheck = $disableMemoryCheck;
    }
    /**
     * Overwriten to choose dynamically the image processor.
     * @return Varien_Image
     */
    public function getImageProcessor() {
        if (!$this->_processor) {
            $this->_processor = new Varien_Image($this->getBaseFile(), Mage::getStoreConfig('design/watermark/image_adapter'));
        }
        $this->_processor->keepAspectRatio($this->_keepAspectRatio);
        $this->_processor->keepFrame($this->_keepFrame);
        $this->_processor->keepTransparency($this->_keepTransparency);
        $this->_processor->constrainOnly($this->_constrainOnly);
        $this->_processor->backgroundColor($this->_backgroundColor);
        $this->_processor->quality($this->_quality);
        return $this->_processor;
    }
    /**
     * @param null $file
     * @return bool
     */
    protected function _checkMemory($file = null) {
        if ($this->getDisableMemoryCheck()) {
            return true;
        } else {
            return parent::_checkMemory($file = null);
        }
    }
	
	
	
    /**
     * This will be used as the file name in the cache
     *
     * @var string
     */
    protected $_niceCacheName = '';
    /**
     * Set the cache file base name
     *
     * @param string $name
     * @see Wsu_Mediacontroll_Helper_Image::init()
     */
    public function setNiceCacheName($name) {
        $this->_niceCacheName = $name;
    }
    /**
     * Return the cache file base name
     *
     * @return string
     * @see Wsu_Mediacontroll_Helper_Image::init()
     */
    public function getNiceCacheName() {
        return $this->_niceCacheName;
    }
    /**
     * Set filenames for base file and new file
     *
     * @param string $file
     * @return Mage_Catalog_Model_Product_Image
     * @throws Mage_Core_Exception
     */
    public function setBaseFile($file) {
        parent::setBaseFile($file);
        if (!Mage::getStoreConfig("catalog/nicerimagenames/disable_ext")) {
            // The $_newFile property is set during parent::setBaseFile()
            list($path, $file) = $this->_getFilePathAndName($this->_newFile);
            $file = $this->_getNiceFileName($file);
            if (Mage::getStoreConfig("catalog/nicerimagenames/lowercase")) {
                $file = strtolower($file);
            }
            $this->_newFile = $path . $file; // the $file contains heading slash
        }
        return $this;
    }
    /**
     * Return the file path and file name as an array
     * 
     * @param $file
     * @return array
     */
    protected function _getFilePathAndName($file) {
        $path = dirname($file);
        $file = '/' . basename($file);
        return array(
            $path,
            $file
        );
    }
    /**
     * Return the filename with the correct number
     *
     * @param string $file
     * @return string
     */
    protected function _getNiceFileName($file) {
        // add the image name without the file type extension to the image cache path
        $pos       = strrpos($file, '.');
        $pathExt   = substr($file, 1, $pos - 1);
        $extension = substr($file, $pos + 1);
        $file      = $this->getNiceCacheName();
        return sprintf('/%s/%s.%s', $pathExt, $file, $extension);
    }
	
	
}
