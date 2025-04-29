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

/*
// Removed Brainforge.UK 2025-04-24 : Unused and parent:: causes fatal error
class pp
{
	/**
	 * Overloaded bind function
	 *
	 * @param	array		$hash named array
	 *
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @see		JTable:bind
	 * @since	1.5
	public function bind($array, $ignore = '')
	{
		if (isset($array['attribs']) && is_array($array['attribs'])) {
			$registry = new Registry();
			$registry->loadArray($array['attribs']);
			$array['attribs'] = (string)$registry;
		}

		if (isset($array['metadata']) && is_array($array['metadata'])) {
			$registry = new Registry();
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string)$registry;
		}

		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules'])) {
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overriden JTable::store to set modified data and user id.
	 *
	 * @param	boolean	True to update fields even if they are null.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param	mixed	An optional array of primary key values to update.  If not
	 *					set the instance property value is used.
	 * @param	integer The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param	integer The user id of the user performing the operation.
	 *
	 * @return	boolean	True on success.
	 * @since	1.0.4
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		F2cBrainforgeukArrayhelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks)) {
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Build the WHERE clause for the primary keys.
		$where = $k.'='.implode(' OR '.$k.'=', $pks);

		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
			$checkin = '';
		} else {
			$checkin = ' AND (checked_out = 0 OR checked_out = '.(int) $userId.')';
		}

		// Update the publishing state for rows with the given primary keys.
		$this->_db->setQuery(
			'UPDATE `'.$this->_tbl.'`' .
			' SET `state` = '.(int) $state .
			' WHERE ('.$where.')' .
			$checkin
		);
		$this->_db->execute();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
			// Checkin the rows.
			foreach($pks as $pk) {
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks)) {
			$this->state = $state;
		}

		$this->setError('');

		return true;
	}
}
*/
/*
====================================================================
SELECT `a`.`id`,`a`.`asset_id`,`a`.`title`,`a`.`alias`,`a`.`checked_out`,`a`.`checked_out_time`,`a`.`catid`,`a`.`state`,`a`.`access`,`a`.`created`,`a`.`created_by`,`a`.`created_by_alias`,`a`.`modified`,`a`.`ordering`,`a`.`featured`,`a`.`language`,`a`.`hits`,`a`.`publish_up`,`a`.`publish_down`,`a`.`introtext`,`a`.`fulltext`,`a`.`note`,`a`.`images`,`a`.`metakey`,`a`.`metadesc`,`a`.`metadata`,`a`.`version`,`fp`.`featured_up`,`fp`.`featured_down`,`l`.`title` AS `language_title`,`l`.`image` AS `language_image`,`uc`.`name` AS `editor`,`ag`.`title` AS `access_level`,`c`.`title` AS `category_title`,`c`.`created_user_id` AS `category_uid`,`c`.`level` AS `category_level`,`c`.`published` AS `category_published`,`parent`.`title` AS `parent_category_title`,`parent`.`id` AS `parent_category_id`,`parent`.`created_user_id` AS `parent_category_uid`,`parent`.`level` AS `parent_category_level`,`ua`.`name` AS `author_name`,`wa`.`stage_id` AS `stage_id`,`ws`.`title` AS `stage_title`,`ws`.`workflow_id` AS `workflow_id`,`w`.`title` AS `workflow_title`
FROM `#__content` AS `a`
LEFT JOIN `#__languages` AS `l` ON `l`.`lang_code` = `a`.`language`
LEFT JOIN `#__content_frontpage` AS `fp` ON `fp`.`content_id` = `a`.`id`
LEFT JOIN `#__users` AS `uc` ON `uc`.`id` = `a`.`checked_out`
LEFT JOIN `#__viewlevels` AS `ag` ON `ag`.`id` = `a`.`access`
LEFT JOIN `#__categories` AS `c` ON `c`.`id` = `a`.`catid`
LEFT JOIN `#__categories` AS `parent` ON `parent`.`id` = `c`.`parent_id`
LEFT JOIN `#__users` AS `ua` ON `ua`.`id` = `a`.`created_by`
INNER JOIN `#__workflow_associations` AS `wa` ON `wa`.`item_id` = `a`.`id`
INNER JOIN `#__workflow_stages` AS `ws` ON `ws`.`id` = `wa`.`stage_id`
INNER JOIN `#__workflows` AS `w` ON `w`.`id` = `ws`.`workflow_id`
WHERE `wa`.`extension` = 'com_content.article' AND `a`.`state` IN (:preparedArray1,:preparedArray2)
ORDER BY a.id DESC
 */