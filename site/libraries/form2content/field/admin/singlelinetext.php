<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminSingleLineText extends F2cFieldAdminBase
{
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('slt_size', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('slt_size', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('slt_max_length', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('slt_max_length', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('slt_attributes', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('slt_attributes', 'settings'); ?></div>
		</div>		
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('html_inputtype', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('html_inputtype', 'settings'); ?></div>
		</div>					
		<h4><?php echo JText::_('COM_FORM2CONTENT_FIELD_VALIDATION'); ?></h4>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('slt_pattern_client', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('slt_pattern_client', 'settings'); ?></div>
		</div>		
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('slt_pattern_server', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('slt_pattern_server', 'settings'); ?></div>
		</div>		
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('slt_pattern_message', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('slt_pattern_message', 'settings'); ?></div>
		</div>						
		<?php
	}
}
?>