<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminEmail extends F2cFieldAdminBase
{
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('eml_attributes_email', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('eml_attributes_email', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('eml_attributes_display_as', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('eml_attributes_display_as', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('eml_show_display_as', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('eml_show_display_as', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('html_inputtype', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('html_inputtype', 'settings'); ?></div>
		</div>					
		<?php
	}
}
?>