<?php
defined('JPATH_PLATFORM') or die();

jimport('joomla.application.component.modellist');

class Form2ContentModelForms extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) 
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'featured', 'a.featured',
				'catid', 'a.catid', 'category_title', 'category_title',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'modified', 'a.modified',
				'created_by', 'a.created_by','authorname','u.name',
				'ordering', 'a.ordering',
				'language', 'a.language','l.title',
				'p.title', 'project_title',
				// filter options			
				'search', 
				'published', 
				'category_id',
				'contenttype_id',
				'author_id',
				'access',
				'level',
				'language'
			);
			
			if (JLanguageAssociations::isEnabled())
			{
				$config['filter_fields'][] = 'association';
			}
		}

		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);
		
		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		$contentTypeId = $this->getUserStateFromRequest($this->context.'.filter.contenttype_id', 'filter_contenttype_id');
		$this->setState('filter.contenttype_id', $contentTypeId);

		$authorId = $this->getUserStateFromRequest($this->context.'.filter.author_id', 'filter_author_id');
		$this->setState('filter.author_id', $authorId);

		$level = $this->getUserStateFromRequest($this->context.'.filter.level', 'filter_level');
		$this->setState('filter.level', $level);

		$access = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access');
		$this->setState('filter.access', $access);
		
		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);
		
		// List state information.
		parent::populateState('a.title', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.category_id');
		$id	.= ':'.$this->getState('filter.contenttype_id');
		$id	.= ':'.$this->getState('filter.author_id');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.level');
		$id	.= ':'.$this->getState('filter.language');
		
		return parent::getStoreId($id);
	}
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('a.*');
		$query->from('`#__f2c_form` AS a');
		
		// Join over the content for the Joomla article.
		$query->select('c.state as contentState, c.publish_up as publish_up_c, c.publish_down as publish_down_c');
		$query->join('LEFT', '`#__content` c ON a.reference_id = c.id');

		// Join over the project for the ContentType.
		$query->select('p.title as project_title');
		$query->join('LEFT', '`#__f2c_project` p ON a.projectid = p.id');

		// Join over the users for the author.
		$query->select('u.name as author_name');
		$query->join('LEFT', '`#__users` u ON a.created_by = u.id');

		// Join over the category for the category information.
		$query->select('cc.title AS category_title');
		$query->join('LEFT', '`#__categories` AS cc ON a.catid = cc.id');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');
		
		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
		
		// Join over the associations.
		if (JLanguageAssociations::isEnabled())
		{
			$query->select('COUNT(asso2.id)>1 as association')
				->join('LEFT', '#__associations AS asso ON asso.id = a.reference_id AND asso.context=' . $db->quote('com_content.item'))
				->join('LEFT', '#__associations AS asso2 ON asso2.key = asso.key')
				->group('a.id, l.title, u.name, ag.title, c.title');
		}		
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) 
		{
			$query->where('a.access = ' . (int) $access);
		}
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		
		// Search filter
		if (!empty($search)) 
		{
			if (stripos($search, 'id:') === 0) 
			{
				$query->where('a.id = '.(int) substr($search, 3));
			}
			elseif (stripos($search, 'author:') === 0) 
			{
				$search = $db->Quote('%'.$db->escape(substr($search, 7), true).'%');
				$query->where('(u.name LIKE '.$search.' OR u.username LIKE '.$search.')');
			}
			else 
			{
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
			}
		}
		
		// Filter by published state
		$published = $this->getState('filter.published');
		
		if (is_numeric($published)) 
		{
			$query->where('a.state = ' . (int) $published);
		}
		else if ($published === '') 
		{
			$query->where('(a.state = '.F2C_STATE_UNPUBLISHED.' OR a.state = '.F2C_STATE_PUBLISHED.')');
		}
		
		// Category filter
		$baselevel = 1;
		
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) 
		{
			$cat_tbl = JTable::getInstance('Category', 'JTable');
			$cat_tbl->load($categoryId);
			$rgt = $cat_tbl->rgt;
			$lft = $cat_tbl->lft;
			$baselevel = (int) $cat_tbl->level;
			$query->where('cc.lft >= '.(int) $lft);
			$query->where('cc.rgt <= '.(int) $rgt);
		}
		elseif (is_array($categoryId)) 
		{
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			$query->where('a.catid IN ('.$categoryId.')');
		}

		// Filter on the level.
		if ($level = $this->getState('filter.level')) 
		{
			$query->where('cc.level <= '.((int) $level + (int) $baselevel - 1));
		}
		
		// Content Type filter
		$contentTypeId = $this->getState('filter.contenttype_id');
		
		if($contentTypeId > 0)
		{
			$query->where('(projectid = '.(int)$contentTypeId.')');
		}

		// Filter by author
		$authorId = $this->getState('filter.author_id');
		if (is_numeric($authorId)) 
		{
			$type = $this->getState('filter.author_id.include', true) ? '= ' : '<>';
			$query->where('a.created_by '.$type.(int)$authorId);
		}
				
		// Filter on the language.
		if ($language = $this->getState('filter.language')) 
		{
			$query->where('a.language = '.$db->quote($language));
		}
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.title');
		$orderDirn	= $this->state->get('list.direction', 'ASC');

		if ($orderCol == 'a.ordering' || $orderCol == 'category_title')
		{
			$orderCol = 'cc.title '.$orderDirn.', a.ordering';
		}
		
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}		
}
?>
