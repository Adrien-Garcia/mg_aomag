<?php
class Addonline_Brand_Model_Product_Attribute_Source_Brand extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('brand/brand_collection')
                ->load()
                ->toOptionArray();
            array_unshift($this->_options, array('value'=>'', 'label'=>Mage::helper('catalog')->__('Please select a brand ...')));
        }
        return $this->_options;
    }
    
    public function getFlatColums() {
        $columns = array();
        $attributeCode = $this->getAttribute()->getAttributeCode();

        if (Mage::helper('core')->useDbCompatibleMode()) {
            $columns[$attributeCode] = array(
                'type'      => 'int',
                'unsigned'  => false,
                'is_null'   => true,
                'default'   => null,
                'extra'     => null
            );
            $columns[$attributeCode . '_value'] = array(
                'type'      => 'varchar(255)',
                'unsigned'  => false,
                'is_null'   => true,
                'default'   => null,
                'extra'     => null
            );
        } else {            $columns[$attributeCode] = array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'length'    => null,
                'unsigned'  => false,
                'nullable'   => true,
                'default'   => null,
                'extra'     => null,
                'comment'   => $attributeCode . ' column'
            );
            $columns[$attributeCode . '_value'] = array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'unsigned'  => false,
                'nullable'  => true,
                'default'   => null,
                'extra'     => null,
                'comment'   => $attributeCode . ' column'
            );
        }

        return $columns;
    }
    
   /**
     * Retrieve Select for update Flat data
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param int $store
     * @param bool $hasValueField flag which require option value
     * @return Varien_Db_Select
     */
    public function getFlatUpdateSelect($store) {

         return Mage::getResourceModel('brand/brand')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
         
    }
    
    public function getFlatIndexes() {
    	$indexes = array();
    	
    	$index = sprintf('IDX_%s', strtoupper($this->getAttribute()->getAttributeCode()));
    	$indexes[$index] = array(
    			'type'      => 'index',
    			'fields'    => array($this->getAttribute()->getAttributeCode())
    	);
    	
    	$sortable   = $this->getAttribute()->getUsedForSortBy();
    	if ($sortable) {
    		$index = sprintf('IDX_%s_VALUE', strtoupper($this->getAttribute()->getAttributeCode()));
    	
    		$indexes[$index] = array(
    				'type'      => 'index',
    				'fields'    => array($this->getAttribute()->getAttributeCode() . '_value')
    		);
    	}
    	
    	return $indexes;
    	 
    }
}
