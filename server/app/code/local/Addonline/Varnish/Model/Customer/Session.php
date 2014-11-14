<?php

/**
 * Customer session model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Addonline_Varnish_Model_Customer_Session extends Mage_Customer_Model_Session
{
  
    public function isLoggedIn()
    {
        /*
         * Les pages statiques qui sont mises en cache doivent correspondre au cas d'un internaute non loggé
         */
        return parent::isLoggedIn() && !Mage::registry('varnish_static');
    }

}
