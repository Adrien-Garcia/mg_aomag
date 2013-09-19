<?php

class Centerax_DeveloperTools_Block_Adminhtml_System_Sql extends Mage_Adminhtml_Block_Template
{

	public function getLogs()
	{
		return Mage::registry('found_reports');
	}

	public function getResult()
	{
		$table = new Zend_Text_Table(array('columnWidths' => array(10, 20)));
		$table->appendRow(array('Zend', 'Framework'));

		return $table;
	}
}