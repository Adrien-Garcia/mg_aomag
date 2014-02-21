<?php
class Addonline_Seo_Block_Head extends Mage_Page_Block_Html_Head
{
	public function getHeadUrl()
	{
		if( !Mage::helper('seo')->isVersionLessThan('1.4.0.0') )
        {
        	//à partir de la version 1.4 de magento les URLs canaonique sont gérées, à activer dans Système>Configuration>Catalogue>otimisation moteur de recherche : 
        	// - Utiliser un lien canonique de meta tag pour les catégories
        	// - Utiliser un lien canonique de meta tag pour les produits
        	return false;
		}
		if (empty($this->_data['urlKey'])) 
		{
			$url = Mage::helper('core/url')->getCurrentUrl();
			$parsedUrl = parse_url($url);

			$scheme = $parsedUrl['scheme'];
			$host = $parsedUrl['host'];
			$port = isset($parsedUrl['port']) ? $parsedUrl['port'] : null;
			$path = $parsedUrl['path'];

			$headUrl = $scheme . '://' . $host . ($port && '80' != $port ? ':' . $port : '') . $path;
			
			if (Mage::getStoreConfig('web/seo/trailingslash')) 
			{
				if (!preg_match('/\\.(rss|html|htm|xml|php?)$/', strtolower($headUrl)) && substr($headUrl, -1) != '/') 
				{
					$headUrl .= '/';
				}
			}
			//return $headUrl;
			$this->_data['urlKey'] =$headUrl;
        }
		
		return $this->_data['urlKey'];
	}

	public function getHeadProductUrl()
    {  
		if( !Mage::helper('seo')->isVersionLessThan('1.4.0.0') )
        {
        	//à partir de la version 1.4 de magento les URLs canaonique sont gérées, à activer dans Système>Configuration>Catalogue>otimisation moteur de recherche : 
        	// - Utiliser un lien canonique de meta tag pour les catégories
        	// - Utiliser un lien canonique de meta tag pour les produits
        	return false;
		}
    	if (empty($this->_data['urlKey'])) 
		{
			$product_id = $this->getRequest()->getParam('id');
			$_item = Mage::getModel('catalog/product')->load($product_id);
			$this->_data['urlKey'] = Mage::getBaseUrl().$_item->getUrlKey().Mage::helper('catalog/product')->getProductUrlSuffix();
			if (Mage::getStoreConfig('web/seo/trailingslash')) 
			{
				if (!preg_match('/\\.(rss|html|htm|xml|php?)$/', strtolower($this->_data['urlKey'])) && substr($this->_data['urlKey'], -1) != '/') 
				{
					$this->_data['urlKey'] .= '/';
				}
			}

			    
		}
		return $this->_data['urlKey'];
	} 
	
	public function getRobots()
	{
		$route = $this->getRequest()->getRouteName();
		$controller = $this->getRequest()->getControllerName();
		
		if($route == "catalogsearch" || $route == "checkout" || $route == "contacts" || $route == "customer" || $controller == "product_compare") {
			$this->_data['robots'] = "NOINDEX,FOLLOW";
		} elseif($route == "cms") {
			$pageCms = Mage::getSingleton('cms/page');
			if ($pageCms->getMetaRobots()) {
				$this->_data['robots'] = $pageCms->getMetaRobots();
			}
		} 
		return parent::getRobots();
	}
}
