<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldFileUpload extends F2cFieldBase
{	
	public $createThumbnail = false;
	public $baseDir; 
	
	function __construct($field)
	{
		$this->reset();		
		parent::__construct($field);
		$this->baseDir = Path::Combine(JPATH_SITE, $this->f2cConfig->get('files_path'));
	}
	
	public function getPrefix()
	{
		return 'ful';
	}
	
	public function reset()
	{
		$this->values['FILENAME'] 			= '';
		$this->internal['method'] 			= '';
		$this->internal['delete'] 			= '';
		$this->internal['filelocation']		= '';
		$this->internal['fieldcontentid']	= null;	
	}
	
	public function render($translatedFields, $contentTypeSettings, $parms = array(), $form, $formId)
	{
		$displayData	= array();
		$link			= '';
		$tmpFileName	= '';
		$extensions		= (array)$this->settings->get('ful_whitelist');
		
		if($this->values['FILENAME'])
		{
			$baseName = basename($this->internal['filelocation']);
			
			if($baseName != $this->values['FILENAME'])
			{
				// temp file => not processed yet
				$tmpFileName = $baseName;	
			}
		}
		
		if($this->values['FILENAME'])
		{
			if($tmpFileName)
			{
				$link = Path::Combine(F2cUri::GetClientRoot().$this->f2cConfig->get('files_path'), $tmpFileName);
			}
			else 
			{
				$link = Path::Combine(self::GetFileUrl($this->projectid, $formId, $this->id), $this->values['FILENAME']);	
			}
		}
		
		$displayData['tmpFileName']			= $tmpFileName;
		$displayData['extensions']			= $extensions;
		$displayData['link']				= $link;
		$displayData['deleteAttribs']		= $this->settings->get('ful_attributes_delete') ? $this->settings->get('ful_attributes_delete') : 'class="btn"';
		$displayData['jsExtensionsArray']	= $this->createJsExtensionsArray($extensions);
		
		$layout = new JLayoutFile('field.fileupload');
		
		return $this->renderLayout('fileupload', $displayData, $translatedFields, $contentTypeSettings);						
	}
	
	public function prepareSubmittedData($formId)
	{
		$jinput = JFactory::getApplication()->input;
		
		$fileName 					= $jinput->getString($this->elementId.'_tmpfilename'); 
		$this->internal['method'] 	= '';
		
		if(!empty($fileName))
		{
			$this->internal['method']	= 'upload';
		}
		
		$this->internal['filelocation']		= $fileName ? Path::Combine($this->baseDir, $fileName) : '';
		$this->internal['fieldcontentid'] 	= $jinput->getInt('hid'.$this->elementId);
		$this->internal['delete'] 			= $jinput->get($this->elementId . '_del');
		$this->internal['currentfilename']	= $jinput->getString($this->elementId . '_filename');
		$this->values['FILENAME'] 			= $jinput->getString($this->elementId.'_originalfilename');

		return $this;
	}
	
	public function store($formId)
	{
		$content 				= array();
		$fieldId 				= $this->internal['fieldcontentid'];
		$srcFile 				= $this->internal['filelocation'];
		$fileName				= '';
		$fieldContent 			= '';
		$dstPath				= self::GetFilePath($this->projectid, $formId, $this->id);
		$db						= JFactory::getDbo();
		$currentFilename		= '';
		
		if(empty($this->internal['method']))
		{
			// no file was uploaded
			$srcFile = '';
		}
		
		// Download remote file first, because the original ones might be deleted later
		if($srcFile && $this->internal['method'] == 'remote')
		{
			$tmpFile = Path::Combine($this->baseDir, uniqid('f2c', true) . '.tmp');
			$this->downloadFile($srcFile, $tmpFile);
		}
		
		if(!empty($fieldId))
		{
			// Load the file field
			$query = $db->getQuery(true);
			$query->select('content')->from('#__f2c_fieldcontent')->where('id='.(int)$fieldId);
			$db->setQuery($query);
			$currentFilename = $db->loadResult();
		}
		
		if(($srcFile && $currentFilename) || $this->internal['delete'])
		{
			// delete the current file
			$file = Path::Combine(self::GetFilePath($this->projectid, $formId, $this->id), $currentFilename);

			if(JFile::exists($file))
			{
				JFile::delete($file);
			}
		}
		
		// Check if the image is marked for deletion (e.g. no replacement image)
		if($this->internal['delete'])
		{
			$content[] 	= new F2cFieldHelperContent($fieldId, '', '', 'DELETE');
			return $content;	
		}

		if($srcFile)
		{
			$this->checkExtension($this->values['FILENAME']);
			
			$fileName		= $this->createUniqueFilename($dstPath, $this->values['FILENAME']);
			$fileLocation 	= Path::Combine($dstPath, $fileName);
			
			if(!JFolder::exists($dstPath))
			{
				JFolder::create($dstPath);
			}

			switch($this->internal['method'])
			{
				case 'upload':
				case 'browse':
					// Move file from temp storage
					JFile::move($srcFile, $fileLocation);
					break;
					
				case 'copy':	
					JFile::copy($srcFile, $fileLocation);
					break;
					
				case 'remote':
					// Move file from temp storage
					JFile::move($tmpFile, $fileLocation);
					break;	
			}			
		}
		else 
		{
			$fileName = $this->values['FILENAME'];
		}
		
		$value 		= $fileName;		
		$action 	= ($value) ? (($fieldId) ? 'UPDATE' : 'INSERT') : (($fieldId) ? 'DELETE' : '');				
		$content[] 	= new F2cFieldHelperContent($fieldId, 'VALUE', $value, $action);

		return $content;	
	}
	
	public function validate(&$data, $item)
	{
		if($this->settings->get('requiredfield'))
		{
			$fieldContentId	= $this->internal['fieldcontentid'];
						
			// Test for presence of temp image file
			if($this->internal['filelocation'] && JFile::exists($this->internal['filelocation']))
			{
				return;
			}
					
			if($this->internal['delete'])
			{
				throw new Exception($this->getRequiredFieldErrorMessage());
			}

			if(($this->internal['method'] == 'copy' || $this->internal['method'] == 'remote') && !empty($this->internal['filelocation']))
			{
				return;   	
		   	}
			
			if($fieldContentId)
			{
				// check if a file exists
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				
				$query->select('content')->from('#__f2c_fieldcontent')->where('id='.$fieldContentId);
				
				$db->setQuery($query);
				
				$content = $db->loadResult();
				
				if(empty($content))
				{
					throw new Exception($this->getRequiredFieldErrorMessage());
				}
				
				return;
			}
			
			throw new Exception($this->getRequiredFieldErrorMessage());
		}
	}
	
	public function copy($formId)
	{
		$this->internal['fieldcontentid'] 	= null;
		$this->internal['method'] 			= 'copy';
		$this->internal['filelocation'] 	= $this->values['FILENAME'] ? Path::Combine(self::GetFilePath($this->projectid, $formId, $this->id), $this->values['FILENAME']) : '';
	}
	
	public function export($xmlFields, $form)
	{
		$exportFileMode	= $this->f2cConfig->get('export_file_mode', 0);
		
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	
      	$xmlFieldContent = $xmlField->addChild('contentFileUpload');
      	$xmlFieldContent->filename = $this->values['FILENAME'];
      						
      	if($this->values['FILENAME'])
      	{	      						
	    	switch($exportFileMode)
	      	{
	      		case F2C_EXPORT_FILEMODE_ENCAPSULATE:
	      			$fileLocation 	= Path::Combine(self::GetFilePath($this->projectid, $form->id, $this->id), $this->values['FILENAME']);
	      			$xmlFile 		= $xmlFieldContent->addChild('file');
					$xmlFile->addCData(base64_encode($this->getFileContents($fileLocation)));      							
	      			$xmlFile->addAttribute('includemode', 'include');
					break;
	      								
	      		case F2C_EXPORT_FILEMODE_LOCAL:
	      			$fileLocation 	= Path::Combine(self::GetFilePath($this->projectid, $form->id, $this->id), $this->values['FILENAME']);
	      			$xmlFile 		= $xmlFieldContent->addChild('file', $fileLocation);
	      			$xmlFile->addAttribute('includemode', 'path');
	      			break;
	      								
	      		case F2C_EXPORT_FILEMODE_REMOTE:
	      			$fileLocation 	= Path::Combine(self::GetFileUrl($this->projectid, $form->id, $this->id), $this->values['FILENAME']);
	      			$xmlFile 		= $xmlFieldContent->addChild('file', $fileLocation);
	      			$xmlFile->addAttribute('includemode', 'url');
	      			break;
	      	}
      	}
      	else 
      	{
      		// no file
   			$xmlFile = $xmlFieldContent->addChild('file', '');
   			$xmlFile->addAttribute('includemode', 'url');
      	}
    }
    
	public function import($xmlField, $existingInternalData, &$data)
	{
      	$this->values['FILENAME'] 			= (string)$xmlField->contentFileUpload->filename;
      	$this->internal['fieldcontentid'] 	= $data['id'] ? $existingInternalData['fieldcontentid'] : 0;
      	$this->internal['method'] 			= 'copy';
      	$this->internal['delete']			= (string)$xmlField->contentFileUpload->file == '' ? 1 : 0;
      						
      	switch((string)$xmlField->contentFileUpload->file->attributes()->includemode)
      	{
      		case 'url':
      			$this->internal['filelocation'] = (string)$xmlField->contentFileUpload->file;
      			$this->internal['method'] = 'remote';
      			break;
      		case 'path':
      			$this->internal['filelocation'] = (string)$xmlField->contentFileUpload->file;
      			break;
      		case 'include':
	      		// encapsulated file
	      		$importTmpPath 	= Path::Combine(JFactory::getConfig()->get('tmp_path'), 'f2c_import');
	      		$tmpFolder 		= Path::Combine($importTmpPath, 'c'.$this->projectid.DIRECTORY_SEPARATOR.'a'.$data['id'].DIRECTORY_SEPARATOR.'f'.$this->id);
	      							
	      		if(!JFolder::exists($tmpFolder))
	      		{
	      			JFolder::create($tmpFolder);
	      		}
	      							
	      		$tmpFile 	= Path::Combine($tmpFolder, $this->values['FILENAME']);
	      		$contents 	= base64_decode((string)$xmlField->contentFileUpload->file);
	      				
	      		JFile::write($tmpFile, $contents);      							
	      		$this->internal['filelocation'] = $tmpFile;
      			break;
      	}
	}
	
	public function addTemplateVar($templateEngine, $form)
	{
		if($this->values['FILENAME'])
		{
			if($this->settings->get('ful_output_mode') == 0)
			{				
				$templateEngine->addVar($this->fieldname, Path::Combine(self::GetFileUrl($this->projectid, $form->id, $this->id), $this->values['FILENAME']));						
			}
			else
			{
				$templateEngine->addVar($this->fieldname, '<a href="'.Path::Combine(self::GetFileUrl($this->projectid, $form->id, $this->id), $this->values['FILENAME']).'" target="_blank">' . $this->stringHTMLSafe($this->values['FILENAME']) . '</a>');
			}
			
			$templateEngine->addVar($this->fieldname.'_FILENAME', $this->values['FILENAME']);
			$templateEngine->addVar($this->fieldname.'_URL_RELATIVE', Path::Combine(self::GetFileUrl($this->projectid, $form->id, $this->id, true), $this->values['FILENAME']));
		}
		else
		{
			// no file was specified
			$templateEngine->addVar($this->fieldname, '');
			$templateEngine->addVar($this->fieldname.'_FILENAME', '');
			$templateEngine->addVar($this->fieldname.'_URL_RELATIVE', '');
		} 
	}
	
	public function getTemplateParameterNames()
	{
		$names = array(	StringHelper::strtoupper($this->fieldname).'_FILENAME',
						StringHelper::strtoupper($this->fieldname).'_URL_RELATIVE');
		
		return array_merge($names, parent::getTemplateParameterNames());
	}
	
	public function setData($data)
	{
		$this->values['FILENAME'] 			= $data->content;
		$this->internal['method'] 			= '';
		$this->internal['delete'] 			= '';
		$this->internal['fieldcontentid']	= $data->fieldcontentid;

		if($data->content)
		{
			$this->internal['filelocation'] = Path::Combine(self::GetFilePath($data->projectid, $data->formid, $data->id), $data->content);						
		}
	}
	
	public function cancel()
	{
		$jinput = JFactory::getApplication()->input;
		
		// check if temporary images were uploaded
		if($tmpFile = $jinput->getString('t'.$this->id.'_tmpfilename'))
		{
			// Remove temporary images
			JFile::delete(Path::Combine($this->baseDir, $tmpFile));
		}
	}
	
	public function clearFile()
	{
		$jinput = JFactory::getApplication()->input;
		$file = Path::Combine(Path::Combine(JPATH_SITE, $this->f2cConfig->get('files_path')), $jinput->get('file'));	
				
		if(JFile::exists($file))
		{
			JFile::delete($file);
		}
	}
	
	public function postUploadCheck(&$resultInfo, $file)
	{
		$this->checkExtension($resultInfo['originalfilename']);
		
		$maxUploadSize = (int)$this->settings->get('ful_max_file_size');
		
		if($maxUploadSize != 0 && (int)($file['size']/1024) > $maxUploadSize)
		{
			throw new Exception(sprintf(JText::_('COM_FORM2CONTENT_ERROR_UPLOAD_MAX_SIZE_FIELD'), $maxUploadSize));
		}

		return true;
	}
	
	private function checkExtension($filename)
	{
		// check extension
		$extension = strtolower(JFile::getExt($filename));

		if(count((array)$this->settings->get('ful_whitelist')))
		{
			if(!array_key_exists($extension, (array)$this->settings->get('ful_whitelist')))
			{
				throw new Exception(sprintf(JText::_('COM_FORM2CONTENT_ERROR_FILE_UPLOAD_EXTENSION_NOT_ALLOWED'), $extension));
			}
		}

		if(count((array)$this->settings->get('ful_blacklist')))
		{
			if(array_key_exists($extension, (array)$this->settings->get('ful_blacklist')))
			{
				throw new Exception(sprintf(JText::_('COM_FORM2CONTENT_ERROR_FILE_UPLOAD_EXTENSION_NOT_ALLOWED'), $extension));
			}
		}		
	}
	
	public function deleteContentType()
	{
		// remove the base files dir
		$baseDir = Path::Combine(self::GetFilesRootPath(), 'c'.$this->projectid);
		
		if(JFolder::exists($baseDir))
		{
			JFolder::delete($baseDir);
		}
	}
	
	public function deleteArticle($formId)
	{
		Path::Remove((Path::Combine(self::GetFilesRootPath(), 'c'.$this->projectid.'/a'.$formId)));
	}
	
	public static function GetFileUrl($contentTypeId, $articleId, $fieldId, $relative = false)
	{
		$path = ($relative) ? '' : F2cUri::GetClientRoot();
		$path.= F2cFactory::getConfig()->get('files_path').'/c'.$contentTypeId.'/a'.$articleId.'/f'.$fieldId;
		return $path;
	}

	public static function GetFilesRootPath($relative = false)
	{
		$path = $relative ? '' : JPATH_SITE.DIRECTORY_SEPARATOR;
		$path .= str_replace('/', DIRECTORY_SEPARATOR, F2cFactory::getConfig()->get('files_path')).DIRECTORY_SEPARATOR;
		return $path;
	}
	
	public static function GetFilesPath($projectId, $formId, $relative = false)
	{
		return Path::Combine(self::GetFilesRootPath($relative), 'c'.$projectId.DIRECTORY_SEPARATOR.'a'.$formId);
	}

	public static function GetFilePath($contentTypeId, $articleId, $fieldId, $relative = false)
	{
		return Path::Combine(self::GetFilesRootPath($relative), 'c'.$contentTypeId.DIRECTORY_SEPARATOR.'a'.$articleId.DIRECTORY_SEPARATOR.'f'.$fieldId);
	}

	public static function GetFilePathFormRoot($contentTypeId, $articleId, $relative = false)
	{
		return Path::Combine(self::GetFilesRootPath($relative), 'c'.$contentTypeId.DIRECTORY_SEPARATOR.'a'.$articleId);
	}
}
?>