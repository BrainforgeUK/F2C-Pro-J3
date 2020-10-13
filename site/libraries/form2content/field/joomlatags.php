<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldJoomlaTags extends F2cFieldBase
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
		return $form->getInput('tags');	
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
		return $this->title ? parent::renderLabel($translatedFields) : $form->getLabel('tags');
	}
	
	public function validate(&$data, $item)
	{
		// skip check when running in cron mode
		if(isset($data['isCron'])) return;
		
		$isNew	= empty($data['id']);

		// get default value for title
		if(!$this->isFieldVisible())
		{
			$data['tags'] = $isNew ? '' : $item->tags;
		}				
	}
	
	public function export($xmlFields, $form)
	{ 
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentMultipleTextValue');
      	$xmlFieldValues = $xmlFieldContent->addChild('values');
      						
      	if(count($form->tags))
      	{
      		foreach ($form->tags as $tag) 
      		{
      			$xmlFieldValues->addChild('value', $this->valueReplace($this->getTagPathById($tag)));
      		}
      	}
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
      	$this->values['VALUE'] = array();
      					
      	if(count($xmlField->contentMultipleTextValue->values->children()))
      	{
      		foreach($xmlField->contentMultipleTextValue->values->children() as $xmlValue)
      		{
      			$this->values['VALUE'][] = $this->getTagIdBypath((string)$xmlValue);
      		}
      	}
	}
		
	public function addTemplateVar($templateEngine, $form)
	{
		// Add the tags to the template
		$tags = array();
		
		if(count($form->tags))
		{
			$tmpTags = $form->tags;
			
			// Strip new tags from array
			foreach($tmpTags as $key => $tag)
			{
				if(!is_numeric($tag))
				{
					unset($tmpTags[$key]);
				}
			}			
			
			if(count($tmpTags))
			{
				foreach($tmpTags as $tagId)
				{
					$tags[$tagId] = $this->getTagTitleById($tagId);
				}
			}
		}

		$templateEngine->addVar(StringHelper::strtoupper($this->fieldname), $tags);
		
		// legacy parameter
		$templateEngine->addVar('F2C_TAGS', $tags);
	}
	
	public function setData($data)
	{
	}	
	
	public function getCssClass()
	{
		return 'f2c_tags';
	}
	
	public function getClientSideValidationScript(&$validationCounter, $form)
	{		
		return '';
	}
	
	private function getTagTitleById($id)
	{
		static $dicTags;
		
		if(!$dicTags)
		{
			// load all tag ids and their corresponding paths
			$db 	= JFactory::getdbo();
			$query 	= $db->getQuery(true);
			
			$query->select('id, title')->from('#__tags');			
			$db->setQuery($query);
			
			$dicTags = $db->loadAssocList('id', 'title');
		}

		return $dicTags[$id];
	}
	
	private function getTagPathById($id)
	{
		static $dicTags;
		
		if(!$dicTags)
		{
			// load all tag ids and their corresponding paths
			$db 	= JFactory::getdbo();
			$query 	= $db->getQuery(true);
			
			$query->select('id, path')->from('#__tags');			
			$db->setQuery($query);
			
			$dicTags = $db->loadAssocList('id', 'path');
		}

		return $dicTags[$id];
	}
	
	private function getTagIdBypath($path)
	{
		static $dicTags = null;
		
		if(!$dicTags)
		{
			// load all tag paths and their corresponding ids
			$db 	= JFactory::getDbo();
			$query 	= $db->getQuery(true);
			
			$query->select('path, id')->from('#__tags');
			$db->setQuery($query);
			
			$dicTags = $db->loadAssocList('path', 'id');			
		}
		
		return (int)$dicTags[$path];
	}
	
	public function setDefaultValue(&$item)
	{
		$item->tags	= array();
	}
	
	public function canBeHiddenInFrontEnd()
	{
		return true;
	}
}
?>