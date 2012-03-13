<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Ogone
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Ogone payment method model
 */
class Addonline_Ogonedirectlink_Model_Api extends Mage_Payment_Model_Method_Cc
{
    protected $_code  = 'ogonedirectlink';
    protected $_formBlockType = 'ogonedirectlink/form';
    protected $_infoBlockType = 'ogonedirectlink/info';
    protected $_config = null;

     /**
     * Availability options
     */
    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_paymentAction;
	const ACTION_CAPTURE  = 'capture';
	
    /* Ogone responce statuses */
    const OGONE_PAYMENT_REQUESTED_STATUS    = 9;
    const OGONE_PAYMENT_PROCESSING_STATUS   = 91;
    const OGONE_AUTH_UKNKOWN_STATUS         = 52;
    const OGONE_PAYMENT_UNCERTAIN_STATUS    = 92;
    const OGONE_PAYMENT_INCOMPLETE          = 1;
    const OGONE_AUTH_REFUZED                = 2;
    const OGONE_AUTH_PROCESSING             = 51;
    const OGONE_TECH_PROBLEM                = 93;
    const OGONE_AUTHORIZED                  = 5;

    /* ogone payment action constant*/
    const OGONE_AUTHORIZE_ACTION = 'RES';
    const OGONE_AUTHORIZE_CAPTURE_ACTION = 'SAL';
    const OGONE_CAPTURE_ACTION = 'SAS';

    protected $_ogoneCardTypes = array('AE'=>'American Express', 'VI'=>'VISA', 'MC'=> 'MasterCard', ''=>'Aurore');

    /**
     * Init Ogone Api instance, detup default values
     *
     * @return Addonline_Ogonedirectlink_Model_Api
     */
    public function __construct()
    {
        $this->_config = Mage::getSingleton('ogonedirectlink/config');
        return $this;
    }

    /**
     * Return ogone config instance
     *
     * @return Addonline_Ogonedirectlink_Model_Api
     */
    public function getConfig()
    {
        return $this->_config;
    }

    public function validate()
    {
    	$info = $this->getInfoInstance();
    	if ($info->getCcNumber()) {
    		//on ne valide le formulaire que lors de l'enregistrement des données du formulaire, pas lors du choix du mode de paiement
	    	parent::validate();
    	} 
    	return $this;

    }
    /**
     * Return debug flag by storeConfig
     *
     * @param int storeId
     * @return bool
     */
    public function getDebug($storeId=null)
    {
        return $this->getConfig()->getConfigData('debug_flag', $storeId);
    }
    
    /**
     * Rrepare params array to send it to gateway page via POST
     *
     * @param Mage_Sales_Model_Order
     * @return array
     */
    private function getFormFields($payment, $order)
    {
    	if (empty($payment)) {
            if (!($payment = $this->getPayment())) {
                return array();
            }
    	}    
    	if (empty($order)) {
    		if (!($order = $this->getOrder())) {
                return array();
            }
    	}
        $billingAddress = $order->getBillingAddress();
        $formFields = array();
        //Données Ogone
        $formFields['PSPID']    = $this->getConfig()->getPSPID();
        $formFields['ORDERID']  = $order->getIncrementId();
        $formFields['USERID']    = $this->getConfig()->getUSERID();
        $formFields['PSWD']  = $this->getConfig()->getPSWD();
        //Données commandes
        $formFields['AMOUNT']   = round($order->getBaseGrandTotal()*100);
        if ($this->getPaymentAction() == self::ACTION_CAPTURE) { 
			$formFields['PAYID'] = $payment->getCcTransId();
		    //Type d'opération  	
			$formFields['OPERATION'] = self::OGONE_CAPTURE_ACTION;
		} else {
			$formFields['CURRENCY'] = Mage::app()->getStore()->getBaseCurrencyCode();
			$formFields['LANGUAGE'] = substr(Mage::app()->getLocale()->getLocaleCode(), 0, strpos(Mage::app()->getLocale()->getLocaleCode(), '_'));
	 		$formFields['COM']      = $this->_translate($this->_getOrderDescription($order));
		    //Type d'opération  	
        	$paymentAction = $this->_getOgonePaymentOperation();
        	if ($paymentAction) {
            	$formFields['OPERATION'] = $paymentAction;
        	}
	 		//Données client
        	$formFields['EMAIL'] = $order->getCustomerEmail();
	        $formFields['OWNERADDRESS'] = $this->_translate(str_replace("\n", ' ',$billingAddress->getStreet(-1)));
	        $formFields['OWNERZIP'] = $billingAddress->getPostcode();
	        $formFields['OWNERTOWN']= $this->_translate($billingAddress->getCity());
	        $formFields['OWNERCTY'] = $billingAddress->getCountry();
	        $formFields['OWNERTELNO']   = $billingAddress->getTelephone();
			//Données CB
	        $formFields['CARDNO'] = $payment->getCcNumber();
	        $formFields['CN'] = $this->_translate($payment->getCcOwner());
	        $formFields['ED'] = Mage::getModel('core/date')->date('my', mktime(0,0,0,$payment->getCcExpMonth(),1,$payment->getCcExpYear()));
	        $formFields['CVC'] = $payment->getCcCid();
			$formFields['BRAND'] = $this->_ogoneCardTypes[$payment->getCcType()];
			$formFields['REMOTE_ADDR'] = $order->getRemoteIp();
			//données adresses du client à renseigner pour les avoir sur le détail dans le BO Ogone	
			$formFields['ECOM_PAYMENT_CARD_VERIFICATION'] = $formFields['CVC'];
			/* Ces données font planter le paiement : 
			$formFields['ECOM_CONSUMERID'] = $order->getCustomerId();
			$formFields['ECOM_BILLTO_POSTAL_CITY'] = $this->_translate($billingAddress->getCity()); 
			$formFields['ECOM_BILLTO_POSTAL_COUNTRYCODE'] = $billingAddress->getCountry();
			$formFields['ECOM_BILLTO_POSTAL_NAME_FIRST'] = $this->_translate($billingAddress->getFirstname());
			$formFields['ECOM_BILLTO_POSTAL_NAME_LAST'] = $this->_translate($billingAddress->getLastname());
			$formFields['ECOM_BILLTO_POSTAL_POSTALCODE'] = $billingAddress->getPostcode();
			$formFields['ECOM_BILLTO_POSTAL_STREET_LINE1'] = $this->_translate($billingAddress->getStreet1());
			$formFields['ECOM_BILLTO_POSTAL_STREET_LINE2'] = $this->_translate($billingAddress->getStreet2());
			*/
 			$shippingAddress = $order->getShippingAddress();
			$formFields['ECOM_SHIPTO_ONLINE_EMAIL'] = $shippingAddress->getEmail();
			$formFields['ECOM_SHIPTO_POSTAL_CITY'] = $this->_translate($shippingAddress->getCity()); 
			$formFields['ECOM_SHIPTO_POSTAL_COUNTRYCODE'] = $shippingAddress->getCountry();
			$formFields['ECOM_SHIPTO_POSTAL_NAME_FIRST'] = $this->_translate(substr ($shippingAddress->getFirstname(), 0, 34));
			$formFields['ECOM_SHIPTO_POSTAL_NAME_LAST'] = $this->_translate(substr ($shippingAddress->getLastname(), 0, 34));
			$formFields['ECOM_SHIPTO_POSTAL_POSTALCODE'] = $shippingAddress->getPostcode();
			$formFields['ECOM_SHIPTO_POSTAL_STREET_LINE1'] = $this->_translate($shippingAddress->getStreet1());
			$formFields['ECOM_SHIPTO_POSTAL_STREET_LINE2'] = $this->_translate($shippingAddress->getStreet2());
			$formFields['ECOM_SHIPTO_TELECOM_PHONE_NUMBER'] = $shippingAddress->getTelephone();
                        
			$secretSet = '';
			if (!$this->getConfig()->getNewHashingMethod()) {
				// ANCIENNE FORME DE CALCUL DU SHA (compte ogone antérieur à Mai 2010 ou configuré avec "Main parameters only. ")
				$secretSet  = $formFields['ORDERID'] . $formFields['AMOUNT'] . $formFields['CURRENCY'] . $formFields['CARDNO'] .
		            $formFields['PSPID'] . $formFields['OPERATION'] . $this->getConfig()->getShaInCode();
			} else {	        
				// NOUVELLE FORME DE CALCUL DU SHA (compte ogone postérieur à Mai 2010 ou ou configuré avec "Each parameter followed by the pass phrase. ")
				//AMOUNT=56900+++HASHKEY+++BRAND=VISA+++HASHKEY+++CARDNO=41XXXXXXXXXX1111+++HASHKEY+++CN=Sylvain PRAS+++HASHKEY+++COM=PHILIPS 32 PFL 6605H+++HASHKEY+++CURRENCY=EUR+++HASHKEY+++CVC=XXXX++HASHKEY+++ECOM_PAYMENT_CARD_VERIFICATION=XXXX++HASHKEY+++ED=0112+++HASHKEY+++EMAIL=sylvain.pras@addonline.fr+++HASHKEY+++LANGUAGE=fr+++HASHKEY+++OPERATION=RES+++HASHKEY+++ORDERID=29000034+++HASHKEY+++OWNERADDRESS=3 rue Fochier+++HASHKEY+++OWNERCTY=FR+++HASHKEY+++OWNERTELNO=0662740296+++HASHKEY+++OWNERTOWN=BOURGOIN+++HASHKEY+++OWNERZIP=38300+++HASHKEY+++PSPID=cobrason+++HASHKEY+++PSWD=XXXXXXXXXXXXXXXXXXXXXXXXX+++HASHKEY+++USERID=cobrasonapi+++HASHKEY+++
			ksort($formFields);
                            foreach ($formFields as $fieldName => $fieldValue) {
					if($fieldValue !=='') $secretSet .= ($fieldName."="./*$this->myUrlEncode(*/iconv("UTF-8", "ISO-8859-1", $formFields[$fieldName])/*). $this->myUrlEncode(*/.iconv("UTF-8", "ISO-8859-1", $this->getConfig()->getShaInCode())/*)*/);

				}
			}
	        //Mage::log($secretSet);
	        $formFields['SHASign']  = Mage::helper('ogonedirectlink')->shaCrypt($secretSet);
	        //Mage::log( $formFields['SHASign']);
	        
		}
		        
        return $formFields;
    }

    /**
     * Get Ogone Payment Action value
     *
     * @param string
     * @return string
     */
    protected function _getOgonePaymentOperation()
    {
        $value = $this->getPaymentAction();
        
        if ($value==Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE) {
            $value = Addonline_Ogonedirectlink_Model_Api::OGONE_AUTHORIZE_ACTION;
        } elseif ($value==Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE) {
            $value = Addonline_Ogonedirectlink_Model_Api::OGONE_AUTHORIZE_CAPTURE_ACTION;
        } elseif ($value==self::ACTION_CAPTURE) {
            $value = Addonline_Ogonedirectlink_Model_Api::OGONE_CAPTURE_ACTION;
        } 
        return $value;
    }
    
    /**
     * to translate UTF 8 to ISO 8859-1
     * Ogone system is only compatible with iso-8859-1 and does not (yet) fully support the utf-8
     */
    protected function _translate($text)
    {
        return htmlentities(iconv("UTF-8", "ISO-8859-1", $text));
    }


    /**
     * Return payment_action value from config area
     *
     * @return string
     */
    public function getPaymentAction()
    {
        if (!$this->_paymentAction) {
        	$this->_paymentAction = $this->getConfig()->getConfigData('payment_action');
        }
    	return $this->_paymentAction;
    }

    /**
     * get formated order description
     *
     * @param Mage_Sales_Model_Order
     * @return string
     */
    protected function _getOrderDescription($order)
    {
        $invoiceDesc = '';
        $lengs = 0;
        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $invoiceDesc .= $item->getName() . ', ';
        }
        $invoiceDesc = Mage::helper('core/string')->substr($invoiceDesc, 0, -2);
        //COM filed can only handle max 100 : on prend une marge de 5% pour les cas limites ...
        if (Mage::helper('core/string')->strlen($invoiceDesc) >= 95) {
             $invoiceDesc = Mage::helper('core/string')->substr($invoiceDesc, 0, 95);
        }
        return $invoiceDesc;
    }
    
	public function authorize(Varien_Object $payment, $amount)
    {

    	parent::authorize($payment, $amount);

		$this->callOgone($payment, $amount, false);

        return $this;
    }

    public function capture(Varien_Object $payment, $amount)
    {

    	$this->_paymentAction = 'capture';
    	
    	parent::capture($payment, $amount);
    	
		//on fait la télécollecte que si elle n'a pas déjà été faite
    	if ($payment->getCcStatus() != self::STATUS_SUCCESS) {
	    	$this->callOgone($payment, $amount, true);
		}

        return $this;
    }

    private function callOgone(Varien_Object $payment, $amount, $capture = false)
    {
    
    	$this->setAmount($amount)
            ->setPayment($payment)
            ->setOrder($payment->getOrder());

        $formFields = $this->getFormFields($payment, $this->getOrder());
        $result = $this->call($formFields);
            
        if ($result) {
        	$this->setTransactionId($result['PAYID']);
        	$payment->setStatus(self::STATUS_APPROVED);
        	if ($capture) {
        		$payment->setCcStatus(self::STATUS_SUCCESS);
        	} else {
        		$payment->setCcStatus(self::STATUS_APPROVED);
        	}
            $payment->setCcTransId($this->getTransactionId());
            $payment->setLastTransId($this->getTransactionId());
                
        } else {
            $e = $this->getError();
            if (isset($e['message'])) {
                $message = $e['message'];
            } else {
                $message = Mage::helper('ogonedirectlink')->__('There has been an error processing your payment. Please try later or contact us for help.');
            }
            Mage::throwException($message);
        }
        
    }
    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED);
        return $this;
    }

	/**
     * 	Urlencode function and rawurlencode are mostly based on RFC 1738.
	 *	However, since 2005 the current RFC in use for URIs standard is RFC 3986.
	 *	Here is a function to encode URLs according to RFC 3986.
     *
     * @param string $string
     * @return $string
     */
    public function myUrlEncode($string) {
	    $entities = array('%2A');
	    $replacements = array('*');
	    return str_replace($entities, $replacements, urlencode($string));
	}
	
    /**
     * Making a call to gateway
     *
     * @param string $requestStr
     * @return bool | array
     */
    public function call($formFields)
    {
		//Mage::log($formFields);
    	$tmpArray = array(); 
        foreach ($formFields as $key => $value) {
            if($value !=='') $tmpArray[] = $key . '=' . $this->myUrlEncode(iconv("UTF-8", "ISO-8859-1", $value));
        }
        $requestBody = implode('&', $tmpArray);

        if ($this->getDebug()) {
            $debug = Mage::getModel('ogonedirectlink/api_debug')
                ->setUrl($requestBody)
                ->save();
        }

        $this->unsError();

        $http = new Varien_Http_Adapter_Curl();
        $config = array('timeout' => 30);
        $http->setConfig($config);
        //Mage::log($requestBody);
        $url = $this->getConfig()->getOgoneOrderDirectlinkUrl();
        if ($this->getPaymentAction() == self::ACTION_CAPTURE) { 
        	$url = $this->getConfig()->getOgoneMaintenanceDirectlinkUrl();
        }
        //Mage::log($url);
        $http->write(Zend_Http_Client::POST, $url, '1.1', array(), $requestBody);
        $response = $http->read();
        
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);
        if ($http->getErrno()) {
            $http->close();
            
            if ($this->getDebug()) {
	           	$debug->setData('data',$response)->save();
	        }
            $this->setError(array(
                'message' => $http->getError()
            ));
            return false;
        }
        $http->close();

        if ($this->getDebug()) {
        	$debug->setData('data', $response)->save();
        }

        $parsedResArr = $this->parseResponseStr($response);
		//Mage::log($parsedResArr);
        if (isset($parsedResArr['NCERROR'])) {
                        $codeErreur = $parsedResArr['NCERROR'];
			if ($codeErreur!='0') {
//				if ($codeErreur == '50001113') { //déjà envoyée
//					 return $parsedResArr;
//				}
				$messageErreur = $parsedResArr['NCERRORPLUS'];
				if (strpos($codeErreur,'002')===0 || $codeErreur == '30051001') {
					$messageErreur = 'Votre paiement n\'a pas abouti.\nIl semblerait que la clé de sécurité et/ou la date de validité de votre carte soit mal renseignée. Vous pouvez refaire votre paiement après correction de ces données.\nMerci.';
				}
				if (strpos($codeErreur,'003')===0) {
					$messageErreur = 'Système de paiement en cours de maintenance, merci de réésayer plus tard';
				}
				if (strpos($codeErreur,'004')===0) {
					$messageErreur = 'Votre paiement n\'a pas abouti.\nIl semblerait que la clé de sécurité et/ou la date de validité de votre carte soit mal renseignée. Vous pouvez refaire votre paiement après correction de ces données.\nMerci.';
				}
				if (strpos($codeErreur,'005')===0) {
					$messageErreur = 'erreur de configuration';
				}
				if ($codeErreur == '50001005') {
					$messageErreur = 'La date d\'expiration est invalide.';
				}
				if ($codeErreur == '50001111' && ' no brand' == $parsedResArr['NCERRORPLUS']) {
					$messageErreur = 'Numéro de carte invalide pour le type de carte selectionné.';
				}
	        	
	        	$this->setError(array(
	                'message' => $messageErreur
	            ));
	            
	            return null;
			}
            
        }
        return $parsedResArr;
 
    }

    /**
     * Parsing response string
     *
     * @param string $str
     * @return array
     */
    public function parseResponseStr($str)
    {
		// problème avec les accents lors du parsing ... on suppirme les accents
        $str = iconv('ISO-8859-1', 'US-ASCII//TRANSLIT', $str);
    	
    	$xml = new Varien_Simplexml_Config($str);
    	$responseArr = array();
    	$responseArr['ORDERID'] = $xml->getNode()->getAttribute('ORDERID');
    	$responseArr['PAYID'] = $xml->getNode()->getAttribute('PAYID');
    	$responseArr['NCSTATUS'] = $xml->getNode()->getAttribute('NCSTATUS');
    	$responseArr['NCERROR'] = $xml->getNode()->getAttribute('NCERROR');
    	$responseArr['ACCEPTANCE'] = $xml->getNode()->getAttribute('ACCEPTANCE');
    	$responseArr['STATUS'] = $xml->getNode()->getAttribute('STATUS');
    	$responseArr['AMOUNT'] = $xml->getNode()->getAttribute('AMOUNT');
    	$responseArr['CURRENCY'] = $xml->getNode()->getAttribute('CURRENCY');
    	$responseArr['PM'] = $xml->getNode()->getAttribute('PM');
    	$responseArr['BRAND'] = $xml->getNode()->getAttribute('BRAND');
    	$responseArr['NCERRORPLUS'] = $xml->getNode()->getAttribute('NCERRORPLUS');

    	return $responseArr;
    }     

}
