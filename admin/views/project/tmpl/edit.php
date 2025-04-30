<?php 
defined('JPATH_PLATFORM') or die;

// Removed Brainforge.uk 2025/04/29
// JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');

JForm::addFieldPath(JPATH_COMPONENT_SITE.'/models/fields');

JText::script('COM_FORM2CONTENT_SYNC_ARTICLES_CONFIRM');
JText::script('COM_FORM2CONTENT_OVERWRITE_DEFAULT_FORM_TEMPLATE');
JText::script('COM_FORM2CONTENT_FORM_TEMPLATE_WRITTEN');

// Added Brainforge.uk 2025/04/30
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useStyle('form2content.admin');

?>
<script type="text/javascript">
Joomla.submitbutton = function(task) 
{
	if (task == 'project.cancel' || document.formvalidator.isValid(/* Modified Brainforge.uk 2025/04/29 */ document.getElementById('adminForm'))) 
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
		return true;
	}

	return false;
}

function syncdata()
{
	if(confirm(Joomla.JText._('COM_FORM2CONTENT_SYNC_ARTICLES_CONFIRM')))
	{
		Joomla.submitform('project.syncarticles', document.getElementById('adminForm'));
		return true;
	}
	else
	{
		return false;
	}	
}

function generateDefaultFormTemplate(id, overwrite, classicLayout)
{
	var url = '<?php echo JURI::base(); ?>index.php?option=com_form2content&task=project.createsampleformtemplate&format=raw&view=project&id='+id+'&overwrite='+overwrite+'&classic='+classicLayout;
	var overwriteTemplate = Joomla.JText._('COM_FORM2CONTENT_OVERWRITE_DEFAULT_FORM_TEMPLATE');
	var writtenTemplate = Joomla.JText._('COM_FORM2CONTENT_FORM_TEMPLATE_WRITTEN');

	var x = new Request({
        url: url, 
        method: 'get', 
        onRequest: function()
        {
        },
        onSuccess: function(response)
        {
            result = response.split(';');

            if(result[0] == 0)
            {
                alert(writtenTemplate.replace('%s', result[1]));
            }
            else
            {
                if(confirm(overwriteTemplate.replace('%s', result[1])))
                {
                	generateDefaultFormTemplate(id, 1, classicLayout);
                }
            }
        	return true;
        },
        onFailure: function()
        {
             alert('Error generating template.');
        }                
    }).send();
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
			<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
		</div>
				
		<?php echo JHtml::_('bootstrap.startTabSet', 'mainTab', array('active' => 'settings')); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'mainTab', 'settings', JText::_('COM_FORM2CONTENT_GENERAL_SETTINGS')); ?>
						
				<?php  $fieldSets = $this->form->getFieldsets('settings'); ?>
				
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('article_caption', 'settings'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('article_caption', 'settings'); ?></div>
				</div>
				
				<?php foreach ($fieldSets as $name => $fieldSet) : ?>				
					<!--  <h4><?php echo !empty($fieldSet->description) ? $this->escape(JText::_($fieldSet->description)) : ''; ?></h4>-->
					<?php foreach ($this->form->getFieldset($name) as $field) : ?>
						
							<div class="control-group">
								<div class="control-label">
									<?php echo $field->label; ?>
								</div>
								<div class="controls">
									<?php echo $field->input; ?>
								</div>
							</div>						
					<?php endforeach; ?>
					
					<?php if($fieldSet->name == 'form_template') : ?>
						<div class="control-group">
							<div class="control-label">&nbsp;</div>
							<div class="controls">
								<input type="button" value="<?php echo JText::_('COM_FORM2CONTENT_GENERATE_DEFAULT_FORM_TEMPLATE');?>" onclick="generateDefaultFormTemplate(<?php echo $this->item->id; ?>, 0, 0);" class="btn" />
								<input type="button" value="<?php echo JText::_('COM_FORM2CONTENT_GENERATE_DEFAULT_FORM_TEMPLATE').' ('.JText::_('COM_FORM2CONTENT_CLASSIC_LAYOUT').')';?>" onclick="generateDefaultFormTemplate(<?php echo $this->item->id; ?>, 0, 1);" class="btn" />
							</div>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'mainTab', 'artdefparams', JText::_('COM_FORM2CONTENT_ARTICLE_DEFAULT_SETTINGS')); ?>
			
				<p><?php echo JText::_('COM_FORM2CONTENT_CONTENTTYPE_SETTINGS_BATCH_EXPLANATION');?><br/><br/></p>
			
				<?php echo JHtml::_('bootstrap.startTabSet', 'artdefparamsTab', array('active' => 'advartparams')); ?>
					<?php echo JHtml::_('bootstrap.addTab', 'artdefparamsTab', 'advartparams', JText::_('COM_FORM2CONTENT_JOOMLA_ADVANCED_ARTICLE_PARAMETERS')); ?>
						<?php  $fieldSets = $this->form->getFieldsets('attribs'); ?>
						<?php foreach ($fieldSets as $name => $fieldSet) : ?>
							<?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
								<p class="tip"><?php echo $this->escape(JText::_($fieldSet->description));?></p>
							<?php endif;
							foreach ($this->form->getFieldset($name) as $field) : ?>
								<div class="control-group">
									<div class="control-label">
										<?php echo $field->label; ?>
									</div>
									<div class="controls">
										<?php echo $field->input; ?>
									</div>
								</div>
							<?php endforeach; ?>
						<?php endforeach; ?>
					<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php echo JHtml::_('bootstrap.addTab', 'artdefparamsTab', 'imagesurls', JText::_('COM_FORM2CONTENT_FIELDSET_URLS_AND_IMAGES')); ?>
						<div class="control-group">
							<div class="span6 form-horizontal">
								<?php foreach($this->form->getGroup('images') as $field): ?>
									<div class="control-group">
										<div class="control-label">
										<?php if (!$field->hidden): ?>
											<?php echo $field->label; ?>
										<?php endif; ?>
										</div>
										<div class="controls">
										<?php echo $field->input; ?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
							<div class="span6 form-horizontal">
								<?php foreach($this->form->getGroup('urls') as $field): ?>
									<div class="control-group">
										<div class="control-label">
										<?php if (!$field->hidden): ?>
											<?php echo $field->label; ?>
										<?php endif; ?>
										</div>
										<div class="controls">
										<?php echo $field->input; ?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>					
					<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php echo JHtml::_('bootstrap.addTab', 'artdefparamsTab', 'metadata', JText::_('COM_FORM2CONTENT_METADATA_INFORMATION')); ?>
						<fieldset>
							<?php echo $this->loadTemplate('metadata'); ?>
						</fieldset>			
					<?php echo JHtml::_('bootstrap.endTab'); ?>	
					<?php echo JHtml::_('bootstrap.addTab', 'artdefparamsTab', 'synchronize', JText::_('COM_FORM2CONTENT_SYNCHRONIZE')); ?>
						<?php echo JText::_('COM_FORM2CONTENT_SYNCHRONIZE_ARTICLES_INSTRUCTIONS'); ?>
							<p><br/></p>
							<div class="control-group">
								<div class="control-label"></div>
								<div class="controls"><input type="button" name="jform[metadata][sync]" id="jform_metadata_sync" value="<?php echo JText::_('COM_FORM2CONTENT_SYNC_EXISTING_ARTICLES'); ?>" class="btn" onclick="syncdata();" /></div>
							</div>
					<?php echo JHtml::_('bootstrap.endTab'); ?>
				<?php echo JHtml::_('bootstrap.endTabSet'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			
			<?php echo JHtml::_('bootstrap.addTab', 'mainTab', 'permissions', JText::_('COM_FORM2CONTENT_FIELDSET_RULES_CONTENTTYPE')); ?>
				<fieldset><?php echo $this->form->getInput('rules'); ?></fieldset>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	<!--  End Content -->
	</div>
	<?php echo F2cViewHelper::displayCredits(); ?>
</div>
<?php
echo $this->form->getInput('published');		
echo $this->form->getInput('created_by');
echo $this->form->getInput('created');
echo $this->form->getInput('modified');
echo $this->form->getInput('version');
?>
<input type="hidden" name="task" value="" />
<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->getCmd('return');?>" />
<?php echo JHtml::_('form.token'); ?>
</form>