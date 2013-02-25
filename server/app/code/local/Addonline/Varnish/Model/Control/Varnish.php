<?php
/**
 * Varnish page cache control model
 *
 * @category    Addonline
 * @package     Addonline_Varnish
 */
class Addonline_Varnish_Model_Control_Varnish implements Mage_PageCache_Model_Control_Interface
{
    /**
     * Clean varnish page cache
     *
     * @return void
     */
    public function clean()
    {
		Mage::helper('varnish')->purge(array('/.*'));
    }
}
