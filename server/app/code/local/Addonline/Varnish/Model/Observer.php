<?php
/**
 * Observer model
 *
 * @category    Addonline
 * @package     Addonline_Varnish
 */
class Addonline_Varnish_Model_Observer
{
    /**
     * Check when varnish caching should be enabled.
     *
     * @param Varien_Event_Observer $observer
     * @return Addonline_Varnish_Model_Observer
     * 
     */
    public function processPreDispatch(Varien_Event_Observer $observer)
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

        $configuration = Mage::getConfig()->getNode(Mage_PageCache_Model_Observer::XML_NODE_ALLOWED_CACHE);

        if (!$configuration) {
            $needCaching = false;
        }

        $configuration = $configuration->asArray();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $actionName = $request->getActionName();

        if (!isset($configuration[$module])) {
            $needCaching = false;
        }

        if (isset($configuration[$module]['controller']) && $configuration[$module]['controller'] != $controller) {
            $needCaching = false;
        }

        if (isset($configuration[$module]['action']) && $configuration[$module]['action'] != $actionName) {
            $needCaching = false;
        }

        //Cas particulier des pages 404 : il ne faut pas les cacher sous peine de poser des problème sur des traitements qui ne doiivent pas être cachés et qui peuvent être sur certains cas traité par l'action noRoute...
        if ($actionName == 'noRoute') {
        	$needCaching = false;
        }
        
        if ($needCaching) {
        	
        	//Flag qui indique aux blocks qu'ils sont en "mode static"
        	Mage::unregister('varnish_static'); //pour le cas des 404 noRoute qui passent 2 fois ici
        	Mage::register('varnish_static', true);

        	$response = $action->getResponse();
	
	        $lifetime = Mage::helper('pagecache')->getNoCacheCookieLifetime();	
	        $response->setHeader('X-Magento-Lifetime', $lifetime, true); // Only for debugging and information
	        $response->setHeader('X-Magento-Action', $action->getFullActionName(), true); // Only for debugging and information
	        $response->setHeader('Cache-Control', 'max-age='. $lifetime, true);
	        $response->setHeader('varnish', 'cache', true);

        }
	}

    /**
     * @see Mage_Core_Model_Cache
     *
     * @param Mage_Core_Model_Observer $observer
     */
    public function onCategorySave($observer)
    {
    	// If Varnish is not enabled on admin don't do anything
    	if (!Mage::helper('varnish')->isEnabled()) {
    		return;
    	}
    	$category = $observer->getCategory(); /* @var $category Mage_Catalog_Model_Category */
    	if ($category->getData('include_in_menu')) {
    		Mage::getModel('adminnotification/inbox')->parse(array(
    														array('severity' => Mage_AdminNotification_Model_Inbox::SEVERITY_MINOR,
    															    'date_added'=> date('Y-m-d H:i:s'),
    																'title'=> Mage::helper('varnish')->__('External Page Cache Varnish need to be refreshed'),
    																'description'   => Mage::helper('varnish')->__('You modified a category "included in navigation", which means your navigation menu might be modified, in this case you need to refresh the entire external page cache Varnish'),
    																'url'=> Mage::helper('adminhtml')->getUrl('*/pageCache/clean'),
    																'internal'      => true)));
    	}
    
    	return $this;
    }
    
    /**
     * Listens to application_clean_cache event and gets notified when a product/category/cms
     * model is saved.
     *
     * @param $observer Mage_Core_Model_Observer
     */
    public function purgeCache($observer)
    {
    	
    	$tags = $observer->getTags();

    	// Dans le cas du vidage de cache de config , il ne faut pas passer par la méthode "isEnabled" qui va justement chercher ses infos dans la config, 
    	// ce qui peux faire planter en 404 via une Mage_Core_Model_Store_Exception car il n'arrive pas à charger la config du Store
    	// on ne poursuit donc le traitement que dans le cas des tags catalog_product, catalog_category ou cms_page 
        if (count($tags)==0) {
        	return;
        } else {	   
        	$tag = $tags[0];
	        if ($tag != 'catalog_product' && $tag !='catalog_category' && $tag !='cms_page') {
	        	return;
	        }
        }
    	
    	// If Varnish is not enabled on admin don't do anything
    	if (!Mage::helper('varnish')->isEnabled()) {
    		return;
    	}
    	
    	$urls = array();
    
    	
    	$categories= array();//cache des catéogries chargées
    	// compute the urls for affected entities
    	foreach ((array)$tags as $tag) {
    		//catalog_product_100 or catalog_category_186
    		$tag_fields = explode('_', $tag);
    		if (count($tag_fields)==3) {
    			if ($tag_fields[1]=='product') {
    				 //Mage::log("Purge urls for product " . $tag_fields[2]);
    
    				// get urls for product
    				$product = Mage::getModel('catalog/product')->load($tag_fields[2]);
    				$urls = array_unique(array_merge($urls, $this->_getUrlsForProduct($product)));
    			} elseif ($tag_fields[1]=='category') {
    				 //Mage::log('Purge urls for category ' . $tag_fields[2]);

    				 if (!isset($categories[$tag_fields[2]])) {
	    				$category = Mage::getModel('catalog/category')->load($tag_fields[2]);
	    				$categories[$tag_fields[2]] = $category;
    					$category_urls = $this->_getUrlsForCategory($category);
    					$urls = array_unique(array_merge($urls, $category_urls));
    				 }
    				
    				if($category->getLevel()>= 3) {
    					if (!isset($categories[$category->getParentId()])) {
    						$category = Mage::getModel('catalog/category')->load($category->getParentId());
    						$categories[$category->getParentId()] = $category;
    						$category_urls = $this->_getUrlsForCategory($category);
    						$urls = array_unique(array_merge($urls, $category_urls));
    					}
    				}
    				
    			} elseif ($tag_fields[1]=='page') {
    				$urls = $this->_getUrlsForCmsPage($tag_fields[2]);
    			}
    		}
    	}
    
    	// Transform urls to relative urls
    	$relativeUrls = array();
    	foreach ($urls as $url) {
    		$relativeUrls[] = parse_url($url, PHP_URL_PATH);
    	}
    	// Mage::log("Relative urls: " . var_export($relativeUrls, True));
    
    	if (!empty($relativeUrls)) {
    		$errors = Mage::helper('varnish')->purge($relativeUrls);
    		if (!empty($errors)) {
    			Mage::getSingleton('adminhtml/session')->addError(
    					"Some Varnish purges failed: <br/>" . implode("<br/>", $errors));
    		} else {
    			Mage::getSingleton('adminhtml/session')->addSuccess(
    					"Purges have been submitted successfully: <br/>" . implode("<br/>", $relativeUrls));
    		}
    	}
    
    	return $this;
    }
    
    /**
     * Returns all the urls related to product
     * @param Mage_Catalog_Model_Product $product
     */
    protected function _getUrlsForProduct($product){
    	$urls = array();
    
    	$store_id = $product->getStoreId();
    
    	$routePath = 'catalog/product/view';
    	$routeParams['id']  = $product->getId();
    	$routeParams['s']   = $product->getUrlKey();
    	$routeParams['_store'] = (!$store_id ? 1: $store_id);
    	$url = Mage::getUrl($routePath, $routeParams);
    	$urls[] = $url;
    
    	// Collect all rewrites
    	$rewrites = Mage::getModel('core/url_rewrite')->getCollection();
    	if (!Mage::getConfig('catalog/seo/product_use_categories')) {
    		$rewrites->getSelect()
    		->where("id_path = 'product/{$product->getId()}'");
    	} else {
    		// Also show full links with categories
    		$rewrites->getSelect()
    		->where("id_path = 'product/{$product->getId()}' OR id_path like 'product/{$product->getId()}/%'");
    	}
    	foreach($rewrites as $r) {
    		unset($routeParams);
    		$routePath = '';
    		$routeParams['_direct'] = $r->getRequestPath();
    		$routeParams['_store'] = $r->getStoreId();
    		$url = Mage::getUrl($routePath, $routeParams);
    		$urls[] = $url;
    	}
        	
    	return $urls;
    }
    
    
    /**
     * Returns all the urls pointing to the category
     */
    protected function _getUrlsForCategory($category) {
    	$urls = array();
    	$routePath = 'catalog/category/view';
    
    	$store_id = $category->getStoreId();
    	$routeParams['id']  = $category->getId();
    	$routeParams['s']   = $category->getUrlKey();
    	$routeParams['_store'] = (!$store_id ? 1 : $store_id); # Default store id is 1
    	$url = Mage::getUrl($routePath, $routeParams);
    	$urls[] = $url;
    
    	// Collect all rewrites
    	$rewrites = Mage::getModel('core/url_rewrite')->getCollection();
    	$rewrites->getSelect()->where("id_path = 'category/{$category->getId()}'");
    	foreach($rewrites as $r) {
    		unset($routeParams);
    		$routePath = '';
    		$routeParams['_direct'] = $r->getRequestPath();
    		$routeParams['_store'] = $r->getStoreId();
    		$routeParams['_nosid'] = True;
    		$url = Mage::getUrl($routePath, $routeParams);
    		$urls[] = $url;
    	}
    
    	if ($category->getId()==3) { //Root category : on flush aussi la Home-page
    		$urls[] = '/$';
    	}
    	
    	return $urls;
    }
    
    /**
     * Returns all urls related to this cms page
     */
    protected function _getUrlsForCmsPage($cmsPageId)
    {
    	$urls = array();
    	$page = Mage::getModel('cms/page')->load($cmsPageId);
    	if ($page->getId()) {
    		$urls[] = '/' . $page->getIdentifier();
    	}
    	//si on met à jour la page cms Home on met à jour le cache de la home page
    	if ($page->getIdentifier() == 'home') {
    		$urls[] = '/$';
    	} else {
    	    if ($page->getId()) {
    			$urls[] = '/' . $page->getIdentifier();
    		}
    	}   	
    	return $urls;
    }
}
