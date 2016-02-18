<?php

class Jetpulp_ResponsiveSlider_Block_Adminhtml_Responsiveslider_Items_Edit_Tab_Form
	extends Jetpulp_ResponsiveSlider_Block_Adminhtml_Slides_Edit_Tab_Form
{

	protected function _prepareForm()
	{
		$this->_prepareFieldset();
		$this->_fieldset->addField('responsiveslider_id', 'hidden', array(
			'name'      => 'responsiveslider_id',
		));

		$responsiveslider_id = $this->getRequest()->getParam('responsiveslider_id');
		if( isset($responsiveslider_id) ) {
			$this->_model->setData('responsiveslider_id', $responsiveslider_id);
		}

		$this->_form->setValues($this->_model->getData());
		$this->setForm($this->_form);

		return $this;
	}
}