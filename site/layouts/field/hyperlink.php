<?php
defined('JPATH_BASE') or die;

$field = $displayData['field'];
?>
<table>
<tr>
	<!--  <td><?php echo Jtext::_('COM_FORM2CONTENT_URL'); ?>:</td>-->
	<td colspan="2">
		<input type="text" <?php echo $displayData['attribsUrl']; ?> name="<?php echo $field->elementId; ?>" 
		 id="<?php echo $field->elementId; ?>" value="<?php echo $this->escape($field->values['URL']); ?>"	size= "40" maxlength= "300">
		 
		<?php if(JFactory::getApplication()->isClient('site') && $displayData['requiredText'] != '') : ?>
			<span class="f2c_required">&nbsp;<?php echo $this->escape($displayData['requiredText']); ?></span>
		<?php endif; ?>
		
		<?php if(JFactory::getApplication()->isClient('site') && $displayData['description'] != '') : ?>
			&nbsp;<?php echo JHtml::tooltip($displayData['description'], $displayData['title']); ?>
		<?php endif; ?>
	</td>
</tr>

<?php if($field->settings->get('lnk_show_display_as')) : ?>
<tr>
	<td><?php echo Jtext::_('COM_FORM2CONTENT_DISPLAY_AS'); ?>:</td>
	<td>		
		<input type="text" <?php echo $displayData['attribsDisplayAs']; ?> name="<?php echo $field->elementId; ?>_display" 
		 id="<?php echo $field->elementId; ?>_display"
		 value="<?php echo $this->escape($field->values['DISPLAY_AS']); ?>"	size= "40" maxlength= "100">
	</td>		 		 
</tr>
<?php endif; ?>

<?php if($field->settings->get('lnk_show_title')) : ?>
<tr>
	<td><?php echo Jtext::_('COM_FORM2CONTENT_TITLE'); ?>:</td>
	<td>		
		<input type="text" <?php echo $displayData['attribsTitle']; ?> name="<?php echo $field->elementId; ?>_title" 
		 id="<?php echo $field->elementId; ?>_title"
		 value="<?php echo $this->escape($field->values['TITLE']); ?>"	size= "40" maxlength= "100">
	</td>		 		 
</tr>
<?php endif; ?>

<?php if($field->settings->get('lnk_show_target')) : ?>
<tr>
	<td><?php echo Jtext::_('COM_FORM2CONTENT_TARGET'); ?>:</td>
	<td>		
		<?php echo JHTMLSelect::genericlist($displayData['listTarget'], $field->elementId . '_target', $field->settings->get('lnk_attributes_target') ,'value', 'text', $field->values['TARGET']); ?>
	</td>		 		 
</tr>
<?php endif; ?>

</table>

<?php if(!$field->settings->get('lnk_show_display_as')) : ?>
	<input type="hidden" name="<?php echo $field->elementId ?>_display" id="<?php echo $field->elementId ?>_display" value="<?php echo $this->escape($field->values['DISPLAY_AS']); ?>" >
<?php endif; ?>

<?php if(!$field->settings->get('lnk_show_title')) : ?>
	<input type="hidden" name="<?php echo $field->elementId ?>_title" id="<?php echo $field->elementId ?>_title" value="<?php echo $this->escape($field->values['TITLE']); ?>" >
<?php endif; ?>

<?php if(!$field->settings->get('lnk_show_target')) : ?>
	<input type="hidden" name="<?php echo $field->elementId ?>_target" id="<?php echo $field->elementId ?>_target" value="<?php echo $this->escape($field->values['TARGET']); ?>" >
<?php endif; ?>
		
<input type="hidden" name="hid<?php echo $field->elementId ?>" id="hid<?php echo $field->elementId ?>" value="<?php echo $this->escape($field->internal['fieldcontentid']); ?>" >