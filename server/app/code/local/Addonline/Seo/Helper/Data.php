<?php

class Addonline_Seo_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isVersionLessThan($ver)
    {
        $magento_version = explode('.', Mage::getVersion());
        $requested_version = explode('.', $ver);
        
        foreach($requested_version as $a => $v)
        {
            if( $magento_version[$a]<$v )
            {
                return true;
            }
        }
        return false;
    }
}
