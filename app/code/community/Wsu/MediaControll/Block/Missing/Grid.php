<?php
class Wsu_Mediacontroll_Block_Missing_Grid extends Mage_Adminhtml_Block_Widget_Grid {
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
        $this->addColumn('id', array(
            'header'	=> Mage::helper('mediacontroll')->__('Product Id'),
            'index'		=> 'prod_id',
            'type'		=> 'number',
        ));

        $this->addColumn('name', array(
            'header'		=> Mage::helper('mediacontroll')->__('Name'),
            'index'		=> 'name',
            'type'		=> 'text',
        ));

        $this->addColumn('action',
            array(
                'header'	=>  Mage::helper('mediacontroll')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getProd_id',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('mediacontroll')->__('Resort'),
                        'url'       => array('base'=> '*/*/resort'),
						'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'prod_id',
                'is_system' => true,
        ));
      return parent::_prepareColumns();
	} 

	protected function _prepareMassaction(){
        $this->setMassactionIdField('prod_id');
        $this->getMassactionBlock()->setFormFieldName('mediacontroll');
        $this->getMassactionBlock()->addItem('resort', array(
             'label'    => Mage::helper('mediacontroll')->__('Resort'),
             'url'      => $this->getUrl('*/*/massResort'),
             'confirm'  => Mage::helper('mediacontroll')->__('Are you sure?')
        ));
        return $this;
    }
}
