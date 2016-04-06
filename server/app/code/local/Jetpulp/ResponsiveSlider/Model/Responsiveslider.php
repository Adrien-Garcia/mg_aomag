<?php

class Jetpulp_ResponsiveSlider_Model_Responsiveslider extends Mage_Core_Model_Abstract
{
	
	public function _construct()
    {
        parent::_construct();
        $this->_init('responsiveslider/responsiveslider');
    }

    /**
     * Prepare slider's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        $statuses = new Varien_Object(array(
            Mage_Cms_Model_Page::STATUS_ENABLED => Mage::helper('cms')->__('Enabled'),
            Mage_Cms_Model_Page::STATUS_DISABLED => Mage::helper('cms')->__('Disabled'),
        ));

        return $statuses->getData();
    }

    public function getActiveSlides($infinitLoop = false) {

        if (!$this->responsivesliderItems) {
            $todayDate = Mage::app()->getLocale()->date()->toString(Varien_Date::DATE_INTERNAL_FORMAT);

            $this->responsivesliderItems = Mage::getModel('responsiveslider/responsiveslider_item')->getCollection();
            $this->responsivesliderItems->join(
                array('link' => 'responsiveslider/responsiveslider_link'),
                'main_table.item_id=link.item_id AND link.responsiveslider_id='.$this->getId(),
                array('sort_order')
            );
            $this->responsivesliderItems->addFieldToFilter('is_active', '1');
            $this->responsivesliderItems->addFieldToFilter(
                'from_date',
                array(array('date' => true, 'to' => $todayDate), array('is' => new Zend_Db_Expr('null')))
            );
            $this->responsivesliderItems->addFieldToFilter(
                'to_date',
                array(array('date' => true, 'from' => $todayDate), array('is' => new Zend_Db_Expr('null')))
            );
            $this->responsivesliderItems->getSelect()->order('link.sort_order', 'asc');
            $this->responsivesliderItems->setPreviousNextFlag(true);
            $this->responsivesliderItems->setInfinitLoopFlag($infinitLoop);
        }
        return $this->responsivesliderItems;

    }

    public function loadByIdentifier( $identifier )
    {
        $collection = Mage::getModel('responsiveslider/responsiveslider')->getCollection();
        $collection->addFieldToFilter('identifier', $identifier );
        return $collection->getFirstItem();
    }

}