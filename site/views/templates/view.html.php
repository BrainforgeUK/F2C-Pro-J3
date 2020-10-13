<?php
defined('JPATH_PLATFORM') or die;

jimport('joomla.application.component.view');

class Form2ContentViewTemplates extends JViewLegacy
{
	protected $items;

	public function display($tpl = null)
	{
		$this->items = $this->get('Items');			
		parent::display($tpl);
	}
}
?>
