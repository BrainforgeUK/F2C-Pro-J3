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
 * Fieldtype helper
 * 
 * This class is used to provide utility functions for Form2Content FieldType operations.
 * 
 * @package     Joomla.Site
 * @subpackage  com_form2content
 * @since       6.16.0
 */
class F2cHelperFieldtype
{
	/**
	 * Create a dictionary of FieldType objects which can be accessed by Id
	 * 
	 * @param	int			$id		Id of the Fieldtype
	 * 
	 * @return	string		Name of the Fieldtype with the given Id
	 * @since	6.16.0
	 */
	public static function getNameById($id)
	{
		static $dicById = null;
		
		if(!$dicById)
		{
			$db 	= JFactory::getDbo();
			$query 	= $db->getQuery(true);
			
			$query->select('id, name')->from('#__f2c_fieldtype');
			$db->setQuery($query);
			
			$dicById = $db->loadObjectList('id');
		}
				
		return $dicById[$id]->name;
	}	
	
	/**
	 * Create a dictionary of FieldType objects which can be accessed by Name
	 * 
	 * @param	string			$name	Name of the Fieldtype
	 * 
	 * @return	int		Id of the Fieldtype with the given name
	 * @since	6.16.0
	 */
	public static function getIdByName($name)
	{
		static $dicByName = null;
		
		if(!$dicByName)
		{
			$db 	= JFactory::getDbo();
			$query 	= $db->getQuery(true);
			
			$query->select('id, name')->from('#__f2c_fieldtype');
			$db->setQuery($query);
			
			$dicByName = $db->loadObjectList('name');
		}
				
		return $dicByName[$name]->id;
	}	
}
?>