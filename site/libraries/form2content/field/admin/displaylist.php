<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminDisplayList extends F2cFieldAdminBase
{
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dsp_output_mode', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dsp_output_mode', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dsp_attributes_table', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dsp_attributes_table', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dsp_attributes_tr', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dsp_attributes_tr', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dsp_attributes_th', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dsp_attributes_th', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dsp_attributes_td', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dsp_attributes_td', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dsp_attributes_item_text', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dsp_attributes_item_text', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dsp_attributes_add_button', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dsp_attributes_add_button', 'settings'); ?></div>
		</div>			
		<?php
	}
}
?>