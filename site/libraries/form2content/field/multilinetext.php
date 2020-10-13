<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldMultiLineText extends F2cFieldBase
{	
	function __construct($field)
	{
		$this->reset();
		parent::__construct($field);
	}
	
	public function getPrefix()
	{
		return 'mlt';
	}
	
	public function render($translatedFields, $contentTypeSettings, $parms = array(), $form, $formId)
	{
		$displayData 	= array();
		$charsRemaining	= 0;
		
		if(!count($parms))
		{
			$parms = JFactory::getApplication()->isClient('site') ? array('cols="50" rows="5" style="width:500px; height:120px"') : array('cols="100" rows="6"');
		}

		$attribs		= '';
		$maxNumChars 	= (int)$this->settings->get('mlt_max_num_chars');		
		$value 			= $this->values['VALUE'];
		
		if((int)$this->settings->get('mlt_num_rows')) $attribs .= ' rows="'.(int)$this->settings->get('mlt_num_rows').'"';
		if((int)$this->settings->get('mlt_num_cols')) $attribs .= ' cols="'.(int)$this->settings->get('mlt_num_cols').'"';		
		if($this->settings->get('mlt_attributes')) $attribs .= ' '.$this->settings->get('mlt_attributes');

		if(!$attribs)
		{
			$attribs = $parms[0];
			$attribs .= ' class="text_area"';			
		}

		if($maxNumChars)
		{
			if(function_exists('mb_substr_count') && function_exists('mb_substr') && function_exists('mb_strlen'))
			{
				$numNewLines = mb_substr_count($value, "\r\n", 'UTF-8');
				$charsRemaining = $maxNumChars + $numNewLines - mb_strlen($value, 'UTF-8');			
				$fieldValue = mb_substr($value, 0, $maxNumChars + $numNewLines, 'UTF-8');
			}
			else
			{
				$numNewLines = substr_count($value, "\r\n");
				$charsRemaining = $maxNumChars + $numNewLines - strlen($value);			
				$fieldValue = substr($value, 0, $maxNumChars + $numNewLines);
			}
			
			if($charsRemaining < 0)
			{
				$charsRemaining = 0;
			}
		}
		
		$displayData['attribs']			= $attribs;
		$displayData['maxNumChars']		= $maxNumChars;
		$displayData['charsRemaining']	= $charsRemaining;
		
		return $this->renderLayout('multilinetext', $displayData, $translatedFields, $contentTypeSettings);						
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
		
		if((int)$this->settings->get('mlt_max_num_chars'))
		{
			if(function_exists('mb_substr_count') && function_exists('mb_substr'))
			{
				$numNewLines = mb_substr_count ($value, "\r\n", 'UTF-8');
				$value = mb_substr($value, 0, (int)$this->settings->get('mlt_max_num_chars') + $numNewLines, 'UTF-8');
			}
			else
			{
				$numNewLines = substr_count ($value, "\r\n");
				$value = substr($value, 0, (int)$this->settings->get('mlt_max_num_chars') + $numNewLines);
			}
		}
							
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
		$templateEngine->addVar($this->fieldname, nl2br($this->stringHTMLSafe($this->values['VALUE'])));
		$templateEngine->addVar($this->fieldname .'_RAW', nl2br($this->values['VALUE']));
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