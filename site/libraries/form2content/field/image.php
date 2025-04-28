<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use \Joomla\Registry\Registry;
use Joomla\String\StringHelper;

class F2cFieldImage extends F2cFieldBase
{	
	public $createThumbnail = true;
	public $baseDir; 
	
	function __construct($field)
	{
		$this->reset();
		parent::__construct($field);
		$this->baseDir = Path::Combine(JPATH_SITE, $this->f2cConfig->get('images_path'));
	}
	
	public function getPrefix()
	{
		return 'img';
	}
	
	public function reset()
	{
		$this->values['FILENAME'] 			= '';
		$this->values['ALT'] 				= '';
		$this->values['TITLE'] 				= '';
		$this->values['WIDTH'] 				= null;
		$this->values['HEIGHT'] 			= null;
		$this->values['WIDTH_THUMBNAIL'] 	= null;
		$this->values['HEIGHT_THUMBNAIL'] 	= null;
		$this->internal['method'] 			= '';
		$this->internal['delete'] 			= '';
		$this->internal['currentfilename']	= '';
		$this->internal['imagelocation']	= '';
		$this->internal['thumblocation']	= '';
		$this->internal['fieldcontentid']	= null;
	}
	
	public function render($translatedFields, $contentTypeSettings, $parms = array(), $form, $formId)
	{
		$displayData = array();
		
		if(!count($parms))
		{
			$parms = JFactory::getApplication()->isClient('site') ? array(50, 100) : array(75, 100);
		}
		
		$thumbSrc 			= '';
		$thumbVis 			= 'display:none;';
		$tmpFileName		= '';
		$cropped			= 0;

		// See which file types we may upload
		switch($this->settings->get('allow_filetype', 0))
		{
			case 0: // jpg, png and gif
				$extensions = array('jpg', 'jpeg', 'png', 'gif');
				break;
			case 1: // jpg
				$extensions = array('jpg', 'jpeg');
				break;
			case 2: // png
				$extensions = array('png');
				break;
			case 3: // gif
				$extensions = array('gif');
				break;
		}
		
		$jsExtensionsArray = $this->createJsExtensionsArray($extensions);
		
		if($this->values['FILENAME'])
		{
			$baseName = basename($this->internal['imagelocation']);
			
			if($baseName != $this->values['FILENAME'])
			{
				// temp file => not processed yet
				$thumbSrc 		= JUri::root(true).Path::Combine($this->f2cConfig->get('images_path'), 'thumb_'.$baseName);	
				$tmpFileName 	= $baseName;	
				$cropped		= $this->internal['cropped'];
			}
			else 
			{
				$thumbSrc = Path::Combine(JUri::root(true), Path::Combine(self::GetThumbnailsUrl($this->projectid, $formId, true, true), $this->values['FILENAME']));
			}
			
			$thumbVis = 'display:block;';
		}
		
		$displayData['tmpFileName']			= $tmpFileName;
		$displayData['cropped']				= $cropped;
		$displayData['form']				= $form;
		$displayData['extensions']			= $extensions;
		$displayData['jsExtensionsArray']	= $jsExtensionsArray;
		$displayData['thumbSrc']			= $thumbSrc;
		$displayData['thumbVis']			= $thumbVis;
		$displayData['attribsDelete']		= $this->settings->get('img_attributes_delete', 'class="btn"');
		$displayData['widthAltText']		= $this->settings->get('img_attributes_alt_text') ? '' : ' width="'.$parms[0].'"';
		$displayData['maxLengthAltText']	= $this->settings->get('img_attributes_alt_text') ? '' : ' maxlength="'.$parms[1].'"';
		$displayData['attribsAltText']		= $this->settings->get('img_attributes_alt_text') ? $this->settings->get('img_attributes_alt_text') : 'class="inputbox"'; 
		$displayData['widthTitle']			= $this->settings->get('img_attributes_title') ? '' : ' width="'.$parms[0].'"';
		$displayData['maxLengthTitle']		= $this->settings->get('img_attributes_title') ? '' : ' maxlength="'.$parms[1].'"';
		$displayData['attribsTitle']		= $this->settings->get('img_attributes_title') ? $this->settings->get('img_attributes_title') : 'class="inputbox"';		

		return $this->renderLayout('image', $displayData, $translatedFields, $contentTypeSettings);						
	}
	
	public function prepareSubmittedData($formId)
	{
		$jinput = JFactory::getApplication()->input;
		
		$imageName 							= $jinput->getString($this->elementId.'_tmpfilename'); 
		$this->internal['imagelocation'] 	= '';
		$this->internal['thumblocation'] 	= '';
		$this->internal['method'] 			= '';
		
		if($imageName)
		{
			$this->internal['imagelocation'] 	= Path::Combine($this->baseDir, $imageName);
			$this->internal['thumblocation'] 	= Path::Combine($this->baseDir, 'thumb_'.$imageName);
			
			// Do we have an upload field?
			if($uploadControl = $jinput->files->get($this->elementId.'_fileupload'))
			{
				$this->internal['method'] = 'upload';	
			}
			else 
			{
				// browse control
				$this->internal['method'] = 'browse';
			}
		}
		else 
		{
			$existingImage = $jinput->getString($this->elementId.'_filename');
			
			if(!empty($existingImage))
			{
				$this->internal['imagelocation'] 	= Path::Combine($this->baseDir, $existingImage);
				$this->internal['thumblocation'] 	= Path::Combine($this->baseDir, 'thumbs/'.$existingImage);
			}
		}
				
		$this->internal['fieldcontentid'] 	= $jinput->getInt('hid'.$this->elementId);
		$this->internal['delete'] 			= $jinput->get($this->elementId . '_del');
		$this->internal['currentfilename']	= $jinput->getString($this->elementId . '_filename');
		$this->internal['cropped']			= $jinput->getInt($this->elementId . '_cropped');
		$this->values['FILENAME'] 			= $jinput->getString($this->elementId.'_originalfilename');
		$this->values['ALT']				= $jinput->getString($this->elementId . '_alt');
		$this->values['TITLE']				= $jinput->getString($this->elementId . '_title');		
		$this->values['WIDTH']				= null;
		$this->values['HEIGHT']				= null;
		$this->values['WIDTH_THUMBNAIL']	= null;
		$this->values['HEIGHT_THUMBNAIL']	= null;

		// Added Brainforge.UK 2025-04-24 ... then no longer seemed necessary
		if (($this->internal['imagelocation'] ?? null) && !is_file($this->internal['imagelocation']))
		{
			//JFactory::getApplication()->enqueueMessage('Invalid: $this->internal[\'imagelocation\']');
			//unset($this->internal['imagelocation']);
		}
		if (($this->internal['thumblocation'] ?? null) && !is_file($this->internal['thumblocation']))
		{
			//JFactory::getApplication()->enqueueMessage('Invalid: $this->internal[\'thumblocation\']');
			//unset($this->internal['thumblocation']);
		}

		return $this;
	}
	
	public function store($formId)
	{
		$content 				= array();
		$fieldId 				= $this->internal['fieldcontentid'];
		// Modified Brainforge.UK 2025-04-24
		$srcImage 				= $this->internal['imagelocation'] ?? null;
		// Modified Brainforge.UK 2025-04-24
		$srcThumb 				= $this->internal['thumblocation'] ?? null;
		// Modified Brainforge.UK 2025-04-24
		$filename				= $this->values['FILENAME'] ?? null;
		$imageContent 			= new Registry();
		$saveImage				= false;
		$imagePath 				= Path::Combine(Path::Combine(self::GetImagesRootPath(), 'p'.$this->projectid), 'f'.$formId);				
		$thumbsPath				= Path::Combine($imagePath, 'thumbs');
		$maxImageWidth 			= $this->settings->get('img_max_width', 10000);
		$maxImageHeight 		= $this->settings->get('img_max_height', 10000);
		$defaultThumbnailWidth 	= $this->f2cConfig->get('default_thumbnail_width', F2C_DEFAULT_THUMBNAIL_WIDTH);
		$defaultThumbnailHeight = $this->f2cConfig->get('default_thumbnail_height', F2C_DEFAULT_THUMBNAIL_HEIGHT);
		$thumbnailWidth 		= $this->settings->get('img_thumb_width', $defaultThumbnailWidth);
		$thumbnailHeight 		= $this->settings->get('img_thumb_height', $defaultThumbnailHeight);
		$db						= JFactory::getDbo();
		
		if(empty($this->internal['method']))
		{
			// no image was uploaded
			$srcImage = '';
		}
		
		// Download remote images first, because the original ones might be deleted later
		if($srcImage && $this->internal['method'] == 'remote')
		{
			$tmpFilename = uniqid('f2c', true) . '.tmp';
			$tmpImage = Path::Combine($this->baseDir, $tmpFilename);
			$tmpThumb = Path::Combine($this->baseDir, 'thumb_'.$tmpFilename);	
			
			$this->downloadFile($srcImage, $tmpImage);
			
			if($srcThumb)
			{
				$this->downloadFile($srcThumb, $tmpThumb);
			}
		}

		// Load the image field
		if($fieldId)
		{
			$query = $db->getQuery(true);
			$query->select('content')->from('#__f2c_fieldcontent')->where('id = ' . (int)$fieldId);
			$db->setQuery($query);
			$result = $db->loadResult();
			
			if($result)
			{
				$imageContent->loadString($result);
			}
		}
		
		if(empty($srcImage))
		{
			// no image was uploaded, but the alt and title tags could be modified
			if($imageContent->get('alt') != $this->values['ALT'] || 
			   $imageContent->get('title') != $this->values['TITLE'])
			{
				$saveImage = true;
			}
		}		

		if(($srcImage && $imageContent->get('filename')) || $this->internal['delete'])
		{
			// delete thumbnail
			$img = Path::Combine(self::GetThumbnailsPath($this->projectid, $formId), $imageContent->get('filename'));
		
			if(JFile::exists($img))
			{
				JFile::delete($img);
			}
	
			// delete image
			$img = Path::Combine(self::GetImagesPath($this->projectid, $formId), $imageContent->get('filename'));
		
			if(JFile::exists($img))
			{
				JFile::delete($img);
			}
		}
		
		// Check if the image is marked for deletion (e.g. no replacement image)
		if($this->internal['delete'])
		{
			$content[] 	= new F2cFieldHelperContent($fieldId, '', '', 'DELETE');
			return $content;	
		}

		if($srcImage)
		{
			$imageFileName		= $this->createUniqueFilename($imagePath, $this->values['FILENAME']);
			$imageFileLocation 	= Path::Combine($imagePath, $imageFileName);
			$thumbnailLocation 	= Path::Combine($thumbsPath, $imageFileName);
			
			if(!JFolder::exists($thumbsPath)) 
			{
				JFolder::create($thumbsPath);
			}
			
			switch($this->internal['method'])
			{
				case 'upload':
				case 'browse':
					// resize image
					if(!ImageHelper::ResizeImage($srcImage, $imageFileLocation, $maxImageWidth, $maxImageHeight, $this->f2cConfig->get('jpeg_quality', 75)))
					{
						throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_IMAGE_RESIZE_FAILED'));
					}
					
					// Move thumbnail image from temp storage
					JFile::move(Path::Combine(dirname($srcImage), 'thumb_'.basename($srcImage)), $thumbnailLocation);
					
					// Delete prepared image
					JFile::delete($srcImage);
					break;
					
				case 'copy':
					JFile::copy($srcImage, $imageFileLocation);
					JFile::copy($srcThumb, $thumbnailLocation);
					break;
					
				case 'remote':
					// resize image
					if(!ImageHelper::ResizeImage($tmpImage, $imageFileLocation, $maxImageWidth, $maxImageHeight, $this->f2cConfig->get('jpeg_quality', 75)))
					{
						throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_IMAGE_RESIZE_FAILED'));
					}
					
					// Check if we need to generate a thumbnail image
					if($srcThumb)
					{
						// copy the thumbnail image
						JFile::copy($tmpThumb, $thumbnailLocation);
					}
					else 
					{
						// create thumbnail image
						if(!ImageHelper::ResizeImage($tmpImage, $thumbnailLocation, $thumbnailWidth, $thumbnailHeight, $this->f2cConfig->get('jpeg_quality', 75)))
						{
							throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_IMAGE_RESIZE_FAILED'));
						}
					}
					
					JFile::delete($tmpImage);
					
					if($srcThumb)
					{
						JFile::delete($tmpThumb);
					}
					break;	
			}
			
			$thumbnail 	= new JImage($thumbnailLocation);
			$image 		= new JImage($imageFileLocation);
			
			$imageContent->set('filename', $imageFileName);
			$imageContent->set('widthThumbnail', $thumbnail->getWidth());
			$imageContent->set('heightThumbnail', $thumbnail->getHeight());
			$imageContent->set('width', $image->getWidth());
			$imageContent->set('height', $image->getHeight());
			
			// Save the image info to the F2C table
			$saveImage = true;									
		}
		
		$imageContent->set('alt', $this->values['ALT']);
		$imageContent->set('title', $this->values['TITLE']);
		
		if($saveImage)								
		{
			$value 		= $imageContent->toString();
			$action 	= ($value) ? (($fieldId) ? 'UPDATE' : 'INSERT') : (($fieldId) ? 'DELETE' : '');				
			$content[] 	= new F2cFieldHelperContent($fieldId, 'VALUE', $value, $action);
		}
				
		return $content;	
	}
	
	public function validate(&$data, $item)
	{
		if($this->settings->get('requiredfield'))
		{
			$fieldContentId	= $this->internal['fieldcontentid'];
						
			// Test for presence of temp image files
			if( $this->internal['imagelocation'] && JFile::exists($this->internal['imagelocation']) &&
				$this->internal['thumblocation'] && JFile::exists($this->internal['thumblocation']))
			{
				return;
			}
					
			if($this->internal['delete'])
			{
				throw new Exception($this->getRequiredFieldErrorMessage());
			}
			
			if(($this->internal['method'] == 'copy' || $this->internal['method'] == 'remote') && !empty($this->internal['imagelocation']))
			{
				return;   	
		   	}
			
			if($fieldContentId)
			{
				// check if an image exists
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				
				$query->select('content')->from('#__f2c_fieldcontent')->where('id='.$fieldContentId);
				
				$db->setQuery($query);
				
				$content = $db->loadResult();
				
				if($content)
				{
					$imageData = new Registry();
					$imageData->loadString($content);
					
					if($imageData->get('filename') != '');
					{
						return;
					}
				}
			}
				
			throw new Exception($this->getRequiredFieldErrorMessage());
		}
	}
	
	public function getClientSideValidationScript(&$validationCounter, $form)
	{
		$script = parent::getClientSideValidationScript($validationCounter, $form);
		
		if($this->settings->get('img_cropping', F2C_FIELD_IMAGE_CROP_NOT_ALLOWED) == F2C_FIELD_IMAGE_CROP_MANDATORY)
		{
			$script .= 'if(document.getElementById("t'.$this->id.'_tmpfilename").value != "" && document.getElementById("t'.$this->id.'_cropped").value != 1)';
			$script .= '{ ';
			$script .= 'alert(\'' . sprintf(JText::_('COM_FORM2CONTENT_ERROR_IMAGE_NOT_CROPPED', true), addslashes($this->title)) . '\'); ';
			$script .= 'return false; } ';
		}
		
		return $script;
	}
	
	public function copy($formId)
	{
		$this->internal['fieldcontentid'] 	= null;
		$this->internal['method'] 			= 'copy';
		$this->internal['imagelocation'] 	= $this->values['FILENAME'] ? Path::Combine(self::GetImagesPath($this->projectid, $formId), $this->values['FILENAME']) : '';
		$this->internal['thumblocation'] 	= $this->values['FILENAME'] ? Path::Combine(self::GetThumbnailsPath($this->projectid, $formId), $this->values['FILENAME']) : '';	
	}

	public function export($xmlFields, $form)
	{
		$exportImageMode = $this->f2cConfig->get('export_images_mode', 0);
		
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
     	$xmlFieldContent = $xmlField->addChild('contentImage');
      	$xmlFieldContent->filename = $this->values['FILENAME'];
      	$xmlFieldContent->alt = $this->values['ALT'];
      	$xmlFieldContent->title = $this->values['TITLE'];
      	$xmlFieldContent->width = $this->values['WIDTH'];
      	$xmlFieldContent->height = $this->values['HEIGHT'];
      	$xmlFieldContent->width_thumbnail = $this->values['WIDTH_THUMBNAIL'];
      	$xmlFieldContent->height_thumbnail = $this->values['HEIGHT_THUMBNAIL'];
      						
      	if($this->values['FILENAME'])
      	{	      						
	      	switch($exportImageMode)
	      	{
	      		case F2C_EXPORT_FILEMODE_ENCAPSULATE:
	      			$imageLocation 	= Path::Combine(self::GetImagesPath($this->projectid, $form->id), $this->values['FILENAME']);
	      			$thumbLocation 	= Path::Combine(self::GetThumbnailsPath($this->projectid, $form->id), $this->values['FILENAME']);
	      			$xmlImage 		= $xmlFieldContent->addChild('image');
					$xmlThumb 		= $xmlFieldContent->addChild('thumbnail');
	      			$xmlImage->addCData(base64_encode($this->getFileContents($imageLocation)));  
					$xmlImage->addAttribute('includemode', 'include');								      	
					$xmlThumb->addCData(base64_encode($this->getFileContents($thumbLocation)));      							
	      			break;
	      								
	      		case F2C_EXPORT_FILEMODE_LOCAL:
	      			$imageLocation 	= Path::Combine(self::GetImagesPath($this->projectid, $form->id), $this->values['FILENAME']);
	      			$thumbLocation 	= Path::Combine(self::GetThumbnailsPath($this->projectid, $form->id), $this->values['FILENAME']);
	      			$xmlImage 		= $xmlFieldContent->addChild('image', self::valueReplace($imageLocation));
	      			$xmlThumb 		= $xmlFieldContent->addChild('thumbnail', self::valueReplace($thumbLocation));
	      			$xmlImage->addAttribute('includemode', 'path');
	      			break;
	      								
	      		case F2C_EXPORT_FILEMODE_REMOTE:
	      			$imageLocation 	= Path::Combine(self::GetImagesUrl($this->projectid, $form->id), $this->values['FILENAME']);
	      			$thumbLocation 	= Path::Combine(self::GetThumbnailsUrl($this->projectid, $form->id), $this->values['FILENAME']);
	      			$xmlImage 		= $xmlFieldContent->addChild('image', self::valueReplace($imageLocation));
	      			$xmlThumb		= $xmlFieldContent->addChild('thumbnail', self::valueReplace($thumbLocation));
	      			$xmlImage->addAttribute('includemode', 'url');
	      			break;
	      	}
      	}      
      	else 
      	{
      		// no image
   			$xmlImage = $xmlFieldContent->addChild('image', '');
   			$xmlThumb = $xmlFieldContent->addChild('thumbnail', '');
   			$xmlImage->addAttribute('includemode', 'url');
      	}						
   }
   
	public function import($xmlField, $existingInternalData, &$data)
	{
      	$this->values['FILENAME'] 			= (string)$xmlField->contentImage->filename;
      	$this->values['ALT'] 				= (string)$xmlField->contentImage->alt;
      	$this->values['TITLE'] 				= (string)$xmlField->contentImage->title;
      	$this->values['WIDTH'] 				= (string)$xmlField->contentImage->width;
      	$this->values['HEIGHT'] 			= (string)$xmlField->contentImage->height;
      	$this->values['WIDTH_THUMBNAIL'] 	= (string)$xmlField->contentImage->width_thumbnail;
      	$this->values['HEIGHT_THUMBNAIL'] 	= (string)$xmlField->contentImage->height_thumbnail;	      						
      	$this->internal['fieldcontentid'] 	= $data['id'] ? $existingInternalData['fieldcontentid'] : 0;
      	$this->internal['method'] 			= 'copy';
      	$this->internal['delete']			= (string)$xmlField->contentImage->image == '' ? 1 : 0;
      						
      	switch((string)$xmlField->contentImage->image->attributes()->includemode)
      	{
      		case 'url':
      			$this->internal['imagelocation'] 	= (string)$xmlField->contentImage->image;
      			$this->internal['thumblocation'] 	= (string)$xmlField->contentImage->thumbnail;
      			$this->internal['method'] 			= 'remote';
      			break;
      		case 'path':
      			$this->internal['imagelocation'] 	= (string)$xmlField->contentImage->image;
      			$this->internal['thumblocation'] 	= (string)$xmlField->contentImage->thumbnail;
      			break;
      		case 'include':
	      		// encapsulated image	      							
	      		$importTmpPath 	= Path::Combine(JFactory::getConfig()->get('tmp_path'), 'f2c_import');
      			$tmpFolder 		= Path::Combine($importTmpPath, 'c'.$this->projectid.DIRECTORY_SEPARATOR.'a'.$data['id'].DIRECTORY_SEPARATOR.'f'.$this->id);
	      		$tmpThumbs 		= Path::Combine($tmpFolder, 'thumbs');
	      							
	      		if(!JFolder::exists($tmpThumbs))
	      		{
	      			JFolder::create($tmpThumbs);
	      		}
	      							
	      		$tmpImage = Path::Combine($tmpFolder, $this->values['FILENAME']);
	      		$tmpThumb = Path::Combine($tmpThumbs, $this->values['FILENAME']);
	      							
	      		$imageBase64Decoded = base64_decode((string)$xmlField->contentImage->image);
	      		$thumbBase64Decoded = base64_decode((string)$xmlField->contentImage->thumbnail);
	      		JFile::write($tmpImage, $imageBase64Decoded);
	      		JFile::write($tmpThumb, $thumbBase64Decoded);
	      							 							
	      		$this->internal['imagelocation'] = $tmpImage;
	      		$this->internal['thumblocation'] = $tmpThumb;
      			break;
      	}
	}	

	public function addTemplateVar($templateEngine, $form)
	{
		if($this->values['FILENAME'])
		{
			if($this->settings->get('img_output_mode') == 0)
			{				
				$templateEngine->addVar($this->fieldname, self::GetImagesUrl($this->projectid, $form->id) . $this->values['FILENAME']);						
			}
			else
			{
				$tagWidth = ($this->values['WIDTH'] > 0) ? ' width="'.$this->values['WIDTH'].'"' : '';
				$tagHeight = ($this->values['HEIGHT'] > 0) ? ' height="'.$this->values['HEIGHT'].'"' : '';
				$templateEngine->addVar($this->fieldname, '<img src="' . self::GetImagesUrl($this->projectid, $form->id) . $this->values['FILENAME'] . '" alt="' . $this->values['ALT'] . '" title="' . $this->values['TITLE'] . '"' . $tagWidth . $tagHeight . '/>');
			}

			$templateEngine->addVar($this->fieldname.'_RAW', self::GetImagesUrl($this->projectid, $form->id) . $this->values['FILENAME']);
			
			// add image information
			$templateEngine->addVar($this->fieldname.'_WIDTH', ($this->values['WIDTH'] > 0) ? $this->values['WIDTH'] : '');					
			$templateEngine->addVar($this->fieldname.'_HEIGHT', ($this->values['HEIGHT'] > 0) ? $this->values['HEIGHT'] : '');					
			$templateEngine->addVar($this->fieldname.'_WIDTH_THUMB', ($this->values['WIDTH_THUMBNAIL'] > 0) ? $this->values['WIDTH_THUMBNAIL'] : '');					
			$templateEngine->addVar($this->fieldname.'_HEIGHT_THUMB', ($this->values['HEIGHT_THUMBNAIL'] > 0) ? $this->values['HEIGHT_THUMBNAIL'] : '');					

			// add image urls
			$templateEngine->addVar($this->fieldname.'_PATH_ABSOLUTE', Path::Combine(self::GetImagesPath($this->projectid, $form->id, false), $this->values['FILENAME']));
			$templateEngine->addVar($this->fieldname.'_PATH_RELATIVE', Path::Combine(self::GetImagesPath($this->projectid, $form->id, true), $this->values['FILENAME']));
			
			// add thumbnail urls
			$templateEngine->addVar($this->fieldname.'_THUMB_URL_ABSOLUTE', Path::Combine(self::GetThumbnailsUrl($this->projectid, $form->id), $this->values['FILENAME']));
			$templateEngine->addVar($this->fieldname.'_THUMB_URL_RELATIVE', Path::Combine(self::GetThumbnailsUrl($this->projectid, $form->id, true), $this->values['FILENAME']));			
		}
		else
		{
			// no image was specified
			$templateEngine->addVar($this->fieldname, '');
			$templateEngine->addVar($this->fieldname.'_RAW', '');
			$templateEngine->addVar($this->fieldname.'_PATH_ABSOLUTE', '');
			$templateEngine->addVar($this->fieldname.'_PATH_RELATIVE', '');
			$templateEngine->addVar($this->fieldname.'_THUMB_URL_ABSOLUTE', '');
			$templateEngine->addVar($this->fieldname.'_THUMB_URL_RELATIVE', '');
		}

		$templateEngine->addVar($this->fieldname.'_ALT', $this->stringHTMLSafe($this->values['ALT']));					
		$templateEngine->addVar($this->fieldname.'_TITLE', $this->stringHTMLSafe($this->values['TITLE']));					
	}

	public function getTemplateParameterNames()
	{
		$uFieldName = StringHelper::strtoupper($this->fieldname);
		
		$names = array(	$uFieldName.'_RAW',
						$uFieldName.'_IMAGE', 
						$uFieldName.'_ALT',
						$uFieldName.'_TITLE',
						$uFieldName.'_WIDTH',
						$uFieldName.'_HEIGHT',
						$uFieldName.'_WIDTH_THUMB',
						$uFieldName.'_HEIGHT_THUMB',
						$uFieldName.'_PATH_ABSOLUTE',
						$uFieldName.'_PATH_RELATIVE',
						$uFieldName.'_THUMB_URL_ABSOLUTE',
						$uFieldName.'_THUMB_URL_RELATIVE');
		
		return array_merge($names, parent::getTemplateParameterNames());
	}
	
	public function setData($data)
	{
		$this->internal['fieldcontentid']	= $data->fieldcontentid;
		$values 							= new Registry($data->content);
		$this->values['FILENAME'] 			= $values->get('filename');
		$this->values['ALT'] 				= $values->get('alt');
		$this->values['TITLE'] 				= $values->get('title');					
		$this->values['WIDTH'] 				= ($values->get('width') != -1) ? $values->get('width') : null;
		$this->values['HEIGHT'] 			= ($values->get('height') != -1) ? $values->get('height') : null;
		$this->values['WIDTH_THUMBNAIL'] 	= ($values->get('widthThumbnail') != -1) ? $values->get('widthThumbnail') : null;
		$this->values['HEIGHT_THUMBNAIL'] 	= ($values->get('heightThumbnail') != -1) ? $values->get('heightThumbnail') : null;						
		$this->internal['method'] 			= '';
		$this->internal['delete'] 			= '';
		$this->internal['currentfilename']	= $values->get('filename');
		
		if($values->get('filename'))
		{
			$this->internal['imagelocation'] = Path::Combine(self::GetImagesPath($data->projectid, $data->formid, false), $values->get('filename'));
			$this->internal['thumblocation'] = Path::Combine(self::GetThumbnailsPath($data->projectid, $data->formid), $values->get('filename'));
		}										
	}
	
	public function preprocessForm(JForm $form)
	{
		$required 	= $this->settings->get('requiredfield') ? 'true' : 'false';
		$rootdir	= $this->settings->get('img_browseserver_root');
		$xml 		= '<field name="t'.$this->id.'_browse" type="F2cFileBrowser" label="" description="" preview="false" selectionrequired="'.$required.'" directory="'.$rootdir.'" onchange="transferImage('.$this->projectid.','.$this->id.');" />';
		$xmlElement = new SimpleXMLElement($xml);
		
		$form->setField($xmlElement);
	}
	
	public function cancel()
	{
		$jinput = JFactory::getApplication()->input;
		
		// check if temporary images were uploaded
		if($tmpImage = $jinput->getString('t'.$this->id.'_tmpfilename'))
		{
			// Remove temporary images
			JFile::delete(Path::Combine($this->baseDir, $tmpImage));
			JFile::delete(Path::Combine($this->baseDir, 'thumb_'.$tmpImage));
		}
	}
	
	public function clearFile()
	{
		$baseFile	= JFactory::getApplication()->input->get('file');
		$image 		= Path::Combine($this->baseDir, $baseFile);
		$thumbnail 	= Path::Combine($this->baseDir, 'thumb_'.$baseFile);
		
		if(JFile::exists($image))
		{
			JFile::delete($image);
		}

		if(JFile::exists($thumbnail))
		{
			JFile::delete($thumbnail);
		}
	}
	
	public function postUploadCheck(&$resultInfo, $file)
	{
		if($this->f2cConfig->get('filename_restriction', 1) == 0)
		{
			// Check the filename, according to Joomla's (Media Manager) standards
			$file = $resultInfo['originalfilename'];
			
			if (str_replace(' ', '', $file) != $file || $file !== JFile::makeSafe($file))
			{
				$resultInfo['error'] = JText::_('COM_FORM2CONTENT_ERROR_WARNFILENAME');		
				return false;
			}
		}
		
		// Check if the image dimensions are within the minimum dimensions
		$minWidth 	= (int)$this->settings->get($this->getPrefix().'_min_width');
		$minHeight 	= (int)$this->settings->get($this->getPrefix().'_min_height');

		if($minWidth > 0 || $minHeight > 0)
		{
			$image = new JImage($file['tmp_name']);
			
			if($minWidth > 0 && $image->getWidth() < $minWidth)
			{
				$resultInfo['error'] = sprintf(JText::_('COM_FORM2CONTENT_ERROR_IMAGE_MIN_WIDTH'), $image->getWidth(), $minWidth);
				return false;
			}
			
			if($minHeight > 0 && $image->getHeight() < $minHeight)
			{
				$resultInfo['error'] = sprintf(JText::_('COM_FORM2CONTENT_ERROR_IMAGE_MIN_HEIGHT'), $image->getHeight(), $minHeight);
				return false;
			}
		}
		
		// Auto-convert the filename to Joomla's (Media Manager) standards
		$resultInfo['originalfilename'] = JFile::makeSafe(str_replace(' ', '_', $resultInfo['originalfilename']));
		return true;		
	}
	
	public function deleteContentType()
	{
		// remove the base image dir
		$baseDir = Path::Combine(self::GetImagesRootPath(), 'p'.$this->projectid);
		
		if(JFolder::exists($baseDir))
		{
			JFolder::delete($baseDir);
		}
	}
	
	public function deleteArticle($formId)
	{
		Path::Remove((Path::Combine(self::GetImagesRootPath(), 'p'.$this->projectid.'/f'.$formId)));
	}
	
	public static function GetImagesUrl($projectId, $formId, $relative = false, $fullRelative = false)
	{
		$imagesPath = F2cFactory::getConfig()->get('images_path');
		
		if($relative)
		{
			if($fullRelative)
			{
				return $imagesPath.'/p'.$projectId.'/f'.$formId.'/';
			}
			else 
			{
				return self::convertToRelativePath($imagesPath)."/p$projectId/f$formId/";
			}
		}
		else
		{
			return F2cUri::GetClientRoot().$imagesPath."/p$projectId/f$formId/";			
		}
	}
	
	public static function GetThumbnailsUrl($projectId, $formId, $relative = false, $fullRelative = false)
	{
		return Path::Combine(self::GetImagesUrl($projectId, $formId, $relative, $fullRelative), 'thumbs');	
	}

	public static function GetImagesRootPath($relative = false, $fullRelative = false)
	{
		$imagesPath = F2cFactory::getConfig()->get('images_path');
		
		if($relative)
		{
			if($fullRelative)
			{
				return $imagesPath.'/p'.$projectId.'/f'.$formId.'/';
			}
			else 
			{
				return self::convertToRelativePath($imagesPath).'/';
			}
		}
		else
		{
			return JPATH_SITE.DIRECTORY_SEPARATOR.$imagesPath.'/';
		}				
	}
	
	public static function GetImagesPath($projectId, $formId, $relative = false)
	{
		return Path::Combine(self::GetImagesRootPath($relative), 'p'.$projectId.'/'.'f'.$formId);
	}

	public static function GetThumbnailsPath($projectId, $formId, $relative = false)
	{
		return Path::Combine(self::GetImagesPath($projectId, $formId, $relative), 'thumbs');	
	}
	
	/* For backward compatibility purposes the path is relative to images/stories when the image path is images/stories/xxxxxxxxxxx.
	 * In all other cases the relative path is relative to the website root
	 */		
	private static function convertToRelativePath($path)
	{
		$search = 'images/stories/';

		if(stripos($path, $search) === 0)
		{
			return substr($path, strlen($search));
		}
		else
		{
			return $path;
		}
	}
}
?>