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
 * @category   Addonline
 * @package    Mage_Protx
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * Atos Configuration Model
 *
 * @category   Addonline
 * @package    Addonline_SprintSecure
 * @name       Addonline_SprintSecure_Model_Config
**/
class Addonline_SprintSecure_Model_Config extends Varien_Object
{
    /**
     * R�cup�re un tableau des devises autoris�es
     *
     * @return array
     */
    public function getCurrencies()
    {
        $currencies = array();
        foreach (Mage::getConfig()->getNode('global/payment/sprintsecure/currencies')->asArray() as $data) 
		{
            $currencies[$data['iso']] = $data['code'];
        }
		
        return $currencies;
    }

    /**
     * R�cup�re un tableau des langages autoris�es
     *
     * @return array
     */
    public function getLanguages()
	{
        $languages = array();
        foreach (Mage::getConfig()->getNode('global/payment/sprintsecure/languages')->asArray() as $data) 
		{
            $languages[$data['code']] = $data['name'];
        }
		
        return $languages;
	}

    /**
     * R�cup�re un tableau des cartes de cr�dit autoris�es
     *
     * @return array
     */
	public function getCreditCardTypes()
    {
        $types = array();

        foreach (Mage::getConfig()->getNode('global/payment/sprintsecure/credit_card')->asArray() as $data) {
            $types[$data['code']] = $data['name'];
        }
		
        return $types;
    }
	
    /**
     * R�cup�re un tableau des IP des serveurs Atos
     *
     * @return array
     */
    public function getAuthorizedIps()
    {
        $config = Mage::getConfig()->getNode('global/payment/sprintsecure/authorized_ip/value')->asArray();
        $authorizedIp = explode(',', $config);
		
        return $authorizedIp;
    }
    
    /**
     * R�cup�re un tableau des mots cl�s du champ data 
     *
     * @return array
     */
	public function getDataFieldKeys()
    {
        $types = array();
        foreach (Mage::getConfig()->getNode('global/payment/sprintsecure/data_field')->asArray() as $data) 
		{
            $types[$data['code']] = $data['name'];
        }
		
        return $types;
    }
}