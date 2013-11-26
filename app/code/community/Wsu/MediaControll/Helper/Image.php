<?php
class Wsu_Mediacontroll_Helper_Image extends Mage_Catalog_Helper_Image {
    /**
     * Add the nice cache name to the image model
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $attributeName One of 'image', 'small_image' or 'thumbnail'
     * @param string $imageFile
     * @return Wsu_Mediacontroll_Helper_Image
     */
    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null) {
        parent::init($product, $attributeName, $imageFile);
        if (!Mage::getStoreConfig("catalog/seoimagenames/disable_ext")) {
            $this->_getModel()->setNiceCacheName($this->_getGeneratedNameForImageAttribute($attributeName));
        }
        if (Mage::getStoreConfig("catalog/seoimagenames/generate_labels")) {
            $this->_setSeoImageLabels($attributeName);
        }
        return $this;
    }
    /**
     * Set the label on the product for the given image type, and also on the gallery images
     * 
     * @param string $attributeName One of 'image' or 'thumbnail' or 'small_image'
     */
    protected function _setSeoImageLabels($attributeName) {
        $map   = $this->_getNiceLabelMap();
        $label = $this->_getGeneratedNameForImageAttribute($attributeName, $map, false);
        // Set it on the product (used for the main product image in the view media template)
        $key   = $attributeName . '_label';
        if (!$this->getProduct()->getData($key)) {
            $this->getProduct()->setData($attributeName . '_label', $label);
            // Set label as info on the image gallery
            if ($gallery = $this->getProduct()->getMediaGalleryImages()) {
                // Usually 'thumbnail' but hardcoded in .phtml so faking it here with a "special" name
                $galleryImage = 'gallery';
                $label        = $this->_getGeneratedNameForImageAttribute($galleryImage, $map, false);
                foreach ($gallery as $image) {
                    if (!$image->getLabel()) {
                        $image->setLabel($label);
                    }
                }
            }
        }
    }
    /**
     * Return the label name template according to the config settings
     * 
     * @return string
     */
    protected function _getNiceLabelMap() {
        if (Mage::getStoreConfig("catalog/seoimagenames/use_filename_map_for_labels")) {
            $map = Mage::getStoreConfig("catalog/seoimagenames/map");
        } else {
            $map = Mage::getStoreConfig("catalog/seoimagenames/label_map");
        }
        return $map;
    }
    /**
     * Build the nice image cache name from the config setting
     *
     * @param string $attributeName One of 'image', 'small_image' or 'thumbnail'
     * @param string $map The template to use to generate the value
     * @param bool $forFiles Should the returned value be usable as a file name  
     * @return string
     */
    protected function _getGeneratedNameForImageAttribute($attributeName, $map = null, $forFiles = true) {
        if (!isset($map)) {
            $map = Mage::getStoreConfig("catalog/seoimagenames/map");
        }
        if (preg_match_all('/(%([a-z0-9]+))/i', $map, $match, PREG_PATTERN_ORDER)) {
            for ($i = 0; $i < count($match[1]); $i++) {
                if ('requestHost' === $match[2][$i]) {
                    $value = Mage::app()->getRequest()->getHttpHost(true);
                } else {
                    $value = $this->_getProductAttributeValue($match[2][$i]);
                }
                $value = $this->_prepareValue($value, $forFiles);
                $map   = str_replace($match[1][$i], $value, $map);
            }
        }
        if (Mage::getStoreConfig("catalog/seoimagenames/unique")) {
            $map .= '-' . $this->_imageAttributeNameToNum($attributeName);
            $map .= $this->_getMediaGalleryId();
        }
        // Replace multiple spaces or - with one of it's kind
        $value = preg_replace('/([ -]){2,}/', '$1', $map);
        return $value;
    }
    /**
     * Prepare a string so it may be used as part of a a file name
     * 
     * @param string $value
     * @param bool $forFiles
     * @return mixed
     */
    protected function _prepareValue($value, $forFiles = true) {
        if ($forFiles) {
            // not needed if this is for image labels
            $value = strtr($value, array(
                '%' => '',
                ' ' => '-',
                '#' => '-',
                '"' => '-',
                "'" => '-',
                ':' => '-',
                '..' => '_',
                '/' => '-'
            ));
            $value = Mage::helper('catalog/product_url')->format($value);
            $value = trim($value, '-_');
        }
        return $value;
    }
    /**
     * Return the value of an attribute
     *
     * @param string $attributeName One of 'image', 'small_image' or 'thumbnail'
     * @param boolean $_sentry
     * @return string
     */
    protected function _getProductAttributeValue($attributeName, $_sentry = false) {
        /*
         * Transform camelCase to underscore (e.g. productName => product_name)
         */
        $attributeCode = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $attributeName));
        $attribute     = $this->getProduct()->getResource()->getAttribute($attributeCode);
        if ($attribute->usesSource()) {
            $value = $this->getProduct()->getAttributeText($attributeCode);
        } else {
            $value = $this->getProduct()->getDataUsingMethod($attributeCode);
        }
        if (!isset($value) && !$_sentry) {
            // last try, load attribute
            $this->_loadAttributeOnProduct($this->getProduct(), $attributeCode);
            return $this->_getProductAttributeValue($attributeCode, $_sentry = true);
        }
        // haha
        if (!is_scalar($value)) {
            return $attributeCode;
        }
        return $value;
    }
    /**
     * Load a single attribute value onto a product. 
     * 
     * This method is not nice. I only keep it because if it reduces
     * the number of support requests, when people specify attributes
     * in the template string but don't have them loaded on product
     * collections.
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param string $attributeCode
     * @return $this
     */
    protected function _loadAttributeOnProduct(Mage_Catalog_Model_Product $product, $attributeCode) {
        $value = $product->getResource()->getAttributeRawValue($product->getId(), $attributeCode, Mage::app()->getStore()->getId());
        $product->setData($attributeCode, $value);
        return $this;
    }
    /**
     * Return the numeric position if the image in the media gallery array
     *
     * @return integer
     */
    protected function _getMediaGalleryId() {
        $product = $this->getProduct();
        $file    = $this->_getImageFile();
        if (!$file) {
            return 0;
        }
        if ($gallery = $product->getMediaGalleryImages()) {
            foreach ($gallery as $image) {
                if ($image->getFile() == $file) {
                    return $image->getPosition(); //return $image->getId();
                }
            }
        }
        // image not found in media gallery...
        return 0;
    }
    /**
     * @return string The image file name
     */
    protected function _getImageFile() {
        if (!($file = $this->getImageFile())) {
            $file = $this->getProduct()->getData($this->_getModel()->getDestinationSubdir());
        }
        return $file;
    }
    /**
     * Return a different number depending on the attributeCode passt to init()
     *
     * @param string $attributeCode
     * @return integer
     */
    protected function _imageAttributeNameToNum($attributeCode) {
        switch ($attributeCode) {
            case 'thumbnail':
                return 1;
            case 'small_image':
                return 2;
            case 'image':
                return 3;
            default:
                return 0;
        }
    }
}