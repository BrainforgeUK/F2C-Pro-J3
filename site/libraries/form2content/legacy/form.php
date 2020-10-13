<?php
class F2cLegacyForm
{
	private $db;
	private $dicContentTypeTitle 		= array();
	private $dicTags 					= array();
	private $dicCatId					= array();
	private $dicViewingAccessLevelTitle	= array();
	
	public function __construct($db)
	{
		$this->db = $db;
		
		// Load a dictionary of Content Types
		$query = $this->db->getQuery(true);
		
		$query->select('id, title');
		$query->from('#__f2c_project');
		
		$this->db->setQuery($query);

		$this->dicContentTypeTitle 	= $this->db->loadAssocList('title', 'id');
		
		// load all tag paths and their corresponding ids
		$query = $this->db->getQuery(true);
		$query->select('path, id')->from('#__tags');
		
		$this->db->setQuery($query);		
		$this->dicTags = $this->db->loadAssocList('path', 'id');	

		$pathList = array();
		
		$query = $this->db->getQuery(true);
		
		$query->select('a.id, a.alias, a.level');
		$query->from('#__categories AS a');
		$query->where('a.parent_id > 0');
		$query->where('extension = ' . $this->db->quote('com_content'));
		$query->order('a.lft');
		
		$this->db->setQuery($query);
		
		$categories = $this->db->loadObjectList();

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
				
				$pathList[$category->level] 						= $pathList[$category->level - 1] . '/' . $category->alias;
				$this->dicCatAliasPath[$pathList[$category->level]]	= $category->id;
				$this->dicCatId[$category->id] 						= $pathList[$category->level];
			}
		}
		
		$query = $this->db->getQuery(true);
		
		$query->select('id, title');
		$query->from('#__viewlevels');
		$this->db->setQuery($query);
		
		$this->dicViewingAccessLevelTitle	= $this->db->loadAssocList('title', 'id');
	}
	
	public function importXml($xml)
	{
		JLog::add('Importing articles in legacy XML file', JLog::INFO, 'com_form2content');
		
		$results 	= array('inserted' => 0, 'updated' => 0, 'deleted' => 0, 'trashed' => 0);
		
		foreach ($xml as $xmlForm)
		{
			$form 				= new Form2ContentModelForm(array('ignore_request' => true));
			$attribs 			= array();
			$metadata			= array();
			$tags				= array();
			$rules				= array();
			$data 				= array();
			$fieldData 			= array();
			$tmpFiles			= array();
			$importWriteMode 	= F2cFactory::getConfig()->get('import_write_action', 1);
			$model				= new Form2ContentModelForm(array('ignore_request' => true));

			// Resolve the Content Type
			$contentTypeId = $this->dicContentTypeTitle[(string)$xmlForm->contenttype];

			if(!$contentTypeId)
			{
				throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_CONTENTTYPE_NOT_RESOLVED'), (string)$xmlForm->contenttype);
			}
			
			$contentType = F2cFactory::getContentType($contentTypeId);
			
			if($importWriteMode == F2C_IMPORT_WRITEMODE_CREATENEW)
			{
				// Always create a new F2C Article
				$formId = 0;
			}
			else 
			{
				// Check if an alternate key was specified
				if($xmlForm->id->attributes()->fieldname)
				{
					// Find the id of the form through the alternate key
					$query = $this->db->getQuery(true);
					
					$query->select('frm.id');
					$query->from('#__f2c_form frm');
					$query->join('inner', '#__f2c_fieldcontent flc on frm.id = flc.formid');
					$query->join('inner', '#__f2c_projectfields pfl on flc.fieldid = pfl.id');
					$query->where('flc.content = ' . $this->db->quote((string)$xmlForm->id));						
					$query->where('pfl.fieldname = ' . $this->db->quote((string)$xmlForm->id->attributes()->fieldname));
					$query->where('frm.state in (0,1,-2)');

					$this->db->setQuery($query);
					$result = $this->db->loadResult();
					// If the alternate key was found use that form id						
					$formId = $result ? $result : 0;
				}
				else 
				{
					
					// verify that this article exists for the specified content type									
					$formTemp = $model->getItem((int)$xmlForm->id);
					
					if($formTemp->projectid != $contentTypeId)
					{
						// there's no such form...create a new one
						$formId = 0;
					}
					else 
					{
						$formId = (int)$xmlForm->id;
					}
				}
			}

			// Check the state of the article
			switch((string)$xmlForm->state)
			{
				case 'deleted':
					$pks = array((int)$xmlForm->id);
					$model->delete($pks, true);
					$results['deleted'] += 1;
					break;
				case 'trashed':
					// Trash the article
					$pks = array((int)$xmlForm->id);
					$model->publish($pks, F2C_STATE_TRASH, true);
					$results['trashed'] += 1;
					break;
				case 'unpublished':
					$data['state'] = 0;
					break;
				case 'published':
					$data['state'] = 1;
					break;
			}
			
			if((string)$xmlForm->state == 'trashed' || (string)$xmlForm->state == 'deleted')
			{
				// stop further execution for this form
				continue;	
			}
			
			if(count($xmlForm->attribs->children()))
			{
				foreach ($xmlForm->attribs->children() as $attribName => $xmlAttrib)
				{
					$attribs[$attribName] = (string)$xmlAttrib;
				}
			}
			
			if(count($xmlForm->metadata->children()))
			{
				foreach ($xmlForm->metadata->children() as $metadataName => $xmlMetadata)
				{
					$metadata[$metadataName] = (string)$xmlMetadata;
				}
			}

			// Tags is an optional element
			if(isset($xmlForm->tags) && count($xmlForm->tags->children()))
			{
				foreach ($xmlForm->tags->children() as $xmlTag)
				{
					if(array_key_exists((string)$xmlTag, $this->dicTags))
					{
						$tags[] = $this->dicTags[(string)$xmlTag];
					}
				}
			}

			// Load the existing form
			$existingForm 		= $form->getItem($formId);
			$existingFieldData 	= $existingForm->fields;
			
			$data['id'] 				= $formId;
			$data['projectid'] 			= $contentTypeId;
			$data['title'] 				= (string)$xmlForm->title;
			$data['alias'] 				= (string)$xmlForm->alias;
			$data['intro_template'] 	= (string)$xmlForm->intro_template;
			$data['main_template'] 		= (string)$xmlForm->main_template;	
			$data['catid'] 				= $this->dicCatAliasPath[(string)$xmlForm->cat_alias_path];
			
			if($formId && F2cFactory::getConfig()->get('import_keep_created_date', 1))
			{
				// update of an existing form, maintain the created date
				$data['created'] = $existingForm->created;
			}
			else 
			{
				$data['created'] = $this->emptyOrIso8601DateToSql((string)$xmlForm->created);	
			}			
			
			$data['created_by'] 		= $this->resolveUsername((string)$xmlForm->created_by_username);
			$data['created_by_alias'] 	= (string)$xmlForm->created_by_alias;
			$data['modified'] 			= $this->emptyOrIso8601DateToSql((string)$xmlForm->modified);
			$data['publish_up'] 		= $this->emptyOrIso8601DateToSql((string)$xmlForm->publish_up);
			$data['publish_down'] 		= $this->emptyOrIso8601DateToSql((string)$xmlForm->publish_down);
			$data['metakey'] 			= (string)$xmlForm->metakey;
			$data['metadesc'] 			= (string)$xmlForm->metadesc;
			$data['access'] 			= (int)$this->dicViewingAccessLevelTitle[(string)$xmlForm->access];
			$data['language'] 			= (string)$xmlForm->language;
			$data['featured'] 			= ((string)$xmlForm->featured == "yes") ? 1 : 0;
			$data['attribs'] 			= $attribs;
			$data['metadata'] 			= $metadata;
			$data['tags'] 				= $tags;

			foreach($contentType->fields as $field)
			{
				if($field->classificationId == F2C_FIELD_JOOMLA_NATIVE)
				{
					// Skip Joomla native fields
					continue;
				}
				
				$field->reset();				
				$fieldData[$field->fieldname] = $field;
			}
			
			if(count($xmlForm->fields->children()))
			{
				foreach($xmlForm->fields->children() as $xmlField)
				{
					$f2cField =& $fieldData[(string)$xmlField->fieldname];
					
					if($f2cField == null)
					{
						// Field is present in import XML, but not part of Content Type to which it should be imported
						throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_FIELD_NOT_PART_OF_CONTENTTYPE'), (string)$xmlField->fieldname, (string)$xmlForm->contenttype);
					}
					
					if(array_key_exists($f2cField->fieldname, $existingForm->fields))
					{
						$existingInternalData = $existingForm->fields[$f2cField->fieldname]->internal;
					}
					else 
					{
						$existingInternalData = null;
					}
					
					try 
					{
						$f2cField->import($xmlField, $existingInternalData, $data);	
					}
					catch(Exception $e)
					{
						throw new Exception('Error for article #'.$data['id'].' ('.$data['title'].'): ' . $e->getMessage());
					}
				}

				$data['preparedFieldData'] 	= $fieldData;

				if(!$form->saveCron($data))
				{
					throw new Exception('Error for article #'.$data['id'].' ('.$data['title'].'): ' . $form->getError());
				}
				
				if($formId)
				{
					$results['updated'] += 1;
				}
				else
				{
					$results['inserted'] += 1;
				}
			}
		}
		
		return $results;
	}
	
	/**
	 * Method to convert a date into the ISO 8601 format when it's not empty
	 * 
	 * @param	object		$date		The date to be formatted.
	 * @return	string		Date in ISO 8601 format or empty string.
	 * @since	4.6.0
	 */
	private function emptyOrIso8601DateToSql($date)
	{
		if($date)
		{
			$formattedDate = new JDate($date);
			return $formattedDate->toSql();
		}
		else 
		{
			return '';	
		}
	}
	
	/*
	 * Find the ID of a give username
	 */
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
}