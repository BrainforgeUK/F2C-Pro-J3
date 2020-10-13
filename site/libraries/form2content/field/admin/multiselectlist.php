<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminMultiSelectList extends F2cFieldAdminBase
{
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><label id="tblMultiSelectKvp-lbl" for="tblMultiSelectKvp"><?php echo JText::_('COM_FORM2CONTENT_OPTIONS'); ?></label></div>
			<div class="controls">
				<table border="1" id="tblMultiSelectKvp" cellspacing="0" cellpadding="0">
				<tr>
					<th style="width:120px;"><?php echo JText::_('COM_FORM2CONTENT_OPTION_VALUE'); ?></th>
					<th style="width:200px;"><?php echo JText::_('COM_FORM2CONTENT_OPTION_DISPLAY_TEXT'); ?></th>
					<th></th>	
					
				</tr>
				<?php							
				$rowcount = 0;
				$options = array();
				
				if(array_key_exists('msl_options', $item->settings))
				{
					$options = $item->settings['msl_options'];
				}
				
				if(count($options))
				{															
					foreach($options as $key => $value)
					{
						$rowId = 'tblMultiSelectKvp_' . $rowcount;								
						$rowcount++;
						echo '<tr id="'.$rowId.'">';
						echo '<td><input type="text" id="'.$rowId.'key" name="'.$rowId.'key" size="20" value="' . $key . '" maxlength="20" /><input type="hidden" name="tblMultiSelectKvpRowKey[]" value="'.$rowId.'"/></td>';
						echo '<td><input type="text" id="'.$rowId.'val" name="'.$rowId.'val" size="40" value="' . htmlspecialchars($value) . '" /></td>';
						echo '<td>';
						echo '<a href="javascript:moveUp(\'tblMultiSelectKvp\',\''.$rowId.'\');"><i class="icon-arrow-up-3 f2c_row_button" title="' . JText::_('COM_FORM2CONTENT_UP') . '"></i></a>';
						echo '<a href="javascript:moveDown(\'tblMultiSelectKvp\',\''.$rowId.'\');"><i class="icon-arrow-down-3 f2c_row_button" title="' . JText::_('COM_FORM2CONTENT_DOWN') . '"></i></a>';
						echo '<a href="javascript:removeRow(\''.$rowId.'\');"><i class="icon-minus f2c_row_button" title="' . JText::_('COM_FORM2CONTENT_DELETE') . '"></i></a>';
						echo '<a href="javascript:addRow(\'tblMultiSelectKvp\',\''.$rowId.'\',\'prepareRowSelectList\');"><i class="icon-plus f2c_row_button" title="' . JText::_('COM_FORM2CONTENT_ADD') . '"></i></a>';
						echo '</td>';
						echo '</tr>';					
					}
				}					
				?>									
				</table>
				<br/>
				<input type="button" value="<?php echo JText::_('COM_FORM2CONTENT_ADD_SELECT_OPTION'); ?>" onclick="addRow('tblMultiSelectKvp','','prepareRowSelectList');" class="btn" />
				<input type="hidden" name="tblMultiSelectKvpMaxKey" id="tblMultiSelectKvpMaxKey" value="<?php echo $rowcount; ?>"/>
			</div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('msl_attributes', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('msl_attributes', 'settings'); ?></div>
		</div>
		<p><strong><?php echo JText::_('COM_FORM2CONTENT_MULTISELECTLIST_CUSTOM_RENDERING'); ?></strong></p>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('msl_pre_list_tag', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('msl_pre_list_tag', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('msl_post_list_tag', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('msl_post_list_tag', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('msl_pre_element_tag', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('msl_pre_element_tag', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('msl_post_element_tag', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('msl_post_element_tag', 'settings'); ?></div>
		</div>			
		<?php
	}
	
	public function clientSideValidation($view)
	{
		?>
		var count = 0;
		var optionKeys = new Array();
		var tbl = document.getElementById('tblMultiSelectKvp');
		
		for(i=1;i<=tbl.rows.length-1;i++)
		{
			var row = tbl.rows[i];
			var key = document.getElementById(row.id+'key').value;
			var val = document.getElementById(row.id+'val').value;
			
			if(key == '')
			{
				if(val == '')
				{
					continue;
				}
				else
				{
					alert('<?php echo $view->escape(JText::_('COM_FORM2CONTENT_ERROR_OPTION_VALUE_EMPTY', true)); ?>');
					return false;
				}
			}
			else
			{
				var re = new RegExp('^[A-Za-z0-9_]+$');
				var result = key.match(re);

				if (result == null)
				{
					alert('<?php echo $view->escape(JText::_('COM_FORM2CONTENT_ERROR_OPTION_VALUE_INVALID_CHARS', true)); ?>');
					return false;
				}
				
				if(optionKeys.contains(key))
				{
					alert('<?php echo $view->escape(JText::_('COM_FORM2CONTENT_ERROR_OPTION_VALUE_DUPLICATE', true)); ?> ' + key);
					return false;
				}
				
				optionKeys.push(key);
				count++;							
			}
		}
		
		if(count == 0)
		{
			alert('<?php echo $view->escape(JText::_('COM_FORM2CONTENT_ERROR_OPTION_VALUE_AT_LEAST_ONE', true)); ?>');
			return false;
		}		
		<?php
	}	

	public function prepareSave(&$data, $useRequestData)
	{
		if($useRequestData)
		{
			$tmpData 									= JFactory::getApplication()->input->get('jform', array(), 'array');
			$data['settings']['msl_options'] 			= $this->getOptionsArray('tblMultiSelectKvpRowKey', true);
			
			// Remove $_POST when RAW is supported
			$data['settings']['msl_pre_list_tag'] 		= $_POST['jform']['settings']['msl_pre_list_tag'];
			$data['settings']['msl_post_list_tag'] 		= $_POST['jform']['settings']['msl_post_list_tag'];
			$data['settings']['msl_pre_element_tag'] 	= $_POST['jform']['settings']['msl_pre_element_tag'];					
			$data['settings']['msl_post_element_tag'] 	= $_POST['jform']['settings']['msl_post_element_tag'];
		}
	}
	
	public function getTemplateSample($fieldname)
	{
      	return $fieldname.': <ul>{$'.strtoupper($fieldname)."}</ul>\n";
	}
}
?>