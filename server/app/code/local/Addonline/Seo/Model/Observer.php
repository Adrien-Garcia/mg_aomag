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
				
        		//Flag qui détermine si on affiche les balises des URLs alternatives pour les sites multilingues
        		$addAlternate = false;

                //BALISES METAS DES PAGES PRODUITS
                // - récupérer la balise head du produit
                // - récupérer la blise head générique produit : dans Systeme>Configuration> design/head/title_product, design/head/description_product, design/head/keywords_product
  				// - appliquer les données du produit aux balises génériques 

        	    if($layout->getBlock('product.info'))
        	    {
        	    	$this->_setProductVariables();
        	    	$_head_title_template = Mage::getStoreConfig('catalog/seo/title_product');
        	    	$_head_description_template = Mage::getStoreConfig('catalog/seo/description_product');
        	    	$_head_keywords_template = Mage::getStoreConfig('catalog/seo/keywords_product');
					
        	    	$_original_title = $headBlock->getTitle();
        	    	$_original_description = $headBlock->getDescription();
        	    	$_original_keywords = $headBlock->getKeywords();
        	    	
        	    	if( !trim($this->_product->getMetaTitle()) && trim($_head_title_template))
        	    	{
        	    		$_title = $this->_filter($_head_title_template);
        	    		$headBlock->setTitle($_title);
        	    	}
        	        if( !trim($this->_product->getMetaDescription()) && trim($_head_description_template) )
        	    	{
        	    		$_description = $this->_filter($_head_description_template);
        	    		$headBlock->setDescription($_description);
        	    	}
        	        if( !trim($this->_product->getMetaKeyword()) && trim($_head_keywords_template))
        	    	{
        	    		$_keywords = $this->_filter($_head_keywords_template);
        	    		$headBlock->setKeywords($_keywords);
        	    	}
        	    	
        	    	// Si l'URL canonique est la même que l'url courante, on n'affiche pas la balise canonical
        	    	// Par contre on affiche la balise alternate
        	    	$_product = Mage::registry('current_product');
        	    	$params = array('_ignore_category' => true);
        	    	$_productUrlSid = $_product->getUrlModel()->getUrl($_product, $params);
        	    	$_productUrl = substr($_productUrlSid, 0, strpos($_productUrlSid, '?'));
        	    	$currentUrl = Mage::helper('core/url')->getCurrentUrl();
        	    	if ($_productUrl == $currentUrl) {
        	    		if (Mage::helper('catalog/product')->canUseCanonicalTag()) {
        	    			$headBlock->removeItem('link_rel', $_productUrl);
        	    			$headBlock->removeItem('link_rel',$_productUrlSid); // parfois la canonical est enregistrée avec le SID...
        	    		}
    	    	    	$addAlternate = true;
        	    	}
        	    	

                }
                //BALISES METAS DES PAGES CATEGORIES
                // - récupérer la balise head de la catégorie
                // - récupérer la blise head générique catégorie : dans Systeme>Configuration> design/head/title_category, design/head/description_category, design/head/keywords_category
  				// - appliquer les données de la category aux balises génériques 

                if($layout->getBlock('category.products'))
        	    {
        	    	$this->_setCategoryVariables();
        	    	$_head_title_template = Mage::getStoreConfig('catalog/seo/title_category');
        	    	$_head_description_template = Mage::getStoreConfig('catalog/seo/description_category');
        	    	$_head_keywords_template = Mage::getStoreConfig('catalog/seo/keywords_category');
					
        	    	$_original_title = $headBlock->getTitle();
        	    	$_original_description = $headBlock->getDescription();
        	    	$_original_keywords = $headBlock->getKeywords();
        	    	
        	        if( !trim($this->_category->getMetaTitle()) && trim($_head_title_template) )
        	    	{
        	    		$_title = $this->_filter($_head_title_template);
           	    		$headBlock->setTitle($_title);
        	    	}
        	    	
        	        if( !trim($this->_category->getMetaDescription()) && trim($_head_description_template) )
        	    	{
        	    		$_description = $this->_filter($_head_description_template);
        	    		$headBlock->setDescription($_description);
        	    	}
        	    	
        	        if( !trim($this->_category->getMetaKeyword())  && trim($_head_keywords_template) )
        	    	{
        	    		$_keywords = $this->_filter($_head_keywords_template);
        	    		$headBlock->setKeywords($_keywords);
        	    	}
        	    	
        	    	//on set la balise robots défini au niveau de la catégorie
        	    	$meta_robots = $headBlock->getRobots();
        	    	if ($this->_category->getMetaRobots()) {
        	    		$meta_robots = $this->_category->getMetaRobots();
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
        	    	
        	    		//Si on a au moins un filtre on n'indexe pas sur google, sauf si il n'y en a qu'un seul et qu'il est configuré pour
        	    		$meta_robots = $headBlock->getRobots();
        	    		foreach ($_filters as $_filter) {
        	    			if ($_filter->getFilter()->getRequestVar()== 'cat') {
        	    				$meta_robots =  'NOINDEX,NOFOLLOW'; //si il y a le filtre catéogire : on n'indexe pas la page
        	    			} else {
        	    				$attribute = $_filter->getFilter()->getAttributeModel();
        	    				$seoattribute = Mage::getModel('seo/attribute')->load($attribute->getId(), 'attribute_id');
        	    				if ($seoattribute->getData('meta_robots') == 'NOINDEX,NOFOLLOW' || $seoattribute->getData('meta_robots') == '') {
        	    					$meta_robots = 'NOINDEX,NOFOLLOW'; //si il y un filtre configuré en NOINDEX,NOFOLLOW : on n'indexe pas la page
        	    				}
        	    				if ($seoattribute->getData('meta_robots') == 'NOINDEX,FOLLOW' &&  $meta_robots != 'NOINDEX,NOFOLLOW') {
        	    					$meta_robots = $seoattribute->getData('meta_robots');
        	    				}
        	    				if ($seoattribute->getData('meta_robots') == 'INDEX,NOFOLLOW' && $meta_robots != 'NOINDEX,NOFOLLOW' &&  $meta_robots != 'NOINDEX,FOLLOW') {
        	    					$meta_robots = $seoattribute->getData('meta_robots');
        	    				}
        	    				if ($seoattribute->getData('meta_robots') == 'INDEX,FOLLOW' && $meta_robots != 'NOINDEX,NOFOLLOW' &&  $meta_robots != 'NOINDEX,FOLLOW' &&  $meta_robots != 'INDEX,NOFOLLOW') {
        	    					$meta_robots = $seoattribute->getData('meta_robots');
        	    				}
        	    			}
        	    		}
        	    		
        	    	}
        	    	$headBlock->setRobots($meta_robots);
        	    	
        	    	$toolbarBlock = $layout->getBlock("product_list_toolbar");
        	    	$order = Mage::app()->getRequest()->getParam($toolbarBlock->getOrderVarName());
        	    	$pager = Mage::app()->getRequest()->getParam($toolbarBlock->getPageVarName());
        	    	$mode = Mage::app()->getRequest()->getParam($toolbarBlock->getModeVarName());
        	    	$limit = Mage::app()->getRequest()->getParam($toolbarBlock->getLimitVarName());
        	    	
        	    	if ( strpos ($meta_robots,'INDEX') !== 0 ) { //Si la page est n'est pas indexée, on enlève la canonical car elle ne sert à rien
        	    		if (Mage::helper('catalog/category')->canUseCanonicalTag()) {
        	    			$headBlock->removeItem('link_rel', $this->_category->getUrl());
        	    		}
        	    	} else {
	        	    	
        	    		if (count($_filters)) {	//Si la page est est indexé avec 1 filtre, on enlève la canonical pour indexer sur cette page filtrée uniquement
							
        	    		    if (Mage::helper('catalog/category')->canUseCanonicalTag()) {
        	    				$headBlock->removeItem('link_rel', $this->_category->getUrl());
        	    			}
        	    			//Mais si la page est aussi paginée ou triée ... on ajoute une cannonical vers la page Filtrée et indexée
        	    			if($order || $pager || $mode || $limit) {
        	    				foreach ($_filters as $_filter) {
        	    					if ($s) {
        	    						$s='?';
        	    					} else {
        	    						$s.='&';
        	    					}
        	    					$s.= $_filter->getFilter()->getAttributeModel()->getAttributeCode().'='.$_filter->getValue();
        	    				}
        	    				$headBlock->addLinkRel('canonical', $this->_category->getUrl().$s);
        	    			}
        	    			
        	    			
        	    		} else {

        	    			//Si il n'y a pas de pagination, pas de tri, pas de mode d'affichage ni de nombre de page :
        	    			// on n'affiche pas la canonical : on n'affiche pas de canonical vers soit-même
        	    			// par contre on affiche les balises alternates pour le multi-site
        	    			if(!$order && !$pager && !$mode && !$limit) {
        	    				if (Mage::helper('catalog/category')->canUseCanonicalTag()) {
        	    					$headBlock->removeItem('link_rel', $this->_category->getUrl());
        	    				}
        	    				$addAlternate = true;
        	    			}
        	    			
        	    		}
        	    	}
        	    	
        	    	        	    	
        	    }
                
                //BALISES METAS TITLE DES PAGES COMMENTAIRE
                if($layout->getBlock('product.info.product_additional_data')) {
                        $headBlock->setTitle($headBlock->getTitle().' - Commentaires des internautes');
                }

                //si on est sur la home :  afficher les URL alternatives 
                $controller = $observer->getEvent()->getAction()->getRequest()->getControllerName();
                $route      = $observer->getEvent()->getAction()->getRequest()->getRouteName();                
                if ( $controller == 'index' && $route == 'cms') {
                	$addAlternate = true;
                }
                
                if ($addAlternate) {
//TODO : récupérer la liste des sites multilingues correspondants au site en cours
//       attention il faut récupérer les store view qui ont le même nom de domaine, avec une code pour différencier seulement, 
//      ou bien tous les store view même si ils ont des noms de domaine différents ? 
// 					foreach ($stores as $autreStore) {  		
//                 		$sHrefLang   = 'en-GB'; //récupérer le code langue du store
//                 		$href = Mage::app()->getStore($autreStore->getId())->getCurrentUrl(false);
//                 		$headBlock->addItem('link_rel', $href, 'rel="alternate" hreflang="' . $sHrefLang . '"');
//                  }
                }
                	
        	}
			
        }
        
		private function _filter($value)
    	{
    		foreach($this->_templateVars as $var => $replacement)
			{
				$value = preg_replace('#'.$var.'#is', $replacement, $value );
        	}
    		return $value;
    	}
        
	    private function _setVariables(array $variables)
	    {
			foreach($variables as $name=>$value)
			{
            	$this->_templateVars[$name] = $value;
        	}
	    }
	    
	    private function _setProductVariables()
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
        	
			$_rootCategoryId = Mage::app()->getStore()->getRootCategoryId();
			
        	$_product_name = $_product->getName();
			$_product_sku = $_product->getSku();
			
	        $_variables['\{\{name\}\}'] = $_product_name;
	        $_variables['\{\{sku\}\}'] = $_product_sku;
	        $_variables['\{\{category\.name\}\}'] = $_category_name;
	        $_variables['\{\{parent\.name\}\}'] = $_parent_category_name;
	        
	        //On vérifie si le module Addonline_Brand est installé, sinon on ne traite pas les lignes ci-dessous
	        $modules = Mage::getConfig()->getNode('modules')->children();
	        $modulesArray = (array)$modules;
	        if(isset($modulesArray['Addonline_Brand'])) {
	        	$_brand = Mage::getModel('brand/brand')->load( $_product->getBrand() );
	        	$_brand_name = $_brand->getNom();
	        	$_variables['\{\{brand\}\}'] = $_brand_name;
	        }
	        
	        $this->_setVariables($_variables);
	    }
	    
	    private function _setCategoryVariables()
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
	        
	        $this->_setVariables($_variables);
	    }
	    
	    /*
	     *  Ajoute un champ Robots au formulaire d'édition des pages CMS (onglet Données Meta)
	     */
	    public function cmsMetaForm($observer) {
	    	$form = $observer->getEvent()->getForm();
	    	
	    	/*
	    	 * Checking if user have permissions to save information
	    	*/
	    	if (Mage::getSingleton('admin/session')->isAllowed('cms/page/save')) {
	    		$isElementDisabled = false;
	    	} else {
	    		$isElementDisabled = true;
	    	}
	    	
	    	$fieldset = $form->getElement('meta_fieldset');
			$values=array_merge(array(""=>"Utiliser la configuration par defaut"),Mage::getSingleton('adminhtml/system_config_source_design_robots')->toOptionArray());
	    	$fieldset->addField('meta_robots', 'select', array(
	    			'name' => 'meta_robots',
	    			'label' => Mage::helper('cms')->__('Robots'),
	    			'title' => Mage::helper('cms')->__('Robots'),
	    			'values'   => $values,
	    			'disabled'  => $isElementDisabled
	    	));
	    }
	    
	    public function loadAttribute($event) {

	    	$attribute = $event->getAttribute();
	    	$attribute_id = ( int ) $attribute->getAttributeId();
	    	
 	    	$seoattribute = Mage::getModel('seo/attribute')->load($attribute_id, 'attribute_id');

 	    	if ($seoattribute && $seoattribute->getId()) {
	    			
 	    		$attribute->addData($seoattribute->getData());

 	    	}
	    }

	    public function saveAttribute($event) {

	    	$attribute_id = ( int ) $event->getAttribute()->getAttributeId();
	    	$meta_robots = $event->getAttribute()->getData('meta_robots');
	    	$seoattribute = Mage::getModel('seo/attribute')->load($attribute_id, 'attribute_id');

	    	if (! $seoattribute->getData('attribute_id')) {
	    		$seoattribute->setData('attribute_id', $attribute_id);
	    		$seoattribute->isObjectNew(true);
	    		 
	    	}

	    	$seoattribute->setData('meta_robots',$meta_robots);
	    	
	    	$seoattribute->save();

	    }
	
	    /*
	     *  Ajoute un champ SEO Robots au formulaire d'édition d'un attribut
	    */
	    public function attributeForm($observer) {
	    	$form = $observer->getEvent()->getForm();
	    
	    	$fieldset = $form->addFieldset('addonline_seo_fieldset', array('legend'=>Mage::helper('catalog')->__('Search Engine Optimizations')));

	    	$values=array_merge(array(""=>Mage::helper('adminhtml')->__('Use Default Value')),Mage::getSingleton('adminhtml/system_config_source_design_robots')->toOptionArray());
	    	$fieldset->addField('meta_robots', 'select', array(
	    			'name' => 'meta_robots',
	    			'label' => Mage::helper('cms')->__('Meta Robots'),
	    			'title' => Mage::helper('cms')->__('Meta Robots'),
	    			'values'   => $values
	    	));
	    }
	     
	    public function redirectCategoryProductUrl($observer)
	    {
	    	//Si l'utilisation des chemins des catégorie n'est PAS utilisé, mais qu'une URL avec le chemin
	    	//d'une catégorie est demandée, alors on fait une redirection 301 vers l'URL sans le chemin de la catégorie   
			if (!Mage::getStoreConfig(Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_USE_CATEGORY)) {
				
				$idCategory = Mage::app()->getRequest()->getParam('category', false);

				if ($idCategory) {
					$_product = $observer->getEvent()->getProduct();
					$params = array('_ignore_category' => true, '_nosid'=> true);
					$_productUrl = $_product->getUrlModel()->getUrl($_product, $params);
					Mage::app()->getFrontController()->getResponse()->setRedirect($_productUrl, 301);
					Mage::app()->getResponse()->sendResponse();
					exit;
				}			
				
			}
	    }
}
  