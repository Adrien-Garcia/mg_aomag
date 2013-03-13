<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Search Controller
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @module     Catalog
 */
class WebMods_Solrsearch_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function productInfoAction()
    {
        
    	Mage::helper('solrsearch/compare')->setCurrentUrl($this->getRequest()->getParam('currentUrl',false));
    	
    	$this->getResponse()->setHeader("Content-Type", "text/javascript", true);
    	$product_info = array();
    	if (!$this->getRequest()->getParam('q', false)) {
            $this->getResponse()->setBody(json_encode(array()));
            return;
        }
		
        $productIdArray = explode(',',$this->getRequest()->getParam('q'));
        $product = Mage::getModel('catalog/product');
        foreach($productIdArray as $pid){
        	if($product = $product->load($pid)){
        		$product_info[$pid]['product_url'] = $product->getProductUrl();
        		
        		$product_info[$pid]['old_price'] = $product->getPrice();
        		$product_info[$pid]['short_description'] = $product->getShortDescription();

        		$product_info[$pid]['final_price'] = $product->getFinalPrice();
        		$product_info[$pid]['final_price_formated'] = Mage::helper('core')->currency($product->getFinalPrice());
        		$product_info[$pid]['add_to_cart_url'] = Mage::helper('checkout/cart')->getAddUrl($product);
        		$product_info[$pid]['add_to_wishlist_url'] = Mage::helper('wishlist')->getAddUrl($product);
        		
        		$product_info[$pid]['add_to_compare_url'] = Mage::helper('solrsearch/compare')->getAddUrl($product);
        		
        		$product_info[$pid]['image_url'] = $product->getSmallImageUrl();
				$product_info[$pid]['image_url'] = (string)Mage::helper('catalog/image')->init($product, 'small_image')->resize(170,157);
        		$product_info[$pid]['full_image_url'] = $product->getImageUrl();
		
        	}
	$product->reset();
        }
        $json_result = Mage::helper('core')->jsonEncode($product_info);
        $this->getResponse()->setBody($this->getRequest()->getParam('callback', false).'('.$json_result.')');
        
    }
    
	public function getInfoBulk($docs)
    {
        
    	Mage::helper('solrsearch/compare')->setCurrentUrl($this->getRequest()->getParam('currentUrl',false));
    	
        $product_info = array();
        $product = Mage::getModel('catalog/product');
        foreach($docs as $doc){
        	if (isset($doc['products_id'])){
	        	if($product = $product->load($doc['products_id'])){
	        		$pid = $doc['products_id'];
	        		$product_info[$pid]['product_url'] = $product->getProductUrl();
	        		
	        		$product_info[$pid]['old_price'] = $product->getPrice();
	        		$product_info[$pid]['short_description'] = $product->getShortDescription();
	
	        		$product_info[$pid]['final_price'] = $product->getFinalPrice();
	        		$product_info[$pid]['final_price_formated'] = Mage::helper('core')->currency($product->getFinalPrice());
	        		$product_info[$pid]['add_to_cart_url'] = Mage::helper('checkout/cart')->getAddUrl($product);
	        		$product_info[$pid]['add_to_wishlist_url'] = Mage::helper('wishlist')->getAddUrl($product);
	        		//$product_info[$pid]['add_to_compare_url'] = Mage::getBlockSingleton('catalog/product_list')->getAddToCompareUrl($product);
	        		$product_info[$pid]['add_to_compare_url'] = Mage::helper('solrsearch/compare')->getAddUrl($product);
	        		
	        		$product_info[$pid]['image_url'] = $product->getSmallImageUrl();
					$product_info[$pid]['image_url'] = (string)Mage::helper('catalog/image')->init($product, 'small_image')->resize(170,157);
	        		$product_info[$pid]['full_image_url'] = $product->getImageUrl();
	        		
	        	}
        	}
			$product->reset();
        }
        return $product_info;
    }
    
    public function productUrlAction(){
    	$this->getResponse()->setHeader("Content-Type", "text/javascript", true);
    	$product_info = array();
    	if (!$this->getRequest()->getParam('q', false)) {
            $this->getResponse()->setBody(json_encode(array()));
            return;
        }
        $product = Mage::getModel('catalog/product');
        $pid = $this->getRequest()->getParam('q');
    	if($p = $product->load($pid)){
        	
    		$product_info['product_url'] = $p->getProductUrl();;
        }
        $json_result = Mage::helper('core')->jsonEncode($product_info);
        $this->getResponse()->setBody($this->getRequest()->getParam('callback', false).'('.$json_result.')');
    }
	public function queryAction(){
		$enable_jsonp = true;
		$this->getResponse()->setHeader("Content-Type", "text/javascript", true);
		
		$query = $this->getRequest()->getParam('q');
		
		$data = $this->doRequest($query);
		
		$returnData = json_decode($data[count($data)-1],true);
				
		if (!isset($returnData['response']['numFound']) || intval($returnData['response']['numFound']) < 1){
			//die(print_r($returnData));
			//echo $url = $this->getRequest()->getParam('r');
			//die($returnData['spellcheck']['suggestions']['collation']);
			$data = $this->doRequest($returnData['spellcheck']['suggestions']['collation']);
		}	
		$jsonp_callback = $enable_jsonp && isset($_GET['json_wrf']) ? $_GET['json_wrf'] : null;
		echo $jsonp_callback.'('.$data[count($data)-1].')';
		exit;
	}
	public function doRequest($keyword){
		$enable_jsonp    = true;
		$enable_native   = false;
		$valid_url_regex = '/.*/';
		
		$url = $this->getRequest()->getParam('r');
		$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', Mage::app()->getStore()->getStoreId());
		
		$solr_index = Mage::getStoreConfig('webmods_solrsearch/settings/solr_index', Mage::app()->getStore()->getStoreId());
		
		$url = trim($solr_server_url,'/').'/'.$solr_index.'/'.$url.'?q='.urlencode(strtolower(trim($keyword)));//.'?q='.$this->getRequest()->getParam('q');
		
		//unset($_GET['r']);
		unset($_GET['q']);
		
		$queryFields = "";
		$solrsearchconfig = Mage::getStoreConfig('webmods_solrsearch_fields/settings/enabled_fields', Mage::app()->getStore()->getStoreId());
		$fieldConfigArray = explode(",",$solrsearchconfig);
		
		$boostFields = Mage::getStoreConfig('webmods_solrsearch_boost_value/settings/enabled_fields', Mage::app()->getStore()->getStoreId());
    	$boostFieldsArray = explode(',',$boostFields);
    	
    	$boostQuery = '';
    	if (count($boostFieldsArray) > 0){
    		foreach ($boostFieldsArray as $boostitem){
    		$boostQuery .= $boostitem." ";
    		}
    	}
    	
    	if (!empty($boostQuery)){
    		$_GET['bq'] = $boostQuery;
    	}else {
    		
    	}
    	
    	//$_GET['bq'] = 'name_varchar_boost:"'.$keyword.'"^80 category_name_boost:"'.$keyword.'"^60 description_text_boost:"'.$keyword.'"^40';
    	//$_GET['mm'] = '0%';
		//$_GET['fq'] = 'category_text:"Badm�bel Set"';
		
		if(isset($_GET['facet_field'])){
			$facetFieldsArray = explode(',',$_GET['facet_field']);
			foreach ($facetFieldsArray as $facetField){
			$url .= '&facet.field='.$facetField;
			}
		}
		$url .= '&facet.limit=3';
		if (isset($_GET['json_nl'])){
		$_GET['json.nl'] = $_GET['json_nl'];
		}
		if (isset($_GET['spellcheck_collate'])){
		$_GET['spellcheck.collate'] = $_GET['spellcheck_collate'];
		}
		if(!empty($_GET['fq'])){
		$_GET['fq'] = utf8_decode($_GET['fq']);
		}
		
		//die($url);
		
		if ( !$url ) {
		  
		  // Passed url not specified.
		  $contents = 'ERROR: url not specified';
		  $status = array( 'http_code' => 'ERROR' );
		  
		} else if ( !preg_match( $valid_url_regex, $url ) ) {
		  
		  // Passed url doesn't match $valid_url_regex.
		  $contents = 'ERROR: invalid url';
		  $status = array( 'http_code' => 'ERROR' );
		  
		} else {
			$ch = curl_init( $url );
		 
		 curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $_GET );
		  
		  if ( isset($_GET['send_cookies']) && $_GET['send_cookies'] ) {
			$cookie = array();
			foreach ( $_COOKIE as $key => $value ) {
			  $cookie[] = $key . '=' . $value;
			}
			if ( $_GET['send_session'] ) {
			  $cookie[] = SID;
			}
			$cookie = implode( '; ', $cookie );
			
			curl_setopt( $ch, CURLOPT_COOKIE, $cookie);
		  }
		  
		  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		  curl_setopt( $ch, CURLOPT_HEADER, true );
		  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		  
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
		  
		  curl_setopt( $ch, CURLOPT_USERAGENT, isset($_GET['user_agent']) ? $_GET['user_agent'] : $_SERVER['HTTP_USER_AGENT'] );
		list( $header, $contents ) = @preg_split( '/([\r\n][\r\n])\\1/', curl_exec( $ch ), 2 ); 
		  $status = curl_getinfo( $ch );
		  
		  curl_close( $ch );
		}
		$data = preg_split( '/[\r\n]+/', $contents );
		return $data;
	}
	public function thumbAction(){
		$productId = $this->getRequest()->getParam('product_id');		
		$width = 32;
		$height = 32;
		$productModel = Mage::getModel('catalog/product');		
		if (isset($productId)){
			if($product = $productModel->load($productId)){
				$imageUrl = (string)Mage::helper('catalog/image')->init($product, 'small_image')->resize($width,$height);
				$start = strrpos($imageUrl,'.') + 1;
				$ext = substr($imageUrl,$start);
				$this->getResponse()->setHeader("Content-Type", "image/".$ext, true);
				$im = file_get_contents($imageUrl);
				$this->getResponse()->setBody($im);
			}
		}
	}
	
	public function categoryAction(){		
    	$catId = $this->getRequest()->getParam('cat_id');
		$url = Mage::getModel('catalog/category')->load($catId)->getUrl(array('_secure' => Mage::app()->getFrontController()->getRequest()->isSecure()));
		$this->_redirectUrl($url);  
		$this->setFlag('', self::FLAG_NO_DISPATCH, true);  
		return $this;	
	}
	
	public function ajaxresultAction() {
		$filterQuery = array();
		$existingFilterQuery = Mage::getSingleton('core/session')->getSolrFilterQuery();
		$filterQuery = array_merge($existingFilterQuery, $filterQuery);
		
		$params = $this->getRequest()->getParams();
		if(isset($params['filterQuery']) && !empty($params['filterQuery'])) {
			$queryFilterValueArr = explode('|',$params['filterQuery']);
			if(intval($queryFilterValueArr[2]) > 0){
				if($queryFilterValueArr[0] == 'price_decimal') {
					$filterQuery[$queryFilterValueArr[0]] = array($queryFilterValueArr[1]);
				}else{
					$filterQuery[$queryFilterValueArr[0]][] = $queryFilterValueArr[1];
				}
				
			}else{
				//echo $queryFilterValueArr[1];
				foreach($filterQuery[$queryFilterValueArr[0]] as $k=>$v){
					//echo $v;
					if($v == $queryFilterValueArr[1]) {
						unset($filterQuery[$queryFilterValueArr[0]][$k]);
					}
				}
			}
		}
		
		
		if(isset($params['refineSearch']) && $params['refineSearch'] == 'yes'){
			$filterQuery = array();
		}
		
		Mage::getSingleton('core/session')->setSolrFilterQuery($filterQuery);
						
		$resultBlock = Mage::app()->getLayout()->getBlockSingleton('solrsearch/result');
		
		$toolbarBlock = Mage::app()->getLayout()->getBlockSingleton('solrsearch/result_toolbar');
		
		$facetsBlock = Mage::app()->getLayout()->getBlockSingleton('solrsearch/faces');
		
		$solrModel = Mage::getModel('solrsearch/solr');
    	$store = Mage::app()->getStore();
    	$url = $solrModel->buildRequestUrl($store);
    	$solrData = $solrModel->doRequest($url, $store);
    	
    	$resultBlock->setData('solrdata', $solrData);
    	$facetsBlock->setData('solrdata', $solrData);
    	$facetsBlock->setData('querytext', $solrModel->getParam('q'));
    	
    	$toolbarBlock->setData('test', 'yessss');
		
		$html = $resultBlock->setTemplate("solrsearch/result-ajax.phtml")->toHtml();
		
		
		$faceshtml = $facetsBlock->setTemplate("solrsearch/searchfaces-ajax.phtml")->toHtml();
		echo $html.$faceshtml;
		exit();
	}

	public function fullqueryAction() {
		$solrModel = Mage::getModel('solrsearch/solr');
    	$store = Mage::app()->getStore();
    	$solrData = $solrModel->getFullQuery($store);
    	print_r($solrData);
    	exit;
	}
	
}