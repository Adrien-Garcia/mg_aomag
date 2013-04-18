<?php
/**
 * Addonline_SoColissimo
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2013 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */

/**
 * Used in creating options for Socolissimo Option Internationnal selection
*
*/
class Addonline_SoColissimo_Model_Adminhtml_System_Config_Source_Domicilesignature
{

	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{

		$options = array();
		$options[] = array('value' => '0', 'label'=>Mage::helper('socolissimo')->__('Sans Signature'));
		$options[] = array('value' => '1', 'label'=>Mage::helper('socolissimo')->__('Avec Signature'));
		return $options;
		
	}


}
