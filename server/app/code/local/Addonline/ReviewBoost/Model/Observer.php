<?php
/**
 * Addonline_SoColissimoFlexibilite
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimoFlexibilite
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Addonline_ReviewBoost_Model_Observer extends Varien_Object
{

	public function _9cd4777ae76310fd6977a5c559c51820(){		
		if (Mage::getStoreConfig('addonline/licence/aomagento')) { return true; }
		$key = 'e983cfc54f88c7114e99da95f5757df6'; if(md5(Mage::getStoreConfig('web/unsecure/base_url').$key.'ReviewBoost')!=Mage::getStoreConfig('reviewboost/licence/serial')){ $severity=Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR;$title= "Vous devez renseigner une clé licence valide pour le module ReviewBoost. Le module a été désactivé";$description= "Le module ReviewBoost n'a pas une clé licence valide";	$date = date('Y-m-d H:i:s'); Mage::getModel('adminnotification/inbox')->parse(array(array('severity' => $severity,'date_added'=> $date,'title'=> $title,'description'   => $description,'url'=> '','internal'      => true)));return false;	}else{$_unreadNotices = Mage::getModel('adminnotification/inbox')->getCollection()->getItemsByColumnValue('is_read', 0); foreach($_unreadNotices as $notice): if(strpos($notice->getData('description'),'ReviewBoost')): $notice->setIsRead(1)->save();	endif;endforeach;return true;}
	}    
		
}