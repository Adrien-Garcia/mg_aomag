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
		        'note'  => Mage::helper('solrsearch')->__('Example: Sony:1. Each pair of key:value separted by linebreak, value will be 1-20')
		));
		
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
}