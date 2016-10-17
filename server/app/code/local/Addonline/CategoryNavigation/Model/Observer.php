<?php

/**
 * CategoryNavigation Observer
 *
 * @category   Addonline
 * @package    Addonline_CategoryNavigation
 * @author     addonline
 */
class Addonline_CategoryNavigation_Model_Observer
{

    public function categoryFlatLoadNodesBefore(Varien_Event_Observer $observer)
    {
        //on ajoute la colonne navigation_type au select de chargement des catégories du menu
        $select = $observer->getEvent()->getSelect();
        $select->columns('navigation_type');
        $select->columns('thumbnail');
        $select->columns('image');
        $select->columns('page_cms');
        
    }
}
