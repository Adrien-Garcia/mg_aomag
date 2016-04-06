<?php

class Jetpulp_ResponsiveSlider_Block_Adminhtml_Slides_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('cmsSliderGrid');
        $this->setDefaultSort('identifier');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('responsiveslider/responsiveslider_item')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('item_id',
            array(
                'header' => Mage::helper('responsiveslider')->__('ID'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'item_id',
                'filter' => false,
            ));

        $this->addColumn('title',
            array(
                'header' => Mage::helper('responsiveslider')->__('Title'),
                'align' => 'left',
                'index' => 'title',
                'filter' => false,
            ));

        $this->addColumn('url',
            array(
                'header' => Mage::helper('responsiveslider')->__('URL'),
                'align' => 'left',
                'index' => 'url',
                'filter' => false,
            ));
        $this->addColumn('background_image',
            array(
                'header'=> Mage::helper('responsiveslider')->__('Image'),
                'type'  => 'image',
                'index' => 'background_image',
                'align' => 'center',
                'renderer'  => 'Jetpulp_ResponsiveSlider_Block_Adminhtml_Slides_Renderer_Image',
                'filter' => false,
            ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('responsiveslider')->__('Product SKU'),
                'width' => '160px',
                'index' => 'product_sku',
                'filter' => false,
            ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('responsiveslider')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                => array($this, '_filterStoreCondition'),
            ));
        }

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('cms')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => Mage::getSingleton('responsiveslider/responsiveslider')->getAvailableStatuses(),
            'filter' => false,
        ));

        $this->addColumn('from_date', array(
            'header'    => Mage::helper('responsiveslider')->__('From Date'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'from_date',
            'filter' => false,
        ));

        $this->addColumn('to_date', array(
            'header'    => Mage::helper('responsiveslider')->__('To Date'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'to_date',
            'filter' => false,
        ));


        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('item_id' => $row->getId()));
    }
}
