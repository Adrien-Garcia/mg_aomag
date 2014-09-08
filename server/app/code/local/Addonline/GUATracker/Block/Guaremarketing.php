<?php
class Addonline_GUATracker_Block_Guaremarketing extends Mage_Core_Block_Template
{
    public $_order;

    public function getAccountId()
    {
        return Mage::getStoreConfig('google/addonline_google_remarketing/account_id');
    }

    public function isActive()
    {                        
        if(Mage::getStoreConfigFlag('google/addonline_google_remarketing/enable')){
                return true;
        }
        return false;
    }
    
    /**
     * Rendu du script js de remarketing
     * en fonction de là ou on est, de la valeur du panier, etc...
     * on va passer des parametres au script de Google
     * @return string
     */
    protected function getRemarketingTag()
    {
    
        //configuration globale
        $accountId = $this->getAccountId();
    
        // on veut le tableau des param pour Google Remarketing avec la valeur qu' on va passer
        $values = $this->getConversionGoogleTagParams();
    
        // langue du store ou on se trouve
        $lang = Mage::app()->getLocale()->getLocaleCode(); // return something like it_IT , fr_FR
        $tmp = explode("_", $lang);
        if(sizeof($tmp) >= 1) { $lang = $tmp[0]; }
    
    
        // on ecrit le 'output' qu'on va mettre dans le HTML
        // donc en 2 parties : une partie ou y a les param pour le script de G.
        // et ensuite le script en lui meme.
        $chaine = '
            <!-- BEGIN OF GOOGLE REMARKETING CODE -->
            <script type="text/javascript">
            var google_tag_params = {
            ';
    
        // on a recu un tab sous la forme [key] = value et on doit tous mettre (le nettoyage dans le tableau a été fait avant)
        // si value n'est pas numeric on va mettre des single quotes autour (pour le js)
        $nbvalue = count($values);
        $i = 0;
        // donc on se fait toutes les (k,v) du tab (et on ecrit dans le output "k = 'v',\n"
        foreach($values as $k=>$v) {
        $i++;
        // si c'est du texte ( = pas une suite de chiffre et pas un tableau = ca commence pas par [
        if(!is_numeric($v) && $v[0] != "[") {
        $v = "'" . trim($v) . "'";
        }
        $chaine .= $k . ": " . $v;
        if($i < $nbvalue) {
        $chaine .= ",";
        }
        $chaine .= "\n";
        };
    
        // fin des arguments pour le script... là on va mettre le script de G.
            $chaine .= '};
                    </script>
    
                    <script type="text/javascript">
                    /* <![CDATA[ */
                    var google_conversion_id        = '.$accountId.';
                    var google_conversion_language  = "'.$lang.'";
            var google_remarketing_only     = true;
            var google_custom_params        = window.google_tag_params;
                    /* ]]> */
            </script>
            <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
            </script>
            <!-- END OF GOOGLE REMARKETING CODE -->
            ';
    
            $datas = http_build_query($values);
            // la partie noscript est pas prise en charge .. pour l'instant
            $chaine .='
            <noscript>
            <div style="display:inline;">
                <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/'.$accountId.'/?guid=ON&amp;script=0&amp;data=' . $datas . '"/>
            </div>
            </noscript>
                    ';
    
    
            return $chaine;
    }
    
    /**
     * retourne un tableau des param qu'on doit passer au script de remarketing de Google sous la forme :
     * [<nom de a clé dans le script de g.>] = <valeur>
                    * @return multitype:boolean string NULL
                    */
    public function getConversionGoogleTagParams() {

        $retours = array();
        $retours['ecomm_prodid']        = false;
        $retours['ecomm_pagetype']      = 'other';  // on retourne tjrs l'info du type de page, par defaut on est sur 'other'
        $retours['ecomm_totalvalue']    = false;
        $retours['ecomm_category']      = false;
        $retours['ecomm_quantity']      = false;
    
        // est ce qu'on a trouvé notre page et on a rempli les infos ? (du coup bien noté la priorité : page produit, page categorie, et puis les autres à la fin)
        $isFilled = false;
    
        // en fonction de la page sur laquel on est on va re définir nos retours
    
        //on a product page?
        $product = Mage::registry('product');
        if (!$isFilled && $product && $product->getId()){
        $_helper = $this->helper('catalog/output');
        $retours['ecomm_prodid']        = $_helper->productAttribute($product, $product->getSku(), 'sku') . " "; // on ajoute un espace comme ca c'est une chaine (meme si y a que des chiffres dans le sku)                        
        $retours['ecomm_totalvalue']    = number_format($product->getSpecialPrice()?$product->getSpecialPrice():$product->getPrice(), 2);
        $retours['ecomm_pagetype']      =  'product';
        $isFilled = true;
        }
    
        //on a category page?
        $categorie = Mage::registry('current_category');
        if (!$isFilled && $categorie && $categorie->getId()){
        $retours['ecomm_pagetype']      =  'category';
        $retours['ecomm_category']      = $this->_format_name($categorie->getName());
        $isFilled = true;
            }

            // is homepage ?
            if(!$isFilled && Mage::getBlockSingleton('page/html_header')->getIsHomePage()) {
            $retours['ecomm_pagetype'] = 'home';
            $isFilled = true;
            }

            // dans les pages du check out, on va donner les infos sur le panier = les sku des articles, le montant et leur quantité
            if (!$isFilled && Mage::app()->getFrontController()->getAction()->getFullActionName() == 'checkout_cart_index') {
            // on recupere le caddie
            $_helper = $this->helper('catalog/output');
            $cart = Mage::getModel('checkout/cart')->getQuote();
            // on prepare nos vars pour avoir les infos sur le caddie
                $value = 0;
                $productNames   = array();
                        $productQtys    = array();

                        // on boucle sur le caddie et on se construit des tableaux, des vars avec les infos dont on aura bessoin
                        foreach ($cart->getAllItems() as $item) {
                        $productNames[] = '"' . $_helper->productAttribute( $item->getProduct(),  $item->getProduct()->getSku(), 'sku') . '"';
                        $productQtys[]  = $item->getQty();
                                                                        
                        $value += ($item->getProduct()->getSpecialPrice()?$item->getProduct()->getSpecialPrice():$item->getProduct()->getPrice() * $item->getQty());
                        }

                        // donc on se fait un tableau des produits du caddie, la qte de chacun d'eux, la valeur du caddie et en fin bin c'est une page caddie
                        $retours['ecomm_prodid']        = "[" . implode(',', $productNames) . "]";
                        $retours['ecomm_quantity']      = "[" . implode(',', $productQtys) . "]";
                                $retours['ecomm_totalvalue']    = number_format($value, 2);
                        $retours['ecomm_pagetype']      = 'cart';
                        $isFilled = true;

            }

            // on ne garde dans retours[] que ce qui a qq chose comme valeur
            foreach($retours as $k=>$v) {
            if($v === false) {
                unset($retours[$k]);
                }
                }

                return $retours;
    
    
        }
    
        /**
        * on passe en minuscule , on vire les simple/double quote, comme ca on a une chaine de caracs pretes pour passer dans du js
        * @param unknown $t
        * @return unknown
        */
        private function _format_name($t)
        {

            //         $t = htmlentities($t, ENT_NOQUOTES, $charset);

            //         $t = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $t);
            //         $t = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $t); // pour les ligatures e.g. '&oelig;'
            //         $t = preg_replace('#&[^;]+;#', '', $t); // supprime les autres caractères

            $t = mb_strtolower ($t, "UTF-8");
            $t = str_replace(' ', '-', $t);
            $t = str_replace('\'', '-', $t);
            $t = str_replace('"', '-', $t);
            $t = str_replace('--', '-', $t);
    
            return $t;
    
        }

}