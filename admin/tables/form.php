<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.access.rules');

use \Joomla\Registry\Registry;
use Joomla\String\StringHelper;

class Form2ContentTableForm extends JTable
{
	/**
	 * Constructor
	 *
	 * @param database Database object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__f2c_form', 'id', $db);
		
		// Set the alias since the column is called state
		$this->setColumnAlias('published', 'state');
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return	string
	 * @since	3.0.0
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_form2content.form.'.(int)$this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	string
	 * @since	3.0.0
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @return	int
	 * @since	3.0.0
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;
		$db = $this->getDbo();

		// This is a article under a contenttype.
		// Build the query to get the asset id for the parent category.
		$query	= $db->getQuery(true);
		$query->select('asset_id');
		$query->from('#__f2c_project');
		$query->where('id = '.(int) $this->projectid);

		// Get the asset id from the database.
		$this->_db->setQuery($query);
		if ($result = $this->_db->loadResult()) 
		{
			$assetId = (int)$result;
		}
 		
		// Return the asset id.
		if ($assetId) 
		{
			return $assetId;
		}
		else 
		{
			return parent::_getAssetParentId();
		}
	}
	 
	/**
	 * Validation
	 *
	 * @return boolean True if buffer valid
	 */
	function check()
	{
		if (trim($this->title) == '') 
		{
			$this->setError(JText::_('COM_FORM2CONTENT_ERROR_TITLE_EMPTY'));
			return false;
		}

		if (trim($this->alias) == '') 
		{
			$this->alias = $this->title;
		}

		$this->alias = JApplicationHelper::stringURLSafe($this->alias);

		if (trim(str_replace('-','',$this->alias)) == '') 
		{
			$this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}

		// Check the publish down date is not earlier than publish up.
		if (intval($this->publish_down) > 0 && $this->publish_down < $this->publish_up) 
		{
			// Swap the dates.
			$temp = $this->publish_up;
			$this->publish_up = $this->publish_down;
			$this->publish_down = $temp;
		}

		// clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (!empty($this->metakey)) 
		{
			// only process if not empty
			$bad_characters = array("\n", "\r", "\"", "<", ">"); // array of characters to remove
			$after_clean = StringHelper::str_ireplace($bad_characters, "", $this->metakey); // remove bad characters
			$keys = explode(',', $after_clean); // create array using commas as delimiter
			$clean_keys = array();

			foreach($keys as $key) {
				if (trim($key)) {  // ignore blank keywords
					$clean_keys[] = trim($key);
				}
			}
			$this->metakey = implode(", ", $clean_keys); // put array back together delimited by ", "
		}

		return true;
	}
	
    public function bind($array, $ignore = '') 
    {
            if (isset($array['attribs']) && is_array($array['attribs'])) 
            {
                    // Convert the params field to a string.
                    $parameter = new Registry;
                    $parameter->loadArray($array['attribs']);
                    $array['attribs'] = (string)$parameter;
            }
            
           if (isset($array['metadata']) && is_array($array['metadata'])) 
           {
                    // Convert the params field to a string.
                    $parameter = new Registry;
                    $parameter->loadArray($array['metadata']);
                    $array['metadata'] = (string)$parameter;
           }

			// Bind the rules.
			if (isset($array['rules']) && is_array($array['rules'])) 
			{
				$rules = new JAccessRules($array['rules']);
				$this->setRules($rules);
			}

			if(array_key_exists('tags', $array))
			{
				if(is_array($array['tags']))
				{
					$extended = new Registry();
					$extended->set('tags', implode(',', $array['tags']));
					$array['extended'] = $extended->toString();
				}
				else
				{
					$array['extended'] = '';
				}
			}
						
           return parent::bind($array, $ignore);
    }
    
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();

		if ($this->id) 
		{
			// Existing item
			$this->modified		= $date->toSql();
			$this->modified_by	= $user->get('id');
		} 
		else 
		{
			// New article. An article created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->created)) 
			{
				$this->created = $date->toSql();				
			}

			$this->modified = $db->getNullDate();
			
			// Allow creation of anonymous users
			//if (empty($this->created_by)) 
			//{
			//	$this->created_by = $user->get('id');
			//}
		}
		
		// Maken sure that the alias is unique
		$tableForm 		= JTable::getInstance('Form','Form2ContentTable');
		$tableContent 	= JTable::getInstance('Content','JTable');
		$uniqueAlias	= false;
		$aliasCounter	= 2;
		$aliasOriginal	= $this->alias;
		
		while(!$uniqueAlias)
		{
			if($tableForm->load(array('alias'=>$this->alias,'catid'=>$this->catid)) && ($tableForm->id != $this->id || $this->id==0) ||
			   $tableContent->load(array('alias'=>$this->alias,'catid'=>$this->catid)) && ($tableContent->id != $this->reference_id || $this->reference_id==0))
			{
				$this->alias = $aliasOriginal . $aliasCounter;
				$aliasCounter++;
				$uniqueAlias = false;
			}
			else
			{
				$uniqueAlias = true;
			} 						
		}

		return parent::store($updateNulls);
	}	 	
}
?>