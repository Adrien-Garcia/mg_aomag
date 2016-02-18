<?php

class Jetpulp_ResponsiveSlider_Model_Resource_Responsiveslider_Item extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        // Note that the id refers to the key field in your database table.
        $this->_init('responsiveslider/responsiveslider_item', 'item_id');
    }

    /**
     * Process block data before deleting
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Jetpulp_ResponsiveSlider_Model_Resource_Responsiveslider
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'item_id = ?'     => (int) $object->getId(),
        );

        $this->_getWriteAdapter()->delete($this->getTable('responsiveslider/responsiveslider_item_store'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Process slide data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Jetpulp_ResponsiveSlider_Model_Resource_Responsiveslider_Item
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {

        // modify create / update dates
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * Perform operations after object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Jetpulp_ResponsiveSlider_Model_Resource_Responsiveslider
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();

        $table  = $this->getTable('responsiveslider/responsiveslider_item_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = array(
                'item_id = ?'     => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                    'item_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);

    }

    /**
     * Perform operations after object load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Jetpulp_ResponsiveSlider_Model_Resource_Responsiveslider_Item
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
            $object->setData('stores', $stores);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Jetpulp_ResponsiveSlider_Model_Resource_Responsiveslider_Item $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $stores = array(
                (int) $object->getStoreId(),
                Mage_Core_Model_App::ADMIN_STORE_ID,
            );

            $select->join(
                array('cbs' => $this->getTable('responsiveslider/responsiveslider_item_store')),
                $this->getMainTable().'.item_id = cbs.item_id',
                array('store_id')
            )->where('is_active = ?', 1)
                ->where('cbs.store_id in (?) ', $stores)
                ->order('store_id DESC')
                ->limit(1);
        }

        return $select;
    }


    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('responsiveslider/responsiveslider_item_store'), 'store_id')
            ->where('item_id = :item_id');

        $binds = array(
            ':item_id' => (int) $id
        );

        return $adapter->fetchCol($select, $binds);
    }
}