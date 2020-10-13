<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminJoomlaTitle extends F2cFieldAdminBase
{
	public $defaultFieldLabel = 'JGLOBAL_TITLE';
		
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('default', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('default', 'settings'); ?></div>
		</div>		
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('attributes', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('attributes', 'settings'); ?></div>
		</div>	
		<?php 
	}
	
	public function clientSideValidation($view)
	{
		?>
		if(jQuery('#jform_frontvisible0').is(':checked') && jQuery('#jform_settings_default').val().trim() == '')
		{
			alert("<?php echo JText::_('COM_FORM2CONTENT_ERROR_PROJECT_TITLE_DEFAULT_EMPTY', true); ?>");
			return false;
		}
		<?php 	
	}
	
	public function prepareSave(&$data, $useRequestData)
	{
		$data['settings']['error_message_required'] = '';
		$data['settings']['requiredfield'] = 1;
		
		if(empty($data['frontvisible']) && trim($data['settings']['default']) == '')
		{
			throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_PROJECT_TITLE_DEFAULT_EMPTY'));
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