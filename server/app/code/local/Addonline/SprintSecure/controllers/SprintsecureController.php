<?php
/*
 * SprintSecure Form Method Front Controller
 *
 * @category   Addonline
 * @package    Addonline_SprintSecure
 * @name       Addonline_SprintSecure_SprintcecureController
*/
class Addonline_SprintSecure_SprintsecureController extends Mage_Core_Controller_Front_Action
{	
    /**
     * Get singleton with SprintSecure
     *
     * @return object Addonline_SprintSecure_SprintSecure
     */
    public function getSprintsecure()
    {
        return Mage::getSingleton('sprintsecure/method_sprintsecure');
    }
	
	public function getConfig()
	{
	    return Mage::getSingleton('sprintsecure/config');
	}
		
    public function redirectAction()
    {
	    $session = Mage::getSingleton('checkout/session');
        
		if ($session->getQuote()->getHasError())
		{
		    $this->_redirect('checkout/cart');
		} else {
	        if ($session->getQuoteId()) 
		    {
                $session->setSprintsecureQuoteId($session->getQuoteId());
		    }
		
			$this->getResponse()->setBody($this->getLayout()->createBlock('sprintsecure/sprintsecure_redirect')->toHtml());
		}
    }
	
	public function cancelAction()
	{   
	    $model = $this->getSprintsecure();
		
		if ( $response = $model->getApiResponse()->doResponse($_POST['DATA'], array('bin_response' => $model->getBinResponse() ) ) ) 
		{
		    $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($response['order_id']);
			
			if ($order->getId()) 
			{
                $order->cancel();
			    $order->save();
			}
		}

        $session = Mage::getSingleton('checkout/session');
	    $session->setQuoteId($session->getSprintsecureQuoteId(true));
	
		$this->_redirect('checkout/cart');
	}
	
	public function normalAction() 
	{
	    $model = $this->getSprintsecure();
		$session = Mage::getSingleton('checkout/session');
		
        if (!$this->getRequest()->isPost('DATA')) 
	    {
            $this->_redirect('');
            return;
        }
		
		$response = $model->getApiResponse()->doResponse($_POST['DATA'], array('bin_response' => $model->getBinResponse() ) );
				
		if ($response['merchant_id'] != $model->getMerchantId()) 
		{
		    Mage::log(sprintf('Response Merchant ID (%s) is not valid with configuration value (%s)' . "\n", $response['merchant_id'], $model->getMerchantId()), null, 'sprintsecure');
		   
		    $session->addError($this->__('We are sorry but we have an error with payment module'));
		    $this->_redirect('checkout/cart');
		    return;
		}
		
		switch ($response['response_code'])
		{
		    case '00':
                $session->setQuoteId($session->getSprintsecureQuoteId(true));
		     	$session->getQuote()->setIsActive(false)->save();
				
				$this->_redirect('checkout/onepage/success', array('_secure'=>true));
			    break;
				
			default:
		        $session->addError($this->__('(Response Code %s) Error with payment module', $response['response_code']));
		        $this->_redirect('checkout/cart');
			    break;
		}
	}


	public function automaticAction() 
	{
	    $model = $this->getSprintsecure();
	
        if (!$this->getRequest()->isPost('DATA')) 
	    {
            $this->_redirect('');
            return;
        }
	
        if ($this->getConfig()->getCheckByIpAddress()) 
		{
			if (!in_array($model->getApiParameters()->getIpAddress(), $this->getConfig()->getAuthorizedIps())) 
			{
		        Mage::log($model->getApiParameters()->getIpAddress() . ' tries to connect to our server' . "\n", null, 'sprintsecure');
		        return;
			}
	    }
		
		$response = $model->getApiResponse()->doResponse($_POST['DATA'], array('bin_response' => $model->getBinResponse() ) );
				
		if ($response['merchant_id'] != $model->getMerchantId()) 
		{
		    Mage::log(sprintf('Response Merchant ID (%s) is not valid with configuration value (%s)' . "\n", $response['merchant_id'], $model->getMerchantId()), null, 'sprintsecure');
		    return;
		}
		
		$order = Mage::getModel('sales/order');
        $order->loadByIncrementId($response['order_id']);
			   
		/*switch ($response['response_code'])
		{
// Success order
		    case '00':
			   if ($order->getId()) 
			   {
                   $order->addStatusToHistory(
                       'processing_sap',
                       Mage::getSingleton('sprintsecure/api_response')->describeResponse($response)
				   );

// Create invoice
                   $this->saveInvoice($order);
				   
                   $order->sendNewOrderEmail();
			   }
			   break;
			   
			default:
// Cancel order
			    if ($order->getId()) 
				{
                    $order->addStatusToHistory(
                       Mage_Sales_Model_Order::STATE_CANCELED,
                       Mage::getSingleton('sprintsecure/api_response')->describeResponse($response)
				    );
				
			        $order->cancel();
				}
			    break;
		}*/
        switch ($response['response_code']) {
        	// Success order
        	case '00':
        		if ($order->getId()) {
        			if ($order->getState() == Mage_Sales_Model_Order::STATE_HOLDED) {
        				$order->unhold();
        			}
        
        			if (!$status = $model->getConfigData('order_status_payment_accepted')) {
        				$status = $order->getStatus();
        			}
        
        			$message = $this->__('Payment accepted by SprintSecure');
        			$message .= ' - '.Mage::getSingleton('sprintsecure/api_response')->describeResponse($response);
        
        			if ($status == Mage_Sales_Model_Order::STATE_PROCESSING) {
        				$order->setState(
        				Mage_Sales_Model_Order::STATE_PROCESSING,
        				$status,
        				$message
        				);
        			} else if ($status == "preparing") {
        				$order->setState(
        				Mage_Sales_Model_Order::STATE_PROCESSING,
        				$status,
        				$message
        				);
        			} else if ($status == Mage_Sales_Model_Order::STATE_COMPLETE) {
        				$order->setState(
        				Mage_Sales_Model_Order::STATE_COMPLETE,
        				$status,
        				$message
        				);
        			} else {
        				$order->addStatusToHistory(
        				$status,
        				$message,
        				true
        				);
        			}
        
        			// Create invoice
        			$this->saveInvoice($order);
        				
        			$order->sendNewOrderEmail();
        		}
        		break;
        
        	default:
        		// Cancel order
        		if ($order->getId()) {
        		$messageError = $this->__('Customer was rejected by SprintSecure');
        		$messageError .= ' - '.Mage::getSingleton('sprintsecure/api_response')->describeResponse($response);
        
        		if ($order->getState() == Mage_Sales_Model_Order::STATE_HOLDED) {
        			$order->unhold();
        		}
        		 
        		if (!$status = $model->getConfigData('order_status_payment_refused')) {
        			$status = $order->getStatus();
        		}
        		 
        		$order->addStatusToHistory(
        		$status,
        		$messageError
        		);
        
        		if ($status == Mage_Sales_Model_Order::STATE_HOLDED && $order->canHold()) {
        			$order->hold();
        		}
        	}
        	break;
        }
		
		$order->save();
	}
	
    /**
     *  Save invoice for order
     *
     *  @param    Mage_Sales_Model_Order $order
     *  @return	  boolean Can save invoice or not
     */
    protected function saveInvoice(Mage_Sales_Model_Order $order)
    {
        if ($order->canInvoice()) 
	    {
            $convertor = Mage::getModel('sales/convert_order');
            
			$invoice = $convertor->toInvoice($order);
                       
			foreach ($order->getAllItems() as $orderItem) 
			{
                if (!$orderItem->getQtyToInvoice()) 
			    {
                    continue;
                }
						   
                $item = $convertor->itemToInvoiceItem($orderItem);
                $item->setQty($orderItem->getQtyToInvoice());
                $invoice->addItem($item);
            }
					   
            $invoice->collectTotals();
            $invoice->register();
                      
		    Mage::getModel('core/resource_transaction')
              ->addObject($invoice)
              ->addObject($invoice->getOrder())
              ->save();
						 
            $order->addStatusToHistory(
                'processing_sap',
                Mage::helper('sprintsecure')->__('Invoice %s was created', $invoice->getIncrementId())
            );
        }

        return false;
    }
}
