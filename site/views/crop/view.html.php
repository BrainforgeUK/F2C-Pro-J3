<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');

class Form2ContentViewCrop extends JViewLegacy
{
	protected $field;
	protected $tmpImage;
	protected $imageDir;
	protected $top;
	protected $left;
	protected $aspectWidth;
	protected $aspectHeight;
	protected $cropThumbOnly;
	protected $row;
	protected $previewHeight;
	protected $previewWidth;
	protected $imageWidth;
	protected $imageHeight;
	protected $minSelectionWidth = -1;
	protected $minSelectionHeight = -1;
	
	function display($tpl = null)
	{
		define("MAXCROPSIZE", 600);
		define("MAXPREVIEWSIZE", 250);
				
		$this->imageDir		= Path::Combine(JUri::root(true), F2cFactory::getConfig()->get('images_path'));
		$imageRoot 			= Path::Combine(JPATH_SITE, F2cFactory::getConfig()->get('images_path'));
		$srcImage			= Path::Combine($imageRoot, JFactory::getApplication()->input->getString('image'));
		$this->tmpImage		= JFactory::getApplication()->input->getString('image');
		$contentType		= F2cFactory::getContentType(JFactory::getApplication()->input->getInt('contenttypeid'));
		$this->field		= $contentType->fields[JFactory::getApplication()->input->getInt('fieldid')];
		$this->row			= JFactory::getApplication()->input->getString('row', -1);
		$scaleFactor		= 1; // no scaling
		$prefix 			= $this->field->getPrefix();
		$this->aspectWidth	= $this->field->settings->get($prefix.'_crop_aspect_width');
		$this->aspectHeight	= $this->field->settings->get($prefix.'_crop_aspect_height');
		$this->cropThumbOnly= $this->field->settings->get($prefix.'_crop_thumb_only', 0);
		$jImage 			= new JImage($srcImage);
		$this->imageWidth 	= $jImage->getWidth();
		$this->imageHeight 	= $jImage->getHeight();

		if($this->imageWidth > MAXCROPSIZE || $this->imageHeight > MAXCROPSIZE)
		{
			// rescale image to fit in viewport boundaries
			$scaleFactor 	= MAXCROPSIZE / max(array($this->imageWidth, $this->imageHeight));
			$this->imageWidth 	= round($this->imageWidth * $scaleFactor,0 , PHP_ROUND_HALF_UP);
			$this->imageHeight 	= round($this->imageHeight * $scaleFactor,0 , PHP_ROUND_HALF_UP);
		}
		
		if(empty($this->aspectWidth) || empty($this->aspectHeight))
		{
			$this->previewHeight 	= MAXPREVIEWSIZE;
			$this->previewWidth 	= MAXPREVIEWSIZE;
		}
		else 
		{
			if($this->aspectWidth > $this->aspectHeight)
			{
				$this->previewWidth 	= MAXPREVIEWSIZE;
				$this->previewHeight 	= round(MAXPREVIEWSIZE * $this->aspectHeight/$this->aspectWidth, 0 , PHP_ROUND_HALF_UP);
			}
			else 
			{
				$this->previewHeight 	= MAXPREVIEWSIZE;
				$this->previewWidth 	= round(MAXPREVIEWSIZE * $this->aspectWidth/$this->aspectHeight, 0 , PHP_ROUND_HALF_UP);
			}
		}
		
		// calculate top and height for centering crop image
		$this->top 	= round((MAXCROPSIZE - $this->imageHeight) / 2.0, 0 , PHP_ROUND_HALF_UP);
		$this->left = round((MAXCROPSIZE - $this->imageWidth) / 2.0, 0 , PHP_ROUND_HALF_UP);
		
		if(!$this->cropThumbOnly)
		{
			$this->minSelectionWidth 	= round($scaleFactor * $this->field->settings->get($this->field->getPrefix().'_min_width'), 0 , PHP_ROUND_HALF_UP);
			$this->minSelectionHeight 	= round($scaleFactor * $this->field->settings->get($this->field->getPrefix().'_min_height'), 0 , PHP_ROUND_HALF_UP);
		} 
		
		JFactory::getDocument()->addScriptDeclaration('var rootUrl = \''.JURI::root().'\'');
		
		parent::display($tpl);		
	}
}
?>