<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldJoomlaPublishDown extends F2cFieldBase
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
		return $form->getInput('publish_down');		
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
		return $this->title ? parent::renderLabel($translatedFields) : $form->getLabel('publish_down');
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
				if($data['publish_down'])
				{
					if($date = F2cDateTimeHelper::ParseDate($data['publish_down'], '%Y-%m-%d'))
					{
						$data['publish_down'] = $date->toSql();			
					}
					else
					{
						$translatedDateFormat = F2cDateTimeHelper::getTranslatedDateFormat();
						throw new Exception(sprintf(JText::_('COM_FORM2CONTENT_ERROR_DATE_FIELD_INCORRECT_DATE'), JText::_('COM_FORM2CONTENT_FIELD_PUBLISH_DOWN_LABEL'), $translatedDateFormat));
					}
				}			
			}
			else
			{
				$isNew	= empty($data['id']);
				$data['publish_down'] = $isNew ? '' : $item->publish_down;						
			}
		}	
		
		if(empty($data['publish_down']))
		{
			// no publish down date was provided => set to null date
			$data['publish_down'] = JFactory::getDbo()->getNullDate();
		}
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
     	$xmlValue = $xmlFieldContent->addChild('value');
      	$xmlValue->addDate($form->publish_down);
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
		try 
		{
			$data['publish_down'] = F2cHelperDatetime::emptyOrIso8601DateToSql((string)$xmlField->contentSingleTextValue->value);
		}
		catch(Exception $e)
		{
			throw new Exception(sprintf(JText::_('COM_FORM2CONTENT_ERROR_DATE_FIELD_INCORRECT_DATE'), JText::_('COM_FORM2CONTENT_FIELD_PUBLISH_DOWN_LABEL'), F2cDateTimeHelper::getTranslatedDateFormat()));
		}
	}	
		
	public function addTemplateVar($templateEngine, $form)
	{
		$db 				= JFactory::getDBO();
		$dateFormat			= str_replace('%', '', F2cFactory::getConfig()->get('date_format') . ' H:i:s');
		$nullDate 			= $db->getNullDate();
		$publishDownUnix	= ''; // Unix timestamp
		$finishPublishing 	= '';
		
		if($form->publish_down != $nullDate)
		{
			$finishPublishing 	= JHTML::_('date', $form->publish_down, $dateFormat, true, true);
			$tmp 				= new JDate($form->publish_down);
			$publishDownUnix 	= $tmp->toUnix(); 
		}

		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname), $finishPublishing);
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname).'_RAW', $publishDownUnix);	
		
		// legacy parameters
		$templateEngine->addVar('JOOMLA_PUBLISH_DOWN', $finishPublishing);
		$templateEngine->addVar('JOOMLA_PUBLISH_DOWN_RAW', $publishDownUnix);	
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
		return 'f2c_publish_down';
	}	
	
	public function getClientSideValidationScript(&$validationCounter, $form)
	{		
		if($this->isFieldVisible())
		{
			$label 					= JText::_($form->getFieldAttribute('publish_down', 'label'), true);
	  		$translatedDateFormat 	= F2cDateTimeHelper::getTranslatedDateFormat();
			$dateFormat				= $this->f2cConfig->get('date_format');
			
			return 'Form2Content.Validation.CheckDateField(\'jform_publish_down\', \''.$dateFormat.'\', \''.$label.'\', \''.$translatedDateFormat.'\');';
		}
		
		return '';
	}	
	
	public function setDefaultValue(&$item)
	{
		$item->publish_down	= JFactory::getDbo()->getNullDate();
	}
	
	public function prepareValidation(&$item, &$data, $isNew)
	{
		if(array_key_exists('publish_down', $data) && $data['publish_down'])
		{
			$translatedDateFormat = F2cDateTimeHelper::getTranslatedDateFormat();
			
			if($date = F2cDateTimeHelper::ParseDate($data['publish_down'], '%Y-%m-%d'))
			{
				$data['publish_down'] = $date->toSql();			
			}
			else
			{
				JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_FORM2CONTENT_ERROR_DATE_FIELD_INCORRECT_DATE'), JText::_('COM_FORM2CONTENT_FIELD_PUBLISH_DOWN_LABEL'), $translatedDateFormat), 'error');
			}
		}		
	}
	
	public function canBeHiddenInFrontEnd()
	{
		return true;
	}
}
?>