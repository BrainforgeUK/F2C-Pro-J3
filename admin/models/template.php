<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'shared.form2content.php');

jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');

class Form2ContentModelTemplate extends JModelAdmin
{
	protected $text_prefix = 'COM_FORM2CONTENT';
	protected $id;
	
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_form2content.template', 'template', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) 
		{
			return false;
		}

		return $form;
	}
	
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_form2content.edit.template.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}

		return $data;
	}
	
	public function getItem($pk = null)
	{
		$item 			= new StdClass();
		$jinput 		= JFactory::getApplication()->input;		
		$this->id 		= $jinput->getString('id');
		$cid 			= $jinput->get('cid', array(), 'array');
		$item->template = '';
		$item->title 	= '';
		
		if(!$this->id && count($cid) > 0)
		{
			$this->id = $cid[0];
		}
	
		$item->id 	= $this->id;
		
		if($item->id)
		{
			$templateFile 	= Path::Combine(F2cFactory::getConfig()->get('template_path'), $this->id.'.tpl');
			$item->title 	= $this->id;
			$item->template = file_get_contents($templateFile);
		}
		
		return $item;		
	}
	
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{		
		if(!empty($data->id))
		{
			// Disable editing of title
			$form->setFieldAttribute('title', 'readonly', true);
		}
		
		parent::preprocessForm($form, $data, $group);
	}
	
	function save($data)
	{
		$filename 	= Path::Combine(F2cFactory::getConfig()->get('template_path'), $data['title'].'.tpl');
		$template	= $data['template'];
		$isNew		= empty($data['id']);
		
		if(HtmlHelper::detectUTF8($template))
		{
			// check if BOM is present
			$utf8bom = "\xEF\xBB\xBF";
			$pos = strpos($template, $utf8bom);
			
			if($pos === false)
			{
				$template = $utf8bom . $template;
			}
		}		

		$this->setState($this->getName() . '.id', $data['title']);
		$this->setState($this->getName() . '.new', $isNew);
		
		return JFile::write($filename, $template);
	}
	
	function delete(&$pks)
	{
		$result = true;
	
		foreach($pks as $id)
		{
			$file = Path::Combine(F2cFactory::getConfig()->get('template_path'), $id.'.tpl');
		
			if(!JFile::exists($file))
			{
				$this->setError(JText::_('COM_FORM2CONTENT_ERROR_TEMPLATE_NOT_FOUND'). ': '. $id);
				$result = false;
				break;
			}
			
			if(!JFile::delete($file))
			{
				$this->setError(JText::_('COM_FORM2CONTENT_ERROR_TEMPLATE_DELETE'). ': '. $id);
				$result = false;
			}
		}
		
		return $result;
	}
	
	public function validate($form, $data, $group = null)
	{
		if(empty($data['id']))
		{
			// new template, make sure the template does not exist yet
			$filename = Path::Combine(F2cFactory::getConfig()->get('template_path'), $data['title'].'.tpl');
			
			if(JFile::exists($filename))
			{
				$this->setError(sprintf(JText::_('COM_FORM2CONTENT_ERROR_TEMPLATE_EXISTS'), $data['title']));
				return false;	
			}
		}
		
		return $data;
	}
	
	public function getTable($name = '', $prefix = 'Table', $options = array())
	{
		// This model does not use a table, return a dummy object instead
		return new stdClass();
	}
	
	function upload()
	{
		$file = JFactory::getApplication()->input->files->get('upload', null, 'raw');
		
		if(empty($file['name'])) 
		{
			$this->setError(JText::_('COM_FORM2CONTENT_ERROR_TEMPLATE_FILE_UPLOAD_EMPTY'));
			return false;
		}

		$templateFile = Path::Combine(F2cFactory::getConfig()->get('template_path'), strtolower($file['name']));
		
		if(JFile::exists($templateFile)) 
		{
			$this->setError(JText::_('COM_FORM2CONTENT_ERROR_TEMPLATE_FILE_UPLOAD_EXISTS'));
			return false;
		}

		if(strtolower(JFile::getExt($templateFile)) != 'tpl')
		{
			$this->setError(JText::_('COM_FORM2CONTENT_ERROR_TEMPLATE_FILE_UPLOAD_INVALID_FILE_TYPE'));
			return false;			
		}
		
		if(!JFile::upload($file['tmp_name'], $templateFile))
		{
			$this->setError(JText::_('COM_FORM2CONTENT_ERROR_TEMPLATE_FILE_UPLOAD_FAILED'));
			return false;			
		}

		JPath::setPermissions($templateFile);			
		return true;
	}	
}
?>