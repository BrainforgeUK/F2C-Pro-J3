<?php
defined('JPATH_BASE') or die;

$field = $displayData['field'];

echo $displayData['editor']->display($field->elementId, htmlspecialchars($field->values['VALUE'], ENT_COMPAT, 'UTF-8'), $displayData['width'], $displayData['height'], $displayData['col'], $displayData['row']);
?>

<?php if(JFactory::getApplication()->isClient('site') && $displayData['requiredText'] != '') : ?>
	<span class="f2c_required">&nbsp;<?php echo $this->escape($displayData['requiredText']); ?></span>
<?php endif; ?>

<?php if(JFactory::getApplication()->isClient('site') && $displayData['description'] != '') : ?>
	&nbsp;<?php echo JHtml::tooltip($displayData['description'], $displayData['title']); ?>
<?php endif; ?>

<input type="hidden" name="hid<?php echo $field->elementId ?>" id="hid<?php echo $field->elementId ?>" value="<?php echo $this->escape($field->internal['fieldcontentid']); ?>" >