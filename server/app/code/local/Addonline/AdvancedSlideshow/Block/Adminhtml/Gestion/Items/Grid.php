<?php 

class Addonline_AdvancedSlideshow_Block_Adminhtml_Gestion_Items_Grid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_gestion_items_list';
		$this->_blockGroup = 'advancedslideshow';
		$this->_headerText = Mage::helper('advancedslideshow')->__('Slideshow') . ' '.Mage::registry('cur_slideshow_id').' : ' . Mage::helper('advancedslideshow')->__('Manage slides');
		$this->_addBackButton();
		$this->_addButtonLabel = Mage::helper('advancedslideshow')->__('Add Slide');
		$this->_addDeleteButton();
		parent::__construct();		
	}
	
    protected function _addBackButton()
    {
        $this->_addButton('back', array(
            'label'     => $this->getBackButtonLabel(),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/index/') .'\')',
            'class'     => 'back',
        ));
    }

    protected function _addDeleteButton()
    {
		$this->_addButton('delete', array(
             'label'     => Mage::helper('adminhtml')->__('Delete'),
             'class'     => 'delete',
             'onclick'   => 'deleteConfirm(\''. Mage::helper('adminhtml')->__('Are you sure you want to do this?')
                 .'\', \'' . $this->getDeleteUrl() . '\')',
         ));
    }
    
    public function getDeleteUrl()
    {
        $_id_slideshow = Mage::registry('cur_slideshow_id');
    	return $this->getUrl('*/*/delete', array('id_slideshow'=>$_id_slideshow));
    }
    
    
    public function getCreateUrl()
    {
    	$_id_slideshow = Mage::registry('cur_slideshow_id');
        return $this->getUrl('*/adminhtml_advancedslideshow_item/edit', array('id_slideshow'=>$_id_slideshow));
    }
  
}