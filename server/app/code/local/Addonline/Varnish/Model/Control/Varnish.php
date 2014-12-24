<?php
/**
 * Varnish page cache control model
 *
 * @category    Addonline
 * @package     Addonline_Varnish
 */
class Addonline_Varnish_Model_Control_Varnish implements Mage_PageCache_Model_Control_Interface
{
    /**
     * Clean varnish page cache
     *
     * @return void
     */
    public function clean()
    {
		Mage::helper('varnish')->purge(array('/.*'));
		//On marque comme lus les messages Varnish
		$_unreadNotices = Mage::getModel('adminnotification/inbox')->getCollection()->getItemsByColumnValue('is_read', 0);
		foreach($_unreadNotices as $notice) {
			if(strpos($notice->getData('description'),'Varnish')) {
				$notice->setIsRead(1)->save();
			}
		}
    }
}
