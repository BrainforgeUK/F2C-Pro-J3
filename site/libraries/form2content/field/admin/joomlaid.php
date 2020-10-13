<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminJoomlaId extends F2cFieldAdminBase
{
	public $defaultFieldLabel = 'JGLOBAL_FIELD_ID_LABEL';
		
	function display($form, $item)
	{
	}
	
	public function prepareSave(&$data, $useRequestData)
	{
		$data['settings']['error_message_required'] = '';
		$data['settings']['requiredfield'] = 0;
		
		if(array_key_exists('frontvisible', $data['settings']))
		{
			$data['frontvisible'] = $data['settings']['frontvisible'];
			unset($data['settings']['frontvisible']);
		}
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