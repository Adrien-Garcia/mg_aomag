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
 * @package     Mage_SalesRule
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Addonline_GiftProduct_Model_SalesRule_Quote_Discount extends Mage_SalesRule_Model_Quote_Discount
{

    /**
     * Collect address discount amount
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_SalesRule_Model_Quote_Discount
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
    	//parent::collect($address);
        //on remplace parent::collect par ce que fait Mage_Sales_Model_Quote_Address_Total_Abstract::collect
    	$this->_setAddress($address);
        /**
         * Reset amounts
         */
        $this->_setAmount(0);
        $this->_setBaseAmount(0);
        // end //
        
        $quote = $address->getQuote();
        $store = Mage::app()->getStore($quote->getStoreId());
        $this->_calculator->reset($address);

        $items = $this->_getAddressItems($address);
        if (!count($items)) {
        	$address->setDiscountAmount(0);
        	$address->setSubtotalWithDiscount(0);
        	$address->setBaseDiscountAmount(0);
        	$address->setBaseSubtotalWithDiscount(0);
            return $this;
        }

        $eventArgs = array(
            'website_id'        => $store->getWebsiteId(),
            'customer_group_id' => $quote->getCustomerGroupId(),
            'coupon_code'       => $quote->getCouponCode(),
        );

        $this->_calculator->init($store->getWebsiteId(), $quote->getCustomerGroupId(), $quote->getCouponCode());
        $this->_calculator->initTotals($items, $address);

        $address->setDiscountDescription(array());

        foreach ($items as $item) {
            if ($item->getNoDiscount()) {
                $item->setDiscountAmount(0);
                $item->setBaseDiscountAmount(0);
            }
            else {
                /**
                 * Child item discount we calculate for parent
                 */
                if ($item->getParentItemId()) {
                    continue;
                }

                $eventArgs['item'] = $item;
                Mage::dispatchEvent('sales_quote_address_discount_item', $eventArgs);

                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $this->_calculator->process($child);
                        $eventArgs['item'] = $child;
                        Mage::dispatchEvent('sales_quote_address_discount_item', $eventArgs);
                        $this->_aggregateItemDiscount($child);
                    }
                } else {
                    $this->_calculator->process($item);
                    $this->_aggregateItemDiscount($item);
                }
            }
        }
        
        /**
         *  Process gift product : add or remove in the cart  
         */
        $rules = Mage::getResourceModel('salesrule/rule_collection');
        $rules->setValidationFilter(Mage::app()->getStore($quote->getStoreId())->getWebsiteId(), $quote->getCustomerGroupId(), $quote->getCouponCode());
        $rules->getSelect()->where('simple_action=\''.Addonline_GiftProduct_Model_SalesRule_Rule::GIFT_PRODUCT_ACTION.'\'');
        $rules->load();
        $product_price = $product_price_tax = $tax_amount = "";
        $item_found = false;
        foreach ($rules as $rule) {
			$giftProduct = Mage::getModel('catalog/product')->load((int)$rule->getDiscountAmount());
			if ($giftProduct) {
				$giftQty=$rule->getDiscountQty()==0?1:$rule->getDiscountQty();
				$giftPrice=(float)$rule->getDiscountStep();
				$giftRequest=new Varien_Object(array('qty'=>$giftQty));
				$giftCandidates = $giftProduct->getTypeInstance(true)->prepareForCart($giftRequest, $giftProduct);
				$giftItem = $quote->getItemByProduct($giftProduct);
				$tax_amount = $product_price_tax - $product_price;
				if (in_array($rule->getId(), explode(',', $quote->getAppliedRuleIds()))) {
					$item_found = true;
					$product_price += $giftProduct->getPrice();
					$product_price_tax += Mage::helper("tax")->getPrice($giftProduct, $giftProduct->getFinalPrice());
					if (!$giftItem) {
						$giftItem = $quote->addProduct($giftProduct, $giftRequest);
						$item_found = false;
					}
					$giftItem->setPrice($giftPrice);
					$giftItem->setBasePrice($giftPrice);
					$giftItem->setCustomPrice($giftPrice);
					$giftItem->setRowTotal($giftPrice);
					$giftItem->setBaseRowTotal($giftPrice);
					$giftItem->setPriceInclTax($giftPrice);
					$giftItem->setTaxPercent($giftPrice);
					$giftItem->setTaxAmount($giftPrice);
					$giftItem->setBaseTaxAmount($giftPrice);
					$giftItem->setBasePriceInclTax($giftPrice);
					$giftItem->setRowTotalInclTax($giftPrice);
					$giftItem->setBaseRowTotalInclTax($giftPrice);
					$giftItem->setAdditionalData(Addonline_GiftProduct_Model_SalesRule_Rule::GIFT_PRODUCT_ACTION);
				} else {
					if ($giftItem && $giftItem->getAdditionalData() == "produit_cadeau") {
						$quote->removeItem($giftItem->getId());
					}
				}
			}
        } 
        
        /**
         * Process shipping amount discount
         */
        
        if($item_found) {
	        $address->setSubtotal(($address->getSubtotal() - $product_price));
	        $address->setBaseSubtotal($address->getSubtotal());
	        $address->setSubtotalInclTax($address->getSubtotalInclTax() - $product_price_tax);
	        $address->setShippingDiscountAmount(0);
	        $address->setBaseShippingDiscountAmount(0);
        } else {
        	//$address->setSubtotal($address->getSubtotal());
        	//$address->setBaseSubtotal($address->getSubtotal());
        	//$address->setSubtotalInclTax($address->getSubtotalInclTax());
        	$address->setShippingDiscountAmount(0);
        	$address->setBaseShippingDiscountAmount(0);
        }
        if ($address->getShippingAmount()) {
            $this->_calculator->processShippingAmount($address);
            $this->_addAmount(-$address->getShippingDiscountAmount());
            $this->_addBaseAmount(-$address->getBaseShippingDiscountAmount());
        }

        $this->_calculator->prepareDescription($address);
        return $this;
    }

}
