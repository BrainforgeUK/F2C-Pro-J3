<?php
defined('JPATH_BASE') or die;

$field = $displayData['field'];
?>
<?php if($field->settings->get('gcd_show_map')) : ?>
	<div id="<?php echo $field->elementId ?>_map_canvas" class="f2c_field_geocoder" style="width: <?php echo $field->settings->get('gcd_map_width', '350'); ?>px; height: <?php echo $field->settings->get('gcd_map_height', '350'); ?>px;"></div>
	<br/>
<?php endif; ?>

<table>
<tr>
	<td>
		<?php echo Jtext::_('COM_FORM2CONTENT_ADDRESS_OF_LOCATION'); ?>: 
	</td>
	<td>
		<input id="<?php echo $field->elementId ?>_address" name="<?php echo $field->elementId ?>_address" type="text" <?php echo $displayData['attributesAddress']; ?> value="<?php echo $this->escape($field->values['ADDRESS']); ?>" style="width:300px;">
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="button" id="<?php echo $field->elementId ?>_geocode" <?php echo $displayData['attributesLookup']; ?> value="<?php echo Jtext::_('COM_FORM2CONTENT_LOOKUP_LAT_LON');?>">
		&nbsp;
		<input type="button" <?php echo $displayData['attributesClear']; ?> value="<?php echo Jtext::_('COM_FORM2CONTENT_CLEAR_RESULTS');?>" onclick="Form2Content.Fields.Geocoder.ClearResults('<?php echo $field->elementId ?>');">
 		
		<?php if(JFactory::getApplication()->isClient('site') && $displayData['description'] != '') : ?>
			&nbsp;<?php echo JHtml::tooltip($displayData['description'], $displayData['title']); ?>
		<?php endif; ?>
		
		<?php if(JFactory::getApplication()->isClient('site') && $displayData['requiredText'] != '') : ?>
			<span class="f2c_required">&nbsp;<?php echo $this->escape($displayData['requiredText']); ?></span>
		<?php endif; ?> 		
	</td>
</tr>
<tr>
	<td><?php echo Jtext::_('COM_FORM2CONTENT_LAT_LON'); ?>: </td>
	<td>
		<span id="<?php echo $field->elementId ?>_latlon"><?php echo $displayData['latLonDisplay']; ?></span>
		<span id="<?php echo $field->elementId ?>_error" style="display: none;"><?php echo Jtext::_('COM_FORM2CONTENT_ERROR_GEOCODER_PROCESS'); ?></span>
	</td>
</tr>
</table>

<input type="hidden" name="hid<?php echo $field->elementId ?>_lat" id="hid<?php echo $field->elementId ?>_lat" value="<?php echo $field->internal['latid']; ?>" >
<input type="hidden" name="hid<?php echo $field->elementId ?>_lon" id="hid<?php echo $field->elementId ?>_lon" value="<?php echo $field->internal['lonid']; ?>" >
<input type="hidden" name="hid<?php echo $field->elementId ?>_address" id="hid<?php echo $field->elementId ?>_address" value="<?php echo $field->internal['addressid']; ?>" >
<input type="hidden" name="<?php echo $field->elementId ?>_hid_lat" id="<?php echo $field->elementId ?>_hid_lat" value="<?php echo $field->values['LAT']; ?>" > 
<input type="hidden" name="<?php echo $field->elementId ?>_hid_lon" id="<?php echo $field->elementId ?>_hid_lon" value="<?php echo $field->values['LON']; ?>" >  	