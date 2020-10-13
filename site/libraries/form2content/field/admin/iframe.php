<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminIframe extends F2cFieldAdminBase
{
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('ifr_attributes_iframe', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('ifr_attributes_iframe', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('ifr_attributes_width', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('ifr_attributes_width', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('ifr_attributes_height', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('ifr_attributes_height', 'settings'); ?></div>
		</div>			
		<?php
	}
}
?>