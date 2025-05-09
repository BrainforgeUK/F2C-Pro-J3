<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use \Joomla\Registry\Registry;

require_once(JPATH_SITE.'/components/com_form2content/shared.form2content.php');
require_once(JPATH_SITE.'/components/com_form2content/utils.form2content.php');

jimport('joomla.application.component.modeladmin');

class Form2ContentModelProjectBase extends JModelAdmin
{
	protected $text_prefix = 'COM_FORM2CONTENT';

	public function getTable($type = 'Project', $prefix = 'Form2ContentTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) 
		{
			// Convert the params field to an array.
			$registry = new Registry;
			// Modified Brainforge.uk 20250509
			$registry->loadString($item->attribs ?? '');
			$item->attribs = $registry->toArray();

			// Convert the metadata field to an array.
			$registry = new Registry;
			// Modified Brainforge.uk 20250509
			$registry->loadString($item->metadata ?? '');
			$item->metadata = $registry->toArray();

			// Convert the settings field to an array.
			$registry = new Registry;
			// Modified Brainforge.uk 20250509
			$registry->loadString($item->settings ?? '');
			$item->settings = $registry->toArray();
			
			// Convert the images field to an array.
			if(!property_exists($item, 'images'))
			{
				$item->images = '';
			}
			
			$registry = new Registry;
			// Modified Brainforge.uk 20250509
			$registry->loadString($item->images ?? '');
			$item->images = $registry->toArray();

			// Convert the urls field to an array.
			if(!property_exists($item, 'urls'))
			{
				$item->urls = '';
			}
			
			$registry = new Registry;
			// Modified Brainforge.uk 20250509
			$registry->loadString($item->urls ?? '');
			$item->urls = $registry->toArray();
			
			$item->fields = $this->getFieldDefinitions($pk);
			
			if(!array_key_exists('create_joomla_article', $item->settings))
			{
				$item->settings['create_joomla_article'] = 1;
			}
		}
		
		return $item;
	}
		
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_form2content.project', 'project', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) 
		{
			return false;
		}

		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_form2content.edit.project.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}

		return $data;
	}

	public function getFieldDefinitions($contentTypeId)
	{
		$query = $this->_db->getQuery(true);
		$query->select('pf.*, ft.name AS fieldtypename, ft.classification_id');
		$query->from('#__f2c_projectfields pf');
		$query->join('INNER', '#__f2c_fieldtype ft ON pf.fieldtypeid = ft.id');
		$query->where('pf.projectid = ' . (int)$contentTypeId);
		$query->order('pf.ordering ASC');
		
		$this->_db->setQuery($query);
		
		$fields 	= $this->_db->loadObjectList('id');
		$fieldsNew 	= array();
		
		if(count($fields))
		{
			foreach($fields as $field)
			{
				// Dynamically create F2C field
				$className 				= 'F2cField'.$field->fieldtypename;
				$fieldsNew[$field->id]	= new $className($field);
			}
		}

		return $fieldsNew;
	}	
}
?>