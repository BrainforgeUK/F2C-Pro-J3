<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminJoomlaTemplate extends F2cFieldAdminBase
{
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('default', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('default', 'settings'); ?></div>
		</div>		
		<?php 
	}
	
	public function prepareSave(&$data, $useRequestData)
	{
		if(empty($data['frontvisible']) && trim($data['settings']['requiredfield']) == 1)
		{
			throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_PROJECTFIELD_NOT_VISIBLE_DEFAULT_EMPTY'));
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