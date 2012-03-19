<?php

require_once "Mage/Newsletter/controllers/ManageController.php";
class Addonline_NewsletterDolist_ManageController extends Mage_Newsletter_ManageController
{    

    public function saveAction()
    {    	
        if (!$this->_validateFormKey()) {
            return $this->_redirect('customer/account/');
        }
        try {        	
        	
        	// abonnement à la newsletter
        	Mage::getSingleton('customer/session')->getCustomer()
        	->setStoreId(Mage::app()->getStore()->getId())
        	->setIsSubscribed((boolean)$this->getRequest()->getParam('is_subscribed', false))
        	->save();
        	
        	$postData = Mage::app()->getRequest()->getPost();
			$tcentreInterets = array();
        	foreach($postData as $key => $value):
        		if (substr($key,0,14) == 'centreinteret_'):
        			$tcentreInterets[] = substr($key,14,strlen($key));
        		endif; 
        	endforeach;
        	$centreInterets = implode(';', $tcentreInterets);
        	Mage::getSingleton('customer/session')->getCustomer()
        	->setStoreId(Mage::app()->getStore()->getId())
        	->setInterests($centreInterets)
        	->save();
        	
        	if ((boolean)$this->getRequest()->getParam('is_subscribed', false)) {
                Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been saved.'));
            } else {
                Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been removed.'));
            }
        	
        }
        catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError($this->__('An error occurred while saving your subscription.'));
        }
        $this->_redirect('customer/account/');
            	
    }
}
