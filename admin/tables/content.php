<?php
// No direct access
defined('JPATH_BASE') or die;

use Joomla\CMS\Workflow\Workflow;

jimport('joomla.database.table.content');
jimport('joomla.database.tableasset');

/**
 * Joomla Content table, overriden for use in Form2Content
 */
class Form2ContentTableContent extends JTableContent
{
	/**
	 * Constructor
	 *
	 * @param database Database object
	 * @since  6.3.0
	 */
	function __construct(&$db)
	{
		parent::__construct($db);

		// Removed Brainforge.uk 2025/04/29
		//JObserverMapper::addObserverClassToClass('JTableObserverTags', 'Form2ContentTableContent', array('typeAlias' => 'com_content.article'));
	}
	
	public function store($updateNulls = false)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		// Modified Brainforge.uk 2025/04/29
		$dispatcher	= F2cBrainforgeukEvent::getInstance();
		$dispatcher->trigger('onBeforeStore', array($updateNulls, $k));
		//$this->_observers->update('onBeforeStore', array($updateNulls, $k));

		/*
	
		// Verify that the alias is unique
		$table = JTable::getInstance('Content','JTable');
		
		//die('$table->id'.$table->id .' $this->id='.$this->id);
		
		if ($table->load(array('alias'=>$this->alias,'catid'=>$this->catid)) && ($table->id != $this->id || $this->id==0)) 
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_ARTICLE_UNIQUE_ALIAS'));
			return false;
		}
		*/

		// The asset id field is managed privately by this class.
		if ($this->_trackAssets) {
			unset($this->asset_id);
		}

		// If a primary key exists update the object, otherwise insert it.
		try
		{
			if ($this->$k) 
			{
				$result = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
			}
			else 
			{
				$result = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);

				// Added Brainforge.uk 2025/04/29
				if ($result && class_exists('\Joomla\CMS\Workflow\Workflow'))
				{
					$workflow = new Workflow('com_content.article');
					$workflow->createAssociation($this->$k, 1);
				}
			}
		}
		catch(Exception $e)
		{
			$this->setError(new Exception(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED', $e->getMessage())));
			return false;
		}
		
		// If the table is not set to track assets return true.
		if (!$this->_trackAssets) {
			return true;
		}

		if ($this->_locked) {
			$this->_unlock();
		}

		//
		// Asset Tracking
		//

		$parentId	= $this->_getAssetParentId();
		$name		= $this->_getAssetName();
		$title		= $this->_getAssetTitle();

		$asset	= JTable::getInstance('Asset');
		$asset->loadByName($name);

		// Re-inject the asset id.
		$this->asset_id = $asset->id;

		// Check for an error.
		if ($error = $asset->getError()) {
			$this->setError($error);
			return false;
		}

		// Specify how a new or moved node asset is inserted into the tree.
		if (empty($this->asset_id) || $asset->parent_id != $parentId) {
			$asset->setLocation($parentId, 'last-child');
		}

		// Prepare the asset to be stored.
		$asset->parent_id	= $parentId;
		$asset->name		= $name;
		$asset->title		= $title;

		if ($this->_rules instanceof JAccessRules) {
			$asset->rules = (string) $this->_rules;
		}

		if (!$asset->check() || !$asset->store($updateNulls)) {
			$this->setError($asset->getError());
			return false;
		}

		if (empty($this->asset_id)) {
			// Update the asset_id field in this table.
			$this->asset_id = (int) $asset->id;

			$query = $this->_db->getQuery(true);
			$query->update($this->_db->quoteName($this->_tbl));
			$query->set('asset_id = '.(int) $this->asset_id);
			$query->where($this->_db->quoteName($k).' = '.(int) $this->$k);
			$this->_db->setQuery($query);

			try 
			{
				$this->_db->execute();
			}
			catch(Exception $e)
			{
				$this->setError(new Exception(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $e->getMessage())));
				return false;
			}
		}

		// Implement JObservableInterface: Post-processing by observers
		// Modified Brainforge.uk 2025/04/29
		$dispatcher	= F2cBrainforgeukEvent::getInstance();
		$dispatcher->trigger('onAfterStore', array(&$result));
		//$this->_observers->update('onAfterStore', array(&$result));
				
		return $result;
	}	
}
