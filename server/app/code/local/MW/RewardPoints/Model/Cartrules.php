<?php
class MW_RewardPoints_Model_Cartrules extends Mage_Core_Model_Abstract
{
	protected $_conditions;
    protected $_actions;
    protected $_form;

    /**
     * Is model deleteable
     *
     * @var boolean
     */
    protected $_isDeleteable = true;

    /**
     * Is model readonly
     *
     * @var boolean
     */
    protected $_isReadonly = false;

    /**
     * Init resoirce
     *
     * @return unknown_type
     */
    protected function _construct()
    {
    	parent::_construct();
        $this->_init('rewardpoints/cartrules');
        $this->setIdFieldName('rule_id');
        
    }

    public function getConditionsInstance()
    {
        return Mage::getModel('salesrule/rule_condition_combine');
    }

    public function _resetConditions($conditions=null)
    {
        if (is_null($conditions)) {
            $conditions = $this->getConditionsInstance();
        }
        $conditions->setRule($this)->setId('1')->setPrefix('conditions');
        $this->setConditions($conditions);

        return $this;
    }

    public function setConditions($conditions)
    {
        $this->_conditions = $conditions;
        return $this;
    }

    /**
     * Retrieve Condition model
     *
     * @return Mage_SalesRule_Model_Rule_Condition_Abstract
     */

    public function getConditions()
    {
        if (empty($this->_conditions)) {
            $this->_resetConditions();
        }
        return $this->_conditions;
    }

    public function getActionsInstance()
    {
        //return Mage::getModel('rule/action_collection');
        return Mage::getModel('salesrule/rule_condition_product_combine');
    }

    public function _resetActions($actions=null)
    {
        if (is_null($actions)) {
            $actions = $this->getActionsInstance();
        }
        $actions->setRule($this)->setId('1')->setPrefix('actions');
        $this->setActions($actions);

        return $this;
    }

    public function setActions($actions)
    {
        $this->_actions = $actions;
        return $this;
    }

    public function getActions()
    {
        if (!$this->_actions) {
            $this->_resetActions();
        }
        return $this->_actions;
    }

    public function getForm()
    {
        if (!$this->_form) {
            $this->_form = new Varien_Data_Form();
        }
        return $this->_form;
    }
/*
    public function asString($format='')
    {
        $str = Mage::helper('rule')->__("Name: %s", $this->getName()) ."\n"
             . Mage::helper('rule')->__("Start at: %s", $this->getStartAt()) ."\n"
             . Mage::helper('rule')->__("Expire at: %s", $this->getExpireAt()) ."\n"
             . Mage::helper('rule')->__("Description: %s", $this->getDescription()) ."\n\n"
             . $this->getConditions()->asStringRecursive() ."\n\n"
             . $this->getActions()->asStringRecursive() ."\n\n";
        return $str;
    }

    public function asHtml()
    {
        $str = Mage::helper('rule')->__("Name: %s", $this->getName()) ."<br/>"
             . Mage::helper('rule')->__("Start at: %s", $this->getStartAt()) ."<br/>"
             . Mage::helper('rule')->__("Expire at: %s", $this->getExpireAt()) ."<br/>"
             . Mage::helper('rule')->__("Description: %s", $this->getDescription()) .'<br/>'
             . '<ul class="rule-conditions">'.$this->getConditions()->asHtmlRecursive().'</ul>'
             . '<ul class="rule-actions">'.$this->getActions()->asHtmlRecursive()."</ul>";
        return $str;
    }
*/
    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }
        if (isset($arr['actions'])) {
            $this->getActions()->setActions(array())->loadArray($arr['actions'][1], 'actions');
        }

        return $this;
    }

    protected function _convertFlatToRecursive(array $rule)
    {
        $arr = array();
        foreach ($rule as $key=>$value) {
            if (($key==='conditions' || $key==='actions') && is_array($value)) {
                foreach ($value as $id=>$data) {
                    $path = explode('--', $id);
                    $node =& $arr;
                    for ($i=0, $l=sizeof($path); $i<$l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = array();
                        }
                        $node =& $node[$key][$path[$i]];
                    }
                    foreach ($data as $k=>$v) {
                        $node[$k] = $v;
                    }
                }
            } else {
                /**
                 * convert dates into Zend_Date
                 */
                if (in_array($key, array('from_date', 'to_date')) && $value) {
                    $value = Mage::app()->getLocale()->date(
                        $value,
                        Varien_Date::DATE_INTERNAL_FORMAT,
                        null,
                        false
                    );
                }
                $this->setData($key, $value);
            }
        }
        return $arr;
    }

    /**
     * Returns rule as an array for admin interface
     *
     * Output example:
     * array(
     *   'name'=>'Example rule',
     *   'conditions'=>{condition_combine::asArray}
     *   'actions'=>{action_collection::asArray}
     * )
     *
     * @return array
     */
    public function asArray(array $arrAttributes = array())
    {
        $out = array(
            'name'=>$this->getName(),
            'start_at'=>$this->getStartAt(),
            'expire_at'=>$this->getExpireAt(),
            'description'=>$this->getDescription(),
            'conditions'=>$this->getConditions()->asArray(),
            'actions'=>$this->getActions()->asArray(),
        );

        return $out;
    }

    public function validate(Varien_Object $object)
    {
        return $this->getConditions()->validate($object);
    }
	public function getResourceCollection()
    {
        return Mage::getResourceModel('rewardpoints/cartrules_collection');
    }
    public function afterLoad()
    {
        $this->_afterLoad();
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        $conditionsArr = unserialize($this->getConditionsSerialized());
        if (!empty($conditionsArr) && is_array($conditionsArr)) {
            $this->getConditions()->loadArray($conditionsArr);
        }

        $actionsArr = unserialize($this->getActionsSerialized());
        if (!empty($actionsArr) && is_array($actionsArr)) {
            $this->getActions()->loadArray($actionsArr);
        }

//        $websiteIds = $this->_getData('website_ids');
//        if (is_string($websiteIds)) {
//            $this->setWebsiteIds(explode(',', $websiteIds));
//        }
//        $groupIds = $this->getCustomerGroupIds();
//        if (is_string($groupIds)) {
//            $this->setCustomerGroupIds(explode(',', $groupIds));
//        }
    }

    /**
     * Prepare data before saving
     *
     * @return Mage_Rule_Model_Rule
     */
    protected function _beforeSave()
    {
        // check if discount amount > 0
//        if ((int)$this->getDiscountAmount() < 0) {
//            Mage::throwException(Mage::helper('rule')->__('Invalid discount amount.'));
//        }


        if ($this->getConditions()) {
            $this->setConditionsSerialized(serialize($this->getConditions()->asArray()));
            $this->unsConditions();
        }
        if ($this->getActions()) {
            $this->setActionsSerialized(serialize($this->getActions()->asArray()));
            $this->unsActions();
        }

//        $this->_prepareWebsiteIds();
//
//        if (is_array($this->getCustomerGroupIds())) {
//            $this->setCustomerGroupIds(join(',', $this->getCustomerGroupIds()));
//        }
        parent::_beforeSave();
    }

    /**
     * Combain website ids to string
     *
     * @return Mage_Rule_Model_Rule
     */
//    protected function _prepareWebsiteIds()
//    {
//        if (is_array($this->getWebsiteIds())) {
//            $this->setWebsiteIds(join(',', $this->getWebsiteIds()));
//        }
//        return $this;
//    }

    /**
     * Check availabitlity to delete model
     *
     * @return boolean
     */
    public function isDeleteable()
    {
        return $this->_isDeleteable;
    }

    /**
     * Set is deleteable flag
     *
     * @param boolean $flag
     * @return Mage_Rule_Model_Rule
     */
    public function setIsDeleteable($flag)
    {
        $this->_isDeleteable = (bool) $flag;
        return $this;
    }


    /**
     * Checks model is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->_isReadonly;
    }

    /**
     * Set is readonly flag
     *
     * @param boolean $value
     * @return Mage_Rule_Model_Rule
     */
    public function setIsReadonly($value)
    {
        $this->_isReadonly = (boolean) $value;
        return $this;
    }
    
    /**
     * Validates data for rule
     * @param Varien_Object $object
     * @returns boolean|array - returns true if validation passed successfully. Array with error
     * description otherwise
//     */
//    public function validateData(Varien_Object $object)
//    {
//        if($object->getData('from_date') && $object->getData('to_date')){
//            $dateStartUnixTime = strtotime($object->getData('from_date'));
//            $dateEndUnixTime   = strtotime($object->getData('to_date'));
//
//            if ($dateEndUnixTime < $dateStartUnixTime) {
//                return array(Mage::helper('rule')->__("End Date should be greater than Start Date"));
//            }
//        }
//        return true;
//    }
    
}