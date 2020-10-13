<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use \Joomla\Registry\Registry;

// TODO: All includes needed?
require_once(JPATH_SITE.'/components/com_form2content/models/projectbase.php');
require_once(JPATH_SITE.'/administrator/components/com_form2content/models/form.php');
require_once(JPATH_SITE.'/administrator/components/com_form2content/models/projectfield.php');

class Form2ContentModelProject extends Form2ContentModelProjectBase
{	
	public function save($data)
	{
		$jConfig	= JFactory::getConfig();
		$tzoffset 	= $jConfig->get('config.offset');
		$dateNow	= JFactory::getDate(null, $tzoffset); 
		$isNew		= empty($data['id']);
		$isImport	= array_key_exists('import', $data);

		if($isNew)
		{
			$user 				= JFactory::getUser();
			$data['created_by']	= $user->id;		
			$data['created']	= $dateNow->toSql();
			
			if($configInfo = JInstaller::parseXMLInstallFile(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'manifest.xml')) 
			{
				$data['version'] = $configInfo['version'];
			}
			
			// Check that we don't have a Content Type with the same title yet
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id')->from('#__f2c_project')->where('title = ' . $db->quote($data['title']));
			$db->setQuery($query);
			
			if($db->loadResult())
			{
				$this->setError(JText::_('COM_FORM2CONTENT_ERROR_CONTENTTYPE_TITLE_EXISTS'));
				return false;	
			}
		}

		$data['modified'] = $dateNow->toSql();
				
		if(!parent::save($data))
		{
			return false;
		}
	
		$data['id'] = $this->getState('project.id');
		
		// Create the fields for the new Content Type
		if($isNew && !$isImport)
		{
			// Load the Content Type 
			$contentType 		= F2cFactory::getContentType($data['id'], false);			
			$contentTypeLegacy 	= new F2cLegacyProject();
			$contentTypeLegacy->upgrade($contentType);
		}
		
		return true;
	}

	public function syncJoomlaAdvancedParms($id)
	{
		$query = $this->_db->getQuery(true);
		$query->update('#__f2c_form frm')->innerJoin('#__f2c_project prj ON frm.projectid = prj.id AND prj.id = '.(int)$id)->set('frm.attribs = prj.attribs');
		
		$this->_db->setQuery($query);
		
		if(!$this->_db->execute())
		{			
			$this->setError($this->_db->getErrorMsg());
			return false; 
		}

		return true;
	}
	
	function syncMetadata($id)
	{
		// Get the metadata fields
		$query = $this->_db->getQuery(true);
		$query->select('pf.*, ft.name as FieldName')->from('#__f2c_projectfields pf')->where('pf.projectid='.(int)$id);
		$query->join('INNER', '#__f2c_fieldtype ft ON pf.fieldtypeid = ft.id')->where('ft.name IN (\'Joomlametadescription\',\'Joomlametakeywords\')');
		$this->_db->setQuery($query);
		
		$metaFields = $this->_db->loadObjectList('FieldName');
		
		$settingsMetaDesc = new Registry($metaFields['Joomlametadescription']->settings);
		$settingsMetaKeys = new Registry($metaFields['Joomlametakeywords']->settings);
		$defaultMetaDesc = $settingsMetaDesc->get('default');
		$defaultMetaKeys = $settingsMetaKeys->get('default');
		
		$query = $this->_db->getQuery(true);
		$query->update('#__f2c_form frm')->innerJoin('#__f2c_project prj ON frm.projectid = prj.id AND prj.id = '.(int)$id);
		$query->set('frm.metadata = prj.metadata, frm.metakey = '.$this->_db->quote($defaultMetaKeys).', frm.metadesc = '.$this->_db->quote($defaultMetaDesc));
		$this->_db->setQuery($query);
		
		if(!$this->_db->execute())
		{			
			$this->setError($this->_db->getErrorMsg());
			return false; 
		}
				
		return true;		
	}
	
	public function copy(&$pks)
	{
		$contentTypeTable		= $this->getTable(); 				
		$contentTypeFieldRow	= JTable::getInstance('ProjectField','Form2ContentTable'); 	
		$dateNow 				= JFactory::getDate();
		$timestamp 				= $dateNow->toSql();
		
		foreach ($pks as $i => $pk)
		{
			if(!$contentTypeTable->load($pk))
			{
				$this->setError($contentTypeTable->getError());
				return false;
			}
			
			$contentTypeTable->title 	= JText::_('COM_FORM2CONTENT_COPY_OF') . ' ' . $contentTypeTable->title;
			$contentTypeTable->id 		= null; // force insert
			$contentTypeTable->asset_id = null; // force insert
			$contentTypeTable->created 	= $timestamp;
			$contentTypeTable->modified = $this->_db->getNullDate();
			
			if(!$contentTypeTable->store())
			{
				$this->setError($contentTypeTable->getError());
				return false;
			}
			
			// copy the ContentType Fields
			$query = $this->_db->getQuery(true);
			$query->select('*');
			$query->from('#__f2c_projectfields');
			$query->where('projectid = ' . (int)$pk);
			
			$this->_db->setQuery($query);
			
			$contentTypeFields = $this->_db->loadAssocList();

			if(count($contentTypeFields))
			{
				foreach($contentTypeFields as $contentTypeField)
				{
					if (!$contentTypeFieldRow->bind($contentTypeField)) 
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}

					$contentTypeFieldRow->id = 0; // force insert
					$contentTypeFieldRow->projectid = $contentTypeTable->id;
				
					if(!$contentTypeFieldRow->store())
					{
						$this->setError($contentTypeFieldRow->getError());
						return false;
					}
					
					// Inserting new Content Type fields generated new ordering
					// Resave the field with the original ordering
					$contentTypeFieldRow->ordering = $contentTypeField['ordering'];
					
					if(!$contentTypeFieldRow->store())
					{
						$this->setError($contentTypeFieldRow->getError());
						return false;
					}
				}
			}
		}
		
		return true;
	}
	
	public function delete(&$pks)
	{
		// Initialise variables.
		$dispatcher			= JEventDispatcher::getInstance();
		$pks				= (array)$pks;
		$context 			= $this->option.'.'.$this->name;
		$modelForm			= new Form2ContentModelForm();
		$contentTypeTable	= $this->getTable();
		
		// Include the content plugins for the on delete events.
		JPluginHelper::importPlugin('form2content');
		
		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk) 
		{
			$contentType = F2cFactory::getContentType($pk);
			
			if($contentTypeTable->load($pk)) 
			{
				// Get the list of forms for this Content Type
				$query = $this->_db->getQuery(true);
				$query->select('id')->from('#__f2c_form')->where('projectid = ' . (int)$pk);
				
				$this->_db->setQuery($query);
				
				$formIds = $this->_db->loadColumn();
				
				if(!$modelForm->delete($formIds))
				{
					$this->setError($modelForm->getError());
					return false;
				}
				
				foreach($contentType->fields as $field)
				{
					$field->deleteContentType();
				}
				
				// Delete the translations
				$this->_db->setQuery('DELETE tra.* FROM #__f2c_translation tra ' . 
									 'INNER JOIN #__f2c_projectfields pfl ON pfl.id = tra.reference_id ' .
									 'WHERE pfl.projectid ='.(int)$pk);
				
				if(!$this->_db->execute())
				{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
				// Delete the Content Type Field definitions
				$query = $this->_db->getQuery(true);
				$query->delete('#__f2c_projectfields')->where('projectid = ' . (int)$pk);
				
				$this->_db->setQuery($query);
				
				if(!$this->_db->execute())
				{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
	
				// Delete the Content Type			
				if (!$contentTypeTable->delete($pk)) 
				{
					$this->setError($contentTypeTable->getError());
					return false;
				}
			}
			else
			{
				$this->setError($contentTypeTable->getError());
				return false;
			}						
		}

		// Clear the component's cache
		$cache = JFactory::getCache($this->option);
		$cache->clean();

		return true;
	}
	
	public function import($file)
	{
		$componentInfo	= JInstaller::parseXMLInstallFile(JPATH_COMPONENT.'/manifest.xml');
		$db				= JFactory::getDbo();
		$f2cConfig		= F2cFactory::getConfig();
		
		if(!$xml = @simplexml_load_file($file, $class_name = 'SimpleXMLExtended'))
		{
			$this->setError(JText::_('COM_FORM2CONTENT_ERROR_IMPORT_CONTENTTYPE_NO_CONTENTTYPE'));
			return false;
		}
		
		// Pre-version check for legacy import
		if($xml->version != '')
		{
			list($importMajor, $importMinor, $importRevision) 	= explode('.', $xml->version);
			list($compMajor, $compMinor, $compRevision) 		= explode('.', $componentInfo['version']);
			
			if($importMajor == $compMajor && $importMinor < 16)
			{
				$importer = new F2cLegacyProject();
			}
			else 
			{
				$importer = new F2cIoImportcontenttype($componentInfo, $db, $f2cConfig, $this);
			}
		}

		try 
		{
			$importer->import($xml);
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}
		
		return true;		
	}
	
	public function createSampleFormTemplate($id, $overwrite = 0, $classic = 0)
	{
		$template 			= '';
		$contentType		= F2cFactory::getContentType($id);
		$templateName 		= 'default_form_template_'.$contentType->title . '.tpl';
		$filename 			= Path::Combine(F2cFactory::getConfig()->get('template_path'), $templateName);
		
		if(JFile::exists($filename) && !$overwrite)
		{
			return '1;'.$templateName;
		}
		
		$template = $classic ? $this->createSampleFormTemplateClassic($contentType) : $this->createSampleFormTemplateModern($contentType);
		
		JFile::write($filename, $template);
		
		return '0;'.$templateName;
	}
	
	private function createSampleFormTemplateClassic($contentType)
	{
		$template = '';
		
		$buttons = '<table style="width:100%;">
					<tr class="f2c_buttons">
						<td><div style="float: right;">{$F2C_BUTTON_APPLY}{$F2C_BUTTON_SAVE}{$F2C_BUTTON_SAVE_AND_NEW}{$F2C_BUTTON_SAVE_AS_COPY}{$F2C_BUTTON_CANCEL}</div></td>
					</tr>
					</table>';
		
		$template .= $buttons;
		$template .= '<div class="width-60 fltlft"><fieldset class="adminform"><table class="adminform" width="100%">'.PHP_EOL;

		if(count($contentType->fields))
		{
			foreach($contentType->fields as $contentTypeField)
			{
				if($contentTypeField->frontvisible)
				{
					$fieldname = strtoupper($contentTypeField->fieldname);
					$template .= '<tr class="f2c_field '.$contentTypeField->getCssClass().'"><td width="100" align="left" class="key f2c_field_label" valign="top">{$'.$fieldname.'_CAPTION}</td><td valign="top" class="f2c_field_value">{$'.$fieldname.'}</td></tr>'.PHP_EOL;
				}
			}
		}
		
		$template .= '</table></fieldset>';
		$template .= '</div><div class="clr"></div>'.PHP_EOL;			
		$template .= $buttons;
		
		return $template;	
	}
	
	private function createSampleFormTemplateModern($contentType)
	{
		$template 			= '';
		
		$buttons = '<div class="f2c_button_bar">
						{$F2C_BUTTON_APPLY}{$F2C_BUTTON_SAVE}{$F2C_BUTTON_SAVE_AND_NEW}{$F2C_BUTTON_SAVE_AS_COPY}{$F2C_BUTTON_CANCEL}
					</div>
					<div class="clearfix"></div>';
				
		$template .= $buttons;
		$template .= '<div class="row-fluid form-horizontal">'.PHP_EOL;

		if(count($contentType->fields))
		{
			foreach($contentType->fields as $contentTypeField)
			{
				if($contentTypeField->frontvisible)
				{
					$fieldname = strtoupper($contentTypeField->fieldname);
					
					$template .= '	<div class="control-group f2c_field ' . $contentTypeField->getCssClass() . '">
										<div class="control-label f2c_field_label">{$'.$fieldname.'_CAPTION}</div>
										<div class="controls f2c_field_value">{$'.$fieldname.'}</div>
									</div>';
				}
			}
		}

		$template .= '</div>';		
		$template .= '<div class="clearfix"></div>'.PHP_EOL;			
		$template .= $buttons;
		
		return $template;
	}					
}
?>