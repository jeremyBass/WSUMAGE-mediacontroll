<?php

class Wsu_Mediacontroll_Block_Adminhtml_Orphaned_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct()
    {
        parent::__construct();
        $this->setId('mediacontrollOrphanedGrid');
        $this->setDefaultSort('prod_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        //$this->setVarNameFilter('mediacontroll_orphaned_filter');
        $this->setUseAjax(true);
    }

    public function getId(){
        return 'mediacontroll_tabs_orphaned_section_content';
    }

    protected function _prepareCollection(){
        $collection = Mage::helper('mediacontroll')->get_ProductUnsortedImages();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){
        $this->addColumn('id', array(
            'header'         => Mage::helper('mediacontroll')->__('Product Id'),
            'index'          => 'prod_id',
            'type'           => 'number',
        ));

        $this->addColumn('name', array(
            'header'         => Mage::helper('mediacontroll')->__('Name'),
            'index'          => 'code',
            'type'           => 'text',
        ));


        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('mediacontroll')->__('Action'),
                'width'     => '100',
				'align'     => 'center',
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
        $this->setMassactionIdField('prod_id');
        $this->getMassactionBlock()->setFormFieldName('mediacontroll');
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('mediacontroll')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('mediacontroll')->__('Are you sure?')
        ));
        return $this;
    }
}