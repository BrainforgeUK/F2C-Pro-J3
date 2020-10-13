<?php
defined('JPATH_PLATFORM') or die();

jimport('joomla.application.component.modellist');

class Form2ContentModelProjects extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) 
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'published', 'a.published',
				'created', 'a.created',
				'modified', 'a.modified',
				'created_by', 'a.created_by',
				'u.name',
				// filter options
				'search',
				'published',
				'author_id'
			);
		}

		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);
		
		$authorId = $this->getUserStateFromRequest($this->context.'.filter.author_id', 'filter_author_id');
		$this->setState('filter.author_id', $authorId);
		
		// List state information.
		parent::populateState('a.title', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.author_id');
		
		return parent::getStoreId($id);
	}
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('a.*');
		$query->from('`#__f2c_project` AS a');
		
		// Join over the users for the author.
		$query->select('u.name AS username');
		$query->join('LEFT', '`#__users` u ON a.created_by = u.id');

		// Filter by search in title.
		$search = $this->getState('filter.search');
		
		if(!empty($search)) 
		{
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('(a.title LIKE '.$search.')');
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		
		if (is_numeric($published)) 
		{
			$query->where('a.published = ' . (int) $published);
		}
		
		// Filter by author
		$authorId = $this->getState('filter.author_id');
		if (is_numeric($authorId)) 
		{
			$type = $this->getState('filter.author_id.include', true) ? '= ' : '<>';
			$query->where('a.created_by '.$type.(int)$authorId);
		}
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		return $query;
	}	
}
?>
