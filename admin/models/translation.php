<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.modeladmin');

class Form2ContentModelTranslation extends JModelAdmin
{
	protected $text_prefix = 'COM_FORM2CONTENT';

	public function getTable($type = 'Translation', $prefix = 'Form2ContentTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) 
		{
			if(empty($item->id))
			{
				$jinput 			= JFactory::getApplication()->input;
				$item->reference_id = $jinput->getInt('reference_id');
				$item->language_id 	= $jinput->getString('lang_code');
			}
			
			self::addReferenceData($item);			
		}
		
		return $item;
	}

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_form2content.translation', 'translation', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) 
		{
			return false;
		}

		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_form2content.edit.translation.data', array());
		
		if (empty($data)) 
		{
			$data = $this->getItem();
		}

		return $data;
	}

	public function save($data)
	{
		$dateNow = JFactory::getDate('now', 'UTC');
		$dateNow->setTimezone(new DateTimeZone(JFactory::getConfig()->get('offset')));
		$dateNow = $dateNow->toSql();									
		$user = JFactory::getUser();
		
		$data['modified_by']	= $user->id;		
		$data['modified']		= $dateNow;
				
		if(!parent::save($data))
		{
			return false;
		}
		
		return true;
	}

	private function addReferenceData($item)
	{
		$db 				= $this->getDbo();
		$query 				= $db->getQuery(true);
		
		$query->select('title, description');
		$query->from('#__f2c_projectfields');
		$query->where('id = ' . (int)$item->reference_id);
		
		$db->setQuery($query->__toString());
		
		$referenceData 				= $db->loadObject();
		$item->title_original 		= $referenceData->title; 
		$item->description_original = $referenceData->description; 
	}	
}
?>