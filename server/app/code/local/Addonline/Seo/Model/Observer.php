<?php

class Addonline_Seo_Model_Observer {

        public function editMetas($observer) {
            
        	$layout = $observer->getLayout();
			//si le block head existe 
        	if ($headBlock = $layout->getBlock('head')) {
				
                //BALISES METAS DES PAGES PRODUITS
                //TODO : 
                // - récupérer la balise head du produit
                // - récupérer la blise head générique produit : dans Systeme>Configuration> design/head/title_product, design/head/description_product, design/head/keywords_product
  				// - appliquer les données du produit aux balises génériques 
  				              
                //BALISES METAS DES PAGES CATEGORIES
                // TODO : idem produit
                
                
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
			}
                
        }
}