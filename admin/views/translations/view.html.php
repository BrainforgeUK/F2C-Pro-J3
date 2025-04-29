<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'shared.form2content.php');

jimport('joomla.language.helper');

class Form2ContentViewTranslations extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
	function display($tpl = null)
	{
		if (!JFactory::getUser()->authorise('core.admin')) 
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}
		
		JFactory::getApplication()->enqueueMessage(Jtext::_('COM_FORM2CONTENT_TRANSLATIONS_OBSOLETE'), 'warning');
		
		if ($this->getLayout() !== 'modal')
		{
			Form2ContentHelperAdmin::addSubmenu('translations');
		}
		
		$this->items					= $this->get('Items');
		$this->pagination				= $this->get('Pagination');
		$this->state					= $this->get('State');
		$this->filterForm    			= $this->get('FilterForm');
		$this->activeFilters 			= $this->get('ActiveFilters');
			
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}
		
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') 
		{
			$this->addToolbar();
			// Removed Brainforge.uk 2025/04/29
// $this->sidebar = JHtmlSidebar::render();
		}
				
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		JHtmlSidebar::setAction('index.php?option=com_form2content&view=translations');
		
		JToolBarHelper::title(JText::_('COM_FORM2CONTENT_FORM2CONTENT') . ': ' . JText::_('COM_FORM2CONTENT_TRANSLATIONS'), 'article.png');
		JToolBarHelper::editList('translation.edit','JTOOLBAR_EDIT');
		JToolBarHelper::trash('translations.delete','JTOOLBAR_TRASH');
		JToolbarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER', false, F2C_DOCUMENTATION_URL);
	}
	
	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   6.0.0
	 */
	protected function getSortFields()
	{
		return array(
			'f.title' 				=> JText::_('COM_FORM2CONTENT_DEFAULT_FIELD_NAME'), 
			'p.title' 				=> JText::_('COM_FORM2CONTENT_PROJECT'), 
			'l.lang_code' 			=> JText::_('COM_FORM2CONTENT_LANGUAGE'), 
			't.title_translation'	=> JText::_('COM_FORM2CONTENT_TRANSLATION'), 
			't.modified' 			=> JText::_('COM_FORM2CONTENT_DATE_MODIFIED'), 
			't.modified_by' 		=> JText::_('COM_FORM2CONTENT_MODIFIED_BY')
		);
	}
}
?>