<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldJoomlaPublished extends F2cFieldBase
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
		return $form->getInput('state');		
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
		return $this->title ? parent::renderLabel($translatedFields) : $form->getLabel('state');
	}
	
	public function validate(&$data, $item)
	{
		// skip check when running in cron mode
		if(isset($data['isCron'])) return;
		
		$isNew	= empty($data['id']);
		
		// get default value for title
		if(!$this->isFieldVisible())
		{
			$data['state'] = $isNew ? $this->settings->get('default') : $item->state;
		}				
	}
	
	public function export($xmlFields, $form)
	{
		$arrState = array(F2C_STATE_TRASH => 'trashed', F2C_STATE_UNPUBLISHED => 'unpublished', F2C_STATE_PUBLISHED => 'published');
		
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
      	$xmlFieldContent->value = $arrState[$form->state];				
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
		$state = strtolower((string)$xmlField->contentSingleTextValue->value);
		
		switch($state)
		{
			case 'published':
				$data['state'] = 1; 
				break;
			case 'unpublished':
				$data['state'] = 0;
				break;
			default:
				throw new Exception('Invalid state detected: ' . $state);
		}
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
		return 'f2c_state';
	}	
	
	public function getClientSideValidationScript(&$validationCounter, $form)
	{		
		return '';
	}	
	
	public function setDefaultValue(&$item)
	{
		$item->language = $this->settings->get('default', 0);
	}
	
	public function prepareValidation(&$item, &$data, $isNew)
	{
		$data['state'] = $isNew ? $this->settings->get('default', 0) : $item->state;
	}
	
	public function canBeHiddenInFrontEnd()
	{
		return true;
	}
}
?>