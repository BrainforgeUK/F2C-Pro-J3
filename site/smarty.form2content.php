<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

if (!class_exists('Smarty')) 
{
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_form2content'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'smarty'.DIRECTORY_SEPARATOR.'Smarty.class.php');
}

class F2C_Smarty
{
	var $templates;
	var $form;
	var $error = '';
	var $smarty = null;
	
	public function __construct()
	{
		$this->smarty 					= new Smarty();
		$this->smarty->template_dir 	= F2cFactory::getConfig()->get('template_path');
		$this->smarty->compile_dir 		= JFactory::getConfig()->get('tmp_path');
	}

	public function parseIntro()
	{
		$parsedContent = $this->smarty->fetch($this->templates[F2C_TEMPLATE_INTRO]);		
		return $parsedContent;
	}

	public function parseMain()
	{
		$parsedContent = '';
		
		if(array_key_exists(F2C_TEMPLATE_MAIN, $this->templates))
		{	
			$parsedContent = $this->smarty->fetch($this->templates[F2C_TEMPLATE_MAIN]);
		}
		
		return $parsedContent;
	}
	
	public function addTemplate($templateResource, $templateType)
	{
		if(!preg_match('/^[^:]*:/', $templateResource))
		{
			// file resource -> check it
			if(!JFile::exists(Path::Combine(F2cFactory::getConfig()->get('template_path'), $templateResource)))
			{
				$this->error = JText::_('COM_FORM2CONTENT_ERROR_TEMPLATE_NOT_FOUND');
				return false;
			}
		}		
		
		$this->templates[$templateType] = $templateResource;
		
		return true;	
	}

	public function clearTemplates()
	{
		// No action necessary
	}
		
	public function addVar($name, $value)
	{
		$this->smarty->assign(strtoupper($name), $value);		
	}
	
	public function clearVar($name)
	{
		$this->smarty->clearAssign(strtoupper($name));
	}
	
	
	public function clearAllVars()
	{
		$this->smarty->clearAllAssign();
	}
		
	public function addFormVar($field)
	{
		// Keep the title for backward compatibility
		$this->addVar($field->fieldname.'_TITLE', HtmlHelper::stringHTMLSafe($field->title));				
		$this->addVar($field->fieldname.'_CAPTION', HtmlHelper::stringHTMLSafe($field->title));				
		$this->addVar($field->fieldname.'_DESCRIPTION', HtmlHelper::stringHTMLSafe($field->description));	

		$field->addTemplateVar($this, $this->form);
	}
	
	public function getTemplateVars($formVars, &$usedVars)
	{		
		foreach($this->templates as $templateName)
		{
			$this->_getTemplateVars($templateName, $formVars, $usedVars);
		}
	}
	
	private function _getTemplateVars($templateName, $formVars, &$usedVars)
	{
		$contents = file_get_contents(Path::Combine(F2cFactory::getConfig()->get('template_path'), $templateName));

		// check which vars are used within the template
		foreach($formVars as $formVarAlias => $formVarName)
		{
			if(preg_match('/{.*?(\$'.$formVarAlias.')(\s+.*)?}/', $contents))
			{
				if(!array_key_exists($formVarName, $usedVars))
				{
					$usedVars[$formVarName] = strtoupper($formVarName);
					continue;
				}
			}

			if(strpos($contents, '{$'.$formVarAlias.'|') !== false)
			{
				if(!array_key_exists($formVarName, $usedVars))
				{
					$usedVars[$formVarName] = strtoupper($formVarName);
					continue;
				}
			}
		}		
	} 
	
	/*
	 * This functionality is needed for F2C KML Feeds
	 */
	public function getPossibleTemplateVars($fields)
	{
		$aliases = array();
		
		foreach($fields as $field)
		{
			$templateParms = $field->getTemplateParameterNames();
			
			foreach($templateParms as $templateParm)
			{
				$aliases[$templateParm] = $field->fieldname;
				
				if($field->classificationId != F2C_FIELD_F2C_NATIVE)
				{
					// prefix with JOOMLA_ for legacy purpose
					$aliases['JOOMLA_'.$templateParm] = $field->fieldname;
				}				
			}
		}
		
		return $aliases;
	}
}
?>