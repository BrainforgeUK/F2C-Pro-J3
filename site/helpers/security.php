<?php
defined('_JEXEC') or die;

class F2cSecurityHelper
{
	public static function canEdit($joomlaId)
	{
		// First check if the Joomla article is linked to a F2C Article
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('id, created_by');
		$query->from('#__f2c_form');
		$query->where('reference_id = ' . (int)$joomlaId);
		
		$db->setQuery($query);
		$form = $db->loadObject();
		
		if($form)
		{
			$user	= JFactory::getUser();
			$asset	= 'com_form2content.form.'.$form->id;

			// Check general edit permission first.
			if ($user->authorise('core.edit', $asset)) 
			{ 
				return true;
			}

			// Fallback on edit.own.
			// First test if the permission is available.
			if ($user->authorise('core.edit.own', $asset) && $form->created_by == $user->get('id')) 
			{
				return true;
			}
		}
		
		return false;
	}
}
?>