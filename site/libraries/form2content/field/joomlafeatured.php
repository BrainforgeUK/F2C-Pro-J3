<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldJoomlaFeatured extends F2cFieldBase
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
		return $form->getInput('featured');	
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
		return $this->title ? parent::renderLabel($translatedFields) : $form->getLabel('featured');
	}
	
	public function validate(&$data, $item)
	{
		// skip check when running in cron mode
		if(isset($data['isCron'])) return;
		
		$isNew	= empty($data['id']);
		
		// get default value for title
		if(!$this->isFieldVisible())
		{
			$data['featured'] = $isNew ? $this->settings->get('default') : $item->featured;
		}				
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
      	$xmlFieldContent->value = $form->featured ? "yes" : "no";
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
		$data['featured'] = (string)$xmlField->contentSingleTextValue->value == "yes" ? 1 : 0; 
	}	
		
	public function addTemplateVar($templateEngine, $form)
	{
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname), $form->featured);
		
		// legacy parameter
		$templateEngine->addVar('JOOMLA_FEATURED', $form->featured);
	}
	
	public function setData($data)
	{
	}

	public function getCssClass()
	{
		return 'f2c_featured';
	}	
	
	public function getClientSideValidationScript(&$validationCounter, $form)
	{		
		return '';
	}

	public function setDefaultValue(&$item)
	{
		$item->featured = $this->settings->get('default', 0);
	}
	
	public function prepareValidation(&$item, &$data, $isNew)
	{
		$data['featured'] = $isNew ? $this->settings->get('default', 0) : $item->featured;
	}

	public function canBeHiddenInFrontEnd()
	{
		return true;
	}
}
?>