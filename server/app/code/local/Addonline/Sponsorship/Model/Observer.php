<?php
/**
 * @category   Addonline
 * @package    Addonline_Sponsorship
 * @author     Addonline
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Addonline_Sponsorship_Model_Observer
{
 

    public function formatDate ($date)
    {
    	$date = strtotime($date);
    	return date('Y-m-d', $date);
    }



    public function getSponsorId($cId)
    {
    	$customer = Mage::getModel('customer/customer')->load($cId);
    	$sponsorId = $customer->getSponsor();
    	if ($sponsorId != null && $sponsorId != 0)
    	{
    		return $sponsorId;
    	}
    	else {
    		return false;
    	}
    }

    public function setSponsor($observer)
    {
        //checkout_type_onepage_save_order_after
        $quote = $observer['quote'];
        $order = $observer['order'];

        //si c'est un enregistrement (nouveau client)
        if ($quote->getData('checkout_method')==Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER)
        {
            //recherche d'un parrain éventuel
            $customerId = $order->getCustomerId();
            if ($customerId != '')
            {
                $customer = mage::getModel("customer/customer")->load($customerId);
                $sponsorId = mage::helper("sponsorship/data")->searchSponsorId($customer->getEmail());
                if ($sponsorId != '')
                {
                    $customer->setData('sponsor',$sponsorId);
                    $cookie = new Mage_Core_Model_Cookie;
                    if ($cookie->get('sponsorship_id'))
                    {
                            $cookie->delete('sponsorship_id');
                            $cookie->delete('sponsorship_email');
                            $cookie->delete('sponsorship_firstname');
                            $cookie->delete('sponsorship_lastname');
                    }
                    $customer->save();
                }
            }
        }
    }

     
    public function affiliate($observer)
    {
    	$controller = $observer['controller_action'];
    	/*
    	 * Transmission de l'id du parrain + nom + prenom dans l'url
    	 * base url / module / controller / action / parametres
    	 * http://www.inkonso.com/cms/index/index/sponsor_id/x/nom/xxx/prenom/xxx/email/xxx
        */
    	$sponsorId = $controller->getRequest()->getParam('sponsor_id');    	
    	if ($sponsorId!='')
    	{
    		$nom = $controller->getRequest()->getParam('nom');
        	$prenom = $controller->getRequest()->getParam('prenom');
        	$email = $controller->getRequest()->getParam('email');
        	
        	//stockage des variables dans la session
        	$session = Mage::getSingleton('core/session');
            $session->setData('sponsor_id',$sponsorId);
        	$session->setData('firstname',$prenom);
        	$session->setData('lastname',$nom);
        	$session->setData('email',$email);
        	
        	//stockage de l'id du parrain dans un cookie        	
            $sponsorInvitationValidity = Mage::getStoreConfig('sponsorship/sponsor/sponsor_invitation_validity');
            $period =3600*24*$sponsorInvitationValidity;
                
        	$cookie = new Mage_Core_Model_Cookie;
        	$cookie->set('sponsorship_id', $sponsorId, $period);
        	$cookie->set('sponsorship_firstname', $prenom, $period);
        	$cookie->set('sponsorship_lastname', $nom, $period);
        	$cookie->set('sponsorship_email', $email, $period);
        	
        	$controller->getRequest()->setParam('sponsor_id', null); 
    	}
    }
    
  

}