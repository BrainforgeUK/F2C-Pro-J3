<?php
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldF2cTemplate extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'F2cTemplate';

	protected function getInput()
	{
		$app 	= JFactory::getApplication();
		$link	= '';
		$layoutFile = 'form.field.template';
		
		if($app->isClient('administrator'))
		{
			$link .= JURI::root();
		}
		
		$link .= 'index.php?option=com_form2content&amp;task=templates.select&amp;view=templates&amp;layout=modal&amp;tmpl=component&amp;field='.$this->id;

		JHtml::script('com_form2content/form/field/template.js', array('relative' => true));

		// Modified Brainforge.uk 20250511
		if (strtolower($this->element['classiclayout'] ?? '') == 'true')
		{
			$layoutFile .= '_classic';
		}

    	$modal_params = array(); 
	 	$modal_params['height'] = "600px";
    	$modal_params['width'] = "480px";	
    	$modal_params['url'] = $link;
    	$modal_params['title'] = JText::_('COM_FORM2CONTENT_SELECT_TEMPLATE');
    	
    	// Initialize some field attributes.
		$attr = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		$displayData 					= array();
		$displayData['id'] 				= $this->id;
		$displayData['value'] 			= $this->value;
		$displayData['name'] 			= $this->name;
		$displayData['attribute'] 		= $attr;
		$displayData['readonly'] 		= $this->element['readonly'];
		$displayData['modal_params'] 	= $modal_params;
		
		$layout = new JLayoutFile($layoutFile, JPATH_SITE.'/components/com_form2content/layouts');
		
		return $layout->render($displayData);				
	}
}
?>
