<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once(JPATH_COMPONENT_SITE.'/libraries/SimpleXMLExtended.php');

class F2cIoExportarticle
{
	/**
	 * Create the XML for exporting an Article
	 *
	 * @param   object		$form   		The article data
	 * @param   object  	$contentType	The Content Type for the Article
	 *
	 * @return 	SimpleXMLExtended	XML document containing the Article definition
	 *
	 * @since   6.17.0
	 */
	function getXml($form, $contentType)
	{
		$xmlForm = new SimpleXMLExtended('<form></form>');
		
		$this->setId($contentType, $form, $xmlForm);

		$xmlForm->contenttype = $contentType->title;
		
		$xmlModified = $xmlForm->addChild('modified');
		$xmlModified->addDate($form->modified);
		$xmlForm->ordering = $form->ordering;
		
		$xmlFieldAttribs = $xmlForm->addChild('attribs');
		
		if($form->attribs)
		{
			$xmlFieldAttribs->addArray($form->attribs);
		}
		
		$xmlFieldMetadata = $xmlForm->addChild('metadata');
		
		if($form->metadata)
		{
			$xmlFieldMetadata->addArray($form->metadata);
		}
		
		$xmlFields = $xmlForm->addChild('fields');
		
		if(count($form->fields))
		{
			foreach($form->fields as $field)
			{
				$field->export($xmlFields, $form);
			}
		}
		
		return $xmlForm;
	}
	
	/**
	 * Determine which field defines the unique Id for the Article:
	 * The form Id or the alternate key, specified as the value of a single line textbox field
	 *
	 * @param   object  	$contentType	The Content Type for the Article
	 * @param   object		$form   		The article data
	 * @param   object		$xmlArticle   	The XML structure for the article data
	 *
	 * @since   6.17.0
	 */
	private function setId($contentType, $form, $xmlArticle)
	{
		if(array_key_exists('field_alt_key', $contentType->settings) && $contentType->settings['field_alt_key'] != '')
		{
			$altKeyFieldId 		= $contentType->settings['field_alt_key'];
			$altKeyFieldName 	= $contentType->fields[$altKeyFieldId]->fieldname;
			$altKeyFieldValue	= $form->fields[$altKeyFieldName]->values['VALUE'];
			
			$xmlArticle->id = $altKeyFieldValue;
			$xmlArticle->id->addAttribute('fieldname', $altKeyFieldName);
		}
		else 
		{
			$xmlArticle->id = $form->id;
		}
	}
}
