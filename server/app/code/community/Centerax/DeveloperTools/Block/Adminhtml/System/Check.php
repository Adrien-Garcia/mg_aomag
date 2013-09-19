<?php

class Centerax_DeveloperTools_Block_Adminhtml_System_Check extends Mage_Adminhtml_Block_Template
{

	public function getResult()
	{
		return Mage::registry('sys_check');
	}

}