<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'formsbase.php');

class Form2ContentControllerTranslations extends JControllerAdmin
{
	protected $default_view = 'translations';

	public function __construct($config = array())
	{
		// Access check.
		if (!JFactory::getUser()->authorise('core.admin')) 
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
		}
				
		parent::__construct($config);
	}

	public function &getModel($name = 'Translation', $prefix = 'Form2ContentModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
}

?>