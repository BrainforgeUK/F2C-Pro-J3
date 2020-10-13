<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

/**
 * Form2Content implementation of a color picker field.
 * This field is based upon the evol.colorpicker
 * The color picker package is located in components/com_form2content/libraries/evol.colorpicker
 * Online it can be found at http://evoluteur.github.io/colorpicker/
 * 
 * @package     Joomla.Site
 * @subpackage  com_form2content
 * @since       6.8.0
 */
class F2cFieldColorPicker extends F2cFieldBase
{
	/**
	 * The constructor creates the field datastructure and resets its values to the default values.
	 * Since Color Picker is simple field having only one stored value, we can use the base reset function.
	 *
	 * @param	object		$field		Field object as created from the database information
	 * 
	 * @return  void
	 * 
	 * @since   6.8.0
	 */
	function __construct($field)
	{
		$this->reset();
		parent::__construct($field);
	}
	
	/**
	 * The prefix is unique value that can be used to prefix field settings that share this same setting
	 * across multiple custom fields.
	 *
	 * @return  string	The Prefix for the field
	 * 
	 * @since   6.8.0
	 */
	public function getPrefix()
	{
		return 'col';
	}
	
	/**
	 * This function builds the client-side Javascript that will be used to initialize the field.
	 * Sometimes initialization is shared between multiple fields of the same type, while other
	 * initialization code will run for each field separately.
	 *
	 * @param	int		$validationCounter	Counter that must be increased on every call of this function
	 * 
	 * @return  string	Generated script
	 * 
	 * @since   6.8.0
	 */
	public function getClientSideInitializationScript()
	{
		// Flag to indicate whether initialization took place
		static $initialized = false;
		
		$script = '';
		
		if(!$initialized)
		{
			parent::getClientSideInitializationScript();
			/*
			 * Initializtion code within this if statement will execute only once for all the
			 * possible color pickers on the form.
			 */ 
			JHtml::_('jquery.framework');
			JHtml::script('components/com_form2content/libraries/colpick/js/colpick.js');
			JHtml::stylesheet('components/com_form2content/libraries/colpick/css/colpick.css');
		
			$initialized = true;
		}
		
		// Get the current or default color (when there's no current color)
		$color = empty($this->values['VALUE']) ? $this->settings->get('default_value', 'ffffff') : $this->values['VALUE'];
		// get the color scheme from the field settings
		$colorScheme =	$this->settings->get('color_scheme');
		
		/*
		 *  jQuery script to initialize the Color Picker field. Each Color Picker on the form
		 *  will have its own intialization script.
		 */ 
		$script = "jQuery(document).ready(function()
					{
						jQuery('#".$this->elementId."_colorpicker').colpick(
						{
							layout:'rgbhex',
							color:'$color',
							colorScheme:'$colorScheme',
							onSubmit:function(hsb,hex,rgb,el) 
							{
								jQuery(el).css('background-color', '#'+hex);
								jQuery(el).colpickHide();
								jQuery('#$this->elementId').val(hex);
								var hexValue = jQuery('#".$this->elementId."_hexvalue');
								if(hexValue){ hexValue.text('#'+hex); }
							}
						})
						.css('background-color', '#$color');
					});\n";
		
		return $script;
	}
	
	/**
	 * This function will generate the HTML for the custom field on the F2C Article form
	 *
	 * @param	array		$translatedFields		Array of field translations
	 * @param	array		$contentTypeSettings	Array containing settings for the Content Type
	 * @param	array		$parms					Array with additional parameters
	 * @param	JForm		$form					Form object
	 * @param	int			$formId					Id of the current form
	 * 
	 * @return  string		HTML containing the rendered field
	 * 
	 * @since   6.8.0
	 */
	public function render($translatedFields, $contentTypeSettings, $parms = array(), $form, $formId)
	{
		$displayData			= array();
		$displayData['color']	= empty($this->values['VALUE']) ? $this->settings->get('default_value') : $this->values['VALUE'];
		
		return $this->renderLayout('colorpicker', $displayData, $translatedFields, $contentTypeSettings);						
	}
	
	/**
	 * Method to convert the submitted (post) data into the internal field data structure.
	 *
	 * @param	int			$formId			Id of the current form
	 * 
	 * @return  void
	 * 
	 * @since   6.8.0
	 */
	public function prepareSubmittedData($formId)
	{
		$jinput = JFactory::getApplication()->input;
		
		$this->internal['fieldcontentid'] 	= $jinput->getInt('hid'.$this->elementId);
		$this->values['VALUE'] 				= $jinput->getString($this->elementId);
		return $this;
	}
		
	/**
	 * Method to create an array of F2cFieldHelperContent objects to pass to the storage engine.
	 *
	 * @param	int			$formId			Id of the current form
	 * 
	 * @return  array		Array of F2cFieldHelperContent objects
	 * 
	 * @since   6.8.0
	 */
	public function store($formid)
	{
		$content 	= array();
		$value 		= isset($this->values['VALUE']) ? $this->values['VALUE'] : '';
		$fieldId 	= $this->internal['fieldcontentid'];
		$action 	= ($value != '') ? (($fieldId) ? 'UPDATE' : 'INSERT') : (($fieldId) ? 'DELETE' : '');
		$content[] 	= new F2cFieldHelperContent($fieldId, 'VALUE', $value, $action);

		return $content;		
	}
		
	/**
	 * Method to validate the field data. Throws an Exception when validation fails.
	 *
	 * @return  void
	 * 
	 * @since   6.8.0
	 */
	public function validate(&$data, $item)
	{
		$value = trim($this->values['VALUE']);
		
		// Check if this is a required field and if so, the user did not select a color
		if($this->settings->get('requiredfield') && $value === '')
		{
			throw new Exception($this->getRequiredFieldErrorMessage());
		}

		if($value)
		{
			// Test for a valid color, ranging from 000000 to ffffff
			if(!preg_match('/^[0-9a-fA-F]{6}$/', $value))
			{
				throw new Exception(sprintf(JText::_('COM_FORM2CONTENT_ERROR_COLOR_VALIDATION'), $value));
			}
		}
	}
		
	/**
	 * Method to generate client-side script to validate the field
	 *
	 * @param	int		$validationCounter	Counter that must be increased on every call of this function
	 * 
	 * @return  string	Generated script
	 * 
	 * @since   6.8.0
	 */
	public function getClientSideValidationScript(&$validationCounter, $form)
	{
		$script = '';
		
		$script .= parent::getClientSideValidationScript($validationCounter, $form);
		
		// Get the value for the color picker field
		$script .= 'var val'.$this->elementId.'=jQuery(\'#'.$this->elementId.'\').val().trim();';

		// Check if the field contains a valid color (range 000000 - fffff)
		$msg = JText::_('COM_FORM2CONTENT_ERROR_COLOR_VALIDATION', true);
		$script .= 'if(val'.$this->elementId.'!=\'\'){Form2Content.Validation.CheckPatternField(\''.$this->elementId.'\',\'^[0-9a-fA-F]{6}$\',\''.$msg.'\'.replace(\'%s\', val'.$this->elementId.'));}';
		
		return $script;
	}
	
	/**
	 * Method to create an Export XML node based upon the field data.
	 *
	 * @param	object		$xmlFields				XML node to append to
	 * @param	int			$formId					Id of the current form
	 * 
	 * @return  void
	 * 
	 * @since   6.8.0
	 */
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
      	$xmlFieldContent->value = $this->values['VALUE'];
	}
	
	/**
	 * Method to fill the internal field data based on an XML import node.
	 *
	 * @param	object		$xmlField				XML node containing the data
	 * @param	object		$existingInternalData	Actual values for the field as present in the database
	 * @param	int			$formId					Id of the current form
	 * 
	 * @return  void
	 * 
	 * @since   6.8.0
	 */
	public function import($xmlField, $existingInternalData, &$data)
	{
		$this->values['VALUE'] = (string)$xmlField->contentSingleTextValue->value;
		$this->internal['fieldcontentid'] = $data['id'] ? $existingInternalData['fieldcontentid'] : 0;
	}	
	
	/**
	 * Method to add field specific template parameters.
	 *
	 * @param	object		$smarty		Template engine object
	 * @param	JForm		$form		Form object
	 * 
	 * @return  void
	 * 
	 * @since   6.8.0
	 */
	public function addTemplateVar($templateEngine, $form)
	{
		$templateEngine->addVar($this->fieldname, $this->values['VALUE']);
		
		if($this->values['VALUE'])
		{
			$templateEngine->addVar($this->fieldname.'_RED', StringHelper::substr($this->values['VALUE'], 0, 2));
			$templateEngine->addVar($this->fieldname.'_GREEN', StringHelper::substr($this->values['VALUE'], 2, 2));
			$templateEngine->addVar($this->fieldname.'_BLUE', StringHelper::substr($this->values['VALUE'], 4, 2));
		}
		else 
		{
			$templateEngine->addVar($this->fieldname.'_RED', '');
			$templateEngine->addVar($this->fieldname.'_GREEN', '');
			$templateEngine->addVar($this->fieldname.'_BLUE', '');
		}		
	}
	
	public function getTemplateParameterNames()
	{
		return array(StringHelper::strtoupper($this->fieldname).'_RED', 
					 StringHelper::strtoupper($this->fieldname).'_GREEN',
					 StringHelper::strtoupper($this->fieldname).'_BLUE');
	}
	
	/**
	 * Method to fill the field data structure from an external data structure. 
	 *
	 * @param	object		$data	Data structure containing the form data
	 * 
	 * @return  void
	 * 
	 * @since   6.8.0
	 */
	public function setData($data)
	{
		if($data->attribute)
		{
			$this->values[$data->attribute] 	= $data->content;
			$this->internal['fieldcontentid'] 	= $data->fieldcontentid;
		}
	}
	
	public function canBeHiddenInFrontEnd()
	{
		if($this->settings->get('requiredfield'))
		{
			// when required, a default should be provided
			if($this->settings->get('default_value', '') == '')
			{
				return false;
			}
		}
		
		return true;
	}
}
?>