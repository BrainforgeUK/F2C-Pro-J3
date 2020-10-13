<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldJoomlaAlias extends F2cFieldBase
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
		$html = $form->getInput('alias');
		
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
		return $this->title ? parent::renderLabel($translatedFields) : $form->getLabel('alias');
	}
	
	public function validate(&$data, $item)
	{
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
      	$xmlFieldContent->value = $form->alias;
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
		$data['alias'] = (string)$xmlField->contentSingleTextValue->value;
	}	
		
	public function addTemplateVar($templateEngine, $form)
	{
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname), HtmlHelper::stringHTMLSafe($form->alias));
		// legacy parameter
		$templateEngine->addVar('JOOMLA_TITLE_ALIAS', HtmlHelper::stringHTMLSafe($form->alias));
	}
	
	public function setData($data)
	{
	}	
	
	public function getCssClass()
	{
		return 'f2c_title_alias';
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