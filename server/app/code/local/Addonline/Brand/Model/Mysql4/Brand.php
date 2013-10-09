<?php

class Addonline_Brand_Model_Mysql4_Brand extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the brand_id refers to the key field in your database table.
        $this->_init('brand/brand', 'brand_id');
    }
    

    /**
     * Retrieve Select for update Flat data
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param int $store
     * @param bool $hasValueField flag which require option value
     * @return Varien_Db_Select
     */
    public function getFlatUpdateSelect(Mage_Eav_Model_Entity_Attribute_Abstract $attribute, $store,
    		$hasValueField = true
    ) {
    	$adapter        = $this->_getReadAdapter();
    	$attributeTable = $attribute->getBackend()->getTable();
    	$attributeCode  = $attribute->getAttributeCode();
    
    	$joinConditionTemplate = "%s.entity_id = %s.entity_id"
    			. " AND %s.entity_type_id = " . $attribute->getEntityTypeId()
    			. " AND %s.attribute_id = " . $attribute->getId()
    			. " AND %s.store_id = %d";
    	$joinCondition = sprintf($joinConditionTemplate, 'e', 't1', 't1', 't1', 't1',
    			Mage_Core_Model_App::ADMIN_STORE_ID);
    	if ($attribute->getFlatAddChildData()) {
    		$joinCondition .= ' AND e.child_id = t1.entity_id';
    	}
    
    	$valueExpr = $adapter->getCheckSql('t2.value_id > 0', 't2.value', 't1.value');
    	/** @var $select Varien_Db_Select */
    	$select = $adapter->select()
    	->joinLeft(array('t1' => $attributeTable), $joinCondition, array())
    	->joinLeft(array('t2' => $attributeTable),
    			sprintf($joinConditionTemplate, 't1', 't2', 't2', 't2', 't2', $store),
    			array($attributeCode => $valueExpr));
    
    	if ($hasValueField) {
    		$valueIdExpr = 'to1.nom';
    		$select
    		->joinLeft(array('to1' => $this->getTable('brand/brand')),
    				"to1.brand_id = {$valueExpr}", array($attributeCode . '_value' => $valueIdExpr));
    	}
    
    	if ($attribute->getFlatAddChildData()) {
    		$select->where('e.is_child = 0');
    	}
    
    	return $select;
    }
    
}