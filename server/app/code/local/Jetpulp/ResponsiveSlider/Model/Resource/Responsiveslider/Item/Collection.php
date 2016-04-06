<?php

class Jetpulp_ResponsiveSlider_Model_Resource_Responsiveslider_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    private $previousNextFlag = false;
    private $infinitLoopFlag = false;

    /**
     * Constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('responsiveslider/responsiveslider_item');
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * @param $bool
     */
    public function setPreviousNextFlag($bool)
    {
        $this->previousNextFlag = $bool;
    }

    /**
     * @param $bool
     */
    public function setInfinitLoopFlag($bool)
    {
        $this->infinitLoopFlag = $bool;
    }

    /**
     *
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $previousItem = null;
        foreach ($this->_items as $item) {

            /*
             * Chargement eventuel du produit associé
             */
            $sku = $item->getData('product_sku');
            if(!empty($sku))
            {
                $product = Mage::getModel('catalog/product');
                $productId = $product->getIdBySku($sku);
                if($productId)
                {
                    $product->load($productId);
                    $item->setProduct($product);
                }
            }

            /*
             * Affectation de l'item suivant et précedent
             * (utile par exemple pour des pévisualisations d'étiquettes)
             */
            if ($this->previousNextFlag) {
                if ($previousItem) {
                    $previousItem->setNextItem($item);
                    $item->setPreviousItem($previousItem);
                }
                $previousItem = $item;
            }

        }

        /**
         * Affectation de l'item suivant et précedent
         * si on veux boucler indéfiniement
         */
        if ($this->previousNextFlag && $this->infinitLoopFlag) {

            if (count($this->_items)) {
                reset($this->_items);
                $firstItem = current($this->_items);
                $lastItem = end($this->_items);
                reset($this->_items);
                $firstItem->setPreviousItem($lastItem);
                $lastItem->setNextItem($firstItem);
            }
        }


    }

    /**
     * Add filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @param bool $withAdmin
     * @return Jetpulp_ResponsiveSlider_Model_Resource_Responsiveslider_Item_Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }

        if (!is_array($store)) {
            $store = array($store);
        }

        if ($withAdmin) {
            $store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
        }

        $this->addFilter('store', array('in' => $store), 'public');

        return $this;
    }

    /**
     * Join store relation table if there is store filter
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('responsiveslider/responsiveslider_item_store')),
                'main_table.item_id = store_table.item_id',
                array()
            )->group('main_table.item_id');

            /*
             * Allow analytic functions usage because of one field grouping
             */
            $this->_useAnalyticFunction = true;
        }
        return parent::_renderFiltersBefore();
    }
}