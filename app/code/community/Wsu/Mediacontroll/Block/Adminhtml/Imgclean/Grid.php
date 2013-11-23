<?php

class Wsu_Mediacontroll_Block_Adminhtml_Imgclean_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    
  public function __construct()
  {
      parent::__construct();
      $this->setId('mediacontrollGrid');
      $this->setDefaultSort('mediacontroll_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
	 	  
  }

  
	// **** // trae las imagenes que no estan en base de datos...
  protected function _prepareCollection()
  {
	$collection = Mage::getModel('mediacontroll/imgclean')->getCollection();
	
	$this->setCollection($collection);

	return parent::_prepareCollection();
	
  }

  protected function _prepareColumns()
  {
    
	    $this->addColumn('filename', array(
          'header'    => Mage::helper('wsu_mediacontroll')->__('Filename'),
          'renderer'     =>'Wsu_Mediacontroll_Block_Adminhtml_Renderer_Image',
          'align'     =>'left',
          'index'     => 'filename'

      ));
		  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('wsu_mediacontroll')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('wsu_mediacontroll')->__('delete'),
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
	
        $this->setMassactionIdField('mediacontroll_id');
        $this->getMassactionBlock()->setFormFieldName('mediacontroll');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('wsu_mediacontroll')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('wsu_mediacontroll')->__('Are you sure?')
        ));
        return $this;
    }
	

}
