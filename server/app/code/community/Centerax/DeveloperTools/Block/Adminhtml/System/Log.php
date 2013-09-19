<?php

class Centerax_DeveloperTools_Block_Adminhtml_System_Log extends Mage_Adminhtml_Block_Template
{

	public function getLogs()
	{
		return Mage::registry('found_logs');
	}

	public function getLogDropdown()
	{
		$sl = $this->getLayout()->createBlock('core/html_select')
            ->setId('file_inspector_dl')
            ->setClass('file_inspector_dl')
            //->setExtraParams('"onchange="Devtools.Log.changeFile();""')
            ->setName('options')->addOption('-', '-- Please select --');
		foreach($this->getlogs() as $_p){
			$sl->addOption($_p['file'], $_p['filename']);
		}
		return $sl->toHtml();
	}

}