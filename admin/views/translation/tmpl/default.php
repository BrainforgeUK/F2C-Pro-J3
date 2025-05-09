<?php
defined('JPATH_PLATFORM') or die;

// Removed Brainforge.uk 2025/04/29
// JHtml::_('behavior.tooltip');
// Modified Brainforge.uk 20250509
//JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
Joomla.submitbutton = function(task) 
{
	if (task == 'translation.cancel') 
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
		return true;
	}
	
	if(!document.formvalidator.isValid(/* Modified Brainforge.uk 2025/04/29 */ document.getElementById('adminForm')))
	{
		alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		return false;
	}
		
	Joomla.submitform(task, document.getElementById('adminForm'));
	return true;
}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_form2content&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<div class="row-fluid">
	<!-- Begin Content -->
	<div class="span12 form-horizontal">
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('language_id'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('language_id'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('reference_id'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('reference_id'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('title_original'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('title_original'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('title_translation'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('title_translation'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('description_translation'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('description_translation'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('description_original'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('description_original'); ?></div>
		</div>
	</div>
	<?php echo F2cViewHelper::displayCredits(); ?>
	<!-- End Content -->
</div>
<input type="hidden" name="task" value="" />
<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->getCmd('return');?>" />
<?php echo JHtml::_('form.token'); ?>
</form>