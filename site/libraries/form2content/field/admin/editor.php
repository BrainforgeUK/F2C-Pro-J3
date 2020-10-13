<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminEditor extends F2cFieldAdminBase
{
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('mle_num_rows', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('mle_num_rows', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('mle_num_cols', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('mle_num_cols', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('mle_width', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('mle_width', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('mle_height', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('mle_height', 'settings'); ?></div>
		</div>			
		<?php
	}
}
?>