<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldJoomlaId extends F2cFieldBase
{	
	public function getPrefix()
	{
		return '';
	}
	
	public function reset()
	{
	}
	
	public function render($translatedFields, $contentTypeSettings, $parms = array(), $form, $formId)
	{
		return $form->getInput('id');	
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
				return $this->title ? parent::renderLabel($translatedFields) : $form->getLabel('id');		
	}
	
	public function validate(&$data, $item)
	{
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
      	$xmlFieldContent->value = $form->id;
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
		// Take the id from the root level
		//$data['id'] = (string)$xmlField->contentSingleTextValue->value;
	}	
		
	public function addTemplateVar($templateEngine, $form)
	{
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname), $form->id);
		
		// legacy parameter
		$templateEngine->addVar('F2C_ID', $form->id);
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
?>