<?php
defined('JPATH_BASE') or die;

$field = $displayData['field'];
?>
<table>
<?php if(JFactory::getApplication()->isClient('site') && ($displayData['description'] != '' || $displayData['requiredText'] != '')) : ?>
	<tr>
		<td>
			<?php if($displayData['description'] != '') : ?>
				&nbsp;<?php echo JHtml::tooltip($displayData['description'], $displayData['title']); ?>
			<?php endif; ?>
			
			<?php if($displayData['requiredText'] != '') : ?>
				<span class="f2c_required">&nbsp;<?php echo $this->escape($displayData['requiredText']); ?></span>
			<?php endif; ?>
		</td>
	</tr>
<?php endif; ?>
<tr>
	<td>
		<?php 
		foreach((array)$field->settings->get('msl_options') as $optionKey => $optionValue)
		{
			?>
			<div class="checkbox_wrapper">
				<input type=checkbox name="<?php echo $field->elementId; ?>[]" value="<?php echo $this->escape($optionKey); ?>" <?php echo $field->settings->get('msl_attributes'); ?>
				<?php if(array_key_exists($optionKey, $displayData['valueList'])) : ?>
				 checked
				 <?php endif; ?>>
				<div class="checkbox_label"><?php echo $this->escape($optionValue); ?></div>
			</div>
			<div style="clear:both;"></div>
			<?php 
		}
		?>
	</td>
</tr>
</table>

<input type="hidden" name="hid<?php echo $field->elementId ?>" id="hid<?php echo $field->elementId; ?>" value="<?php echo $field->id; ?>">