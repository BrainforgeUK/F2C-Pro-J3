<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldDatePicker extends F2cFieldBase
{	
	function __construct($field)
	{
		$this->reset();
		parent::__construct($field);
	}
	
	public function getPrefix()
	{
		return 'dat';
	}
	
	public function render($translatedFields, $contentTypeSettings, $parms = array(), $form, $formId)
	{
		$displayData			= array();
		$displayValue 		 	= $this->values['VALUE'];
		$displayData['form'] 	= $form;
		
		//$form->set
		
		if($displayValue)
		{
			$user 			= \JFactory::getUser();
			$config 		= \JFactory::getConfig();
			$timezone 		= new \DateTimeZone($user->getParam('timezone', $config->get('offset')));
			
			// Invert the offset and force the sign in front of it	
			$offsetHours = sprintf('%+d', (-1 * $timezone->getOffset(new \DateTime())/3600));
			
			// Apply the offset to the datetime value
			$date = \JFactory::getDate($displayValue, 'UTC');
			$date->setTimezone($timezone);
			$date->modify($offsetHours.' hours');
			
			$dateFormat = str_replace('%','', $this->f2cConfig->get('date_format')) . ' H:i:s';
			$value = $date->format($dateFormat, true);
			$displayValue =	$date->format('Y-m-d H:i:s');	
		}
		
		$displayData['attributes']		= $this->settings->get('dat_attributes') ? $this->settings->get('dat_attributes') : 'inputbox';
		$displayData['displayValue']	= $displayValue;

		return $this->renderLayout('datepicker', $displayData, $translatedFields, $contentTypeSettings);						
	}
	
	public function prepareSubmittedData($formId)
	{
		$jinput = JFactory::getApplication()->input;
		
		$this->internal['fieldcontentid'] 	= $jinput->getInt('hid'.$this->elementId);		
		$this->values['VALUE'] 				= '';				
		$formData 							= new JRegistry($jinput->get('jform', '', 'array'));
		$value 								= $formData->get($this->elementId);
		
		if($value)
		{
			$date = F2cDateTimeHelper::ParseDate($value, $this->f2cConfig->get('date_format'));
			$this->values['VALUE'] = ($date) ? $date->toISO8601() : '';						
		}
		
		return $this;
	}
	
	public function store($formid)
	{
		$content	= array();
		$fieldId 	= $this->internal['fieldcontentid'];
		$value 		= $this->values['VALUE'];
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
	
	public function getClientSideValidationScript(&$validationCounter, $form)
	{
		$script = parent::getClientSideValidationScript($validationCounter, $form);
		
		$dateFormat				= $this->f2cConfig->get('date_format');
	  	$translatedDateFormat 	= F2cDateTimeHelper::getTranslatedDateFormat();
	  	$label					= JText::_($this->title, true);
				
		$script .= 'Form2Content.Validation.CheckDateField(\'jform_'.$this->elementId.'\', \''.$dateFormat.'\', \''.$label.'\', \''.$translatedDateFormat.'\');';
		
		return $script;
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentDate');
      	$xmlValue = $xmlFieldContent->addChild('value');
      	$xmlValue->addDate($this->values['VALUE']);
    }
    
	public function import($xmlField, $existingInternalData, &$data)
	{
		$this->values['VALUE'] = (string)$xmlField->contentDate->value;
		$this->internal['fieldcontentid'] = $data['id'] ? $existingInternalData['fieldcontentid'] : 0;
	}
	
	public function addTemplateVar($templateEngine, $form)
	{
		$value 			= '';
		$unixTimestamp 	= '';
		$dateFormat		= str_replace('%', '', $this->f2cConfig->get('date_format'));
		
		if($this->values['VALUE'])
		{
			$date 			= new JDate($this->values['VALUE']);
			$value			= $date->format($dateFormat);
			$unixTimestamp	= $date->toUnix();
		}

		$templateEngine->addVar($this->fieldname, $value);
		$templateEngine->addVar($this->fieldname . '_RAW', $unixTimestamp);
	}
	
	public function getTemplateParameterNames()
	{
		$names = array(	StringHelper::strtoupper($this->fieldname).'_RAW');
		
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

	public function preprocessForm(JForm $form)
	{
		$xml = '<field name="t'.$this->id.'" type="F2cCalendar" label=""
			description="" class="inputbox" size="22" value="" showtime="false"
			format="%Y-%m-%d" filter="Form2ContentHelper::filterUserUtcWithFormat" classiclayout="false" />';
		
		$xmlElement = new SimpleXMLElement($xml);
		
		$form->setField($xmlElement);
	}
}
?>