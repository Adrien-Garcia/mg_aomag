<?php
/**
 * Redirect to Franfinance
 *
 * @category   Addonline
 * @package    Addonline_SprintSecure
 * @name       Addonline_SprintSecure_Block_SprintSecure_Redirect
 */
class Addonline_SprintSecure_Block_Sprintsecure_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $sprintsecure = Mage::getModel('sprintsecure/method_sprintsecure');
		$sprintsecure->callRequest();
		
        if ($sprintsecure->getSystemError()) {
		    return $sprintsecure->getSystemMessage();
		}
		
		$html = '';

		$html.= '<style type="text/css">'."\n";
		$html.= '  @import url("'.$this->getSkinUrl('css/stylesheet.css').'");'."\n";
		$html.= '  @import url("'.$this->getSkinUrl('css/checkout.css').'");'."\n";
		$html.= '</style>'."\n";

		//Changer l'id du formulaire
		$html.= '<div id="sprintsecureButtons" style="display: none;">'."\n";
		$html.= '  <p class="center">'.$this->__('You have to pay to validate your order').'</p>'."\n";
		$html.= '  <form id="sprintsecure_payment_checkout" action="'.$sprintsecure->getSystemUrl().'" method="post">'."\n";
		$html .= '<input type="hidden" name="SOLUTIONSPRINTSECURE_x" value="1" />';
		$html .= '<input type="hidden" name="SOLUTIONSPRINTSECURE_y" value="1" />';
		$html.= $sprintsecure->getSystemMessage()."\n";
		$html.= '  </form>'."\n";
		$html.= '</div>'."\n";
		$html.= '<script type="text/javascript">document.getElementById("sprintsecure_payment_checkout").submit();</script>';
		
        return $html;
    }
}