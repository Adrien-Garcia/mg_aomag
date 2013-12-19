<?php
/**
 * Addonline_GLS
 *
 * @category    Addonline
 * @package     Addonline_GLS
 * @copyright   Copyright (c) 2013 GLS
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_Gls_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isOnlyXLRelay() {
		return Mage::getStoreConfig('carriers/gls/relay_xl_only');
	}

	public function getExportFolder(){
		return Mage::getStoreConfig('carriers/gls/export_folder');
	}

}
