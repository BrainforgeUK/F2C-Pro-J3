<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use \Joomla\Registry\Registry;

/**
 * Form2Content Admin implementation of a color picker field.
 * 
 * This field is based upon the evol.colorpicker
 * The color picker package is located in components/com_form2content/libraries/evol.colorpicker
 * Online it can be found at http://evoluteur.github.io/colorpicker/
 * 
 * @package     Joomla.Site
 * @subpackage  com_form2content
 * @since       6.8.0
 */
class F2cFieldAdminColorPicker extends F2cFieldAdminBase
{
	/**
	 * The display function is used to display the fields specific to the Custom Field.
	 * The mandatory fields (like title, description, required, etc.) are displayed automatically.
	 * In this case for the Color Picker, the fields default_value and color_scheme are displayed.
	 * Since the field default_value is a Color Picker itself, intialization code is added to set-up
	 * this field correctly.
	 *
	 * @param   JForm 	$form 	the form definition object
	 * @param   object 	$item	the admin field object
	 *
	 * @return  string	Generated HTML
	 *
	 * @since   6.8.0
	 */
	function display($form, $item)
	{
		// Add libraries
		JHtml::script('components/com_form2content/libraries/colpick/js/colpick.js');
		JHtml::stylesheet('components/com_form2content/libraries/colpick/css/colpick.css');
		
		// Get the current or default color
		$settings 		= new Registry($item->settings);
		$defaultColor	= $settings->get('default_value', 'ffffff');		
		?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery('#jform_settings_default_value_colorpicker').colpick(
						{
							layout:'rgbhex',
							color:'<?php echo $defaultColor; ?>',
							colorScheme:'light',
							onSubmit:function(hsb,hex,rgb,el) 
							{
								jQuery(el).css('background-color', '#'+hex);
								jQuery(el).colpickHide();
								jQuery('#jform_settings_default_value').val(hex);
							}
						})
						.css('background-color', '#<?php echo $defaultColor; ?>');
			});	
		</script>
		<div class="control-group">
			<div class="control-label">
				<label id="jform_settings_default_value-lbl" class="hasTooltip" title="" 
					for="jform_settings_default_value" 
					data-original-title="&lt;strong&gt;<?php echo JText::_('COM_FORM2CONTENT_COLORPICKER_DEFAULT_VALUE', true); ?>&lt;/strong&gt;"> <?php echo JText::_('COM_FORM2CONTENT_COLORPICKER_DEFAULT_VALUE', true); ?></label>			
			</div>
			<div class="controls">
				<div id="jform_settings_default_value_colorpicker" class="f2ccolorpicker"><i class="icon-edit f2ccolorpickericon"></i></div>
				<?php echo $form->getInput('default_value', 'settings'); ?>
			</div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('color_scheme', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('color_scheme', 'settings'); ?></div>
		</div>		
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('show_hex_value', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('show_hex_value', 'settings'); ?></div>
		</div>		
		<p>&nbsp;</p><p>&nbsp;</p>	
		<?php
	}
}
?>