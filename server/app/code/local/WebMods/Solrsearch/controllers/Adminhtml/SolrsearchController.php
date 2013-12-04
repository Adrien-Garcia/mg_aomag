<?php
class WebMods_Solrsearch_Adminhtml_SolrsearchController extends Mage_Adminhtml_Controller_Action
{
	public $logFields = array();

	 protected function _initAction() {
	  	$this->loadLayout()
	   		->_setActiveMenu('solrsearch/indexes')
	   		->_addBreadcrumb(Mage::helper('adminhtml')->__('Solr Bridge Indexes'), Mage::helper('adminhtml')->__('Solr Bridge Indexes'));
	  
	  	return $this; 
	 }   
 
	 public function indexAction() {
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
		
		 $this->_title($this->__('Solr Bridge Indexes'))
				 ->_title($this->__('Solr Bridge Indexes'))
				// Highlight the current menu
				 ->_setActiveMenu('solrsearch/indexes');
		
		//Get dashboard block object
		$dashboard = $this->getLayout()->getBlock('adminhtml_solrsearch');
		
		$solrIndexesConfigArray = Mage::getStoreConfig('webmods_solrsearch_indexes');
		
		$solrIndexesConfigArrayData = array();
		
		foreach ($solrIndexesConfigArray as $key=>$values) {
			$coreData = array();
			$coreData['key'] = $key;
			$coreData['stores'] = trim($values['stores'], ',');
			$coreData['label'] = $values['label'];
			if (trim($coreData['stores']) == '' || empty($coreData['label'])) {
				continue;
			}
			
			$solrIndexesConfigArrayData[] = $coreData;
		}
		
		$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', 0);
		if (empty($solr_server_url)) {	
			Mage::getSingleton("core/session")->addWarning('Solr Server Url is empty or Magento store and Solr index not yet mapped. Please go to System > Configuration > Solr Bridge > Basic Settings');
		}	
		$dashboard->setData('indexes', $solrIndexesConfigArrayData);
		
		$resource = Mage::getSingleton('core/resource');
		$connection = $resource->getConnection('core_read');
		$logtable = $resource->getTableName('solrsearch/logs');
		
		$results = $connection->query("SELECT * FROM {$logtable} WHERE `logs_type` = 'INDEXEDFIELDS' ORDER BY update_at DESC LIMIT 1;");
			
		$row = $results->fetch();
		
		$indexedFields = explode(',', trim($row['value'], ','));
		
		
		$solr_include_category_in_search = Mage::getStoreConfig('webmods_solrsearch/settings/solr_search_in_category', 0);
		$use_category_as_facet = Mage::getStoreConfig('webmods_solrsearch/settings/use_category_as_facet', 0);
		
		$entityType = Mage::getModel('eav/config')->getEntityType('catalog_product');
		$catalogProductEntityTypeId = $entityType->getEntityTypeId();
		
		$facetFieldsInfo = Mage::getResourceModel('eav/entity_attribute_collection')
		->setEntityTypeFilter($catalogProductEntityTypeId)
		->addSetInfo()
		->getData();
		
		$magentoFields = array();
		foreach($facetFieldsInfo as $att){
			$backendType = $att['backend_type'];
			if ($backendType == 'int') {
				$backendType = 'varchar';
			}
			
			if ($att['is_searchable'] < 1) continue;
			
			$attributeKey = $att['attribute_code'].'_'.$backendType;
			
			$magentoFields[] = $attributeKey;
			
			$attributeKeyFacets = $att['attribute_code'].'_facet';
			
			$attributeKeyBoost = $att['attribute_code'].'_boost';
			
			
			
			if ( 
				(isset($att['solr_search_field_weight']) && !empty($att['solr_search_field_weight']))
				 ||
				(isset($att['solr_search_field_boost']) && !empty($att['solr_search_field_boost']))	
			) {
				$magentoFields[] = $attributeKeyBoost;
			}	
			
			if (
				(isset($att['solr_search_field_facet']) && !empty($att['solr_search_field_facet']))
			) {
				$magentoFields[] = $attributeKeyFacets;
				$magentoFields[] = $att['attribute_code'].'_text';
			}
			
		}
		
		if ($use_category_as_facet) {
			$magentoFields[] = 'category_facet';
			$magentoFields[] = 'category_text';
			$magentoFields[] = 'category_boost';
		}		
		
		$magentoFields[] = 'category_path';
		$magentoFields[] = 'textSearch';
		$magentoFields[] = 'special_price_decimal';
		$magentoFields[] = 'url_path_varchar';
				
		$magentoFields[] = 'products_id';
		
		$changedFields = array();
		if (count($indexedFields) > 0 && isset($indexedFields[0]) && !empty($indexedFields[0])) {
			$changedFields = array_diff($magentoFields, $indexedFields);
		}
		$dashboard->setData('changedFields',$changedFields);
		
		$this->renderLayout();
	 }
	 
	 public function processAction() {
		$startTime = time();
		
		$errors = array();
		
		//get current page
		$page = 1;
		if( isset($_POST['page']) && is_numeric($_POST['page'])) { $page = $_POST['page']; }
		
		//get solr core
		$solrcore = 'english';
		if ( isset($_POST['core']) && !empty($_POST['core'])) { $solrcore = $_POST['core']; }
		
		//get current website id
		$websiteid = array();
		if ( isset($_POST['website']) && !empty($_POST['website'])) { $websiteid = explode(',', $_POST['website']); }
		
		//get total pages
		$totalPages = 1;
		if ( isset($_POST['totalpage']) && is_numeric($_POST['totalpage'])) { $totalPages = $_POST['totalpage']; }
		
		//get stores ids
		$stores = '';
		$storesArr = array();
		if ( isset($_POST['stores']) && !empty($_POST['stores'])) { 
			$stores = $_POST['stores']; 
			$storesArr = explode(',', $stores);
		}
		
		//get total of products
		$productCount = 1;
		if ( isset($_POST['productcount']) && is_numeric($_POST['productcount'])) { $productCount = $_POST['productcount']; }
		
		//get total number of solr documents
		$numDocs = 0;
		if ( isset($_POST['numDocs']) && is_numeric($_POST['numDocs']) ) { $numDocs = $_POST['numDocs']; }
		//get action
		$action = 'NEW';
		if (isset($_POST['action']) && !empty($_POST['action'])) { $action = $_POST['action']; }
		
		$itemsPerCommit = 50;
		$itemsPerCommitConfig = Mage::getStoreConfig('webmods_solrsearch/settings/items_per_commit', 0);
		if(intval($itemsPerCommitConfig) > 0) $itemsPerCommit = $itemsPerCommitConfig;
		
		$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', 0);
		
		//is category name searchable
		$solr_include_category_in_search = Mage::getStoreConfig('webmods_solrsearch/settings/solr_search_in_category', 0);
		//use category for facets
		$use_category_as_facet = Mage::getStoreConfig('webmods_solrsearch/settings/use_category_as_facet', 0);
		
		if (empty($solrcore) || empty($solr_server_url)){       	
			$errors[] = Mage::helper('solrsearch')->__('Solr Server Url is empty or Magento store and Solr index not yet mapped.');
		}
		//Solr data update url
		$Url = trim($solr_server_url,'/').'/'.$solrcore.'/update/json?commit=true&wt=json';
		//Solr get one doc url
		$start = intval($page) - 1;
		//print_r($_POST);
		
		$SolrQueryUrl = trim($solr_server_url,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id,store_id&start=0&rows='.$_POST['productcount'].'&wt=json';

		//Solr get all docs url
		$getExistingSolrDocsQueryUrl = trim($solr_server_url,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id,store_id&start=0&rows='.$_POST['productcount'].'&wt=json';
		//Solr delete all docs from index
		$clearnSolrIndexUrl = trim($solr_server_url,'/').'/'.$solrcore.'/update?stream.body=<delete><query>*:*</query></delete>&commit=true';
		
		//$logFields = array(); //log which fields send for indexing in Solr
		
		$resource = Mage::getSingleton('core/resource');
		$connection = $resource->getConnection('core_read');
		$logtable = $resource->getTableName('solrsearch/logs');
		$logIndexedproductTable = $resource->getTableName('solrsearch/logs_indexedproduct');
		
		$results = $connection->query("SELECT * FROM {$logtable} WHERE `logs_type` = 'INDEXEDFIELDS' ORDER BY update_at DESC LIMIT 1;");
			
		$row = $results->fetch();
		
		$this->logFields = explode(',', trim($row['value'], ','));
		
		$oldLogFieldId = $row['logs_id'];
		

		if($action == 'UPDATE') {
			if ($productCount == $numDocs && $page == 1) {
				//empty solr index
				
				$storeMappingString = Mage::getStoreConfig('webmods_solrsearch_indexes/'.$solrcore.'/stores', 0);
				
				$storeMappingString = trim($storeMappingString, ',');
				if (!empty($storeMappingString)) {
					$connection = $resource->getConnection('core_write');
					$results = $connection->query("DELETE FROM {$logIndexedproductTable} WHERE store_id IN({$storeMappingString});");
				}
				
				$this->doRequest($clearnSolrIndexUrl);
				$returnData = array();
				$returnData['page'] = $page;
				$returnData['documents'] = 0;
				$returnData['continueprocess'] = 'yes';
				$returnData['nextpage'] = 1;
				$returnData['percent'] = 0;
				$this->getResponse()->setHeader("Content-Type", "application/json", true);
							
				$returnData['estimatedtime'] = 0;
				$returnData['remainedtime'] = 0;
				$returnData['numdocs'] = 0;
				$returnData['action'] = 'NEW';
				echo json_encode($returnData);
				exit;
			}
			$updateParams = array(
				'existing_solr_docs_query_url' => $getExistingSolrDocsQueryUrl.'&rows='.$_POST['numDocs'],
				'stores' => explode(',', $stores),
				'solr_update_url' => $Url,
				'solr_query_url' => $SolrQueryUrl,
				'page' => $page
			);
			$numberOfDocuments = $this->processUpdateSolrIndex( $updateParams);
		}else {
			$newParams = array(
				'existing_solr_docs_query_url' => $getExistingSolrDocsQueryUrl.'&rows='.$_POST['numDocs'],
				'stores' => explode(',', $stores),
				'solr_update_url' => $Url,
				'solr_query_url' => $SolrQueryUrl,
				'page' => $page
			);
			//Mage::log($newParams);
			$numberOfDocuments = $this->processNewSolrIndex($newParams);
		}
		
		//Log index fields
		$writeConnection = $resource->getConnection('core_write');
		$writeConnection->beginTransaction();
		
		//Delete old log record
		$condition = array($writeConnection->quoteInto('logs_id=?', $oldLogFieldId));
		$writeConnection->delete($logtable, $condition);
		
		//Log index fields
		$insertArray = array();
		$insertArray['logs_id'] = NULL;
		$insertArray['store_id'] = 0;
		$insertArray['logs_type'] = 'INDEXEDFIELDS';
		$insertArray['value'] = @implode(',', $this->logFields);
		$writeConnection->insert($logtable, $insertArray);
		
		
		$writeConnection->commit();
		
		$returnData = array();
		$returnData['page'] = $page;
		$returnData['documents'] = $numberOfDocuments;
		$returnData['continueprocess'] = (is_numeric($numberOfDocuments) && $numberOfDocuments < $productCount)?'yes':'no';
		$returnData['nextpage'] = $page + 1;
		$returnData['action'] = (is_numeric($numberOfDocuments) && $numberOfDocuments > 0)?'UPDATE':'NEW';
		$returnData['percent'] = round(($numberOfDocuments*100)/$productCount);
		$returnData['numdocs'] = $numberOfDocuments;
		$this->getResponse()->setHeader("Content-Type", "application/json", true);
		
		$endTime = time();
		if (!isset($_POST['estimatedtime']) || $_POST['estimatedtime'] < 1) {
			$seconds = $endTime - $startTime;
		}else{
			$seconds = $_POST['estimatedtime'];
		}
		$returnData['estimatedtime'] = $seconds;
		
		if ($seconds*($totalPages - $page) > 60 && ($seconds/60)*($totalPages - $page) < 60){
			$returnData['remainedtime'] = ($seconds/60)*($totalPages - $page).' minute(s)';
		}else if (($seconds/60)*($totalPages - $page) > 60){
			$returnData['remainedtime'] = ((($seconds/60)*($totalPages - $page))/60).' hour(s)';
		} else {
			$returnData['remainedtime'] = $seconds*($totalPages - $page).' second(s)';
		}
		
		echo json_encode($returnData);
		exit;
	 }
	 
	public function processUpdateSolrIndex($params = array()) {
		
		$numberOfIndexedDocuments = 0;
		
		foreach ($params['stores'] as $storeid) {
			$storeObject = Mage::getModel('core/store')->load($storeid);
			
			$collection = $this->loadUpdateProductCollection($storeObject->getWebsiteId(), $storeid, $params['page']);
			
			$jsonData = $this->getJsonData($collection, $storeObject);
			$returnNoOfDocuments = $this->solr_index_commit_data($jsonData, $params['solr_update_url'], $params['solr_query_url']);
			$numberOfIndexedDocuments = $returnNoOfDocuments;
		}
		return $numberOfIndexedDocuments;
	}

	public function processNewSolrIndex($params = array()) {
		$numberOfIndexedDocuments = 0;
		
		foreach ($params['stores'] as $storeid) {
			$storeObject = Mage::getModel('core/store')->load($storeid);
			
			$collection = $this->loadProductCollection($storeObject->getWebsiteId(), $storeid, $params['page']);
			$jsonData = $this->getJsonData($collection, $storeObject);
			$returnNoOfDocuments = $this->solr_index_commit_data($jsonData, $params['solr_update_url'], $params['solr_query_url']);
			$numberOfIndexedDocuments = $returnNoOfDocuments;
		}
		return $numberOfIndexedDocuments;
	}

	public function getJsonData($collection, $store) {
		//is category name searchable
		$solr_include_category_in_search = Mage::getStoreConfig('webmods_solrsearch/settings/solr_search_in_category', 0);
		//use category for facets
		$use_category_as_facet = Mage::getStoreConfig('webmods_solrsearch/settings/use_category_as_facet', 0);
		
		$startPoint = 0;
		$index = 1;
		$textSearch = array();
		
		$documents = "{";
		//Mage::log($collection->getSelect()->__toString());
		//loop products
		foreach ($collection as $product) {
			$textSearch = array();
			$docData = array(); //ajout ADDONLINE pour réinitialiser 
			
			$_product = Mage::getModel('catalog/product')->setStoreId($store->getId())->load($product->getId());
			$atributes = $_product->getAttributes();

			foreach ($atributes as $key=>$atributeObj) {
				$backendType = $atributeObj->getBackendType();
				$frontEndInput = $atributeObj->getFrontendInput();
				$attributeCode = $atributeObj->getAttributeCode();
				$attributeData = $atributeObj->getData();
				
				if (!$atributeObj->getIsSearchable()) continue; // ignore fields which are not searchable
				
				if ($backendType == 'int') {
					$backendType = 'varchar';
				}
				
				$attributeKey = $key.'_'.$backendType;
				
				$attributeKeyFacets = $key.'_facet';
				
				if (!is_array($atributeObj->getFrontEnd()->getValue($_product))){
					$attributeVal = strip_tags($atributeObj->getFrontEnd()->getValue($_product));
				}else {
					$attributeVal = $atributeObj->getFrontEnd()->getValue($_product);
					$attributeVal = implode(' ', $attributeVal);
				}
				
				//Start collect values
				$this->logFields[] = $attributeKey;
							
				if (!empty($attributeVal)) {
					if($frontEndInput == 'multiselect') {
						$attributeValFacets = @explode(',', $attributeVal);
					}else {
						$attributeValFacets = $attributeVal;
					}
					
					if ($backendType == 'datetime') {
						$attributeVal = date("Y-m-d\TG:i:s\Z", $attributeVal);
					}
					
					if (!in_array($attributeVal, $textSearch) && $attributeVal != 'None' && $attributeCode != 'status' && $attributeCode != 'sku'){
						$textSearch[] = $attributeVal;
					}
					
					$docData[$attributeKey] = $attributeVal;
					
					$docData[$key.'_boost'] = $attributeVal;
					
					if ( 
						(isset($attributeData['solr_search_field_weight']) && !empty($attributeData['solr_search_field_weight']))
						 ||
						(isset($attributeData['solr_search_field_boost']) && !empty($attributeData['solr_search_field_boost']))	
					) {
						$docData[$key.'_boost'] = $attributeVal;
					}	
					
					
					
					if (
						(isset($attributeData['is_filterable_in_search']) && !empty($attributeData['is_filterable_in_search']) && $attributeValFacets != 'No' && $attributeKey != 'price_decimal' && $attributeKey != 'special_price_decimal')
					) {
						$docData[$attributeKeyFacets] = $attributeValFacets;
						//$docData[$key.'_text'] = $attributeValFacets;
					}											
				}
				
			}
			
			$cats = $_product->getCategoryIds();
			$catNames = array();
			$categoryPaths = array();
			foreach ($cats as $category_id) {
				$_cat = Mage::getModel('catalog/category')->load($category_id) ;
				if ($_cat->getIsActive()) {
					$catNames[] = $_cat->getName();
					$categoryPaths[] = $this->getCategoryPath($_cat);
				}
			} 
			
			if ($solr_include_category_in_search > 0) {
				$textSearch = array_merge($textSearch, $catNames);
			}
			$sku = $_product->getSku();
			$textSearch[] = $sku;
			$textSearch[] = str_replace(array('-', '_'), '', $sku);
			if ($use_category_as_facet) {
				$docData['category_facet'] = $catNames;
				$docData['category_text'] = $catNames;
				$docData['category_boost'] = $catNames;
			}		
			
			$docData['category_path'] = $categoryPaths;
			$docData['textSearch'] = $textSearch;
			//ADDONLINE : utiliser $product (chargé par la collection) pour avoir les prix avec les promotions catalogue
			if ($product->getFinalPrice()) {
				$docData['price_decimal'] = $product->getFinalPrice();
			} else {
				$docData['price_decimal'] = 0.0;
			}
			if ($product->getSpecialPrice()) {
				$docData['special_price_decimal'] = $product->getSpecialPrice();
			}
			//FIN ADDONLINE
			$docData['url_path_varchar'] = $_product->getProductUrl();	
			
			$docData['name_boost'] = $_product->getName();
					
			$docData['products_id'] = $_product->getId();
			
			$docData['unique_id'] = $store->getId().'P'.$_product->getId();
			
			$docData['store_id'] = $store->getId();
			
			$docData['website_id'] = $store->getWebsiteId();
			
			$docData['product_status'] = $_product->getStatus();
			
			$this->logFields = array_unique(array_merge($this->logFields, array_keys($docData)));
			$this->generateThumb($_product);
			$documents .= '"add": '.json_encode(array('doc'=>$docData)).",";
			
			$index++;			
		}
		
		$jsonData = trim($documents,",").'}';

		return $jsonData;
	}
	 
	public function getCategoryPath($category){
		$currentCategory = $category;
		$categoryPath = str_replace('/', '_._._',$category->getName());
		while ($category->getParentId() > 0){
			
			$category = $category->getParentCategory();
			if ($category->getParentId() > 0){
				$categoryPath = str_replace('/', '_._._',$category->getName()).'/'.$categoryPath;
			}
		}
		return $categoryPath.'/'.$currentCategory->getId();
	}

	public function solr_index_commit_data($jsonData, $updateurl, $queryurl){
		//echo 'yes function called';
		// is cURL installed yet?
		if (!function_exists('curl_init')){
			//Mage::getSingleton("core/session")->addError('CURL have not installed yet in this server, this caused the Solr index data out of date.');
			echo 'CURL have not installed yet in this server, this caused the Solr index data out of date.';
			exit;
		}else{
			if(!isset($jsonData) && empty($jsonData)) {
				return 0;
			}
			
			$postFields = array('stream.body'=>$jsonData);
			
			$output = $this->doRequest($updateurl, $postFields);
			
			if (isset($output['responseHeader']['QTime']) && intval($output['responseHeader']['QTime']) > 0){
				$returnData = $this->doRequest($queryurl);
				
				if (isset($returnData['response']['numFound']) && intval($returnData['response']['numFound']) > 0){
					if (is_array($returnData['response']['docs'])) {
						$indexedProducts = array();
						foreach ($returnData['response']['docs'] as $doc) {
//							$this->logProductId($doc['products_id'], $doc['store_id']);
							$indexedProducts[$doc['store_id']][$doc['products_id']]=$doc['products_id'];
						}
						$this->logProductIds($indexedProducts);
						
					}
					return $returnData['response']['numFound'];
				}
			}else {
				return 0;
			}
		}
	}

	public function doRequest($url, $postFields = NULL){
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
			$isAuthentication = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url_auth', 0);
			$authUser = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url_auth_username', 0);
			$authPass = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url_auth_password', 0);
		
			Mage::app()->saveCache($isAuthentication, 'solr_bridge_is_authentication', array(), 60*60*24);
			Mage::app()->saveCache($authUser, 'solr_bridge_authentication_user', array(), 60*60*24);
			Mage::app()->saveCache($authPass, 'solr_bridge_authentication_pass', array(), 60*60*24);
		}			
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		if(is_array($postFields)) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if (isset($isAuthentication) && $isAuthentication > 0 ) {			
			curl_setopt($ch, CURLOPT_USERPWD, $authUser.':'.$authPass);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		}
		$output = curl_exec($ch);
		$output = json_decode($output,true);
		curl_close($ch);
		return $output;
	}

	public function generateThumb($product){	
		$productId = $product->getId();
		$image = $product->getImage();
		$productImagePath = Mage::getBaseDir("media").DS.'catalog'.DS.'product'.$image;
		if (!file_exists($productImagePath) || empty($image)){
			$productImagePath = Mage::getBaseDir("skin").DS.'frontend'.DS.'base'.DS.'default'.DS.'images'.DS.'catalog'.DS.'product'.DS.'placeholder'.DS.'image.jpg';
		}
		$info = getimagesize($productImagePath);
		$image_mime = $info['mime'];
		if (!file_exists($productImagePath) || $image_mime != "image/jpeg"){						
			return false;
		}
					 

		$productImageThumbPath = Mage::getBaseDir('media').DS."catalog".DS."product".DS."sb_thumb".DS.$productId.'.jpg';
		if (file_exists($productImageThumbPath)) {
			unlink($productImageThumbPath);
		}
		$imageResizedUrl = Mage::getBaseUrl("media").DS."catalog".DS."product".DS."sb_thumb".DS.$productId.'.jpg';
		
		$imageObj = new Varien_Image($productImagePath);
		$imageObj->constrainOnly(FALSE);
		$imageObj->keepAspectRatio(TRUE);
		$imageObj->keepFrame(FALSE);
		$imageObj->backgroundColor(array(255,255,255));
		$imageObj->keepTransparency(TRUE);
		$imageObj->resize(32, 32);    
		$imageObj->save($productImageThumbPath);
		if (file_exists($productImageThumbPath)) {
			return true;
		}
		
		return false;
	}
	
	public function emptyindexAction() {
		$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', 0);

		//get solr core
		$solrcore = 'english';
		if ( isset($_POST['core']) && !empty($_POST['core'])) { $solrcore = $_POST['core']; }
		
		$storeMappingString = Mage::getStoreConfig('webmods_solrsearch_indexes/'.$solrcore.'/stores', 0);
		
		//Solr delete all docs from index
		$clearnSolrIndexUrl = trim($solr_server_url,'/').'/'.$solrcore.'/update?stream.body=<delete><query>*:*</query></delete>&commit=true';
		
		$output = $this->doRequest($clearnSolrIndexUrl);
		
		$this->getResponse()->setHeader("Content-Type", "application/json", true);
		
		while(true) {
			$SolrQueryUrl = trim($solr_server_url,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id&rows=1&wt=json';
			$queryOutput = $this->doRequest($SolrQueryUrl);
			if(is_array($queryOutput) && isset($queryOutput['response']) && isset($queryOutput['response']['numFound']) && intval($queryOutput['response']['numFound']) < 1) {
				$storeMappingString = trim($storeMappingString, ',');
				if (!empty($storeMappingString)) {
					$resource = Mage::getSingleton('core/resource');
					$connection = $resource->getConnection('core_write');
					$logIndexedproductTable = $resource->getTableName('solrsearch/logs_indexedproduct');
					
					$results = $connection->query("DELETE FROM {$logIndexedproductTable} WHERE store_id IN({$storeMappingString});");
				}
				break;
			}
		}
		
		echo 'true';
		exit;
	}

	public function logProductIds($indexedProducts){
		//Log index fields
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$logIndexedproductTable = $resource->getTableName('solrsearch/logs_indexedproduct');
	
		$connectionRead = $resource->getConnection('core_read');
	
		$results = $connectionRead->query("SELECT `store_id`, `value` FROM {$logIndexedproductTable} ORDER BY `store_id`, `value`;");

		$indexedDBProducts = array();
		foreach ($results->fetchAll() as $row) {
			$indexedDBProducts[$row['store_id']][$row['value']]=$row['value'];				
		}	
	
		foreach ($indexedProducts as $store => $products) {
			foreach ($products as $product) {
				if (!isset($indexedDBProducts[$store][$product])) {					
					$writeConnection->beginTransaction();
					//Log index fields
					$insertArray = array();
					$insertArray['logs_id'] = NULL;
					$insertArray['value'] = $product;
					$insertArray['store_id'] = $store;
					$writeConnection->insert($logIndexedproductTable, $insertArray);
				
					$writeConnection->commit();
				}
			}
		}
	}
	
	public function loadProductCollection($websiteId, $storeId, $page = 1){
		$itemsPerCommit = 50;
		$itemsPerCommitConfig = Mage::getStoreConfig('webmods_solrsearch/settings/items_per_commit', 0);
		if(intval($itemsPerCommitConfig) > 0) $itemsPerCommit = $itemsPerCommitConfig;
		
		$collection = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('*')
			->addStoreFilter($storeId)
			->addWebsiteFilter($websiteId)
			->addFieldToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
			->addFieldToFilter(
                array(
                     array('attribute'=>'visibility','eq'=>Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH),
                     array('attribute'=>'visibility','eq'=>Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH)
                )
        	)
        	->addFinalPrice()
			->setPage($page, $itemsPerCommit);
			
		if (!Mage::getStoreConfig('cataloginventory/options/show_out_of_stock', $storeId)) {
			$collection->getSelect()->joinLeft(
                  array('stock' => 'cataloginventory_stock_item'),
                  "e.entity_id = stock.product_id",
                  array('stock.is_in_stock')
          	)->where('stock.is_in_stock = 1');
		}
          	
        return $collection;
	}
	
	public function loadUpdateProductCollection($websiteId, $storeId, $page = 1) {
		$collection = $this->loadProductCollection($websiteId, $storeId, $page);
		
		$timezone = Mage::getStoreConfig('general/locale/timezone', 0);
		$datetime = new DateTime($_POST['lastindextime']);
		$la_time = new DateTimeZone($timezone);
		$datetime->setTimezone($la_time);
		$lastIndexTime = $datetime->format('Y-m-d H:i:s');
				
		$resource = Mage::getSingleton('core/resource');
		$logIndexedproductTable = $resource->getTableName('solrsearch/logs_indexedproduct');
		
		//On Fait une Jointure LEFT avec une condition Where = NULL pour sélectionner les enregsitrement qui ne sont PAS dans la table des logs
		// plus une condition OR sur la date de modification dans le WHERE pour sélectionner les produits qui ont été modifiés depuis la dernière indexation 
		$collection->getSelect()->joinLeft(
				array('log' => $logIndexedproductTable),
				"e.entity_id = log.value AND log.store_id=$storeId ",
				array()
		)->where('(log.logs_id IS NULL OR e.updated_at > \''.$lastIndexTime.'\')');
		
		return $collection;
	}

}
?>