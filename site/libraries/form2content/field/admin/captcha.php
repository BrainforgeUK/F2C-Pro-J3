<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminCaptcha extends F2cFieldAdminBase
{
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('public_key', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('public_key', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('private_key', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('private_key', 'settings'); ?></div>
		</div>			
		<?php 
	}
	
	public function prepareSave(&$data, $useRequestData)
	{
		$data['settings']['error_message_required'] = '';
		$data['settings']['requiredfield'] = 1;
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