<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');
//jimport('joomla.language.helper');

class Form2ContentViewTranslation extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
	
	function display($tpl = null)
	{
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		$this->addToolbar();
		
		JFactory::getApplication()->enqueueMessage(Jtext::_('COM_FORM2CONTENT_TRANSLATIONS_OBSOLETE'), 'warning');
		
		parent::display($tpl);		
	}
	
	protected function addToolbar()
	{
		$isNew = ($this->item->id == 0);
	
		JFactory::getApplication()->input->set('hidemainmenu', true);

		JToolBarHelper::title(JText::_('COM_FORM2CONTENT_TRANSLATION_'.($isNew ? 'ADD' : 'EDIT')), 'pencil-2 article-add');
		
		// Built the actions for new and existing records.
		JToolBarHelper::apply('translation.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('translation.save', 'JTOOLBAR_SAVE');
		
		if ($isNew)  
		{
			JToolBarHelper::cancel('translation.cancel', 'JTOOLBAR_CANCEL');
		}
		else 
		{
			JToolBarHelper::cancel('translation.cancel', 'JTOOLBAR_CLOSE');
		}		
		
		JToolbarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER', false, F2C_DOCUMENTATION_URL);
	}		
}

?>