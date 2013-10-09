<?php

class Addonline_Aomagento_GenerationController extends Mage_Core_Controller_Front_Action {
	
	public function getlicenceAction() {
		$key = 'e983cfc54f88c7114e99da95f5757df6';
		$hostname = isset($_GET['hostname']) ? $_GET['hostname'] : "";
		$module = isset($_GET['module']) ? $_GET['module'] : "";
		$id_parent = isset($_GET['id_parent']) ? $_GET['id_parent'] : "";
		if(($module == "SoColissimoFlexibilite" && !preg_match("/_flexi$/", $id_parent)) || ($module == "SoColissimoLiberte" && !preg_match("/_liberte$/", $id_parent))) {
			return;
		}
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