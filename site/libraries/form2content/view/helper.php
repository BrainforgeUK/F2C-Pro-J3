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
 * View Helper
 * 
 * This class is used to provider helper functions for rendering views.
 * 
 * @package     Joomla.Site
 * @subpackage  com_form2content
 * @since       6.10.0
 */
class F2cViewHelper
{
	/**
	 * Method to display the credits footer in the back-end.
	 * 
	 * @return  string
	 * 
	 * @since   6.10.0
	 */
    public static function displayCredits()
	{
		if($data = JInstaller::parseXMLInstallFile(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'manifest.xml')) 
		{
			$version = $data['version'];
		}
		else
		{
			$version = 'undefined';
		}
        // Modified Brainforge.uk 20250511
		?>
        <hr/>
        <div align="center">
            <span class="smallgrey"><?php echo JText::_('COM_FORM2CONTENT_FORM2CONTENT') . ' ' . JText::_('COM_FORM2CONTENT_VERSION') . ' ' . $version; ?> (<a href="http://www.form2content.com/changelog/pro-joomla3" target="_blank"><?php echo JText::_('COM_FORM2CONTENT_CHECK_VERSION'); ?></a>), &copy; 2008 - <?php echo Date("Y"); ?> - Copyright by <a href="http://www.opensourcedesign.nl" target="_blank">Open Source Design</a> - e-mail: <a href="mailto:support@opensourcedesign.nl">support@opensourcedesign.nl</a></span>
        </div>
        <br/>
        <div align="center">
            <span class="smallgrey">This patched version created for use with Joomla 5 by <a href="https://brainforge.uk" target="_blank">Jonathan Brain</a> - e-mail: <a href="mailto:brainforge.uk@btinternet.com">brainforge.uk@btinternet.com</a></span>
        </div>
		<?php
	}	
}
?>