<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldJoomlaCreatedBy extends F2cFieldBase
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
		return $form->getInput('created_by');
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
		return $this->title ? parent::renderLabel($translatedFields) : $form->getLabel('created_by');
	}
	
	public function validate(&$data, $item)
	{
		// skip check when running in cron mode
		if(isset($data['isCron'])) return;
		
		$user 	= JFactory::getUser();
		$isNew	= empty($data['id']);

		// get default value for title
		if(!$this->isFieldVisible())
		{
			$data['created_by'] = $isNew ? $user->id : $item->created_by;
		}				
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
      	$xmlFieldContent->value = $this->resolveUserid($form->created_by);
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
		$data['created_by'] = $this->resolveUsername((string)$xmlField->contentSingleTextValue->value);
	}	
		
	public function addTemplateVar($templateEngine, $form)
	{
		$usrTmp = JFactory::getUser($form->created_by);
		
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname), HtmlHelper::stringHTMLSafe($usrTmp->name));
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname).'_USERNAME', HtmlHelper::stringHTMLSafe($usrTmp->username));
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname).'_EMAIL', $usrTmp->email);	
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname).'_ID', $form->created_by);
		
		// legacy parameter
		$templateEngine->addVar('JOOMLA_AUTHOR', HtmlHelper::stringHTMLSafe($usrTmp->name));
		$templateEngine->addVar('JOOMLA_AUTHOR_USERNAME', HtmlHelper::stringHTMLSafe($usrTmp->username));
		$templateEngine->addVar('JOOMLA_AUTHOR_EMAIL', $usrTmp->email);	
		$templateEngine->addVar('JOOMLA_AUTHOR_ID', $form->created_by);
	}
	
	public function getTemplateParameterNames()
	{
		$uFieldName = StringHelper::strtoupper($this->fieldname);		
		$names 		= array($uFieldName.'_USERNAME', $uFieldName.'_EMAIL', $uFieldName.'_ID');
				
		return array_merge($names, parent::getTemplateParameterNames());		
	}

	public function setData($data)
	{
	}

	public function getCssClass()
	{
		return 'f2c_created_by';
	}	
	
	public function getClientSideValidationScript(&$validationCounter, $form)
	{		
		return '';
	}	
	
	private function resolveUserid($userid)
	{
		static $usernames = array();
		
		if(array_key_exists($userid, $usernames))
		{
			return $usernames[$userid];
		}
		else 
		{
			$user = JUser::getInstance($userid);
			$usernames[$userid] = $user->username;
			
			return $user->username;			
		}
	}
	
	private function resolveUsername($username)
	{
		static $usernames = array();
		
		if(array_key_exists($username, $usernames))
		{
			return $usernames[$username];
		}
		else 
		{
			$userId = JUserHelper::getUserId($username);

			if($userId)
			{
				$usernames[$username] = $userId;
				return $userId;
			}
			else 
			{
				return 0;
			}
		}
	}
	
	public function setDefaultValue(&$item)
	{
		$item->created_by = JFactory::getUser()->id;
	}
	
	public function prepareValidation(&$item, &$data, $isNew)
	{
		$data['created_by'] = $isNew ? $this->settings->get('default') : $item->created_by;
	}
}
?>