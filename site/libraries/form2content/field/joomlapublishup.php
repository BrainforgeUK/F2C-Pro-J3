<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldJoomlaPublishUp extends F2cFieldBase
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
		return $form->getInput('publish_up');		
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
		return $this->title ? parent::renderLabel($translatedFields) : $form->getLabel('publish_up');
	}
	
	public function validate(&$data, $item)
	{
		// Get the current date and time
		$dateNow = JFactory::getDate('now', 'UTC');
		$dateNow->setTimezone(new DateTimeZone(JFactory::getConfig()->get('offset')));
		$dateNow = $dateNow->toSql();						
		
		// skip checks for cron import
		if(!isset($data['isCron']))
		{		
			// check if we need to validate the date as entered by the user
			if($this->isFieldVisible())
			{
				if($data['publish_up'])
				{
					if($date = F2cDateTimeHelper::ParseDate($data['publish_up'], '%Y-%m-%d'))
					{
						$data['publish_up'] = $date->toSql();			
					}
					else
					{
						$translatedDateFormat = F2cDateTimeHelper::getTranslatedDateFormat();
						throw new Exception(sprintf(JText::_('COM_FORM2CONTENT_ERROR_DATE_FIELD_INCORRECT_DATE'), JText::_('COM_FORM2CONTENT_FIELD_PUBLISH_UP_LABEL'), $translatedDateFormat));
					}
				}			
			}
			else
			{
				$isNew	= empty($data['id']);
				$data['publish_up'] = $isNew ? '' : $item->publish_up;						
			}
		}	
		
		if(empty($data['publish_up']))
		{
			// no publish up date was provided => set to current date and time or null date, depending on the state
			$data['publish_up'] = ($data['state'] == F2C_STATE_PUBLISHED) ? $dateNow : JFactory::getDbo()->getNullDate();
		}
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
      	$xmlValue = $xmlFieldContent->addChild('value');
      	$xmlValue->addDate($form->publish_up);
      	
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
		try 
		{
			$data['publish_up'] = F2cHelperDatetime::emptyOrIso8601DateToSql((string)$xmlField->contentSingleTextValue->value);	
		}
		catch(Exception $e)
		{
			throw new Exception(sprintf(JText::_('COM_FORM2CONTENT_ERROR_DATE_FIELD_INCORRECT_DATE'), JText::_('COM_FORM2CONTENT_FIELD_PUBLISH_UP_LABEL'), F2cDateTimeHelper::getTranslatedDateFormat()));
		}
	}	
		
	public function addTemplateVar($templateEngine, $form)
	{
		$db 				= JFactory::getDBO();
		$dateFormat			= str_replace('%', '', F2cFactory::getConfig()->get('date_format') . ' H:i:s');
		$nullDate 			= $db->getNullDate();
		$publishUpUnix		= ''; // Unix timestamp
		$startPublishing 	= '';
		
		if($form->publish_up != $nullDate)
		{
			$startPublishing 	= JHTML::_('date', $form->publish_up, $dateFormat, true, true);
			$tmp 				= new JDate($form->publish_up);
			$publishUpUnix 		= $tmp->toUnix(); 
		}
		
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname), $startPublishing);
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname).'_RAW', $publishUpUnix);	
		
		// legacy parameters
		$templateEngine->addVar('JOOMLA_PUBLISH_UP', $startPublishing);
		$templateEngine->addVar('JOOMLA_PUBLISH_UP_RAW', $publishUpUnix);	
	}
	
	public function getTemplateParameterNames()
	{
		$names = array(StringHelper::strtoupper($this->fieldname).'_RAW');		
		return array_merge($names, parent::getTemplateParameterNames());		
	}
	
	public function setData($data)
	{
	}	
	
	public function getCssClass()
	{
		return 'f2c_publish_up';
	}

	public function getClientSideValidationScript(&$validationCounter, $form)
	{		
		if($this->isFieldVisible())
		{
			$label 					= JText::_($form->getFieldAttribute('publish_up', 'label'), true);
	  		$translatedDateFormat 	= F2cDateTimeHelper::getTranslatedDateFormat();
			$dateFormat				= $this->f2cConfig->get('date_format');
			
			return 'Form2Content.Validation.CheckDateField(\'jform_publish_up\', \''.$dateFormat.'\', \''.$label.'\', \''.$translatedDateFormat.'\');';
		}
		
		return '';
	}	
	
	public function setDefaultValue(&$item)
	{
		$date				= JFactory::getDate();		
		$item->publish_up 	= $date->toSql();
	}
	
	public function prepareValidation(&$item, &$data, $isNew)
	{
		if(array_key_exists('publish_up', $data) && $data['publish_up'])
		{
			$translatedDateFormat = F2cDateTimeHelper::getTranslatedDateFormat();
			
			if($date = F2cDateTimeHelper::ParseDate($data['publish_up'], '%Y-%m-%d'))
			{
				$data['publish_up'] = $date->toSql();			
			}
			else
			{
				JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_FORM2CONTENT_ERROR_DATE_FIELD_INCORRECT_DATE'), JText::_('COM_FORM2CONTENT_FIELD_PUBLISH_UP_LABEL'), $translatedDateFormat), 'error');
			}
		}		
	}
	
	public function canBeHiddenInFrontEnd()
	{
		return true;
	}
}
?>