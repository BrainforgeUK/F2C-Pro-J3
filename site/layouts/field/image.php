<?php
defined('JPATH_BASE') or die;

$field = $displayData['field'];
$form =	$displayData['form'];
?>
<table>
<tr>
	<td colspan="2">
		<?php if($field->settings->get('img_input_type', F2C_FIELD_IMAGE_UPLOAD) == F2C_FIELD_IMAGE_BROWSE) : ?>
			<?php echo $form->getInput($field->elementId.'_browse'); ?>
		<?php else : ?>
			<script type="text/javascript">
				<?php if($field->f2cConfig->get('force_iframe_upload', 0)) : ?>
					formDataSupport = false;
				<?php endif; ?>
				var f2cfield<?php echo $field->elementId; ?>={id:<?php echo $field->id; ?>, fieldtypeid:<?php echo $field->fieldtypeid; ?>, contenttypeid:<?php echo $field->projectid ?>};
				
				if (formDataSupport)
				{
					document.write("<?php echo $field->renderUploadControl($field->elementId,'uploadFile(f2cfield'.$field->elementId.','.$displayData['jsExtensionsArray'].');', $displayData['extensions']); ?>");		
				} 
				else
				{
					document.write("<iframe id=\"t<?php echo $field->id; ?>_iframe\" src=\"<?php echo Path::Combine(JUri::root(true), 'index.php?option=com_form2content&view=iframeupload&task=form.renderiframeupload&format=raw&fieldid='.$field->id.'&contenttypeid='.$field->projectid); ?>\" frameborder=\"0\" height=\"18\" width=\"220\" scrolling=\"no\"></iframe>");
				}						
			</script>
			&nbsp;
			
			<?php if(!$field->settings->get('requiredfield')) : ?>			
				<input type="button" onclick="deleteUploadedFile(f2cfield<?php echo $field->elementId; ?>);return false;" value="<?php echo Jtext::_('COM_FORM2CONTENT_DELETE_IMAGE'); ?>" <?php echo $displayData['attribsDelete']; ?> />&nbsp;
			<?php endif; ?>
		<?php endif; ?>
		
		<?php if(!$field->settings->get('requiredfield')) : ?>			
			<input type="hidden" id="<?php echo $field->elementId; ?>_del" name="<?php echo $field->elementId; ?>_del" value="">
		<?php endif; ?>
		
		<?php if($field->settings->get('img_cropping', F2C_FIELD_IMAGE_CROP_NOT_ALLOWED) != F2C_FIELD_IMAGE_CROP_NOT_ALLOWED) : ?>
			<a id="<?php echo $field->elementId; ?>_crop" href="#modalCropWindow" class="btn" style="display: none;"><?php echo JText::_('COM_FORM2CONTENT_CROP'); ?></a>
		<?php endif; ?>

		<input type="hidden" id="<?php echo $field->elementId; ?>_filename" name="<?php echo $field->elementId; ?>_filename" value="<?php echo $this->escape($field->values['FILENAME']);?>">
		
		<?php if(JFactory::getApplication()->isClient('site') && $displayData['requiredText'] != '') : ?>
			<span class="f2c_required">&nbsp;<?php echo $this->escape($displayData['requiredText']); ?></span>
		<?php endif; ?>
		
		<?php if(JFactory::getApplication()->isClient('site') && $displayData['description'] != '') : ?>
			&nbsp;<?php echo JHtml::tooltip($displayData['description'], $displayData['title']); ?>
		<?php endif; ?>
		
		<?php if(!$field->settings->get('img_show_alt_tag')) : ?>
			<input type="hidden" id="<?php echo $field->elementId; ?>_alt" name="<?php echo $field->elementId; ?>_alt" value="">
		<?php endif; ?>
			
		<?php if(!$field->settings->get('img_show_title_tag')) : ?>
			<input type="hidden" id="<?php echo $field->elementId; ?>_title" name="<?php echo $field->elementId; ?>_title" value="">
		<?php endif; ?>
	</td>
</tr>

<?php if($field->settings->get('img_show_alt_tag')) : ?>
	<tr>
		<td><?php echo Jtext::_('COM_FORM2CONTENT_ALT_TEXT'); ?>:</td>
		<td>
			<input type="text" id="<?php echo $field->elementId; ?>_alt" name="<?php echo $field->elementId; ?>_alt" value="<?php echo $this->escape($field->values['ALT']); ?>"
			 <?php echo $displayData['widthAltText']; ?> <?php echo $displayData['maxLengthAltText']; ?> <?php echo $displayData['attribsAltText'];?> >
		</td>
	</tr>
<?php endif; ?>

<?php if($field->settings->get('img_show_title_tag')) : ?>
	<tr>
		<td><?php echo Jtext::_('COM_FORM2CONTENT_TITLE'); ?>:</td>
		<td>
			<input type="text" id="<?php echo $field->elementId; ?>_title" name="<?php echo $field->elementId; ?>_title" value="<?php echo $this->escape($field->values['TITLE']); ?>"
			 <?php echo $displayData['widthTitle']; ?> <?php echo $displayData['maxLengthTitle']; ?> <?php echo $displayData['attribsTitle'];?> >
		</td>
	</tr>
<?php endif; ?>

<tr>
	<td valign="top"><?php echo Jtext::_('COM_FORM2CONTENT_PREVIEW'); ?>:</td>
	<td>
		<span id="<?php echo $field->elementId; ?>_previewcontainer">
			<img id="<?php echo $field->elementId; ?>_preview" src="<?php echo $displayData['thumbSrc']; ?>" style="border: 1px solid #000000;<?php echo $displayData['thumbVis']; ?>">
		</span>
	</td>
</tr>
</table>

<!-- Hidden field to hold temporary uploaded filename -->
<input type="hidden" name="<?php echo $field->elementId ?>_tmpfilename" id="<?php echo $field->elementId ?>_tmpfilename" value="<?php echo $this->escape($displayData['tmpFileName']); ?>" >
<!-- Hidden field to hold original filename before upload -->
<input type="hidden" name="<?php echo $field->elementId ?>_originalfilename" id="<?php echo $field->elementId ?>_originalfilename" value="<?php echo $this->escape($field->values['FILENAME']); ?>" >	
<!-- Hidden field to check whether the image was cropped -->
<input type="hidden" name="<?php echo $field->elementId ?>_cropped" id="<?php echo $field->elementId ?>_cropped" value="<?php echo $this->escape($displayData['cropped']); ?>" >	
<input type="hidden" name="hid<?php echo $field->elementId ?>" id="hid<?php echo $field->elementId ?>" value="<?php echo $this->escape($field->internal['fieldcontentid']); ?>" >