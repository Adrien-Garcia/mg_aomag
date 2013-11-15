<?php

/**
 * Customer module observer
 *
 */
class Addonline_Varnish_Model_Customer_Observer extends Mage_Customer_Model_Observer
{

    /**
     * Before load layout event handler
     *
     * @param Varien_Event_Observer $observer
     */
    public function beforeLoadLayout($observer)
    {
	    if (Mage::registry('varnish_static')) {
			$observer->getEvent()->getLayout()->getUpdate()->addHandle('customer_logged_out');
		} else {
			parent::beforeLoadLayout($observer);
		}

    }

}
