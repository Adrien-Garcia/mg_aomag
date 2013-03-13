<?php
class WebMods_Solrsearch_Model_Indexer_Solr extends Mage_Index_Model_Indexer_Abstract{
	protected function __construct(){
		$this->_init('solrsearch/indexer_solr');
	}
	
	public function getName(){
		Mage::helper('WebMods_Solrsearch')->__('Solr Index');
	}
	
	public function getDescription(){
		Mage::helper('WebMods_Solrsearch')->__('Solr Bridge Index');
	}
	
	protected function _registerEvent(Mage_Index_Model_Event $event){
		
	}
	
	protected function _processEvent(Mage_Index_Model_Event $event){
		
	}
}