<?php
defined('JPATH_BASE') or die;

$field = $displayData['field'];
?>
<div id="<?php echo $field->elementId ?>_colorpicker" class="f2ccolorpicker">
	<i class="icon-edit f2ccolorpickericon"></i>
</div>
		
<?php if($field->settings->get('show_hex_value', true)) : ?>
	<div class="f2ccolorpicker_hexvalue">
		<div class="valign" id="<?php echo $field->elementId ?>_hexvalue"><?php echo ($displayData['color'] ? '#' : '').$displayData['color']; ?></div>
	</div>
<?php endif; ?>

<?php if(JFactory::getApplication()->isClient('site') && $displayData['requiredText'] != '') : ?>
	<span class="f2c_required">&nbsp;<?php echo $this->escape($displayData['requiredText']); ?></span>
<?php endif; ?>

<?php if(JFactory::getApplication()->isClient('site') && $displayData['description'] != '') : ?>
	&nbsp;<?php echo JHtml::tooltip($displayData['description'], $displayData['title']); ?>
<?php endif; ?>
		
<input type="hidden" name="<?php echo $field->elementId ?>" id="<?php echo $field->elementId ?>" value="<?php echo $displayData['color']; ?>" >
<input type="hidden" name="hid<?php echo $field->elementId ?>" id="hid<?php echo $field->elementId ?>" value="<?php echo $this->escape($field->internal['fieldcontentid']); ?>" >