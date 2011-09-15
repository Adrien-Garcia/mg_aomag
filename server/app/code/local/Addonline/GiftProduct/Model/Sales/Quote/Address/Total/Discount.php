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
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Addonline_GiftProduct_Model_Sales_Quote_Address_Total_Discount extends Mage_Sales_Model_Quote_Address_Total_Discount
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $quote = $address->getQuote();
        $eventArgs = array(
            'website_id'=>Mage::app()->getStore($quote->getStoreId())->getWebsiteId(),
            'customer_group_id'=>$quote->getCustomerGroupId(),
            'coupon_code'=>$quote->getCouponCode(),
        );

        $address->setFreeShipping(0);
        $totalDiscountAmount = 0;
        $subtotalWithDiscount= 0;
        $baseTotalDiscountAmount = 0;
        $baseSubtotalWithDiscount= 0;
        $address->setAppliedRuleIds('');
        $quote->setAppliedRuleIds('');
        
        $items = $address->getAllItems();
        if (!count($items)) {
            $address->setDiscountAmount($totalDiscountAmount);
            $address->setSubtotalWithDiscount($subtotalWithDiscount);
            $address->setBaseDiscountAmount($baseTotalDiscountAmount);
            $address->setBaseSubtotalWithDiscount($baseSubtotalWithDiscount);
            return $this;
        }

        $hasDiscount = false;
        foreach ($items as $item) {
            if ($item->getNoDiscount()) {
                $item->setDiscountAmount(0);
                $item->setBaseDiscountAmount(0);
                $item->setRowTotalWithDiscount($item->getRowTotal());
                $item->setBaseRowTotalWithDiscount($item->getRowTotal());

                $subtotalWithDiscount+=$item->getRowTotal();
                $baseSubtotalWithDiscount+=$item->getBaseRowTotal();
            }
            else {
                /**
                 * Child item discount we calculate for parent
                 */
                if ($item->getParentItemId()) {
                    continue;
                }

                /**
                 * Composite item discount calculation
                 */

                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $eventArgs['item'] = $child;
                        Mage::dispatchEvent('sales_quote_address_discount_item', $eventArgs);

                        if ($child->getDiscountAmount() || $child->getFreeShipping()) {
                            $hasDiscount = true;
                        }

                        /**
                         * Parent free shipping we apply to all children
                         */
                        if ($item->getFreeShipping()) {
                            $child->setFreeShipping($item->getFreeShipping());
                        }

                        /**
                         * @todo Parent discount we apply for all children without discount
                         */
                        if (!$child->getDiscountAmount() && $item->getDiscountPercent()) {

                        }
                        $totalDiscountAmount += $child->getDiscountAmount();//*$item->getQty();
                        $baseTotalDiscountAmount += $child->getBaseDiscountAmount();//*$item->getQty();

                        $child->setRowTotalWithDiscount($child->getRowTotal()-$child->getDiscountAmount());
                        $child->setBaseRowTotalWithDiscount($child->getBaseRowTotal()-$child->getBaseDiscountAmount());

                        $subtotalWithDiscount+=$child->getRowTotalWithDiscount();
                        $baseSubtotalWithDiscount+=$child->getBaseRowTotalWithDiscount();
                    }
                }
                else {
                    $eventArgs['item'] = $item;
                    Mage::dispatchEvent('sales_quote_address_discount_item', $eventArgs);

                    if ($item->getDiscountAmount() || $item->getFreeShipping()) {
                        $hasDiscount = true;
                    }
                    $totalDiscountAmount += $item->getDiscountAmount();
                    $baseTotalDiscountAmount += $item->getBaseDiscountAmount();

                    $item->setRowTotalWithDiscount($item->getRowTotal()-$item->getDiscountAmount());
                    $item->setBaseRowTotalWithDiscount($item->getBaseRowTotal()-$item->getBaseDiscountAmount());

                    $subtotalWithDiscount+=$item->getRowTotalWithDiscount();
                    $baseSubtotalWithDiscount+=$item->getBaseRowTotalWithDiscount();
                }
            }
        }
        $address->setDiscountAmount($totalDiscountAmount);
        $address->setSubtotalWithDiscount($subtotalWithDiscount);
        $address->setBaseDiscountAmount($baseTotalDiscountAmount);
        $address->setBaseSubtotalWithDiscount($baseSubtotalWithDiscount);

        $address->setGrandTotal($address->getGrandTotal() - $address->getDiscountAmount());
        $address->setBaseGrandTotal($address->getBaseGrandTotal()-$address->getBaseDiscountAmount());
        
        /* gift product : add or remove the gift product in the cart */
        $rules = Mage::getResourceModel('salesrule/rule_collection');
        $rules->setValidationFilter(Mage::app()->getStore($quote->getStoreId())->getWebsiteId(), $quote->getCustomerGroupId(), $quote->getCouponCode());
        $rules->getSelect()->where('simple_action=\''.Addonline_GiftProduct_Model_SalesRule_Rule::GIFT_PRODUCT_ACTION.'\'');
        $rules->load();
        foreach ($rules as $rule) { 
			$giftProduct = Mage::getModel('catalog/product')->load($rule->getDiscountAmount());
			Mage::log('discount ');
			if ($giftProduct) {
			Mage::log('discount ' .$giftProduct->getId());
				$giftQty=$rule->getDiscountQty()==0?1:$rule->getDiscountQty();
				$giftPrice=$rule->getDiscountStep();
				$giftRequest=new Varien_Object(array('qty'=>$giftQty));
				$giftCandidates = $giftProduct->getTypeInstance(true)->prepareForCart($giftRequest, $giftProduct);
				$giftItem = $quote->getItemByProduct($giftProduct);
				Mage::log($rule->getId());
				Mage::log( $quote->getAppliedRuleIds());
				Mage::log( $giftItem);
				if (in_array($rule->getId(), explode(',', $quote->getAppliedRuleIds()))) {
					if (!$giftItem) {
						$giftItem = $quote->addProduct($giftProduct, $giftRequest);
					}
					$giftItem->setCustomPrice($giftPrice);
					$giftItem->setAdditionalData(Addonline_GiftProduct_Model_SalesRule_Rule::GIFT_PRODUCT_ACTION);
				} else {
					if ($giftItem) {					
						$quote->removeItem($giftItem->getId());
					}
				}
			}
        }
        /* end gift product */
        return $this;
    }

}