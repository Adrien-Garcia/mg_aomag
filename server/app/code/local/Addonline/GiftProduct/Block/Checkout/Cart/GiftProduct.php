<?php

/**
 * Cart Gift Product
 *
 * @category   Addonline
 * @package    Addonline_Checkout
 * @author 	   spras
 */
class Addonline_GiftProduct_Block_Checkout_Cart_GiftProduct extends Mage_Core_Block_Template
{

	private $_rule;
	
	public function getGiftProductRule()
    {

    	$quote = Mage::getSingleton('checkout/cart')->getQuote();
    	if (!$this->_rule) {    
    		$rules = Mage::getResourceModel('salesrule/rule_collection');
	        $rules->setValidationFilter(Mage::app()->getStore($quote->getStoreId())->getWebsiteId(), $quote->getCustomerGroupId(), $quote->getCouponCode());
	        $rules->getSelect()->where('simple_action=\''.Addonline_GiftProduct_Model_SalesRule_Rule::GIFT_PRODUCT_ACTION.'\'');
	        $rules->load();
	        foreach ($rules as $rule) { 
				$giftProduct = Mage::getModel('catalog/product')->load($rule->getDiscountAmount());
	        	$giftQty=$rule->getDiscountQty()==0?1:$rule->getDiscountQty();
				$giftRequest=new Varien_Object(array('qty'=>$giftQty));
				$giftCandidates = $giftProduct->getTypeInstance(true)->prepareForCart($giftRequest, $giftProduct);
				$giftItem = $quote->getItemByProduct($giftProduct);
				if (!$giftItem) {
					$rule->setProduct($giftProduct);
		        	$this->_rule = $rule;
		        	continue;
				}
	        }
    	}
    	return $this->_rule;
    }

    
}
