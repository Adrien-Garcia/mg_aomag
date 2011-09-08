<?php

class Addonline_AdvancedSlideshow_Block_Adminhtml_Gestion_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
		$collection = Mage::getModel('advancedslideshow/advancedslideshow')->getCollection();
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
		$this->addColumn('title',
			array(
				'header' => Mage::helper('advancedslideshow')->__('Title'), 
				'align' => 'left',
				'index' => 'title'
			));
		return parent::_prepareColumns();
	}
		
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit',array('id' => $row->getId()));
	}
	
}