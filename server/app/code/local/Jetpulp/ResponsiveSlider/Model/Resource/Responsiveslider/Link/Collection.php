<?php

class Jetpulp_ResponsiveSlider_Model_Resource_Responsiveslider_Link_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    private $previousNextFlag = false;

    /**
     * Constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('responsiveslider/responsiveslider_link');
    }

    /**
     * @param $bool
     */
    public function setPreviousNextFlag($bool)
    {
        $this->previousNextFlag = $bool;
    }
}