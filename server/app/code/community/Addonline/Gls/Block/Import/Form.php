<?php
/**
 * GLS
 *
 * @category    Addonline
 * @package     Addonline_GLS
 * @copyright   Copyright (c) 2013 GLS
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_Gls_Block_Import_Form extends Mage_Adminhtml_Block_Widget
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('gls/import/form.phtml');
    }

}
