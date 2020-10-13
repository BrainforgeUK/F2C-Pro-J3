<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminDatePicker extends F2cFieldAdminBase
{
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dat_attributes', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dat_attributes', 'settings'); ?></div>
		</div>			
		<?php
	}
}
?>