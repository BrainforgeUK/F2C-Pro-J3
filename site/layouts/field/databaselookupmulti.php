<?php
defined('JPATH_BASE') or die;

$field 		= $displayData['field'];
$rowcount 	= 0;
$imgPath 	= JURI::root(true).'/media/com_form2content/images/';

JHtml::_('bootstrap.framework');
?>
<p>Envelope icon: <span class="glyphicon glyphicon-envelope"></span></p>
<table>
<tr>
	<td>
		<table <?php echo $displayData['attributesTable']; ?> id="<?php echo $field->elementId; ?>" cellspacing="0" cellpadding="0">		
			<tr <?php echo $field->settings->get('dlm_attributes_tr'); ?>>
				<th <?php echo $field->settings->get('dlm_attributes_th'); ?> style="width:200px;"><?php echo Jtext::_('COM_FORM2CONTENT_LIST_ITEM'); ?></th>
				<th <?php echo $field->settings->get('dlm_attributes_th'); ?>></th>			
			</tr>
			<?php 
			if(count($field->values['VALUE']))
			{
				foreach($field->values['VALUE'] as $value)
				{
					$rowId = $field->elementId.'_'.$rowcount;
					$rowcount++;
					
					if(array_key_exists($value, $displayData['rowList']))
					{
						?>
						<tr id="<?php echo $rowId; ?>" <?php echo $field->settings->get('dlm_attributes_tr');?>>
							<td <?php echo $field->settings->get('dlm_attributes_td');?>>
								<input type="hidden" name="<?php echo $field->elementId; ?>RowKey[]" value="<?php echo $rowId; ?>"/>
								<input type="hidden" id="<?php echo $rowId; ?>val" name="<?php echo $rowId; ?>val" value="<?php echo $this->escape($value); ?>" />
								<?php echo $this->escape($displayData['rowList'][$value][1]);?>
							</td>
							<td <?php echo $field->settings->get('dlm_attributes_td');?>>
								<a href="javascript:moveUp('<?php echo $field->elementId; ?>', '<?php echo $rowId; ?>');"><i class="icon-f2carrow-up f2c_row_button" title="<?php echo JText::_('COM_FORM2CONTENT_UP');?>"></i></a>
								<a href="javascript:moveDown('<?php echo $field->elementId; ?>', '<?php echo $rowId; ?>');"><i class="icon-f2carrow-down f2c_row_button" title="<?php echo JText::_('COM_FORM2CONTENT_DOWN');?>"></i></a>
								<a href="javascript:removeRow('<?php echo $rowId; ?>');"><i class="icon-f2cminus f2c_row_button" title="<?php echo JText::_('COM_FORM2CONTENT_DELETE');?>"></i></a>
							</td>
						</tr>
						<?php
					}
				}
			}
			?>
		</table>
		<br/>							
	</td>
	<td valign="top">
		<?php if(JFactory::getApplication()->isClient('site') && $displayData['requiredText'] != '') : ?>
			<span class="f2c_required">&nbsp;<?php echo $this->escape($displayData['requiredText']); ?></span>
		<?php endif; ?>
	
		<?php if(JFactory::getApplication()->isClient('site') && $displayData['description'] != '') : ?>
			&nbsp;<?php echo JHtml::tooltip($displayData['description'], $displayData['title']); ?>
		<?php endif; ?>
	</td>
</tr>
</table>
<?php echo JHTMLSelect::genericlist($displayData['listOptions'], $field->elementId.'_lookup', $field->settings->get('dlm_attributes_select'), 'value', 'text', ''); ?>&nbsp;
<input type="button" value="<?php echo Jtext::_('COM_FORM2CONTENT_ADD'); ?>" <?php echo $field->settings->get('dlm_attributes_add_button'); ?> onclick="addDbLookupkMultiRow('<?php echo $field->elementId;?>','');" class="btn">
<input type="hidden" name="<?php echo $field->elementId; ?>MaxKey" id="<?php echo $field->elementId; ?>MaxKey" value="<?php echo $rowcount; ?>">
<input type="hidden" name="hid<?php echo $field->elementId ?>" id="hid<?php echo $field->elementId; ?>" value="<?php echo $field->id; ?>">
