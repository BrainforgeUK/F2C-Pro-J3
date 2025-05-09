<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldJoomlaTitle extends F2cFieldBase
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
		$html = $form->getInput('title');
		
		if($this->settings->get('attributes'))
		{
			// Inject the attributes into the rendered string
			$parts = explode('/>', $html);
			$html = $parts[0].' '.$this->settings->get('attributes').'/>';
		}
		
		return $html;
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
		return $this->title ? parent::renderLabel($translatedFields) : $form->getLabel('title');
	}
	
	public function validate(&$data, $item)
	{
		// skip check when running in cron mode
		if(isset($data['isCron'])) return;
		
		$isNew	= empty($data['id']);
		
		// get default value for title
		if(!$this->isFieldVisible())
		{
			$data['title'] = $isNew ? $this->settings->get('default') : $item->title;
		}		
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
      	$xmlFieldContent->value = $form->title;
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
		$data['title'] = (string)$xmlField->contentSingleTextValue->value;
	}	
		
	public function addTemplateVar($templateEngine, $form)
	{
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname), HtmlHelper::stringHTMLSafe($form->title));
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname).'_RAW', $form->title);
		
		// legacy parameters
		$templateEngine->addVar('JOOMLA_TITLE', HtmlHelper::stringHTMLSafe($form->title));
		$templateEngine->addVar('JOOMLA_TITLE_RAW', $form->title);
	}
	
	public function getTemplateParameterNames()
	{
		$names = array(StringHelper::strtoupper($this->fieldname).'_RAW');		
		return array_merge($names, parent::getTemplateParameterNames());		
	}

	public function setData($data)
	{
	}	
	
	public function getCssClass()
	{
		return 'f2c_title';
	}

	public function getClientSideValidationScript(&$validationCounter, $form)	
	{		
		$script = '';
		
		if($this->settings->get('requiredfield'))
		{
			$script = 'arrValidation['.$validationCounter++.']=new Array(\'jform_title\',\''.$this->getFieldName().'\',\''.addslashes($this->getRequiredFieldErrorMessage()).'\');';
		}
		
		return $script;	
	}	

	public function setDefaultValue(&$item)
	{
		$item->title = $this->settings->get('default', '');
	}
	
	public function prepareValidation(&$item, &$data, $isNew)
	{
		$data['title'] = $isNew ? $this->settings->get('default') : $item->title;
	}
	
	public function canBeHiddenInFrontEnd()
	{
		if($this->settings->get('default', '') == '')
		{
			return false;
		}
		
		return true;
	}
	
	protected function getLabelFor()
	{
		return 'jform_title';
	}
}
?>