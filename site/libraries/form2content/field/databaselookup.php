<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldDatabaseLookup extends F2cFieldBase
{
	function __construct($field)
	{
		$this->reset();
		parent::__construct($field);
	}
	
	public function getPrefix()
	{
		return 'dbl';
	}
	
	// Modified Brainforge.uk 20250509
	public function render($translatedFields, $contentTypeSettings, $parms, $form, $formId)
	{
		$displayData	= array();
		$listOptions 	= array();
		$translate 		= $this->f2cConfig->get('custom_translations', false);

		if($this->settings->get('dbl_show_empty_choice_text'))
		{
			$emptyText = $translate ? JText::_($this->settings->get('dbl_empty_choice_text')) : $this->settings->get('dbl_empty_choice_text');
			$listOptions[] = JHTML::_('select.option', '', $emptyText, 'value', 'text');
		}
			      				
		$rowList = $this->getRowList();

		if(count($rowList))
		{
			foreach($rowList as $row)
			{
				$listOptions[] = JHTML::_('select.option', $row[0], $row[1], 'value', 'text');
			}
		}
		
		$displayData['listOptions']	= $listOptions;
		
		return $this->renderLayout('databaselookup', $displayData, $translatedFields, $contentTypeSettings);
	}
	
	public function prepareSubmittedData($formId)
	{
		$jinput = JFactory::getApplication()->input;
		
		$this->internal['fieldcontentid'] 	= $jinput->getInt('hid'.$this->elementId);
		$this->values['VALUE'] = $jinput->get($this->elementId, '', 'RAW');
		
		return $this;
	}
	
	public function store($formid)
	{
		$content 	= array();
		$value 		= $this->values['VALUE'];
		$fieldId 	= $this->internal['fieldcontentid'];
		$action 	= ($value) ? (($fieldId) ? 'UPDATE' : 'INSERT') : (($fieldId) ? 'DELETE' : '');
		$content[] 	= new F2cFieldHelperContent($fieldId, 'VALUE', $value, $action);
		
		return $content;		
	}
	
	public function validate(&$data, $item)
	{
		if($this->settings->get('requiredfield') && $this->values['VALUE'] === '')
		{
			throw new Exception($this->getRequiredFieldErrorMessage());
		}
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
      	$xmlFieldContent->value = $this->values['VALUE'];
    }
    
	public function import($xmlField, $existingInternalData, &$data)
	{
		$this->values['VALUE'] = (string)$xmlField->contentSingleTextValue->value;
		$this->internal['fieldcontentid'] = $data['id'] ? $existingInternalData['fieldcontentid'] : 0;
	}
	
	public function addTemplateVar($templateEngine, $form)
	{
		$text = '';
		$value = '';
				
		if($this->values['VALUE'])
		{
			$value 		= $this->values['VALUE'];
			$rowList 	= $this->getRowList();
			
			if((array_key_exists($value, $rowList)))
			{
				$text= $rowList[$value][1];
			}	
		}	
		
		$templateEngine->addVar($this->fieldname, $value);
		$templateEngine->addVar($this->fieldname.'_TEXT', $text);
	}
	
	public function getTemplateParameterNames()
	{
		$names = array(StringHelper::strtoupper($this->fieldname).'_TEXT');		
		return array_merge($names, parent::getTemplateParameterNames());
	}
	
	public function setData($data)
	{
		if($data->attribute)
		{
			$this->values[$data->attribute] 	= $data->content;
			$this->internal['fieldcontentid'] 	= $data->fieldcontentid;
		}
	}

	/*
	 * Fetch the select list options from the database
	 */
	private function getRowList()
	{
		$db 	= JFactory::getDBO();
		$user 	= JFactory::getUser();
		
		$sql = $this->settings->get('dbl_query');
		$sql = str_replace('{$CURRENT_USER_ID}', $user->id, $sql);
		$sql = str_replace('{$CURRENT_USER_GROUPS}', implode(',', $user->groups), $sql);
		$sql = str_replace('{$LANGUAGE}', JFactory::getLanguage()->getTag(), $sql);
		
		$db->setQuery($sql);
		return $db->loadRowList(0);
	}
}
?>