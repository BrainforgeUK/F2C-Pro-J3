<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'utils.form2content.php');

jimport('joomla.application.component.controller');

class Form2ContentControllerTemplate extends JControllerForm
{
	public function __construct($config = array())
	{
		// Access check.
		if (!JFactory::getUser()->authorise('core.admin')) 
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		parent::__construct($config);
	}
	
	function __getRedirect()
	{
		return 'index.php?option=com_form2content&view=templates';
	}

	function edit($key = null, $urlVar = null)
	{
		$app   		= JFactory::getApplication();
		$model 		= $this->getModel();
		$cid   		= $this->input->post->get('cid', array(), 'array');
		$context 	= "$this->option.edit.$this->context";
		$key		= 'id';

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}

		// Get the previous record id (if any) and the current record id.
		$recordId = (count($cid) ? $cid[0] : $this->input->getString($urlVar));

		// Push the new record id into the session.
		$app->setUserState($context . '.id', array($recordId));
		$app->setUserState($context . '.data', null);
		
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_item
				. $this->getRedirectToItemAppend($recordId, $urlVar), false
			)
		);

		return true;
	}
	
	function cancel($key = null)
	{
		$this->setRedirect($this->__getRedirect());
	}

	function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app   		= JFactory::getApplication();
		$lang  		= JFactory::getLanguage();
		$model 		= $this->getModel();
		$data  		= $this->input->post->get('jform', array(), 'array');
		$context 	= "$this->option.edit.$this->context";
		$task 		= $this->getTask();
		$key 		= 'id';

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}

		$recordId = $this->input->getString($urlVar);

		// Populate the row id from the session.
		$data[$key] = $recordId;

		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);

		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		// Test whether the data is valid.
		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState($context . '.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);

			return false;
		}

		if (!isset($validData['tags']))
		{
			$validData['tags'] = null;
		}

		// Attempt to save the data.
		if (!$model->save($validData))
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);

			return false;
		}

		$this->setMessage(
			JText::_(
				($lang->hasKey($this->text_prefix . ($recordId == 0 && $app->isClient('site') ? '_SUBMIT' : '') . '_SAVE_SUCCESS')
					? $this->text_prefix
					: 'JLIB_APPLICATION') . ($recordId == 0 && $app->isClient('site') ? '_SUBMIT' : '') . '_SAVE_SUCCESS'
			)
		);

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				// Set the record data in the session.
				$recordId = $model->getState($this->context . '.id');

				$app->setUserState($context . '.id', array($recordId));
				$app->setUserState($context . '.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_item
						. $this->getRedirectToItemAppend($recordId, $urlVar), false
					)
				);
				break;

			default:
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context . '.data', null);

				// Redirect to the list screen.
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_list
						. $this->getRedirectToListAppend(), false
					)
				);
				break;
		}

		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model, $validData);

		return true;
	}
	
	function delete()
	{
 		$model	= $this->getModel('Template'); 		
		$cid 	= $this->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1) 
		{
			throw new Exception(JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		}
		
		if($model->delete($cid))
		{
			$message = JText::_('COM_FORM2CONTENT_TEMPLATE_DELETE_SUCCESSFUL');
		}
		else
		{
			// Check for errors.
			if (count($errors = $model->getErrors())) 
			{
				JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'notice');
			}
			
			$message = JText::_('COM_FORM2CONTENT_ERRORS_OCCURRED');
		}
		
		$this->setRedirect($this->__getRedirect(), $message);
	}
	
	function upload()
	{
		$model = $this->getModel('Template');
		
		if($model->upload())
		{
			$message = JText::_('COM_FORM2CONTENT_TEMPLATE_UPLOAD_SUCCESSFUL');
		}
		else
		{
			// Check for errors.
			if (count($errors = $model->getErrors())) 
			{
				JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'notice');
			}
			
			$message = JText::_('COM_FORM2CONTENT_ERRORS_OCCURRED');
		}
		
		$this->setRedirect($this->__getRedirect(), $message);
	}
}
?>