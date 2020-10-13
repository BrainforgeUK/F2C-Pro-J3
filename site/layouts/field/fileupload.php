<?php
defined('JPATH_BASE') or die;

$field = $displayData['field'];
?>
<table>
<tr>
	<td colspan="2">
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
				document.write("<iframe id=\"t<?php echo $field->id; ?>_iframe\" src=\"<?php echo F2cUri::GetClientRoot().'index.php?option=com_form2content&view=iframeupload&task=form.renderiframeupload&format=raw&fieldid='.$field->id.'&contenttypeid='.$field->projectid; ?>\" frameborder=\"0\" height=\"18\" width=\"220\" scrolling=\"no\"></iframe>");
			}						
		</script>
		&nbsp;

		<?php if(!$field->settings->get('requiredfield')) : ?>
			<input type="button" onclick="deleteUploadedFile(f2cfield<?php echo $field->elementId; ?>);return false;" value="<?php echo Jtext::_('COM_FORM2CONTENT_DELETE_FILE'); ?>" <?php echo $displayData['deleteAttribs']; ?> />&nbsp;
			<input type="hidden" id="<?php echo $field->elementId; ?>_del" name="<?php echo $field->elementId; ?>_del" value="">
		<?php endif; ?>

		<?php if(JFactory::getApplication()->isClient('site') && $displayData['requiredText'] != '') : ?>
			<span class="f2c_required">&nbsp;<?php echo $this->escape($displayData['requiredText']); ?></span>
		<?php endif; ?>

		<?php if(JFactory::getApplication()->isClient('site') && $displayData['description'] != '') : ?>
			&nbsp;<?php echo JHtml::tooltip($displayData['description'], $displayData['title']); ?>
		<?php endif; ?>
		
	</td>
</tr>
<tr>
	<td valign="top"><?php echo Jtext::_('COM_FORM2CONTENT_PREVIEW'); ?>:&nbsp;
		<span id="<?php echo $field->elementId; ?>_previewcontainer">
			<?php if($displayData['link']) : ?>
				<a id="<?php echo $field->elementId; ?>_preview" href="<?php echo $displayData['link']; ?>" target="_blank"><?php echo $this->escape($field->values['FILENAME']);?></a>
			<?php endif; ?>
		</span>
	</td>
</tr>
</table>
			







<!-- Hidden field to hold temporary uploaded filename -->
<input type="hidden" name="<?php echo $field->elementId ?>_tmpfilename" id="<?php echo $field->elementId ?>_tmpfilename" value="<?php echo $this->escape($displayData['tmpFileName']); ?>" >
<!-- Hidden field to hold original filename before upload -->
<input type="hidden" name="<?php echo $field->elementId ?>_originalfilename" id="<?php echo $field->elementId ?>_originalfilename" value="<?php echo $this->escape($field->values['FILENAME']); ?>" >	
<input type="hidden" name="hid<?php echo $field->elementId ?>" id="hid<?php echo $field->elementId ?>" value="<?php echo $this->escape($field->internal['fieldcontentid']); ?>" >