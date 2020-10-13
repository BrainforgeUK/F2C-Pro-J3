<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldJoomlaCreated extends F2cFieldBase
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
		return $form->getInput('created');		
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
		return $this->title ? parent::renderLabel($translatedFields) : $form->getLabel('created');
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
				if($data['created'])
				{
					if($date = F2cDateTimeHelper::ParseDate($data['created'], '%Y-%m-%d'))
					{
						$data['created'] = $date->toSql();			
					}
					else
					{
						$translatedDateFormat = F2cDateTimeHelper::getTranslatedDateFormat();
						throw new Exception(sprintf(JText::_('COM_FORM2CONTENT_ERROR_DATE_FIELD_INCORRECT_DATE'), JText::_('COM_FORM2CONTENT_FIELD_CREATED_LABEL'), $translatedDateFormat));
					}
				}			
			}
			else
			{				
				$isNew	= empty($data['id']);
				$data['created'] = $isNew ? $dateNow : $item->created;						
			}
		}	
		
		if(empty($data['created']))
		{
			// no created date was provided => set to current date and time
			$data['created'] = $dateNow;
		}
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
      	$xmlValue = $xmlFieldContent->addChild('value');
      	$xmlValue->addDate($form->created);
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
		if($data['id'] && $this->f2cConfig->get('import_keep_created_date', 1))
		{
			// update of an existing form, maintain the created date
			$form 				= new Form2ContentModelForm(array('ignore_request' => true));
			$existingForm 		= $form->getItem($data['id']);
			$data['created'] 	= $existingForm->created;
		}
		else 
		{
			try
			{
				$data['created'] = F2cHelperDatetime::emptyOrIso8601DateToSql((string)$xmlField->contentSingleTextValue->value);	
			}
			catch(Exception $e)
			{
				throw new Exception(sprintf(JText::_('COM_FORM2CONTENT_ERROR_DATE_FIELD_INCORRECT_DATE'), JText::_('COM_FORM2CONTENT_FIELD_CREATED_LABEL'), F2cDateTimeHelper::getTranslatedDateFormat()));
			}
		}
	}	
		
	public function addTemplateVar($templateEngine, $form)
	{
		$app				= JFactory::getApplication();
		$db 				= JFactory::getDBO();
		$dateFormat			= str_replace('%', '', $this->f2cConfig->get('date_format') . ' H:i:s');
		$nullDate 			= $db->getNullDate();
		$createdUnix		= ''; // Unix timestamp
		$creationDate 		=  JHTML::_('date', $form->created, $dateFormat);
		$dateCreated		= new JDate($form->created);
		
		if($form->created != $nullDate)
		{
			$tmp 			= new JDate($form->created);
			$createdUnix 	= $tmp->toUnix(); 
		}

		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname), $creationDate);
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname).'_RAW', $createdUnix);	
		
		// legacy parameters
		$templateEngine->addVar('JOOMLA_CREATED', $creationDate);
		$templateEngine->addVar('JOOMLA_CREATED_RAW', $createdUnix);	
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
		return 'f2c_created';
	}	
	
	public function getClientSideValidationScript(&$validationCounter, $form)
	{		
		if($this->isFieldVisible())
		{
			$label 					= JText::_($form->getFieldAttribute('created', 'label'), true);
	  		$translatedDateFormat 	= F2cDateTimeHelper::getTranslatedDateFormat();
			$dateFormat				= $this->f2cConfig->get('date_format');
			
			return 'Form2Content.Validation.CheckDateField(\'jform_created\', \''.$dateFormat.'\', \''.$label.'\', \''.$translatedDateFormat.'\');';
		}
		
		return '';
	}

	public function setDefaultValue(&$item)
	{
		$date			= JFactory::getDate();		
		$item->created 	= $date->toSql();
	}
	
	public function prepareValidation(&$item, &$data, $isNew)
	{
		if(array_key_exists('created', $data) && $data['created'])
		{
			$translatedDateFormat = F2cDateTimeHelper::getTranslatedDateFormat();
			
			if($date = F2cDateTimeHelper::ParseDate($data['created'], '%Y-%m-%d'))
			{
				$data['created'] = $date->toSql();			
			}
			else
			{
				JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_FORM2CONTENT_ERROR_DATE_FIELD_INCORRECT_DATE'), JText::_('COM_FORM2CONTENT_FIELD_CREATED_LABEL'), $translatedDateFormat), 'error');
			}
		}		
	}
	
	public function canBeHiddenInFrontEnd()
	{
		return true;
	}
}
?>