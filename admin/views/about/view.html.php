<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');

class Form2ContentViewAbout extends JViewLegacy
{
	function display($tpl = null)
	{
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') 
		{
			Form2ContentHelperAdmin::addSubmenu('about');
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		JHtmlSidebar::setAction('index.php?option=com_form2content&view=about');
		
		$title = JText::_('COM_FORM2CONTENT_FORM2CONTENT') . ': ' . JText::_('COM_FORM2CONTENT_ABOUT');			
		JToolBarHelper::title($title, 'generic.png');	
		JToolbarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER', false, F2C_DOCUMENTATION_URL);
	}
}
?>