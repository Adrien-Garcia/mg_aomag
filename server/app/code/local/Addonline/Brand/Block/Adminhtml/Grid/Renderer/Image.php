<?php
class Addonline_Brand_Block_Adminhtml_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $filename=$row->getFilename();
    	if(!$filename || !$this->getFileExists($filename)) {
    		return "<center>" . $this->__("(no image)") . "</center>";
        } else{
        	$url=$this->getImageUrl($filename);
            return '<center><a href="#" onclick="window.open(\''. $url .'\', \''. $filename .'\')"'.
            'title="'. $filename .'" '. ' url="'.$url.'" id="imageurl">'.
            "<img src='".$url."' width='75' height='75'/>".
            "</a></center>";
        }
        

    }
    
    public function getImageUrl($image_file)
    {
        $url = false;
        $url = Mage::getBaseUrl('media').'catalog/brand/'. $image_file;
        return $url;
    }
  
    
    public function getFileExists($image_file)
    {
        $file_exists = false;
        $file_exists = file_exists('media/catalog/brand/'. $image_file);
        return $file_exists;
    }
    
} 
