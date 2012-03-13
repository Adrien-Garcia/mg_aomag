<?php
/**
 * Form Block
 *
 * @category   Addonline
 * @package    Addonline_SprintSecure
 * @name       Addonline_SprintSecure_Block_SprintSecure_Form
 */
class Addonline_SprintSecure_Block_Sprintsecure_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('sprintsecure/form/sprintsecure.phtml');
        parent::_construct();
    }

}