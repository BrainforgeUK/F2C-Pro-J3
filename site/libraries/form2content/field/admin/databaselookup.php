<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminDatabaseLookup extends F2cFieldAdminBase
{
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dbl_display_mode', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dbl_display_mode', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dbl_show_empty_choice_text', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dbl_show_empty_choice_text', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dbl_empty_choice_text', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dbl_empty_choice_text', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dbl_query', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dbl_query', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('dbl_attributes', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('dbl_attributes', 'settings'); ?></div>
		</div>			
		<?php
	}
	
	public function clientSideValidation($view)
	{
		?>
		var fldDblQuery = document.getElementById('jform_settings_dbl_query');
		
		if(fldDblQuery.value.indexOf('*') != -1)
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
			$data['settings']['dbl_query'] = $tmpData['settings']['dbl_query'];
			
			if(empty($data['settings']['dbl_query']))
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_FORM2CONTENT_SQL_QUERY_EMPTY'), 'warning');
			}
		}
	}
	
	public function getTemplateSample($fieldname)
	{
      	$template = $fieldname.' (value): {$'.strtoupper($fieldname)."}<br/>\n";			
      	$template .= $fieldname.' (text): {$'.strtoupper($fieldname)."_TEXT}\n";
      	return $template;
	}
}
?>