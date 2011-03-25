<?php
class Addonline_Brand_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/brand?id=15 
    	 *  or
    	 * http://site.com/brand/id/15 	
    	 */
    	/* 
		$brand_id = $this->getRequest()->getParam('id');

  		if($brand_id != null && $brand_id != '')	{
			$brand = Mage::getModel('brand/brand')->load($brand_id)->getData();
		} else {
			$brand = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($brand == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$brandTable = $resource->getTableName('brand');
			
			$select = $read->select()
			   ->from($brandTable,array('brand_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$brand = $read->fetchRow($select);
		}
		Mage::register('brand', $brand);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}