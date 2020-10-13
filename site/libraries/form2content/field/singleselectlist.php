<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldSingleSelectList extends F2cFieldBase
{	
	function __construct($field)
	{
		$this->reset();
		parent::__construct($field);
	}
	
	public function getPrefix()
	{
		return 'ssl';
	}
	
	public function render($translatedFields, $contentTypeSettings, $parms = array(), $form, $formId)
	{
		$displayData	= array();
		$listOptions 	= null;
		$translate 		= $this->f2cConfig->get('custom_translations', false);

		if($this->settings->get('ssl_show_empty_choice_text'))
		{ 
			$emptyText = $translate ? JText::_($this->settings->get('ssl_empty_choice_text')) : $this->settings->get('ssl_empty_choice_text');
			$listOptions[] = JHTML::_('select.option', '', $emptyText);
		}
			     				
		if(count((array)$this->settings->get('ssl_options')))
		{
			foreach((array)$this->settings->get('ssl_options') as $key => $value)
			{ 
				$optionValue = $translate ? JText::_($value) : $value;
				$listOptions[] = JHTML::_('select.option', $key, $optionValue);  	
			}			
		}

		$displayData['listOptions']	= $listOptions;

		return $this->renderLayout('singleselectlist', $displayData, $translatedFields, $contentTypeSettings);		
	}
	
	public function prepareSubmittedData($formId)
	{
		$jinput = JFactory::getApplication()->input;
		
		$this->internal['fieldcontentid'] = $jinput->getInt('hid'.$this->elementId);
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
		$value = trim($this->values['VALUE']);
		
		if($this->settings->get('requiredfield') && $value === '')
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
		$translate 	= $this->f2cConfig->get('custom_translations', false);
		$options 	= (array)$this->settings->get('ssl_options');
		$fieldText 	= '';					
		$fieldValue	= '';					
		
		if($this->values['VALUE'])
		{
			$fieldValue = $this->values['VALUE'];
			
			// TODO: why doesn't array_key_exists work here?
			foreach($options as $key => $value)
			{
				if($key == $fieldValue)
				{
					$fieldText = $value;
					break;
				}
			}
		}
		
		if($translate)
		{
			$fieldText = JText::_($fieldText);
		}
		
		$templateEngine->addVar($this->fieldname, $fieldValue);
		$templateEngine->addVar($this->fieldname.'_TEXT', $fieldText);
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
}
?>