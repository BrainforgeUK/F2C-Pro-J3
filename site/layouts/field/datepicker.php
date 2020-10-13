<?php
defined('JPATH_BASE') or die;

$field = $displayData['field'];
$form =	$displayData['form'];

$form->setFieldAttribute($field->elementId, 'format', F2cFactory::getConfig()->get('date_format'));
$form->setFieldAttribute($field->elementId, 'class', $displayData['attributes']);
$form->setValue($field->elementId, null, $displayData['displayValue']);

echo $form->getInput($field->elementId);
?>

<?php if(JFactory::getApplication()->isClient('site') && $displayData['requiredText'] != '') : ?>
	<span class="f2c_required">&nbsp;<?php echo $this->escape($displayData['requiredText']); ?></span>
<?php endif; ?>

<?php if(JFactory::getApplication()->isClient('site') && $displayData['description'] != '') : ?>
	&nbsp;<?php echo JHtml::tooltip($displayData['description'], $displayData['title']); ?>
<?php endif; ?>

<input type="hidden" name="hid<?php echo $field->elementId ?>" id="hid<?php echo $field->elementId ?>" value="<?php echo $field->internal['fieldcontentid']; ?>" >