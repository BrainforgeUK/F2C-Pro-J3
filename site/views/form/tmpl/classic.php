<?php
// No direct access.
defined('_JEXEC') or die('Restricted access');

// Removed Brainforge.uk 2025/04/29
// JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.framework');
JHtml::_('bootstrap.framework');

JHtml::script('com_form2content/validation.js', array('relative' => true));
JHtml::script('com_form2content/f2c_upload.js', array('relative' => true));
JHtml::script('com_form2content/f2c_util.js', array('relative' => true));
JHtml::script('com_form2content/jquery.blockUI.js', array('relative' => true));
JHtml::script('com_form2content/jquery.sprintf.js', array('relative' => true));
JHtml::script('com_form2content/f2c_lists.js', array('relative' => true));
JHtml::script('com_form2content/f2c_imageupload.js', array('relative' => true));
JHtml::stylesheet('com_form2content/f2cfields.css', array('relative' => true));
JHtml::stylesheet('com_form2content/f2cfrontend.css', array('relative' => true));
JHtml::stylesheet('com_form2content/main.css', array('relative' => true));

JText::script('COM_FORM2CONTENT_ERROR_DATE_FIELD_INCORRECT_DATE');
JText::script('COM_FORM2CONTENT_UP');
JText::script('COM_FORM2CONTENT_DOWN');
JText::script('COM_FORM2CONTENT_ADD');
JText::script('COM_FORM2CONTENT_DELETE');
JText::script('COM_FORM2CONTENT_EXTENSION_UPLOAD_NOT_ALLOWED');

// Added Brainforge.uk 2025/04/30
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useStyle('form2content.admin');
$wa->useStyle('form2content.site');

JForm::addFieldPath(JPATH_COMPONENT_SITE.'/models/fields');

$assocField = $this->contentType->getFieldByType('JoomlaAssociations');
// Added Brainforge.uk 2025/04/30
$itemId = empty($this->activeMenu->id) ? '' : '&Itemid=' .  $this->activeMenu->id;
?>
<script type="text/javascript">	
var dateFormat = '<?php echo $this->dateFormat; ?>';
<?php
echo $this->jsScripts['fieldInit'];
?>
Joomla.submitbutton = function(task) 
{
	if (task == 'form.cancel')
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
		return true;
	}

	if(!document.formvalidator.isValid(/* Modified Brainforge.uk 2025/04/29 */ document.getElementById('adminForm')))
	{
		return false;
	}

	var form = /* Modified Brainforge.uk 2025/04/29 */ document.getElementById('adminForm');
	messages = {"error": []};
	
	<?php echo $this->jsScripts['validation']; ?>
	
	Form2Content.Validation.CheckRequiredFields(arrValidation);
	
	if(messages.error.length > 0)
	{
		Joomla.renderMessages(messages);
		return false;
	}
	
	<?php echo $this->submitForm; ?>
}
</script>
<div class="f2c-article<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>">
	<h1><?php echo $this->pageTitle; ?></h1>
	<div id="f2c_form" class="content_type_<?php echo $this->item->projectid; ?>">
		<form action="<?php echo JRoute::_('index.php?option=com_form2content&view=form&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
		<?php if(!$this->contentTypeSettings->get('use_form_template', 0)) 
		{
		?>
			<div style="float: right;" class="f2c_buttons">
				<?php if($this->settings->get('show_save_button')) :?>
					<button type="button" class="f2c_button f2c_apply" onclick="javascript:Joomla.submitbutton('form.apply')"><?php echo JText::_('COM_FORM2CONTENT_TOOLBAR_APPLY'); ?></button>
				<?php endif;?>
				<button type="button" class="f2c_button f2c_save" onclick="javascript:Joomla.submitbutton('form.save')"><?php echo JText::_('COM_FORM2CONTENT_TOOLBAR_SAVE'); ?></button>
				<?php if($this->settings->get('show_save_and_new_button')) :?>
					<button type="button" class="f2c_button f2c_saveandnew" onclick="javascript:Joomla.submitbutton('form.save2new')"><?php echo JText::_('COM_FORM2CONTENT_TOOLBAR_SAVE_AND_NEW'); ?></button>
				<?php endif;?>
				<?php if($this->settings->get('show_save_as_copy_button')) :?>
					<button type="button" class="f2c_button f2c_saveascopy" onclick="javascript:Joomla.submitbutton('form.save2copy')"><?php echo JText::_('COM_FORM2CONTENT_TOOLBAR_SAVE_AS_COPY'); ?></button>
				<?php endif;?>
				<?php if($this->item->id == 0) { ?>
					<button type="button" class="f2c_button f2c_cancel" onclick="javascript:Joomla.submitbutton('form.cancel')"><?php echo JText::_('COM_FORM2CONTENT_TOOLBAR_CANCEL'); ?></button>
				<?php } else { ?>
					<button type="button" class="f2c_button f2c_cancel" onclick="javascript:Joomla.submitbutton('form.cancel')"><?php echo JText::_('COM_FORM2CONTENT_TOOLBAR_CLOSE'); ?></button>
				<?php } ?>
			</div>

			<div class="clearfix"></div>
		
			<div class="width-60 fltlft">
				<fieldset class="adminform">
				<table class="adminform" width="100%">
				<?php if($assocField->frontvisible) : ?>
					<tr class="f2c_field f2c_associations">
						<td valign="top" class="f2c_field_label"><?php echo $assocField->renderLabel($this->translatedFields, $this->form); ?></td>
						<td valign="top" class="f2c_field_value"><?php echo $assocField->render($this->translatedFields, $this->contentType->settings, array(), $this->form, $this->item->id); ?></td>
					</tr>
				<?php endif; ?>							
				<?php
				// User defined fields
				if(count($this->item->fields))
				{
					foreach ($this->item->fields as $field) 
					{
						// skip processing of hidden fields
						if(!$field->frontvisible) continue;																				
						?>
						<tr class="f2c_field <?php echo 'f2c_' . $field->fieldname; ?>  <?php echo $field->getCssClass(); ?>">
							<td width="100" align="left" class="key f2c_field_label" valign="top">
								<?php echo $field->renderLabel($this->translatedFields); ?>
							</td>
							<td valign="top" class="f2c_field_value">
								<div class="f2c_field">
								<?php echo $field->render($this->translatedFields, $this->contentType->settings, array(), $this->form, $this->item->id); ?>	
								</div>		
							</td>
						</tr>
						<?php
					}
				}
				?>		
				</table>						
				</fieldset>
			</div>
			
			<div class="clearfix"></div>
			
			<div style="float: right;" class="f2c_buttons">
				<button type="button" class="f2c_button f2c_apply" onclick="javascript:Joomla.submitbutton('form.apply')"><?php echo JText::_('COM_FORM2CONTENT_TOOLBAR_APPLY'); ?></button>
				<button type="button" class="f2c_button f2c_save" onclick="javascript:Joomla.submitbutton('form.save')"><?php echo JText::_('COM_FORM2CONTENT_TOOLBAR_SAVE'); ?></button>
				<?php if($this->settings->get('show_save_and_new_button')) :?>
					<button type="button" class="f2c_button f2c_saveandnew" onclick="javascript:Joomla.submitbutton('form.save2new')"><?php echo JText::_('COM_FORM2CONTENT_TOOLBAR_SAVE_AND_NEW'); ?></button>
				<?php endif;?>
				<?php if($this->settings->get('show_save_as_copy_button')) :?>
					<button type="button" class="f2c_button f2c_saveascopy" onclick="javascript:Joomla.submitbutton('form.save2copy')"><?php echo JText::_('COM_FORM2CONTENT_TOOLBAR_SAVE_AS_COPY'); ?></button>
				<?php endif;?>
				<?php if($this->item->id == 0) { ?>
					<button type="button" class="f2c_button f2c_cancel" onclick="javascript:Joomla.submitbutton('form.cancel')"><?php echo JText::_('COM_FORM2CONTENT_TOOLBAR_CANCEL'); ?></button>
				<?php } else { ?>
					<button type="button" class="f2c_button f2c_cancel" onclick="javascript:Joomla.submitbutton('form.cancel')"><?php echo JText::_('COM_FORM2CONTENT_TOOLBAR_CLOSE'); ?></button>
				<?php } ?>
			</div>
		<?php 
		}
		else 
		{
			$this->renderFormTemplate();
		}
		?>		
		<?php echo $this->form->getInput('projectid'); ?>
		<input type="hidden" name="option" value="com_form2content" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->getCmd('return');?>" />
		<input type="hidden" name="Itemid" value="<?php echo JFactory::getApplication()->input->getInt('Itemid'); ?>" />			
		<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>
<?php echo JHTML::_('bootstrap.renderModal', 'modalCropWindow', $this->modalParams, ''); ?>