<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminDatabaseLookupMulti extends F2cFieldAdminBase
{
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dlm_output_mode', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dlm_output_mode', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dlm_attributes_table', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dlm_attributes_table', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dlm_attributes_tr', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dlm_attributes_tr', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dlm_attributes_th', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dlm_attributes_th', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dlm_attributes_td', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dlm_attributes_td', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dlm_attributes_item_text', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dlm_attributes_item_text', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dlm_attributes_add_button', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dlm_attributes_add_button', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dlm_attributes_select', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dlm_attributes_select', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dlm_show_empty_choice_text', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dlm_show_empty_choice_text', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dlm_empty_choice_text', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dlm_empty_choice_text', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dlm_query', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dlm_query', 'settings'); ?></div>
		</div>			
		<?php
	}
	
	public function clientSideValidation($view)
	{
		?>
		var fldDlmQuery = document.getElementById('jform_settings_dlm_query');
		
		if(fldDlmQuery.value.indexOf('*') != -1)
		{
			alert('<?php echo $view->escape(JText::_('COM_FORM2CONTENT_ERROR_QUERY_ASTERISK_NOT_ALLOWED', true)); ?>');
			return false;			
		}				
		<?php
	}
	
	public function prepareSave(&$data, $useRequestData)
	{
		if($useRequestData)
		{
			$tmpData = JFactory::getApplication()->input->get('jform', array(), 'array');
			$data['settings']['dlm_query'] = $tmpData['settings']['dlm_query'];
			
			if(empty($data['settings']['dlm_query']))
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_FORM2CONTENT_SQL_QUERY_EMPTY'), 'warning');
			}
		}
	}
}
?>