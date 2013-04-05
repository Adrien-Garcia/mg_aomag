<?php
class WebMods_Solrsearch_Block_Result extends Mage_Core_Block_Template
{
	protected $products;
	protected $facetFieldsLabels;
	
	protected function _construct()
    {
    	$this->setTemplate('solrsearch/result.phtml');
    }
	
	public function _prepareLayout()
    {
    	return parent::_prepareLayout();
    }
    
    protected function getProducts(){
    	
    	$solrModel = Mage::getModel('solrsearch/solr');
    	$store = Mage::app()->getStore();
    	$url = $solrModel->buildRequestUrl($store);
    	$returnData = $solrModel->doRequest($url, $store);
    	
		$facetFieldsLabels['category_facet'] = $this->__('Category');	
		
		Mage::getSingleton('core/session')->setSolrFacetFieldsLabels($facetFieldsLabels);
		
		$toolbar = $this->getToolbarBlock();
		$currentPage = 1;//$toolbar->getCurrentPage()?$toolbar->getCurrentPage():$_GET['p'];
		if(!empty($params['p'])){
			$currentPage = $params['p'];
		}
		$itemsPerPage= 9;//$toolbar->getItemPerPage()?$toolbar->getItemPerPage():9;
		$start = $itemsPerPage * ($currentPage - 1);
		
		if (isset($returnData['response']['numFound']) && intval($returnData['response']['numFound']) > 0){
			return $returnData;
		}else{
			$url = trim($solr_server_url,'/').'/'.$solr_index.'/select/?q='.urlencode(strtolower($returnData['spellcheck']['suggestions']['collation']));
			
			$url .= '&facet.field=category_facet';	
			$returnData = $solrModel->doRequest($url,$arguments);
		}
		return $returnData;
    }
    
    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
    	$params = $this->getRequest()->getParams();
	    				
		//order by
		$orderby = "";
		$orderby = Mage::getSingleton('core/session')->getSolrSortOrderBy();
		
		if(isset($params['orderby']) && !empty($params['orderby'])) {
			$orderby = $params['orderby'];
		}
		Mage::getSingleton('core/session')->setSolrSortOrderBy($orderby);
		
		//direction
		$direction = "asc";
		$direction = Mage::getSingleton('core/session')->getSolrSortOrderDirection();
		if(isset($params['direction']) && !empty($params['direction'])) {
			$direction = $params['direction'];
		}
		Mage::getSingleton('core/session')->setSolrSortOrderDirection($direction);
		
		//mode
		$mode = "grid";
		$mode = Mage::getSingleton('core/session')->getSolrSearchResultMode();
		if(isset($params['mode']) && !empty($params['mode'])) {
			$mode = $params['mode'];
		}
		Mage::getSingleton('core/session')->setSolrSearchResultMode($mode);
		
		
		$solrData = $this->getData('solrdata');
		
		$documents = $solrData['response']['docs'];
		
		
		$productIds = array();
		if(is_array($documents) && count($documents) > 0) {
			foreach ($documents as $_doc) {
				$productIds[] = $_doc['products_id'];
			}
		}
    	$collection = Mage::getResourceModel('catalog/product_collection');
		$collection->addAttributeToFilter('entity_id', array('in' => $productIds));
		$collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addFieldToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
			//->addFieldToFilter('is_in_stock', 1)
			->addFieldToFilter(
                array(
                     array('attribute'=>'visibility','eq'=>Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH),
                     array('attribute'=>'visibility','eq'=>Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH)
                )
        	)
            ->addTaxPercents();
			//->addAttributeToSort($orderby, $direction);
		if (empty($orderby)){
			$collection->getSelect()->order("find_in_set(e.entity_id,'".implode(',',$productIds)."')");	
		}else {
			$collection->addAttributeToSort($orderby, $direction);
		}
		
		$this->setProductCollection($collection);
		
    	$toolbar = $this->getToolbarBlock();
    	$toolbar->setData('mode', $mode);
    	$toolbar->setData('direction', $direction);
    	$toolbar->setData('orderby', $orderby);
    	$toolbar->setSolrData($solrData);
    	$this->setChild('toolbar', $toolbar);
    	//echo $collection->getSelect();
    	return parent::_beforeToHtml();
    }
    
	/**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock()
    {
        $block = $this->getLayout()->createBlock('solrsearch/result_toolbar', microtime());
        return $block;
    }
	/**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getOptionsBlock()
    {
        $block = $this->getLayout()->createBlock('solrsearch/result_options', microtime());
        return $block;
    }
	/**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getFacesBlock()
    {
        $block = $this->getLayout()->createBlock('solrsearch/searchfaces', microtime());
        return $block;
    }
    
	/**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }
    
	/**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getOptionsHtml()
    {
        return $this->getChildHtml('options');
    }
    public function setFacetFieldsLabels($facetFieldsLabels){
    	$this->facetFieldsLabels = $facetFieldsLabels;
    }
	public function getFacetFieldsLabels(){
    	return $this->facetFieldsLabels;
    }
	
	public function getPriceHtml($product)
    {
        $this->setTemplate('catalog/product/price.phtml');
        $this->setProduct($product);
        return $this->toHtml();
    }
	public function getAddToCartUrl($product){
    	$product_list_block = $this->getLayout()->getBlockSingleton('catalog/product_list');
    	return $product_list_block->getAddToCartUrl($product,array('_secure'=>Mage::app()->getFrontController()->getRequest()->isSecure()));
    }
    public function _getAttributeAdminLabel($attribute_code, $attributeValueId, $admin=false){
    	if($admin) {
    		$filter = 0;
    	} else {
    		$filter = 1;
    	}
    	$attribute = Mage::getModel('catalog/product')->getResource()->getAttribute($attribute_code);
    	$_collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
    	->setStoreFilter($filter)
    	->setAttributeFilter($attribute->getId())
    	->load();
    
    
    	foreach( $_collection->toOptionArray() as $key => $_option ) {
    		if ($_option['value'] == $attributeValueId){
    			return trim($_option['label']);
    		}
    	}
    }
}