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
        /* ADDONLINE : modif version 1.8.7, désactivé car fait planter dans le cas des résultats non ajax
    	$_solrDataArray = $this->getSolrData();
    	
    	$paramss = $this->getRequest()->getParams();
    	
    	if( isset($_solrDataArray['responseHeader']['params']['q']) && !empty($_solrDataArray['responseHeader']['params']['q']) ) {
        	if ($paramss['q'] != $_solrDataArray['responseHeader']['params']['q']) {
        		$paramss['q'] = $_solrDataArray['responseHeader']['params']['q'];
        	}
        }
        
        foreach ($params as $key=>$item) {
        	if ($key == 'fq') {
        		foreach ($item as $k=>$v) {
        			if (isset($paramss[$key][$k]) && $v == $paramss[$key][$k]){
        			
        			}else{
        				$finalParams = array_merge_recursive($params, $paramss);
        			}
        		}
        	}
        }
        */
        
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
    
	public function parseCategoryPathFacet($faces){
	
    	$facetCountArray = array();
	    $array = array();
	    foreach ($faces as $path => $count) {
	      $path = trim($path, '/');
	      $path = substr($path, 0,strrpos($path, '/'));
	      $path = substr($path, strpos($path, '/')+1, strlen($path));
	     // $path = $path.'/'.$count;
	      
	      $facetCountArray[$path] = $count;
	      
	      $list = explode('/', $path);
	      $n = count($list);
	      
	
	      $arrayRef = &$array; // start from the root
	      for ($i = 0; $i < $n; $i++) {
	        $key = $list[$i];
	        $arrayRef = &$arrayRef[$key]; // index into the next level
	      }
	    }
	    
	    //die(print_r($array));
	    
	    $path = '';
	    $level = 0;
	    $lastLevel = 0;
	    $keepCountNumber = 0;
	    $this->toUL($array, $facetCountArray, $path, $level, $lastLevel, $keepCountNumber);
	    
	    //print_r($facetCountArray);
	    
    }
    
    //output a multi-dimensional array as a nested UL
    
	protected function toUL($array, $facetCountArray, &$path, &$level, &$lastLevel, &$countNumber){
		$solrModel = Mage::getModel('solrsearch/solr');
		$filterQuery =$solrModel->getStandardFilterQuery();
		
	    //start the UL
	    echo "<ol>\n";    
	    $countNumber = count($array);
	    $i = 1;
	    //loop through the array
	    foreach($array as $key => $member){
	    	if ($level == 0) {
	    		$path = $key;
	    	}
	 
	        //check for value member
	        if( isset($key) && is_array($member)){
	        	if ($level > 0) {
	        		$path = $path.'/'.$key;
	        	}
	        	
	        	$activeClass = '';
				//$facetUrl = $this->getHrefFacet($key, $path, $facetCountArray);
				if (isset($filterQuery['category_facet']) && in_array($this->facetFormat($key), $filterQuery['category_facet'])){
					$activeClass = 'active';
					//$facetUrl = $this->getRemoveHrefFacet($key, $path, $facetCountArray);
				}
	        	
	        	echo "<li><a href=\"".$this->getHrefFacet($key, $path, $facetCountArray)."\" class=\"facet-item ".$activeClass."\">{$this->facetFormat($key, $path, $facetCountArray)}</a>"./*'Count:'.$countNumber.'-Index'.$i.'-L:'.$level.$path.*/"</li>\n";
	            $level++;
	            //if the member is another array, start a fresh li
	            echo "<li>\n";
	            //and pass the member back to this function to start a new ul
	            $this->toUL($member, $facetCountArray, $path, $level, $lastLevel, $countMember);
	            //then close the li
	            echo "</li>\n";
	            
	            
	            
	        }
	        else if( isset($key) && !is_array($member) ){
	        	
	        	$lastPath = $path.'/'.$key;
	        	
	        	$activeClass = '';
				
				//$facetUrl = $this->getHrefFacet($key, $path, $facetCountArray);
	        	if (isset($filterQuery['category_facet']) && in_array($this->facetFormat($key), $filterQuery['category_facet'])){
					$activeClass = 'active';
					//$facetUrl = $this->getRemoveHrefFacet($key, $path, $facetCountArray);
				}
	        	
	        	echo "<li><a href=\"".$this->getHrefFacet($key, $path, $facetCountArray)."\" class=\"facet-item ".$activeClass."\">{$this->facetFormat($key, $lastPath, $facetCountArray)}</a>"./*'Count:'.$countNumber.'-Index'.$i.'-L:'.$level.$lastPath.*/"</li>\n";
	        	
	        	if ($i == $countNumber) {
	        		$tmpPath = trim($path, '/');
	        		$path = substr($tmpPath, 0,strrpos($tmpPath, '/'));
	        	}
	        	
	        	$level++;
	        	
	        }
	        $level--;
	        $i++;
	    }
		if ($level == 0) {
	    $path = '';
		}
	    //finally close the ul
	    echo "</ol>\n";
	}
	
	public function facetFormat($key, $path="", $facetCountArray=array()) {
		$count = $facetCountArray[trim($path, '/')];
		if ($count > 0) {
			return str_replace('_._._', '/', $key.' ('.$count.')');
		}else{
			return str_replace('_._._', '/', $key);
		}
	}
	
	protected function getHrefFacet($key, $path, $facetCountArray){
		$count = $facetCountArray[trim($path, '/')];
		if ($count > 0) {
			return $this->getFacesUrl(array('fq'=>array('category'=>str_replace('_._._', '/', $key))));
		}else{
			return 'javascript:;';
		}
	}
	
	protected function getRemoveHrefFacet($key, $path, $facetCountArray){
		$count = $facetCountArray[trim($path, '/')];
		if ($count > 0) {
			return $this->getRemoveFacesUrl('category', str_replace('_._._', '/', $key));
		}else{
			return 'javascript:;';
		}
	}
}