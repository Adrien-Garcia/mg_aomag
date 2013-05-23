<?php

class Addonline_SoColissimo_Adminhtml_BatchController extends Mage_Adminhtml_Controller_action
{

	public function indexAction()
	{
 			$this->loadLayout();
 			$block = $this->getLayout()->createBlock('core/text');
 			
 			$log = Mage::getSingleton('socolissimo/liberte_batch')->run("");
 			
 			$block->setText($log);

 			$this->_addContent($block);
 			$this->renderLayout();
	}
	
	
}