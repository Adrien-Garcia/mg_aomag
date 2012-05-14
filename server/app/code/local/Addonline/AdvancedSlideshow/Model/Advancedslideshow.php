<?php

class Addonline_AdvancedSlideshow_Model_Advancedslideshow extends Mage_Core_Model_Abstract
{
	
	public function _construct()
    {
        parent::_construct();
        $this->_init('advancedslideshow/advancedslideshow');
    }
    
    public function _9cd4777ae76310fd6977a5c559c51820(){
    	$key = 'e983cfc54f88c7114e99da95f5757df6'; if(md5(Mage::getStoreConfig('web/unsecure/base_url').$key.'AdvancedSlideshow')!=Mage::getStoreConfig('advancedslideshow/licence/serial')){
    		$severity=Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR;$title= "Vous devez renseigner une clé licence valide pour le module AdvancedSlideshow. Le module a été désactivé";$description= "Le module AdvancedSlideshow n'a pas une clé licence valide";	$date = date('Y-m-d H:i:s'); Mage::getModel('adminnotification/inbox')->parse(array(array('severity' => $severity,'date_added'=> $date,'title'=> $title,'description'   => $description,'url'=> '','internal'      => true)));    		
    		return false;
    	}else{$_unreadNotices = Mage::getModel('adminnotification/inbox')->getCollection()->getItemsByColumnValue('is_read', 0); foreach($_unreadNotices as $notice): if(strpos($notice->getData('description'),'AdvancedSlideshow')): $notice->setIsRead(1)->save();	endif;endforeach;return true;
    	}
    }
    
}