<?php
/**
 * @category   Addonline
 * @package    Addonline_Sponsorship
 * @author     Addonline
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Addonline_Sponsorship_Model_Changestatut extends Varien_Object
{
    const STATUS_WAITING	= 'waiting';
    const STATUS_EXPORTED	= 'exported';
    const STATUS_SOLVED		= 'solved';
    const STATUS_CANCELED	= 'canceled';

    static public function getOptionArray()
    {
        return array(
            self::STATUS_WAITING    => Mage::helper('sponsorship')->__('Waiting'),
            self::STATUS_EXPORTED   => Mage::helper('sponsorship')->__('Exported'),
            self::STATUS_SOLVED     => Mage::helper('sponsorship')->__('Solved'),
            self::STATUS_CANCELED   => Mage::helper('sponsorship')->__('Canceled')
        );
    }
}