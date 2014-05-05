<?php
class Wsu_Mediacontroll_Block_Sorted_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct();
		$this->setId('mediacontrollGrid');
		$this->setDefaultSort('prod_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->_emptyText = Mage::helper('adminhtml')->__('Nothing is left outstanding, try to refresh. <button title="Refresh" class="scalable refresh" onclick="setLocation(\''.$this->getUrl('*/*/search').'\')" type="button"><span><span><span>Refresh</span></span></span></button>');
	}
	protected function _prepareCollection() {
		$collection = Mage::getModel('wsu_mediacontroll/unsorted')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){
        $this->addColumn('prod_id', array(
            'header'	=> Mage::helper('mediacontroll')->__('Product Id'),
            'index'		=> 'prod_id',
            'type'		=> 'number',
        ));

        $this->addColumn('name', array(
            'header'		=> Mage::helper('mediacontroll')->__('Name'),
			'renderer'	=>'Wsu_Mediacontroll_Block_Adminhtml_Renderer_Sorted_Name',
            'index'		=> 'name',
            'type'		=> 'text',
        ));
		
		
	    $this->addColumn('prod_state', array(
          'header'		=> Mage::helper('mediacontroll')->__('Product image state'),
          'renderer'	=>'Wsu_Mediacontroll_Block_Adminhtml_Renderer_Sorted_ProdState',
          'align'		=>'left',
          'index'		=> 'prod_state'
		));		
	    $this->addColumn('imgs_state', array(
          'header'		=> Mage::helper('mediacontroll')->__('Image States'),
          'renderer'	=>'Wsu_Mediacontroll_Block_Adminhtml_Renderer_Sorted_ProdImgState',
          'align'		=>'left',
          'index'		=> 'imgs_state'
		));
        $this->addColumn('action',
            array(
                'header'	=>  Mage::helper('mediacontroll')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getUnsorted_id',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('mediacontroll')->__('Resort'),
                        'url'       => array('base'=> '*/*/resort'),
						'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'unsorted_id',
                'is_system' => true,
        ));
      return parent::_prepareColumns();
	} 

	protected function _prepareMassaction(){
        $this->setMassactionIdField('unsorted_id');
        $this->getMassactionBlock()->setFormFieldName('mediacontroll');
		
        $this->getMassactionBlock()->addItem('resort', array(
             'label'    => Mage::helper('mediacontroll')->__('Resort'),
             'url'      => $this->getUrl('*/*/massResort'),
             'confirm'  => Mage::helper('mediacontroll')->__('Are you sure?')
        ));
        return $this;
    }
}
