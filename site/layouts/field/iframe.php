<?php
defined('JPATH_BASE') or die;

$field = $displayData['field'];
?>
<table>
<tr>
	<td><?php echo Jtext::_('COM_FORM2CONTENT_URL');?>:</td>
	<td>
		<input type="text" name="<?php echo $field->elementId ?>" id="<?php echo $field->elementId ?>" value="<?php echo $this->escape($field->values['URL'])?>" size="65" <?php echo $displayData['iFrameClass'].' '.$displayData['iFrameAttributes']; ?>>

		<?php if(JFactory::getApplication()->isClient('site') && $displayData['description'] != '') : ?>
			&nbsp;<?php echo JHtml::tooltip($displayData['description'], $displayData['title']); ?>
		<?php endif; ?>
		
		<?php if(JFactory::getApplication()->isClient('site') && $displayData['requiredText'] != '') : ?>
			<span class="f2c_required">&nbsp;<?php echo $this->escape($displayData['requiredText']); ?></span>
		<?php endif; ?>		
	</td>
</tr>
<tr>
	<td>
		<?php echo Jtext::_('COM_FORM2CONTENT_WIDTH');?>:
	</td>
	<td>
		<input type="text" name="<?php echo $field->elementId; ?>_width" id="<?php echo $field->elementId; ?>_width" value="<?php echo $this->escape($field->values['WIDTH'])?>" size="5" maxlength="4" <?php echo $displayData['widthClass'].' '.$displayData['widthAttributes']; ?>>
	</td>
</tr>
<tr>
	<td>
		<?php echo Jtext::_('COM_FORM2CONTENT_HEIGHT'); ?>:
	</td>
	<td>		      							
		<input type="text" name="<?php echo $field->elementId; ?>_height" id="<?php echo $field->elementId; ?>_height" value="<?php echo $this->escape($field->values['HEIGHT'])?>" size="5" maxlength="4" <?php echo $displayData['heightClass'].' '.$displayData['heightAttributes']; ?>>
	</td>		      							
</tr>
</table>

<input type="hidden" name="hid<?php echo $field->elementId ?>" id="hid<?php echo $field->elementId ?>" value="<?php echo $this->escape($field->internal['fieldcontentid']); ?>" >