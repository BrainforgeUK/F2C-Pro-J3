<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldDatabaseLookupMulti extends F2cFieldBase
{	
	function __construct($field)
	{
		$this->reset();
		parent::__construct($field);
	}
	
	public function getPrefix()
	{
		return 'dlm';
	}
	
	public function reset()
	{
		$this->values['VALUE']				= array();
		$this->internal['fieldcontentid']	= null;
	}
	
	public function render($translatedFields, $contentTypeSettings, $parms = array(), $form, $formId)
	{
		$displayData	= array();
		$listOptions	= array();
		$translate 		= $this->f2cConfig->get('custom_translations', false);
		
		// Prepare drop down list
		if($this->settings->get('dlm_show_empty_choice_text'))
		{
			$emptyText = $translate ? JText::_($this->settings->get('dlm_empty_choice_text')) : $this->settings->get('dlm_empty_choice_text');
			$listOptions[] = JHTML::_('select.option', '', $emptyText, 'value', 'text');
		}
		
		$rowList = $this->getRowList();

		if(count($rowList))
		{
			foreach($rowList as $row)
			{
				$listOptions[] = JHTML::_('select.option', $row[0], $row[1],'value','text');
			}
		}

		$displayData['listOptions']		= $listOptions;
		$displayData['rowList']			= $rowList;
		$displayData['attributesTable']	= $this->settings->get('dlm_attributes_table') ? $this->settings->get('dlm_attributes_table') : 'border="1"';
		
		return $this->renderLayout('databaselookupmulti', $displayData, $translatedFields, $contentTypeSettings);	
	}
	
	public function prepareSubmittedData($formId)
	{
		$jinput = JFactory::getApplication()->input;
		
		$this->internal['fieldcontentid'] 	= $jinput->getInt('hid'.$this->elementId);
		$this->values['VALUE'] 				= array();
		$rowKeys 							= $jinput->get($this->elementId.'RowKey', array(), 'ARRAY');
		
		if(count($rowKeys))
		{
			foreach($rowKeys as $rowKey)
			{
				$value =  $jinput->get($rowKey . 'val', '', 'RAW');
												
				// prevent duplicate and empty entries
				if(!array_key_exists($value, $this->values['VALUE']) && $value != '')
				{
					$this->values['VALUE'][] = $value;
				}
			}
		}
		
		return $this;
	}
	
	public function store($formid)
	{
		$content	= array();							
		$fieldId 	= array_key_exists('fieldcontentid', $this->internal)? $this->internal['fieldcontentid'] : 0;
			
		if(count($this->values['VALUE']))
		{
			foreach($this->values['VALUE'] as $item)
			{ 
				$content[] 	= new F2cFieldHelperContent($fieldId, 'VALUE', $item, 'INSERT');
			}
		}
		
		// Remove all previous entries
		if(!empty($fieldId))
		{
			$db 	= JFactory::getDbo();
			$query 	= $db->getQuery(true);
			$query->delete('#__f2c_fieldcontent')->where('formid='.$formid)->where('fieldid='.$fieldId);

			$db->setQuery($query);
			$db->execute();
		}
						
		return $content;
	}
	
	public function validate(&$data, $item)
	{
		if($this->settings->get('requiredfield'))
		{
			if(count($this->values['VALUE']))
			{
				foreach($this->values['VALUE'] as $value)
				{
					if($value !== '') return;
				}
			}
			
			throw new Exception($this->getRequiredFieldErrorMessage());		
		}
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentMultipleTextValue');
      	$xmlFieldValues = $xmlFieldContent->addChild('values');
      						
      	if(count($this->values['VALUE']))
      	{
      		foreach($this->values['VALUE'] as $item)
      		{
      			$xmlFieldValues->addChild('value', self::valueReplace($item));
      		}
      	}
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
      	$this->values['VALUE'] 				= array();
      	$this->internal['fieldcontentid'] 	= $this->id;
      					
      	if(count($xmlField->contentMultipleTextValue->values->children()))
      	{
      		foreach($xmlField->contentMultipleTextValue->values->children() as $xmlValue)
      		{
      			$this->values['VALUE'][] = (string)$xmlValue;
      		}
      	}
	}
	
	public function addTemplateVar($templateEngine, $form)
	{
		$output 	= '';		
		$assocArray	= array();
		$dicValues = $this->getRowList();
		
		if(count($this->values))
		{
			foreach($this->values['VALUE'] as $value)
			{
				if(array_key_exists($value, $dicValues))
				{
					$output .= '<li>'.$dicValues[$value][1].'</li>';
					$assocArray[$value] = $dicValues[$value][1];
				}
			}	
			
			if($this->settings->get('dlm_output_mode'))
			{
				$output = '<ul>'.$output.'</ul>';
			}
			else
			{
				$output = '<ol>'.$output.'</ol>';				
			}				
		}
		
		$templateEngine->addVar($this->fieldname.'_VALUES', $assocArray);
		$templateEngine->addVar($this->fieldname, $output);
		$templateEngine->addVar($this->fieldname.'_CSV', implode(', ', $assocArray));		
	}
	
	public function getTemplateParameterNames()
	{
		$names = array(	StringHelper::strtoupper($this->fieldname).'_VALUES',
						StringHelper::strtoupper($this->fieldname).'_CSV');
		
		return array_merge($names, parent::getTemplateParameterNames());
	}
	
	public function setData($data)
	{
		$this->values[$data->attribute][] = $data->content;
	}

	/*
	 * Fetch the select list options from the database
	 */
	private function getRowList()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		
		$sql = $this->settings->get('dlm_query');
		$sql = str_replace('{$CURRENT_USER_ID}', $user->id, $sql);
		$sql = str_replace('{$CURRENT_USER_GROUPS}', implode(',', $user->groups), $sql);
		$sql = str_replace('{$LANGUAGE}', JFactory::getLanguage()->getTag(), $sql);
		
		$db->setQuery($sql);
		return $db->loadRowList(0);
	}
}
?>