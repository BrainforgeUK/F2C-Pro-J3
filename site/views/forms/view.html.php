<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use \Joomla\Registry\Registry;
use \Joomla\Utilities\ArrayHelper;

require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'form2content.php';
require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'utils.form2content.php';

JLoader::register('F2cViewHelper', JPATH_COMPONENT_SITE.'/helpers/view.php');

jimport('joomla.application.component.view');

class Form2ContentViewForms extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $nullDate;
	protected $menuParams;
	protected $activeMenu;
	protected $params;
	protected $numCols;
	protected $categoryOptions = array();
	protected $contentTypeSettings;
	
	function display($tpl = null)
	{
		$app				= JFactory::getApplication();
		$menu				= $app->getMenu();
		$this->activeMenu	= $menu->getActive();
		$db					= $this->get('Dbo');
		$this->state		= $this->get('State');
		$this->params		= $app->getParams();
		$this->nullDate		= $db->getNullDate();

		// Modified Brainforge.uk 2025/04/29
		$params = $this->activeMenu->getParams();
		$this->getMenuParameters($params);


		
		$contentTypeId	= $params->get('contenttypeid');
		$model 			= $this->getModel();
		
		$model->setState('ContentTypeId', $contentTypeId);		
		
		
		if ((int)$this->menuParams->get('classic_layout', 0))
		{
			$this->setLayout('classic');
		}
		
		// Verify that the Content Type exists
		$contentType = F2cFactory::getContentType($contentTypeId);
		
		if(empty($contentType->id))
		{
			throw new Exception(sprintf(JText::_('COM_FORM2CONTENT_ERROR_ARTICLE_MANAGER_UNKNOWN_CONTENT_TYPE'), $contentTypeId));
		}		
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true) . '/media/com_form2content/css/f2cjui.css');
		$document->setTitle(F2cViewHelper::getPageTitle($this->params->get('page_title', '')));
		
		$this->contentTypeSettings = new Registry();
		$this->contentTypeSettings->loadArray($contentType->settings);
		
		// get the category Options
		$categoryField = $contentType->getFieldByType('JoomlaCategory');
		
		$defaultCategoryId = (int)$categoryField->settings->get('default');
		
		if($defaultCategoryId != -1)
		{
			if((int)$categoryField->settings->get('behaviour') == 0)
			{
				// The category is fixed
				$this->categoryOptions = Form2ContentHelper::getCategoryList(2, 'com_content', $defaultCategoryId);
			}
			else
			{
				// The category is the root category
				$this->categoryOptions = Form2ContentHelper::getCategoryList(1, 'com_content', $defaultCategoryId);
			}
		}
		else
		{
			// Get all categories
			$this->categoryOptions = Form2ContentHelper::getCategoryList();
		}		
		
		// Load extra language file for Joomla admin functionality
		$lang = JFactory::getLanguage();
		$lang->load('', JPATH_ADMINISTRATOR, $lang->getTag(), true);
		
		$this->numCols = 3;
		if($this->menuParams->get('show_ordering')) $this->numCols++;
		if($this->menuParams->get('show_published_column')) $this->numCols++;
		if($this->menuParams->get('show_category')) $this->numCols++;
		if($this->menuParams->get('show_author_column')) $this->numCols++;
		if($this->menuParams->get('show_created_column')) $this->numCols++;
		if($this->menuParams->get('show_modified_column')) $this->numCols++;
		if($this->menuParams->get('show_publish_up_column')) $this->numCols++;
		if($this->menuParams->get('show_publish_down_column')) $this->numCols++;
		if($this->menuParams->get('show_language_column')) $this->numCols++;
		
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_FORM2CONTENT_FORM2CONTENT') . ': ' . JText::_('COM_FORM2CONTENT_FORMS'), 'generic.png');		
	}
	
	private function getMenuParameters($params)
	{
		$this->menuParams	= new Registry();
		$contentTypeId		= $params->get('contenttypeid');
		$canDo				= Form2ContentHelper::getActions($contentTypeId);		
		
		$this->menuParams->set('show_published_filter', $params->get('show_published_filter', 0));
		$this->menuParams->set('show_category_filter', $params->get('show_category_filter', 1));
		$this->menuParams->set('show_search_filter', $params->get('show_search_filter', 1));
		$this->menuParams->set('contenttypeid', $contentTypeId);
		$this->menuParams->set('classic_layout', $params->get('classic_layout', 0));
		
		switch($params->get('show_category_ordering',1))
		{
			case 0: // hide both category and ordering
				$this->menuParams->set('show_category', 0);
				$this->menuParams->set('show_ordering', 0);
				break;
			case 1: // show both category and ordering
				$this->menuParams->set('show_category', 1);
				$this->menuParams->set('show_ordering', 1);
				break;
			case 2: // show category, hide ordering
				$this->menuParams->set('show_category', 1);
				$this->menuParams->set('show_ordering', 0);
				break;
			case 3: // hide category, show ordering
				$this->menuParams->set('show_category', 0);
				$this->menuParams->set('show_ordering', 1);
				break;
		}
		
		if ($canDo->get('core.create') && $params->get('show_new_button',1))
		{
			$this->menuParams->set('show_new_button', 1);
		}
		else
		{
			$this->menuParams->set('show_new_button', 0);
		}
		
		if ($canDo->get('core.create') && $params->get('show_copy_button',1))
		{
			$this->menuParams->set('show_copy_button', 1);
		}
		else
		{
			$this->menuParams->set('show_copy_button', 0);
		}
		
		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) 
		{
			if($params->get('show_edit_button',1))
			{
				$this->menuParams->set('show_edit_button', 1);
			}
			else 
			{
				$this->menuParams->set('show_edit_button', 0);
			}
		}
		else
		{
			$this->menuParams->set('show_edit_button', 0);
		}
		
		if ($canDo->get('core.edit.state') || $canDo->get('form2content.edit.state.own'))
		{
			$this->menuParams->set('show_publish_button', 1);
			$this->menuParams->set('show_archive_button', $params->get('show_archive_button', 0));
		}
		else
		{
			$this->menuParams->set('show_publish_button', 0);
			$this->menuParams->set('show_archive_button', 0);
		}
				
		if($params->get('show_delete_button', 1))
		{ 
			if ((int)$this->state->get('filter.published') != (int)F2C_STATE_TRASH)
			{
					$this->menuParams->set('show_trash_button', $canDo->get('core.delete') || $canDo->get('form2content.delete.own'));
					$this->menuParams->set('show_delete_button', 0);			
			}
			else
			{
				$this->menuParams->set('show_trash_button', 0);
				$this->menuParams->set('show_delete_button', $canDo->get('form2content.trash') || $canDo->get('form2content.trash.own'));				
			}
		}
		else 
		{
			$this->menuParams->set('show_trash_button', 0);
			$this->menuParams->set('show_delete_button', 0);						
		}
		
		$this->menuParams->set('show_created_column', $params->get('show_created', 1));
		$this->menuParams->set('show_modified_column', $params->get('show_modified', 1));
		$this->menuParams->set('show_author_column', $params->get('show_author', 1));
		$this->menuParams->set('show_published_column', $params->get('show_published', 1));
		$this->menuParams->set('show_featured_column', $params->get('show_featured', 1));
		$this->menuParams->set('show_language_column', $params->get('show_language', 1));
		$this->menuParams->set('show_publish_up_column', $params->get('show_publish_up', 0));
		$this->menuParams->set('show_publish_down_column', $params->get('show_publish_down', 0));
		$this->menuParams->set('show_f2c_id_column', $params->get('show_f2c_id', 1));
		$this->menuParams->set('show_joomla_id_column', $params->get('show_joomla_id', 0));
	}
	
	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   5.0.0
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
			'a.created_by' => JText::_('JAUTHOR'),
			'language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.created' => JText::_('COM_FORM2CONTENT_CREATED'),
			'a.modified' => JText::_('COM_FORM2CONTENT_MODIFIED'),
			'a.id' => JText::_('JGRID_HEADING_ID'),
			'a.publish_up' => JText::_('COM_FORM2CONTENT_PUBLISH_UP'),
			'a.publish_down' => JText::_('COM_FORM2CONTENT_PUBLISH_DOWN'),
			'a.reference_id' => JText::_('COM_FORM2CONTENT_JOOMLA_ID')
		);
	}
	
	protected function legacyPublished($html)
	{
		$html = str_ireplace('<i class="icon-publish"></i>', '<span class="state publish"><span class="text">'.JText::_('JPUBLISHED').'</span></span>', $html);
		$html = str_ireplace('<i class="icon-unpublish"></i>', '<span class="state unpublish"><span class="text">'.JText::_('JUNPUBLISHED').'</span></span>', $html);
		$html = str_ireplace('<i class="icon-expired"></i>', '<span class="state expired"><span class="text">'.JText::_('JPUBLISHED').'</span></span>', $html);
		$html = str_ireplace('<i class="icon-pending"></i>', '<span class="state pending"><span class="text">'.JText::_('JPUBLISHED').'</span></span>', $html);
		$html = str_ireplace('<i class="icon-trash"></i>', '<span class="state trash"><span class="text">'.JText::_('JTRASHED').'</span></span>', $html);
		
		return $html;
	}
	
	protected function legacyOrdering($html)
	{
		$html = str_ireplace('<i class="icon-downarrow"></i>', '<span class="state downarrow"><span class="text">'.JText::_('JLIB_HTML_MOVE_DOWN').'</span></span>', $html);
		$html = str_ireplace('<i class="icon-uparrow"></i>', '<span class="state uparrow"><span class="text">'.JText::_('JLIB_HTML_MOVE_UP').'</span></span>', $html);
		
		return $html;
	}
	
	protected function legacyFeatured($value = 0, $i, $canChange = true)
	{
		// Array of image, task, title, action
		$states	= array(
			0	=> array('disabled.png',	'forms.featured',	'COM_CONTENT_UNFEATURED',	'COM_CONTENT_TOGGLE_TO_FEATURE'),
			1	=> array('featured.png',		'forms.unfeatured',	'COM_CONTENT_FEATURED',		'COM_CONTENT_TOGGLE_TO_UNFEATURE'),
		);
		$state	= /* Modified Brainforge.uk 2025/04/29 */ArrayHelper::getValue($states, (int) $value, $states[1]);
		
		$html	= '<img src="media/com_form2content/images/'.$state[0].'" alt="'.JText::_($state[2]).'" />';
		if ($canChange) {
			// Modified Brainforge.uk 2025/04/29
			$html	= '<a href="#" onclick="return Joomla.listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'
					. $html.'</a>';
		}

		return $html;
	}
	
}
?>