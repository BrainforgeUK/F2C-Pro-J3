<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldJoomlaMetaKeywords extends F2cFieldBase
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
		return $form->getInput('metakey');
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
		return $this->title ? parent::renderLabel($translatedFields) : $form->getLabel('metakey');
	}
	
	public function validate(&$data, $item)
	{
		// skip check when running in cron mode
		if(isset($data['isCron'])) return;
		
		$isNew	= empty($data['id']);
		
		// get default value for title
		if(!$this->isFieldVisible())
		{
			$data['metakey'] = $isNew ? $this->settings->get('default') : $item->metakey;
		}				
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
      	$xmlFieldContent->value = $form->metakey;
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
		$data['metakey'] = (string)$xmlField->contentSingleTextValue->value;
	}	
		
	public function addTemplateVar($templateEngine, $form)
	{
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname), HtmlHelper::stringHTMLSafe($form->metakey));
		
		// legacy parameter
		$templateEngine->addVar('JOOMLA_META_KEYWORDS', HtmlHelper::stringHTMLSafe($form->metakey));
	}
	
	public function setData($data)
	{
	}	
	
	public function getCssClass()
	{
		return 'f2c_metakey';
	}

	public function getClientSideValidationScript(&$validationCounter, $form)
	{		
		$script = '';
		
		if($this->settings->get('requiredfield'))
		{
			$script = 'arrValidation['.$validationCounter++.']=new Array(\'jform_metakey\',\''.$this->getFieldName().'\',\''.addslashes($this->getRequiredFieldErrorMessage()).'\');';
		}
		
		return $script;	
	}	
	
	public function setDefaultValue(&$item)
	{
		$item->metakey 	= $this->settings->get('default', '');
	}
	
	public function prepareValidation(&$item, &$data, $isNew)
	{
		$data['metakey'] = $isNew ? $this->settings->get('default') : $item->metakey;
	}
	
	public function canBeHiddenInFrontEnd()
	{
		return true;
	}
}
?>