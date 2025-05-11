<?php
// No direct access.
defined('JPATH_PLATFORM') or die;

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
// Removed Brainforge.uk 2025/04/29
// JHtml::_('behavior.tooltip');
JHtml::_('bootstrap.framework');
JHtml::_('formbehavior.chosen', 'select');

JHtml::script('com_form2content/validation.js', array('relative' => true));
JHtml::script('com_form2content/f2c_upload.js', array('relative' => true));
JHtml::script('com_form2content/f2c_lists.js', array('relative' => true));
JHtml::script('com_form2content/f2c_util.js', array('relative' => true));
JHtml::script('com_form2content/jquery.blockUI.js', array('relative' => true));
JHtml::script('com_form2content/jquery.sprintf.js', array('relative' => true));
JHtml::script('com_form2content/f2c_imageupload.js', array('relative' => true));
JHtml::stylesheet('com_form2content/f2cfields.css', array('relative' => true));
JHtml::stylesheet('com_form2content/main.css', array('relative' => true));

JText::script('COM_FORM2CONTENT_ERROR_DATE_FIELD_INCORRECT_DATE');
JText::script('COM_FORM2CONTENT_UP');
JText::script('COM_FORM2CONTENT_DOWN');
JText::script('COM_FORM2CONTENT_ADD');
JText::script('COM_FORM2CONTENT_DELETE');
JText::script('COM_FORM2CONTENT_EXTENSION_UPLOAD_NOT_ALLOWED');

// Added Brainforge.uk 2025/04/30
if (method_exists($this, 'getDocument'))
{
	/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
	$wa = $this->getDocument()->getWebAssetManager();
	$wa->useStyle('form2content.admin');
}

$input 		= JFactory::getApplication()->input;
$assoc 		= JLanguageAssociations::isEnabled();

JForm::addFieldPath(JPATH_COMPONENT_SITE.'/models/fields');
?>
<script type="text/javascript">
<!--
<?php echo $this->jsScripts['fieldInit'];?>
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

	messages = {"error": []};

	<?php echo $this->jsScripts['validation']; ?>

	Form2Content.Validation.CheckRequiredFields(arrValidation);
	
	if(messages.error.length > 0)
	{
		Joomla.renderMessages(messages);
		return false;
	}

	Joomla.submitform(task, document.getElementById('adminForm'));
}
-->
</script>
<form action="<?php echo JRoute::_('index.php?option=com_form2content&layout=edit&id='.(int) $this->item->id); ?>" 
	  method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span10 form-horizontal">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('COM_FORM2CONTENT_ARTICLE_DETAILS');?></a></li>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_FORM2CONTENT_FIELDSET_PUBLISHING');?></a></li>
				<?php $fieldSets = $this->form->getFieldsets('attribs'); ?>
				<?php foreach ($fieldSets as $name => $fieldSet) : ?>
					<li><a href="#attrib-<?php echo $name;?>" data-toggle="tab"><?php echo JText::_($fieldSet->label);?></a></li>
				<?php endforeach; ?>	
				<?php if($assoc) : ?>
					<li><a href="#associations" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS');?></a></li>
				<?php endif; ?>			
				<li><a href="#metadata" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS');?></a></li>
				<li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_FORM2CONTENT_FIELDSET_RULES');?></a></li>
			</ul>
				
			<div class="tab-content">
				<!-- Begin Tabs -->
				<div class="tab-pane active" id="general">
					<div class="row-fluid">
						<div class="span6">
							<?php echo $this->renderCustomField($this->contentType->getFieldByType('JoomlaTitle')->fieldname)?>
							<?php echo $this->renderCustomField($this->contentType->getFieldByType('JoomlaCategory')->fieldname)?>
						</div>
						<div class="span6">
							<?php
							foreach ($this->contentType->getFieldsByType('JoomlaTemplate') as $templateField)
							{
								echo $this->renderCustomField($templateField->fieldname);
							}
							?>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
						<?php
						// User defined fields
						if(count($this->item->fields))
						{
							foreach($this->item->fields as $field)
							{	
								// Skip Joomla core fields and captcha			
								if($field->classificationId != 0 && $field->fieldtypename != 'Captcha')
								{		
								?>
								<div class="control-group">
									<div class="control-label"><?php echo $field->renderLabel($this->translatedFields, $this->form); ?></div>
									<div class="controls f2cfield <?php echo $field->getCssClass(); ?>"><?php echo $field->render($this->translatedFields, $this->contentType->settings, array(), $this->form, $this->item->id); ?></div>
								</div>
								<?php
								}
							}
						}
						?>							
						</div>
					</div>					
				</div>
				<div class="tab-pane" id="publishing">
					<div class="row-fluid">
						<div class="span6">
							<?php echo $this->renderCustomField($this->contentType->getFieldByType('JoomlaAlias')->fieldname)?>
							<?php echo $this->renderCustomField($this->contentType->getFieldByType('JoomlaId')->fieldname)?>
							<?php echo $this->renderCustomField($this->contentType->getFieldByType('JoomlaCreatedBy')->fieldname)?>
							<?php echo $this->renderCustomField($this->contentType->getFieldByType('JoomlaCreatedByAlias')->fieldname)?>
							<?php echo $this->renderCustomField($this->contentType->getFieldByType('JoomlaCreated')->fieldname)?>
						</div>
						<div class="span6">
							<?php echo $this->renderCustomField($this->contentType->getFieldByType('JoomlaPublishUp')->fieldname)?>
							<?php echo $this->renderCustomField($this->contentType->getFieldByType('JoomlaPublishDown')->fieldname)?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('modified'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('modified'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('modified_by'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('modified_by'); ?>
								</div>								
							</div>
		
							<?php if ($this->jArticle->version) : ?>
								<div class="control-group">
									<div class="control-label">
										<?php echo $this->form->getLabel('version'); ?>
									</div>
									<div class="controls">
										<?php echo $this->form->getInput('version'); ?>
									</div>
								</div>
							<?php endif; ?>
		
							<?php if ($this->jArticle->hits) : ?>
								<div class="control-group">
									<div class="control-label">
										<?php echo $this->form->getLabel('hits'); ?>
									</div>
									<div class="controls">
										<?php echo $this->form->getInput('hits'); ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>			
				</div>
	 			<?php  $fieldSets = $this->form->getFieldsets('attribs'); ?>
				<?php foreach ($fieldSets as $name => $fieldSet) : ?>
					<div class="tab-pane" id="attrib-<?php echo $name;?>">
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
					</div>
				<?php endforeach; ?>
				<?php if($assoc) :?>
				<div class="tab-pane" id="associations">
					<fieldset>
						<?php echo $this->renderCustomField($this->contentType->getFieldByType('JoomlaAssociations')->fieldname); ?>
					</fieldset>
				</div>
				<?php endif; ?>
				<div class="tab-pane" id="metadata">
					<fieldset>
						<?php echo $this->loadTemplate('metadata'); ?>
					</fieldset>
				</div>
				<?php if ($this->canDo->get('core.admin')): ?>
					<div class="tab-pane" id="permissions">
						<fieldset>
							<?php echo $this->form->getInput('rules'); ?>
						</fieldset>
					</div>
				<?php endif; ?>
				<!-- End Tabs -->
			</div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" />
			<?php echo JHtml::_('form.token'); ?>											
		</div>
		<!--  End Content -->
		<!-- Begin Sidebar -->
		<div class="span2">
			<h4><?php echo JText::_('JDETAILS');?></h4>
			<hr />
			<fieldset class="form-vertical">
				<div class="control-group">
					<div class="controls">
						<?php echo $this->form->getValue('title'); ?>
					</div>
				</div>
				<?php
				echo $this->renderCustomField($this->contentType->getFieldByType('JoomlaPublished')->fieldname); 
				echo $this->renderCustomField($this->contentType->getFieldByType('JoomlaAccess')->fieldname); 
				echo $this->renderCustomField($this->contentType->getFieldByType('JoomlaFeatured')->fieldname); 
				echo $this->renderCustomField($this->contentType->getFieldByType('JoomlaLanguage')->fieldname); 
				echo $this->renderCustomField($this->contentType->getFieldByType('JoomlaTags')->fieldname); 
				?>
			</fieldset>
		</div>
		<!-- End Sidebar -->	
	</div>
	<?php echo F2cViewHelper::displayCredits(); ?>
	<?php echo $this->form->getInput('projectid'); ?>
</form>
<?php echo JHTML::_('bootstrap.renderModal', 'modalCropWindow', $this->modalParams, ''); ?>