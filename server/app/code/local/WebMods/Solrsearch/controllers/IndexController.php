<?php
class WebMods_Solrsearch_IndexController extends Mage_Core_Controller_Front_Action
{
    //public $mediaPath;
	public function indexAction() {
    	Mage::getSingleton('core/session')->setSolrFilterQuery(array());
		
		$layout = $this->getLayout();
    	$this->loadLayout();
    	//print_r($layout);
    	$resultBlock = $layout->getBlock('searchresult');
    	//$resultBlock->setData('mediaPath',Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA));
    	///$resultBlock->setData('jsPath',Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS));    
    	//$resultBlock->setData('skinPath',Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN));
    	//$resultBlock->setData('basePath',Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
    	
    	$facetsBlock = $layout->getBlock('searchfaces');
		
    	$solrModel = Mage::getModel('solrsearch/solr');
    	$store = Mage::app()->getStore();
    	$url = $solrModel->buildRequestUrl($store);
    	$solrData = $solrModel->doRequest($url, $store);
    	
    	$resultBlock->setData('solrdata', $solrData);
    	$facetsBlock->setData('solrdata', $solrData);
    	
    	$facetsBlock->setData('querytext', $solrModel->getParams('q'));
    	
    	$params = $this->getRequest()->getParams();
    	$filterQuery = (array)Mage::getSingleton('core/session')->getSolrFilterQuery();
    	if (isset($params['fq'])){
    		foreach ($params['fq'] as $key=>$value ) {
    			$filterQuery[$key.'_facet'] = array(0=>$value);
    		}
    	}
    	if (isset($params['clear']) && $params['clear'] == 'yes') $filterQuery = array();
    	Mage::getSingleton('core/session')->setSolrFilterQuery(array_unique($filterQuery));
    	$this->renderLayout();
    }
}