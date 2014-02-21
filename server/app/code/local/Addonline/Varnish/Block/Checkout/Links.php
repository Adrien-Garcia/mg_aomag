<?php

/**
 * Links block
 *
 * @category    Addonline
 * @package     Addonline_Varnish
 */
if ((string)Mage::getConfig()->getModuleConfig('Idev_OneStepCheckout')->active != 'true')
{
	class Idev_OneStepCheckout_Block_Checkout_Links extends Mage_Checkout_Block_Links{}
}
class Addonline_Varnish_Block_Checkout_Links extends Idev_OneStepCheckout_Block_Checkout_Links
{
    /**
	 * Set the original module name to avoid breaking translations
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setModuleName('Mage_Checkout');
	}

	/**
     * Add shopping cart link to parent block
     * 
     * On génère le text différement si on affiche le bloc de manière dynamique ou non
     *
     * @return Mage_Checkout_Block_Links
     */
    public function addCartLink()
    {
    	$parentBlock = $this->getParentBlock();
        if ($parentBlock && Mage::helper('core')->isModuleOutputEnabled('Mage_Checkout')) {
        	
        	if (Mage::registry('varnish_static')) {
        		$text = $this->__('My Cart');
        	} else {
        		$count = $this->getSummaryQty() ? $this->getSummaryQty()
	                : $this->helper('checkout/cart')->getSummaryCount();
	            if ($count == 1) {
	                $text = $this->__('My Cart (%s item)', $count);
	            } elseif ($count > 0) {
	                $text = $this->__('My Cart (%s items)', $count);
	            } else {
	                $text = $this->__('My Cart');
	            }
        	}

            $parentBlock->removeLinkByUrl($this->getUrl('checkout/cart'));
            $parentBlock->addLink($text, 'checkout/cart', $text, true, array(), 50, null, 'class="top-link-cart"');
        }
        return $this;
    }

}
