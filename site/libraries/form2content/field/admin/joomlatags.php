<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminJoomlaTags extends F2cFieldAdminBase
{
	public $defaultFieldLabel = 'JTAG';
		
	function display($form, $item)
	{
		// Load language file from the tags component
		JFactory::getLanguage()->load('com_tags', JPATH_ADMINISTRATOR);
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('field_ajax_mode', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('field_ajax_mode', 'settings'); ?></div>
		</div>		
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('allow_custom', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('allow_custom', 'settings'); ?></div>
		</div>		
		<?php 
	}
		
	public function prepareSave(&$data, $useRequestData)
	{
		$data['settings']['error_message_required'] = '';
		$data['settings']['requiredfield'] = 0;
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