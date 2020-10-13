<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_form2content'.DIRECTORY_SEPARATOR.'utils.form2content.php');
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_content'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'route.php');

defined('F2C_ENGINE_UNDEFINED')		or define('F2C_ENGINE_UNDEFINED', -1);
defined('F2C_ENGINE_PATTEMPLATE')	or define('F2C_ENGINE_PATTEMPLATE', 0);
defined('F2C_ENGINE_SMARTY')		or define('F2C_ENGINE_SMARTY', 1);

defined('F2C_TEMPLATE_INTRO')		or define('F2C_TEMPLATE_INTRO', 0);
defined('F2C_TEMPLATE_MAIN')		or define('F2C_TEMPLATE_MAIN', 1);

class F2cParser extends JObject
{
	var $_engineType = F2C_ENGINE_UNDEFINED;
	var $_parser = null;
	
	private function _detectEngine($templateFile)
	{
		$sample = file_get_contents($templateFile, false, null, 0, 9);
		
		if(strtolower($sample) == '<mos:tmpl' ||
		   strtolower(substr($sample, 3)) == '<mos:t')
		{
			return F2C_ENGINE_PATTEMPLATE;
		}
		else
		{
			return F2C_ENGINE_SMARTY;
		}		
	}
	
	public function addTemplate($templateResource, $templateType)
	{
		if(!preg_match('/^[^:]*:/', $templateResource))
		{
			$filename = Path::Combine(F2cFactory::getConfig()->get('template_path'), $templateResource);

			if(!JFile::exists($filename))
			{
				$this->setError(JText::_('COM_FORM2CONTENT_ERROR_TEMPLATE_NOT_FOUND'));
				return false;
			}
	
			$engineType = $this->_detectEngine($filename);
		}
		else 
		{
			// non-file template resources are always Smarty templates
			$engineType = F2C_ENGINE_SMARTY;
		}
		
		if($templateType == F2C_TEMPLATE_INTRO)
		{
			$this->_engineType = $engineType;
		}
		else
		{
			if($this->_engineType != $engineType)
			{
				$this->SetError('COM_FORM2CONTENT_ERROR_TEMPLATES_DIFFERENT_ENGINES');
				return false;
			}
		}		 

		if(!$this->_parser)
		{
			switch($this->_engineType)
			{
				case F2C_ENGINE_SMARTY:
					if (!class_exists('F2C_Smarty')) 
					{
						require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_form2content'.DIRECTORY_SEPARATOR.'smarty.form2content.php');
					}					
					$this->_parser = new F2C_Smarty();
					break;
				default:
					$this->SetError('COM_FORM2CONTENT_ERROR_UNSUPPORTED_TEMPLATE_ENGINE');
					return false;
					break;
			}
		}

		if(!$this->_parser->addTemplate($templateResource, $templateType))
		{
			$this->setError($this->_parser->error);
			return false;
		}
		
		return true;	
	}
	
	public function clearTemplates()
	{
		$this->_parser->clearTemplates();
	}
	
	public function addVar($name, $value)
	{
		$this->_parser->addVar($name, $value);
	}

	public function clearVar($name)
	{
		$this->_parser->clearVar($name);
	}
	
	public function addVars($form)
	{
		// Clear all previous vars
		$this->_parser->clearAllVars();
		
		$this->_parser->form = $form;
		
		$app				= JFactory::getApplication();
		$db 				= JFactory::getDBO();
		$dateFormat			= str_replace('%', '', F2cFactory::getConfig()->get('date_format') . ' H:i:s');
		$nullDate 			= $db->getNullDate();
		$joomlaId 			= $form->reference_id;
		$modifiedUnix		= ''; // Unix timestamp
		$modifiedDate 		=  ($form->modified != '' && $form->modified != $nullDate) ? JHTML::_('date', $form->modified, $dateFormat) : '';
		
		if($form->modified != $nullDate)
		{
			$tmp 			= new JDate($form->modified);
			$modifiedUnix 	= $tmp->toUnix(); 
		}
		
		$slug 		= ($form->alias) ? $joomlaId.':'.$form->alias : $joomlaId;
		$catslug 	= ($form->catAlias) ? $form->catid.':'.$form->catAlias : $form->catid;
		$link 		= 'index.php?option=com_content&view=article&id='. $slug . '&catid=' . $catslug;
		
		$this->_parser->addVar('JOOMLA_ID', $joomlaId);
		$this->_parser->addVar('JOOMLA_ARTICLE_LINK', $link);
		$this->_parser->addVar('JOOMLA_ARTICLE_LINK_SEF', '{plgContentF2cSef}'.$slug.','.$catslug.'{/plgContentF2cSef}');
		$this->_parser->addVar('JOOMLA_MODIFIED', $modifiedDate);
		$this->_parser->addVar('JOOMLA_MODIFIED_RAW', $modifiedUnix);	
		$this->_parser->addVar('JOOMLA_MODIFIED_BY', $form->modified_by);			

		// Add F2C parameters to template
		$this->_parser->addVar('F2C_IMAGES_PATH_THUMBS_RELATIVE', F2cFieldImage::GetThumbnailsPath($form->projectid, $form->id, true)); 
		$this->_parser->addVar('F2C_IMAGES_PATH_THUMBS_ABSOLUTE', F2cFieldImage::GetThumbnailsPath($form->projectid, $form->id, false)); 
		$this->_parser->addVar('F2C_IMAGES_PATH_RELATIVE', F2cFieldImage::GetImagesPath($form->projectid, $form->id, true)); 
		$this->_parser->addVar('F2C_IMAGES_PATH_ABSOLUTE', F2cFieldImage::GetImagesPath($form->projectid, $form->id, false)); 
		
		foreach($form->fields as $field)
		{
			$this->_parser->addFormVar($field);
		}
		
		return true;
	}
	
	public function parseIntro()
	{
		$content = $this->_parser->parseIntro();
		$this->setError($this->_parser->error);
		return $content;
	}

	public function parseMain()
	{
		$content = $this->_parser->parseMain();
		$this->setError($this->_parser->error);
		return $content;
	}
	
	public function getTemplateVars($formVars, &$usedVars)
	{
		$this->_parser->getTemplateVars($formVars, $usedVars);
	}
	
	public function getPossibleTemplateVars($contentTypeFields)
	{
		return $this->_parser->getPossibleTemplateVars($contentTypeFields);
	}
}
?>