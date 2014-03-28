<?php

/**
 * Customer module observer
 *
 */
class Addonline_Varnish_Model_Customer_Observer extends Mage_Customer_Model_Observer
{
	
	/**
	 * Cookie name for disabling external caching
	 *
	 * @var string
	 */
	const CUSTOMER_GROUP_COOKIE = 'customer_group';

    /**
     * Before load layout event handler
     *
     * @param Varien_Event_Observer $observer
     */
    public function beforeLoadLayout($observer)
    {
    	//Si la page générée est pour une mise en cache (varnish_static) on se considère comme non loggé
	    if (Mage::registry('varnish_static')) {
			$observer->getEvent()->getLayout()->getUpdate()->addHandle('customer_logged_out');
		} else {
			parent::beforeLoadLayout($observer);
		}

		if (Mage::getStoreConfig('system/external_page_cache/varnish_customer_group_cache')) {
	    	//Si on est loggé on ajoute un cookie avec l'id de group (utilisé dans varnish pour hasher différement les pages selon le grope de client)
			$session = Mage::getSingleton('customer/session');
			$cookie = Mage::getSingleton('core/cookie');
			$cgCookie = $cookie->get(self::CUSTOMER_GROUP_COOKIE);
			if ( $session->isLoggedIn()) {
				if ($cgCookie) {
					$cookie->renew(self::CUSTOMER_GROUP_COOKIE, $session->getLifeTime());
				} else {
					$cookie->set(self::CUSTOMER_GROUP_COOKIE, $session->getCustomerGroupId() , $session->getLifeTime());
				}
			} else {
				if ($cgCookie) {
					$cookie->delete(self::CUSTOMER_GROUP_COOKIE);
				}
			}
		}
		
    }

}
