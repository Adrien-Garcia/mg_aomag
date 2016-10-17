<?php

class Addonline_Magmi_Block_Magmi extends Mage_Core_Block_Template
{
    
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        return '<iframe src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'magmi/web/magmi.php" width="1000px" height="2000px" style="border:0;"></iframe>';
    }
}
