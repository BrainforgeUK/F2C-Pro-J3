<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once(JPATH_COMPONENT_SITE.'/libraries/SimpleXMLExtended.php');

class F2cIoImportcontenttype
{
	private $componentInfo;
	private $db;
	private $f2cConfig;
	private $model;
	
	public function __construct($componentInfo, $db, $f2cConfig, $model)
	{
		$this->componentInfo 	= $componentInfo;
		$this->db 				= $db;
		$this->f2cConfig		= $f2cConfig;
		$this->model			= $model;
	}
	
	/**
	 * Import the Content Type via an XML File
	 *
	 * @param   object  	$xml	XML structure containing Content Type
	 *
	 * @return 	void
	 *
	 * @since   6.17.0
	 */
	public function import($xml)
	{
		if($xml->getName() != 'contenttype')
		{
			throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_IMPORT_CONTENTTYPE_NO_CONTENTTYPE'));
		}
		
		$contentTypeTitle 	= $xml->title;
		$version 			= $xml->version;
		$nodeSettings		= $xml->settings;
		$formTemplate 		= $nodeSettings->form_template;
		
		$this->checkVersion($xml->version);
		$this->checkContentTypeNotExists($xml->title);
		
		// Only perform check when there is a form template
		if($nodeSettings->form_template)
		{
			$this->checkTemplateNotExists($nodeSettings->form_template);
		}
		
		// Get the default template names
		$nodes = $xml->xpath('//fields/field[fieldname = "intro_template"]');
		$introTemplate = $nodes[0]->settings->default;
		$nodes = $xml->xpath('//fields/field[fieldname = "main_template"]');
		$mainTemplate = $nodes[0]->settings->default;
		
		$this->checkTemplateNotExists($introTemplate);
		$this->checkTemplateNotExists($mainTemplate);
		
		$data				= array();
		$data['import']		= true;
		$data['title'] 		= (string)$xml->title;
		$data['id'] 		= null; // force insert
		$data['asset_id'] 	= null; // force insert
		$data['published']	= (string)$xml->published;
		$data['settings'] 	= $xml->settings->toArray();
		$data['attribs'] 	= $xml->attribs->toArray();
		$data['metadata'] 	= $xml->metadata->toArray();

		if(!$this->model->save($data))
		{
			throw new Exception($this->model->getError());
		}
		
		$contentTypeId =  $this->model->getState('project.id');
		
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
				// TODO refactor
				$fldData['fieldtypeid'] 	= F2cHelperFieldtype::getIdByName((string)$field->fieldtype);
				$fldData['settings']		= $field->settings->toArray();
				
				$fld->save($fldData, false);
			}
		}
		
		// Write the template files
		$this->writeTemplateFile($introTemplate, $xml->introtemplatefile);
		$this->writeTemplateFile($mainTemplate, $xml->maintemplatefile);
		
		if($formTemplate != '')
		{
			$this->writeTemplateFile($formTemplate, $xml->formtemplatefile);
		}		
	}
	
	/**
	 * Check if the version of the import is compatible with the current version
	 * Throws Exception when version is not compatible
	 *
	 * @param   string  	$version	version number
	 *
	 * @return 	void
	 *
	 * @since   6.17.0
	 */
	private function checkVersion($version)
	{
		$componentVersion = $this->componentInfo['version'];
		
		if($version == '')
		{
			throw new Exception(JText::sprintf(JText::_('COM_FORM2CONTENT_ERROR_IMPORT_CONTENTTYPE_INCOMPATIBLE_VERSION'), $componentVersion, $version));
		}
		
		list($importMajor, $importMinor, $importRevision) 	= explode('.', $version);
		list($compMajor, $compMinor, $compRevision) 		= explode('.', $componentVersion);
		
		// Major versions must be the same
		if((int)$compMajor != (int)$importMajor)
		{
			throw new Exception(JText::sprintf(JText::_('COM_FORM2CONTENT_ERROR_IMPORT_CONTENTTYPE_INCOMPATIBLE_VERSION'), $componentVersion, $version));
		}
		
		if((int)$compMinor > (int)$importMinor)
		{
			// version check OK
			return;
		}
		
		if(((int)$compMinor == (int)$importMinor) && ((int)$compRevision >= (int)$importRevision))
		{
			// version check OK
			return;
		}
		
		throw new Exception(JText::sprintf(JText::_('COM_FORM2CONTENT_ERROR_IMPORT_CONTENTTYPE_VERSION_TOO_LOW'), $componentVersion, $version));
	}
	
	/**
	 * Check if the Content Type does not exist yet
	 * Throws Exception when Content Type exists
	 *
	 * @param   string  	$title	Title of Content Type
	 *
	 * @return 	void
	 *
	 * @since   6.17.0
	 */
	private function checkContentTypeNotExists($title)
	{
		$query = $this->db->getQuery(true);
		
		$query->select('count(*)')
				->from('#__f2c_project')
				->where('title='.$this->db->quote($title));
		 
		$this->db->setQuery($query);
		 
		if($this->db->loadResult())
		{
			throw new Exception(JText::sprintf(JText::_('COM_FORM2CONTENT_ERROR_IMPORT_CONTENTTYPE_EXISTS'), $contentTypeTitle));
		} 
	}
	
	/**
	 * Check if the template does not exist yet
	 * Throws Exception when template exists
	 *
	 * @param   string  	$template	Title of template without path
	 *
	 * @return 	void
	 *
	 * @since   6.17.0
	 */
	private function checkTemplateNotExists($template)
	{
		$templateFile = Path::Combine($this->f2cConfig->get('template_path'), $template);
		
		if(JFile::exists($templateFile))
		{
			throw new Exception(JText::sprintf(JText::_('COM_FORM2CONTENT_ERROR_IMPORT_CONTENTTYPE_TEMPLATE_EXISTS'), $template));
		}
	}
	
	/**
	 * Write a template file to disk
	 *
	 * @param   string  	$name		Name of template without path
	 * @param	string		$contents	Template contents
	 *
	 * @return 	void
	 *
	 * @since   6.17.0
	 */
	private function writeTemplateFile($name, $contents)
	{
		$templateFile = Path::Combine($this->f2cConfig->get('template_path'), $name);
		JFile::write($templateFile, $contents);
	}
}
?>