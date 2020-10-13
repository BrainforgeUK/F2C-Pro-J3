<?php
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldF2cMultiSelect extends JFormField
{
	protected $type = 'F2cMultiSelect';

	private static $initialized = false;
	
	protected function getInput()
	{
		$html 		= array();
		$attr 		= '';
		$options 	= (array)$this->getOptions();
		
		// Load com_form2content admin language file for correct label translations
		JFactory::getLanguage()->load('com_form2content', JPATH_ADMINISTRATOR);		
		
		// Perform the javascript and css initialization. This code should only run once for multiple JFormFieldF2cMultiSelect controls
		if(!self::$initialized)
		{
			// Add the javascript library
			$document = JFactory::getDocument();
			
			JHtml::stylesheet(JURI::root().'/components/com_form2content/libraries/lou-multi-select/css/multi-select.css');
			JHtml::script(JURI::root().'/components/com_form2content/libraries/lou-multi-select/js/jquery.multi-select.js', true);
			
			$document->addScriptDeclaration("jQuery(document).ready(function(){jQuery('.lou-multiselect').multiSelect({keepOrder: true,});});");
			
			// Prevent this code from running again
			self::$initialized = true;
		}

		$document->addScriptDeclaration("jQuery(document).ready(function(){
			jQuery('#".$this->id."-select-all').click(function(){jQuery('#".$this->id."').multiSelect('select_all');return false;});
			jQuery('#".$this->id."-deselect-all').click(function(){jQuery('#".$this->id."').multiSelect('deselect_all');return false;});
			});");
		
		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : 'class="lou-multiselect"';
		$attr .= $this->element['style'] ? ' style="'.(string) $this->element['style'].'"' : '';
		
		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';
		
		
		$html[] = '<a href="#" id="'.$this->id.'-select-all" class="f2c-ms-selectall">'.JText::_('COM_FORM2CONTENT_SELECT_ALL').'</a>
				   <a href="#" id="'.$this->id.'-deselect-all" class="f2c-ms-deselectall">'.JText::_('COM_FORM2CONTENT_DESELECT_ALL').'</a>';
		$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);

		return implode($html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$reg = new JRegistry();		
		$reg->loadString($this->element['options']);
		$optionList = $reg->get('options');
		
		// Initialize variables.
		$options = array();

		foreach ($optionList as $key => $value) 
		{

			$tmp = JHtml::_('select.option', $key, $value, 'value', 'text');
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}