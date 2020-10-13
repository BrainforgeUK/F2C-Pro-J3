<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldJoomlaCategory extends F2cFieldBase
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
		$app = JFactory::getApplication();
		
		// Check if a default category is selected
		$defaultCategoryId = (int)$this->settings->get('default');

		if($defaultCategoryId != -1)
		{
			switch((int)$this->settings->get('behaviour'))
			{
				case 0:
					// The category is fixed
					$form->setFieldAttribute('catid', 'readonly', 'true');
					$form->setFieldAttribute('catid', 'rootCategoryId', $defaultCategoryId);
					$form->setFieldAttribute('catid', 'value', $defaultCategoryId);
					$form->setFieldAttribute('catid', 'behaviour', 2);
					break;
				case 1:
					// The category is the root category
					$form->setFieldAttribute('catid', 'rootCategoryId', $defaultCategoryId);
					$form->setFieldAttribute('catid', 'behaviour', 1);
					break;
				case 2:
					// The category is the root category -> select all below too
					$form->setFieldAttribute('catid', 'rootCategoryId', $defaultCategoryId);
					$form->setFieldAttribute('catid', 'behaviour', 3);
					break;		
			}	
		}		

		if($this->settings->get('use_joomla_acl', 0) && $app->isClient('site'))
		{
			// Use Joomla ACL to build the list of categories
			$form->setFieldAttribute('catid', 'action', 1);
		}
		
		return $form->getInput('catid');	
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
		return $this->title ? parent::renderLabel($translatedFields) : $form->getLabel('catid');
	}
	
	public function validate(&$data, $item)
	{ 
		// skip check when running in cron mode
		if(isset($data['isCron'])) return;
		
		$isNew	= empty($data['id']);
		
		// get default value for title
		if(!$this->isFieldVisible())
		{
			$data['catid'] = $isNew ? $this->settings->get('default') : $item->catid;
		}				
	}
	
	public function export($xmlFields, $form)
	{
		static $dicCatId = null;
		
		if($dicCatId == null)
		{
			$dicCatId = $this->loadDicCatId();
		}
		
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
      	$xmlFieldContent->value = $dicCatId[$form->catid];
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
		$data['catid'] = $this->ConvertCatAliasPath((string)$xmlField->contentSingleTextValue->value);
	}	
		
	public function addTemplateVar($templateEngine, $form)
	{
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname).'_ID', $form->catid);
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname).'_TITLE', $form->catTitle);
		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname).'_ALIAS', $form->catAlias);
		
		// legacy parameters
		$templateEngine->addVar('JOOMLA_CATEGORY_ID', $form->catid);
		$templateEngine->addVar('JOOMLA_CATEGORY_TITLE', $form->catTitle);
		$templateEngine->addVar('JOOMLA_CATEGORY_ALIAS', $form->catAlias);
	}
	
	public function getTemplateParameterNames()
	{
		$uFieldName = StringHelper::strtoupper($this->fieldname);
		
		return array($uFieldName.'_ID', $uFieldName.'_TITLE', $uFieldName.'_ALIAS');
	}

	public function setData($data)
	{
	}	
	
	public function getCssClass()
	{
		return 'f2c_catid';
	}	
	
	public function getClientSideValidationScript(&$validationCounter, $form)
	{		
		$script = '';
		
		if($this->settings->get('requiredfield'))
		{
			$script = 'arrValidation['.$validationCounter++.']=new Array(\'jform_catid\',\''.$this->getFieldName().'\',\''.addslashes($this->getRequiredFieldErrorMessage()).'\');';
		}
		
		return $script;	
	}	
	
	private function loadDicCatId()
	{
		$pathList 	= array();
		$dicCatId	= array();
		$db 		= JFactory::getDbo();		
		$query 		= $db->getQuery(true);
		
		$query->select('a.id, a.alias, a.level');
		$query->from('#__categories AS a');
		$query->where('a.parent_id > 0');
		$query->where('extension = ' . $db->quote('com_content'));
		$query->order('a.lft');
		
		$db->setQuery($query);
		
		$categories = $db->loadObjectList();

		if(count($categories))
		{
			foreach($categories as $category)
			{
				if($category->level == 1)
				{
					// reset pathlist
					$pathList = array();
					$pathList[0] = '';
				}
				
				$pathList[$category->level] = $pathList[$category->level - 1] . '/' . $category->alias;
				$dicCatId[$category->id] = $pathList[$category->level];
			}
		}
		
		return $dicCatId;
	}
	
	private function ConvertCatAliasPath($path)
	{
		static $dicCatAliasPath = null;
		
		if(!$dicCatAliasPath)
		{
			$db 	= JFactory::getDbo();
			$query 	= $db->getQuery(true);
			
			$query->select('a.id, a.alias, a.level');
			$query->from('#__categories AS a');
			$query->where('a.parent_id > 0');
			$query->where('extension = ' . $db->quote('com_content'));
			$query->order('a.lft');
			
			$db->setQuery($query);
			
			$categories = $db->loadObjectList();

			if(count($categories))
			{
				foreach($categories as $category)
				{
					if($category->level == 1)
					{
						// reset pathlist
						$pathList = array();
						$pathList[0] = '';
					}
					
					$pathList[$category->level] 					= $pathList[$category->level - 1] . '/' . $category->alias;
					$dicCatAliasPath[$pathList[$category->level]]	= $category->id;
				}
			}
		}
		
		return (int)$dicCatAliasPath[$path];
	}
	
	public function setDefaultValue(&$item)
	{
		$item->catid = $this->settings->get('default', 0);
	}
	
	public function prepareValidation(&$item, &$data, $isNew)
	{
		$data['catid'] = $isNew ? $this->settings->get('default', 0) : $item->catid;
	}
	
	public function canBeHiddenInFrontEnd()
	{
		if($this->settings->get('requiredfield'))
		{
			// when required, a default should be provided
			if($this->settings->get('default', -1) == -1)
			{
				return false;
			}
		}
		
		return true;
	}
	
	protected function getLabelFor()
	{
		return 'jform_catid';
	}
}	
?>