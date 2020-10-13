<?php
class Form2ContentTableTranslation extends JTable
{
	/**
	 * Constructor
	 *
	 * @param database Database object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__f2c_translation', 'id', $db);
	}
	 
	/**
	 * Validation
	 *
	 * @return boolean True if buffer valid
	 */
	function check()
	{
		return true;
	}
}
?>