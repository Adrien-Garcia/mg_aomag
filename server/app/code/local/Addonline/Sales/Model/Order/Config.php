<?php
class Addonline_Sales_Model_Order_Config extends Mage_Sales_Model_Order_Config
{
	public function getStateStatuses($state, $addLabels = true)
	{
		//$key = $state . $addLabels;
		$key = is_array($state)?implode($state):$state . $addLabels;
		if (isset($this->_stateStatuses[$key])) {
			return $this->_stateStatuses[$key];
		}
		$statuses = array();
		if (empty($state) || !is_array($state)) {
			$state = array($state);
		}
		foreach ($state as $_state) {
			if ($stateNode = $this->_getState($_state)) {
				$collection = Mage::getResourceModel('sales/order_status_collection')
				->addStateFilter($_state)
				->orderByLabel();
				foreach ($collection as $status) {
					$code = $status->getStatus();
					if ($addLabels) {
						$statuses[$code] = $status->getStoreLabel();
					} else {
						$statuses[] = $code;
					}
				}
			}
		}
		$this->_stateStatuses[$key] = $statuses;
		return $statuses;
	}
}