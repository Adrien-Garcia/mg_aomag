<?php

class Jetpulp_ResponsiveSlider_Block_Adminhtml_Responsiveslider_Edit_Tab_Items
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface

{

    public function __construct()
    {
        parent::__construct();
        $this->setId('responsivesliderGrid');
        $this->setUseAjax(true);
    }

    public function getSlider()
    {
        return Mage::registry('cms_responsiveslider');
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_slider') {
            $slideIds = $this->_getSelectedSlides();
            if (empty($slideIds)) {
                $slideIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.item_id', array('in'=>$slideIds));
            }
            elseif(!empty($slideIds)) {
                $this->getCollection()->addFieldToFilter('main_table.item_id', array('nin'=>$slideIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('responsiveslider/responsiveslider_item')->getCollection();
        $slidesLinked = $this->_getSelectedSlides();
        if ($this->getSlider()->getId() && count($slidesLinked) >0 ) {
            $this->setDefaultFilter( array('in_slider' => 1) );
        }

        $storeIdsFilter = $this->_getSliderStore();
        if( (count($storeIdsFilter) == 1 && $storeIdsFilter[0] == 0) == FALSE ) {
            $collection->addStoreFilter($storeIdsFilter);
        }

        $this->setCollection($collection);

       return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('in_slider',
            array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'field_name' => 'in_slider_items[]',
                'values'    => $this->_getSelectedSlides(),
                'align'     => 'center',
                'index'     => 'item_id',
            ));

        $this->addColumn('item_id',
            array(
                'header' => Mage::helper('responsiveslider')->__('ID'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'item_id',
            ));

        $this->addColumn('title_item',
            array(
                'header' => Mage::helper('responsiveslider')->__('Title'),
                'align' => 'left',
                'index' => 'title',
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
                'column_css_class'=>'no-display',//this sets a css class to the column row item
                'header_css_class'=>'no-display',//this sets a css class to the column header
            ));
        }

        $this->addColumn('is_active_item', array(
            'header'    => Mage::helper('responsiveslider')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => Mage::getSingleton('responsiveslider/responsiveslider')->getAvailableStatuses(),
        ));

        $this->addColumn('position[]', array(
            'header'    => Mage::helper('catalog')->__('Position'),
            'width'     => '1',
            'type'      => 'number',
            'index'     => 'sort_order',
            'editable'  => true,
            'sortable'      => false,
            'filter' => false,
            'renderer'  => 'responsiveslider/widget_slide_renderer_position'
        ));
        
        return parent::_prepareColumns();
    }


    public function getRowUrl($row)
    {
        return $this->getUrl('*/responsiveslider_item/edit',array('responsiveslider_id' => $this->getSlider()->getId(), 'id' => $row->getId()));
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('responsiveslider')->__('Slides');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('responsiveslider')->__('Slides');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _getSliderStore()
    {
        $stores = $this->getSlider()->getStores();
        return $stores;
    }

    protected function _getSelectedSlides()
    {
        $responsiveslider_id = $this->getRequest()->getParam('responsiveslider_id');

        $zendDb = Mage::getModel('core/resource')->getConnection('core_read');
        $tableName = Mage::getSingleton('core/resource')->getTableName('responsiveslider/responsiveslider_link');
        $query = $zendDb->select()
            ->from( array('c' => $tableName), array('item_id') )
            ->where('responsiveslider_id = ?', $responsiveslider_id);

        $slides = $zendDb->fetchAssoc($query);

        return array_keys($slides);
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
}