<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');

class Form2ContentViewProjectFields extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $contentTypeId;
	protected $pageTitle;

	function display($tpl = null)
	{
		if (!JFactory::getUser()->authorise('core.admin')) 
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}
		
		if ($this->getLayout() !== 'modal')
		{
			Form2ContentHelperAdmin::addSubmenu('projects');
		}
		
		$this->items			= $this->get('Items');
		$this->pagination		= $this->get('Pagination');
		$this->state			= $this->get('State');
		$this->contentTypeId	= JFactory::getApplication()->input->getInt('projectid');
		$this->filterForm   	= $this->get('FilterForm');
		$this->activeFilters 	= $this->get('ActiveFilters');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			throw new Exception(implode("\n", $errors));
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
		JHtmlSidebar::setAction('index.php?option=com_form2content&view=projects');
		
		$contentType = F2cFactory::getContentType($this->contentTypeId);
		$this->pageTitle = JText::_('COM_FORM2CONTENT_PROJECTFIELDS') . ' - ' . $contentType->title;
		
		JToolBarHelper::title(JText::_('COM_FORM2CONTENT_FORM2CONTENT') . ': ' . $this->pageTitle , 'generic.png');
		JToolBarHelper::addNew('projectfieldtypeselect.select','JTOOLBAR_NEW');
		JToolBarHelper::custom('projectfield.projectselect', 'copy.png', 'copy_f2.png','COM_FORM2CONTENT_COPY', true);
		JToolBarHelper::editList('projectfield.edit','JTOOLBAR_EDIT');
		JToolBarHelper::trash('projectfields.delete','JTOOLBAR_TRASH');
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
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.fieldname' => JText::_('COM_FORM2CONTENT_FIELDNAME'),
			'a.title' => JText::_('COM_FORM2CONTENT_FIELD_CAPTION'),
			'a.description' => JText::_('COM_FORM2CONTENT_DESCRIPTION'),
			'a.id' => JText::_('JGRID_HEADING_ORDERING')
		);
	}
	
	protected function gridFrontVisible($value, $i, $prefix = '')
	{
		if (is_object($value))
		{
			$value = $value->frontvisible;
		}
		
		$img = $value ? 'icon-publish' : 'icon-unpublish';
		$task = $value ? 'setfrontinvisible' : 'setfrontvisible';
		$action = $value ? JText::_('COM_FORM2CONTENT_SET_ITEM_FRONTINVISIBLE') : JText::_('COM_FORM2CONTENT_SET_ITEM_FRONTVISIBLE');

		// Modified Brainforge.uk 2025/04/29
		return '<a href="#" onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . $prefix . $task . '\')" title="' . $action . '">'
			. '<i class="'.$img.'"></i></a>';
		
	}
}
?>