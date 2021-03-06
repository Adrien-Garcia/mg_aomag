<?php

class Addonline_Varnish_Block_Core_Messages extends Mage_Core_Block_Messages {

    public function _prepareLayout()
    {
        if (!Mage::registry('varnish_static')) {
            parent::_prepareLayout();
        }
    }
    
    public function _toHtml(){
        if (Mage::registry('varnish_static')) {
            //id=BlockAlias pour pouvoir le sélectionner en javascript (sans . dans le nom), par contre on met rel=NameInLayout pour pouvoir le sélectionner dans la layout (avec . dans le nom)
            $html = '<div id="'.($this->getBlockAlias()).'" class="varnish_placeholder" rel="'.($this->getNameInLayout()).'" >'.'</div>';
        } else {
            $html = parent::_toHtml();
        }
        return $html;
    }
    
}