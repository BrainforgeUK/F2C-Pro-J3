<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminGeocoder extends F2cFieldAdminBase
{
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('api_key', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('api_key', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('gcd_show_map', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('gcd_show_map', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('gcd_map_width', 'settings'); ?></div>
			<div class="controls">
				<?php echo $form->getInput('gcd_map_width', 'settings'); ?> X 
				<?php echo $form->getInput('gcd_map_height', 'settings'); ?> 
				<?php echo JText::_('COM_FORM2CONTENT_PIXELS'); ?> (<?php echo JText::_('COM_FORM2CONTENT_WIDTH_X_HEIGHT'); ?>)
			</div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('gcd_map_lat', 'settings'); ?></div>
			<div class="controls">
				<?php echo $form->getInput('gcd_map_lat', 'settings'); ?>&nbsp;,&nbsp;
				<?php echo $form->getInput('gcd_map_lon', 'settings'); ?> 
			</div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('gcd_map_zoom', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('gcd_map_zoom', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('gcd_map_type', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('gcd_map_type', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('gcd_attributes_address', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('gcd_attributes_address', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('gcd_attributes_lookup_lat_lon', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('gcd_attributes_lookup_lat_lon', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('gcd_attributes_clear_results', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('gcd_attributes_clear_results', 'settings'); ?></div>
		</div>			
		<?php
	}
	
	public function clientSideValidation($view)
	{
		?>
		var reWholeNumber = new RegExp('^\\d+$');
		var reLatLon = new RegExp('^-*\\d{1,3}\\.\\d{1,7}$');
		
		if(document.getElementById('jform_settings_gcd_map_width').value != '' && 
		   	!document.getElementById('jform_settings_gcd_map_width').value.match(reWholeNumber))
		{
			alert('<?php echo $view->escape(JText::_('COM_FORM2CONTENT_ERROR_MAP_WIDTH_INVALID_VALUE', true)); ?>');
			return false;
		}
	
		if(document.getElementById('jform_settings_gcd_map_height').value != '' &&
			!document.getElementById('jform_settings_gcd_map_height').value.match(reWholeNumber))
		{
			alert('<?php echo $view->escape(JText::_('COM_FORM2CONTENT_ERROR_MAP_HEIGHT_INVALID_VALUE', true)); ?>');
			return false;
		}

		if(document.getElementById('jform_settings_gcd_map_lat').value != '' &&
			!document.getElementById('jform_settings_gcd_map_lat').value.match(reLatLon))
		{
			alert('<?php echo $view->escape(JText::_('COM_FORM2CONTENT_ERROR_MAP_LAT_INVALID_VALUE', true)); ?>');
			return false;
		}

		if(document.getElementById('jform_settings_gcd_map_lon').value != '' &&
			!document.getElementById('jform_settings_gcd_map_lon').value.match(reLatLon))
		{
			alert('<?php echo $view->escape(JText::_('COM_FORM2CONTENT_ERROR_MAP_LON_INVALID_VALUE', true)); ?>');
			return false;
		}
		<?php
	}
	
	public function getTemplateSample($fieldname)
	{
		$template = $fieldname.' (addres): {$'.strtoupper($fieldname)."_ADDRESS}<br/>\n";			
	    $template .= $fieldname.' (latitude): {$'.strtoupper($fieldname)."_LAT}<br/>\n";			
	    $template .= $fieldname.' (longitude): {$'.strtoupper($fieldname)."_LON}\n";			
		return $template;		
	}
}
?>