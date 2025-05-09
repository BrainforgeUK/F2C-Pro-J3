<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_form2content'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'project.php');
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_form2content'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'formbase.php');

class Form2ContentModelForm extends Form2ContentModelFormBase
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   4.6.5
	 */
	protected function populateState()
	{
		parent::populateState();
		
		$return = JFactory::getApplication()->input->get('return', null, 'base64');
		// Modified Brainforge.uk 20250509
		$this->setState('return_page', base64_decode($return ?? ''));
	}
	
	public function save($data)
	{
		$isNew			= empty($data['id']);
		$contentTypeId	= (int)$data['projectid'];
		$item			= $this->getItem(($isNew ? 0 : (int)$data['id']));
		$contentType	= F2cFactory::getContentType($contentTypeId);
		
		if(!$this->canSubmitArticle($contentTypeId, $item->id))
		{
			return false;
		}
		
		$data['attribs'] = $isNew ? $contentType->attribs : $item->attribs;
		$data['metadata'] = $isNew ? $contentType->metadata : $item->metadata;
		
		return parent::save($data, false);
	}

	/*
	 * Special save function for import.
	 * This function skips all user related checks.
	 */
	public function saveCron($data)
	{
		return parent::save($data);
	}
	
	function canSubmitArticle($contentTypeId, $id)
	{
		$user 					= JFactory::getUser();
		$contentType 			= F2cFactory::getContentType($contentTypeId);
		$menuParams 				= $this->getState('parameters.menu', null);
		$maxFormsContentType	= (int)$contentType->settings['max_forms'];
		$maxFormsMenu 			= $menuParams != null ? $menuParams->get('max_forms', 0) : 0;
				
		$maxForms = (empty($maxFormsContentType) || empty($maxFormsMenu)) ? max($maxFormsContentType, $maxFormsMenu) : min($maxFormsContentType, $maxFormsMenu);
		
		if($maxForms == 0)
		{
			// no limit
			return true;
		}
		else
		{
			$db = $this->getDbo();
		
			$query = $db->getQuery(true);
			$query->select('COUNT(*)');
			$query->from('#__f2c_form');
			$query->where('projectid = ' . (int)$contentTypeId);
			$query->where('created_by = ' . (int)$user->id);
			$query->where('id <> ' . (int)$id);
			$query->where('state <> ' . F2C_STATE_TRASH);
	
			$db->setQuery($query);

			$result = $db->loadResult();
			
			if($result >= $maxForms)
			{
				if($maxForms > 1)
				{
					$numArticles = '('.$maxForms.' '.JText::_('COM_FORM2CONTENT_FORMS').')'; 			
				}
				else
				{
					$numArticles = '('.$maxForms.' '.JText::_('COM_FORM2CONTENT_FORM').')'; 					
				}
				
				$this->setError(JText::_('COM_FORM2CONTENT_ERROR_MAX_FORMS_EXCEEDED') . ' ' . $numArticles);
				return false; 				
			}
		}
		
		return true;
	}
	
	/*
	 * Load the Id of the newest article that the current user has written.
	 * The ContentTypeId may be predefined.
	 * If no article is found, the id for a new article is returned 
	 */
	public function getDefaultArticleId($contentTypeId)
	{
		$user 	= JFactory::getUser();
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);
		
		$query->select('id');
		$query->from('#__f2c_form');
		$query->where('created_by = ' . (int)$user->id);
		$query->where('projectid = ' . (int)$contentTypeId);
		$query->where('state IN (' . F2C_STATE_PUBLISHED . ', ' . F2C_STATE_UNPUBLISHED . ')');
		$query->order('created DESC LIMIT 1');

		$db->setQuery($query);
		$id = $db->loadResult();

		return ($id) ? $id : 0;		
	}		
	
	/**
	 * Get the return URL.
	 *
	 * @return  string	The return URL.
	 * @since   4.6.5
	 */
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}
}
?>