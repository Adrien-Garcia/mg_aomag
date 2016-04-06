<?php

class Jetpulp_ResponsiveSlider_Model_Responsiveslider_Link extends Mage_Core_Model_Abstract
{
	
	public function _construct()
    {
        parent::_construct();
        $this->_init('responsiveslider/responsiveslider_link');
    }

    public function isLinkIsExist($itemId, $responsivesliderId)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('item_id', $itemId)
            ->addFieldToFilter('responsiveslider_id', $responsivesliderId)
            ->addFieldToFilter('responsiveslider_id', $responsivesliderId);

        $data = $collection->getData();
        if( count($data) > 0 ) {
            return true;
        } else {
            return false;
        }

    }
}