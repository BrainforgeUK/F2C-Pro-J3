<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.controllerform');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_form2content'.DIRECTORY_SEPARATOR.'utils.form2content.php');

class Form2ContentControllerFormBase extends JControllerForm
{
	private $f2cConfig;
	private $field;
	private $jInput;
	
	function __construct($config)
	{
		parent::__construct($config);
		
		$this->f2cConfig		= F2cFactory::getConfig();
		$this->jInput			= JFactory::getApplication()->input;
		$contentType			= F2cFactory::getContentType((int)$this->jInput->getInt('contenttypeid'));
		// TODO: rewrite for content type field
		$this->field			= $contentType->fields[(int)$this->jInput->getInt('fieldid')];
	}
	
	/*
	 * Upload entry point for Gallery field
	 */
	public function imageUpload()
	{
		JLog::add('Gallery image upload started', JLog::INFO, 'com_form2content');
		
		echo $this->handleImageUpload(JFactory::getApplication()->input->files->get(0));
	}
	
	/*
	 * Upload entry point for Image and File Upload field
	 */
	public function fileUpload()
	{	
		JLog::add('File upload started', JLog::INFO, 'com_form2content');
		
		echo $this->handleFileUpload(JFactory::getApplication()->input->files->get(0, null, 'raw'));
	}
	
	/*
	 * Delete the temporary uploaded files
	 */
	public function fileClear()
	{
		$this->field->clearFile();
		return;
	}
	
	/*
	 * Handle the images that are selected with the server browse control
	 */
	public function imageTransfer()
	{
		$image 							= Path::Combine(JPATH_SITE, JFactory::getApplication()->input->getString('image'));
		$filename						= uniqid('f2c', true) . '.' . JFile::getExt($image);
		$tmpImage						= Path::Combine($this->field->baseDir, $filename);	
		$resultInfo						= array();	
		$resultInfo['error'] 			= '';
		$resultInfo['originalfilename']	= basename($image);
		
		if($this->checkFilename($resultInfo))
		{
			JFile::copy($image, $tmpImage);
			
			// create thumbnail image
			if($thumbnail = $this->createThumbnail($tmpImage))
			{
				$resultInfo['filename']			= $filename;
				$resultInfo['thumbnail'] 		= $thumbnail;
				$resultInfo['cropping']			= $this->field->settings->get('img_cropping', F2C_FIELD_IMAGE_CROP_NOT_ALLOWED);
				$resultInfo['preview'] 			= '<img src="'.$resultInfo['thumbnail'].'" id="t'.$this->field->id.'_preview">';
			}
			else 
			{
				$resultInfo['error'] = JText::_('COM_FORM2CONTENT_ERROR_IMAGE_RESIZE_FAILED');
			}				
		}
				
		echo json_encode($resultInfo);
		
		$this->purgeOrphanedFiles();
		return;
	}
	
	/*
	 * Create a thumbnail image
	 */
	private function createThumbnail($image)
	{		
		$defaultThumbWidth 		= $this->f2cConfig->get('default_thumbnail_width', F2C_DEFAULT_THUMBNAIL_WIDTH);
		$defaultThumbHeight 	= $this->f2cConfig->get('default_thumbnail_height', F2C_DEFAULT_THUMBNAIL_HEIGHT);
		$defaultThumbQuality	= $this->f2cConfig->get('jpeg_quality', 75);
		$thumbWidth 			= $this->field->settings->get($this->field->getPrefix().'_thumb_width', $defaultThumbWidth);
		$thumbHeight 			= $this->field->settings->get($this->field->getPrefix().'_thumb_height', $defaultThumbHeight);	
		$thumbQuality			= $this->field->settings->get($this->field->getPrefix().'_thumb_quality', $defaultThumbQuality);		
		$thumbFile				= Path::Combine(dirname($image), 'thumb_'.basename($image));
		$imgBaseUrl				= JUri::root(true).'/'.$this->f2cConfig->get('images_path');

		if(ImageHelper::ResizeImage($image, $thumbFile, $thumbWidth, $thumbHeight, $thumbQuality))
		{
			return $imgBaseUrl . '/thumb_' . basename($image);
		}
		
		return '';
	}

	/*
	 * Render the file upload control that works with an iFrame
	 */
	public function renderIframeUpload($postbackData = '')
	{
		if(is_a($this->field, 'F2cFieldFileUpload'))
		{
			$extensions = (array)$this->field->settings->get('ful_whitelist');
		}
		else
		{
			// Image (gallery) field. See which file types we may upload
			switch($this->field->settings->get('allow_filetype', 0))
			{
				case 0: // jpg and png
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
		}
				
		$accept = '';
		$jsExtensions = $this->createJsExtensionsArray($extensions);
		
		if(count($extensions))
		{
			foreach ($extensions as &$extension)
			{
				$extension = '.'.$extension;
			}
			
			$accept = 'accept="'.implode(',', $extensions).'"';
		}
		
		$controlId = 't'.$this->field->id.'_fileupload';
		?>
		<html>
			<head>
				<style>
					body
					{
						margin: 0px;
						padding: 0px;
					}
				</style>
			</head>
			<script type="text/javascript">
			function submitUpload()
			{
				var extensions = <?php echo $jsExtensions; ?>;

				if(extensions != null && extensions.length > 0)
				{
					var elm = document.getElementById('<?php echo $controlId; ?>');
					var extensionFound = false;
					var ext = elm.value.substr(elm.value.lastIndexOf('.') + 1).toLowerCase();
					
					for(var i = 0; i < extensions.length; i++) {
						if(extensions[i] === ext) {
							extensionFound = true;
							break;
						}
					}
					
					if(!extensionFound)
					{
						var extensionList = '';
						for(var i = 0; i < extensions.length; i++) {
							extensionList += '.' + extensions[i];
							if(i < extensions.length -1) {
								extensionList += ', ';
							}
						}

						var jExtensionUploadNotAllowed = '<?php echo JText::_('COM_FORM2CONTENT_EXTENSION_UPLOAD_NOT_ALLOWED', true); ?>';
						alert(jExtensionUploadNotAllowed.replace('%1s', '.' + ext).replace('%2s', extensionList));
						return false;
					}
				}
				
				window.parent.blockUiUpload();
				var frm = document.getElementById('adminForm');
				frm.submit();
			}

			<?php 
			if($postbackData)
			{
				echo 'var pdData = '.json_encode($postbackData).';';
				echo 'window.parent.iFrameUpload('.$this->field->id.','.$this->field->fieldtypeid.','.$this->field->projectid.', pdData);';
			}
			?>
			</script>
			<body>
				<form method="post" action="<?php echo Path::Combine(JUri::root(true), ''); ?>index.php?option=com_form2content&view=iframeupload&task=form.iframeUpload&format=raw" name="adminForm" id="adminForm" enctype="multipart/form-data">
					<input type="file" id="<?php echo $controlId; ?>" name="<?php echo $controlId; ?>" onchange="submitUpload();" <?php echo $accept; ?> />
					<input type="hidden" id="contenttypeid" name="contenttypeid" value="<?php echo $this->field->projectid; ?>" />
					<input type="hidden" id="fieldid" name="fieldid" value="<?php echo $this->field->id; ?>" />
				</form>
				<script type="text/javascript">
					var elm = document.getElementById('<?php echo $controlId; ?>');
					var iframe = window.parent.document.getElementById('t<?php echo $this->field->id; ?>_iframe');

					if(elm)
					{
						iframe.style.height = elm.offsetHeight + "px";
						iframe.style.width = elm.offsetWidth + "px";
					}
				</script>
			</body>
		</html>
		<?php
	}
	
	/*
	 * File upload through iFrame mechanism
	 */
	public function iFrameUpload()
	{		
		JLog::add('iFrame upload started', JLog::INFO, 'com_form2content');
		
		$result = $this->handleFileUpload($this->jInput->files->get('t'.$this->field->id.'_fileupload'));		
		// Redraw the upload screen with the postback data
		$this->renderIframeUpload($result);
	}
	
	public function imageCrop()
	{		
		$x				= $this->jInput->get('x');
		$y				= $this->jInput->get('y');
		$w				= $this->jInput->get('w');
		$h				= $this->jInput->get('h');
		$cropThumbOnly	= $this->jInput->get('cropthumbonly', 0);	
		$filename		= $this->jInput->get('filename');
		$src			= Path::Combine($this->field->baseDir, $filename);
		$srcThumb		= Path::Combine($this->field->baseDir, 'thumb_'.$filename);
		$srcImage		= new JImageF2cExtended($src);
    	$srcProps 		= JImageF2cExtended::getImageFileProperties($src);
    	$srcWidth 		= $srcImage->getWidth();
    	$srcHeight 		= $srcImage->getHeight();
		$filename		= uniqid('f2c', true) . '.' . JFile::getExt($src);
		$croppedImage	= Path::Combine($this->field->baseDir, $filename);		
		$resultInfo		= array();		
		$imageQuality 	= $this->field->settings->get($this->field->getPrefix().'_image_quality', $this->f2cConfig->get('jpeg_quality', 75));
				
    	if($srcWidth > 600 || $srcHeight > 600)
    	{
    		// crop sizes are scaled sizes, calculate the absolute size
    		$scaleFactor = max(array($srcWidth, $srcHeight)) / 600;
    		$x *= $scaleFactor;
   			$y *= $scaleFactor;
   			$w *= $scaleFactor;
   			$h *= $scaleFactor;		
    	}
    	
    	$tmpImage = $srcImage->crop($w, $h, $x, $y, false);
    	
    	if($cropThumbOnly == '1')
    	{
    		// Store the cropped image
    		$tmpImage->toFile($croppedImage, $srcProps->type);
    		// Create a new thumbnail
    		$thumbnail = $this->createThumbnail($croppedImage);
    		// Keep the original image
    		JFile::move($src, $croppedImage);
    		
    	}
    	else
    	{
    		// Store the cropped image
    		$tmpImage->toFile($croppedImage, $srcProps->type, array('quality' => $imageQuality));
	    	// Create a new thumbnail
	    	$thumbnail = $this->createThumbnail($croppedImage);
    	}
    	
    	// remove the old files
    	JFile::delete($src);
    	JFile::delete($srcThumb);

    	$resultInfo['error'] 		= '';
		$resultInfo['filename']		= $filename;
		$resultInfo['thumbnail'] 	= $thumbnail;
    	
    	echo json_encode($resultInfo);
	}
	
	private function handleImageUpload($image)
	{
		$filename						= uniqid('f2c', true) . '.' . JFile::getExt($image['name']);
		$tmpImage						= Path::Combine($this->field->baseDir, $filename);		
		$resultInfo 					= array();			
		$resultInfo['error'] 			= '';
		$resultInfo['originalfilename']	= basename($image['name']);
		
		JLog::add('$filename = ' . $tmpImage, JLog::INFO, 'com_form2content');
		JLog::add('originalfilename = ' . $resultInfo['originalfilename'], JLog::INFO, 'com_form2content');
		JLog::add('error = ' . $image['error'], JLog::INFO, 'com_form2content');
		
		JLog::add(print_r($image, true), JLog::INFO, 'com_form2content');
		
		switch($image['error'])
		{
			case UPLOAD_ERR_OK:
				
				// Check if the filesize within the F2C limits
				$maxImageUploadSize = (int)$this->f2cConfig->get('max_image_upload_size');
				
				if($maxImageUploadSize != 0 && (int)($image['size']/1024) > $maxImageUploadSize)
				{
					$resultInfo['error'] = JText::_('COM_FORM2CONTENT_ERROR_IMAGE_UPLOAD_MAX_SIZE_F2C_CONFIG');
					break;
				}			
				
				try 
				{	
					if(!$this->field->postUploadCheck($resultInfo, $image))
					{
						break;
					}
				}
				catch(Exception $e)
				{
					$resultInfo['error'] = $e->getMessage();
					break;
				}
				
				if(!$this->checkFilename($resultInfo))
				{
					break;
				}
				
				// Move the image to the temp location
				JFile::upload($image['tmp_name'], $tmpImage);
				
				try 
				{
					// create thumbnail image
					if($thumbnail = $this->createThumbnail($tmpImage))
					{
						$resultInfo['filename']			= $filename;
						$resultInfo['thumbnail'] 		= $thumbnail;
						$resultInfo['cropping']			= $this->field->settings->get('img_cropping', F2C_FIELD_IMAGE_CROP_NOT_ALLOWED);
					}
					else 
					{
						$resultInfo['error'] = JText::_('COM_FORM2CONTENT_ERROR_IMAGE_RESIZE_FAILED');
					}				
				} 
				catch(Exception $e) 
				{
					$resultInfo['error'] = $e->getMessage();
				}
				break;
				
			case UPLOAD_ERR_INI_SIZE:
				$resultInfo['error'] = JText::_('COM_FORM2CONTENT_ERROR_IMAGE_UPLOAD_MAX_SIZE', true);
				break;
				
			default:
				$resultInfo['error'] = JText::_('COM_FORM2CONTENT_ERRORS_OCCURRED');
				break;
		}
				
		$this->purgeOrphanedFiles();
		
		return json_encode($resultInfo);
	} 

	private function handleFileUpload($file)
	{
		$filename						= uniqid('f2c', true) . '.' . JFile::getExt($file['name']);
		$tmpFile						= Path::Combine($this->field->baseDir, $filename);		
		$resultInfo 					= array();			
		$resultInfo['error'] 			= '';
		$resultInfo['originalfilename']	= basename($file['name']);
				
		JLog::add('$filename = ' . $tmpFile, JLog::INFO, 'com_form2content');
		JLog::add('originalfilename = ' . $resultInfo['originalfilename'], JLog::INFO, 'com_form2content');
		JLog::add('error = ' . $file['error'], JLog::INFO, 'com_form2content');
		
		JLog::add(print_r($file, true), JLog::INFO, 'com_form2content');
		
		switch($file['error'])
		{
			case UPLOAD_ERR_OK:
				
				
				// Check if the filesize within the F2C limits
				$maxImageUploadSize = (int)$this->f2cConfig->get('max_image_upload_size');
								
				if($maxImageUploadSize != 0 && (int)($file['size']/1024) > $maxImageUploadSize)
				{
					$resultInfo['error'] = JText::_('COM_FORM2CONTENT_ERROR_UPLOAD_MAX_SIZE_F2C_CONFIG');
					break;
				}			

				try 
				{	
					if(!$this->field->postUploadCheck($resultInfo, $file))
					{
						break;
					}
				}
				catch(Exception $e)
				{
					$resultInfo['error'] = $e->getMessage();
					return json_encode($resultInfo);
				}
				
				// Move the image to the temp location
				JLog::add('Move the image to the temp location', JLog::INFO, 'com_form2content');
				
				JFile::upload($file['tmp_name'], $tmpFile, false, true);
								
				$resultInfo['filename']	= $filename;
					
				if($this->field->createThumbnail)
				{
					// Fix the rotation (issue caused mainly by mobile uploads)
					$this->fixRotation($tmpFile);
					
					try 
					{
						// create thumbnail image
						if($thumbnail = $this->createThumbnail($tmpFile))
						{
							$resultInfo['thumbnail'] 		= $thumbnail;
							$resultInfo['cropping']			= $this->field->settings->get($this->field->getPrefix().'_cropping', F2C_FIELD_IMAGE_CROP_NOT_ALLOWED);
						}
						else 
						{
							$resultInfo['error'] = JText::_('COM_FORM2CONTENT_ERROR_IMAGE_RESIZE_FAILED');
						}				
					} 
					catch(Exception $e) 
					{
						$resultInfo['error'] = $e->getMessage();
					}
					
					// Create the preview image	
					$resultInfo['preview'] = '<img src="'.$resultInfo['thumbnail'].'" id="t'.$this->field->id.'_preview">';
				}
				else 
				{						
					// File upload => Create preview link
					$fileUrl = Path::Combine(Path::Combine('/', $this->f2cConfig->get('files_path')), $filename);
					$resultInfo['preview'] = '<a id="t'.$this->field->id.'_preview" href="'.$fileUrl.'" target="_blank">'.$resultInfo['originalfilename'].'</a>';					
				}
				break;
				
			case UPLOAD_ERR_INI_SIZE:
				$resultInfo['error'] = JText::_('COM_FORM2CONTENT_ERROR_UPLOAD_MAX_SIZE', true);
				break;
				
			default:
				$resultInfo['error'] = JText::_('COM_FORM2CONTENT_ERRORS_OCCURRED');
				break;
		}
				
		$this->purgeOrphanedFiles();
				
		return json_encode($resultInfo);
	} 
	
	/**
	 * Delete files older than one day.
	 * Files can become orphaned when an upload gets cancelled by closing the window for example.
	 */
	private function purgeOrphanedFiles()
	{	
		$session = JFactory::getSession();

		if($session->get('purgeOrphanedFiles'.$this->field->fieldtypeid) == 1)
		{
			// we have already cleaned up files during this session
			return;
		}
		
		$this->cleanFiles($this->field->baseDir);
		
		// Mark operation as run
		$session->set('purgeOrphanedFiles'.$this->field->fieldtypeid, 1);
	}
	
	private function cleanFiles($directory)
	{
		$files = JFolder::files($directory, '', false, true);
		
		if(count($files))
		{
			foreach($files as $file)
			{
				if(filemtime($file) <= (time() - 24*60*60))
				{
					JFile::delete($file);
				}
			}
		}
	}
	
	private function checkFilename(&$resultInfo)
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
		
		// Auto-convert the filename to Joomla's (Media Manager) standards
		$resultInfo['originalfilename'] = JFile::makeSafe(str_replace(' ', '_', $resultInfo['originalfilename']));
		return true;		
	}
	
	private function fixRotation($srcFile)
	{
		$srcProps 	= JImageF2cExtended::getImageFileProperties($srcFile);
		
		if($srcProps->type != IMG_JPG)
		{
			// no action required
			return false;
		}

		// Check if the exif_read_data is present
		if(!function_exists('exif_read_data'))
		{
			// we can't perform the rotation fix....
			JLog::add('Form2Content: function exif_read_data not present. Can\'t execute fixRotation function.', JLog::WARNING, 'error');			
			return false;
		}
		
		// Suppress warning while reading exif data (Incorrect APP1 Exif Identifier Code)
		$exif = @exif_read_data($srcFile);	
		
		$rotated 	= false;
		
		if (isset($exif['Orientation']))
		{
			$image 		= new JImageF2cExtended($srcFile);
			
	  		switch ($exif['Orientation'])
	  		{
	    		case 3:
	      			// Need to rotate 180 deg
	      			$image = $image->rotate(180);
	      			$rotated = true;
	      			break;
			    case 6:
			      // Need to rotate 90 deg clockwise
			      $image = $image->rotate(-90);
			      $rotated = true;
			      break;
			    case 8:
			      // Need to rotate 90 deg counter clockwise
			      $image = $image->rotate(90);
			      $rotated = true;
			      break;
	  		}
	  		
	  		if($rotated)
	  		{
	  			// Store the image (overwrite original file)
	  			$image->toFile($srcFile, $srcProps->type, array('quality' => 100));
	  		}
		}
	}
	
	private function createJsExtensionsArray($extensions)
	{
		foreach($extensions as &$extension)
		{
			$extension = '\''.strtolower($extension).'\'';
		}
		
		return '['.implode(',', $extensions).']';
	}

	// Added Brainforge.uk 2025/05/07

	protected $errors = [];

	/*
	 */
	public function setError($msg)
	{
		if (is_callable('parent::func')) {
			parent::setError($msg);
			return;
		}

		$this->errors[] = $msg;
	}

	/*
	 */
	public function getError($i = null, $toString = true)
	{
		if (is_callable('parent::getError')) {
			return parent::getError($i, $toString);
		}

		return empty($this->errors) ? '' : end($this->errors);
	}
}
