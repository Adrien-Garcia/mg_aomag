<?php

/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Addonline_CategoryNavigation_Block_Page_Html_Breadcrumbs extends Mage_Page_Block_Html_Breadcrumbs
{

    function __construct()
    {
        parent::__construct();
        $this->setTemplate('category_navigation/breadcrumbs.phtml');
    }

}
