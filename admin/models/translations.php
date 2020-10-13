<?php
defined('JPATH_PLATFORM') or die();

jimport('joomla.application.component.modellist');

class Form2ContentModelTranslations extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) 
		{
			$config['filter_fields'] = array(
				'title', 'f.title',
				'projecttitle', 'p.title',
				'lang_code', 'l.lang_code',
				'title_translation', 't.title_translation',
				'modified', 't.modified',
				'modifier', 't.modified_by',
				// filter options
				'search',
				'contenttype_id',
				'language',
				'translationstate'
			);
		}
		
		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}
		
		// Initialise variables.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$contentTypeId = $this->getUserStateFromRequest($this->context.'.filter.contenttype_id', 'filter_contenttype_id');
		$this->setState('filter.contenttype_id', $contentTypeId);

		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language');
		$this->setState('filter.language', $language);

		$translationState = $this->getUserStateFromRequest($this->context.'.filter.translationstate', 'filter_translationstate');
		$this->setState('filter.translationstate', $translationState);
		
		// List state information.
		parent::populateState('f.title', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.contenttype_id');
		$id	.= ':'.$this->getState('filter.language');
		$id	.= ':'.$this->getState('filter.translationstate');
		
		return parent::getStoreId($id);
	}
	
	protected function getListQuery()
	{
		$db 				= $this->getDbo();
		$query 				= $db->getQuery(true);
		
		$query->select('f.id as fieldid, f.fieldname, f.title as fieldtitle');
		$query->from('#__f2c_projectfields f');
		
		$query->select('p.title as projecttitle');
		$query->join('INNER', '#__f2c_project p on f.projectid = p.id');
		
		$query->select('l.lang_code');
		$query->join('LEFT', '#__languages l on 1 = 1');

		$query->select('t.id as translation_id, t.title_translation, t.modified');
		$query->join('LEFT', ' #__f2c_translation t on (f.id = t.reference_id AND l.lang_code = t.language_id)');
		
		$query->select('u.name AS modifier');
		$query->join('LEFT', '#__users AS u ON u.id = t.modified_by');
		
		if((int)$this->getState('filter.contenttype_id') > 0)
		{
			$query->where('f.projectid = ' . (int)$this->getState('filter.contenttype_id'));
		}
		
		if($this->getState('filter.language') != '')
		{
			$query->where('l.lang_code = ' . $db->quote($this->getState('filter.language')));
		}
		
		if($this->getState('filter.translationstate') != '')
		{
			if($this->getState('filter.translationstate') == '1')
			{
				$query->where('t.title_translation IS NOT NULL');	
			}
			else 
			{
				$query->where('t.title_translation IS NULL');					
			}
		}
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		
		// Search filter
		if(!empty($search)) 
		{
			$query->where('LOWER(f.title) LIKE '.$db->Quote('%'.$db->escape( $search, true ).'%', false));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}
}
?>
