<?php

class Addonline_NewsletterDolist_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{
 
	const XML_PATH_DOLIST_ACTIVE       = 'newsletter/dolist/active';
	const XML_PATH_DOLIST_ID		   = 'newsletter/dolist/dolistid';
	const XML_PATH_DOLIST_SUB_FORMID   = 'newsletter/dolist/subscribe_form_id';
	const XML_PATH_DOLIST_SUB_FORM     = 'newsletter/dolist/subscribe_form';
	const XML_PATH_DOLIST_SUB_EMAIL    = 'newsletter/dolist/subscribe_emailfield';
	const XML_PATH_DOLIST_UNS_FORMID   = 'newsletter/dolist/unsubscribe_form_id';
	const XML_PATH_DOLIST_UNS_EMAIL    = 'newsletter/dolist/unsubscribe_emailfield';
		
	const URL_SUBSCRIPTION_FORM        = 'http://form.dolist.net/sw/default.aspx';
	const URL_UNSUBSCRIPTION_FORM      = 'http://form.dolist.net/uw/Default.aspx';
	
	public $centreInterets = '';
	
	public function subscribe($email)
    {
		$status = parent::subscribe($email);
    	if (Mage::getStoreConfig(self::XML_PATH_DOLIST_ACTIVE)) {
    		if ($status == self::STATUS_SUBSCRIBED) {
				$this->_sendDolistSubscriptionForm();
			}
    	}
    	return $status;
    }

    public function unsubscribe()
    {
    	parent::unsubscribe();
    	if (Mage::getStoreConfig(self::XML_PATH_DOLIST_ACTIVE)) {
			$this->_sendDolistUnsubscriptionForm();
    	} 
		return $this;
    }

    /**
     * Saving customer subscription status
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  Mage_Newsletter_Model_Subscriber
     */
    public function subscribeCustomer($customer)
    {
    	$this->centreInterets = $customer->getInterests();
    	    	
        $subscriber = parent::subscribeCustomer($customer);        
        if (Mage::getStoreConfig(self::XML_PATH_DOLIST_ACTIVE)) {			
				if ($subscriber->getStatus() == self::STATUS_SUBSCRIBED) {
					$this->_sendDolistSubscriptionForm();
				} else if ($subscriber->getStatus() == self::STATUS_UNSUBSCRIBED) {
					$this->_sendDolistUnsubscriptionForm();					
				}			
        }
		return $subscriber;
    }

    /**
     * Confirms subscriber newsletter
     *
     * @param string $code
     * @return boolean
     */
    public function confirm($code)
    {
        $confirm = parent::confirm($code);
    	if ($confirm) {
	    	if (Mage::getStoreConfig(self::XML_PATH_DOLIST_ACTIVE)) {
				$this->_sendDolistSubscriptionForm();
			} 
        }
        return $confirm;
    }
    
    private function _sendDolistSubscriptionForm() {

    	$formId = Mage::getStoreConfig(self::XML_PATH_DOLIST_SUB_FORMID);
    	$emailName = Mage::getStoreConfig(self::XML_PATH_DOLIST_SUB_EMAIL);
    	$this->_sendDolistForm(self::URL_SUBSCRIPTION_FORM, 'do_IdSubscribe', $formId, $emailName);

    }

    private function _sendDolistUnsubscriptionForm() {

    	$formId = Mage::getStoreConfig(self::XML_PATH_DOLIST_UNS_FORMID);
    	$emailName = Mage::getStoreConfig(self::XML_PATH_DOLIST_UNS_EMAIL);
    	$this->_sendDolistForm(self::URL_UNSUBSCRIPTION_FORM, 'do_IdUnsubscribe', $formId, $emailName);
    	
    }
    
    private function _sendDolistForm($url, $formNameId, $formId, $emailName) {    	    	
    	
    	// Récupération des centre d'intérets disponible
    	$formlist = Mage::helper('newsletterdolist')->load();    	    	
    	
    	$params['do_ListId'] = Mage::getStoreConfig(self::XML_PATH_DOLIST_ID);
    	$params[$formNameId] = $formId;
    	$params[$emailName] = $this->getSubscriberEmail();
    	
    	$interets = explode(";",$this->centreInterets);
    	
    	$params[$formlist[1]] = $interets;    	    
    	
    	$response = false;
		
    	$config = array('maxredirects' => 0, 'timeout' => 30);
			
		$client = new Addonline_NewsletterDolist_Http_Client($url, $config);
		$client->setMethod(Addonline_NewsletterDolist_Http_Client::POST);
		
		if (is_array($params) && count($params) > 0)
		{
			$client->setParameterPost($params);
		}
		$response = $client->request();		
		if ($response->getStatus() != '200' && $response->getStatus() != '302') {			
			Mage::throwException("Erreur lors de l'envoi du formulaire dolist");
		}

    }

	// Mantis 6785 : on n'envois jamais d'email de confirmation d'inscription à la newsletter 
    public function sendConfirmationSuccessEmail() {
    	return $this;
    }

    // ... ni de confirmation de déinscription
    public function sendUnsubscriptionEmail() {
    	return $this;
    }         
            
}
