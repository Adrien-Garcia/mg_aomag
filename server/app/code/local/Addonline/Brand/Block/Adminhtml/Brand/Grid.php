<?php

class Addonline_Brand_Block_Adminhtml_Brand_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('brandGrid');
      $this->setDefaultSort('brand_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('brand/brand')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('brand_id', array(
          'header'    => Mage::helper('brand')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'brand_id',
      ));
      
      $this->addColumn('nom', array(
          'header'    => Mage::helper('brand')->__('Nom'),
          'align'     =>'left',
          'width'     => '200px',
          'index'     => 'nom',      
      ));
      
      /*
      $this->addColumn('logo', array(
          'header'    => Mage::helper('brand')->__('Logo'),
          'align'     =>'right',
          'width'     => '100px',
          'index'     => 'filename', 
      	'type' => 'image'     
      ));
	*/
		$this->addColumn('filename', array(
            'header'=>Mage::helper('brand')->__('Logo'),
            'filter'=>false,
            'index'=>'filename',
            'renderer'  => 'brand/adminhtml_grid_renderer_image',
            'align' => 'center',
            'width'     => '100px',
              
        ));   
            
      $this->addColumn('status', array(
          'header'    => Mage::helper('brand')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Activé',
              2 => 'Désactiver',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('brand')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('brand')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('brand')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('brand')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('brand_id');
        $this->getMassactionBlock()->setFormFieldName('brand');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('brand')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('brand')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('brand/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('brand')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('brand')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}