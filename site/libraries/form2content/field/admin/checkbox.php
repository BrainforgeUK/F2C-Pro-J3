<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminCheckbox extends F2cFieldAdminBase
{
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('chk_attributes', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('chk_attributes', 'settings'); ?></div>
		</div>			
		<?php
	}
}
?>