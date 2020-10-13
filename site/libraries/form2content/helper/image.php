<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_form2content
 *
 * @copyright   Copyright (C) 2006 - 2014 Open Source Design. All rights reserved.
 * @author      Open Source Design <info@opensourcedesign.nl>
 */
defined('JPATH_PLATFORM') or die('Restricted acccess');

/**
 * Image helper
 * 
 * This class is used to provide utility functions for Form2Content Image operations.
 * 
 * @package     Joomla.Site
 * @subpackage  com_form2content
 * @since       6.16.0
 */
class F2cHelperImage
{
	/**
	 * Check if the GD library has been installed
	 * 
	 * @return	bool	Flag to indicate that GD library is installed
	 * 
	 * @since	6.16.0
	 */
	public static function checkGdLibrary($generateMessage = true)
	{
	 	if(!(extension_loaded('gd') && function_exists('gd_info')))
	 	{
	 		if($generateMessage)
	 		{
	 			JFactory::getApplication()->enqueueMessage(JText::_('COM_FORM2CONTENT_GDI_NOT_INSTALLED'), 'warning');
	 		}
	 		
	 		return false;
	 	}
	 	
	 	return true;
	}		
}
?>