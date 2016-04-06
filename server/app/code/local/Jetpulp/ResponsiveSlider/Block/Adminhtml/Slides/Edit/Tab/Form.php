<?php

class Jetpulp_ResponsiveSlider_Block_Adminhtml_Slides_Edit_Tab_Form
	extends Mage_Adminhtml_Block_Widget_Form
	implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

	protected $_form;

	protected $_fieldset;

	protected $_model;

	/**
	 * Load Wysiwyg on demand and Prepare layout
	 */
	protected function _prepareLayout()
	{
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		}
		/* @var $model Jetpulp_ResponsiveSlider_Model_Responsiveslider_Item */
		$this->_model = Mage::registry('cms_slides');

		parent::_prepareLayout();
	}

	protected function _prepareForm()
	{
		$this->_prepareFieldset();

		$this->_form->setValues($this->_model->getData());
		$this->setForm($this->_form);

		return parent::_prepareForm();

	}

	protected function _prepareFieldset()
	{
		$this->_form = new Varien_Data_Form();
		$this->_form->setHtmlIdPrefix('slides_');

		$this->_fieldset = $this->_form->addFieldset('base_fieldset', array('legend'=>Mage::helper('responsiveslider')->__('Slide Information')));

		$isElementDisabled = false;
		$dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

		if ($this->_model->getId()) {
			$this->_fieldset->addField(
				'item_id',
				'hidden',
				array(
					'name' => 'item_id',
				)
			);
		}

		$this->_fieldset->addField('title', 'text', array(
			'label'     => Mage::helper('responsiveslider')->__('Title'),
			'name'      => 'title',
		));

		$this->_fieldset->addField('baseline', 'text', array(
			'label'     => Mage::helper('responsiveslider')->__('Baseline'),
			'name'      => 'baseline',
		));

		$this->_fieldset->addField('url', 'text', array(
			'label'     => Mage::helper('responsiveslider')->__('URL link'),
			'name'      => 'url',
			'class' 	=> 'required-entry',
			'required'  => true,
		));

		/**
		 * Check is single store mode */
		if (!Mage::app()->isSingleStoreMode()) {
			$field = $this->_fieldset->addField('store_id', 'multiselect', array(
				'name'      => 'stores[]',
				'label'     => Mage::helper('responsiveslider')->__('Store View'),
				'title'     => Mage::helper('responsiveslider')->__('Store View'),
				'required'  => true,
				'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
				'disabled'  => $isElementDisabled,
			));
			$renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
			$field->setRenderer($renderer);
		}
		else {
			$this->_fieldset->addField('store_id', 'hidden', array(
				'name'      => 'stores[]',
				'value'     => Mage::app()->getStore(true)->getId()
			));
			$this->_model->setStoreId(Mage::app()->getStore(true)->getId());
		}

		$this->_fieldset->addField('background_image', 'image', array(
			'label'     => Mage::helper('responsiveslider')->__('Background image'),
			'name'      => 'background_image',
			'required'  => true,
		))->setAfterElementHtml(
			"<script type=\"text/javascript\">
				if($('".$this->_form->getHtmlIdPrefix()."background_image_image') == null){
					$('".$this->_form->getHtmlIdPrefix()."background_image').addClassName('required-entry required-file');
				}
			</script>");

		$this->_fieldset->addField('alt_image', 'image', array(
			'label'     => Mage::helper('responsiveslider')->__('Alternative image'),
			'name'      => 'alt_image',
			'required'  => false,
		));

		$this->_fieldset->addField('is_active', 'select', array(
			'label'     => Mage::helper('cms')->__('Status'),
			'title'     => Mage::helper('responsiveslider')->__('Slider Status'),
			'name'      => 'is_active',
			'required'  => true,
			'options'   => $this->_model->getAvailableStatuses(),
			'disabled'  => $isElementDisabled,
		));
		if (!$this->_model->getId()) {
			$this->_model->setData('is_active', $isElementDisabled ? '0' : '1');
		}

		$this->_fieldset->addField('from_date', 'date', array(
			'label'     => Mage::helper('responsiveslider')->__('From Date'),
			'name'      => 'from_date',
			'image'  	=> $this->getSkinUrl('images/grid-cal.gif'),
			'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
			'format'       => $dateFormatIso
		));
		$this->_fieldset->addField('to_date', 'date', array(
			'label'     => Mage::helper('responsiveslider')->__('To Date'),
			'name'      => 'to_date',
			'image'  	=> $this->getSkinUrl('images/grid-cal.gif'),
			'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
			'format'       => $dateFormatIso
		));

		$this->_fieldset->addField('content', 'editor', array(
			'name'      => 'content',
			'label'     => Mage::helper('cms')->__('Content'),
			'title'     => Mage::helper('cms')->__('Content'),
			'style'     => 'height:36em',
			'required'  => false,
			'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig()
		));

		$this->_fieldset->addField('product_sku', 'text', array(
			'label'     => Mage::helper('responsiveslider')->__('Linked product SKU'),
			'name'      => 'product_sku',
		));

		$this->_fieldset->addField('sort_order', 'text', array(
			'label'     => Mage::helper('responsiveslider')->__('Position'),
			'name'      => 'sort_order',
		));
	}

	/**
	 * Prepare label for tab
	 *
	 * @return string
	 */
	public function getTabLabel()
	{
		return Mage::helper('responsiveslider')->__('Slide Information');
	}

	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return Mage::helper('responsiveslider')->__('Slide Information');
	}

	/**
	 * Returns status flag about this tab can be shown or not
	 *
	 * @return true
	 */
	public function canShowTab()
	{
		return true;
	}

	/**
	 * Returns status flag about this tab hidden or not
	 *
	 * @return true
	 */
	public function isHidden()
	{
		return false;
	}

	/**
	 * Check permission for passed action
	 *
	 * @param string $action
	 * @return bool
	protected function _isAllowedAction($action)
	{
	return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
	}
	 */
}