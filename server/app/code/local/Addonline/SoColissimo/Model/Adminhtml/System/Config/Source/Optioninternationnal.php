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
class Addonline_SoColissimo_Model_Adminhtml_System_Config_Source_Optioninternationnal
{

	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{

		$options = array();
		$options[] = array('value' => '0', 'label'=>Mage::helper('socolissimo')->__('France uniquement'));
		$options[] = array('value' => '1', 'label'=>Mage::helper('socolissimo')->__('France et international'));
		$options[] = array('value' => '2', 'label'=>Mage::helper('socolissimo')->__('international uniquement'));
		return $options;
		
	}


}
