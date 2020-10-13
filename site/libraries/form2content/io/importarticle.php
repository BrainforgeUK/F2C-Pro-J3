<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once(JPATH_COMPONENT_SITE.'/libraries/SimpleXMLExtended.php');

class F2cIoImportarticle
{
	private $db;
	private $f2cConfig;
	private $model;
	private $namespace = 'http://schemas.form2content.com/forms';
	private $dicContentTypesByName;
	public $logMessages = array();
	
	public function __construct($db, $f2cConfig, $model)
	{
		$this->db 						= $db;
		$this->f2cConfig				= $f2cConfig;
		$this->model					= $model;
		$this->dicContentTypesByName	= $this->loadDictionaryContentTypes();		
	}
	
	public function processFiles()
	{
		$this->logMessages 	= array(); // clear log
		$importDir 			= $this->f2cConfig->get('import_dir');
		$archiveDir			= $this->f2cConfig->get('import_archive_dir');
		$errorDir			= $this->f2cConfig->get('import_error_dir');
		$postAction 		= $this->f2cConfig->get('import_post_action', 1);
		$importTmpPath		= Path::Combine(JFactory::getConfig()->get('tmp_path'), 'f2c_import');
		
		$this->AddLogEntry(JText::_('COM_FORM2CONTENT_IMPORT_STARTED'));
		
		try 
		{
			$this->preImportCheck();
		}
		catch(Exception $e)
		{
			$this->AddLogEntry($e->getMessage());
			$this->AddLogEntry(JText::_('COM_FORM2CONTENT_IMPORT_ENDED'));
			return false;
		}

		$importFiles = JFolder::files($importDir, '.xml', false, true);
		
		if(count($importFiles) == 0)
		{
			// Nothing to import
			$this->AddLogEntry(JText::_('COM_FORM2CONTENT_NO_IMPORT_FILES'));
			$this->AddLogEntry(JText::_('COM_FORM2CONTENT_IMPORT_ENDED'));
			return true;
		}
		
		if(JFolder::exists($importTmpPath))
		{
			JFolder::delete($importTmpPath);
		}
		
		// Create a temporary folder for files and images
		JFolder::create($importTmpPath);
		
		foreach($importFiles as $importFile)
		{
			$this->AddLogEntry(sprintf(JText::_('COM_FORM2CONTENT_IMPORTING_FILE'), basename($importFile)));
			
			try 
			{
				$xml = $this->loadXmlFile($importFile);
			}
			catch(Exception $e)
			{
				$this->AddLogEntry(JText::_('COM_FORM2CONTENT_ERROR_IMPORT_INVALID_XML_FILE'). ': '.$importFile);
				$this->AddLogEntry($e->getMessage());
				$this->moveFileToDirectory($importFile, $errorDir);
				// Process next file
				continue;
			}
			
			// Check if this is an import file that should be handled by legacy code
			list($xsdMajor, $xsdMinor, $xsdRevision) = explode('.', $xml[0]);
			
			try
			{
				// Schema versions 1.x.x will be handled by legacy importer
				$importer 	= ($xsdMajor >= 2) ? $this : new F2cLegacyForm($this->db);				
				$result 	= $importer->importXml($xml[1]);
				
				$this->AddLogEntry(sprintf(JText::_('COM_FORM2CONTENT_IMPORT_TOTALS'),
						$result['inserted'], $result['updated'], $result['trashed'], $result['deleted']));
				
				// Clean up after a successful import
				if($postAction == F2C_IMPORT_POSTACTION_DELETE)
				{
					JFile::delete($importFile);
				}
				else
				{
					$this->moveFileToDirectory($importFile, $archiveDir);
				}
				
			}
			catch(Exception $e)
			{
				$this->AddLogEntry($e->getMessage());
				$this->moveFileToDirectory($importFile, $errorDir);
			}
		}
		
		// Clean up the temporary folder for files and images
		JFolder::delete($importTmpPath);
		
		$this->AddLogEntry(JText::_('COM_FORM2CONTENT_IMPORT_ENDED'));
	}
	
	/**
	 * Import a multiple Articles from XML source
	 *
	 * @param   object  $xml	XML node containing the article
	 *
	 * @return 	array			Array with grouped totals
	 *
	 * @since   6.17.0
	 */
	public function importXml($xml)
	{
		$results = array('inserted' => 0, 'updated' => 0, 'deleted' => 0, 'trashed' => 0);
		
		foreach ($xml as $xmlForm)
		{
			$result = $this->importArticle($xmlForm);
			$results[$result]++; 
		}
		
		return $results;
	}
	
	/**
	 * Load an XML file containing Article definitions and verify it against its schema
	 *
	 * @param   string  $filename	filename pointing to XML file
	 *
	 * @return 	array			Array: index 0 is the version, index 1 the XML document
	 *
	 * @since   6.17.0
	 */
	public function loadXmlFile($filename)
	{
		$result = array();
		$domXml = new DOMDocument();
		
		libxml_use_internal_errors(true);
		
		if (!$domXml->load($filename))
		{
			$messages = array();
			
			foreach (libxml_get_errors() as $error) 
			{
				$messages[] = $error->message;
			}
			
			throw new Exception(implode("\n", $messages));
		}
		
		$schemaTag = $domXml->documentElement->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'schemaLocation');
		
		if(!$schemaTag)
		{
			throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_NO_SCHEMA_FILE'));
		}
		
		$pairs = preg_split('/\s+/', $schemaTag);
		$pairCount = count($pairs);
		
		if ($pairCount <= 1)
		{
			throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_INVALID_SCHEMA_LOCATION'));
		}
		
		$schemaLocation = JPATH_SITE.'/media/com_form2content/schemas/'.$pairs[1];
		
		if(!JFile::exists($schemaLocation))
		{
			throw new Exception(sprintf(JText::_('COM_FORM2CONTENT_ERROR_SCHEMA_NOT_FOUND'), $schemaLocation));
		}
		
		if(!$domXml->schemaValidate($schemaLocation))
		{
			$messages = array();
			
			foreach (libxml_get_errors() as $error)
			{
				$messages[] = $error->message;
			}
			
			throw new Exception(implode("\n", $messages));
		}
		
		// add the version of the schema to the results so we know how to handle the document
		$versionparts 	= explode('_', str_replace('.xsd', '', $pairs[1]));
		$result[0] 		= $versionparts[2].'.'.$versionparts[3].'.'.$versionparts[4];
		// We're dealing with a valid XML file, convert it for further handling to a SimpleXMLExtended document
		$result[1] = simplexml_import_dom($domXml, $class_name = 'SimpleXMLExtended');
		
		return $result;
	}
	
	/**
	 * Import a single Article from XML source
	 *
	 * @param   object  $xmlArticle			XML node containing the article
	 *
	 * @return 	string		Result: inserted, updated, trashed, deleted
	 *
	 * @since   6.17.0
	 */
	public function importArticle($xmlArticle)
	{
		$form 			= new Form2ContentModelForm(array('ignore_request' => true));
		$rules			= array();
		$data 			= array();
		$fieldData 		= array();
		
		$xmlArticle->registerXPathNamespace('f', $this->namespace);
		
		// Resolve the Content Type
		$contentTypeId = $this->dicContentTypesByName[(string)$xmlArticle->contenttype];
		
		if(!$contentTypeId)
		{
			throw new Exception(sprintf(JText::_('COM_FORM2CONTENT_ERROR_CONTENTTYPE_NOT_RESOLVED'), (string)$xmlArticle->contenttype));
		}
		
		$contentType 	= F2cFactory::getContentType($contentTypeId);
		$articleId 		= $this->getArticleId($xmlArticle->id, $contentTypeId);
		$state 			= $this->getState($contentType, $xmlArticle);

		if($state == 'trashed' || $state == 'deleted')
		{
			$pks = array((int)$articleId);

			if($state == 'trashed')
			{
				$form->publish($pks, F2C_STATE_TRASH, true);
			}
			else 
			{
				$form->delete($pks, true);
			}
			
			// stop further processing
			return $state;
		}
		
		// Load the existing form
		$existingForm 				= $form->getItem($articleId);
		$existingFieldData 			= $existingForm->fields;
		
		$data['id'] 				= $articleId;
		$data['projectid'] 			= $contentTypeId;
		$data['attribs'] 			= $xmlArticle->attribs->toArray();
		$data['metadata'] 			= $xmlArticle->metadata->toArray();
		
		foreach($contentType->fields as $field)
		{
			$field->reset();
			$fieldData[$field->fieldname] = $field;
		}
		
		if(count($xmlArticle->fields->children()))
		{
			foreach($xmlArticle->fields->children() as $xmlField)
			{
				$f2cField =& $fieldData[(string)$xmlField->fieldname];
				
				if($f2cField == null)
				{
					// Field is present in import XML, but not part of Content Type to which it should be imported
					throw new Exception(sprintf(JText::_('COM_FORM2CONTENT_ERROR_FIELD_NOT_PART_OF_CONTENTTYPE'), (string)$xmlField->fieldname, (string)$xmlArticle->contenttype));
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
			$data['isCron'] 			= true;
			
			if(!$form->saveCron($data))
			{
				throw new Exception('Error for article #'.$data['id'].' ('.$data['title'].'): ' . $form->getError());
			}
			
			return $articleId ? 'updated' : 'inserted';
		}
	}
	
	/**
	 * Determine the id for the article to be imported
	 *
	 * @param   object  $idNode			XML node containing the id
	 * @param   int  	$contentTypeId	Id of the Content Type
	 *
	 * @return 	int		The id of the Article
	 *
	 * @since   6.17.0
	 */
	private function getArticleId($idNode, $contentTypeId)
	{
		if($this->f2cConfig->get('import_write_action', F2C_IMPORT_WRITEMODE_UPDATE) == F2C_IMPORT_WRITEMODE_CREATENEW)
		{
			// Always create a new F2C Article
			return 0;
		}
		
		// Check if an alternate key was specified
		if($idNode->attributes()->fieldname)
		{
			// Find the id of the form through the alternate key
			$query = $this->db->getQuery(true);
			
			$query->select('frm.id');
			$query->from('#__f2c_form frm');
			$query->join('inner', '#__f2c_fieldcontent flc on frm.id = flc.formid');
			$query->join('inner', '#__f2c_projectfields pfl on flc.fieldid = pfl.id');
			$query->where('flc.content = ' . $this->db->quote((string)$idNode));
			$query->where('pfl.fieldname = ' . $this->db->quote((string)$idNode->attributes()->fieldname));
			$query->where('frm.state in (0,1,-2)');
			
			$this->db->setQuery($query);

			// If the alternate key was found use that form id
			return $this->db->loadResult() ?? 0;
		}
		
		// Verify that this article exists for the specified content type
		$formTemp = $this->model->getItem((int)$idNode);
		
		if($formTemp->projectid != $contentTypeId)
		{
			// There's no such article...create a new one
			return 0;
		}
		
		return (int)$idNode;
	}	
	
	/**
	 * Load a dictionary to load Content Types by name
	 *
	 * @return 	array		Dictionary of Content Type id's by name
	 *
	 * @since   6.17.0
	 */
	private function loadDictionaryContentTypes()
	{
		$query = $this->db->getQuery(true);
		
		$query->select('id, title')->from('#__f2c_project');
		
		$this->db->setQuery($query);
		
		return $this->db->loadAssocList('title', 'id');
	}
	
	/**
	 * Get the state for the current Article
	 *
	 * @param   object  	$contentType	Content Type
	 * @param   object  	$xmlArticle		XML containing Article data
	 * 
	 * @return 	string		Article's state
	 *
	 * @since   6.17.0
	 */
	private function getState($contentType, $xmlArticle)
	{
		$stateFieldName = null;
		
		// Find the name of the state field in the Content Type
		foreach ($contentType->fields as $field)
		{
			if($field->fieldtypename == 'Joomlapublished')
			{
				$stateFieldName= $field->fieldname;
			}
		}
		
		if($stateFieldName == null)
		{
			throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_F2C_STATE_FIELD_MISSING'));
		}
		
		// Find the value for the state in the Article data
		$result = $xmlArticle->xpath('.//f:field[f:fieldname="'.$stateFieldName.'"]');
		
		return $result[0]->contentSingleTextValue->value;
	}
	
	/**
	 * Checks directories before an import takes place
	 * Throws Exception when directories are not correctly configured
	 *
	 * @return 	void
	 *
	 * @since   6.17.0
	 */
	private function preImportCheck()
	{
		$importDir 	= $this->f2cConfig->get('import_dir');
		$archiveDir	= $this->f2cConfig->get('import_archive_dir');
		$errorDir	= $this->f2cConfig->get('import_error_dir');
		$postAction = $this->f2cConfig->get('import_post_action', 1);
		
		if(empty($importDir))
		{
			throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_IMPORT_DIR_EMPTY'));
		}
		
		if(!JFolder::exists($importDir))
		{
			throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_IMPORT_DIR_DOES_NOT_EXIST'));
		}
		
		if(empty($errorDir))
		{
			throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_ERROR_DIR_EMPTY'));
		}
		
		if(!JFolder::exists($errorDir))
		{
			throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_ERROR_DIR_DOES_NOT_EXIST'));
		}
		
		if($postAction == F2C_IMPORT_POSTACTION_ARCHIVE)
		{
			if(empty($archiveDir))
			{
				throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_IMPORT_ARCHIVE_DIR_EMPTY'));
			}
			
			if(!JFolder::exists($archiveDir))
			{
				throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_ARCHIVE_DIR_DOES_NOT_EXIST'));
			}
		}
	}
	
	/**
	 * Move a file to a given directory. Prepend its name with the current date
	 *
	 * @param   string  	$file		The file to be moved
	 * @param   string  	$directory	The directory where it should be moved to
	 * 
	 * @return 	void
	 *
	 * @since   6.17.0
	 */
	private function moveFileToDirectory($file, $directory)
	{
		$timestamp = new JDate();
		$fileName = $timestamp->format('YmdHis'). ' ' . basename($file);
		JFile::move($file, Path::Combine($directory, $fileName));
	}
	
	private function AddLogEntry($msg)
	{
		$this->logMessages[] = JFactory::getDate()->format('c') . ';' . $msg;
	}
}