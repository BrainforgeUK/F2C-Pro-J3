<?php
class Form2ContentHelperAdmin
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 *
	 * @return	void
	 * @since	4.0.0
	 */
	public static function addSubmenu($vName)
	{
		$canDo	= self::getActions();
		
		if ($canDo->get('core.admin'))
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_FORM2CONTENT_CONTENTTYPE_MANAGER'),
				'index.php?option=com_form2content&view=projects',
				$vName == 'projects'
			);
		}
		
		JHtmlSidebar::addEntry(
			JText::_('COM_FORM2CONTENT_ARTICLE_MANAGER'),
			'index.php?option=com_form2content&view=forms',
			$vName == 'forms'
		);

		if ($canDo->get('core.admin'))
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_FORM2CONTENT_TRANSLATIONS'),
				'index.php?option=com_form2content&view=translations',
				$vName == 'translations'
			);
	
			JHtmlSidebar::addEntry(
				JText::_('COM_FORM2CONTENT_TEMPLATE_MANAGER'),
				'index.php?option=com_form2content&view=templates',
				$vName == 'templates'
			);
		}
		
		JHtmlSidebar::addEntry(
			JText::_('COM_FORM2CONTENT_DOCUMENTATION'),
			'index.php?option=com_form2content&view=documentation',
			$vName == 'documentation'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_FORM2CONTENT_ABOUT'),
			'index.php?option=com_form2content&view=about',
			$vName == 'about'
		);
	}
	
	public static function getActions($categoryId = 0, $formId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_form2content';

		if (empty($formId) && empty($categoryId)) 
		{
			$assetName = 'com_form2content';
		}
		else if (empty($categoryId)) 
		{
			$assetName = 'com_form2content.category.'.(int) $categoryId;
		}
		else 
		{
			$assetName = 'com_form2content.form.'.(int) $formId;
		}
		
		$actions = array(	'core.admin', 'core.manage', 'core.create', 
							'core.edit', 'core.edit.own', 
							'core.edit.state', 'form2content.edit.state.own',
							'form2content.trash', 'form2content.trash.own',
							'core.delete', 'form2content.delete.own');

		foreach ($actions as $action) 
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
?>
