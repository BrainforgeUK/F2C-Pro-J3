<?php
defined('JPATH_BASE') or die;

$field = $displayData['field'];
?>
<input type="<?php echo $displayData['htmlInputType']; ?>" name="<?php echo $field->elementId ?>" id="<?php echo $field->elementId ?>" value="<?php echo $this->escape($field->values['VALUE'])?>" size="<?php echo $displayData['size']; ?>" <?php echo $displayData['class'].' '.$displayData['attributes']; ?> maxlength="<?php echo $displayData['maxLength']; ?>">

<?php if(JFactory::getApplication()->isClient('site') && $displayData['description'] != '') : ?>
	&nbsp;<?php echo JHtml::tooltip($displayData['description'], $displayData['title']); ?>
<?php endif; ?>

<?php if(JFactory::getApplication()->isClient('site') && $displayData['requiredText'] != '') : ?>
	<span class="f2c_required">&nbsp;<?php echo $this->escape($displayData['requiredText']); ?></span>
<?php endif; ?>

<input type="hidden" name="hid<?php echo $field->elementId ?>" id="hid<?php echo $field->elementId ?>" value="<?php echo $this->escape($field->internal['fieldcontentid']); ?>" >