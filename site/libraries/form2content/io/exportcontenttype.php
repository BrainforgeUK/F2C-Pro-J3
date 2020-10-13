<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once(JPATH_COMPONENT_SITE.'/libraries/SimpleXMLExtended.php');

class F2cIoExportcontenttype
{
	private $contentType;
	private $componentInfo;
	private $f2cConfig;
	
	public function __construct($contentType, $componentInfo, $f2cConfig)
	{
		$this->contentType		= $contentType;
		$this->componentInfo 	= $componentInfo;
		$this->f2cConfig 		= $f2cConfig;
	}
	
	/**
	 * Create the XML for exporting a Content Type
	 *
	 * @param   int  				$id   		Content Type Id
	 *
	 * @return 	SimpleXMLExtended	XML document containing the Content Type definition
	 *
	 * @since   6.17.0
	 */
	public function getXml()
	{
		$introTemplate	= '';
		$mainTemplate	= '';

		$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><contenttype></contenttype>');
		
		$xml->title 	= $this->contentType->title;
		$xml->version 	= $this->componentInfo['version'];
		$xml->published = $this->contentType->published;
		
		$xmlSettings = $xml->addChild('settings');
		$xmlSettings->addArray($this->contentType->settings);
		
		$xmlAttribs = $xml->addChild('attribs');
		$xmlAttribs->addArray($this->contentType->attribs);
		
		$xmlMetadata = $xml->addChild('metadata');
		$xmlMetadata->addArray($this->contentType->metadata);
		
		$xmlFields = $xml->addChild('fields');
		
		if(count($this->contentType->fields))
		{
			foreach($this->contentType->fields as $field)
			{
				$xmlField = $xmlFields->addChild('field');
				
				$xmlField->fieldname 	= $field->fieldname;
				$xmlField->title 		= $field->title;
				$xmlField->description 	= $field->description;
				$xmlField->fieldtype 	= $field->fieldtypename;
				$xmlField->ordering 	= $field->ordering;
				$xmlField->frontvisible = $field->frontvisible;
				
				$xmlFieldSettings = $xmlField->addChild('settings');
				
				if($field->settings)
				{
					$xmlFieldSettings->addRegistry($field->settings);
				}
				
				if($field->fieldname == 'intro_template')
				{
					$introTemplate = $field->settings->get('default');
				}
				
				if($field->fieldname == 'main_template')
				{
					$mainTemplate = $field->settings->get('default');
				}
			}
		}
		
		$xmlIntroTemplateFile = $xml->addChild('introtemplatefile');
		$xmlIntroTemplateFile->addCData($this->getTemplateContents($introTemplate));
		$xmlMainTemplateFile = $xml->addChild('maintemplatefile');
		$xmlMainTemplateFile->addCData($this->getTemplateContents($mainTemplate));
		
		if(array_key_exists('form_template', $this->contentType->settings) && $this->contentType->settings['form_template'])
		{
			$xmlFormTemplateFile = $xml->addChild('formtemplatefile');
			$xmlFormTemplateFile->addCData($this->getTemplateContents($this->contentType->settings['form_template']));
		}
		
		return $xml;
	}
	
	/**
	 * Read the template from disk and return its contents
	 * 
	 * @param   string	$template	Template file name (without path)
	 * 
	 * @return 	string	Template contents
	 * 
	 * @since   6.17.0
	 */
	private function getTemplateContents($template)
	{
      	$contents 		= '';
      	$templateFile 	= Path::Combine($this->f2cConfig->get('template_path'), $template);

      	if(JFile::exists($templateFile))
      	{
      		$contents = file_get_contents($templateFile);
      	}
		
      	return $contents;
	}
}

?>