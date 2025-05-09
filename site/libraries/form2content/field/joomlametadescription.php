<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldJoomlaMetaDescription extends F2cFieldBase
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
		$metadesc = $form->getInput('metadesc');
		$attributes = $this->settings->get('attributes');
		
		if($attributes)
		{
			// find the first occurence of >
			$pos = strpos($metadesc, '>', 0);
			$metadesc = substr($metadesc, 0, $pos).' '.$attributes.substr($metadesc, $pos);
		}
		
		return $metadesc;	
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
		return $this->title ? parent::renderLabel($translatedFields) : $form->getLabel('metadesc');		
	}
	
	public function validate(&$data, $item)
	{
		// skip check when running in cron mode
		if(isset($data['isCron'])) return;
		
		$isNew	= empty($data['id']);
		
		// get default value for title
		if(!$this->isFieldVisible())
		{
			$data['metadesc'] = $isNew ? $this->settings->get('default') : $item->metadesc;
		}				
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
      	$xmlFieldContent->value = $form->metadesc;
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
		$data['metadesc'] = (string)$xmlField->contentSingleTextValue->value;
	}	
		
	public function addTemplateVar($templateEngine, $form)
	{
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname), HtmlHelper::stringHTMLSafe($form->metadesc));
		
		// legacy parameter
		$templateEngine->addVar('JOOMLA_META_DESCRIPTION', HtmlHelper::stringHTMLSafe($form->metadesc));
	}
	
	public function setData($data)
	{
	}	
	
	public function getCssClass()
	{
		return 'f2c_metadesc';
	}

	public function getClientSideValidationScript(&$validationCounter, $form)
	{		
		$script = '';
		
		if($this->settings->get('requiredfield'))
		{
			$script = 'arrValidation['.$validationCounter++.']=new Array(\'jform_metadesc\',\''.$this->getFieldName().'\',\''.addslashes($this->getRequiredFieldErrorMessage()).'\');';
		}
		
		return $script;	
	}	
	
	public function setDefaultValue(&$item)
	{
		$item->metadesc	= $this->settings->get('default', '');
	}
	
	public function prepareValidation(&$item, &$data, $isNew)
	{
		$data['metadesc'] = $isNew ? $this->settings->get('default') : $item->metadesc;
	}
	
	public function canBeHiddenInFrontEnd()
	{
		return true;
	}
}
?>