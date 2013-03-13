<?php
class WebMods_Solrsearch_Block_Faces extends Mage_Core_Block_Template
{
	protected $solrData = array();
	protected function _construct()
    {
    	$is_ajax = Mage::getStoreConfig('webmods_solrsearch/settings/use_ajax_result_page', 0);
    	if (intval($is_ajax) > 0) {
    		$this->setTemplate('solrsearch/searchfaces.phtml');
    	}else{
    		$this->setTemplate('solrsearch/standard/searchfaces.phtml');
    	}
    }
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
        
    public function getSolrData(){
    	return $this->getData('solrdata');
    }
	/**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
		return parent::_beforeToHtml();
    }
    
    public function getFacetLabel($facetCode){
    	
    	$startPoint = strrpos($facetCode, '_')+1;
    	$endPoint = strlen($facetCode);
    	$attributeCode = substr($facetCode, 0, ($startPoint-1));
    	
    	$facetLabelCache = Mage::app()->loadCache('solr_bridge_'.$facetCode.'_cache');
    	
    	if ( isset($facetLabelCache) && !empty($facetLabelCache) ) {
    		return $facetLabelCache;
    	}else {
    		$entityType = Mage::getModel('eav/config')->getEntityType('catalog_product');
			$catalogProductEntityTypeId = $entityType->getEntityTypeId();
			
			$facetFieldsInfo = Mage::getResourceModel('eav/entity_attribute_collection')
			->setEntityTypeFilter($catalogProductEntityTypeId)
			->setCodeFilter(array($attributeCode))
			->addSetInfo()
			->getData();
			
			$facetLabel = '';
			foreach($facetFieldsInfo as $att){
				if ($att['attribute_code'] == $attributeCode) {
					$facetLabel = $att['frontend_label'];
					Mage::app()->saveCache($facetLabel, 'solr_bridge_'.$facetCode.'_cache', array(), 60*60*24*360);
					break;
				}
			}
			if ($attributeCode == 'category') {
				$facetLabel = $this->helper('catalog')->__('Category');
			}
			return $facetLabel;
    	}
    }
    
	/**
     * Return current URL with rewrites and additional parameters
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getFacesUrl($params=array())
    {
        $paramss = $this->getRequest()->getParams();
        
        $finalParams = array_merge_recursive($params, $paramss);
        
        if (isset($finalParams['p'])) {
        	$finalParams['p'] = 1;
        }
		
    	$urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $finalParams;
        return $this->getUrl('*/*/*', $urlParams);
    }
    
	/**
     * Return current URL with rewrites and additional parameters
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getRemoveFacesUrl($key,$value)
    {
        $paramss = $this->getRequest()->getParams();
        
        $finalParams = $paramss;
        
        if (!is_array($finalParams['fq'][$key]) && !empty($finalParams['fq'][$key])) {
        	unset($finalParams['fq'][$key]);
        }else if (is_array($finalParams['fq'][$key]) && count($finalParams['fq'][$key]) > 0) {
        	foreach ($finalParams['fq'][$key] as $k=>$v) {
        		if ($v == $value) {
        			unset($finalParams['fq'][$key][$k]);
        		}
        	}
        }
		//print_r($finalParams);
    	$urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $finalParams;
        //return '';
        return Mage::getUrl('*/*/*', $urlParams);
    }
}