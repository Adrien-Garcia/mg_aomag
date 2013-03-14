<?php
class WebMods_Solrsearch_Model_Solr extends Mage_Core_Model_Abstract {
	//queryText
	public $queryText = '';
	//Page per items
	protected $_rows = 9;
	//start off set
	protected $_start = 0;	
	//Field list
	protected $_fieldList = 'products_id,name_varchar';
	//Query field - which field search for
	protected $_queryField = 'textSearch';
	//Search OP
	protected $_mm = '100%';
	//Boost query field
	protected $_boostQuery = '';
	//Filter query
	protected $_filterQuery = '';
	//Facet fields
	protected $_facetFields = array('category_facet');
	//Boost fields
	protected $_boostfields = array();
	
	protected function _construct()
    {
        $this->_init('solrsearch/solr');
        $this->getConfiguredFields();
    }
    
    protected function getRows() {
    	return $this->_rows;
    }
	protected function setRows($rows) {
    	$this->_rows = $rows;
    }
    
	protected function getStart() {
    	return $this->_start;
    }
	protected function setStart($start) {
    	$this->_start = $start;
    }
    
	protected function getFieldList() {
    	return $this->_fieldList;
    }
	protected function setFieldList($fieldList) {
		$fieldListString = $this->_fieldList;
		if (is_array($fieldList)) {
			$fieldListString = @implode(',', $fieldList);
		}else {
			$fieldListString = $fieldList;
		}
    	$this->_fieldList = $fieldListString;
    }
    
	protected function getQueryField() {
    	return $this->_queryField;
    }
	protected function setQueryField($queryField) {
		if (is_array($queryField)) {
			$queryField = @implode(',', $queryField);
		}
    	$this->_queryField = $queryField;
    }
    
    protected function getBoostQuery($rebuild=FALSE){
    	if (!$rebuild) {
    		return $this->_boostQuery;
    	}else{
    		$q = $this->queryText;
    		$boostString = '';
    		foreach($this->_boostfields as $attribute){
			   	if(isset($attribute['attribute_code']) && !empty($attribute['attribute_code']) && $attribute['weight'] > 0){
			    	$boostString .= $attribute['attribute_code'].':'.(empty($attribute['value'])?$q:$attribute['value']).''."^".$attribute['weight']." ";
			   	}
    		}
    		return $boostString;
    	}
    }
    
	protected function setBoostQuery($boostQueryArray){
    	$this->_boostQuery = $boostQueryArray;
    }
    
	protected function getSearchOp() {
    	return $this->_mm;
    }
	protected function setSearchOp($op) {
    	$this->_mm = $op;
    }
    
	protected function getFilterQuery() {
    	return $this->_filterQuery;
    }
	protected function setFilterQuery($filterQuery) {
    	$this->_filterQuery = $filterQuery;
    }
    
	protected function getFacetFields() {
    	return $this->_facetFields;
    }
	protected function setFacetFields($facetFields) {
    	$this->_facetFields = $facetFields;
    }
    
    public function doRequest($url, $store, $args = array()) {
    	$arguments = array(
			'json.nl' => 'map',
			'rows' => $this->getRows(),
			'start' => $this->getStart(),
			'fl' => $this->getFieldList(),
			'qf' => $this->getQueryField(),
			'bq' => $this->getBoostQuery(),
    		//'fq' => $this->getFilterQuery(),
			'spellcheck' => 'true',
			'spellcheck.collate' => 'true',
			'facet' => 'true',
			//'facet.field' => $this->getFacetFields(),
			'facet.mincount' => 1,
			'timestamp' => time(),
			'mm' => $this->getSearchOp(),
			'defType'=> 'dismax',
    		'stats' => 'true',
    		'stats.field' => 'price_decimal',
			'wt'=> 'json',			
		);
		
		//Facet fields
		$facetFieldsStr = '';
		$facetFieldsArr = $this->getFacetFields();
		foreach ($facetFieldsArr as $fieldKey) {
			$facetFieldsStr .= 'facet.field='.$fieldKey.'&';
		}
		$facetFieldsStr = trim($facetFieldsStr,'&');
		if (!empty($facetFieldsStr)) {
			$url .= '&'.$facetFieldsStr;
		}
		
    	$filterQuery = Mage::getSingleton('core/session')->getSolrFilterQuery();
    	if ($this->getStandardFilterQuery()) {
    		$filterQuery = $this->getStandardFilterQuery();
    	}

		$filterQueryArray = array();
		foreach($filterQuery as $key=>$filterItem){
			if(count($filterItem) > 0){
				$query = '';
				foreach($filterItem as $value){
					if ($key == 'price_decimal') {
						$query .= $key.':'.urlencode(trim($value)).'+OR+';
					}else{
						$query .= $key.':%22'.urlencode(trim($value)).'%22+OR+';
					}
				}
				
				$query = trim($query, '+OR+');
				
				$filterQueryArray[] = $query;
			}			
		}
				
		if(count($filterQueryArray) > 0) {
			if(count($filterQueryArray) < 2) {
				$url .= '&fq='.$filterQueryArray[0];
			}else{
				$url .= '&fq='.'%28'.@implode('%29+AND+%28', $filterQueryArray).'%29';
			}
		}
		
		$isAuthentication = 0;
		$authUser = '';
		$authPass = '';
		
		$isAuthenticationCache = Mage::app()->loadCache('solr_bridge_is_authentication');
	    if ( isset($isAuthenticationCache) && !empty($isAuthenticationCache) ) {
			$isAuthentication = $isAuthenticationCache;
			$authUser = Mage::app()->loadCache('solr_bridge_authentication_user');
			$authPass = Mage::app()->loadCache('solr_bridge_authentication_pass');
		}else {
			// Save data to cache
			$isAuthentication = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url_auth', $store->getId());
			$authUser = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url_auth_username', $store->getId());
			$authPass = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url_auth_password', $store->getId());
		
			Mage::app()->saveCache($isAuthentication, 'solr_bridge_is_authentication', array(), 60*60*24);
			Mage::app()->saveCache($authUser, 'solr_bridge_authentication_user', array(), 60*60*24);
			Mage::app()->saveCache($authPass, 'solr_bridge_authentication_pass', array(), 60*60*24);
		}		
				
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $arguments);
		
		//print_r($arguments);
		
		if (isset($isAuthentication) && $isAuthentication > 0 ) {				
			curl_setopt($ch, CURLOPT_USERPWD, $authUser.':'.$authPass);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		}
		
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		  
		curl_setopt( $ch, CURLOPT_USERAGENT, isset($_GET['user_agent']) ? $_GET['user_agent'] : $_SERVER['HTTP_USER_AGENT'] );
		
		$returnDataRaw = curl_exec( $ch );
		
		$returnData = json_decode($returnDataRaw,true);
		
		
		if(count($filterQueryArray) > 0) return $returnData;

    	if (isset($returnData['response']['numFound']) && intval($returnData['response']['numFound']) > 0){
			
			return $returnData;
			
		}else{
			if (isset($returnData['spellcheck']['suggestions']['collation'])) {
				$queryText = strtolower($returnData['spellcheck']['suggestions']['collation']);
			}
			
			if (empty($queryText)) $queryText = $this->getParams('q');
			$this->queryText = $queryText;
			
			$url = $this->buildRequestUrl($store,true,$queryText);
			
			if (!empty($facetFieldsStr)) {
				$url .= '&'.$facetFieldsStr;
			}
			
			$arguments['mm'] = '0%';
			$arguments['bq'] = $this->getBoostQuery(true);
			
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $arguments);
			
			
			if (isset($isAuthentication) && $isAuthentication > 0 ) {				
				curl_setopt($ch, CURLOPT_USERPWD, $authUser.':'.$authPass);
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			}
			
			$returnData = curl_exec( $ch );
			
			$returnData = json_decode($returnData,true);
		}
		
		return $returnData;
    }
    
    public function buildRequestUrl($store, $hasCore=true, $query=""){
    	$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', $store->getStoreId());
		$solr_index = Mage::getStoreConfig('webmods_solrsearch/settings/solr_index', $store->getStoreId());
		//Get all params
		$params = $this->getParams();
		
		$q = "*:*";
		
    	if (!empty($query)) {
			$q = $query;
		}else {	
			if(isset($params['q'])){
				$q = $params['q'];
			}
		}
		if ($hasCore){
			$url = trim($solr_server_url,'/').'/'.$solr_index.'/select/?q='.urlencode(strtolower(trim(trim($q,'"'))));
		}else{
			$url = trim($solr_server_url,'/').'/select/?q='.urlencode(strtolower(trim(trim($q,'"'))));
		}
		
		return $url;
    }
    
    public function getParams($key = "") {
 		$queryString = $_SERVER['QUERY_STRING'];
 		$output = array();
		parse_str($queryString, $output);
		
		if (isset($_POST)) {
			$output = array_merge($output, $_POST);
		}
				
		if (!empty($key) && isset($output[$key]) && !empty($output[$key])) {
			return $output[$key];
		}else if (empty($key)){
			return $output;
		}else{
			return false;
		}
    }
    
    protected function getConfiguredFields(){
    	$q = $this->getParams('q');
    	$boostFieldsArr = array();
    	$boostFields = array();
    	$facetFields = array();
    	
    	$boostWeights = $this->getSearchWeights(); //get static field weight mapping
    	
    	$entityType = Mage::getModel('eav/config')->getEntityType('catalog_product');
		$catalogProductEntityTypeId = $entityType->getEntityTypeId();
		
		$attributesInfo = Mage::getResourceModel('eav/entity_attribute_collection')
		->setEntityTypeFilter($catalogProductEntityTypeId)
		->addSetInfo()
		->getData();
		
    	foreach($attributesInfo as $attribute){
		   if(isset($attribute['solr_search_field_weight']) && !empty($attribute['solr_search_field_weight']) && $attribute['solr_search_field_weight'] > 0){
		    	$boostFields[$attribute['attribute_code']] = $attribute['attribute_code'].'_boost:'.$q.''."^".$attribute['solr_search_field_weight'];
		   		$boostFieldsArr[] = array('attribute_code'=>$attribute['attribute_code'].'_boost','weight'=>$attribute['solr_search_field_weight'],'value'=>'');
		   }
		   if (isset($attribute['solr_search_field_boost']) && !empty($attribute['solr_search_field_boost'])) {
		   		$boostValues = explode("\n", $attribute['solr_search_field_boost']);
		   		$boostString = "";
		   		foreach ($boostValues as $boostValue) {
		   			$pair = explode('|', trim($boostValue));
		   			if (isset($pair[0]) && !empty($pair[0]) && isset($pair[1]) && !empty($pair[1])) {
		   				$boostString .= $attribute['attribute_code'].'_boost:'.$pair[0].''."^".$boostWeights[$pair[1]]." ";
		   				$boostFieldsArr[] = array('attribute_code'=>$attribute['attribute_code'].'_boost','weight'=>$attribute['solr_search_field_weight'],'value'=>$pair[0]);
		   			}
		   		}
		   		$boostFields[$attribute['attribute_code']] = trim($boostString);
		   }
		   
		   if (isset($attribute['is_filterable_in_search']) && $attribute['is_filterable_in_search'] > 0) {
		   		$facetFields[] = $attribute['attribute_code'].'_facet';
		   }
		   
		}
		
		if (count($boostFields)) {
			$boostFieldsString = @implode(" ", $boostFields);
			$this->setBoostQuery($boostFieldsString);
			$this->_boostfields = $boostFieldsArr;
		}else {
			$boostFieldsString = 'name_boost:'.$q.'^80 category_boost:'.$q.'^60';
			$this->setBoostQuery($boostFieldsString);
			$this->_boostfields = array(
				array('attribute_code'=>'name_boost', 'weight'=>80, 'value'=>''),
				array('attribute_code'=>'category_boost', 'weight'=>60, 'value'=>'')
			);
		}
		
		
		$use_category_as_facet = Mage::getStoreConfig('webmods_solrsearch/settings/use_category_as_facet', 0);
    	if ($use_category_as_facet) {
			$facetFields[] = 'category_facet';			
		}
		
    	if (count($facetFields)) {			
			//$facetFieldsString = @implode(",", $facetFields);
			$this->setFacetFields($facetFields);
		}
		
		$currentPage = 1;
		$page = $this->getParams('p');
		if(!empty($page) && is_numeric($page)){
			$currentPage = $page;
		}
		$itemsPerPage= 32;
		$itemsPerPageSettings = Mage::getStoreConfig('webmods_solrsearch/settings/items_per_page', 0);
		if (!empty($itemsPerPageSettings) && is_numeric($itemsPerPageSettings)) {
			$itemsPerPage = $itemsPerPageSettings;
		}
		$start = $itemsPerPage * ($currentPage - 1);
		$this->setStart($start);
		$this->setRows($itemsPerPage);
    }
    
    protected function getSearchWeights() {
    	$weights = array();
		$index = 1;
		foreach (range(10, 200, 10) as $number) {
		    $weights[$index] = $number;
		    $index++;
		}
		return $weights;
    }
    
    public function getFullQuery($store) {
    	$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', $store->getStoreId());
		$solr_index = Mage::getStoreConfig('webmods_solrsearch/settings/solr_index', $store->getStoreId());
		
		$url = trim($solr_server_url,'/').'/'.$solr_index.'/select/?q=*:*&wt=json';
		
		$isAuthentication = 0;
		$authUser = '';
		$authPass = '';
		
		$isAuthenticationCache = Mage::app()->loadCache('solr_bridge_is_authentication');
	    if ( isset($isAuthenticationCache) && !empty($isAuthenticationCache) ) {
			$isAuthentication = $isAuthenticationCache;
			$authUser = Mage::app()->loadCache('solr_bridge_authentication_user');
			$authPass = Mage::app()->loadCache('solr_bridge_authentication_pass');
		}else {
			// Save data to cache
			$isAuthentication = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url_auth', $store->getId());
			$authUser = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url_auth_username', $store->getId());
			$authPass = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url_auth_password', $store->getId());
		
			Mage::app()->saveCache($isAuthentication, 'solr_bridge_is_authentication', array(), 60*60*24);
			Mage::app()->saveCache($authUser, 'solr_bridge_authentication_user', array(), 60*60*24);
			Mage::app()->saveCache($authPass, 'solr_bridge_authentication_pass', array(), 60*60*24);
		}
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		if (isset($isAuthentication) && $isAuthentication > 0 ) {				
			curl_setopt($ch, CURLOPT_USERPWD, $authUser.':'.$authPass);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		}
		
		$returnData = curl_exec( $ch );
		
		$returnData = json_decode($returnData,true);
		return $returnData;
    }
    
    public function getStandardFilterQuery(){
    	$params = $this->getParams();
    	if (isset($params['fq']) && is_array($params['fq'])) {
    		$filterQuery = array();
    		foreach ($params['fq'] as $key=>$values) {
    			if (!empty($key) && !is_array($values) && !empty($values)) {
    				$filterQuery[$key.'_facet'] = array($values);
    			}else if(!empty($key) && is_array($values)){
    				$filterQuery[$key.'_facet'] = $values;
    			}
    		}
    		return $filterQuery;
    	}
		return array();
    }
}