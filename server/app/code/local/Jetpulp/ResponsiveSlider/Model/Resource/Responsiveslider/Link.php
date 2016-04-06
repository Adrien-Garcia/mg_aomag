<?php

class Jetpulp_ResponsiveSlider_Model_Resource_Responsiveslider_Link extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        // Note that the id refers to the key field in your database table.
        $this->_init('responsiveslider/responsiveslider_link', 'link_id');
    }
}