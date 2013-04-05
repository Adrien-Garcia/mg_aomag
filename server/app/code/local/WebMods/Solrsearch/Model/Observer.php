<?php
class WebMods_Solrsearch_Model_Observer {
	const FLAG_SHOW_CONFIG = 'showConfig';
	const FLAG_SHOW_CONFIG_FORMAT = 'showConfigFormat';
	const BATCH_DIRECTORY = '';

	private $request;

	public function checkForConfigRequest($observer) {
		$this->request = $observer->getEvent()->getData('front')->getRequest();
		if($this->request->{self::FLAG_SHOW_CONFIG} === 'true'){
			$this->setHeader();
			$this->outputConfig();
		}
	}

	public function addSearchWeightFieldToAttributeForm($observer){
		$weights = array(
			array(
		                'value' => "",
		                'label' => Mage::helper('catalog')->__('Default')
			),		
			array(
		                'value' => 200,
		                'label' => Mage::helper('catalog')->__('1')
			),
			array(
		                'value' => 190,
		                'label' => Mage::helper('catalog')->__('2')
			),
			array(
		                'value' => 180,
		                'label' => Mage::helper('catalog')->__('3')
			),
			array(
		                'value' => 170,
		                'label' => Mage::helper('catalog')->__('4')
			),
			array(
		                'value' => 160,
		                'label' => Mage::helper('catalog')->__('5')
			),
			array(
		                'value' => 150,
		                'label' => Mage::helper('catalog')->__('6')
			),
			array(
		                'value' => 140,
		                'label' => Mage::helper('catalog')->__('7')
			),
			array(
		                'value' => 130,
		                'label' => Mage::helper('catalog')->__('8')
			),
			array(
		                'value' => 120,
		                'label' => Mage::helper('catalog')->__('9')
			),
			array(
		                'value' => 110,
		                'label' => Mage::helper('catalog')->__('10')
			),
			array(
		                'value' => 100,
		                'label' => Mage::helper('catalog')->__('11')
			),
			array(
		                'value' => 90,
		                'label' => Mage::helper('catalog')->__('12')
			),
			array(
		                'value' => 80,
		                'label' => Mage::helper('catalog')->__('13')
			),
			array(
		                'value' => 70,
		                'label' => Mage::helper('catalog')->__('14')
			),
			array(
		                'value' => 60,
		                'label' => Mage::helper('catalog')->__('15')
			),
			array(
		                'value' => 50,
		                'label' => Mage::helper('catalog')->__('16')
			),
			array(
		                'value' => 40,
		                'label' => Mage::helper('catalog')->__('17')
			),
			array(
		                'value' => 30,
		                'label' => Mage::helper('catalog')->__('18')
			),
			array(
		                'value' => 20,
		                'label' => Mage::helper('catalog')->__('19')
			),
			array(
		                'value' => 10,
		                'label' => Mage::helper('catalog')->__('20')
			)
		);
		
		$fieldset = $observer->getForm()->getElement('front_fieldset');
		$attribute = $observer->getAttribute();
		$attributeCode = $attribute->getName();

		$fieldset->addField('solr_search_field_weight', 'select', array(
		        'name'      => 'solr_search_field_weight',
		        'label'     => Mage::helper('solrsearch')->__('Solr Search weight'),
		        'title'     => Mage::helper('solrsearch')->__('Solr Search weight'),
		        'values'    => $weights,
		));
		
		$fieldset->addField('solr_search_field_boost', 'textarea', array(
		        'name'      => 'solr_search_field_boost',
		        'label'     => Mage::helper('solrsearch')->__('Solr Search boost'),
		        'title'     => Mage::helper('solrsearch')->__('Solr Search booost'),
		        //'values'    => $weights,
		        'note'  => Mage::helper('solrsearch')->__('Example: Sony:1. Each pair of key:value separted by linebreak, value will be 1-20')
		));
		/*
		$facetOptions = array(
			array(
		                'value' => 0,
		                'label' => Mage::helper('catalog')->__('No')
			),
			array(
		                'value' => 1,
		                'label' => Mage::helper('catalog')->__('Yes')
			)
		);
		$fieldset->addField('solr_search_field_facet', 'select', array(
		        'name'      => 'solr_search_field_facet',
		        'label'     => Mage::helper('solrsearch')->__('Solr Search facet'),
		        'title'     => Mage::helper('solrsearch')->__('Solr Search facet'),
		        'values'    => $facetOptions,
		));
		*/
	}
	public function productDeleteBefore($observer){
		$product = $observer->getEvent()->getProduct();
			
	}
	public function productDeleteAfter($observer){
		$storeIds = array_keys(Mage::app()->getStores());
        foreach ($storeIds as $storeId) {
        	$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', $storeId);
			$solr_index = trim(Mage::getStoreConfig('webmods_solrsearch/settings/solr_index', $storeId));			
        	if(empty($solr_index)) {
        		continue;
        	}	
			$opts = array(
				  'http'=>array(
				    'method'=>"GET",
			)
			);
	
			$product = $observer->getEvent()->getProduct();
			// is cURL installed yet?
			if (!function_exists('curl_init')){
				Mage::getSingleton("core/session")->addError('CURL have not installed yet in this server, this caused the Solr index data out of date.');
			}else{
				// Now set some options (most are optional)
				$Url = trim($solr_server_url,'/').'/'.$solr_index.'/update?stream.body=<delete><query>products_id:'.$product->getId().'</query></delete>&commit=true';
				$ch = curl_init($Url);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$output = curl_exec($ch);
				curl_close($ch);
			}
        }
		
	}
	public function productAddUpdate($observer){
		
		$_product = $observer->getProduct();
		
		$getSolrIndexesConfigArray = $this->getSolrIndexesConfigArray();
		
		$params = array();
		
		$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', 0);
		
		//Loop thru solr cores
		foreach ($getSolrIndexesConfigArray as $core) {
			if( !empty($core['stores']) ) {
				$storeIdArray = explode(',', $core['stores']);
				
				foreach ($storeIdArray as $storeid) {
					$storeObject = Mage::getModel('core/store')->load($storeid);
					
					$collection = $this->loadProductCollectionByProductId($_product);
			
					$collection->addStoreFilter($storeObject);
					$collection->addWebsiteFilter($storeObject->getWebsiteId());
					
					$jsonData = $this->getJsonData($collection, $storeObject);
					
					$solrcore = $core['key'];
					
					$params['solr_update_url'] = trim($solr_server_url,'/').'/'.$solrcore.'/update/json?commit=true&wt=json';
					
					$params['solr_query_url'] = trim($solr_server_url,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id,store_id&start=0&rows=1&wt=json';
					
					$returnNoOfDocuments = $this->solr_index_commit_data($jsonData, $params['solr_update_url'], $params['solr_query_url']);
				}
				
			}
		}
		
		//die(print_r($getSolrIndexesConfigArray));
		/*
		$storeIds = array_keys(Mage::app()->getStores());
        foreach ($storeIds as $storeId) {
            $solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', $storeId);
			$solr_index = trim(Mage::getStoreConfig('webmods_solrsearch/settings/solr_index', $storeId));
			$auto_update_after_save = Mage::getStoreConfig('webmods_solrsearch/settings/solr_index_auto_when_product_save', $storeId);
						
	        if(intval($auto_update_after_save) > 0 && !empty($solr_index)){
				$opts = array(
					  'http'=>array(
					    'method'=>"GET",
				)
				);
				// is cURL installed yet?
				if (!function_exists('curl_init')){
					Mage::getSingleton("core/session")->addError('CURL have not installed yet in this server, this caused the Solr index data out of date.');
					
				}else{
					// Now set some options (most are optional)
					$Url = trim($solr_server_url,'/').'/'.$solr_index.'/dataimport?command=delta-import';					
					$ch = curl_init($Url);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$output = curl_exec($ch);
					curl_close($ch);
				}
			}
        }
        */
	}

	private function setHeader() {
		$format = isset($this->request->{self::FLAG_SHOW_CONFIG_FORMAT}) ?
		$this->request->{self::FLAG_SHOW_CONFIG_FORMAT} : 'xml';
		switch($format){
			case 'text':
				header("Content-Type: text/plain");
				break;
			default:
				header("Content-Type: text/xml");
		}
	}

	private function outputConfig() {
		//die(Mage::app()->getConfig()->getNode()->asXML());
	}
	
	public static function solrDataCommit(){
		/*
		$f = fopen('d:/cronlog.txt', 'w');
		fwrite($f, 'run...');
		fclose($f);
		*/
	}
	public function productAttributeSaveBefore($observer) {
		echo get_class($observer->getEvent());
		die();
	}
	
	public function getSolrIndexesConfigArray() {
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
		
		return $solrIndexesConfigArrayData;
	}
	
	public function loadProductCollectionByProductId($product){
		$collection = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('*')
			->addFieldToFilter(
                array(
                     array('attribute'=>'visibility','eq'=>Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH),
                     array('attribute'=>'visibility','eq'=>Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH)
                )
        	);
			
        	$collection->addAttributeToFilter( 'entity_id', array( 'in' => array( $product->getId() ) ) );
			
			$collection->getSelect()->joinLeft(
                  array('stock' => 'cataloginventory_stock_item'),
                  "e.entity_id = stock.product_id",
                  array('stock.is_in_stock')
          	)->where('stock.is_in_stock = 1');
          	
        return $collection;
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
		
		//loop products
		foreach ($collection as $product) {
			$textSearch = array();
			
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
				$catNames[] = $_cat->getName();
				$categoryPaths[] = $this->getCategoryPath($_cat);
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
			
			$docData['price_decimal'] = $_product->getFinalPrice();
			$docData['special_price_decimal'] = $_product->getSpecialPrice();
			
			$docData['url_path_varchar'] = $_product->getProductUrl();	
			
			$docData['name_boost'] = $_product->getName();
					
			$docData['products_id'] = $_product->getId();
			
			$docData['unique_id'] = $store->getId().'P'.$_product->getId();
			
			$docData['store_id'] = $store->getId();
			
			$docData['website_id'] = $store->getWebsiteId();
			
			$docData['product_status'] = $_product->getStatus();

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

		if (!function_exists('curl_init')){
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
						foreach ($returnData['response']['docs'] as $doc) {
							$this->logProductId($doc['products_id'], $doc['store_id']);
						}
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
		if (!file_exists($productImagePath)){						
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
	
	public function logProductId($id, $store_id){
		//Log index fields
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$logIndexedproductTable = $resource->getTableName('solrsearch/logs_indexedproduct');
		
		$connectionRead = $resource->getConnection('core_read');
		
		$results = $connectionRead->query("SELECT * FROM {$logIndexedproductTable} WHERE `store_id`=".$store_id." AND `value`=".$id.";");

		$row = $results->fetch();
		
		if (is_array($row) && $row['logs_id'] > 0) {
			return false;
		}
		
		$writeConnection->beginTransaction();
		//Log index fields
		$insertArray = array();
		$insertArray['logs_id'] = NULL;
		$insertArray['value'] = $id;
		$insertArray['store_id'] = $store_id;
		$writeConnection->insert($logIndexedproductTable, $insertArray);
		
		$writeConnection->commit();
	}
}