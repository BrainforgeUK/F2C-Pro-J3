<?php
defined('JPATH_BASE') or die;

$field 		= $displayData['field'];
$rowcount 	= 0;
?>
<table id="<?php echo $field->elementId ?>" class="f2c_image_gallery_tbl">
<?php 	
if($field->values['VALUE'] && count($field->values['VALUE']) > 0)
{
	foreach($field->values['VALUE'] as $value)
	{
		$rowId 		= $field->elementId.'_'.$rowcount;
		$state		= $value['STATE'];
		$rowStyle	= $value['STATE'] == 2 ? 'style="display: none;"' : '';	
		$rowcount++;
				
		if(array_key_exists('TMPFILENAME', $value) && $value['TMPFILENAME'])
		{
			$imgSrc = JUri::root(true).Path::Combine($field->f2cConfig->get('images_path'), 'thumb_'.$value['TMPFILENAME']);	
		}
		else 
		{
			$imgSrc = $displayData['thumbsgalleryUrl'] . $value['FILENAME'];	
		}
		?>
		<tr id="<?php echo $rowId; ?>" <?php echo $rowStyle; ?>>
			<td>
				<img src="<?php echo $imgSrc; ?>" />
				<input type="hidden" name="<?php echo $field->elementId ?>RowKey[]" value="<?php echo $rowId; ?>"/>
				<input type="hidden" name="<?php echo $rowId;?>width" id="<?php echo $rowId;?>width" value="<?php echo $value['WIDTH']; ?>"/>
				<input type="hidden" name="<?php echo $rowId;?>height" id="<?php echo $rowId;?>height" value="<?php echo $value['HEIGHT']; ?>"/>
				<input type="hidden" name="<?php echo $rowId;?>thumbwidth" id="<?php echo $rowId;?>thumbwidth" value="<?php echo $value['WIDTH_THUMB']; ?>"/>
				<input type="hidden" name="<?php echo $rowId;?>thumbheight" id="<?php echo $rowId;?>thumbheight" value="<?php echo $value['HEIGHT_THUMB']; ?>"/>
				<input type="hidden" name="<?php echo $rowId;?>_cropped" id="<?php echo $rowId;?>_cropped" value="<?php echo 1; ?>"/>
				<input type="hidden" name="<?php echo $rowId;?>state" id="<?php echo $rowId;?>state" value="<?php echo $state; ?>"/>
							
				<?php if(array_key_exists('TMPFILENAME', $value) && $value['TMPFILENAME']) : ?>
					<input type="hidden" name="<?php echo $rowId;?>filename" value="<?php echo $this->escape($value['TMPFILENAME']); ?>"/>
					<input type="hidden" name="<?php echo $rowId;?>originalfilename" value="<?php echo $this->escape($value['FILENAME']); ?>"/>
				<?php else :?>
					<input type="hidden" name="<?php echo $rowId;?>filename" value="<?php echo $this->escape($value['FILENAME']); ?>"/>
				<?php endif; ?>							
			</td>
			
			<?php if($field->settings->get('igl_show_alt_tag') || $field->settings->get('igl_show_title_tag')) : ?>
				<td>
					<table class="f2c_image_gallery_tbl_alt_title">
					<?php if($field->settings->get('igl_show_alt_tag')) : ?>
						<tr>
							<td><?php echo JText::_('COM_FORM2CONTENT_ALT_TEXT');?></td>
							<td><input type="text" id="<?php echo $rowId;?>alt" name="<?php echo $rowId;?>alt" size="40" value="<?php echo $this->escape($value['ALT']);?>" maxlength="255" <?php echo $field->settings->get('igl_attributes_item_text');?> /></td>						
						</tr>
					<?php endif; ?>				
					<?php if($field->settings->get('igl_show_title_tag')) : ?>
						<tr>
							<td><?php echo JText::_('COM_FORM2CONTENT_TITLE');?></td>
							<td><input type="text" id="<?php echo $rowId;?>title" name="<?php echo $rowId;?>title" size="40" value="<?php echo $this->escape($value['TITLE']);?>" maxlength="255" <?php echo $field->settings->get('igl_attributes_item_text');?> /></td>
						</tr>
					<?php endif; ?>	
					</table>
				</td>			
			<?php endif; ?>

			<td nowrap>
				<a href="javascript:moveRowUp('<?php echo $rowId;?>');"><i class="icon-f2carrow-up f2c_row_button" title="<?php echo JText::_('COM_FORM2CONTENT_UP');?>"></i></a>
				<a href="javascript:moveRowDown('<?php echo $rowId;?>');"><i class="icon-f2carrow-down f2c_row_button" title="<?php echo JText::_('COM_FORM2CONTENT_DOWN');?>"></i></a>
				<a href="javascript:deleteImageGalleryRow('<?php echo $rowId;?>');"><i class="icon-f2cminus f2c_row_button" title="<?php echo JText::_('COM_FORM2CONTENT_DELETE');?>"></i></a>
			</td>
		</tr>
		<?php
	}
}
?>	
</table>

<div id="<?php echo $field->elementId ?>_upload_area">
	<?php if($field->settings->get('igl_input_type', F2C_FIELD_IMAGE_UPLOAD) == F2C_FIELD_IMAGE_BROWSE) : ?>
		<?php echo $displayData['form']->getInput($field->elementId.'_browse'); ?>
	<?php else : ?>
		<script type="text/javascript">
			<?php if($field->f2cConfig->get('force_iframe_upload', 0)) : ?>
				formDataSupport = false;
			<?php endif; ?>					
			if (formDataSupport)
			{
				document.write("<?php echo $field->renderUploadControl($field->elementId,'uploadGalleryImage('.$field->projectid.','.$field->id.','.$displayData['jsExtensionsArray'].');', $displayData['extensions']);?>");		
			} 
			else
			{
				document.write("<iframe id=\"t<?php echo $field->id;?>_iframe\" src=\"<?php echo Path::Combine(JUri::root(true), 'index.php?option=com_form2content&view=iframeupload&task=form.renderiframeupload&format=raw&fieldid='.$field->id.'&contenttypeid='.$field->projectid);?>\" frameborder=\"0\" height=\"18\" width=\"220\" scrolling=\"no\"></iframe>");
			}
		</script>
		&nbsp;	
	<?php endif; ?>
</div>

<?php if(JFactory::getApplication()->isClient('site') && $displayData['requiredText'] != '') : ?>
	<span class="f2c_required">&nbsp;<?php echo $this->escape($displayData['requiredText']); ?></span>
<?php endif; ?>

<input type="hidden" name="hid<?php echo $field->elementId ?>" id="hid<?php echo $field->elementId ?>" value="<?php echo $this->escape($field->internal['fieldcontentid']); ?>" >
<input type="hidden" name="<?php echo $field->elementId ?>MaxKey" id="<?php echo $field->elementId ?>MaxKey" value="<?php echo $rowcount; ?>" >
<input type="hidden" name="<?php echo $field->elementId ?>Cropping" id="<?php echo $field->elementId ?>Cropping" value="<?php echo $field->settings->get('igl_cropping', 0); ?>" >
<input type="hidden" name="<?php echo $field->elementId ?>MaxUploads" id="<?php echo $field->elementId ?>MaxUploads" value="<?php echo $field->settings->get('igl_max_num_images'); ?>" >