<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'formsbase.php');

jimport('joomla.filesystem.folder');

class Form2ContentControllerForms extends Form2ContentControllerFormsBase
{
	function refresh()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid	= $this->input->get('cid', array(), 'array');

		if (empty($cid)) 
		{
			throw new Exception(JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		}
		else 
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);

			// Refresh the items -> publish with F2C_STATE_RETAIN retains the current state.
			if (!$model->publish($cid, F2C_STATE_RETAIN)) 
			{
				throw new Exception($model->getError());
			}
			else 
			{
				$this->setMessage(JText::plural($this->text_prefix.'_N_ITEMS_REFRESHED', count($cid)));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
	}
	
	function export()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to export from the request.
		$cid 		= $this->input->get('cid', array(), 'array');
		
		$exportDir	= F2cFactory::getConfig()->get('export_dir');
		$timestamp 	= new JDate();
		
		if(empty($cid)) 
		{
			throw new Exception(JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		}

		if(empty($exportDir))
		{
			throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_EXPORT_DIR_EMPTY'));
		}
		
		if(!JFolder::exists($exportDir))
		{
			throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_EXPORT_DIR_DOES_NOT_EXIST'));
		}
		
		// Create the export XML
		$xml= $this->getModel()->export($cid);
		
		$fileName = Path::Combine($exportDir, $timestamp->format('YmdHis').'_F2C_Export.xml');
		
		JFile::write($fileName, $xml->asXML());
		
		JFactory::getApplication()->enqueueMessage(JText::sprintf(JText::_('COM_FORM2CONTENT_ARTICLE_EXPORT_COMPLETE'), count($cid), $fileName));
		
		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));		
	}
}
?>