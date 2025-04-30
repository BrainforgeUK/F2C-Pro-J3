<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'formsbase.php');

class Form2ContentControllerForms extends Form2ContentControllerFormsBase
{
	public function display($cachable = false, $urlparams = false)
	{
		$document	= JFactory::getDocument();
		$viewType	= $document->getType();
		$viewName	= $this->input->getCmd('view', $this->default_view);
		$viewLayout = $this->input->getCmd('layout', 'default');
		
		$view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath));

		// Get/Create the model
		if ($model = $this->getModel($viewName, 'Form2ContentModel', array())) 
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}
		
		// Set the layout
		$view->setLayout($viewLayout);

		// Modified Brainforge.uk 2025/04/29
		//$view->assignRef('document', $document);
		$view->document = $document;

		$conf = JFactory::getConfig();

		// Display the view
		if ($cachable && $viewType != 'feed' && $conf->get('caching') >= 1) {
			$option	= $this->input->getCmd('option');
			$cache	= JFactory::getCache($option, 'view');

			if (is_array($urlparams)) {
				$app = JFactory::getApplication();

				$registeredurlparams = $app->get('registeredurlparams');

				if (empty($registeredurlparams)) {
					$registeredurlparams = new stdClass();
				}

				foreach ($urlparams AS $key => $value)
				{
					// add your safe url parameters with variable type as value {@see JFilterInput::clean()}.
					$registeredurlparams->$key = $value;
				}

				$app->set('registeredurlparams', $registeredurlparams);
			}

			$cache->get($view, 'display');

		}
		else {
			$view->display();
		}

		return $this;
	}

	/*
	 */
	public function setRedirect($url, $msg = null, $type = null)
	{
		$return = $this->input->getBase64('return');
		if (!empty($return)) {
			$url = base64_decode($return);
		}

		return parent::setRedirect($url, $msg, $type);
	}
}
?>