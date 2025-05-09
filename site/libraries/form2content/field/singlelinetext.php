<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldSingleLineText extends F2cFieldBase
{	
	function __construct($field)
	{
		$this->reset();
		parent::__construct($field);
	}
	
	public function getPrefix()
	{
		return 'slt';
	}
	
	// Modified Brainforge.uk 20250509
	public function render($translatedFields, $contentTypeSettings, $parms, $form, $formId)
	{
		$displayData = array();
		
		if(!count($parms))
		{
			$parms = JFactory::getApplication()->isClient('site') ? array(50, 100) : array(100, 100);
		}
		
		$displayData['size']			= $this->settings->get('slt_size', $parms[0]);
		$displayData['maxLength']		= $this->settings->get('slt_max_length', $parms[1]);
		$displayData['attributes']		= $this->settings->get('slt_attributes');
		$displayData['class']			= $this->settings->get('slt_attributes') ? '' : 'class="inputbox"';
		$displayData['htmlInputType']	= $this->settings->get('html_inputtype', 'text');
		
		return $this->renderLayout('singlelinetext', $displayData, $translatedFields, $contentTypeSettings);			
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
		$value 		= isset($this->values['VALUE']) ? $this->values['VALUE'] : '';
		$fieldId 	= $this->internal['fieldcontentid'];
		$action 	= ($value != '') ? (($fieldId) ? 'UPDATE' : 'INSERT') : (($fieldId) ? 'DELETE' : '');
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
				
		$pattern = $this->settings->get('slt_pattern_server');
		
		if(!empty($pattern))
		{
			if(!preg_match($pattern, $this->values['VALUE']))
			{
				throw new Exception($this->settings->get('slt_pattern_message') != '' ? $this->settings->get('slt_pattern_message') : sprintf(JText::_('COM_FORM2CONTENT_VALIDATION_PATTERN_MESSAGE_EMPTY'), $this->title));
			}
		}
	}

	public function getClientSideValidationScript(&$validationCounter, $form)
	{
		$script = parent::getClientSideValidationScript($validationCounter, $form);
		
		$pattern = $this->settings->get('slt_pattern_client');
		
		if(!empty($pattern))
		{
			$message = $this->settings->get('slt_pattern_message', sprintf(JText::_('COM_FORM2CONTENT_VALIDATION_PATTERN_MESSAGE_EMPTY'), JText::_($this->title)));
			$script .= 'Form2Content.Validation.CheckPatternField(\'t'.$this->id.'\', \''.$pattern.'\', \''.sprintf(JText::_($message, true), JText::_($this->title)).'\');';
		}
		
		return $script;
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
		$templateEngine->addVar($this->fieldname, $this->stringHTMLSafe($this->values['VALUE']));
		$templateEngine->addVar($this->fieldname .'_RAW', $this->values['VALUE']);
	}
	
	public function getTemplateParameterNames()
	{
		$names = array(StringHelper::strtoupper($this->fieldname).'_RAW');		
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