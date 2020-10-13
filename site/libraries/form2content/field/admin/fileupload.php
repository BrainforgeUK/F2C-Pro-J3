<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminFileUpload extends F2cFieldAdminBase
{
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('ful_output_mode', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('ful_output_mode', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label">
				<?php
				$text 	= JText::_('COM_FORM2CONTENT_EXTENSIONS_WHITE_LIST');
				$desc 	= JText::_('COM_FORM2CONTENT_EXTENSIONS_WHITE_LIST_DESC');				 	
				$label 	= '';
				$label .= '<label id="tblFileWhiteList-lbl" for="tblFileWhiteList" class="hasPopover" title="" ';
				$label .= 'data-original-title="'.$text.'" data-content="'.$desc.'"';
				$label .= '>'.$text.'</label>';
				
				echo $label;
				?>
			</div>
			<div class="controls">
				<table border="1" id="tblFileWhiteList" cellspacing="0" cellpadding="0">
				<tr>
					<th style="width:120px;"><?php echo JText::_('COM_FORM2CONTENT_EXTENSION'); ?></th>
					<th></th>			
				</tr>
				<?php							
				$rowcount = 0;
				$whiteList = array();
				
				if(array_key_exists('ful_whitelist', $item->settings))
				{
					$whiteList = $item->settings['ful_whitelist'];
				}
				
				if(count($whiteList))
				{															
					foreach($whiteList as $key => $value)
					{
						$rowId = 'tblFileWhiteList_' . $rowcount;								
						$rowcount++;
						echo '<tr id="'.$rowId.'">';
						echo '<td>';
						//echo '<input type="hidden" name="'.$rowId.'key" value="'.$rowId.'"/>';
						echo '<input type="hidden" name="tblFileWhiteListRowKey[]" value="'.$rowId.'"/>';
				  		echo '<input type="text" id="'.$rowId.'key" name="'.$rowId.'key" size="20" value="' . htmlspecialchars($value) . '" maxlength="5" />';
						echo '</td>';									
						echo '<td>';
						echo '<a href="javascript:removeRow(\''.$rowId.'\');"><i class="icon-minus f2c_row_button" title="' . JText::_('COM_FORM2CONTENT_DELETE') . '"></i></a>';
						echo '<a href="javascript:addRow(\'tblFileWhiteList\',\''.$rowId.'\',\'prepareRowExtensionList\');"><i class="icon-plus f2c_row_button" title="' . JText::_('COM_FORM2CONTENT_ADD') . '"></a>';
						echo '</td>';
						echo '</tr>';									
					}
				}					
				?>									
				</table>							
				 <br/>
				<input type="button" value="<?php echo JText::_('COM_FORM2CONTENT_ADD_EXTENSION'); ?>" onclick="addRow('tblFileWhiteList','','prepareRowExtensionList');" class="btn" />
				<input type="hidden" name="tblFileWhiteListMaxKey" id="tblFileWhiteListMaxKey" value="<?php echo $rowcount; ?>"/>
			</div>
		</div>			
		<div class="control-group">
			<div class="control-label">
				<?php
				$text 	= JText::_('COM_FORM2CONTENT_EXTENSIONS_BLACK_LIST');
				$desc 	= JText::_('COM_FORM2CONTENT_EXTENSIONS_BLACK_LIST_DESC');				 	
				$label 	= '';
				$label .= '<label id="tblFileBlackList-lbl" for="tblFileBlackList" class="hasPopover" title="" ';
				$label .= 'data-original-title="'.$text.'" data-content="'.$desc.'"';
				$label .= '>'.$text.'</label>';
				
				echo $label;
				?>
			</div>
			<div class="controls">
				<table border="1" id="tblFileBlackList" cellspacing="0" celpadding="0">
				<tr>
					<th style="width:120px;"><?php echo JText::_('COM_FORM2CONTENT_EXTENSION'); ?></th>
					<th></th>			
				</tr>
				<?php							
				$rowcount = 0;
				$blackList = array();
				
				if(array_key_exists('ful_blacklist', $item->settings))
				{
					$blackList = $item->settings['ful_blacklist'];
				}
				
				if(count($blackList))
				{															
					foreach($blackList as $key=>$value)
					{
						$rowId = 'tblFileBlackList_' . $rowcount;								
						$rowcount++;
						echo '<tr id="'.$rowId.'">';
						echo '<td>';
						//echo '<input type="hidden" name="'.$rowId.'key" value="'.$rowId.'"/>';
						echo '<input type="hidden" name="tblFileBlackListRowKey[]" value="'.$rowId.'"/>';
				  		echo '<input type="text" id="'.$rowId.'key" name="'.$rowId.'key" size="20" value="' . htmlspecialchars($value) . '" maxlength="5" />';
						echo '</td>';									
						echo '<td>';
						echo '<a href="javascript:removeRow(\''.$rowId.'\');"><i class="icon-minus f2c_row_button" title="' . JText::_('COM_FORM2CONTENT_DELETE') . '"></i></a>';
						echo '<a href="javascript:addRow(\'tblFileBlackList\',\''.$rowId.'\',\'prepareRowExtensionList\');"><i class="icon-plus f2c_row_button" title="' . JText::_('COM_FORM2CONTENT_ADD') . '"></a>';
						echo '</td>';
						echo '</tr>';									
					}
				}					
				?>									
				</table>
				 <br/>
				<input type="button" value="<?php echo JText::_('COM_FORM2CONTENT_ADD_EXTENSION'); ?>" onclick="addRow('tblFileBlackList','','prepareRowExtensionList');" class="btn" />
				<input type="hidden" name="tblFileBlackListMaxKey" id="tblFileBlackListMaxKey" value="<?php echo $rowcount; ?>"/>
			</div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('ful_attributes_upload', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('ful_attributes_upload', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('ful_attributes_delete', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('ful_attributes_delete', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('ful_max_file_size', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('ful_max_file_size', 'settings'); ?></div>
		</div>			
		<?php
	}
	
	public function clientSideValidation($view)
	{
		?>
		var whiteList = new Array();
		var blackList = new Array();
		var tbl = document.getElementById('tblFileWhiteList');

		for(i=1;i<=tbl.rows.length-1;i++)
		{
			var row = tbl.rows[i];
			var key = document.getElementById(row.id+'key').value;

			if(key != '' && !(key in whiteList))
			{
				whiteList[key] = key;
			}
		}
		
		tbl = document.getElementById('tblFileBlackList');
		
		for(i=1;i<=tbl.rows.length-1;i++)
		{
			var row = tbl.rows[i];
			var key = document.getElementById(row.id+'key').value;
			
			if(key in whiteList)
			{
				alert('<?php echo $view->escape(JText::_('COM_FORM2CONTENT_ERROR_EXTENSION_IN_BOTH_LISTS', true)); ?>: ' + key);
				return false;
			}
		}							
		<?php
	}	
	
	public function prepareSave(&$data, $useRequestData)
	{
		if($useRequestData)
		{	
			$data['settings']['ful_whitelist'] = $this->getOptionsArray('tblFileWhiteListRowKey');
			$data['settings']['ful_blacklist'] = $this->getOptionsArray('tblFileBlackListRowKey');
		}
	}
	
	public function delete($id)
	{
		JLoader::register('F2cFieldFileUpload', JPATH_COMPONENT_SITE.'/libraries/form2content/field/fileupload.php');
		
		$db = JFactory::getDBO(); 

		$query 	= $db->getQuery(true);
		
		$query->select("pfl.projectid, fct.formid, fct.content")->from("#__f2c_projectfields pfl");
		$query->join("INNER", "#__f2c_fieldcontent fct ON pfl.id = fct.fieldid");
		$query->join("INNER", "#__f2c_fieldtype ftp ON pfl.fieldtypeid = ftp.id AND ftp.name = 'Fileupload'");
		$query->where("pfl.id = ".(int)$id);
		
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		for ($i=0, $n=count($rows); $i < $n; $i++) 
		{
	  		$row = &$rows[$i];
	  		$path = JPath::clean(Path::Combine(F2cFieldFileUpload::GetFilesRootPath(), "c$row->projectid/a$row->formid/f$id"), DIRECTORY_SEPARATOR);
	  		JFolder::delete($path);
		}					
	}
	
	public function getTemplateSample($fieldname)
	{
	   $template = $fieldname.' (url): {$'.strtoupper($fieldname)."}<br/>\n";			
	   $template .= $fieldname.' (filename): {$'.strtoupper($fieldname)."_FILENAME}\n";
	   return $template;		
	}
}
?>