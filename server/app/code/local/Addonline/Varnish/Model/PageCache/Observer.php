<?php
/**
 * Observer model
 *
 * @category    Addonline
 * @package     Addonline_Varnish
 */
class Addonline_Varnish_Model_PageCache_Observer extends Mage_PageCache_Model_Observer
{
    /**
     * Check when varnish caching should be enabled.
     *
     * @param Varien_Event_Observer $observer
     * @return Addonline_Varnish_Model_Observer
     * 
     */
    public function processPostDispatch(Varien_Event_Observer $observer)
	{
    	 
        if (!Mage::helper("varnish")->isEnabled()) {
		    return $this;
        }
        $action = $observer->getEvent()->getControllerAction();
        $request = $action->getRequest();
        $needCaching = true;

        if ($request->isPost()) {
            $needCaching = false;
        }

        $configuration = Mage::getConfig()->getNode(self::XML_NODE_ALLOWED_CACHE);

        if (!$configuration) {
            $needCaching = false;
        }

        $configuration = $configuration->asArray();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if (!isset($configuration[$module])) {
            $needCaching = false;
        }

        if (isset($configuration[$module]['controller']) && $configuration[$module]['controller'] != $controller) {
            $needCaching = false;
        }

        if (isset($configuration[$module]['action']) && $configuration[$module]['action'] != $action) {
            $needCaching = false;
        }

        if ($needCaching) {
 		
 	        /* @var $response Mage_Core_Controller_Response_Http */
	        $response = $action->getResponse();
	
	        $lifetime = Mage::helper('pagecache')->getNoCacheCookieLifetime();	
	        $response->setHeader('X-Magento-Lifetime', $lifetime, true); // Only for debugging and information
	        $response->setHeader('X-Magento-Action', $action->getFullActionName(), true); // Only for debugging and information
	        $response->setHeader('Cache-Control', 'max-age='. $lifetime, true);
	        $response->setHeader('varnish', 'cache', true);
	        
        }
		
        return $this;
    }
}
