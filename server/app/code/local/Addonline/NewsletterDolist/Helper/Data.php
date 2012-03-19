<?php
class Addonline_NewsletterDolist_Helper_Data extends Mage_Core_Helper_Data
{
	const XML_PATH_DOLIST_SUB_FORM     = 'newsletter/dolist/subscribe_form';	
	public $label = array();
	public $fieldName = '';
	public $id = 0;
	public $getLabel = false;	
	
	public function loadForm(){
		$dolist_subscribe_form = Mage::getStoreConfig(self::XML_PATH_DOLIST_SUB_FORM);		
		if($dolist_subscribe_form != ''):						
			$doc = new DOMDocument();
			$doc->loadHTML($dolist_subscribe_form);
			return $doc;
		else:
			return null;
		endif;		
	}
	
	public function parse($doc) {

  		  $getLabel;
  		  $label;
		  $fieldName;
		  $id;
		  
		  foreach ($doc->childNodes as $item) {
		    if ($item->nodeType == XML_TEXT_NODE ) {
		      if ($this->getLabel) { // L'element précédent était un input de type 'centre d'interet', on recupere le libellé de ce centre d'interet.
		        $this->label[$this->id] = $item->data;
		        $this->getLabel = false;
		      }  
		    }
		    if ($item->nodeType == XML_ELEMENT_NODE ) {
		      //echo "- ".$item->tagName;
		      if ($item->tagName=='input' && $item->getAttribute('type')=='checkbox' && strpos($item->getAttribute('name'),'do_interest')!==false) { // On est sur un élément du type <input type="checkbox" value="35" Name="do_interest_9">, l'element suivant devrait etr eun text_node avec le libellé
		        //echo " ".$item->getAttribute('value');
		        //echo " ".$item->getAttribute('name');
		        $this->getLabel = true;
		        $this->id = $item->getAttribute('value');
		        $this->fieldName = $item->getAttribute('name');
		      }
		      //echo PHP_EOL;
		     $this->parse($item); // Sous-élément à parser !
		    }
		
		  }
	}
	
	public function load(){
		if($this->loadForm()):
			$this->parse($this->loadForm());		
			return array($this->label, $this->fieldName);
		else:
			return null;
		endif;		
	}
		
			
}