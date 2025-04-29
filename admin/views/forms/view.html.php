<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class Form2ContentViewForms extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $nullDate;
	
	function display($tpl = null)
	{
		$db						= $this->get('Dbo');		
		$this->items			= $this->get('Items');
		$this->pagination		= $this->get('Pagination');
		$this->state			= $this->get('State');
		$this->nullDate			= $db->getNullDate();
		$this->filterForm    	= $this->get('FilterForm');
		$this->activeFilters	= $this->get('ActiveFilters');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		if ($this->getLayout() !== 'modal')
		{
			Form2ContentHelperAdmin::addSubmenu('forms');
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
		$canDo	= Form2ContentHelperAdmin::getActions($this->state->get('filter.category_id'));
		$user  	= JFactory::getUser();
		$bar 	= JToolBar::getInstance('toolbar');
		
		JToolBarHelper::title(JText::_('COM_FORM2CONTENT_FORM2CONTENT') . ': ' . JText::_('COM_FORM2CONTENT_FORMS'), 'generic.png');
		
		if ($canDo->get('core.create')) 
		{
			JToolBarHelper::addNew('form.projectselect','JTOOLBAR_NEW');
		}
		
		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) 
		{
			JToolBarHelper::editList('form.edit','JTOOLBAR_EDIT');
		}
		
		if ($canDo->get('core.create')) 
		{
			JToolBarHelper::custom('forms.copy', 'copy.png', 'copy_f2.png', 'Copy', true);
		}
			
		if ($canDo->get('core.edit.state') || $canDo->get('form2content.edit.state.own')) 
		{
			JToolBarHelper::divider();
			JToolBarHelper::custom('forms.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('forms.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::custom('forms.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
			JToolbarHelper::archiveList('forms.archive');			
			
			// Add the refresh button
			$title = JText::_('COM_FORM2CONTENT_REFRESH');
			$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModalBatchRefresh\" class=\"btn btn-small\" id=\"btnBatchRefesh\">
						<i class=\"icon-refresh\" title=\"$title\"></i>
						$title</button>";
			$bar->appendButton('Custom', $dhtml, 'batch-refresh');
		}
		
		if ($this->state->get('filter.published') == F2C_STATE_TRASH && ($canDo->get('core.delete') || $canDo->get('form2content.delete.own'))) 
		{
			JToolBarHelper::deleteList('', 'forms.delete','JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('form2content.trash') || $canDo->get('form2content.trash.own')) 
		{
			JToolBarHelper::trash('forms.trash','JTOOLBAR_TRASH');
		}
		
		// Add a batch button
		if ($user->authorise('core.edit'))
		{
			$title = JText::_('JTOOLBAR_BATCH');
			$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\">
						<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
						$title</button>";
			$bar->appendButton('Custom', $dhtml, 'batch');
		}
		
		if ($canDo->get('core.admin')) 
		{
			JToolBarHelper::divider();		
			JToolBarHelper::preferences('com_form2content', 550, 800);			
			JToolBarHelper::custom('forms.export', 'box-add', 'box-add', JText::_('COM_FORM2CONTENT_EXPORT'), true);
		}
		
		JToolbarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER', false, F2C_DOCUMENTATION_URL);
		
		JHtmlSidebar::setAction('index.php?option=com_form2content&view=forms');
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
			'a.state' => JText::_('JSTATUS'),
			'a.title' => JText::_('JGLOBAL_TITLE'),
			'category_title' => JText::_('JCATEGORY'),
			'project_title' => JText::_('COM_FORM2CONTENT_PROJECT'),
			'a.access' => JText::_('JGRID_HEADING_ACCESS'),
			'u.name' => JText::_('JAUTHOR'),
			'l.title' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.created' => JText::_('COM_FORM2CONTENT_CREATED'),
			'a.modified' => JText::_('COM_FORM2CONTENT_MODIFIED'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
?>