<?php
class Wsu_Mediacontroll_Block_Assignment_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct();
		$this->setId('mediacontrollGrid');
		$this->setDefaultSort('prod_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);	  
	}
	protected function _prepareCollection() {
		$prod_array = Mage::helper('mediacontroll')->get_ProductUnassignedImages();
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
		
		
	    $this->addColumn('prod_state', array(
          'header'		=> Mage::helper('mediacontroll')->__('Product image state'),
          'renderer'	=>'Wsu_Mediacontroll_Block_Adminhtml_Renderer_ProdState',
          'align'		=>'left',
          'index'		=> 'prod_state'
		));		
	    $this->addColumn('imgs_state', array(
          'header'		=> Mage::helper('mediacontroll')->__('Image States'),
          'renderer'	=>'Wsu_Mediacontroll_Block_Adminhtml_Renderer_ProdImgState',
          'align'		=>'left',
          'index'		=> 'imgs_state'
		));
        $this->addColumn('action',
            array(
                'header'	=>  Mage::helper('mediacontroll')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getProd_id',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('mediacontroll')->__('delete'),
                        'url'       => array('base'=> '*/*/delete'),
						'field'     => 'id'
                    ),
                    array(
                        'caption'   => Mage::helper('mediacontroll')->__('Assignment'),
                        'url'       => array('base'=> '*/*/assignment'),
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
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('mediacontroll')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('mediacontroll')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('reassignment', array(
             'label'    => Mage::helper('mediacontroll')->__('Resort'),
             'url'      => $this->getUrl('*/*/massAssignment'),
             'confirm'  => Mage::helper('mediacontroll')->__('Are you sure?')
        ));
        return $this;
    }
}
