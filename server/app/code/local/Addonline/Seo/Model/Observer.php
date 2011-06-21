<?php

class Addonline_Seo_Model_Observer {
	
	protected $_templateVars = array();
	protected $_product;
	protected $_category;

        public function editMetas($observer)
        {
            
        	$layout = $observer->getLayout();
			//si le block head existe 
        	if ($headBlock = $layout->getBlock('head')) {
				
                //BALISES METAS DES PAGES PRODUITS
                //TODO : 
                // - récupérer la balise head du produit
                // - récupérer la blise head générique produit : dans Systeme>Configuration> design/head/title_product, design/head/description_product, design/head/keywords_product
  				// - appliquer les données du produit aux balises génériques 

        	    if($layout->getBlock('product.info') && $headBlock = $layout->getBlock('head'))
        	    {
        	    	$this->setProductVariables();
        	    	$_head_title_template = Mage::getStoreConfig('design/head/title_product');
        	    	$_head_description_template = Mage::getStoreConfig('design/head/description_product');
        	    	$_head_keywords_template = Mage::getStoreConfig('design/head/keywords_product');
					
        	    	$_original_title = $headBlock->getTitle();
        	    	$_original_description = $headBlock->getDescription();
        	    	$_original_keywords = $headBlock->getKeywords();
        	    	
        	    	if( !trim($this->_product->getMetaTitle()) )
        	    	{
        	    		$_title = $this->filter($_head_title_template);
        	    		$headBlock->setTitle($_title);
        	    	}
        	        if( !trim($this->_product->getMetaDescription()) )
        	    	{
        	    		$_description = $this->filter($_head_description_template);
        	    		$headBlock->setDescription($_description);
        	    	}
        	        if( !trim($this->_product->getMetaKeyword()) )
        	    	{
        	    		$_keywords = $this->filter($_head_keywords_template);
        	    		$headBlock->setKeywords($_keywords);
        	    	}
        	    	
                }
                //BALISES METAS DES PAGES CATEGORIES
                // TODO : idem produit
				if($layout->getBlock('category.products') && $headBlock = $layout->getBlock('head'))
        	    {
        	    	$this->setCategoryVariables();
        	    	$_head_title_template = Mage::getStoreConfig('design/head/title_category');
        	    	$_head_description_template = Mage::getStoreConfig('design/head/description_category');
        	    	$_head_keywords_template = Mage::getStoreConfig('design/head/keywords_category');
					
        	    	$_original_title = $headBlock->getTitle();
        	    	$_original_description = $headBlock->getDescription();
        	    	$_original_keywords = $headBlock->getKeywords();
        	    	
        	        if( !trim($this->_category->getMetaTitle()) )
        	    	{
        	    		$_title = $this->filter($_head_title_template);
        	    		$headBlock->setTitle($_title);
        	    	}
        	    	
        	        if( !trim($this->_category->getMetaDescription()) )
        	    	{
        	    		$_description = $this->filter($_head_description_template);
        	    		$headBlock->setDescription($_description);
        	    	}
        	    	
        	        if( !trim($this->_category->getMetaKeyword()) )
        	    	{
        	    		$_keywords = $this->filter($_head_keywords_template);
        	    		$headBlock->setKeywords($_keywords);
        	    	}
        	    	
                	
                	
        	    }
                
				//BALISES METAS DES PAGES FILTREES
                $_filters = Mage::getSingleton('catalog/layer')->getState()->getFilters();
                if(count($_filters)) {
                        $separator = ' '.Mage::getStoreConfig('catalog/seo/title_separator').' ';
                        $s = '';
                        foreach ($_filters as $_filter) $s .= $separator.strip_tags(Mage::helper('cms')->__($_filter->getName()).' '.$_filter->getLabel());
                        
                        $head = array();
                        if(strlen(Mage::getStoreConfig('design/head/title_prefix'))) $head[Mage::getStoreConfig('design/head/title_prefix')] = '';
                        if(strlen(Mage::getStoreConfig('design/head/title_suffix'))) $head[Mage::getStoreConfig('design/head/title_suffix')] = '';
                                
                        $headBlock->setTitle(implode(array_filter(explode($separator,strtr($headBlock->getTitle().$separator.$s,$head))),$separator));
                        $headBlock->setDescription(implode(array_filter(explode($separator,strtr($headBlock->getDescription().$separator.$s,$head))),$separator));
                }
                
                //BALISES METAS TITLE DES PAGES COMMENTAIRE
                if($layout->getBlock('product.info.product_additional_data') && $headBlock = $layout->getBlock('head')) {
                        $headBlock->setTitle($headBlock->getTitle().' - Commentaires des internautes');
                }
                
			}
			
        }
        
		public function filter($value)
    	{
    	    foreach($this->_templateVars as $var => $replacement)
			{
        		$value = preg_replace('#'.$var.'#is', $replacement, $value );
        	}
    		return $value;
    	}
        
	    public function setVariables(array $variables)
	    {
			foreach($variables as $name=>$value)
			{
            	$this->_templateVars[$name] = $value;
        	}
	    }
	    
	    public function setProductVariables()
	    {
	    	$_variables = array();
	    	
	    	$_category_name = '';
	    	$_parentCategory = '';
	    	$_parent_category_name = '';
	    	
			$_product = Mage::registry('current_product');
        	$_category = Mage::registry('current_category');
        	if( $_category instanceof Mage_Catalog_Model_Category )
        	{
        		$_category_name = $_category->getName();
        		$_parentCategory = Mage::getModel('catalog/category')->load($_category->getParentId());
				$_parent_category_name = $_parentCategory->getName();
        	}
        	
        	$this->_product = $_product;
        	$this->_category = $_category;
        	
			$_brand = Mage::getModel('brand/brand')->load( $_product->getBrand() );
			
			$_rootCategoryId = Mage::app()->getStore()->getRootCategoryId();
			
			$_brand_name = $_brand->getNom();
        	$_product_name = $_product->getName();
			$_product_sku = $_product->getSku();
			
	        $_variables['\{\{name\}\}'] = $_product_name;
	        $_variables['\{\{sku\}\}'] = $_product_sku;
	        $_variables['\{\{category\.name\}\}'] = $_category_name;
	        $_variables['\{\{parent\.name\}\}'] = $_parent_category_name;
	        $_variables['\{\{brand\}\}'] = $_brand_name;
	        
	        $this->setVariables($_variables);
	    }
	    public function setCategoryVariables()
	    {
	    	$_variables = array();
        	$_category = Mage::registry('current_category');
        	$this->_category = $_category;
        	$_parentCategory = Mage::getModel('catalog/category')->load($_category->getParentId());
			
			$_rootCategoryId = Mage::app()->getStore()->getRootCategoryId();
			
			$_category_name = $_category->getName();
			$_parent_category_name = $_parentCategory->getName();
			
	        $_variables['\{\{category\.name\}\}'] = $_category_name;
	        $_variables['\{\{parent\.name\}\}'] = $_parent_category_name;
	        
	        $this->setVariables($_variables);
	    }
	    
        
}