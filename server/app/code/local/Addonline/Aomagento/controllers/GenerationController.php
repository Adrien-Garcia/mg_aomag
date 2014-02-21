<?php

class Addonline_Aomagento_GenerationController extends Mage_Core_Controller_Front_Action {
	
	public function getlicenceAction() {
		$key = 'e983cfc54f88c7114e99da95f5757df6';
		$hostname = isset($_GET['hostname']) ? $_GET['hostname'] : "";
		$module = isset($_GET['module']) ? $_GET['module'] : "";
		
		if($hostname != "") {
			if(!preg_match("/^http:\/\/|^https:\/\//", $hostname)) {
				$hostname = "http://".$hostname;
			}
			if(!preg_match("/\/$/", $hostname)) {
				$hostname = $hostname."/";
			}
			echo $hostname."::";
			$licence = md5($hostname.$key.$module);
			echo $licence;
		} else {
			echo "Vous n'avez pas renseigné le nom de domaine.";
		}
		
	}
}