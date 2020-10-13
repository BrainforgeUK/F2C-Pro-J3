<?php
defined('JPATH_PLATFORM') or die();

jimport('joomla.application.component.modellist');

class Form2ContentModelProjectFields extends JModelList
{
	protected $contentTypeId;

	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) 
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'fieldname', 'a.fieldname',
				'description', 'a.description',
				'ordering', 'a.ordering',
				// filter options
				'search',
				'fieldtypeid',
				'frontvisible'
			);
		}
		
		parent::__construct($config);
		$this->contentTypeId = JFactory::getApplication()->input->getInt('projectid', 0);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$fieldTypeId = $this->getUserStateFromRequest($this->context.'.filter.fieldtypeid', 'filter_fieldtypeid', '');
		$this->setState('filter.fieldtypeid', $fieldTypeId);

		$frontVisible = $this->getUserStateFromRequest($this->context.'.filter.frontvisible', 'filter_frontvisible', '');
		$this->setState('filter.frontvisible', $frontVisible);
		
		// List state information.
		parent::populateState('a.ordering', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.fieldtypeid');
		$id	.= ':'.$this->getState('filter.frontvisible');
		
		return parent::getStoreId($id);
	}
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('a.*');
		$query->from('`#__f2c_projectfields` AS a');
		
		// Join over the fieldtype for the description and t.classification_id.
		$query->select('t.description AS fieldtype');
		$query->select('t.classification_id');
		$query->join('LEFT', '`#__f2c_fieldtype` t ON a.fieldtypeid = t.id');

		// Filter by search in title.
		$search = $this->getState('filter.search');
		
		// Search filter
		if(!empty($search)) 
		{
			$query->where('(LOWER(a.fieldname) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false ) . ')');
		}

		// Content Type filter
		$query->where('(projectid = '.(int)$this->contentTypeId.')');

		// Filter by Field Type
		$fieldTypeId = $this->getState('filter.fieldtypeid');
		
		if (is_numeric($fieldTypeId)) 
		{
			$query->where('a.fieldtypeid = ' . (int)$fieldTypeId);
		}
		
		$frontVisible = $this->getState('filter.frontvisible');
		
		// Search front-end visibility
		if(is_numeric($frontVisible)) 
		{
			$query->where('a.frontvisible = '.$frontVisible);
		}
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.ordering');
		$orderDirn	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		JLog::add("Ordering: " . $orderCol . " " . $orderDirn, JLog::INFO, 'com_form2content');

		return $query;
	}	
}
?>