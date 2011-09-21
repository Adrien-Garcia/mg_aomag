<?php
/**
 * @category   Addonline
 * @package    Addonline_Sponsorship
 * @author     Addonline
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Addonline_Sponsorship_Block_Customer_Form_Boost extends Mage_Customer_Block_Account_Dashboard
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getInvit()
    {
        $param = $this->getRequest()->get("sponsorship_id");
        if ( $param )
        {
            $invit = mage::getModel("sponsorship/sponsorship")->load($param);
            if ($invit->getParentId() == Mage::getSingleton('customer/session')->getId())
            {
                return $invit;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    
    public function getParentEmail()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer->getEmail();
    }
}