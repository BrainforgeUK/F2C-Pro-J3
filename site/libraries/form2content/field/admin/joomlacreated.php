<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminJoomlaCreated extends F2cFieldAdminBase
{
	public $defaultFieldLabel = 'COM_FORM2CONTENT_FIELD_CREATED_LABEL';
		
	function display($form, $item)
	{
	}
	
	public function prepareSave(&$data, $useRequestData)
	{
		$data['settings']['error_message_required'] = '';
		$data['settings']['requiredfield'] = 0;
	}
	
	public function canBeHiddenInFrontEnd()
	{
		return true;
	}
	
	/**
	 * Method to generate template code for the sample template
	 *
	 * @param   string	$fieldname	Name of the field
	 *
	 * @return  string	Generated template code
	 *
	 * @since   6.17.0
	 */
	public function getTemplateSample($fieldname)
	{
		// No template sample
		return 	'';
	}		
}
?>