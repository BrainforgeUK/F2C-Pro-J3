<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldJoomlaAccess extends F2cFieldBase
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
		return $form->getInput('access');		
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
		return $this->title ? parent::renderLabel($translatedFields) : $form->getLabel('access');
	}
	
	public function validate(&$data, $item)
	{
		// skip check when running in cron mode
		if(isset($data['isCron'])) return;
		
		$isNew	= empty($data['id']);
		
		// get default value for title
		if(!$this->isFieldVisible())
		{
			$data['access'] = $isNew ? $this->settings->get('default') : $item->access;
		}				
	}
	
	public function export($xmlFields, $form)
	{		
		static $dicViewingAccessLevelId = null;
				
		if($dicViewingAccessLevelId == null)
		{
			$dicViewingAccessLevelId = $this->loadDicViewingAccessLevelId();
		}
		
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
      	$xmlFieldContent->value = $dicViewingAccessLevelId[$form->access];
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
		$data['access'] = $this->getDicViewingAccessLevelTitle((string)$xmlField->contentSingleTextValue->value);
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
		return 'f2c_access';
	}	
	
	public function getClientSideValidationScript(&$validationCounter, $form)
	{		
		return '';
	}	
	
	private function loadDicViewingAccessLevelId()
	{
		$db = JFactory::getDbo();		
		$query = $db->getQuery(true);
		
		$query->select('id, title');
		$query->from('#__viewlevels');
		$db->setQuery($query);
		
		return $db->loadAssocList('id', 'title');
	}
	
	private function getDicViewingAccessLevelTitle($access)
	{
		static $dicViewingAccessLevelTitle = null;
		
		if(!$dicViewingAccessLevelTitle)
		{
			$db 	= JFactory::getDbo();
			$query 	= $db->getQuery(true);
			
			$query->select('id, title');
			$query->from('#__viewlevels');
			$db->setQuery($query);
			
			$dicViewingAccessLevelTitle	= $db->loadAssocList('title', 'id');			
		}
		
		return (int)$dicViewingAccessLevelTitle[$access];
	}
	
	public function setDefaultValue(&$item)
	{
		$item->access = $this->settings->get('default', $this->getDicViewingAccessLevelTitle('Public'));
	}
	
	public function prepareValidation(&$item, &$data, $isNew)
	{
		$data['access'] = $isNew ? $this->settings->get('default', $this->getDicViewingAccessLevelTitle('Public')) : $item->access;
	}
	
	public function canBeHiddenInFrontEnd()
	{
		if($this->settings->get('requiredfield'))
		{
			// when required, a default should be provided
			if($this->settings->get('default', '') == '')
			{
				return false;
			}
		}
		
		return true;
	}
}
?>