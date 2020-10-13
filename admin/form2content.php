<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'const.form2content.php';
require_once JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'factory.form2content.php';

jimport('joomla.application.component.controller');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_form2content')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

// Check if we need to enable the logging
$f2cConfig = F2cFactory::getConfig();

if($f2cConfig->get('enable_logging', 0))
{
	jimport('joomla.log.log');
	JLog::addLogger(array('text_file' => 'com_form2content.logging.php'), JLog::ALL, array('com_form2content'));		
}

JLoader::register('Form2ContentHelperAdmin', __DIR__ . '/helpers/form2content.php');
JLoader::registerPrefix('F2c', JPATH_COMPONENT_SITE.'/libraries/form2content');
JLoader::registerNamespace('form2content', JPATH_COMPONENT_SITE.'/libraries');

// Load the com_content back-end language file
JFactory::getLanguage()->load('com_content', JPATH_ADMINISTRATOR);

// Check the presence of the GD library
F2cHelperImage::checkGdLibrary();

$controller = JControllerLegacy::getInstance('Form2Content');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();
?>