<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldJoomlaTemplate extends F2cFieldBase
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
		return $form->getInput($this->getTemplateName());
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
		return $this->title ? parent::renderLabel($translatedFields) : $form->getLabel($this->getTemplateName());
	}
	
	public function validate(&$data, $item)
	{
		// skip check when running in cron mode
		if(isset($data['isCron'])) return;
		
		$isNew	= empty($data['id']);
		
		// get default value for title
		if(!$this->isFieldVisible())
		{
			$templateName = $this->getTemplateName();
			$data[$templateName] = $isNew ? $this->settings->get('default') : $item->$templateName;
		}
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField 				= $xmlFields->addChild('field');
      	$xmlField->fieldname 	= $this->fieldname;
      	$xmlFieldContent 		= $xmlField->addChild('contentSingleTextValue');
		$templateName 			= $this->getTemplateName();		
		$xmlFieldContent->value = $form->$templateName;
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
		$data[$this->getTemplateName()] = (string)$xmlField->contentSingleTextValue->value;
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
		return 'f2c_template';
	}
	
	public function getClientSideValidationScript(&$validationCounter, $form)	
	{		
		$script = '';
		
		if($this->settings->get('requiredfield'))
		{
			$script = 'arrValidation['.$validationCounter++.']=new Array(\'jform_'.$this->getTemplateName().'_name\',\''.$this->getFieldName().'\',\''.addslashes($this->getRequiredFieldErrorMessage()).'\');';
		}
		
		return $script;	
	}	

	public function setDefaultValue(&$item)
	{
		$templateName 			= $this->getTemplateName();
		$item->$templateName 	= $this->settings->get('default', '');
	}

	/*
	 * Check if the template value equals the default template value
	 */
	public function valueIsDefaultValue($item)
	{
		// get default template name
		$contentType = F2cFactory::getContentType($item->projectid);
		$templateName = $this->getTemplateName();
		$defaultTemplate = 'default_'.$templateName.'_';
		$defaultTemplate .= JFile::makeSafe($contentType->title) . '.tpl';
		
		$templateValue = $item->$templateName;
		
		return $defaultTemplate == $templateValue;
	}	
	
	public function prepareValidation(&$item, &$data, $isNew)
	{
		$templateName = $this->getTemplateName();
		
		$data[$templateName] = $isNew ? $this->settings->get('default') : $item->$templateName;
	}
	
	public function canBeHiddenInFrontEnd()
	{
		if($this->settings->get('default', '') == '')
		{
			return false;
		}
		
		return true;
	}
	
	private function getTemplateName()
	{
		return strpos($this->fieldname, 'intro_template') !== false ? 'intro_template' : 'main_template';
	}
}
?>