<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');

class Form2ContentViewF2C extends JViewLegacy
{
	function display($tpl = null)
	{
		if(JFactory::getApplication()->input->get('layout') == 'about')
		{
			$title = JText::_('Form2Content') . ' - ' . JText::_('About');
		}
		else
		{
			$title = JText::_('Form2Content') . ' - ' . JText::_('CONTROL_PANEL');			
		}
		
		JToolBarHelper::title($title, 'generic.png');
		JToolbarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER', false, F2C_DOCUMENTATION_URL);
		parent::display($tpl);
	}
}

?>