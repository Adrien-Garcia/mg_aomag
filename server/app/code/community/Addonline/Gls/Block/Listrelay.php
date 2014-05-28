<?php
class Addonline_Gls_Block_Listrelay extends Mage_Core_Block_Template
{
	private $_listRelay = array();

	public function getListRelay() {
		return $this->_listRelay;
	}

	public function setListRelay($list) {
		$this->_listRelay = $list;
	}

	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('gls/listrelais.phtml');
	}

}