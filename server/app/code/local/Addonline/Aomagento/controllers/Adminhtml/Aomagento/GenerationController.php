<?php

class Addonline_Aomagento_Adminhtml_Aomagento_GenerationController extends Mage_Adminhtml_Controller_Action {
	
	public function getlicenceAction() {
		$this->loadLayout();
		$this->renderLayout();
		$key = 'e983cfc54f88c7114e99da95f5757df6';
		$hostname = isset($_GET['hostname']) ? $_GET['hostname'] : "";
		$module = isset($_GET['module']) ? $_GET['module'] : "";
				
		$url = strtolower($hostname);
		$domainname = preg_replace("/^[\w\:\/]*\/\/?([\w\d\.\-]+).*\/*$/", "$1", $url);
		$domainname = preg_replace("/^([\w\d\.\-]+).*\/*$/", "$1", $domainname);				 	

		echo $domainname."::";
		$licence = md5($domainname.$key.$module);
		echo $licence."::";
		return;
		
	}
	
	public function indexAction() {
		Mage::log("generation");
	}
}