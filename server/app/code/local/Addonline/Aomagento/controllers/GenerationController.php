<?php

class Addonline_Aomagento_GenerationController extends Mage_Core_Controller_Front_Action {
	
	public function getlicenceAction() {
		$key = 'e983cfc54f88c7114e99da95f5757df6';
		if($_GET['hostname'] != "") {
			$licence = md5($_GET['hostname'].$key.$_GET['module']);
			echo $licence;
		} else {
			echo "Vous n'avez pas renseigné le nom de domaine.";
		}
		
	}
}