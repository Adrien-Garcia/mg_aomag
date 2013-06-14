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

class Addonline_GiftProduct_Model_Observer extends Varien_Object
{

	//Licence activation module
	public function _9cd4777ae76310fd6977a5c559c51820(){		
		if (Mage::getStoreConfig('addonline/licence/aomagento')) { return true; }
		$key = 'e983cfc54f88c7114e99da95f5757df6'; if(md5(Mage::getStoreConfig('web/unsecure/base_url').$key.'GiftProduct')!=Mage::getStoreConfig('giftproduct/licence/serial')){ 
			//Mage::getModel('core/config')->saveConfig('newsletter/dolist/active', 0 );
			$severity=Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR;$title= "Vous devez renseigner une clé licence valide pour le module GiftProduct. Le module a été désactivé";
			$description= "Le module GiftProduct n'a pas une clé licence valide";	$date = date('Y-m-d H:i:s'); Mage::getModel('adminnotification/inbox')->parse(array(array('severity' => $severity,'date_added'=> $date,'title'=> $title,'description'   => $description,'url'=> '','internal'      => true)));return false;						
		}else{
			$_unreadNotices = Mage::getModel('adminnotification/inbox')->getCollection()->getItemsByColumnValue('is_read', 0); foreach($_unreadNotices as $notice): if(strpos($notice->getData('description'),'GiftProduct')): $notice->setIsRead(1)->save();	endif;endforeach;return true;
		}
	}    
	
	//Permet d'ajouter l'option produit cadeau au remise panier
	public function addActions($observer)
	{
		$form = $observer->getEvent()->getForm();
		$prefix = $form->getHtmlIdPrefix();
		$fieldset = $form->getElement('action_fieldset');
		$fieldset->removeField('simple_action');
		$fieldset->addField('simple_action', 'select', array(
				'label'     => Mage::helper('salesrule')->__('Apply'),
				'name'      => 'simple_action',
				'options'    => array(
						'by_percent' => Mage::helper('salesrule')->__('Percent of product price discount'),
						'by_fixed' => Mage::helper('salesrule')->__('Fixed amount discount'),
						'cart_fixed' => Mage::helper('salesrule')->__('Fixed amount discount for whole cart'),
						'buy_x_get_y' => Mage::helper('salesrule')->__('Buy X get Y free (discount amount is Y)'),
                		Addonline_GiftProduct_Model_SalesRule_Rule::GIFT_PRODUCT_ACTION => Mage::helper('salesrule')->__('Add gift product (set product ID in the Discount Amount field)')
				),
		));
	}
}
