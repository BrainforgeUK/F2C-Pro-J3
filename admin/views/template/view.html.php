<?php
defined('_JEXEC') or die('Restricted acccess');

class Form2ContentViewTemplate extends JViewLegacy
{
	protected $item;
	protected $id;
	protected $isNew = false;	

	function display($tpl = null)
	{
		$model = $this->getModel();
	
		$this->form	= $this->get('Form');
		$this->item	= $this->get('Item');
		$this->id	= $model->get('id');
		
		if(empty($this->item->id))
		{
			$this->isNew = true;
		}
		
		$this->addToolbar();
		
		parent::display($tpl);		
	}
	
	protected function addToolbar()
	{
		$formTitle = JText::_('COM_FORM2CONTENT_TEMPLATE_MANAGER') . ' : ';
		$formTitle .= JText::_('COM_FORM2CONTENT_EDIT') . ' ' . JText::_('COM_FORM2CONTENT_TEMPLATE');
		
		JToolBarHelper::title($formTitle);
		JToolBarHelper::apply('template.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('template.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::cancel('template.cancel', 'JTOOLBAR_CANCEL');
		JToolbarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER', false, F2C_DOCUMENTATION_URL);
	}
}
?>