<?php

class Addonline_Brand_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;
    
    public function _9cd4777ae76310fd6977a5c559c51820(){
    	$key = 'e983cfc54f88c7114e99da95f5757df6'; if(md5(Mage::getStoreConfig('web/unsecure/base_url').$key.'Brand')!=Mage::getStoreConfig('brand/licence/serial')){
    		Mage::log(md5(Mage::getStoreConfig('web/unsecure/base_url').$key.'Brand'));
    		$severity=Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR;$title= "Vous devez renseigner une clé licence valide pour le module Brand. Le module a été désactivé";$description= "Le module Brand n'a pas une clé licence valide";	$date = date('Y-m-d H:i:s'); Mage::getModel('adminnotification/inbox')->parse(array(array('severity' => $severity,'date_added'=> $date,'title'=> $title,'description'   => $description,'url'=> '','internal'      => true)));
    		//Mage::getModel('core/config')->saveConfig('payment/sprintsecure/active', 0 );
    		return false;
    	}else{$_unreadNotices = Mage::getModel('adminnotification/inbox')->getCollection()->getItemsByColumnValue('is_read', 0); foreach($_unreadNotices as $notice): if(strpos($notice->getData('description'),'Brand')): $notice->setIsRead(1)->save();	endif;endforeach;return true;
    	}
    }

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('brand')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('brand')->__('Disabled')
        );
    }
}