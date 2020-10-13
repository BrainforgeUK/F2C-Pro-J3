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
 * Datetime helper
 * 
 * This class is used to provide utility functions for datetime operations.
 * 
 * @package     Joomla.Site
 * @subpackage  com_form2content
 * @since       6.16.0
 */
class F2cHelperDatetime
{
	/**
	 * Method to convert a date into the ISO 8601 format when it's not empty
	 * 
	 * @param	object		$date		The date to be formatted.
	 * @return	string		Date in ISO 8601 format or empty string.
	 * @since	6.16.0
	 */
	public static function emptyOrIso8601DateToSql($date)
	{
		if($date)
		{
			$formattedDate = new JDate($date);	
			return $formattedDate->toSql();
		}
		else 
		{
			return '';	
		}
	}	
}
?>