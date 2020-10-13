<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use \Joomla\Registry\Registry;
use Joomla\String\StringHelper;

class F2cLegacyProject
{
	public function import($xml)
	{
		// Clone the settings, because we need them for the creation of the Joomla fields
		$settings			= $this->xmlToArray($xml->settings);		
		$clonedSettings 	= $settings; // arrays are copied by value
		
		$nodeSettings		= $xml->settings;
		$introTemplate 		= $nodeSettings->intro_template;
		$mainTemplate 		= $nodeSettings->main_template;
		
		// Check if the templates don't exist yet
      	$introTemplateFile = Path::Combine($f2cConfig->get('template_path'), $introTemplate);

      	if(JFile::exists($introTemplateFile))
      	{
      		throw new Exception(JText::sprintf(JText::_('COM_FORM2CONTENT_ERROR_IMPORT_CONTENTTYPE_TEMPLATE_EXISTS'), $introTemplate));		
      	}
		
      	$mainTemplateFile = Path::Combine($f2cConfig->get('template_path'), $mainTemplate);
      	
      	if(JFile::exists($mainTemplateFile))
      	{
			throw new Exception(JText::sprintf(JText::_('COM_FORM2CONTENT_ERROR_IMPORT_CONTENTTYPE_TEMPLATE_EXISTS'), $mainTemplate));		
      	}
		
		// Strip the settings, because in the new structure most settings have moved to the Content Type Field settings
		$this->removeLegacySettings($settings);
		
		$data				= array();
      	$data['import']		= true;
		$data['title'] 		= (string)$xml->title;
		$data['id'] 		= null; // force insert
		$data['asset_id'] 	= null; // force insert
		$data['published']	= (string)$xml->published;
		$data['metakey']	= (string)$xml->metakey;
		$data['metadesc']	= (string)$xml->metadesc;
		$data['settings'] 	= $settings;
		$data['attribs'] 	= $this->xmlToArray($xml->attribs);
		$data['metadata'] 	= $this->xmlToArray($xml->metadata);
		
		$contentType = new Form2ContentModelProject();
		
		if(!$contentType->save($data))
		{
			throw new Exception(implode("\n", $contentType->getErrors()));
		}	
		
		$contentTypeId =  $contentType->getState('project.id');
				
		// Create the Joomla fields, since they are not part of the import
		$joomlaFields = $this->getAllJoomlaFields();
		$contentType = F2cFactory::getContentType($contentTypeId, false);
		$contentType->settings = $clonedSettings; // Fake the legacy settings
		
		$this->addJoomlaFieldsToContentType($contentType, $joomlaFields);
		
      	if($xml->fields->children())
      	{
      		foreach($xml->fields->children() as $field)
      		{
				$fld 						= new Form2ContentModelProjectField();
				
				$fldData 					= array();
				$fldData['projectid'] 		= $contentTypeId;
				$fldData['fieldname'] 		= (string)$field->fieldname;
				$fldData['title'] 			= (string)$field->title;
				$fldData['description'] 	= (string)$field->description;
				$fldData['frontvisible']	= (string)$field->frontvisible;
				$fldData['fieldtypeid'] 	= (string)$field->fieldtypeid;
				$fldData['settings']		= $this->xmlToArray($field->settings);

				$fld->save($fldData, false);
      		}
      	}
		
		// Write the template files
		JFile::write($introTemplateFile, $xml->introtemplatefile);
		JFile::write($mainTemplateFile, $xml->maintemplatefile);
		
		if($formTemplate != '')
		{
			JFile::write($formTemplateFile, $xml->formtemplatefile);
		}
		
		return true;		
	}
	
	public function upgrade($contentType)
	{
		$this->fillDefaultContentTypeSettings($contentType);
		$joomlaFields = $this->getAllJoomlaFields();
		$this->addJoomlaFieldsToContentType($contentType, $joomlaFields);		
	}
	
	/*
	 * Upgrade all Content Types to the new field structure where Joomla fields are modeled as F2C fields
	 */
	public function upgradeAllContentTypes()
	{
		$joomlaFields 	= $this->getAllJoomlaFields();
		$contentTypes 	= $this->getAllContentTypes();
		$reservedFields = array();
		
		$db 	= JFactory::getDbo();
	 	$query 	= $db->getQuery(true);
		
	 	// Check if there are no conflicts with the new field names
	 	$reservedFieldNames = array('id', 'title', 'title_alias', 'meta_description', 'meta_keywords', 'category', 'author', 'author_alias', 'access', 
	 								'created', 'modified', 'publish_start','publish_stop', 'featured','language','published','tags','associations',
	 								'intro_template','main_template', 'captcha');
	 		 	
	 	$query->select('p.id AS contentTypeId, pf.fieldname');
	 	$query->from('#__f2c_projectfields pf')->innerJoin('#__f2c_project p ON pf.projectid = p.id');
	 	$query->where('pf.fieldname in (\''.implode("','",$reservedFieldNames).'\')');

	 	$db->setQuery($query);
	 	
	 	$listFields = $db->loadObjectList();

	 	foreach ($listFields as $field) 
		{
			if(!array_key_exists($field->contentTypeId, $reservedFields))
			{
				$reservedFields[$field->contentTypeId] = array();
			}
			
			$reservedFields[$field->contentTypeId][] = StringHelper::strtolower($field->fieldname);
		}

		$query	= $db->getQuery(true);
	
		// Shift all the sorted fields to make room for the new fields
		$fields = array('ordering = ordering + 20');
				
		$query->update('#__f2c_projectfields')->set($fields);
	
		$db->setQuery($query);
		$db->execute();
				
		foreach($contentTypes as $c)
		{
			$reservedContentTypeFields = (array_key_exists($c->id, $reservedFields)) ? $reservedFields[$c->id] : array();
			$contentType = F2cFactory::getContentType($c->id);
			$this->addJoomlaFieldsToContentType($contentType, $joomlaFields, $reservedContentTypeFields);
		}
	}
	
	public function removeLegacySettings(&$settings)
	{
		$this->removeSetting($settings, 'id_front_end');
		$this->removeSetting($settings, 'id_caption'); 
		$this->removeSetting($settings, 'title_front_end'); 
		$this->removeSetting($settings, 'title_caption'); 
		$this->removeSetting($settings, 'title_default'); 
		$this->removeSetting($settings, 'title_attributes'); 
		$this->removeSetting($settings, 'title_alias_front_end'); 
		$this->removeSetting($settings, 'title_alias_caption'); 
		$this->removeSetting($settings, 'metadesc_front_end'); 
		$this->removeSetting($settings, 'metadesc_caption'); 
		$this->removeSetting($settings, 'metakey_front_end'); 
		$this->removeSetting($settings, 'metakey_caption'); 
		$this->removeSetting($settings, 'frontend_catsel'); 
		$this->removeSetting($settings, 'category_caption'); 
		$this->removeSetting($settings, 'catid'); 
		$this->removeSetting($settings, 'cat_behaviour'); 
		$this->removeSetting($settings, 'cat_joomla_acl'); 
		$this->removeSetting($settings, 'author_front_end'); 
		$this->removeSetting($settings, 'author_caption'); 
		$this->removeSetting($settings, 'author_alias_front_end'); 
		$this->removeSetting($settings, 'author_alias_caption'); 
		$this->removeSetting($settings, 'access_level_front_end'); 
		$this->removeSetting($settings, 'access_level_caption'); 
		$this->removeSetting($settings, 'access_default'); 
		$this->removeSetting($settings, 'frontend_templsel'); 
		$this->removeSetting($settings, 'intro_template'); 
		$this->removeSetting($settings, 'intro_template_caption'); 
		$this->removeSetting($settings, 'main_template'); 
		$this->removeSetting($settings, 'main_template_caption'); 
		$this->removeSetting($settings, 'date_created_front_end'); 
		$this->removeSetting($settings, 'created_caption'); 
		$this->removeSetting($settings, 'frontend_pubsel'); 
		$this->removeSetting($settings, 'publish_up_caption'); 
		$this->removeSetting($settings, 'publish_down_caption'); 
		$this->removeSetting($settings, 'state_front_end'); 
		$this->removeSetting($settings, 'state_caption'); 
		$this->removeSetting($settings, 'state_default'); 
		$this->removeSetting($settings, 'featured_front_end'); 
		$this->removeSetting($settings, 'featured_caption'); 
		$this->removeSetting($settings, 'featured_default'); 
		$this->removeSetting($settings, 'language_front_end'); 
		$this->removeSetting($settings, 'language_caption'); 
		$this->removeSetting($settings, 'language_default'); 
		$this->removeSetting($settings, 'captcha_front_end'); 
		$this->removeSetting($settings, 'tags_front_end'); 
		$this->removeSetting($settings, 'tags_caption'); 
		$this->removeSetting($settings, 'tag_field_ajax_mode'); 
		$this->removeSetting($settings, 'tags_allow_custom'); 
		$this->removeSetting($settings, 'associations_front_end'); 
		$this->removeSetting($settings, 'associations_caption'); 		
	}
	
	/*
	 *	Function to upgrade the Content Type to the new structure where the Joomla fields are modeled as F2C Fields
	 */
	private function addJoomlaFieldsToContentType($contentType, $joomlaFields, $reservedContentTypeFields = null)
	{
		$fieldTemplateList = $this->getDefaultContentTypeFields($contentType);
		$ordering = 1;

		foreach($fieldTemplateList as $fieldname => $fieldTemplate)
		{
			$field = $this->createEmptyField($contentType, $fieldname, $fieldTemplate, $ordering++);
			
			// Intercept upgrade field
			if(is_array($reservedContentTypeFields))
			{
				$this->upgradeField($fieldTemplate['type'], $field, $contentType, $reservedContentTypeFields);				
			}

			$this->saveField($field);
		}
	}
	
	private function createFieldname($suggestedName, $reservedContentTypeFields)
	{
		if(StringHelper::strtolower($suggestedName) == 'id')
		{
			// When the reserved field name already exists for this Content Type prefix it with xjoomla_
			// Exception to avoid conflicts with Joomla_id template parameter
			return in_array(StringHelper::strtolower($suggestedName), $reservedContentTypeFields) ? 'xjoomla_'.$suggestedName : $suggestedName;
		}
		else 
		{
			// When the reserved field name already exists for this Content Type prefix it with joomla_
			return in_array(StringHelper::strtolower($suggestedName), $reservedContentTypeFields) ? 'joomla_'.$suggestedName : $suggestedName;
		}
	}
	
	private function getAllJoomlaFields()
	{
		$db 	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('*')->from('#__f2c_fieldtype')->where('classification_id = 0');
		
		$db->setQuery($query);
		
		return $db->loadObjectList();
	} 
	
	private function getAllContentTypes()
	{
		$db 	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('*')->from('#__f2c_project');
		
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}	
	
	private function fillDefaultContentTypeSettings($contentType)
	{
		$contentType->settings['id_front_end'] = 1;
		$contentType->settings['id_caption'] = '';
		$contentType->settings['title_front_end'] = 1;
		$contentType->settings['title_caption'] = '';
		$contentType->settings['title_default'] = '';
		$contentType->settings['title_attributes'] = '';
		$contentType->settings['title_alias_front_end'] = 1;
		$contentType->settings['title_alias_caption'] = '';
		$contentType->settings['metadesc_front_end'] = 1;
		$contentType->settings['metadesc_caption'] = '';
		$contentType->settings['metakey_front_end'] = 1;
		$contentType->settings['metakey_caption'] = '';
		$contentType->settings['frontend_catsel'] = 1;
		$contentType->settings['category_caption'] = '';
		$contentType->settings['catid'] = -1;
		$contentType->settings['cat_behaviour'] = 0;
		$contentType->settings['cat_joomla_acl'] = 0;
		$contentType->settings['author_front_end'] = 1;
		$contentType->settings['author_caption'] = '';
		$contentType->settings['author_alias_front_end'] = 1;
		$contentType->settings['author_alias_caption'] = '';
		$contentType->settings['access_level_front_end'] = 1;
		$contentType->settings['access_level_caption'] = '';
		$contentType->settings['access_default'] = 1;
		$contentType->settings['frontend_templsel'] = 1;
		$contentType->settings['intro_template'] = 'default_intro_template_empty project.tpl';
		$contentType->settings['intro_template_caption'] = '';
		$contentType->settings['main_template'] = 'default_main_template_empty project.tpl';
		$contentType->settings['main_template_caption'] = '';
		$contentType->settings['date_created_front_end'] = 1;
		$contentType->settings['created_caption'] = '';
		$contentType->settings['frontend_pubsel'] = 1;
		$contentType->settings['publish_up_caption'] = '';
		$contentType->settings['publish_down_caption'] = '';
		$contentType->settings['state_front_end'] = 1;
		$contentType->settings['state_caption'] = '';
		$contentType->settings['featured_front_end'] = 1;
		$contentType->settings['featured_caption'] = '';
		$contentType->settings['language_front_end'] = 1;
		$contentType->settings['language_caption'] = '';
		$contentType->settings['language_default'] = '*';
		$contentType->settings['captcha_front_end'] = 0;
		$contentType->settings['required_field_text'] = '';
		$contentType->settings['tags_front_end'] = 0;
		$contentType->settings['tags_caption'] = '';
		$contentType->settings['tag_field_ajax_mode'] = 1;
		$contentType->settings['tags_allow_custom'] = 1;
		$contentType->settings['associations_front_end'] = 0;
		$contentType->settings['associations_caption'] = '';
	}
	
	private function removeSetting(&$settings, $settingKey)
	{
		if(array_key_exists($settingKey, $settings))
		{
			unset($settings[$settingKey]);
		}
	}	
	
	private function addSettingWhenExists($settings, $key, $default = '')
	{
		if(array_key_exists($key, $settings) && $settings[$key] != '')
		{
			return $settings[$key];
		}
		else
		{
			return $default;
		}		
	}		
	
	private function xmlToArray($node)
	{
		$array = array();
		
		if(count($node->children()))
		{
			foreach($node->children() as $elementName => $child)
			{
				if($child->children())
				{
					if($elementName != 'arrayelement')
					{
						$array[$elementName] = self::xmlToArray($child);;
					}
					else 
					{
						$array[(string)$child->key] = (string)$child->value;
					}					
				}
				else
				{
					$array[$elementName] = (string)$child;
				}
			}
		}
		
		return $array;
	}	
	
	private function createEmptyField($contentType, $fieldName, $fieldTemplate, $ordering)
	{
		$field = new stdClass();
		
		$field->projectid = $contentType->id;
		$field->fieldtypeid = $fieldTemplate['typeId'];
		$field->fieldname = $fieldName;
		$field->title = $fieldTemplate['title'];
		$field->frontvisible = $fieldTemplate['frontvisible'];
		$field->ordering = $ordering;
		
		$field->settings = new Registry();
		$field->settings->set('requiredfield', 0);
		$field->settings->set('error_message_required', '');
		
		foreach($fieldTemplate['settings'] as $settingsKey => $settingsValue)
		{
			$field->settings->set($settingsKey, $settingsValue);
		}		

		return $field;
	}
	
	private function saveField($field)
	{
		// Convert the registry object
		$field->settings = $field->settings->toString();
		
		// Insert the field
		JFactory::getDbo()->insertObject('#__f2c_projectfields', $field);
	}
	
	private function getDefaultContentTypeFields($contentType)
	{
		$fields 					= array();
		$fields['id'] 				= array('type' => 'Joomlaid',
											'title' => 'JGLOBAL_FIELD_ID_LABEL',
											'frontvisible' => 1,
											'settings' => array('requiredfield' => 1));
		$fields['title'] 			= array('type' => 'Joomlatitle',
											'title' => 'JGLOBAL_TITLE',
											'frontvisible' => 1,
											'settings' => array('requiredfield' => 1));
		$fields['title_alias'] 		= array('type' => 'Joomlaalias',
											'title' => 'JFIELD_ALIAS_LABEL',
											'frontvisible' => 0,
											'settings' => array());
		$fields['meta_description'] = array('type' => 'Joomlametadescription',
											'title' => 'JFIELD_META_DESCRIPTION_LABEL',
											'frontvisible' => 1,
											'settings' => array('default' => $contentType->metadesc));
		$fields['meta_keywords'] 	= array('type' => 'Joomlametakeywords',
											'title' => 'JFIELD_META_KEYWORDS_LABEL',
											'frontvisible' => 1,
											'settings' => array('default' => $contentType->metakey));
		$fields['tags'] 			= array('type' => 'Joomlatags',
											'title' => 'JTAG',
											'frontvisible' => 0,
											'settings' => array('field_ajax_mode' => 1, 'allow_custom' => 1));
		$fields['category'] 		= array('type' => 'Joomlacategory',
											'title' => 'JCATEGORY',
											'frontvisible' => 1,
											'settings' => array('requiredfield' => 1, 'default' => -1, 'behaviour' => 1, 'use_joomla_acl' => 0));
		$fields['author'] 			= array('type' => 'Joomlacreatedby',
											'title' => 'COM_FORM2CONTENT_FIELD_CREATED_BY_LABEL',
											'frontvisible' => 0,
											'settings' => array());
		$fields['author_alias'] 	= array('type' => 'Joomlacreatedbyalias',
											'title' => 'COM_FORM2CONTENT_FIELD_CREATED_BY_ALIAS_LABEL',
											'frontvisible' => 0,
											'settings' => array());
		$fields['access'] 			= array('type' => 'Joomlaaccess',
											'title' => 'JFIELD_ACCESS_LABEL',
											'frontvisible' => 0,
											'settings' => array());
		$fields['intro_template'] 	= array('type' => 'Joomlatemplate',
											'title' => 'COM_FORM2CONTENT_INTRO_TEMPLATE',
											'frontvisible' => 0,
											'settings' => array());
		$fields['main_template'] 	= array('type' => 'Joomlatemplate',
											'title' => 'COM_FORM2CONTENT_MAIN_TEMPLATE',
											'frontvisible' => 0,
											'settings' => array());
		$fields['created'] 			= array('type' => 'Joomlacreated',
											'title' => 'COM_FORM2CONTENT_FIELD_CREATED_LABEL',
											'frontvisible' => 1,
											'settings' => array());
		$fields['publish_start'] 	= array('type' => 'Joomlapublishup',
											'title' => 'COM_FORM2CONTENT_FIELD_PUBLISH_UP_LABEL',
											'frontvisible' => 1,
											'settings' => array());
		$fields['publish_stop'] 	= array('type' => 'Joomlapublishdown',
											'title' => 'COM_FORM2CONTENT_FIELD_PUBLISH_DOWN_LABEL',
											'frontvisible' => 1,
											'settings' => array());
		$fields['published'] 		= array('type' => 'Joomlapublished',
											'title' => 'JSTATUS',
											'frontvisible' => 1,
											'settings' => array('default' => 0));	
		$fields['language'] 		= array('type' => 'Joomlalanguage',
											'title' => 'JFIELD_LANGUAGE_LABEL',
											'frontvisible' => 1,
											'settings' => array());
		$fields['featured'] 		= array('type' => 'Joomlafeatured',
											'title' => 'JFEATURED',
											'frontvisible' => 1,
											'settings' => array('default' => 0));
		$fields['associations'] 	= array('type' => 'Joomlaassociations',
											'title' => 'JGLOBAL_FIELDSET_ASSOCIATIONS',
											'frontvisible' => 0,
											'settings' => array());			

		$dicFields = F2cFactory::getFieldTypesDictionary(false);
		
		foreach ($fields as &$field) 
		{
			$field['typeId'] = $dicFields[$field['type']]->id;
		}
		
		return $fields;
	}
	
	private function upgradeField($fieldType, $field, $contentType, $reservedContentTypeFields)
	{
		// check if we need to change the fieldname
		$field->fieldname = $this->createFieldname($field->fieldname, $reservedContentTypeFields);

		switch($fieldType)
		{
			case 'Joomlaid':
				$field->frontvisible = $this->addSettingWhenExists($contentType->settings, 'id_front_end', 1);
				$field->title = $this->addSettingWhenExists($contentType->settings, 'id_caption', 'JGLOBAL_FIELD_ID_LABEL');
				break;
			case 'Joomlatitle':
				$field->frontvisible = $contentType->settings['title_front_end'] ? 1 : 0;
				$field->title = $this->addSettingWhenExists($contentType->settings, 'title_caption', 'JGLOBAL_TITLE');
				$field->settings->set('default', $this->addSettingWhenExists($contentType->settings, 'title_default'));
				$field->settings->set('attributes', $this->addSettingWhenExists($contentType->settings, 'title_attributes'));
				break;
			case 'Joomlaalias':
				$field->frontvisible = $contentType->settings['title_alias_front_end'] ? 1 : 0;
				$field->title = $this->addSettingWhenExists($contentType->settings, 'title_alias_caption', 'JFIELD_ALIAS_LABEL');
				break;				
			case 'Joomlametadescription':
				$field->frontvisible = $this->addSettingWhenExists($contentType->settings, 'metadesc_front_end', 1);
				$field->title = $this->addSettingWhenExists($contentType->settings, 'metadesc_caption', 'JFIELD_META_DESCRIPTION_LABEL');
				break;
			case 'Joomlametakeywords':
				$field->frontvisible = $this->addSettingWhenExists($contentType->settings, 'metakey_front_end', 1);
				$field->title = $this->addSettingWhenExists($contentType->settings, 'metakey_caption', 'JFIELD_META_KEYWORDS_LABEL');
				break;
			case 'Joomlatags':
				if(array_key_exists('tags_front_end', $contentType->settings))
				{
					$field->frontvisible = $contentType->settings['tags_front_end'] ? 1 : 0;
				}
				else
				{
					$field->frontvisible = 0;
				}
				$field->title = $this->addSettingWhenExists($contentType->settings, 'tags_caption', 'JTAG');
				$field->settings->set('field_ajax_mode', $this->addSettingWhenExists($contentType->settings, 'tag_field_ajax_mode', 1));
				$field->settings->set('allow_custom', $this->addSettingWhenExists($contentType->settings, 'tags_allow_custom', 1));					
				break;					
			case 'Joomlacategory':
				$field->frontvisible = $this->addSettingWhenExists($contentType->settings, 'frontend_catsel', 1);
				$field->title = $this->addSettingWhenExists($contentType->settings, 'category_caption', 'JCATEGORY');				
				$field->settings->set('default', $this->addSettingWhenExists($contentType->settings, 'catid', -1));
				$field->settings->set('behaviour', $this->addSettingWhenExists($contentType->settings, 'cat_behaviour', 1));
				$field->settings->set('use_joomla_acl', $this->addSettingWhenExists($contentType->settings, 'cat_joomla_acl', 0));
				break;
			case 'Joomlacreatedby':
				$field->frontvisible = $this->addSettingWhenExists($contentType->settings, 'author_front_end', 1);
				$field->title = $this->addSettingWhenExists($contentType->settings, 'author_caption', 'COM_FORM2CONTENT_FIELD_CREATED_BY_LABEL');
				break;
			case 'Joomlacreatedbyalias':
				$field->frontvisible = $this->addSettingWhenExists($contentType->settings, 'author_alias_front_end', 1);
				$field->title = $this->addSettingWhenExists($contentType->settings, 'author_alias_caption', 'COM_FORM2CONTENT_FIELD_CREATED_BY_ALIAS_LABEL');
				break;
			case 'Joomlaaccess':
				$field->frontvisible = $this->addSettingWhenExists($contentType->settings, 'access_level_front_end', 1);
				$field->title = $this->addSettingWhenExists($contentType->settings, 'access_caption', 'JFIELD_ACCESS_LABEL');
				$field->settings->set('default', $this->addSettingWhenExists($contentType->settings, 'access_default'));
				break;				
			case 'Joomlatemplate':
				if(strpos($field->fieldname, 'intro_template') !== false)
				{
					$field->title = $this->addSettingWhenExists($contentType->settings, 'intro_template_caption', 'COM_FORM2CONTENT_INTRO_TEMPLATE');
					$field->settings->set('default', $this->addSettingWhenExists($contentType->settings, 'intro_template'));
				}
				else 
				{
					$field->title = $this->addSettingWhenExists($contentType->settings, 'main_template_caption', 'COM_FORM2CONTENT_MAIN_TEMPLATE');
					$field->settings->set('default', $this->addSettingWhenExists($contentType->settings, 'main_template'));
				}
				$field->settings->set('requiredfield', 0);					
				break;						
			case 'Joomlacreated':
				$field->frontvisible = $this->addSettingWhenExists($contentType->settings, 'date_created_front_end', 1);
				$field->title = $this->addSettingWhenExists($contentType->settings, 'created_caption', 'COM_FORM2CONTENT_FIELD_CREATED_LABEL');
				break;
			case 'Joomlapublishup':
				$field->frontvisible = $contentType->settings['frontend_pubsel'] ? 1 : 0;
				$field->title = $this->addSettingWhenExists($contentType->settings, 'publish_up_caption', 'COM_FORM2CONTENT_FIELD_PUBLISH_UP_LABEL');
				break;
			case 'Joomlapublishdown':
				$field->frontvisible = $contentType->settings['frontend_pubsel'] ? 1 : 0;
				$field->title = $this->addSettingWhenExists($contentType->settings, 'publish_down_caption', 'COM_FORM2CONTENT_FIELD_PUBLISH_DOWN_LABEL');
				break;
			case 'Joomlapublished':
				$field->frontvisible = $this->addSettingWhenExists($contentType->settings, 'state_front_end', 1);
				$field->title = $this->addSettingWhenExists($contentType->settings, 'state_caption', 'JSTATUS');
				$field->settings->set('default', $this->addSettingWhenExists($contentType->settings, 'state_default', 0));
				break;
			case 'Joomlalanguage':
				$field->frontvisible = $this->addSettingWhenExists($contentType->settings, 'language_front_end', 1);
				$field->title = $this->addSettingWhenExists($contentType->settings, 'language_caption', 'JFIELD_LANGUAGE_LABEL');
				$field->settings->set('default', $this->addSettingWhenExists($contentType->settings, 'language_default'));
				break;					
			case 'Joomlafeatured':
				$field->frontvisible = $this->addSettingWhenExists($contentType->settings, 'featured_front_end', 1);
				$field->title = $this->addSettingWhenExists($contentType->settings, 'featured_caption', 'JFEATURED');
				$field->settings->set('default', $this->addSettingWhenExists($contentType->settings, 'featured_default', 0));
				break;
			case 'Joomlaassociations':
				if(array_key_exists('associations_front_end', $contentType->settings))
				{
					$field->frontvisible = $contentType->settings['associations_front_end'] ? 1 : 0;
				}
				else
				{
					$field->frontvisible = 0;
				}					
				$field->title = $this->addSettingWhenExists($contentType->settings, 'associations_caption', 'JGLOBAL_FIELDSET_ASSOCIATIONS');
				break;			
		}
	}
}
?>