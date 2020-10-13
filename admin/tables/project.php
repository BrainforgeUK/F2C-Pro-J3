<?php
use \Joomla\Registry\Registry;

jimport('joomla.access.rules');

class Form2ContentTableProject extends JTable
{
	/**
	 * Constructor
	 *
	 * @param database Database object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__f2c_project', 'id', $db);
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
		return 'com_form2content.project.'.(int)$this->$k;
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
		$asset = JTable::getInstance('Asset');
        $asset->loadByName('com_form2content');
        return $asset->id;		
	}
	 
	/**
	 * Validation
	 *
	 * @return boolean True if buffer valid
	 */
	function check()
	{
		if(trim($this->title) == '')
		{
			$this->setError(JText::_('COM_FORM2CONTENT_ERROR_PROJECT_TITLE_EMPTY'));
			return false;
		}
						
		$settings = new Registry;
		$settings->loadString($this->settings);			
		
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
            
           if (isset($array['settings']) && is_array($array['settings'])) 
           {
                    // Convert the params field to a string.
                    $parameter = new Registry;
                    $parameter->loadArray($array['settings']);
                    $array['settings'] = (string)$parameter;
           }

			// Bind the rules.
			if (isset($array['rules']) && is_array($array['rules'])) 
			{
				$rules = new JAccessRules($array['rules']);
				$this->setRules($rules);
			}

           if (isset($array['images']) && is_array($array['images'])) 
           {
                    // Convert the params field to a string.
                    $parameter = new Registry;
                    $parameter->loadArray($array['images']);
                    $array['images'] = (string)$parameter;
           }
			
           if (isset($array['urls']) && is_array($array['urls'])) 
           {
                    // Convert the params field to a string.
                    $parameter = new Registry;
                    $parameter->loadArray($array['urls']);
                    $array['urls'] = (string)$parameter;
           }
			
           return parent::bind($array, $ignore);
    }	
}
?>