<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use \Joomla\Registry\Registry;
use Joomla\String\StringHelper;

jimport('joomla.html.pagination');
jimport('joomla.utilities.date');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class Path
{
	static function Combine($path, $fileName)
	{
		return rtrim($path, "/\\") . '/' . $fileName;
	}
	
	/*
	 * Recursively remove a path, thus deleting all its files and subfolders
	 */
	static function Remove($path)
	{
		$path = JPath::clean($path);
	
		if(!JFolder::exists($path)) return false;
		
		$files = JFolder::files($path, '.', false, true);
	
		if(count($files))
		{		
			JFile::delete($files);
		}
	
		$folders = JFolder::folders($path, '.', false, true);
		
		if(count($folders))
		{
			foreach($folders as $folder);
			{
				Path::Remove($folder);
			}
		}
			
		JFolder::delete($path);	
		return true;
	}
}

class F2C_FileInfo
{
	var $id;
	var $fileName;
	var $fileLocation;
	var $fileSize;
	var $fileExtension;
	
	function __construct($fileLocation, $fileName)
	{
		$this->id = $fileLocation.$fileName;
		$this->fileName = $fileName;
		$this->fileLocation = $fileLocation;
		$this->fileExtension = JFile::getExt($this->id);
		$this->fileSize = F2C_FileInfo::FormatFileSize(filesize($this->id));
	}
	
	static function FormatFileSize($filesize)
	{	
		if($filesize > 1024 * 1024)
		{
			$filesize = round($filesize / (1024 * 1024), 2);
			return $filesize . ' Mb';
		}
	
		if($filesize > 1024)
		{
			$filesize = round($filesize / 1024, 2);
			return $filesize . ' kb';
		}
		
		return $filesize . ' bytes';
	}
}

/*
 * Extend custom image class from JImage, since JImage does not handle file writes through the FTP layer
 */
class JImageF2cExtended extends JImage
{
	/**
	 * Method to write the current image out to a file. Customized to be compatible with the FTP layer
	 *
	 * @param   string   $path     The filesystem path to save the image.
	 * @param   integer  $type     The image type to save the file as.
	 * @param   array    $options  The image type options to use in saving the file.
	 *
	 * @return  boolean
	 *
	 * @see     http://www.php.net/manual/image.constants.php
	 * @since   11.3
	 * @throws  LogicException
	 */
	public function toFile($path, $type = IMAGETYPE_JPEG, array $options = array())
	{
		// Make sure the resource handle is valid.
		if (!$this->isLoaded())
		{
			throw new LogicException('No valid image was loaded.');
		}
		
		ob_start();
		
		switch ($type)
		{
			case IMAGETYPE_GIF:				
				imagegif($this->handle);				
				break;

			case IMAGETYPE_PNG:
				// For PNG, the image quality must be between 0 and 9, 0 meaning no compression
				$imageQuality = (array_key_exists('quality', $options)) ? $options['quality'] : 0;
				
				JLog::add('Raw image quality passed to PNG resize function: ' . $imageQuality, JLog::INFO, 'com_form2content');
				
				$imageQuality = round((100 - $imageQuality) * 0.09);
				
				JLog::add('Converted image quality passed to PNG resize function: ' . $imageQuality, JLog::INFO, 'com_form2content');
				
				imagepng($this->handle, null, $imageQuality);
				break;

			case IMAGETYPE_JPEG:
			default:
				imagejpeg($this->handle, null, (array_key_exists('quality', $options)) ? $options['quality'] : 100);
		}
		
		$output = ob_get_contents();
		ob_end_clean();
		
		return JFile::write($path, $output);
	}
}

class ImageHelper
{
    static function ResizeImage($srcFile, $dstFile = null, &$dstWidth, &$dstHeight, $imageQuality = 75) 
    {
    	if(!$dstFile)
    	{
    		// Resize the source file
			$dstFile = $srcFile;    		
    	}
    	
    	$srcImage 	= new JImageF2cExtended($srcFile); 	
    	$srcProps 	= JImageF2cExtended::getImageFileProperties($srcFile);
    	$srcWidth 	= $srcImage->getWidth();
    	$srcHeight 	= $srcImage->getHeight();
    	
    	if(($srcWidth <= $dstWidth) && ($srcHeight <= $dstHeight))
    	{
    		// No resize necessary: fill the output parameters and keep the original image
    		$dstWidth = $srcWidth;
    		$dstHeight = $srcHeight;
    		
    		if($srcFile == $dstFile)
    		{
    			return true;
    		}
    		
    		return JFile::copy($srcFile, $dstFile);
    	}
    	    	
    	$srcRatio = $srcWidth / $srcHeight;

		if($dstWidth / $dstHeight > $srcRatio)
		{
		   $dstWidth = $dstHeight * $srcRatio;
		}
		else
		{
		   $dstHeight = $dstWidth / $srcRatio;
		}

		$dstHeight = (int)ceil($dstHeight);
		$dstWidth = (int)ceil($dstWidth);
    	
		$dstImage = $srcImage->resize($dstWidth, $dstHeight, false);
		
		return $dstImage->toFile($dstFile, $srcProps->type, array('quality' => $imageQuality));
    }
    
    static function isGdiLibInstalled()
    {
    	if((!function_exists('imagecreatetruecolor')) 	|| (!function_exists('imagecreatefromgif')) 	||
		   (!function_exists('imagecopyresampled'))		|| (!function_exists('imagegif')) 				||
		   (!function_exists('imagecreatefromgif')) 	|| (!function_exists('imagecreatefromjpeg')) 	||
		   (!function_exists('imagecreatefrompng'))		|| (!function_exists('imagecolorstotal'))		||
		   (!function_exists('imagecolortransparent'))	|| (!function_exists('imagefill'))				||
		   (!function_exists('imagetruecolortopalette'))|| (!function_exists('imagepalettecopy')))
		{		
			return false;
		}
		else
		{
			return true;
		}
    }	
}

class F2cContentHelper
{
	static function syncArticleOrder($catid)
	{
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		$query->update('#__content c');
		$query->innerJoin('#__f2c_form f ON f.reference_id = c.id');
		$query->set('c.ordering = f.ordering');
		$query->where('f.catid = ' . (int)$catid);
		
		$db->setQuery($query);
		$db->execute();
		
		$cache = JFactory::getCache('com_content');
		$cache->clean();
	}	
}

class F2cDateTimeHelper
{
	static function ParseDate($date, $format)
	{
		$day = 0;
		$month = 0;
		$year = 0;
		$date = trim($date);

		if(stristr($date, ' ') === FALSE)
		{
			$date .= ' 00:00:00';
		}
		
		list($datePart, $timePart) = explode(' ', $date);
		
		$strippedFormat = StringHelper::str_ireplace('%d', '', $format);
		$strippedFormat = StringHelper::str_ireplace('%m', '', $strippedFormat);
		$strippedFormat = StringHelper::str_ireplace('%Y', '', $strippedFormat);
		$separator 		= StringHelper::substr($strippedFormat, 0, 1);
		$dateFormat 	= explode($separator, $format); 
		$dateParts 		= explode($separator, $datePart);
		$timeParts 		= explode(':', $timePart);
	
		if(count($dateParts) != 3)
		{
			return false;
		}
	
		if(count($timeParts) == 1)
		{
			$timeParts[1] = '00';
			$timeParts[2] = '00';
		}
	
		if(count($timeParts) == 2)
		{
			$timeParts[2] = '00';
		}
	
		$timeParts[0] = (int)$timeParts[0];
		$timeParts[1] = (int)$timeParts[1];
		$timeParts[2] = (int)$timeParts[2];
		
		if(!F2cDateTimeHelper::checktime($timeParts[0], $timeParts[1], $timeParts[2]))
		{
			return false;
		}
				
		for($i = 0; $i < count($dateFormat); $i++)
		{
			switch($dateFormat[$i])
			{
				case '%d':
					$day = (int)$dateParts[$i];
					break;
				case '%m':
					$month = (int)$dateParts[$i];
					break;
				case '%Y':
					$year = (int)$dateParts[$i];
					break;
			}
		}
				
		if(checkdate($month, $day, $year))
		{
			return new JDate($year.'-'.$month.'-'.$day. ' '.$timeParts[0].':'.$timeParts[1].':'.$timeParts[2]);
		}
		else
		{
			return false;
		}	
	}
	
	static function checktime($hours, $minutes, $seconds)
	{
		if($hours < 0 || $hours > 23) return false;
		if($minutes < 0 || $minutes > 59) return false;
		if($seconds < 0 || $seconds > 59) return false;
		return true;
	}
	
	static function getTranslatedDateFormat()
	{
		$dateFormat	= F2cFactory::getConfig()->get('date_format');
		$dateFormat = str_replace('%', '', $dateFormat);
		$dateFormat = str_replace('-', '_', $dateFormat);
		return JText::_('COM_FORM2CONTENT_DATE_FORMAT_'.strtoupper($dateFormat));
	}
}

class F2cMenuHelper
{
	/*
	 * Return the Request menu parameters as a Registry object
	 */
	static function getParameters($itemId)
	{
		$arrQueryString	= array();
		$db 			= JFactory::getDbo();
		$query 			= $db->getQuery(true);
		$queryString 	= new Registry();

		$query->select('link');
		$query->from('#__menu');
		$query->where('id='.(int)$itemId);
		
		$db->setQuery($query->__toString());
		
		parse_str(parse_url($db->loadResult(), PHP_URL_QUERY), $arrQueryString);
		
		$queryString->loadArray($arrQueryString);
		
		return $queryString;
	}
}

class F2cUri
{
	static function GetClientRoot()
	{
		$config = JFactory::getConfig();
		$root	= JURI::root();
		
		switch((int)$config->get('force_ssl'))
		{
			case 0: // none
				if(strpos(strtolower($root), 'https') === 0)
				{
					$root = substr_replace($root, 'http', 0, 5);
				}
				break;
			case 1: // admin only
				$root = substr_replace(JURI::root(), 'http', 0, 5);
				break;
			case 2: // entire site
				break;
		}

		return $root;
	}	
}
?>