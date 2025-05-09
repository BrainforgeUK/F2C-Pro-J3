<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldJoomlaAssociations extends F2cFieldBase
{	
	public function getPrefix()
	{
		return '';
	}
	
	public function reset()
	{
	}
	
	// Modified Brainforge.uk 20250509
	public function render($translatedFields, $contentTypeSettings, $parms, $form, $formId)
	{
		$assoc = JLanguageAssociations::isEnabled() && $this->frontvisible;
		
		if($assoc)
		{
			JForm::addFieldPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/fields'); 
			// load general admin language file
			JFactory::getLanguage()->load('', JPATH_ADMINISTRATOR);	
		}
		
		$displayData = new DisplayDataAssociations();
		$displayData->form = $form;
		
		return JLayoutHelper::render('joomla.edit.associations', $displayData);
	}
	
	public function prepareSubmittedData($formId)
	{
		return $this;
	}
	
	public function store($formid)
	{
		return array();		
	}
	
	public function renderLabel($translatedFields, $form = null)
	{
		return empty($this->title) ? JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS') : JText::_($this->title);
	}
	
	public function validate(&$data, $item)
	{
	}
	
	public function export($xmlFields, $formId)
	{
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
	}	
	
	public function addTemplateVar($templateEngine, $form)
	{
	}
	
	public function getTemplateParameterNames()
	{
		return array();
	}

	public function setData($data)
	{
	}	
	
	public function getCssClass()
	{
		return 'f2c_id';
	}
	
	public function getClientSideValidationScript(&$validationCounter, $form)
	{		
		return '';
	}
	
	public function canBeHiddenInFrontEnd()
	{
		return true;
	}
}

class DisplayDataAssociations
{
	public $form;
	
	public function getForm()
	{
		return $this->form;
	}
}
?>