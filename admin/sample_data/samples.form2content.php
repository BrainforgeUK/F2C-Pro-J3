<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once(JPATH_COMPONENT.'/models/project.php');
require_once(JPATH_COMPONENT.'/models/form.php');

class F2cSampleDataHelper
{
	/**
	 * Install the samples from the sample_data folder
	 * 
	 * @return	void
	 * 
	 * @since   6.17.0
	 */
	public function install()
	{
		$samplebase 	= JPATH_COMPONENT_ADMINISTRATOR.'/sample_data/';
		$catPath 		= $this->getDefaultCategoryPath();
		$form 			= new Form2ContentModelForm();
		$db				= JFactory::getDbo();
		$f2cConfig 		= F2cFactory::getConfig();
				
		if($contentType = $this->importContentType($samplebase.'Simple article example_f2c_pro_contenttype.xml'))
		{
			$articleImport 	= new F2cIoImportarticle($db, $f2cConfig, $form);
			$xml 			= $articleImport->loadXmlFile($samplebase.'Simple article example_f2c_pro_form.xml');
			
			foreach ($xml[1] as $xmlForm)
			{
				$xmlForm->registerXPathNamespace('f', 'http://schemas.form2content.com/forms');
				// Set the category to a known category for the user's site
				$nodeList = $xmlForm->xpath('.//f:field[f:fieldname="category"]');
				$nodeList[0]->value = $catPath;
			}
			
			$articleImport->importXml($xml[1]);
		}

		if($contentType = $this->importContentType($samplebase.'All fields example_f2c_pro_contenttype.xml'))
		{
			$articleImport 	= new F2cIoImportarticle($db, $f2cConfig, $form);
			$xml 			= $articleImport->loadXmlFile($samplebase.'All fields example_f2c_pro_form.xml');
			
			foreach ($xml[1] as $xmlForm)
			{
				$xmlForm->registerXPathNamespace('f', 'http://schemas.form2content.com/forms');
				// Set the category to a known category for the user's site
				$nodeList = $xmlForm->xpath('.//f:field[f:fieldname="category"]');
				$nodeList[0]->value = $catPath;
				// Set the article choice to a known article for the user's site
				$nodeList = $xmlForm->xpath('.//f:field[f:fieldname="database"]');
				$nodeList[0]->value = $this->getDefaultArticle();				
			}
			
			$articleImport->importXml($xml[1]);
		}
	}			
	
	/**
	 * Get the first available category path on the website
	 * 
	 * @return	string	path to category
	 * 
	 * @since   6.17.0
	 */
	private function getDefaultCategoryPath()
	{
		$db 	= JFactory::getDbo();		
		$query	= $db->getQuery(true);
		
		$query->select('path')->from('#__categories')->where('extension = \'com_content\' AND published = 1');
		
		$db->setQuery($query, 0, 1);
		
		return '/'.$db->loadResult();
	}
		
	/**
	 * Get the first available Joomla article on the website
	 * 
	 * @return	int		Id of the Joomla article
	 * 
	 * @since   6.17.0
	 */
	private function getDefaultArticle()
	{
		$db 	= JFactory::getDbo();		
		$query	= $db->getQuery(true);
		
		$query->select('id')->from('#__content')->where('state = 1');
		
		$db->setQuery($query, 0, 1);
		
		return $db->loadResult();		
	}	
	
	/**
	 * Import a Content Type based on an XML file
	 * 
	 * @param	string	Path to XML file
	 * 
	 * @return	object	Imported Content Type
	 * 
	 * @since   6.17.0
	 */
	private function importContentType($filename)
	{
		$model 	= new Form2ContentModelProject();
		
		if(!$model->import($filename))
		{
			// Check for errors.
			if (count($errors = $model->getErrors())) 
			{
				JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'notice');
				echo implode("\n", $errors);
			}
			
			return false;
		}
		
		return F2cFactory::getContentType($model->getState('project.id'));
	}
}
?>