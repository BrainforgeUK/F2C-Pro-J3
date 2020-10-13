<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldInfoText extends F2cFieldBase
{	
	public function getPrefix()
	{
		return 'inf';
	}
	
	public function reset()
	{
	}
	
	public function render($translatedFields, $contentTypeSettings, $parms = array(), $form, $formId)
	{
		$displayData = array();
		
		return $this->renderLayout('infotext', $displayData, $translatedFields, $contentTypeSettings);		
	}
	
	public function prepareSubmittedData($formId)
	{
		return $this;
	}
	
	public function store($formid)
	{
		return array();		
	}
	
	public function renderLabel($translatedFields)
	{
		return '';
	}
	
	public function validate(&$data, $item)
	{
	}
	
	public function export($xmlFields, $form)
	{
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
	}	
	
	public function addTemplateVar($templateEngine, $form)
	{
		$templateEngine->addVar($this->fieldname, '');
	}
	
	public function getTemplateParameterNames()
	{
		return array();
	}

	public function setData($data)
	{
	}	
	
	public function canBeHiddenInFrontEnd()
	{
		return true;
	}
}
?>