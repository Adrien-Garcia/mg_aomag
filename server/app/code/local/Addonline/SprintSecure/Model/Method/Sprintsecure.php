<?php
/**
 * SprintSecure Amount Model
 *
 * @category   Addonline
 * @package    Addonline_SprintSecure
 * @name       Addonline_SprintSecure_Model_Method_SprintSecure
**/
class Addonline_SprintSecure_Model_Method_Sprintsecure extends Addonline_SprintSecure_Model_Abstract
{
	private $_url = null;
	private $_message = null;
	private $_error = false;

    protected $_code  = 'sprintsecure';
	
    protected $_formBlockType = 'sprintsecure/sprintsecure_form';
	protected $_infoBlockType = 'sprintsecure/sprintsecure_info';

    /**
     * Availability options
     */
    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = false;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
	
    public function getCode()
	{
	    return $this->_code;
	}
	
	public function isAvailable($quote = null)
	{	
	   	if (Mage::getSingleton('checkout/session')->getQuote()->getIsMultiShipping()) {
            return false;
		} else {
		    return parent::isAvailable($quote);
		}
	}
		
    /**
     *  Form block description
     *
     *  @return	 object
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock($this->_formBlockType, $name);
        $block->setMethod($this->_code);
        $block->setPayment($this->getPayment());
        return $block;
    }
	
    /**
     *  @return	  string Return cancel URL
     */
    public function getCancelReturnUrl()
    {
        return Mage::getUrl('sprintsecure/sprintsecure/cancel');
    }
	
    /**
     *  Return URL for customer response
     *
     *  @return	  string Return customer URL
     */
    public function getNormalReturnUrl()
    {
        return Mage::getUrl('sprintsecure/sprintsecure/normal');
    }
	
    /**
     *  Return URL for automatic response
     *
     *  @return	  string Return automatic URL
     */
    public function getAutomaticReturnUrl()
    {
        return Mage::getUrl('sprintsecure/sprintsecure/automatic');
    }
	
    /**
     *  Return Order Place Redirect URL
     *
     *  @return	  string Order Redirect URL
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('sprintsecure/sprintsecure/redirect');
	}
	
    public function callRequest()
    {
		
    	$customer = Mage::getSingleton('checkout/session')->getQuote()->getCustomer();
       	$billingAddress = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress();
        
		// Numéro | Description                 	 | Oblig/Facult| Type
		// 1      | Encodage utilisé (défaut UTF-8)  | F           | Code
		$solutionprintsecude_data = '#'; 		
		// 2      | Authentification du prescripteur | O           | Spécifications Franfinance
		$solutionprintsecude_data .= $this->getMerchantFranfinanceId().'#'; 
		// 3      | Code style parcours				 | F           | AN
		$solutionprintsecude_data .= $this->getCodeStyleFranfinance().'#'; 		
		// 4      | Code option OPT1				 | F           | AN
		$solutionprintsecude_data .= '#'; 		
		// 5      | Code option OPT2				 | F           | AN
		$solutionprintsecude_data .= '#'; 		
		// 6      | Code option OPT3				 | F           | AN
		$solutionprintsecude_data .= '#'; 		
		// 7      | Code option OPT4				 | F           | AN
		$solutionprintsecude_data .= '#'; 		
		// 8      | Code option OPT5				 | F           | AN
		$solutionprintsecude_data .= '#'; 		
		// 9      | Acceptation – Pré-saisie BIN (défaut 503206)| F| N
		$solutionprintsecude_data .= '#'; 
		// 10     | Civilité						 | F           | A		
		$solutionprintsecude_data .= $customer->getPrefix().'#'; 
		// 11     | Nom								 | F           | A
		$solutionprintsecude_data .= $customer->getLastname().'#'; 		
		// 12     | Nom de jeune fille				 | F           | A
		$solutionprintsecude_data .= '#'; 		
		// 13     | Prénom 							 | F           | A
		$solutionprintsecude_data .= $customer->getFirstname().'#'; 		
		// 14     | Date de naissance				 | F           | A
		$solutionprintsecude_data .= ($customer->getDob()?(substr($customer->getDob(),8,2).'.'.substr($customer->getDob(),5,2).'.'.substr($customer->getDob(),0,4)):'').'#'; 		
		// 15     | Code lieu de naissance			 | F           | A
		$solutionprintsecude_data .= '#'; 		
		// 16     | Code département de naissance	 | F           | A
		$solutionprintsecude_data .= $customer->getRob().'#'; 		
		// 17     | Lieu de naissance				 | F           | A
		$solutionprintsecude_data .= $customer->getCob().'#';
		// 18     | Adresse de livraison			 | F           | A
		$solutionprintsecude_data .= $billingAddress->getStreet1().'#'; 		
		// 19     | Complément d'adresse de livraison| F           | A
		$solutionprintsecude_data .= $billingAddress->getStreet2().'#'; 		
		// 20     | Code postal résidence de livraison|F           | A
		$solutionprintsecude_data .= $billingAddress->getPostcode().'#'; 		
		// 21     | Ville de résidence de livraison  | F           | A
		$solutionprintsecude_data .= $billingAddress->getCity().'#'; 		
		// 22     | Téléphone domicile				 | F           | A
		$solutionprintsecude_data .= $billingAddress->getTelephone().'#'; 		
		// 23     | Téléphone portable 				 | F           | A
		$solutionprintsecude_data .= '#'; 		
		
		//on ajoute des " pour éviter les problèmes avec les espaces... 		
      	$command = ' data="SOLUTIONSPRINTSECURE_DATA='.$solutionprintsecude_data.';"';
      	    
        $parameters = array(
            'command'       => $command,
            'bin_request'   => $this->getBinRequest(),
        	'templatefile'   => $this->getTemplatefile(),
            'merchant_id'   => $this->getMerchantId(),
            'payment_means' => 'SOLUTIONSPRINTSECURE,1',
            'url' => array(
                'cancel' => $this->getCancelReturnUrl(),
                'normal' => $this->getNormalReturnUrl(),
                'automatic' => $this->getAutomaticReturnUrl()
            )
		);
		
		$sips = $this->getApiRequest()->doRequest($parameters);
		
        if ($sips['error']) {
            $this->_error = true;
        } else {
	        $regs = array();
			
	        if (eregi('<form [^>]*action="([^"]*)"[^>]*>(.*)</form>', $sips['message'], $regs)) {
	            $this->_url = $regs[1];
                $this->_message = $regs[2];
	        } else {
                $this->_error = true;
                $this->_message = Mage::helper('sprintsecure')->__('Call Bin Request Error - Check path to the file or command line for debug');
	        }
        }
    }
	
	public function getSystemUrl() 
	{	
	    return $this->_url;
	}
	
	public function getSystemMessage() 
	{
	    return $this->_message;
	}
	
    public function getSystemError() 
	{
	    return $this->_error;
	}
	
    /**
     * Return merchant ID
     *
     * @return string
     */
    public function getMerchantId()
    {
	    return $this->getConfigData('merchant_id');
    }

    /**
     * Return merchant FRANFINANCE ID
     *
     * @return string
     */
    public function getMerchantFranfinanceId()
    {
	    return $this->getConfigData('merchant_franfinance_id');
    }
    
    /**
     * Return Code style parcours FRANFINANCE
     *
     * @return string
     */
    public function getCodeStyleFranfinance()
    {
	    return $this->getConfigData('code_style_franfinance');
    }
    
    
    public function getPathfile()
    {
	    return $this->getConfigData('pathfile');
    }
	
    /**
     *  Return Atos bin file for request
     *
     *  @return	  string
     */
    public function getBinRequest()
    {
	    return $this->getConfigData('bin_request');
    }
	
    /**
     *  Return Atos bin file for response
     *
     *  @return	  string
     */
    public function getBinResponse()
    {
	    return $this->getConfigData('bin_response');
    }

    public function getCheckByIpAddress()
	{
	    return $this->getConfigData('check_ip_address');
	}
	
	public function getDataFieldKeys()
	{
	    return $this->getConfigData('data_field');
	}


     public function getCaptureMode()
	{
	    return $this->getConfigData('capture_mode');
	}

    public function getCaptureDays()
	{
	    return $this->getConfigData('capture_days');
	}
        
    /**
     *  Return new order status
     *
     *  @return	  string New order status
     */
    public function getNewOrderStatus()
    {
        return $this->getConfigData('order_status');
    }
	
    /**
     * Return a minimum amount to activate the module
     *
     * @return number
     */
    public function getMinimumAmount()
    {
	    return $this->getConfigData('min_order_total');
    }
}
