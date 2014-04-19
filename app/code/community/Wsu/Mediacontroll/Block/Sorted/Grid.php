<?php
class Wsu_Mediacontroll_Block_Sorted_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct();
		$this->setId('mediacontrollGrid');
		$this->setDefaultSort('prod_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);	  
	}
	protected function _prepareCollection() {
		$prod_array = Mage::helper('mediacontroll')->get_ProductUnsortedImages();
		$prod_collection = Mage::helper('mediacontroll')->getVarienDataCollection($prod_array);
		$this->setCollection($prod_collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){
	    $this->addColumn('filename', array(
          'header'    => Mage::helper('mediacontroll')->__('Filename'),
          'renderer'     =>'Wsu_Mediacontroll_Block_Adminhtml_Renderer_Image',
          'align'     =>'left',
          'index'     => 'filename'
		));
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('mediacontroll')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('mediacontroll')->__('delete'),
                        'url'       => array('base'=> '*/*/delete'),
                       'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
      return parent::_prepareColumns();
	} 

	protected function _prepareMassaction(){
        $this->setMassactionIdField('imgclean_id');
        $this->getMassactionBlock()->setFormFieldName('mediacontroll');
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('mediacontroll')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('mediacontroll')->__('Are you sure?')
        ));
        return $this;
    }
}
