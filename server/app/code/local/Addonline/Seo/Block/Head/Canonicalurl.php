<?php

class Addonline_Seo_Block_Head_Canonicalurl extends Mage_Core_Block_Template
//Mage_Page_Block_Html_Head
//Fooman_Speedster_Block_Page_Html_Head
{
	
    /**
     * Add Link element to HEAD entity (Magento 1.3)
     *
     * @param string $rel forward link types
     * @param string $href URI for linked resource
     * @return Mage_Page_Block_Html_Head
     */
/*
    public function addLinkRel($rel, $href)
    {
        $this->addItem('link_rel', $href, 'rel="' . $rel . '"');
        return $this;
    }
*/
/*
	public function getHeadUrl()
	{
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
			$this->_data['urlKey'] =$headUrl;
        }
		
		return $this->_data['urlKey'];
	}
*/
	public function getHeadProductUrl()
    {
		if( !Mage::helper('seo')->isVersionLessThan('1.4.0.0') )
        {
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

	public function getHeadCategoryUrl()
    {
    	if( !Mage::helper('seo')->isVersionLessThan('1.4.0.0') )
        {
        	return false;
		}
		
    	if (empty($this->_data['urlKey'])) 
		{
			$category_id = $this->getRequest()->getParam('id');
			$category = Mage::getModel('catalog/category')->load($category_id);
			$this->_data['urlKey'] = $category->getUrl();
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
}