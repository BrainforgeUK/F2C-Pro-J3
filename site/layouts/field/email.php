<?php
defined('JPATH_BASE') or die;

$field = $displayData['field'];
?>
<table>
<tr>
	<td><?php echo Jtext::_('COM_FORM2CONTENT_EMAIL'); ?>:</td>
	<td>
		<input type="<?php echo $displayData['EmailInputType']; ?>" <?php echo $displayData['attribsEmail']; ?> name="<?php echo $field->elementId; ?>" 
		 id="<?php echo $field->elementId; ?>" value="<?php echo $this->escape($field->values['EMAIL']); ?>"	size= "40" maxlength= "100">

		<?php if(JFactory::getApplication()->isClient('site') && $displayData['requiredText'] != '') : ?>
			<span class="f2c_required">&nbsp;<?php echo $this->escape($displayData['requiredText']); ?></span>
		<?php endif; ?>
		
		<?php if(JFactory::getApplication()->isClient('site') && $displayData['description'] != '') : ?>
			&nbsp;<?php echo JHtml::tooltip($displayData['description'], $displayData['title']); ?>
		<?php endif; ?>
	</td>
</tr>

<?php if($field->settings->get('eml_show_display_as')) : ?>
<tr>
	<td><?php echo Jtext::_('COM_FORM2CONTENT_DISPLAY_AS'); ?>:</td>
	<td>		
		<input type="text" <?php echo $displayData['attribsDisplayAs']; ?> name="<?php echo $field->elementId; ?>_display" 
		 id="<?php echo $field->elementId; ?>_display"
		 value="<?php echo $this->escape($field->values['DISPLAY_AS']); ?>"	size= "40" maxlength= "100">
	</td>		 		 
</tr>
<?php endif; ?>
</table>

<?php if(!$field->settings->get('eml_show_display_as')) : ?>
	<input type="hidden" name="<?php echo $field->elementId ?>_display" id="<?php echo $field->elementId ?>_display" value="<?php echo $this->escape($field->values['DISPLAY_AS']); ?>" >
<?php endif; ?>

<input type="hidden" name="hid<?php echo $field->elementId ?>" id="hid<?php echo $field->elementId ?>" value="<?php echo $this->escape($field->internal['fieldcontentid']); ?>" >