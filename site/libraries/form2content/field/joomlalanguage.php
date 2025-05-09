<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldJoomlaLanguage extends F2cFieldBase
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
		return $form->getInput('language');		
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
		return $this->title ? parent::renderLabel($translatedFields) : $form->getLabel('language');		
	}
	
	public function validate(&$data, $item)
	{
		// skip check when running in cron mode
		if(isset($data['isCron'])) return;
		
		$isNew	= empty($data['id']);
		
		// get default value for title
		if(!$this->isFieldVisible())
		{
			$data['language'] = $isNew ? $this->settings->get('default', '*') : $item->language;
		}				
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
      	$xmlFieldContent->value = $form->language;
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
		$data['language'] = (string)$xmlField->contentSingleTextValue->value;
	}	
		
	public function addTemplateVar($templateEngine, $form)
	{
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname), $form->language);
		
		// legacy parameter
		$templateEngine->addVar('JOOMLA_LANGUAGE', $form->language);
	}
	
	public function setData($data)
	{
	}	
	
	public function getCssClass()
	{
		return 'f2c_language';
	}

	public function getClientSideValidationScript(&$validationCounter, $form)
	{		
		return '';
	}

	public function setDefaultValue(&$item)
	{
		$item->language = $this->settings->get('default', '');
	}
	
	public function prepareValidation(&$item, &$data, $isNew)
	{
		$data['language'] = $isNew ? $this->settings->get('default', 0) : $item->language;
	}
	
	public function canBeHiddenInFrontEnd()
	{
		return true;
	}
}
?>