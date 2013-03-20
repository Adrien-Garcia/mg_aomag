<?php
class WebMods_Solrsearch_Block_Adminhtml_Solrsearch extends Mage_Core_Block_Template
{
  //protected $collection;
	public function __construct()
  {
    $this->setTemplate('solrsearch/solrsearch.phtml');
  }
  
public function getCollectionData($store) {
 	 $itemsPerCommit = 50;
		$itemsPerCommitConfig = Mage::getStoreConfig('webmods_solrsearch/settings/items_per_commit', 0);
		if(intval($itemsPerCommitConfig) > 0) $itemsPerCommit = $itemsPerCommitConfig;
		 $collection = Mage::getModel('catalog/product')->getCollection()
		->addFieldToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
		->addFieldToFilter(
                array(
                     array('attribute'=>'visibility','eq'=>Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH),
                     array('attribute'=>'visibility','eq'=>Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH)
                ))
		->addStoreFilter($store->getId())
		->addWebsiteFilter($store->getWebsiteId()) // set the offset (useful for pagination) 
		->addFinalPrice();
		 $collection->getSelect()->joinLeft(
                  array('stock' => 'cataloginventory_stock_item'),
                  "e.entity_id = stock.product_id",
                  array('stock.is_in_stock')
          )->where('stock.is_in_stock = 1');
		//->load();	
		$productCount = $collection->getSize();
		$totalPages = ceil($productCount/$itemsPerCommit);
		return array('productCount'=>$productCount, 'totalPages'=>$totalPages);
 }

 
public function getTotalPages($productCount) {
		$itemsPerCommit = 50;
		$itemsPerCommitConfig = Mage::getStoreConfig('webmods_solrsearch/settings/items_per_commit', 0);
		if(intval($itemsPerCommitConfig) > 0) $itemsPerCommit = $itemsPerCommitConfig;

		$totalPages = ceil($productCount/$itemsPerCommit);
	
	return $totalPages;
 }
 
 public function getSolrLuke($solr_index) {
 	$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', 0);
 	
 	$Url = trim($solr_server_url,'/').'/'.$solr_index.'/admin/luke/?wt=json';
 	
 	$ch = curl_init($Url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
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
	
 	if (isset($isAuthentication) && $isAuthentication > 0 ) {			
		curl_setopt($ch, CURLOPT_USERPWD, $authUser.':'.$authPass);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	}
	
	$returnData = curl_exec( $ch );
	$returnData = json_decode($returnData,true);
	return $returnData;
 }
 
 public function checkValuesChanges($fields, $storeid=0){
 	
 	$resource = Mage::getSingleton('core/resource');
    $connection = $resource->getConnection('core_read');
    $attrtable = $resource->getTableName('eav/attribute');
 	
    $changedFields = array();
    
 	foreach ($fields as $key) {
 		$startPoint = strrpos($key, '_')+1;
    	$attributeCode = substr($key, 0, ($startPoint-1));
    	
    	//Query building
		$query = 'SELECT * FROM ' . $resource->getTableName('catalog_eav_attribute').' a 
		INNER JOIN '.$resource->getTableName('eav_attribute'). ' b ON a.attribute_id = b.attribute_id';
		$query .= ' WHERE b.attribute_code = \''.$attributeCode.'\' LIMIT 1';
		
		$result = $connection->query($query);
		
		$result = $result->fetch();
		
		if (!is_array($result) || $result['backend_type'] == 'static') {
			continue;
		}
		
		$query = 'SELECT * FROM ' . $resource->getTableName('catalog_product_entity_'.$result['backend_type']).' WHERE `attribute_id` = '.$result['attribute_id'].' LIMIT 1';

		$result = $connection->query($query);
		
		$result = $result->fetch();
		
		if (is_array($result) && count($result) > 0) {
			$changedFields[] = $attributeCode;
		}    	
 	}
 	return array_unique($changedFields);
 }
}