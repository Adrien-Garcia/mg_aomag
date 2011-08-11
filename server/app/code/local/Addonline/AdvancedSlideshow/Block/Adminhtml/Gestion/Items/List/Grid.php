<?php

class Addonline_AdvancedSlideshow_Block_Adminhtml_Gestion_Items_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('advancedslideshowGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}
	
	protected function _prepareCollection()
	{
		$_id_slideshow = Mage::registry('cur_slideshow_id');
		$collection = Mage::getModel('advancedslideshow/advancedslideshow_item')->getCollection();
		$collection->addFilter('id_slideshow', $_id_slideshow);
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn('id',
			array(
				'header' => Mage::helper('advancedslideshow')->__('ID'), 
				'align' => 'right', 
				'width' => '50px', 
				'index' => 'id'
			));
        $this->addColumn('from_date', array(
            'header'    => Mage::helper('advancedslideshow')->__('Date Start'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'from_date',
        ));

        $this->addColumn('to_date', array(
            'header'    => Mage::helper('advancedslideshow')->__('Date Expire'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'to_date',
        ));
		$this->addColumn('url',
			array(
				'header' => Mage::helper('advancedslideshow')->__('URL'), 
				'align' => 'left',
				'index' => 'url'
			));
        $this->addColumn('image',
        	array(
            	'header'=> Mage::helper('advancedslideshow')->__('Image'),
                'type'  => 'image',
                'index' => 'image',
        ));
        
        $this->addColumn('sku',
			array(
            	'header'=> Mage::helper('advancedslideshow')->__('SKU'),
                'width' => '160px',
                'index' => 'product_sku',
        ));
        $this->addColumn('product_name',
			array(
            	'header'=> Mage::helper('advancedslideshow')->__('Product Name'),
                'index' => 'product_name',
        ));
			
        $this->addColumn('sort_order',
			array(
            	'header'=> Mage::helper('advancedslideshow')->__('Position'),
                'width' => '80px',
                'index' => 'sort_order',
        ));
			
		return parent::_prepareColumns();
	}
		
	public function getRowUrl($row)
	{
		$_id_slideshow = Mage::registry('cur_slideshow_id');
		return $this->getUrl('*/adminhtml_advancedslideshow_item/edit',array('id' => $row->getId(), 'id_slideshow' => $_id_slideshow));
	}
	
}