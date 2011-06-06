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
 * @category   Mage
 * @package    Mage_Paybox
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paybox System Checkout Controller
 *
 * @category   Mage
 * @package    Mage_Paybox
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paybox_SystemController extends Mage_Core_Controller_Front_Action
{
    protected $_payboxResponse = null;

    protected $_responseStatus = false;


public function testAction()
{
$model = 	       Mage::getModel('paybox/direct')->setRang(10)->setSiteNumber(999988);
echo "nico ".$model->getQuestionNumberModel()->getNextQuestionNumber();

$model->getQuestionNumberModel()
                ->increaseQuestionNumber();

}

    /**
     * seting response after returning from paybox
     *
     * @param array $response
     * @return object $this
     */
    protected function setPayboxResponse($response)
    {
        if (count($response)) {
            $this->_payboxResponse = $response;
        }
        return $this;
    }

    /**
     * Get System Model
     *
     * @return Mage_Paybox_Model_System
     */
    public function getModel()
    {
        return Mage::getSingleton('paybox/system');
    }

    /**
     * Get Checkout Singleton
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Redirect action. Redirect customer to Paybox
     *
     */
    public function redirectAction()
    {
        $session = $this->getCheckout();
        $session->setPayboxQuoteId($session->getQuoteId());

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $order->addStatusToHistory($order->getStatus(), $this->__('Customer was redirected to Paybox'));
        $order->save();

        $session->setPayboxOrderId(Mage::helper('core')->encrypt($session->getLastRealOrderId()));
        $session->setPayboxPaymentAction(
            $order->getPayment()->getMethodInstance()->getPaymentAction()
        );

        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('paybox/system_redirect')
                ->setOrder($order)
                ->toHtml()
        );

        $session->unsQuoteId();
    }

    /**
     * Customer returning to this action if payment was successe
     */
    public function successAction()
    {
        $model = $this->getModel();
        $this->setPayboxResponse($this->getRequest()->getParams());

        if ($this->_checkResponse()) {

            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($this->_payboxResponse['ref']);

            if (!$order->getId()) {
                Mage::throwException($this->__('There are no order.'));
            }

            if (Mage::helper('core')->decrypt($this->getCheckout()->getPayboxOrderId()) != $this->_payboxResponse['ref']) {
                Mage::throwException($this->__('Order is not match.'));
            }
            $this->getCheckout()->unsPayboxOrderId();

            if ((int)($order->getBaseGrandTotal()*100) != (int)$this->_payboxResponse['amount']) {            
                $erreur = $this->__('Amount is not match.');
                $erreur .= (int)($order->getBaseGrandTotal()*100).' != '.$this->_payboxResponse['amount'];

                $order->addStatusToHistory($order->getStatus(), $erreur);
                $order->save();
                Mage::throwException($this->__('Amount is not match.'));
            }

            $order->addStatusToHistory($order->getStatus(), $this->__('Customer successfully returned from Paybox'));

            $redirectTo = 'checkout/onepage/success';
            /*if ($this->getCheckout()->getPayboxPaymentAction() == Mage_Paybox_Model_System::PBX_PAYMENT_ACTION_ATHORIZE_CAPTURE) {
                $this->getCheckout()->unsPayboxPaymentAction();
                $order->getPayment()
                    ->getMethodInstance()
                    ->setTransactionId($this->_payboxResponse['trans']);
                
                // Faut-il créer la facture
                if ($model->getConfigData('invoice_create')) {
                    if ($this->_createInvoice($order)) {
                        $order->addStatusToHistory($order->getStatus(), $this->__('Invoice was create successfully'));
                    } else {
                        $order->addStatusToHistory($order->getStatus(), $this->__('Cann\'t create invoice'));
                    }
                }
            }*/

            $session = $this->getCheckout();
            $session->setQuoteId($session->getPayboxQuoteId(true));
            $session->getQuote()->setIsActive(false)->save();
            $session->unsPayboxQuoteId();

            //$order->sendNewOrderEmail();
            $order->save();

            $this->_redirect($redirectTo);
        } else {
            $this->norouteAction();
            return;
        }
    }

    /**
     * Action when payment was refused by Paybox
     */
    public function refuseAction()
    {
        $model = $this->getModel();
        
        $this->setPayboxResponse($this->getRequest()->getParams());
        if ($this->_checkResponse()) {
            $this->getCheckout()->unsPayboxQuoteId();
            $this->getCheckout()->setPayboxErrorMessage($this->__('Order was canceled by Paybox'));

            $order = Mage::getModel('sales/order')
                ->loadByIncrementId($this->_payboxResponse['ref']);
            
            $messageError = $this->__('Customer was refuse by Paybox');
            if (array_key_exists('error', $this->_payboxResponse)) {
                $messageError .= ' - Code Erreur : '.$this->_payboxResponse['error'];
            }
                
            $order->addStatusToHistory(
			    $model->getConfigData('order_status_payment_refused'),
				$messageError
			);

			if ($model->getConfigData('order_status_payment_refused') == Mage_Sales_Model_Order::STATE_CANCELED && $order->canCancel()) {
				$order->cancel();
			} else if ($model->getConfigData('order_status_payment_refused') == Mage_Sales_Model_Order::STATE_HOLDED && $order->canHold()) {
				$order->hold();
			}
			
			$order->save();

            $this->_redirect('*/*/failure');

        } else {
            $this->norouteAction();
            return;
        }
    }

    /**
     * Action when customer cancels payment or press button to back to shop
     */
    public function declineAction()
    {
        $model = $this->getModel();

        $this->setPayboxResponse($this->getRequest()->getParams());
        if ($this->_checkResponse()) {

            $order = Mage::getModel('sales/order')
                ->loadByIncrementId($this->_payboxResponse['ref']);
            /*$order->cancel();
            $order->addStatusToHistory($order->getStatus(), $this->__('Order was canceled by customer'));*/
                
            $order->addStatusToHistory(
			    $model->getConfigData('order_status_payment_canceled'),
				$this->__('Order was canceled by customer')
			);

			if ($model->getConfigData('order_status_payment_canceled') == Mage_Sales_Model_Order::STATE_CANCELED && $order->canCancel()) {
				$order->cancel();
			} else if ($model->getConfigData('order_status_payment_canceled') == Mage_Sales_Model_Order::STATE_HOLDED && $order->canHold()) {
				$order->hold();
			}
                
            $order->save();

            $session = $this->getCheckout();
            $session->setQuoteId($session->getPayboxQuoteId(true));
            $session->getQuote()->setIsActive(false)->save();
            $session->unsPayboxQuoteId();

            $this->_redirect('checkout/cart');
        } else {
            $this->norouteAction();
            return;
        }
    }

    /**
     * Redirect action. Redirect to Paybox using commandline mode
     *
     */
    public function commandlineAction()
    {
        $session = $this->getCheckout();
        $session->setPayboxQuoteId($session->getQuoteId());

        $order = Mage::getModel('sales/order')
            ->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
        $order->addStatusToHistory(
            $order->getStatus(), $this->__('Customer was redirected to Paybox using \'command line\' mode')
        );
        $order->save();

        $session->setPayboxOrderId(Mage::helper('core')->encrypt($session->getLastRealOrderId()));
        $session->setPayboxPaymentAction(
            $order->getPayment()->getMethodInstance()->getPaymentAction()
        );

        $session->unsQuoteId();

        $payment = $order->getPayment()->getMethodInstance();
        $fieldsArr = $payment->getFormFields();
        $paramStr = '';
        foreach ($fieldsArr as $k => $v) {
            $paramStr .= $k.'='.$v.' ';
        }

        $paramStr = str_replace(';', '\;', $paramStr);
        $result = shell_exec(Mage::getBaseDir().'/'.$this->getModel()->getPayboxFile().' '.$paramStr);

        if (isset($fieldsArr['PBX_PING']) && $fieldsArr['PBX_PING'] == '1') {
            $fieldsArr['PBX_PING'] = '0';
            $fieldsArr['PBX_PAYBOX'] = trim(substr($result, strpos($result, 'http')));
            $paramStr = '';
            foreach ($fieldsArr as $k => $v) {
                $paramStr .= $k.'='.$v.' ';
            }

            $paramStr = str_replace(';', '\;', $paramStr);
            $result = shell_exec(Mage::getBaseDir().'/'.$this->getModel()->getPayboxFile().' '.$paramStr);
        }

        $this->loadLayout(false);
        $this->getResponse()->setBody($result);
        $this->renderLayout();
    }

    /**
     * Error action. If request params to Paybox has mistakes
     *
     */
    public function errorAction()
    {
        if (!$this->getCheckout()->getPayboxQuoteId()) {
            $this->norouteAction();
            return;
        }

        $session = $this->getCheckout();
        $session->setQuoteId($session->getPayboxQuoteId(true));
        $session->getQuote()->setIsActive(false)->save();
        $session->unsPayboxQuoteId();

        if (!$this->getRequest()->getParam('NUMERR')) {
            $this->norouteAction();
            return;
        }

        $this->loadLayout();

        $this->getCheckout()
            ->setPayboxErrorNumber(
                $this->getRequest()->getParam('NUMERR')
            );

        $this->renderLayout();
    }

    /**
     * Failure action.
     * Displaying information if customer was redirecting to cancel or decline actions
     *
     */
    public function failureAction()
    {
        if (!$this->getCheckout()->getPayboxErrorMessage()) {
            $this->norouteAction();
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Checking response and Paybox session variables
     *
     * @return unknown
     */
    protected function _checkResponse()
    {
        if (!$this->getCheckout()->getPayboxQuoteId()) {
            $this->norouteAction();
            return;
        }

        if (!$this->getCheckout()->getPayboxOrderId()) {
            $this->norouteAction();
            return;
        }

        if (!$this->getCheckout()->getPayboxPaymentAction()) {
            $this->norouteAction();
            return;
        }

        if (!$this->_payboxResponse) {
            return false;
        }

        //check for valid response
        if ($this->getModel()->checkResponse($this->_payboxResponse)) {
            return true;
        }

        return true;
    }

    /**
     * Creating invoice
     *
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    protected function _createInvoice(Mage_Sales_Model_Order $order)
    {
        if ($order->canInvoice()) {
			$invoice = $order->prepareInvoice();
            $invoice->register()->capture();
            Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
            return true;
        }
        return false;
    }

    /**
	 *  Paybox response router
	 *
	 *  @param    none
	 *  @return	  void
	 */
	public function notifyAction()
	{
	    $model = $this->getModel();
	    
	    $params = $this->getRequest()->getParams();
        $this->setPayboxResponse($params);
        
	    // Vérification des adresses IP des serveurs Paybox
	    $unauthorized_server = false;
	    
	    if ($model->getConfigData('pbx_check_ip')) {
	        $authorized_ips = $model->getAuthorizedIps();
    		$unauthorized_server = true;
    		foreach ($authorized_ips as $authorized_ip) {
    			if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && $_SERVER['HTTP_X_FORWARDED_FOR'] == $authorized_ip) {
    				$unauthorized_server = false;
    				break;
    			}	
    			if ($_SERVER['REMOTE_ADDR'] == $authorized_ip) {
    				$unauthorized_server = false;
    				break;
    			}
    		}
	    }

        if (!$unauthorized_server) {
    		try {
    			$order = Mage::getModel('sales/order');
                $order->loadByIncrementId($this->_payboxResponse['ref']);
    
                if (!$order->getId()) {
                    Mage::throwException($this->__('There are no order.'));
                }
    
                if ((int)($order->getBaseGrandTotal()*100) != (int)$this->_payboxResponse['amount']) {
                    Mage::throwException($this->__('Amount is not match.'));
                }
    
                if ($this->_payboxResponse['error'] == '00000') {
                    // Aucune erreur = paiement paybox accepté
                    if ($order->getState() == Mage_Sales_Model_Order::STATE_HOLDED) {
    					$order->unhold();
    				}
                    
                    if ($model->getConfigData('order_status_payment_accepted') == Mage_Sales_Model_Order::STATE_PROCESSING) {
                        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, $model->getConfigData('order_status_payment_accepted'), $this->__('Payment accepted by Paybox'));
                    } else {
                        $order->addStatusToHistory(
        					$model->getConfigData('order_status_payment_accepted'),
        					$this->__('Payment accepted by Paybox'),
        					true
        				);
    				}

                    if ($order->getPayment()->getMethodInstance()->getPaymentAction() == Mage_Paybox_Model_System::PBX_PAYMENT_ACTION_ATHORIZE_CAPTURE) {
                        $order->getPayment()
                            ->getMethodInstance()
                            ->setTransactionId($this->_payboxResponse['trans']);
                        
                        // Faut-il créer la facture
                        if ($model->getConfigData('invoice_create')) {
                            if ($this->_createInvoice($order)) {
                                $order->addStatusToHistory($order->getStatus(), $this->__('Invoice was create successfully'));
                            } else {
                                $order->addStatusToHistory($order->getStatus(), $this->__('Cann\'t create invoice'));
                            }
                        }
                    }
    
                    $session = $this->getCheckout();
                    $session->setQuoteId($session->getPayboxQuoteId(true));
                    $session->getQuote()->setIsActive(false)->save();
                    $session->unsPayboxQuoteId();
    
                 	$order->sendNewOrderEmail();
                	$order->setEmailSent(true);
                	$order->save();
    
                    // On exit car en cas de réponse valide une page blanche doit être retournée
                    exit();
    
                } else {
                    // Si le client a déjà payé on ne fait aucun traitement
                    if ($order->getStatus() == $model->getConfigData('order_status_payment_accepted') && $session->getQuote()->getIsActive() == false) {
                        $order->addStatusToHistory($order->getStatus(), $this->__('Attempt to return to Paybox, action ignored'));
                        exit();
                    }
                    
                    // Erreur = paiement paybox refusé
                    $messageError = $this->__('Customer was rejected by Paybox');
                    if (array_key_exists('error', $this->_payboxResponse)) {
                    	$messageError .= ' - Code Erreur : '.$this->_payboxResponse['error'];
                    }
                    
                    $order->addStatusToHistory(
    					$model->getConfigData('order_status_payment_refused'),
    					$messageError
    				);
    
    				if ($model->getConfigData('order_status_payment_refused') == Mage_Sales_Model_Order::STATE_CANCELED && $order->canCancel()) {
    					$order->cancel();
    				} else if ($model->getConfigData('order_status_payment_refused') == Mage_Sales_Model_Order::STATE_HOLDED && $order->canHold()) {
    					$order->hold();
    				}
    				
                    $order->save();
                }
    
            }
            catch( Exception $e ) {
    			$order->addStatusToHistory($order->getStatus(), $this->__('Error in order validation %s', $e->getMessage()))
    				->save();
    			$this->loadLayout();
    			$this->renderLayout();
    		}
        } else {
            $order = Mage::getModel('sales/order')->loadByIncrementId($this->_payboxResponse['ref']);
            $order->addStatusToHistory($order->getStatus(), $this->__('Error bad IP : %s', $_SERVER['REMOTE_ADDR']))
				->save();
        }

		$this->loadLayout();
        $this->renderLayout();
    }

}
