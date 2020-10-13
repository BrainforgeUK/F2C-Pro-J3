<?php
defined('JPATH_BASE') or die;

$field = $displayData['field'];

if((int)$field->settings->get('dbl_display_mode') == 0)
{
	echo JHTMLSelect::genericlist($displayData['listOptions'], $field->elementId, $field->settings->get('dbl_attributes'), 'value', 'text', $field->values['VALUE']);
}
else
{
	?>
	<fieldset class="radio">
		<?php echo JHTML::_('select.radiolist', $displayData['listOptions'], $field->elementId, $field->settings->get('dbl_attributes'), 'value', 'text', $field->values['VALUE']); ?>
	</fieldset>
	<?php 
}
?>

<?php if(JFactory::getApplication()->isClient('site') && $displayData['description'] != '') : ?>
	&nbsp;<?php echo JHtml::tooltip($displayData['description'], $displayData['title']); ?>
<?php endif; ?>

<?php if(JFactory::getApplication()->isClient('site') && $displayData['requiredText'] != '') : ?>
	<span class="f2c_required">&nbsp;<?php echo $this->escape($displayData['requiredText']); ?></span>
<?php endif; ?>

<input type="hidden" name="hid<?php echo $field->elementId ?>" id="hid<?php echo $field->elementId ?>" value="<?php echo $field->internal['fieldcontentid']; ?>">