<?php
class Addonline_Brand_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    public function listeAction() 
    {
   		$this->loadLayout();
   		$this->renderLayout();
    }
    
    public function marqueAction() 
    {
    	$this->loadLayout();
    	$this->renderLayout();
    }
}