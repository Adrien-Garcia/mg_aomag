<?php

/**
 * Class Fabien_Admintheme_Controller_Observer
 */
class Fabien_Admintheme_Controller_Observer
{

    /**
     * overrideTheme
     *
     * @throws Mage_Core_Exception
     */
    public function overrideTheme()
    {
        Mage::getDesign()->setArea( 'adminhtml' )->setTheme( ( string )( Mage::getStoreConfig( 'design/admin/theme' ) ) );
    }
}
