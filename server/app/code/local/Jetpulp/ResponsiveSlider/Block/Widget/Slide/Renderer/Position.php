<?php

/**
 * Grid input column renderer
 *
 * @category   Jetpulp
 * @package    Jetpulp_Responsiveslider
 */

class Jetpulp_ResponsiveSlider_Block_Widget_Slide_Renderer_Position
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_values;

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $linkData = Mage::getModel('responsiveslider/responsiveslider_link')->getCollection()
                        ->addFieldToFilter('item_id', $row->getData('item_id'))
                        ->addFieldToFilter('responsiveslider_id', $this->getRequest()->getParam('responsiveslider_id'))
                        ->getData();
        $value = '';
        if( count($linkData) > 0 AND isset($linkData) ) {
            $value = $linkData[0]['sort_order'];
        }

        $html = '<input type="text" ';
        $html .= 'name="position[' . $row->getData('item_id') . ']" ';
        $html .= 'value="' . $value . '"';
        $html .= 'class="input-text ' . $this->getColumn()->getInlineCss() . '"/>';

        return $html;

    }
}
