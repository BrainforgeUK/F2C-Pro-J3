<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use \Joomla\Registry\Registry;
use Joomla\String\StringHelper;

class F2cFieldDisplayList extends F2cFieldBase
{	
	function __construct($field)
	{
		$this->reset();
		parent::__construct($field);
	}
	
	public function getPrefix()
	{
		return 'dsp';
	}
	
	public function reset()
	{
		$this->values['VALUE'] 				= array();
		$this->internal['fieldcontentid']	= null;
	}
	
	public function render($translatedFields, $contentTypeSettings, $parms = array(), $form, $formId)
	{
		$displayData					= array();
		$displayData['attributesTable']	= $this->settings->get('dsp_attributes_table') ? $this->settings->get('dsp_attributes_table') : 'border="1"';
		
		return $this->renderLayout('displaylist', $displayData, $translatedFields, $contentTypeSettings);				
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
		$fieldId 	= $this->internal['fieldcontentid'];
		$listNew 	= null;
		$valueList	= new Registry();

		if(count($this->values['VALUE']))
		{
			foreach($this->values['VALUE'] as $displayItem)
			{ 
				$listNew[] = $displayItem;
			}
		}
		
		$valueList->loadArray($listNew);
				
		$value 		= $valueList->toString();		
		$action 	= ($value) ? (($fieldId) ? 'UPDATE' : 'INSERT') : (($fieldId) ? 'DELETE' : '');
		$content[] 	= new F2cFieldHelperContent($fieldId, 'VALUE', $value, $action);
		
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
      			$tmpNode = $xmlFieldValues->addChild('value');
				$tmpNode[0] = $item;
       		}
      	}
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{	
		$this->values['VALUE'] = array();
      						
      	if(count($xmlField->contentMultipleTextValue->values->children()))
      	{
			$this->internal['fieldcontentid'] = $data['id'] ? $existingInternalData['fieldcontentid'] : 0;
      					      							
      		foreach($xmlField->contentMultipleTextValue->values->children() as $xmlValue)
      		{
      			$this->values['VALUE'][] = (string)$xmlValue;
      		}
      	}
	}
	
	public function addTemplateVar($templateEngine, $form)
	{
		$output = '';
		$values	= array();
		
		if($this->values['VALUE'] && count($this->values['VALUE']))
		{
			foreach($this->values['VALUE'] as $value)
			{
				$output 	.= '<li>'.htmlspecialchars($value).'</li>';
				$values[] 	= $value;
			}	
			
			if($this->settings->get('dsp_output_mode'))
			{
				$output = '<ul>'.$output.'</ul>';
			}
			else
			{
				$output = '<ol>'.$output.'</ol>';				
			}				
		}
						
		$templateEngine->addVar($this->fieldname, $output);
		$templateEngine->addVar($this->fieldname.'_VALUES', $values);
		$templateEngine->addVar($this->fieldname.'_CSV', implode(', ', $values));
	}
	
	public function getTemplateParameterNames()
	{
		$names = array(	StringHelper::strtoupper($this->fieldname).'_VALUES',
						StringHelper::strtoupper($this->fieldname).'_CSV');
		
		return array_merge($names, parent::getTemplateParameterNames());
	}
	
	public function setData($data)
	{
		$this->internal['fieldcontentid']	= $data->fieldcontentid;
		$values 							= new Registry($data->content);											
		$this->values['VALUE'] 				= $values->toArray();
	}
}
?>