<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.controlleradmin');

class Form2ContentControllerProjectFields extends JControllerAdmin
{
	protected $default_view = 'projectfields';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JControllerLegacy
	 * @since   1.6
	 * @throws  Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Define standard task mappings.

		// Value = 0
		$this->registerTask('setfrontinvisible', 'setfrontvisible');
	}
	
	public function &getModel($name = 'ProjectField', $prefix = 'Form2ContentModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
	
	public function reorder()
	{
		parent::reorder();
		$this->redirect .= '&projectid='.$this->input->getInt('projectid');
	
	}
	
	function saveorder()
	{
		parent::saveorder();
		$this->redirect .= '&projectid='.$this->input->getInt('projectid');	
	}
	
	function delete()
	{
		parent::delete();
		$this->redirect .= '&projectid='.$this->input->getInt('projectid');
	}
	
	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return	void
	 *
	 * @since   5.0.0
	 */
	public function saveOrderAjax()
	{
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		F2cBrainforgeukArrayhelper::toInteger($pks);
		F2cBrainforgeukArrayhelper::toInteger($order);

		// Get the model
		$model = $this->getModel();
		
		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
			JLog::add('SaveOrderAjax succeeded', JLog::INFO, 'com_form2content');
		}
		else 
		{
			JLog::add('SaveOrderAjax failed', JLog::INFO, 'com_form2content');
		}

		// Close the application
		JFactory::getApplication()->close();
	}
	
	/**
	 * Method to set the visibility of Content Type fields in the front-end
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function setfrontvisible()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid = $this->input->get('cid', array(), 'array');
		$data = array('setfrontvisible' => 1, 'setfrontinvisible' => 0);
		$task = $this->getTask();
		$value = ArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid))
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			JLog::add(sprintf('Changing visibility of %s front-end fields.', count($cid)), JLog::INFO, 'com_form2content');
			
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			$cid = ArrayHelper::toInteger($cid);

			// Set the visibility of the items.
			try
			{
				$model->setfrontvisible($cid, $value);
				// TODO: generate messages in case of multiple model actions
			}
			catch (Exception $e)
			{
				$this->setMessage($e->getMessage(), 'error');
			}
		}

		$extension = $this->input->get('extension');
		$extensionURL = ($extension) ? '&extension=' . $extension : '';
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));
		
		$this->redirect .= '&projectid='.$this->input->getInt('projectid');
	}
}
?>