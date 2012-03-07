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
 * @package     Mage_Ogone
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Config model
 */
class Addonline_OgoneDirectlink_Model_Config extends Mage_Payment_Model_Config
{
    const OGONE_PAYMENT_PATH = 'payment/ogonedirectlink/';

    /**
     * Return ogone payment config information
     *
     * @param string $path
     * @param int $storeId
     * @return Simple_Xml
     */
    public function getConfigData($path, $storeId=null)
    {
        if (!empty($path)) {
            return Mage::getStoreConfig(self::OGONE_PAYMENT_PATH . $path, $storeId);
        }
        return false;
    }

    /**
     * Return SHA1-in crypt key from config. Setup on admin place.
     *
     * @param int $storeId
     * @return string
     */
    public function getShaInCode($storeId=null)
    {
        return Mage::helper('core')->decrypt($this->getConfigData('secret_key_in', $storeId));
    }

    /**
     * Return gateway path, get from confing. Setup on admin place.
     *
     * @param int $storeId
     * @return string
     */
    public function getGatewayPath($storeId=null)
    {
        return $this->getConfigData('ogone_gateway', $storeId);
    }

    /**
     * Get PSPID, affiliation name in ogone system
     *
     * @param int $storeId
     * @return string
     */
    public function getPSPID($storeId=null)
    {
        return $this->getConfigData('pspid', $storeId);
    }

    /**
     * Get USERID, affiliation name in ogone system
     *
     * @param int $storeId
     * @return string
     */
    public function getUSERID($storeId=null)
    {
        return $this->getConfigData('userid', $storeId);
    }
    
    /**
     * Get PSWD, affiliation name in ogone system
     *
     * @param int $storeId
     * @return string
     */
    public function getPSWD($storeId=null)
    {
        return $this->getConfigData('pswd', $storeId);
    }

    /**
     * Return ogone order url
     *
     * @return string
     */
    public function getOgoneOrderDirectlinkUrl($storeId=null)
    {
        return  $this->getConfigData('ogone_gateway_order', $storeId);
    }
    
    /**
     * Return ogone maintenance url
     *
     * @return string
     */
    public function getOgoneMaintenanceDirectlinkUrl($storeId=null)
    {
        return  $this->getConfigData('ogone_gateway_maintenance', $storeId);
    }
    
    /**
     * Return ogone new hashing method
     *
     * @return string
     */
    public function getNewHashingMethod($storeId=null)
    {
        return  $this->getConfigData('hashing_method', $storeId);
    }
}
